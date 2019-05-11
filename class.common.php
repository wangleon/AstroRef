<?php
/*
common classes for ref, docs, and formula.
*/

include_once("sysconf.inc");

function connect_db($dbname) {
    global $DB_HOST;
    global $DB_USER;
    global $DB_PWD;
    //mysql_connect($DB_HOST,$DB_USER,$DB_PWD) or die(mysql_error());
    //mysql_select_db($dbname) or die(mysql_error());
    //mysql_query("set names utf8");
    $pdo = new PDO("mysql:host=$DB_HOST;dbname=$dbname",$DB_USER,$DB_PWD);
    $pdo->exec('set names utf8');
    return $pdo;
}


///////////////////////////////////////////////////////////////////
class doc_text {
    /*
    class for parse docs text
    */

    var $ref_path = '/ref/ref-';

    function get_html($text, $conn) {
        $text = $this->replace_special_char($text);
        $text = $this->make_clickable($text, $conn);
        $text = $this->seperate_paragraph($text);
        $text = $this->add_style($text);
        //$text = $this->make_latex($text);
        return $text;
    }

    function replace_special_char($text) {
        $text = htmlentities($text,ENT_QUOTES,'UTF-8');
        return $text;
    }

    function seperate_paragraph($text) {
        $text  = str_replace("\r","",$text);
        $text  = "<p>".str_replace("\n","</p>\n<p>",$text)."</p>\n";
        return $text;
    }

    function add_style($text) {
        $text=str_replace("<p>[code]</p>","<div class=\"code\">",$text);
        $text=str_replace("<p>[/code]</p>","</div>",$text);
        //$text = $this->parse_code_space($text);

        $group = array('del','b','i','underline',
                       'red','blue','green','yellow',
                       'white','pink','purple','gray');
        foreach($group as $e) {
            $text = str_replace("[$e]","<span class=\"$e\">",$text);
            $text = str_replace("[/$e]","</span>",$text);
        }
        return $text;
    }


    function make_clickable($text,$conn) {
        // make normal link
        /*$text = preg_replace(
                "/([[:alnum:]]+)://([^[:space:]>]*)([[:alnum:]#?/&=])/i",
                "<a href=\"\\1://\\2\\3\" target=\"_blank\">\\1://\\2\\3</a>",
                $text);
         */
        $text = preg_replace(
                "/(http|https|ftp|ftps)\:\/\/([a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?)/i",
                "<a href=\"\\1://\\2\" target=\"_blanck\">\\1://\\2</a>",
                $text);
         
        $text = preg_replace(
                "/([a-z0-9_]|\\-|\\.)+@([^[:space:]]*)([[:alnum:]-])/i",
                "<a href=\"mailto:\\1\">\\1</a>",
                $text);

        // arxiv and ads link shortage
        $text = str_replace(">http://arxiv.org/abs/",">arXiv: ",$text);
        $text = str_replace(">http://adsabs.harvard.edu/abs/",">",$text);

        // make ref link
        $text = preg_replace_callback("|\&lt\;ref-(\d+)\&gt\;|",
                "get_ref_cc", $text);

        // make ref link in another way
        $text = preg_replace_callback("|ref\:(\S+),(\d{4}),(\S+),(\S+),(\S+)|",
                "get_ref_cc2", $text);

        // another style
        //$link = "<a href=\"".$this->ref_path."$1\" target=\"_blank\">ref-$1</a>";
        //$text = preg_replace("|\&lt\;ref-(\d+)\&gt\;|",$link,$text);

        return $text;
    }

    function parse_code_space($text) {
        $reg  = '/(<div\sclass=\"code\">)([\s\S]*?)(<\/div>)/';
        $text = preg_replace_callback($reg, "get_code_space", $text);
        $reg  = '/(<a)([\s\S]*?)(>)/';
        $text = preg_replace_callback($reg, "get_code_space2", $text);
        return $text;
    }

