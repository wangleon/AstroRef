<?php

function URLParser($conn, $url) {
    $url=trim($url);

    // parse adscode
    $url = str_replace('%26','&',$url);

    // get adscode from url
    $site_lst = array("http://adsabs.harvard.edu/abs/",
                      "http://ads.bao.ac.cn/abs/",
                      "http://esoads.eso.org/abs/",
                     );

    if (substr($url,0,7)=="http://") {

        // search among site list
        $find = False;
        foreach ($site_lst as $site) {
            if (substr($url,0,strlen($site))==$site) {
                $adscode=substr($url,strlen($site));
                $find = True;
            }
        }

        // if not in site lst, return a URL error
        if (!$find) ErrorURL();
    } elseif (substr($url,0,34)=="https://ui.adsabs.harvard.edu/abs/") {
        $adscode=substr($url,34,19);
        $find = True;
    }


    if (check_repeat($conn, $adscode)) {
        RepeatRef($conn,$adscode);
    } else {
        return NewADSParser($adscode, $url);

        /*
        if (
             (substr($adscode,4,4)=='A&A.') or
             (substr($adscode,4,4)=='A&C.') or
             (substr($adscode,4,5)=='A&AS.') or
             (substr($adscode,4,6)=='Ap&SS.')
           ) {
            return ADSParser2($adscode, $url);
        } else {
           return ADSParser($adscode, $url);
        }
        */
    }
}

function check_repeat($conn, $adscode) {
    // if repeat, return true, else return false
    $rid = get_rid_by_adscode($conn, $adscode);
    if ($rid==0)  return false;
    else return true;
}

function get_rid_by_adscode($conn,$adscode) {
    $adscode = str_replace('%26','&',$adscode);
    $sql = "select id from paper where adscode='$adscode'";
    $stat = $conn->query($sql);
    $count = $stat->rowCount();
    if ($count==0) {
        return 0;
    } elseif ($count==1) {
        $row = $stat->fetch(PDO::FETCH_ASSOC);
        return $row['id'];
    } else {
        echo 'ERROR: repeat adscode: '.$adscode;
        return false;
    }
}

function RepeatRef($conn,$adscode) {
    $rid = get_rid_by_adscode($conn,$adscode);
    header("Location:ref-".$rid);
}

function ErrorURL() {
    $_SESSION["err_msg"]="URL地址不符合要求.";
    header("Location:url.php");
}

function ADSParser($adscode, $url) {

    $i = stripos($url,'/abs/');
    $site = substr($url,0,$i);

    $url2=$site."/cgi-bin/nph-abs_connect?bibcode=".$adscode;
    $url2.="&data_type=Custom&format=";
    $sep="[mark]";
    $url2.=$sep."%25g".$sep."%25F";
    $url2.=$sep."%25T".$sep."%25B".$sep."%25K";
    $url2.=$sep."%25J".$sep."%25V".$sep."%25p".$sep."%25P";
    $url2.=$sep."%25Y".$sep."%25D";
    $url2.=$sep."%25d".$sep."%25X".$sep."%25R".$sep."%25q";
    $url2.=$sep."&return_fmt=LONG&nocookieset=1";
    $content_arr=file($url2);
    $content=implode("",$content_arr);
    $content=str_replace("\n","",$content);
    // fix ADS query bug
    $content=str_replace("mark]",$sep,$content);
    $content=str_replace("[[mark]",$sep,$content);
    $info=explode($sep,$content);
    $bibtex = GetBibtex($adscode, $url);
    $info[16] = $bibtex;
    return $info;
}


function parse_ads_meta($content) {
    $meta = array();
    foreach ($content as $row) {
        $row = trim($row);
        if ( (substr($row,0,5)=='<meta') and (strlen(strip_tags($row))==0) ) {
            $g = explode('"',$row);
            for ($i=0;$i<count($g);$i++) {
                if (trim($g[$i])=='<meta name=') {
                    $key = $g[$i+1];
                } elseif (trim($g[$i])=='content=') {
                    $value = $g[$i+1];
                }
            }

            $meta[$key] = $value;
        }
    }
    return $meta;
}

