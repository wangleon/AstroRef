<?php
session_start();
include_once("class.ref.php");
include_once("class.common.php");
$conn = connect_db("ref");

    if (isset($_POST["url"])) {
        $info=URLParser($conn,$_POST["url"]);
        $author   = $info[1];
        $affi     = htmlentities($info[2]);
        $title    = $info[3];
        $abstract = $info[4];
        $keyword  = $info[5];
        $journal  = $info[6];
        $volume   = $info[7];
        $page     = $info[8];
        $page2    = $info[9];
        $year     = $info[10];
        $date     = $info[11];
        $doi      = $info[12];
        $arxiv    = $info[13];
        $adscode  = $info[14];
        $journal2 = $info[15];
        $bibtex   = htmlentities($info[16]);
        $display = true;
        $type = "import";

    include_once("header.php");
    echo "<div id=\"wrap\">";
    echo "<table id=\"tbl_edit_ref\">";
    echo "<form method=\"post\" action=\"save.php\">";

    echo "<input type=\"hidden\" name=\"type\" value=\"$type\">\n";

//  echo "<tr><td>标题</td><td><input type=\"text\" id=\"titl\" name=\"titl\" value=\"$title\"></td></tr>\n";
    echo "<tr><td class=\"col1\">标题</td><td id=\"titl\">$title</td></tr>\n";
    echo "<input type=\"hidden\" name=\"titl\" value=\"$title\">\n";

//  echo "<tr><td>摘要</td><td><textarea id=\"abstract\" name=\"abstract\">$abstract</textarea></td></tr>\n";
    echo "<tr><td>摘要</td><td id=\"abstract\">$abstract</td></tr>\n";
    echo "<input type=\"hidden\" name=\"abstract\" value=\"$abstract\">\n";

//  echo "<tr><td>关键词</td><td><textarea id=\"keyword\" name=\"keyword\">$keyword</textarea></td></tr>\n";
    echo "<tr><td>关键词</td><td id=\"keyword\">$keyword</td></tr>\n";
    echo "<input type=\"hidden\" name=\"keyword\" value=\"$keyword\">\n";

//  echo "<tr><td>作者</td><td><textarea id=\"author\" name=\"author\">$author</textarea></td></tr>\n";
    echo "<tr><td>作者</td><td id=\"author\">$author</td></tr>\n";
    echo "<input type=\"hidden\" name=\"author\" value=\"$author\">\n";

//  echo "<tr><td>作者信息</td><td><textarea id=\"affi\" name=\"affi\">$affi</textarea></td></tr>\n";
//  echo "<tr><td>作者信息</td><td id=\"affi\">$affi</td></tr>\n";
    echo "<input type=\"hidden\" name=\"affi\" value=\"$affi\">\n";

//  echo "<tr><td>年份</td><td><input type=\"text\" id=\"year\" name=\"year\" value=\"$year\"></td></tr>\n";
    echo "<tr><td>年份</td><td id=\"year\">$year</td></tr>\n";
    echo "<input type=\"hidden\" name=\"year\" value=\"$year\">\n";

//  echo "<tr><td>期刊</td><td><input type=\"text\" id=\"journal\" name=\"journal\" value=\"$journal\"></td></tr>\n";
    echo "<tr><td>期刊</td><td id=\"journal\">$journal</td></tr>\n";
    echo "<input type=\"hidden\" name=\"journal\" value=\"$journal\">\n";

//  echo "<tr><td>期刊缩写</td><td><input type=\"text\" id=\"journal2\" name=\"journal2\" value=\"$journal2\"></td></tr>\n";
    echo "<tr><td>期刊缩写</td><td id=\"journal2\">$journal2</td></tr>\n";
    echo "<input type=\"hidden\" name=\"journal2\" value=\"$journal2\">\n";

//  echo "<tr><td>卷号</td><td><input type=\"text\" id=\"volume\" name=\"volume\" value=\"$volume\"></td></tr>\n";
    echo "<tr><td>卷号</td><td id=\"volume\">$volume</td></tr>\n";
    echo "<input type=\"hidden\" name=\"volume\" value=\"$volume\">\n";

//  echo "<tr><td>起始页</td><td><input type=\"text\" id=\"page\" name=\"page\" value=\"$page\"></td></tr>\n";
    echo "<tr><td>起始页</td><td id=\"page\">$page</td></tr>\n";
    echo "<input type=\"hidden\" name=\"page\" value=\"$page\">\n";

//  echo "<tr><td>终止页</td><td><input type=\"text\" id=\"page2\" name=\"page2\" value=\"$page2\"></td></tr>\n";
    echo "<tr><td>终止页</td><td id=\"page2\">$page2</td></tr>\n";
    echo "<input type=\"hidden\" name=\"page2\" value=\"$page2\">\n";

//  echo "<tr><td>发表日期</td><td><input type=\"text\" id=\"date\" name=\"date\" value=\"$date\"></td></tr>\n";
    echo "<tr><td>发表日期</td><td id=\"date\">$date</td></tr>\n";
    echo "<input type=\"hidden\" name=\"date\" value=\"$date\">\n";

//  echo "<tr><td>DOI</td><td><input type=\"text\" id=\"doi\" name=\"doi\" value=\"$doi\"></td></tr>\n";
    echo "<tr><td>DOI</td><td id=\"doi\">$doi</td></tr>\n";
    echo "<input type=\"hidden\" name=\"doi\" value=\"$doi\">\n";

//  echo "<tr><td>arXiv</td><td><input type=\"text\" id=\"arxiv\" name=\"arxiv\" value=\"$arxiv\"></td></tr>\n";
    echo "<tr><td>arXiv</td><td id=\"arxiv\">$arxiv</td></tr>\n";
    echo "<input type=\"hidden\" name=\"arxiv\" value=\"$arxiv\">\n";

//  echo "<tr><td>ADS Code</td><td><input type=\"text\" id=\"adscode\" name=\"adscode\" value=\"$adscode\"></td></tr>\n";
    echo "<tr><td>ADS Code</td><td id=\"adscode\">$adscode</td></tr>\n";
    echo "<input type=\"hidden\" name=\"adscode\" value=\"$adscode\">\n";

    echo "<tr><td>Bibtex</td><td id=\"bibtex\">$bibtex</td></tr>\n";
    echo "<input type=\"hidden\" name=\"bibtex\" value=\"$bibtex\">\n";

    //echo "<tr><td>标签</td><td><input type=\"text\" name=\"tag\" id=\"tag\"/>&nbsp;&nbsp;多个标签用\",\"分开</td></tr>\n";

    //echo "<tr><td>优先级</td><td>\n";
    //echo "<select name=\"read\" size=\"1\" id=\"read\">\n";
    //echo "    <option value=\"5\">置顶阅读</option>\n";
    //echo "    <option value=\"4\">特别希望阅读</option>\n";
    //echo "    <option value=\"3\" selected>将阅读</option>\n";
    //echo "    <option value=\"2\">可能阅读</option>\n";
    //echo "    <option value=\"1\">不是很想阅读</option>\n";
    //echo "    <option value=\"0\">已经阅读</option>\n";
    //echo "</select>\n</td></tr>\n";
    //echo "<tr><td>隐私</td><td>\n";
    //echo "<select name=\"privacy\" size=\"1\" id=\"privacy\">\n";
    //echo "    <option value=\"0\">公开</option>\n";
    //echo "    <!--<option value=\"1\">仅好友可见</option>-->\n";
    //echo "    <option value=\"2\">仅自己可见</option>\n";
    //echo "</select>\n</td></tr>\n";
    //echo "<tr><td>读书笔记</td><td><textarea name=\"note\" id=\"note\"></textarea></td></tr>\n";
    //echo "<tr><td></td><td><input type=\"checkbox\" name=\"note_privacy\" value=\"1\">仅自己可见</td></tr>";
    echo "<tr><td colspan=\"2\"><input type=\"submit\" id=\"tbl_submit\" value=\"保存\" ></td></tr>\n";
    echo "</table>";
    echo "</div><!--div id=wrap-->";
    include_once("footer.php");

    } elseif (($_GET["type"]=="add_fav")) {
        $sql = "select * from paper where id=$_GET[id]";
        $row = $conn->query($sql)->fetch(PDO::FETCH_ASSOC);
        $author   = $row["author"];
        $affi     = $row["affi"];
        $title    = $row["title"];
        $abstract = $row["abstract"];
        $keyword  = $row["keyword"];
        $journal  = $row["journal"];
        $volume   = $row["volume"];
        $page     = $row["page"];
        $page2    = $row["page2"];
        $year     = $row["year"];
        $date     = $row["date"];
        $doi      = $row["doi"];
        $arxiv    = $row["arxiv"];
        $adscode  = $row["adscode"];
        $journal2 = $row["journal"];
        if (CheckFav($adscode)==0) {
            $type = "add_fav";
        } else {
            $_SESSION["err_msg"]="您已经收藏了该文献";
            header("Location:$_GET[id].html");
        }
        include_once("header.php");
        echo "<div id=\"wrap\">";
        echo "<table id=\"tbl_edit_ref\">";
        echo "<form method=\"post\" action=\"save.php\">";
        echo "<input type=\"hidden\" name=\"type\" value=\"$type\">\n";
        echo "<tr><td class=\"col1\">标题</td><td id=\"titl\">$title</td></tr>\n";
        echo "<tr><td>摘要</td><td id=\"abstract\">$abstract</td></tr>\n";
        echo "<tr><td>关键词</td><td id=\"keyword\">$keyword</td></tr>\n";
        echo "<tr><td>作者</td><td id=\"author\">$author</td></tr>\n";
        echo "<tr><td>年份</td><td id=\"year\">$year</td></tr>\n";
        echo "<tr><td>期刊</td><td id=\"journal\">$journal</td></tr>\n";
        echo "<tr><td>卷号</td><td id=\"volume\">$volume</td></tr>\n";
        echo "<tr><td>起始页</td><td id=\"page\">$page</td></tr>\n";
        echo "<tr><td>终止页</td><td id=\"page2\">$page2</td></tr>\n";
        echo "<tr><td>发表日期</td><td id=\"date\">$date</td></tr>\n";
        echo "<tr><td>ADS Code</td><td id=\"adscode\">$adscode</td></tr>\n";
        echo "<input type=\"hidden\" name=\"adscode\" value=\"$adscode\">\n";
        echo "<tr><td>优先级</td><td>\n";
        echo "<select name=\"read\" size=\"1\" id=\"read\">\n";
        echo "    <option value=\"5\">置顶阅读</option>\n";
        echo "    <option value=\"4\">特别希望阅读</option>\n";
        echo "    <option value=\"3\" selected>将阅读</option>\n";
        echo "    <option value=\"2\">可能阅读</option>\n";
        echo "    <option value=\"1\">不是很想阅读</option>\n";
        echo "    <option value=\"0\">已经阅读</option>\n";
        echo "</select>\n</td></tr>\n";
        echo "<tr><td>隐私</td><td>\n";
        echo "<select name=\"privacy\" size=\"1\" id=\"privacy\">\n";
        echo "    <option value=\"0\">公开</option>\n";
        echo "    <!--<option value=\"1\">仅好友可见</option>-->\n";
        echo "    <option value=\"2\">仅自己可见</option>\n";
        echo "</select>\n</td></tr>\n";
        echo "<tr><td>读书笔记</td><td><textarea name=\"note\" id=\"note\"></textarea></td></tr>\n";
        echo "<tr><td></td><td><input type=\"checkbox\" name=\"note_privacy\" value=\"1\">仅自己可见</td></tr>";
        echo "<tr><td colspan=\"2\"><input type=\"submit\" id=\"tbl_submit\" value=\"保存\" ></td></tr>\n";
        echo "</table>";
        echo "</div><!--div id=wrap-->";
        include_once("footer.php");

    } elseif (($_GET["type"]=="edit_fav")) {
        $query="select * from fav where id=$_GET[fid]";
        $row2=mysql_fetch_array(mysql_query($query));

        if ($row2["creater"]==$_SESSION["username"]) {
            $query="select * from paper where adscode='$row2[adscode]'";
            $row=mysql_fetch_array(mysql_query($query));
            $author   = $row["author"];
            $affi     = $row["affi"];
            $title    = $row["title"];
            $abstract = $row["abstract"];
            $keyword  = $row["keyword"];
            $journal  = $row["journal"];
            $volume   = $row["volume"];
            $page     = $row["page"];
            $page2    = $row["page2"];
            $year     = $row["year"];
            $date     = $row["date"];
            $doi      = $row["doi"];
            $arxiv    = $row["arxiv"];
            $adscode  = $row["adscode"];
            $journal2 = $row["journal"];
            $type = "edit_fav";
            include_once("header.php");
            echo "<div id=\"wrap\">";
            echo "<table id=\"tbl_edit_ref\">";
            echo "<form method=\"post\" action=\"save.php\">";
            echo "<input type=\"hidden\" name=\"type\" value=\"$type\">\n";
            echo "<tr><td class=\"col1\">标题</td><td id=\"titl\">$title</td></tr>\n";
            echo "<tr><td>摘要</td><td id=\"abstract\">$abstract</td></tr>\n";
            echo "<tr><td>关键词</td><td id=\"keyword\">$keyword</td></tr>\n";
            echo "<tr><td>作者</td><td id=\"author\">$author</td></tr>\n";
            echo "<tr><td>年份</td><td id=\"year\">$year</td></tr>\n";
            echo "<tr><td>期刊</td><td id=\"journal\">$journal</td></tr>\n";
            echo "<tr><td>卷号</td><td id=\"volume\">$volume</td></tr>\n";
            echo "<tr><td>起始页</td><td id=\"page\">$page</td></tr>\n";
            echo "<tr><td>终止页</td><td id=\"page2\">$page2</td></tr>\n";
            echo "<tr><td>发表日期</td><td id=\"date\">$date</td></tr>\n";
            echo "<tr><td>ADS Code</td><td id=\"adscode\">$adscode</td></tr>\n";
            echo "<input type=\"hidden\" name=\"adscode\" value=\"$adscode\">\n";
            echo "<tr><td>优先级</td><td>\n";
            echo "<select name=\"read\" size=\"1\" id=\"read\">\n";
            echo "    <option value=\"5\">置顶阅读</option>\n";
            echo "    <option value=\"4\">特别希望阅读</option>\n";
            echo "    <option value=\"3\" selected>将阅读</option>\n";
            echo "    <option value=\"2\">可能阅读</option>\n";
            echo "    <option value=\"1\">不是很想阅读</option>\n";
            echo "    <option value=\"0\">已经阅读</option>\n";
            echo "</select>\n</td></tr>\n";
            echo "<tr><td>隐私</td><td>\n";
            echo "<select name=\"privacy\" size=\"1\" id=\"privacy\">\n";
            echo "    <option value=\"0\">公开</option>\n";
            echo "    <!--<option value=\"1\">仅好友可见</option>-->\n";
            echo "    <option value=\"2\">仅自己可见</option>\n";
            echo "</select>\n</td></tr>\n";
            echo "<input type=\"hidden\" name=\"fid\" value=\"$_GET[fid]\">\n";
            echo "<tr><td colspan=\"2\"><input type=\"submit\" id=\"tbl_submit\" value=\"保存\" ></td></tr>\n";
            echo "</table>";
            echo "</div><!--div id=wrap-->";
            include_once("footer.php");
        }

    } elseif (($_GET["type"]=="add_note")) {
        $type = "add_note";
        $rid = (int)$_GET[id];
        $ref = new ref_info($conn,$rid);
        include_once("header.php");
        echo "<div id=\"wrap\">
              <table id=\"tbl_edit_ref\">
              <form method=\"post\" action=\"save.php\">
              <input type=\"hidden\" name=\"type\" value=\"$type\">
              <tr><td class=\"col1\">标题</td><td id=\"titl\">$ref->title</td></tr>
              <tr><td>文献引用</td>
                  <td>$ref->author_abbr, $ref->year, $ref->journal2, $ref->volume, $ref->page.
                  <a href=\"http://adswww.harvard.edu/abs/$ref->adscode\">$ref->adscode</a></td>
              </tr>
              <input type=\"hidden\" name=\"rid\" value=\"$ref->rid\" />
              <tr><td>读书笔记</td><td><textarea name=\"note\" id=\"note\"></textarea></td></tr>
              <tr><td colspan=\"2\"><input type=\"submit\" id=\"tbl_submit\" value=\"保存\" ></td></tr>
              </table>";
        include_once("footer.php");

    } elseif (($_GET["type"]=="edit_note")) {
        $type = "edit_note";
        $nid = $_GET['nid'];
        $note = new ref_note($conn,$nid);
        $ref  = new ref_info($conn,$note->rid);
        include_once("header.php");
        echo "<div id=\"wrap\">
              <table id=\"tbl_edit_ref\">
              <form method=\"post\" action=\"save.php\">
              <input type=\"hidden\" name=\"type\" value=\"$type\">
              <tr><td class=\"col1\">标题</td><td id=\"titl\">$ref->title</td></tr>
              <tr><td>文献引用</td>
                  <td>$ref->author_abbr, $ref->year, $ref->journal2, $ref->volume, $ref->page.
                  <a href=\"http://adswww.harvard.edu/abs/$ref->adscode\">$ref->adscode</a></td>
              </tr>
              <input type=\"hidden\" name=\"nid\" value=\"$nid\">
              <tr><td>读书笔记</td><td>";
        echo "<textarea name=\"note\" id=\"note\">".get_text_foredit($note->text)."</textarea>";
        echo "
              </td></tr>
              <tr><td colspan=\"2\"><input type=\"submit\" id=\"tbl_submit\" value=\"保存\" ></td></tr>
              </table>";
        include_once("footer.php");

    } elseif (($_GET["type"]=="edit_tag")) {

        $type = "edit_tag";
        $rid = (int)$_GET[id];
        $ref = new ref_info($conn,$rid);
        $script = array('js/tag_suggest.js');
        include_once("header.php");
        $tag = implode(", ",$ref->tag_lst);
        echo "<div id=\"wrap\">
              <table id=\"tbl_edit_ref\">
              <form method=\"post\" action=\"save.php\">
              <input type=\"hidden\" name=\"type\" value=\"$type\">
              <tr><td class=\"col1\">标题</td><td id=\"titl\">$ref->title</td></tr>
              <tr><td>文献引用</td>
                  <td>$ref->author_abbr, $ref->year, $ref->journal2, $ref->volume, $ref->page.
                  <a href=\"http://adswww.harvard.edu/abs/$ref->adscode\">$ref->adscode</a></td>
              </tr>
              <input type=\"hidden\" name=\"rid\" value=\"$ref->rid\" />
              <tr><td>标签</td>
                  <td>
                  <input type=\"text\" name=\"tag\" id=\"tag\" value=\"$tag\" />
                  &nbsp;&nbsp;多个标签用\",\"分开
                  <td>
              </tr>
              <tr><td colspan=\"2\">
              <input type=\"submit\" id=\"tbl_submit\" value=\"保存\" >
              </td></tr>
              </table>";
        include_once("footer.php");

    } elseif (($_GET["type"]=="edit")) {

        $sql = "select * from paper where id=$_GET[id]";
        $row = $conn->query($sql)->fetch(PDO::FETCH_ASSOC);

        $sql  = "select * from detail where id=$_GET[id]";
        $row2 = $conn->query($sql)->fetch(PDO::FETCH_ASSOC);

        $sql  = "select * from relation where id=$_GET[id]";
        $row3 = $conn->query($sql)->fetch(PDO::FETCH_ASSOC);

        $author   = $row["author"];
        $affi     = $row2["affiliation"];
        $title    = $row2["title"];
        $abstract = $row2["abstract"];
        $keyword  = $row2["keyword"];
        $bibtex   = $row2["bibtex"];
        $journal  = $row["journal"];
        $journal2 = $row["journal2"];
        $volume   = $row["volume"];
        $page     = $row["page"];
        $page2    = $row["page2"];
        $year     = $row["year"];
        $date     = $row["date"];
        $doi      = $row["doi"];
        $arxiv    = $row["arxiv"];
        $adscode  = $row["adscode"];
        $is_erratum = $row['is_erratum'];
        $has_erratum = $row['has_erratum'];
        //$tag      = TagInverseParser($row["tag"]);
        $related_rids = $row3['related_rids'];
        $type = "edit";
        include_once("header.php");
        echo "<div id=\"wrap\">";
        echo "<table id=\"tbl_edit_ref\">";
        echo "<form method=\"post\" action=\"save.php\">";
        echo "<input type=\"hidden\" name=\"type\" value=\"$type\">\n";
        echo "<input type=\"hidden\" name=\"pid\" value=\"$_GET[id]\">\n";
        echo "<tr><td class=\"col1\">标题</td><td id=\"titl\"><textarea name=\"title\">$title</textarea></td></tr>\n";
        echo "<tr><td>摘要</td><td id=\"abstract\"><textarea name=\"abstract\">$abstract</textarea></td></tr>\n";
        echo "<tr><td>关键词</td><td id=\"keyword\"><input type=\"text\" name=\"keyword\" value=\"$keyword\" /></td></tr>\n";
        echo "<tr><td>作者</td><td id=\"author\"><input type=\"text\" name=\"author\" value=\"$author\" /></td></tr>\n";
        echo "<tr><td>作者信息</td><td id=\"affi\"><textarea name=\"affi\">$affi</textarea></td></tr>\n";
        echo "<tr><td>年份</td><td id=\"year\"><input type=\"text\" name=\"year\" value=\"$year\" /></td></tr>\n";
        echo "<tr><td>期刊</td><td id=\"journal\"><input type=\"text\" name=\"journal\" value=\"$journal\" /></td></tr>\n";
        echo "<tr><td>期刊缩写</td><td id=\"journal2\"><input type=\"text\" name=\"journal2\" value=\"$journal2\" /></td></tr>\n";
        echo "<tr><td>卷号</td><td id=\"volume\"><input type=\"text\" name=\"volume\" value=\"$volume\" /></td></tr>\n";
        echo "<tr><td>起始页</td><td id=\"page\"><input type=\"text\" name=\"page\" value=\"$page\" /></td></tr>\n";
        echo "<tr><td>终止页</td><td id=\"page2\"><input type=\"text\" name=\"page2\" value=\"$page2\" /></td></tr>\n";
        echo "<tr><td>发表日期</td><td id=\"date\"><input type=\"text\" name=\"date\" value=\"$date\" /></td></tr>\n";
        echo "<tr><td>ADS Code</td><td id=\"adscode\"><input type=\"text\" name=\"adscode\" value=\"$adscode\" /></td></tr>\n";
        echo "<input type=\"hidden\" name=\"oldadscode\" value=\"$adscode\" />\n";
        echo "<tr><td>DOI</td><td id=\"doi\"><input type=\"text\" name=\"doi\" value=\"$doi\" /></td></tr>\n";
        echo "<tr><td>arXiv</td><td id=\"arxiv\"><input type=\"text\" name=\"arxiv\" value=\"$arxiv\" /></td></tr>\n";
        echo "<tr><td>is Erratum of</td><td id=\"is_erratum\"><input type=\"text\" name=\"is_erratum\" value=\"$is_erratum\" /></td></tr>\n";
        echo "<tr><td>has Erratum:</td><td id=\"has_erratum\"><input type=\"text\" name=\"has_erratum\" value=\"$has_erratum\" /></td></tr>\n";
        echo "<tr><td>相关文献</td><td id=\"relation\"><input type=\"text\" name=\"relation\" value=\"$related_rids\" /></td></tr>\n";
        //echo "<tr><td>标签</td><td><input type=\"text\" name=\"tag\" id=\"tag\" value=\"$tag\"/>&nbsp;&nbsp;多个标签用\",\"分开</td></tr>\n";
        echo "<tr><td>Bibtex</td><td id=\"bibtex\"><textarea name=\"bibtex\">$bibtex</textarea></td></tr>\n";
        echo "<tr><td colspan=\"2\"><input type=\"submit\" id=\"tbl_submit\" value=\"保存\" ></td></tr>\n";
        echo "</form></table>";
        echo "</div><!--div id=wrap-->";
        include_once("footer.php");

    }



?>