    function make_latex($text) {
        $reg = '/<p>\[math\]([\s\S]*?)\[\/math\]<\/p>/';
        $text = preg_replace_callback($reg, "get_display_formula", $text);
        $reg = '/\[math\]([\s\S]*?)\[\/math\]/';
        $text = preg_replace_callback($reg, "get_inline_formula", $text);
        return $text;
    }
 
}


function get_ref_cc($matches) {
    // call back function of ref cross-citation
    global $conn;
    $rid = (int)$matches[1];
    $ref = new ref_cite($conn,$rid);

    $url = "/ref/ref-$rid";
    $title = $ref->author_abbr.", ".$ref->year.", ".$ref->journal2.", ".$ref->volume.", ".$ref->page." "."<ref-$rid>";
    $text = $ref->author_abbr." ".$ref->year;

    $link = "<a href=\"$url\" target=\"_blank\" title=\"$title\">$text</a>";
    return $link;
}

function get_ref_cc2($matches) {
    global $conn;
    // call back function of ref cross-citation
    $author   = $matches[1];
    $year     = $matches[2];
    $journal2 = str_replace('&amp;','&',$matches[3]);
    $volume   = $matches[4];
    $page     = $matches[5];

    $sql   = "select id from ref.paper where year=$year and journal2='$journal2' and volume='$volume' and page='$page'";
    $stat  = $conn->query($sql);
    $count = $stat->rowCount();
    if ($count==0) {
        return "$author,$year,$journal2,$volume,$page";
    }
    $row = $stat->fetch(PDO::FETCH_ASSOC);
    $rid = $row['id'];
    $ref = new ref_cite($conn, $rid);

    $url = "/ref/ref-$rid";
    $title = $ref->author_abbr.", ".$ref->year.", ".$ref->journal2.", ".$ref->volume.", ".$ref->page." "."<ref-$rid>";
    $text = $ref->author_abbr." ".$ref->year;

    $link = "<a href=\"$url\" target=\"_blank\" title=\"$title\">$text</a>";
    return $link;
}


function get_code_space($matches) {
    $code = str_replace(" ","&nbsp;",$matches[2]);
    return $matches[1].$code.$matches[3];
}

function get_code_space2($matches) {
    $code = str_replace("&nbsp;"," ",$matches[2]);
    return $matches[1].$code.$matches[3];
}

function get_text_forsave($text) {
    $text = str_replace('\r\n','\n',$text);
    return $text;
}

function get_text_foredit($text) {
    return $text;
}

function get_display_formula($matches) {
    $code = html_entity_decode($matches[1],ENT_QUOTES,'UTF-8');
    $formula = new latex_formula($code,"display");
    $res = "<img src=\"$formula->img_path\" alt=\"\$\$$code\$\$\" />";
    $res = "<div class=\"formula\">$res</div>";
    return $res;
}

function get_inline_formula($matches) {
    $code = html_entity_decode($matches[1],ENT_QUOTES,'UTF-8');
    $formula = new latex_formula($code,"inline");
    $res = "<img src=\"$formula->img_path\" alt=\"\$$code\$\" />";
    return $res;
}

///////////////////////////////////////////////////////////////////

class ref_cite {
    /*
    class for ref citation
    */
    function __construct($conn, $rid) {
        $this->rid = $rid;
        $sql = "select * from ref.paper where id=$rid";
        $row = $conn->query($sql)->fetch(PDO::FETCH_ASSOC);
        $this->adscode  = $row['adscode'];
        $this->author   = $row['author'];
        $this->journal  = $row['journal'];
        $this->journal2 = $row['journal2'];
        $this->year     = $row['year'];
        $this->volume   = $row['volume'];
        $this->page     = $row['page'];
        $this->page2    = $row['page2'];
        $this->date     = $row['date'];
        $this->doi      = $row['doi'];
        $this->arxiv    = $row['arxiv'];
        $this->create_time = $row['create_time'];
        $this->author_lst  = $this->get_author_lst($this->author);
        $this->author_abbr = $this->get_author_abbr($this->author_lst);

        $jour = str_replace('&','',$row['journal2']);
        if (strlen($row['volume'])>0) {
            $vol = $row['volume'];
        } else {
            $vol = '_';
        }
        $this->code     = $row['year'].'/'.str_replace('&','',$row['journal2']).'/'.$vol.'/'.$row['page'];
        $this->fulltext_path = $this->get_fulltext_path($this);
        $this->data_path     = $this->get_data_path($this);
    }