function ADSParser2($adscode,$url) {

    $content = file($url);
    $find = False;
    $end  = False;
    $find_abstract = False;

    $meta = parse_ads_meta($content);

    $title    = array_key_exists('citation_title',        $meta)?$meta['citation_title']:'';
    $authors  = array_key_exists('citation_authors',      $meta)?$meta['citation_authors']:'';
    $journal  = array_key_exists('citation_journal_title',$meta)?$meta['citation_journal_title']:'';
    $volume   = array_key_exists('citation_volume',       $meta)?$meta['citation_volume']:'';
    $page1    = array_key_exists('citation_startingPage', $meta)?$meta['citation_startingPage']:'';
    $page2    = array_key_exists('citation_endingPage',   $meta)?$meta['citation_endingPage']:'';
    $keywords = array_key_exists('citation_keywords',     $meta)?$meta['citation_keywords']:'';
    $date     = array_key_exists('citation_date',         $meta)?$meta['citation_date']:'';
    $arxiv    = array_key_exists('citation_arxiv_id',     $meta)?$meta['citation_arxiv_id']:'';
    $doi      = array_key_exists('citation_doi',          $meta)?$meta['citation_doi']:'';

    $year     = substr($date,3,4);

    if (substr($adscode,4,5)=='A&AS.')      $journal2 = 'A&AS';
    elseif (substr($adscode,4,4)=='A&A.')   $journal2 = 'A&A';
    elseif (substr($adscode,4,6)=='Ap&SS.') $journal2 = 'Ap&SS';


    // fix the bug of missing page in A&A
    if ((strlen($page1)==0) and ($journal2=='A&A')) {
        $tmp = explode(",",$meta['dc.source']);
        $key_page = 'id.';
        foreach ($tmp as $g) {
            $g = trim($g);
            if (substr($g,0,strlen($key_page))==$key_page) {
                $page1 = trim(substr($g,strlen($key_page)));
                if (substr($page1,0,1)=='A') $page1 = substr($page1,1);
            }
        }
    }

    // fix the bug of missing page in Ap&SS
    if ((strlen($page1)==0) and ($journal2=='Ap&SS')) {
        $tmp = explode(",",$meta['dc.source']);
        $key_page = 'article id.';
        foreach ($tmp as $g) {
            $g = trim($g);
            if (substr($g,0,strlen($key_page))==$key_page) {
                $page1 = trim(substr($g,strlen($key_page)));
            }
        }
    }


    // Locate affliation and Abstract
    //$key_affi = '<tr><td nowrap valign="top" align="left"><b>Affiliation:</b></td><td><br></td><td align="left" valign="top">';
    $key_affi = '<tr><td valign="top" align="left"><b>Affiliation:</b></td><td><br></td><td align="left" valign="top">';
    $key_abstract = '<h3 align="center">';

    // searching for affliations and abstract
    foreach ($content as $row) {

        // locating the searching area
        if (!$end and (trim($row)=='<table><tbody>'))  $find = True;
        elseif (!$end and $find and (trim($row)=='</tbody></table>'))  $end = True;

        // found alliliation
        elseif (($find) and !$end) {
            $row = trim($row);
            if (substr($row,0,strlen($key_affi))==$key_affi) {
                // parse affiliations
                $tmp = substr($row,strlen($key_affi));
                $tmp = preg_replace('/ <EMAIL>.*</EMAIL>/','',$tmp);
                $affi = html_entity_decode(strip_tags($tmp));
            }

        // found abstract
        } elseif ($end) {
            if ( !$find_abstract and
                 (strlen($row)>strlen($key_abstract)) and
                 (substr($row,0,strlen($key_abstract))==$key_abstract) and 
                 (trim(strip_tags(trim($row)))=='Abstract')
                ) {
                $find_abstract = True;
                $abstract = '';
            } elseif ($find_abstract and trim($row)=='<hr>') $find_abstract = False;
            elseif ($find_abstract) {
                $row = trim($row);
                if (strlen($row)>0)  $abstract = "$abstract$row ";
            }
        }
    }
    $abstract = str_replace('<p/> ',"\n",$abstract);

    // encode special html characters
    $abstract = htmlentities($abstract);

    $bibtex = GetBibtex($adscode, $url);

    return array('',$authors,$affi,$title,$abstract,$keywords,$journal,$volume,$page1,$page2,
                 $year, $date,$doi,$arxiv,$adscode,$journal2,$bibtex);
}


