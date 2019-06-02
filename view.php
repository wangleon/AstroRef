<?php
session_start();
include_once("header.php");
?>

    <div id="content">
    <div id="wrap">
    <!--<h2>文献阅读</h2>-->
    <div id="cen_body">
    <div id="main_body">

<?php

if (isset($_GET["id"])) {
    $ref = new ref_info($conn,$_GET["id"]);
    //mysql_query("update paper set count=count+1 where id=$id");
    ShowErrorMsg();
    //$fid=CheckFav($row["adscode"]);
    //echo "<p class=\"pt_fav\">";
    //echo_act_nav($id,$fid);
    //echo "</p>\n";
    echo "<div class=\"paper_title\">\n";
    echo "    <h4>".$ref->title."</h4>\n";
    //if ($fid>0) {
    //    $row2 = mysql_fetch_array(mysql_query("select * from users where username='$_SESSION[username]'"));
    //    echo "        <p class=\"paper_comment\"><a href=\"user-$row2[username]\">$row2[fullname]</a> ";
    //    $query = "select * from fav where id=$fid";
    //    $row3 = mysql_fetch_array(mysql_query($query));
    //    echo "添加于 $row3[create_time] ";
    //} else {
    //    $row2 = mysql_fetch_array(mysql_query("select * from users where username='$row[creater]'"));
    //    echo "        <p class=\"paper_comment\"><a href=\"user-$row2[username]\">$row2[fullname]</a> ";
    //    $query = "select * from fav where adscode='$row[adscode]' and creater='$row[creater]'";
    //    $row3 = mysql_fetch_array(mysql_query($query));
    //    echo "添加于 $row[create_time] ";
    //}

    //echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
    //echo "权限:".ShowPrivacy("$row3[privacy]");
    //echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
    //echo "阅读优先级:".ShowRead("$row3[read]");
    //if ($fid>0) {
    //    echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
    //    echo "<a href=\"edit.php?type=edit_fav&fid=$fid\">调整优先级</a>";
    //}

    echo "<p class=\"paper_comment\"><a href=\"edit.php?type=edit&id=$ref->rid\"></a></p>";
    //echo "</p>\n";
    echo "</div>\n";
    echo "<ul id=\"paper_detail\">\n";

    echo "    <li class=\"paper_detail_lst\">\n";
    echo "        <h5>作者</h5>\n";
    echo "        <p>".AuthorLink($ref->author)."</p>\n";
    echo "    </li>\n";

    echo "    <li class=\"paper_detail_lst\" id=\"paper_detail_abstract\">\n";
    echo "        <h5>摘要</h5>\n";
    $abstract = str_replace("\n","</p><p>",$ref->abstract);
    echo "        <p>$abstract</p>\n";
    echo "    </li>\n";

    if ($ref->has_erratum>0) {
        $ref2 = new ref_item($conn, $ref->has_erratum);
        echo "    <li class=\"paper_detail_lst\" id=\"paper_detail_erratum\">\n";
        echo "        <h5>堪误</h5>\n";
        echo "        <p><a href=\"ref-".$ref2->rid."\">$ref2->author_abbr, $ref2->year, $ref2->journal2, $ref2->volume, $ref2->page</a></p>\n";
        echo "    </li>\n";
    }

    if ($ref->is_erratum>0) {
        $ref2 = new ref_item($conn, $ref->is_erratum);
        echo "    <li class=\"paper_detail_lst\" id=\"paper_detail_erratum\">\n";
        echo "        <h5>堪误</h5>\n";
        echo "        <p>注意：本文是 <a href=\"ref-$ref2->rid\">$ref2->author_abbr, $ref2->year, $ref2->journal2, $ref2->volume, $ref2->page</a> 的堪误。</p>\n";
        echo "    </li>\n";
    }

    echo "    <li class=\"paper_detail_lst\">\n";
    echo "        <h5>标签</h5>\n";
    echo "        <p>".TagLink($ref->tag_lst)."</p>\n";
    echo "    </li>\n";

    if ($ref->keyword!="") {
        echo "    <li class=\"paper_detail_lst\">\n";
        echo "        <h5>关键词</h5>\n";
        echo "        <p>$ref->keyword</p>\n";
        echo "    </li>\n";
    }

    // Show Source
    echo "    <li class=\"paper_detail_lst\">\n";
    echo "        <h5>文献来源</h5>\n";
    echo "        <p>".$ref->year.", <span class=\"paper_journal\">".$ref->journal."</span>, ";
    if ($ref->volume!="") {
        echo "Volume ".$ref->volume.", ";
    }
    if ($ref->page!="") {
        echo "Page ".$ref->page;
    }
    if ($ref->page2!="") {
        echo " - ".$ref->page2;
    }
    echo "</p>\n";
    echo "    </li>\n";

    // Show Citation String
    echo "    <li class=\"paper_detail_lst\">\n";
    echo "        <h5>文献引用</h5>\n";
    echo "        <p>$ref->author_abbr, $ref->year, $ref->journal2, $ref->volume, $ref->page</p>\n";
    // reStructuredText String
    $firstname = $ref->get_first_name($ref->author_lst[0]);
    $firstname = str_replace(' ','',$firstname);
    $citemark = "[#$firstname$ref->year]";
    $ads = str_replace('&','%26',$ref->adscode);
    echo "        <p>$ref->author_abbr $ref->year $citemark"."_</p>\n";
    echo "        <p>.. $citemark $ref->author_abbr, $ref->year, *$ref->journal2*, $ref->volume, $ref->page :ads:`$ads`</p>\n";
    echo "        <p>$ref->year.$firstname.$ref->journal2.$ref->volume.$ref->page.&lt;ref-$ref->rid&gt;.pdf</p>\n";
    echo "    </li>\n";



    // Show Citation String
    echo "    <li class=\"paper_detail_lst\">\n";
    echo "        <h5>ADS Code</h5>\n";
    echo "        <p>".$ref->adscode."</p>\n";
    echo "    </li>\n";

    // Show Full Text
?>


    <li class="paper_detail_lst">
        <h5>全文链接</h5>
        <p>
            <span class="full_text_item"><a href="http://adsabs.harvard.edu/abs/<?php echo$ref->adscode?>" title="<?php echo$ref->adscode?>" target="_blank">ADS</a></span>
<?php
    if ($ref->arxiv!="") {
        $arxiv_link=str_replace("arXiv:","",$ref->arxiv);
?>
            <span class="full_text_item"><a href="http://arxiv.org/abs/<?php echo$arxiv_link?>" title="<?php echo$ref->arxiv?>" target="_blank">arXiv</a></span>
<?php
    }
    if ($ref->doi!="") {
?>
            <span class="full_text_item"><a href="http://dx.doi.org/<?php echo$ref->doi?>" title="<?php echo$ref->doi?>" target="_blank">DOI</a></span>
<?php
    }
    //echo $ref->fulltext_path.'----';
    //$fulltext_path = "data/".$ref->code."/".$ref->adscode.".pdf";
    $path = search_fulltext($ref);
    if (strlen($path)>0) {
?>
            <span class="full_text_item"><a href="<?php echo $path?>" title="pdf" target="_blank">PDF</a></span>
<?php
    }
?>
        </p>
    </li>
<?php
    // get attachment array
    $att_lst = array();
    if (file_exists($ref->data_path)) {
        $dir = dir($ref->data_path);
        while (($filename = $dir->read()) !== false) {

            if (($filename != '.') and ($filename != '..') and ($filename != "$ref->adscode.pdf")) {
                array_push($att_lst,$filename);
            }
            //array_push($att_lst,$filename);
            //$fg = explode('.',$filename);
            //if (($fg[0] == $ref->rid) and ($fg[count($fg)-1] !== 'pdf')) {
            //    array_push($att_lst,$filename);
            //}
        }
        $dir->close();
        sort($att_lst);
    }

    // display attachments.
    if (count($att_lst)>0){
        echo "    <li class=\"paper_detail_lst\">\n";
        echo "        <h5>附件</h5>\n";
        echo "        <p>\n";
        foreach ($att_lst as $att) {
            echo "<span class=\"full_text_item\">";
            echo "<a href=\"$ref->data_path/$att\"";
            echo " title=\"attchment\" target=\"_blank\">$att</a></span>\n";
        }
        echo "        </p>\n";
        echo "    </li>\n";
    }

    if (strlen($ref->bibtex)>0) {
?>

<script type="text/javascript"> 
    function copy_bibtex(){
        var content=document.getElementById("bibtex");
        content.select();
        document.execCommand("Copy");
        content.blur();
    }
</script>
<input type="button" id="bibtexbutton" onClick="copy_bibtex();" value="复制 Bibtex" />
<textarea id="bibtex" name="bibtex"><?php echo $ref->bibtex ?></textarea>

<?php

    }
    //$note_header=false;
    //if (isset($_SESSION["username"])) {
    //    $query="select * from note where adscode='$row[adscode]' and creater='$_SESSION[username]' order by update_time desc";
    //    $note_res=mysql_query($query);
    //    if (mysql_num_rows($note_res)>0) {
    //        $note_header=ShowNoteHeader($note_header);
    //        while($rownotes=mysql_fetch_array($note_res)) {
    //            ShowSingleNote($rownotes);
    //        }
    //    }
    //}
    //$query="select * from note where adscode='$row[adscode]' and privacy=0 and creater!='$_SESSION[username]' order by update_time desc";
    //$note_res=mysql_query($query);
    //if (mysql_num_rows($note_res)>0) {
    //    $note_header=ShowNoteHeader($note_header);
    //    while($rownotes=mysql_fetch_array($note_res)) {
    //        ShowSingleNote($rownotes);
    //    }
    //}
    //ShowNoteFooter($note_header);

    if (count($ref->notes)>0) {
        echo "    <li class=\"paper_detail_lst\">\n";
        echo "        <h5>笔记</h5>\n";
        echo "        <ul class=\"note_lst\">\n";
        foreach ($ref->notes as $note) {
            echo_a_note($note, $conn);
        }
        echo "        </ul><!--class=note_lst-->\n";
        echo "    </li>\n";
    }

?>

    <li class="paper_detail_lst">
        <p class="pt_fav">
<?php
    //if ($fid>0) {
    //    echo str_repeat(' ',8);
    //    echo "<a href=\"save.php?type=del_fav&fid=$fid\" ";
    //    echo "onclick=\"return confirm('确定要把这篇文献从我的文献库中移除吗?\\r\\n移除后仍然可以重新收藏')\">从我的文献库中移除</a>";
    //} else {
    //    echo str_repeat(' ',8);
    //    echo "<a href=\"edit.php?type=add_fav&id=$id\">";
    //    echo "收藏到我的文献库</a>";
    //}

    //echo "&nbsp;&nbsp;|&nbsp;&nbsp;\n";
?>
            <a href="edit.php?type=add_note&id=<?php echo $ref->rid ?>">添加笔记</a>&nbsp;&nbsp;|&nbsp;&nbsp;
            <a href="edit.php?type=edit_tag&id=<?php echo $ref->rid ?>">编辑标签</a>&nbsp;&nbsp;|&nbsp;&nbsp;
            <a href="edit.php?type=edit&id=<?php echo $ref->rid ?>">编辑文章信息</a>&nbsp;&nbsp;|&nbsp;&nbsp;
            <a href="javascript:history.back();">返回上一页</a>
        </p>
    </li>

<?php

    if (count($ref->rids)>0) {
        echo "    <li class=\"paper_detail_lst\">\n";
        echo "        <h5>相关文献</h5>\n";
        echo "        <ul id=\"related_ref_lst\">\n";
        foreach ($ref->rids as $rid2) {
            $ref2 = new ref_item($conn, $rid2);
            echo "        <li class=\"related_ref\">\n";
            echo "        <p><a href=\"ref-".$ref2->rid."\">$ref2->author_abbr, $ref2->year, $ref2->journal2, $ref2->volume, $ref2->page</a></p>\n";
            echo "        <p>$ref2->digest</p>\n";
            echo "        </li>\n";
        }
        echo "        </ul>";
        echo "    </li>\n";
    }

    echo "</ul>\n";

    //echo "<h5 id=\"who_fav_title\">谁收藏了该文献？</h5>\n";
    //echo "<div id=\"who_fav\">\n";
    //echo "    <div class=\"ye_r_t\"><div class=\"ye_l_t\"><div class=\"ye_r_b\"><div class=\"ye_l_b\">\n";
    //ShowWhoFav($row["adscode"]);
    //echo "    </div></div></div></div>\n";
    //echo "</div>\n";
    //echo "<p id=\"paper_count\"><span class=\"paper_nums\">$row[count]</span> 次阅读</p>\n";

}

?>

</div><!--div id=main_body-->

<div id="siderbar">
<?php include_once("querybar.php")?>
<?php //include_once("tagbar.php")?>
</div><!--div id=siderbar-->

</div><!--div id=cen_body-->

<?php
    include_once("footer.php");
?>