    function get_author_lst($author) {
        $lst = array();
        $authors = explode("|",$author);
        foreach ($authors as $name) {
            if (trim($name)!="") {
                array_push($lst,trim($name));
            }
        }
        return $lst;
    }

    function get_author_abbr($lst) {
        $res = $this->get_first_name($lst[0]);
        if (count($lst)==1) {
            $res .= '';
        } elseif (count($lst)==2) {
            $res .= " & ".$this->get_first_name($lst[1]);
        } else {
            $res .= " et al.";
        }
        return $res;
    }

    function get_first_name($name) {
        $g = explode(" ",$name);
        $res = "";
	    foreach ($g as $e) {
            if (substr($e,-1)!='.') {
                $res .= ' '.$e;
            }
        }
        return $res;
    }

    function get_fulltext_path() {
        $conf_lst = array('AcMik', 'AIPC', 'ASInC', 'ASPC', 'ASSP', 'EAS',
            'EPJWC','EPSC','ESASP','ESOC', 'IAUS','IAUTA','ICRC',
            'JPhCS','RMxAC','SPIE');
        $bulletin_lst = array('BAAS','BICDS','CiUO','IBVS','LicOB');
        $jour_novol_lst = array('LanB');

        if ((substr($this->adscode,4,8)=='astro.ph') or
            (substr($this->adscode,4,5)=='arXiv')) {
            # this is an arxiv paper
            return "fulltext/arxiv/$this->year/$this->adscode.pdf";
        } elseif (substr($this->adscode,9,4)=='book') {
            # this is a book
            return "fulltext/book/$this->adscode.pdf";
        } elseif (substr($this->adscode,9,4)=='conf') {
            # this is a conference article
            $journal = substr($this->adscode,4,5);
            $journal = str_replace('.','',$journal);
            return "fulltext/conference/$journal/$this->adscode.pdf";
        } elseif (substr($this->adscode,9,4)=='work') {
            # this is a conference article
            $journal = substr($this->adscode,4,5);
            $journal = str_replace('.','',$journal);
            return "fulltext/conference/$journal/$this->adscode.pdf";
        } elseif (in_array($this->journal2, $conf_lst)) {
            # this is a reference article
            $journal = str_replace('&','',$this->journal2);
            $vol     = $this->volume;
            $adscode = $this->adscode;
            return "fulltext/conference/$journal/$vol/$adscode.pdf";
        } elseif (in_array($this->journal2, $bulletin_lst)) {
            # this is a bulletin article
            $journal = str_replace('&','',$this->journal2);
            $vol     = $this->volume;
            $adscode = $this->adscode;
            return "fulltext/bulletin/$journal/$vol/$adscode.pdf";
        } elseif (in_array($this->volume,$jour_novol_lst)) {
            $journal = str_replace('&','',$this->journal2);
            return "fulltext/journal/$journal/$adscode.pdf";
        } else {
            # this is a normal journal paper
            $journal = str_replace('&','',$this->journal2);
            $vol     = $this->volume;
            $adscode = $this->adscode;
            return "fulltext/journal/$journal/$vol/$adscode.pdf";
        }
    }

    function get_data_path() {
       $journal = str_replace('&','',$this->journal2);
       $vol     = $this->volume;
       $page    = $this->page;
       return "data/$journal/$vol/$page";
    }

}