function NewADSParser($adscode, $url) {
    global $ADS_TOKEN;

    $tmp_adscode = str_replace('&','%26',$adscode);
    $site = "https://api.adsabs.harvard.edu/v1";
    $fl_lst = array("title", "abstract", "author", "aff", "keyword",
                    "year", "bibstem", "pub", "volume", "issue", "page",
                    "pubdate", "doi", "identifier");
    $fl_str = implode(",", $fl_lst);
    $url = "$site/search/query?q=bibcode:$tmp_adscode&fl=$fl_str";
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: Bearer $ADS_TOKEN"));
    $url_content = curl_exec($ch);
    $content = json_decode($url_content, true);

    $data = $content["response"]["docs"][0];

    // find arxiv id
    $arxiv = "";
    foreach($data["identifier"] as $idt) {
        if (substr($idt,0,6)=="arXiv:") {
            $arxiv = $idt;
            break;
        }
    }

    $journal = $data["pub"];
    global $journal_name_abbr_lst;
    if (array_key_exists($journal, $journal_name_abbr_lst)) {
        $journal_abbr = $journal_name_abbr_lst[$journal];
    } else {
        $journal_abbr = "";
    }

    return array(
        "authors"       => "|".implode("|", $data["author"])."|",
        "affi"          => "|".implode("|", $data["aff"])."|",
        "title"         => $data["title"][0],
        "abstract"      => $data["abstract"],
        "keywords"      => "|".implode("|", $data["keyword"])."|",
        "journal"       => $journal,
        "volume"        => $data["volume"],
        "page1"         => $data["page"][0],
        "page2"         => "",
        "year"          => $data["year"],
        "date"          => $data["pubdate"],
        "doi"           => $data["doi"][0],
        "arxiv"         => $arxiv,
        "adscode"       => $adscode,
        "journal_abbr"  => $journal_abbr,
        "bibtex"        => "",
    );
}

function GetBibtex($adscode,$url) {

    $i = stripos($url,'/abs/');
    $site = substr($url,0,$i);
    $url2 = "$site/cgi-bin/nph-bib_query?bibcode=$adscode&data_type=BIBTEX&db_key=AST&nocookieset=1";
    $content_arr=file($url2);
    $text = "";
    $start = False;
    foreach ($content_arr as $row) {
        if (strlen(trim($row))==0) {
            continue;
        }
        if (substr($row,0,1)=='@') {
            $start = True;
        }
        if ($start) {
            $text = $text.$row;
        }
    }
    return $text;

}

function AuthorParser($authorlst) {
    if (strpos($authorlst, ';')==false) {
        $author=str_replace(", ","|",$authorlst);
        $author=str_replace(",","|",$author);
    } else {
        $author=str_replace("; ","|",$authorlst);
        $author=str_replace(";","|",$author);
        $author=str_replace(", "," ",$author);
        $author=str_replace(","," ",$author);
    }
    $author="|".$author."|";
    $author=str_replace(" |","|",$author);
    $author=str_replace("| ","|",$author);
    $author=str_replace("||","|",$author);

    $author = addslashes($author);
    return $author;
}

function AuthorAbbr($author,$num=3) {
    // display an abbreivated author list. as "Zechmeister M. et al."
    // $num is number of authors to be displayed. default = 3
    $author_array=explode("|",$author);
    $count=0;
    $res="";
    foreach ($author_array as $author) {
        if ((trim($author)!="")and($count<$num)) {
            if ($res!="") {
                $res=$res.", ".$author;
            } else {
                $res=$res.$author;
            }
            $count=$count+1;
        }
    }
    if ($count==$num) {
        $res=$res." et al.";
    }
    return $res;
}


function get_paper_abbr($author,$year,$journal,$volume,$page) {
    $name = explode("|",$author);
    $g = explode(" ",$name[1]);
    $author1 = '';
    foreach ($g as $e) {
        if (substr($e,-1)!='.') {
            $author1 .= ' '.$e;
        }
    }
    $res = trim($author1).", $year, $journal, $volume, $page";
    return $res;
}



function AuthorLink($author) {
    $author_arr=explode("|",$author);
    $count=0;
    $res="";
    foreach ($author_arr as $ind) {
        if (trim($ind)!="") {
            $ind="<a href=\"search-".str_replace(" ","+",$ind)."\">".$ind."</a>";
            if ($count==0) {
                $res=$res.$ind;
                $count=$count+1;
            } elseif ($count!=0) {
                $res=$res.", ".$ind;
                $count=$count+1;
            }
        }
    }
    return $res;
}

function UpdateTag($tag) {
    // If tag do not exist, insert a new tag
    $row=mysql_fetch_row(mysql_query("select id from tag where tag='$tag'"));
    if ($row[0]==0) {
        mysql_query("insert into tag (tag) values ('$tag')");
    }

}

function UpdateTagCount() {
    $res=mysql_query("select tag from tag");
    while($row=mysql_fetch_array($res)) {
        $query="select count(*) from paper where tag like '%|$row[tag]|%'";
        $row2=mysql_fetch_row(mysql_query($query));
        mysql_query("update tag set `count`=$row2[0] where tag='$row[tag]'");
    }
    mysql_query("delete from tag where count=0");
}

function TagReduce($glue,$glue2,$input) {
    $lst = explode($glue,$input);
    $lst2 = array();
    foreach ($lst as $e) {
        $str = trim($e);
        if (strlen($str)>0) {
            array_push($lst2,$str);
        }
    }
    return implode($glue2,$lst2);
}

function TagParse($input) {
    $text = TagReduce('|','|',$input);
    $text = TagReduce(',','|',$text);
    $text = TagReduce(':',':',$text);
    $text = TagReduce(' ',' ',$text);
    return '|'.$text.'|';
}


function TagInverseParser($tag_str) {
    $tag_arr=explode("|",$tag_str);
    $res="";
    $count=0;
    foreach ($tag_arr as $ind) {
        if (trim($ind)!="") {
            if ($count==0) {
                $res.=$ind;
            } else {
                $res.=", ".$ind;
            }
            $count+=1;
        }
    }
    return $res;
}

function TagReplace($tag) {
    // translate URL to db string
    $tag = str_replace(":",".",$tag);
    $tag = str_replace(" ","_",$tag);
    //$tag=str_replace("/","_2f_",$tag);
    //$tag=str_replace("[","_5b_",$tag);
    //$tag=str_replace("]","_5d_",$tag);
    //$tag=str_replace(" ","_",$tag);
    return $tag;
}

function TagInverseReplace($tag) {
    // translate db string to URL
    $tag = str_replace(".",":",$tag);
    $tag = str_replace("_"," ",$tag);
    //$tag=str_replace("_2f_","/",$tag);
    //$tag=str_replace("_5b_","[",$tag);
    //$tag=str_replace("_5d_","]",$tag);
    //$tag=str_replace("_"," ",$tag);
    return $tag;
}

function TagLink($tag_lst) {
    $count = 0;
    foreach ($tag_lst as $tag) {
        $link=TagReplace($tag);
        $ind="<a href=\"tag-$link\">$tag</a>";
        if ($count==0) {
            $res.=$ind;
        } elseif ($count!=0) {
            $res.=", ".$ind;
        }
        $count=$count+1;
    }

    if ($count==0) {
        return "没有标签";
    } else {
        return $res;
    }
}

function ShowPrivacy($privacy) {
    if ($privacy==0) {
        return "公开";
    } elseif ($privacy==2) {
        return "私人";
    }
}

function ShowRead($read) {
    if ($read==5) {
        $res="★★★★★";
    } elseif ($read==4) {
        $res="★★★★☆";
    } elseif ($read==3) {
        $res="★★★☆☆";
    } elseif ($read==2) {
        $res="★★☆☆☆";
    } elseif ($read==1) {
        $res="★☆☆☆☆";
    } elseif ($read==0) {
        $res="☆☆☆☆☆";
    }
    return "<span class=\"paper_read\">".$res."</span>";
}

function cutstr($string, $length) {
// Cut UTF-8 string
    preg_match_all("/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/", $string, $info);  
    for($i=0; $i<count($info[0]); $i++) {
        $wordscut .= $info[0][$i];
        $j = ord($info[0][$i]) > 127 ? $j + 2 : $j + 1;
        if ($j > $length - 3) {
            return $wordscut." ...";
        }
    }
    return join('', $info[0]);
}