class ref_item extends ref_cite {
    /*
    class for ref item
    */
    function __construct($conn,$rid) {
        parent::__construct($conn,$rid);
        $this->title   = $this->get_title($conn,$this->rid);
        $this->tag_lst = $this->get_tag_lst($conn,$this->rid);
        $this->digest  = $this->get_digest($conn,$this->rid);
        $res           = $this->get_erratum($conn,$this->rid);
        $this->is_erratum  = $res[0];
        $this->has_erratum = $res[1];
        $this->rids        = $this->get_relation($conn,$this->rid);
    }

    function get_title($conn,$rid) {
        $sql = "select title from ref.detail where id=$rid";
        $row = $conn->query($sql)->fetch(PDO::FETCH_ASSOC);
        return $row['title'];
    }

    function get_tag_lst($conn,$rid) {
        $tag_lst = array();
        $sql   = "select tag from ref.tag where id=$rid";
        $stat  = $conn->query($sql);
        $row   = $stat->fetch(PDO::FETCH_ASSOC);
        $count = $stat->rowCount();
        if ($count > 0) {
            $tags = explode("|",$row['tag']);
            foreach ($tags as $tag) {
                if (trim($tag)!="") {
                    array_push($tag_lst,trim($tag));
                }
            }
        }
        return $tag_lst;
    }

    function get_digest($conn,$rid) {
        $sql = "select note from ref.note where rid=$rid
                order by create_time limit 1";
        $stat  = $conn->query($sql);
        $row   = $stat->fetch(PDO::FETCH_ASSOC);
        $count = $stat->rowCount();
        if ($count == 0) {
            return "没有摘要";
        } else {
            $g = explode("。",$row["note"]);
            return $g[0]."。";
        }
    }

    function get_erratum($conn,$rid) {
        $sql = "select is_erratum,has_erratum from ref.paper where id=$rid";
        $row = $conn->query($sql)->fetch(PDO::FETCH_ASSOC);
        return array($row['is_erratum'], $row['has_erratum']);
    }

    function get_relation($conn,$rid) {
        $sql = "select related_rids from ref.relation where id=$rid";
        $stat  = $conn->query($sql);
        $row   = $stat->fetch(PDO::FETCH_ASSOC);
        $count = $stat->rowCount();
        $group = array();
        if ($count==0) {
            return $group;
        } else{
            $exp = explode(',',$row['related_rids']);
            foreach ($exp as $g) {
                $g = trim($g);
                if (strlen($g)!=0) {
                    array_push($group,$g);
                }
            }
            return $group;
        }
    }

}

class ref_info extends ref_item {
    /*
    class for ref info
    */
    function __construct($conn,$rid) {
        parent::__construct($conn,$rid);
        $sql = "select abstract, affiliation, keyword, bibtex from ref.detail where id=$rid";
        $row = $conn->query($sql)->fetch(PDO::FETCH_ASSOC);
        $this->abstract    = $row['abstract'];
        $this->affiliation = $row['affiliation'];
        $this->keyword     = $row['keyword'];
        $this->bibtex      = $row['bibtex'];
        $this->notes       = $this->get_notes($conn,$rid);
    }


    function get_notes($conn,$rid) {
        $sql = "select id from ref.note where rid=$rid order by create_time";
        $stat  = $conn->query($sql);
        $res   = $stat->fetchAll(PDO::FETCH_ASSOC);
        $count = $stat->rowCount();
        $notes = array();
        if ($count>0) {
            foreach ($res as $row) {
                $nid = $row['id'];
                $note = new ref_note($conn,$nid);
                array_push($notes,$note);
            }
        }
        return $notes;
    }

}