function show_item($conn,$rid) {
    // return a paper list as shown in the index page.
    $ref = new ref_item($conn,$rid);
    //echo $ref->adscode;


    echo "<div class=\"paper_lst\">\n";
    //echo "    <div class=\"paper_fig\">";
    //if (isset($_GET["user"])) {
    //    echo "<a href=\"user-$_GET[user]\"><img src=\"img/user/$_GET[user].jpg\"></a></div>\n";
    //} else {
    //    echo "<a href=\"user-$row[creater]\"><img src=\"img/user/$row[creater].jpg\"></a></div>\n";
    //}
    //echo "    <div class=\"paper_info\">\n";
    echo "        <div class=\"paper_title\">\n";
    echo "            <h4><a href=\"ref-".$ref->rid."\">".$ref->title."</a></h4>\n";
    //if (isset($_GET["user"])) {
    //    $row2 = mysql_fetch_array(mysql_query("select * from users where username='$_GET[user]'"));
    //    $row3 = mysql_fetch_array(mysql_query("select * from fav where creater='$_GET[user]' and adscode='$row[adscode]'"));
    //} else {
    //    $row2 = mysql_fetch_array(mysql_query("select * from users where username='$row[creater]'"));
    //    $row3 = mysql_fetch_array(mysql_query("select * from fav where creater='$row[creater]' and adscode='$row[adscode]'"));
    //}
    //echo "            <p class=\"paper_comment\">";
    //echo "<a href=\"user-$row2[username]\">$row2[fullname]</a> ";
    //echo "添加于 ".$ref->create_time." ";
    //echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
    //echo "权限:".ShowPrivacy("$row3[privacy]");
    //echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
    //echo "阅读优先级:".ShowRead("$row3[read]")."</p>\n";
    echo "        </div>\n";
    echo "        <ul>\n";
    echo "            <li>标签：".TagLink($ref->tag_lst)."</li>\n";
    echo "            <li>来源：$ref->author_abbr, $ref->year,
                      <span class=\"paper_journal\">$ref->journal2</span>,
                      <span class=\"paper_vol\">$ref->volume</span>, $ref->page.</li>\n";
    echo "            <li>摘要：$ref->digest</li>\n";
  //echo "            <li>作者：".AuthorAbbr($row["author"])."</li>\n";


    //$query="select note from `note` where adscode='$row[adscode]' order by update_time limit 1";
    //$note_res=mysql_query($query);
    //if (mysql_num_rows($note_res)>0) {
    //    $row=mysql_fetch_array($note_res);
    //    $note=$row["note"];
    //    echo "            <li>笔记：".cutstr($note,90)."</li>\n";
    //}

    echo "        </ul>\n";
    //echo "    </div>\n";
    echo "</div>\n";
}

function ShowNoteHeader($cri) {
    if ($cri) {
        return true;
    } else {
        echo "    <li class=\"paper_detail_lst\">\n";
        echo "        <h5>读书笔记</h5>\n";
        echo "        <ul class=\"note_lst\">\n";
        return true;
    }
}

function FormatNote($note) {
    $res=str_replace("\r\n","<br />",$note);
    $res=str_replace("\n","<br />",$res);
    $res=str_replace("[red]", "<span class=\"note_red\">",$res);
    $res=str_replace("[/red]","</span>",$res);
    $res=str_replace("[blue]","<span class=\"note_blue\">",$res);
    $res=str_replace("[/blue]","</span>",$res);
    $res=str_replace("[gray]","<span class=\"note_gray\">",$res);
    $res=str_replace("[/gray]","</span>",$res);
    $res=str_replace("[green]","<span class=\"note_green\">",$res);
    $res=str_replace("[/green]","</span>",$res);
    $res=str_replace("[yellow]","<span class=\"note_yellow\">",$res);
    $res=str_replace("[/yellow]","</span>",$res);
    $res=str_replace("[purple]","<span class=\"note_purple\">",$res);
    $res=str_replace("[/purple]","</span>",$res);
    $res=str_replace("[b]","<span class=\"note_bold\">",$res);
    $res=str_replace("[/b]","</span>",$res);
    $res=str_replace("[i]","<span class=\"note_italic\">",$res);
    $res=str_replace("[/i]","</span>",$res);
    $res=str_replace("[del]","<span class=\"note_del\">",$res);
    $res=str_replace("[/del]","</span>",$res);
    return $res;
}

function ShowSingleNote($row) {
    echo "            <li>\n";
    $row2=mysql_fetch_row(mysql_query("select fullname from users where username='$row[creater]'"));
    echo "            <div class=\"note_fig\">";
    echo "<a href=\"user-$row[creater]\" title=\"$row2[0]\"><img src=\"img/user/$row[creater].jpg\"></a></div>\n";
    $note=FormatNote($row["note"]);
    echo "            <div class=\"note_rec\"><p>$note</p></div>\n";
    echo "            <div class=\"note_info\"><p>";

    if ($row["creater"]==$_SESSION["username"]) {
        echo "<span class=\"note_action\">";
        echo "<a href=\"edit.php?type=edit_note&nid=$row[id]\">编辑</a>&nbsp;&nbsp;";
        echo "<a href=\"save.php?type=del_note&nid=$row[id]\" ";
        echo "onclick=\"return confirm('确定删除这条读书笔记吗?\\r\\n删除后不能恢复')\">删除</a>&nbsp;&nbsp;";
        echo "</span>";
    }

    echo "$row2[0] 于 $row[create_time] 添加";
    echo "</p></div>\n";
    echo "            </li>\n";
}

function echo_a_note($note,$conn) {
    //echo "            <div class=\"note_fig\">";
    //echo "<a href=\"user-$row[creater]\" title=\"$row2[0]\"><img src=\"img/user/$row[creater].jpg\"></a></div>\n";
    $text = new doc_text();
    echo "            <li>
          <div class=\"note_rec\">\n";
    echo $text->get_html($note->text,$conn);
    echo "
          </div>
          <div class=\"note_info\">
          <p>
          <span class=\"note_action\">
          <a href=\"edit.php?type=edit_note&nid=$note->nid\">编辑</a>&nbsp;&nbsp;
          <a href=\"save.php?type=del_note&nid=$note->nid\" 
           onclick=\"return confirm('确定删除这条读书笔记吗?\\r\\n删除后不能恢复')\">删除</a>&nbsp;&nbsp;
          </span>
          添加于 $note->create_time
          </p>
          </div>
          </li>\n";

}

function ShowNoteFooter($cri) {
    if ($cri) {
        echo "        </ul><!--class=note_lst-->\n";
        echo "    </li>\n";
    }
}

function ShowSingleFav($user) {
    echo "        <li>";
    $row=mysql_fetch_row(mysql_query("select fullname from users where username='$user'"));
    echo "<a href=\"user-$user\" title=\"$row[0]\"><img src=\"img/user/$user.jpg\"></a>";
    echo "</li>\n";
}

function ShowWhoFav($adscode) {
    $res1=mysql_query("select * from fav where adscode='$adscode' and privacy=0");
    echo "        <ul id=\"who_fav_lst\">\n";
    while($row2=mysql_fetch_array($res1)) {
        ShowSingleFav($row2["creater"]);
    }
    echo "        </ul>\n";
}

function ShowErrorMsg() {
    if ($_SESSION["err_msg"]!="") {
        echo "<div id=\"err_msg\"><span id=\"msg\">$_SESSION[err_msg]</span></div>\n";
        $_SESSION["err_msg"]="";
    }
}


function check_adscode($conn, $adscode) {
    $sql = "select id from paper where adscode='$adscode'";
    $stat = $conn->query($sql);
    $count = $stat->rowCount();
    if ($count == 0) {
        return 0;
    }
    $row = $stat->fetch();
    return $row[0];
}

function check_arxiv($conn, $arxiv) {
    $sql = "select id from paper where arxiv='$arxiv'";
    $stat = $conn->query($sql);
    $count = $stat->rowCount();
    if ($count == 0) {
        return 0;
    }
    $row = $stat->fetch();
    return $row[0];
}

function CheckFav($adscode) {
    $res=mysql_query("select id from fav where adscode='$adscode' and creater='$_SESSION[username]'");
    if (mysql_num_rows($res)==0) {
        return 0;
    } else {
        $row=mysql_fetch_row($res);
        return $row[0];
    }
}

function CheckNote($adscode) {
    $query="select id from note where adscode='$adscode' and creater='$_SESSION[username]'";
    if (mysql_num_rows(mysql_query($query))==0) {
        return false;
    } else {
        return true;
    }
}

function show_page_nav($total_num,$current_page,$items_per_page,$url) {
    $total_page = ceil($total_num/$items_per_page);
    echo "<div id=\"page_nav\">\n";
    echo "<span class=\"page page_total_num\">";
    echo "$total_num papers in $total_page pages</span>\n";
    $page_index=1;

    if ($current_page>1) {
        echo_page_link($url,$page_index,'|&lt;');
        echo_page_link($url,$current_page-1,'&lt;');
    }

    while ($page_index<=$total_page) {
        if (abs($page_index - $current_page)<=3) {
            if ($current_page==$page_index) {
                echo "    <span class=\"page current_page\">$page_index</span>\n";
            } else {
                // here $page_index = $page_display
                echo_page_link($url, $page_index, $page_index);
            }

        }
        $page_index+=1;
    }

    if ($current_page<$total_page) {
        echo_page_link($url,$current_page+1,'&gt;');
        echo_page_link($url,$total_page,'&gt;|');
    }

    echo "</div>\n";
}