class ref_note {
    /*
    class for ref_note
    */
    function __construct($conn,$nid) {
        $sql = "select * from ref.note where id=$nid";
        $row = $conn->query($sql)->fetch(PDO::FETCH_ASSOC);
        $this->nid         = $nid;
        $this->rid         = $row['rid'];
        $this->text        = $row['note'];
        $this->create_time = $row['create_time'];
        $this->update_time = $row['update_time'];
    }

}

////////////////////////////////////////////////////////////////////

class latex_formula {

    var $main_dir  = "formula/";
    #var $tmp_dir      = $this->main_dir."tmp/";
    //var $tmp_filename = $this->tmp_dir."tmp";
    //var $img_dir      = $this->main_dir."img";


    function __construct($code,$mode) {

        $this->img_density = 120;

        $this->latex_path   = "latex";
        $this->dvips_path   = "dvips";
        $this->convert_path = "convert";

        $this->code     = $code;
        $this->mode     = $mode;
        $this->size     = "11pt";
        $this->img_fmt  = "png";
        $this->formula_img_dir = '../formula/img/';
        $this->formula_tmp_dir = '../formula/tmp/';
        $this->tmp_to_img_path = '../img/';
        $this->tmp_filename    = 'tmp';
        $this->main_dir = getcwd();
        $this->img_name = $this->get_imgname();
        $this->img_path = $this->formula_img_dir.$this->img_name;

        if (! file_exists($this->img_path)) {
            $this->make_latex();
        }

    }

    function make_latex() {
        chdir($this->formula_tmp_dir);

        $latex_doc = $this->wrap_formula();

        // create temporary latex file
        $fp = fopen($this->tmp_filename.".tex","w");
        fputs($fp, $latex_doc);
        fclose($fp);

        // create temporary dvi file
        $command  = $this->latex_path." --interaction=nonstopmode ";
        $command .= $this->tmp_filename.".tex";
        $this->exec_cmd($command);

        // convert dvi file to postscript using dvips
        $command  = $this->dvips_path." -E ";
        $command .= $this->tmp_filename.".dvi";
        $command .= " -o ".$this->tmp_filename.".ps";
        exec($command);

        //convert ps to image and trim picture
        $command  = $this->convert_path;
        $command .= " -density ".$this->img_density;
        $command .= " -trim ";
        //$command .= " -transparent \"#FFFFFF\" ";
        $command .= $this->tmp_filename.".ps ";
        $command .= $this->img_name;
        exec($command);

        copy($this->img_name, $this->tmp_to_img_path.$this->img_name);
        $this->clean_tmp_dir();

        chdir($this->main_dir);
    }


    function wrap_formula() {
        $string  = "\documentclass[$this->size]{article}
                    \usepackage[latin1]{inputenc}
                    \usepackage{xcolor}
                    \usepackage{amsmath}
                    \usepackage{amsfonts}
                    \usepackage{amssymb}
                    \pagestyle{empty}
                    \begin{document}\n";

        if ($this->mode == 'inline') {
            // inline mode
            $string .= "$".$this->code."$\n";
        } else {
            // display math mode
            $string .= "$$".$this->code."$$\n";
        }
        $string .= "\end{document}\n";
        return $string;
    }

    function clean_tmp_dir() {
        unlink($this->tmp_filename.".tex");
        unlink($this->tmp_filename.".log");
        unlink($this->tmp_filename.".aux");
        unlink($this->tmp_filename.".dvi");
        unlink($this->tmp_filename.".ps");
        unlink($this->img_name);
    }


    function clean_dir($dir) {
        chdir($dir);
        $command = "rm -rf *";
        chdir($this->main_dir);
    }

    function exec_cmd($command) {
        $status_code = exec($command);
        if (!$status_code) {
            // if command run error
            echo "Error while executing: $command\n";
        }
    }

    function get_imgname() {
        $imgname .= hash("md5",$this->code);
        if ($this->mode=="inline") {
            // inline mode
            $imgname .= "_inline";
        }
        $imgname .= ".$this->img_fmt";
        return $imgname;
    }

}

?>