function echo_page_link($url, $page_index, $page_display) {
    // $url: url of website
    // $page_index: index of page in the link
    // $page_display: displayed characters
    echo "    <span class=\"page\">";
    if (strpos($url,"page")===false) {
        $tmp=$url."-page".$page_index;
        $tmp=str_replace("/-","/",$tmp);
    } else {
        $arr=explode("page",$url);
        $tmp=$arr[0]."page".$page_index;
    }
    echo "<a href=\"$tmp\">$page_display</a>";
    echo "</span>\n";
}

function refresh_tags($conn,$rid) {
    $sql = "select tag from ref.tag where id=$rid";
    $row = $conn->query($sql)->fetch();
    $text = trim($row[0]);
    $lst = explode('|',$text);
    foreach ($lst as $word) {
        $word = trim($word);
        if (strlen($word)>0) {
            $sql = "select count(id) from tags where tag='$word'";
            $row = $conn->query($sql)->fetch();
            $count = $row[0];
            if ($count == 0) {
                $conn->exec("insert into tags (tag) values ('$word')");
            }
        }
    }

    refresh_all_tags($conn);

}


function refresh_all_tags($conn) {
    $sql = "select id,tag from ref.tags";
    $res = $conn->query($sql)->fetchAll();
    foreach ($res as $row) {
        $id  = $row[0];
        $tag = trim($row[1]);
        $sql2 = "select id from ref.tag where tag like '%|$tag|%'";
        $count = $conn->query($sql2)->rowCount();
        $conn->exec("update ref.tags set count=$count where id=$id");
    }
    $conn->exec("delete from ref.tags where count=0");
}

function get_rid_by_ads($ads) {
    $res = mysql_query("select id from paper where adscode like '$ads'");
    if (mysql_num_rows($res)==0) {
        $row=mysql_fetch_row($res);
        return $row[0];
    } else {
        return 0;
    }

}

$journal_abbr_name_lst = array(
    "A&A"   => "Astronomy and Astrophysics",
    "A&AR"  => "Astronomy and Astrophysics Review",
    "A&ARv" => "The Astronomy and Astrophysics Review",
    "A&AS"  => "Astronomy and Astrophysics Supplement Series",
    "A&AT"  => "Astronomical and Astrophysical Transactions",
    "A&C"   => "Astronomy and Computing",
    "AAS"   => "Bulletin of the American Astronomical Society",
    "AcASn" => "Acta Astronomica Sinica",
    "AcMik" => "Mikrochimica Acta",
    "ADNDT" => "Atomic Data and Nuclear Data Tables",
    "AdSpR" => "Advances in Space Research",
    "AJ"    => "The Astronomical Journal",
    "ApJ"   => "The Astrophysical Journal",
    "ApJS"  => "The Astrophysical Journal Supplement Series",
    "ApL"   => "Astrophysical Letters",
    "ApOpt" => "Applied Optics",
    "ArA"   => "Arkiv for Astronomi",
    "ARA&A" => "Annual Review of Astronomy and Astrophysics",
    "ARep"  => "Astronomy Reports",
    "AsBio" => "Astrobiology",
    "ASPC"  => "Astronomical Society of the Pacific Conference Series",
    "AstBu" => "Astrophysical Bulletin",
    "AstL"  => "Astronomy Letters",
    "AzAJ"  => "Azerbaijani Astronomical Journal",
    "AZh"   => "Astronomicheskii Zhurnal",
    "BAAS"  => "Bulletin of the American Astronomical Society",
    "BASI"  => "Bulletin of the Astronomical Society of India",
    "CaJPh" => "Canadian Journal of Physics",
    "ChA&A" => "Chinese Astronomy and Astrophysics",
    "ChJAA" => "Chinese Journal of Astronomy and Astrophysics",
    "ChPhL" => "Chinese Physics Letters",
    "ChPhy" => "Chinese Physics",
    "ChRv"  => "Chemical Reviews",
    "CoAst" => "Communications in Asteroseismology",
    "EAS"   => "EAS Publications Series",
    "EPJD"  => "European Physical Journal D",
    "EPJWC" => "European Physical Journal Web of Conferences",
    "GeCoA" => "Geochimica et Cosmochimica Acta",
    "GeoRL" => "Geophysical Research Letters",
    "IAUS"  => "IAU Symposium",
    "IBVS"  => "Information Bulletin on Variable Stars",
    "Icar"  => "Icarus",
    "IJAsB" => "International Journal of Astrobiology",
    "JApA"  => "Journal of Astrophysics and Astronomy",
    "JATIS" => "Journal of Astronomical Telescopes, Instruments, and Systems",
    "JAVSO" => "Journal of the American Association of Variable Star Observers (JAAVSO)",
    "JBIS"  => "Journal of the British Interplanetary Society",
    "JCAP"  => "Journal of Cosmology and Astro-Particle Physics",
    "JDSO"  => "Journal of Double Star Observations",
    "JGR"   => "Journal of Geophysical Research",
    "JKAS"  => "Journal of Korean Astronomical Society",
    "JOSAA" => "Journal of the Optical Society of America A",
    "JOSAB" => "Journal of the Optical Society of America B Optical Physics",
    "JPCRD" => "Journal of Physical and Chemical Reference Data",
    "JPhB"  => "Journal of Physics B Atomic Molecular Physics",
    "JPhCS" => "Journal of Physics Conference Series",
    "JPhG"  => "Journal of Physics G Nuclear Physics",
    "JQSRT" => "Journal of Quantitative Spectroscopy and Radiative Transfer",
    "MNRAS" => "Monthly Notices of the Royal Astronomical Society",
    "MSAIS" => "Memorie della Società Astronomica Italiana Supplement",
    "Msngr" => "The Messenger",
    "NatAs" => "Nature Astronomy",
    "NatGe" => "Nature Geoscience",
    "NatPh" => "Nature Physics",
    "NatSR" => "Scientific Reports",
    "Natur" => "Nature",
    "NewA"  => "New Astronomy",
    "NewAR" => "New Astronomy Review",
    "NJPh"  => "New Journal of Physics",
    "NuPhA" => "Nuclear Physics A",
    "OExpr" => "Optics Express",
    "OptL"  => "Optics Letters",
    "PABei" => "Progress in Astronomy",
    "PASA"  => "Publications of the Astronomical Society of Australia",
    "PASJ"  => "Publications of the Astronomical Society of Japan",
    "PASP"  => "Publications of the Astronomical Society of the Pacific",
    "PhR"   => "Physics Reports",
    "PhRv"  => "Physical Review",
    "PhRvA" => "Physical Review A",
    "PhRvC" => "Physical Review C",
    "PhRvD" => "Physical Review D",
    "PhRvE" => "Physical Review E",
    "PhRvL" => "Physical Review Letters",
    "PhST"  => "Physica Scripta Volume T",
    "PhT"   => "Physics Today",
    "PhyS"  => "Physica Scripta",
    "PNAOJ" => "Publications of the National Astronomical Observatory of Japan",
    "PNAS"  => "Proceedings of the National Academy of Science",
    "RAA"   => "Research in Astronomy and Astrophysics",
    "RNAAS" => "Research Notes of the American Astronomical Society",
    "Sci"   => "Science",
    "SciA"  => "Science Advances",
    "SciAm" => "Scientific American",
    "SoPh"  => "Solar Physics",
    "SPIE"  => "Society of Photo-Optical Instrumentation Engineers (SPIE) Conference Series",
    "SSRv"  => "Space Science Reviews",
    "SvAL"  => "Soviet Astronomy Letters",
    "VA"    => "Vistas in Astronomy",
    "ZA"    => "Zeitschrift fur Astrophysik",
    "ZPhy"  => "Zeitschrift fur Physik",
);


$journal_name_abbr_lst = array_flip($journal_abbr_name_lst);


function search_fulltext($ref) {
    if ((substr($ref->adscode,4,8)=='astro.ph') or
        (substr($ref->adscode,4,5)=='arXiv')) {
        # arxiv paper
        $path="fulltext/arxiv/$ref->adscode.pdf";
        if (file_exists($path)) {
            return $path;
        } else {
            return "";
        }
    } elseif (substr($ref->adscode,9,4)=='book') {
        # book
        $path="fulltext/book/$ref->adscode.pdf";
        if (file_exists($path)) {
            return $path;
        } else {
            return "";
        }
    } else {
        $journal_abbr = str_replace('&','',$ref->journal2);
        $volume       = $ref->volume;
        $year         = $ref->year;
        $adscode      = $ref->adscode;
        $path_lst = array(
            "fulltext/$journal_abbr/$adscode.pdf",
            "fulltext/$journal_abbr/$volume/$adscode.pdf",
            "fulltext/$journal_abbr/$year/$adscode.pdf",
        );
        foreach ($path_lst as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }
        return "";
    }
}

?>
