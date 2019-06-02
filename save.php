<?php
session_start();
include_once("class.ref.php");
include_once("class.common.php");
$conn = connect_db("ref");


    if ($_POST["type"]=="import") {

        $pid = check_adscode($conn,$_POST["adscode"]);
        if ($pid>0) {
            // adscode exists
            $pid=$pid;
        } else {
            // new adscode
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $sql = "insert into paper (`adscode`) values ('$_POST[adscode]')";
            $conn->exec($sql);
            $pid = check_adscode($conn, $_POST["adscode"]);

            $title       = addslashes($_POST['titl']);
            $abstract    = addslashes($_POST['abstract']);
            $keyword     = addslashes($_POST['keyword']);
            $affiliation = addslashes($_POST['affi']);
            $bibtex      = addslashes($_POST['bibtex']);
            //$author      = AuthorParser($_POST['author']);
            $author      = $_POST['author'];

            $year        = $_POST['year'];
            $journal2    = $_POST['journal2'];
            $volume      = $_POST['volume'];
            $page        = $_POST['page'];
            $abbr        = get_paper_abbr($author,$year,$journal2,$volume,$page);

            $conn->exec("insert into detail (`id`) values ($pid)");

            $conn->exec("update detail set `title`       ='$title'       where id=$pid");
            $conn->exec("update detail set `abstract`    ='$abstract'    where id=$pid");
            $conn->exec("update detail set `keyword`     ='$keyword'     where id=$pid");
            $conn->exec("update detail set `affiliation` ='$affiliation' where id=$pid");
            $conn->exec("update detail set `bibtex`      ='$bibtex'      where id=$pid");
            $conn->exec("update paper set `author`      ='$author'             where id=$pid");
            $conn->exec("update paper set `year`        = $year                where id=$pid");
            $conn->exec("update paper set `journal`     ='$_POST[journal]'     where id=$pid");
            $conn->exec("update paper set `journal2`    ='$journal2'           where id=$pid");
            $conn->exec("update paper set `volume`      = $volume              where id=$pid");
            $conn->exec("update paper set `page`        ='$page'               where id=$pid");
            $conn->exec("update paper set `page2`       ='$_POST[page2]'       where id=$pid");
            $conn->exec("update paper set `date`        ='$_POST[date]'        where id=$pid");
            $conn->exec("update paper set `doi`         ='$_POST[doi]'         where id=$pid");
            $conn->exec("update paper set `arxiv`       ='$_POST[arxiv]'       where id=$pid");
            $conn->exec("update paper set `abbr`        ='$abbr'               where id=$pid");
          //$conn->exec("update paper set `creater`     ='$_SESSION[username]' where id=$pid");
          //$conn->exec("update paper set `privacy`     = $_POST[privacy]      where id=$pid");
            $conn->exec("update paper set `create_time` = CURRENT_TIMESTAMP    where id=$pid");
          //$conn->exec("update paper set `count`       = 0                    where id=$pid");
          //$tag=TagParser($_POST["tag"]);
          //mysql_$conn->exec("update paper set `tag`   = '$tag'               where id=$pid");
          //UpdateTagCount();
        }

    } elseif ($_POST["type"]=="add_fav") {

        $pid=check_adscode($_POST["adscode"]);

        $fid=CheckFav($_POST["adscode"]);
        if ($fid>0) {
            mysql_query("update fav set `read`        = $_POST[read]         where id=$fid");
            mysql_query("update fav set `privacy`     = $_POST[privacy]      where id=$fid");
            mysql_query("update fav set `update_time` = CURRENT_TIMESTAMP    where id=$fid");
        } else {
            mysql_query("insert into fav (`adscode`,`creater`) values ('$_POST[adscode]','$_SESSION[username]')");
            $fid=CheckFav($_POST["adscode"]);
            mysql_query("update fav set `arxiv`       ='$_POST[arxiv]'       where id=$fid");
            mysql_query("update fav set `read`        = $_POST[read]         where id=$fid");
            mysql_query("update fav set `privacy`     = $_POST[privacy]      where id=$fid");
            mysql_query("update fav set `create_time` = CURRENT_TIMESTAMP    where id=$fid");
            mysql_query("update fav set `update_time` = CURRENT_TIMESTAMP    where id=$fid");
        }

        if (trim($_POST["note"])!="") {
            mysql_query("insert into note (`adscode`) values ('$_POST[adscode]')");
            $row=mysql_fetch_row(mysql_query("select max(id) from note where adscode='$_POST[adscode]'"));
            mysql_query("update note set `creater`     ='$_SESSION[username]' where id=$row[0]");
            mysql_query("update note set `arxiv`       ='$_POST[arxiv]'       where id=$row[0]");
            mysql_query("update note set `note`        ='$_POST[note]'        where id=$row[0]");
            mysql_query("update note set `privacy`     = $_POST[note_privacy] where id=$row[0]");
            mysql_query("update note set `create_time` = CURRENT_TIMESTAMP    where id=$row[0]");
            mysql_query("update note set `update_time` = CURRENT_TIMESTAMP    where id=$row[0]");
        }

    } elseif ($_POST["type"]=="edit_fav") {

        $pid = check_adscode($_POST["adscode"]);
        $row=mysql_fetch_array(mysql_query("select * from fav where id=$_POST[fid]"));
        if ($row["creater"]==$_SESSION["username"]) {
            mysql_query("update fav set `read`        = $_POST[read]         where id=$_POST[fid]");
            mysql_query("update fav set `privacy`     = $_POST[privacy]      where id=$_POST[fid]");
            mysql_query("update fav set `update_time` = CURRENT_TIMESTAMP    where id=$_POST[fid]");
        }

    } elseif ($_GET["type"]=="del_fav") {

        $row=mysql_fetch_array(mysql_query("select * from fav where id=$_GET[fid]"));
        $pid=check_adscode($row["adscode"]);
        if ($row["creater"]==$_SESSION["username"]) {
            mysql_query("delete from fav where id=$_GET[fid]");
        }
    } elseif ($_POST["type"]=="edit_note") {
        // edit a note
        $nid = $_POST["nid"];
        $note = new ref_note($conn,$nid);
        $text = str_replace("\r","",$_POST['note']);
        $text = addslashes($text);
        $sql  = "update note set `note` = '$text',`update_time` = CURRENT_TIMESTAMP  where id=$nid";
        $conn->exec($sql);
        $pid = $note->rid;

    } elseif ($_POST["type"]=="add_note") {
        // add a note
        if (trim($_POST["note"])!="") {
            $rid = $_POST["rid"];
            $text = str_replace("\r","",$_POST['note']);
            $text = addslashes($text);
            $sql = "insert into note (rid,note,create_time,update_time)
                    values ($rid, '$text', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)";
            $conn->exec($sql);
        }
        $pid = $rid;

    } elseif ($_GET["type"]=="del_note") {

        $sql = "select * from note where id=$_GET[nid]";
        $row = $conn->query($sql)->fetch(PDO::FETCH_ASSOC);
        $pid=CheckADS($row["adscode"]);
        if ($row["creater"]==$_SESSION["username"]) {
            $sql = "delete from note where id=$_GET[nid]";
            $conn->exec($sql);
        }

    } elseif ($_POST["type"]=="edit_tag") {

        $tag_text = TagParse($_POST["tag"]);

        $rid = $_POST["rid"];
        $sql = "select count(id) from ref.tag where id=$rid";
        $num = $conn->query($sql)->fetch()[0];
        if ($num == 0) {
            $sql2 = "insert into ref.tag (id,tag) values ($rid,'$tag_text')";
        } else {
            $sql2 = "update ref.tag set tag = '$tag_text' where id=$rid";
        }
        $conn->exec($sql2);
        //UpdateTagCount();
        refresh_tags($conn,$rid);
        $pid = $rid;
    } elseif ($_POST["type"]=="edit") {
        $pid = $_POST["pid"];
        $sql = "update paper set `adscode` = '$_POST[adscode]' where `id`=$pid";
        $conn->exec($sql);

            $title       = addslashes($_POST['title']);
            $abstract    = addslashes($_POST['abstract']);
            $keyword     = addslashes($_POST['keyword']);
            $affiliation = addslashes($_POST['affi']);
            $bibtex      = addslashes($_POST['bibtex']);
            //$author      = AuthorParser($_POST['author']);
            $author      = $_POST['author'];

            $year        = $_POST['year'];
            $journal2    = $_POST['journal2'];
            $volume      = $_POST['volume'];
            $page        = $_POST['page'];
            $abbr        = get_paper_abbr($author,$year,$journal2,$volume,$page);
            $is_erratum  = $_POST['is_erratum'];
            $has_erratum = $_POST['has_erratum'];
            $relation    = $_POST['relation'];

            //$pid=CheckADS($_POST["adscode"]);
            $conn->exec("update detail set `title`       ='$title'              where id=$pid");
            $conn->exec("update detail set `abstract`    ='$abstract'           where id=$pid");
            $conn->exec("update detail set `keyword`     ='$keyword'            where id=$pid");
            $conn->exec("update detail set `affiliation` ='$affiliation'        where id=$pid");
            $conn->exec("update detail set `bibtex`      ='$bibtex'             where id=$pid");
            $conn->exec("update paper set `author`       ='$author'             where id=$pid");
            $conn->exec("update paper set `year`         = $year                where id=$pid");
            $conn->exec("update paper set `journal`      ='$_POST[journal]'     where id=$pid");
            $conn->exec("update paper set `journal2`     ='$journal2'           where id=$pid");
            $conn->exec("update paper set `volume`       = $volume              where id=$pid");
            $conn->exec("update paper set `page`         ='$page'               where id=$pid");
            $conn->exec("update paper set `page2`        ='$_POST[page2]'       where id=$pid");
            $conn->exec("update paper set `date`         ='$_POST[date]'        where id=$pid");
            $conn->exec("update paper set `doi`          ='$_POST[doi]'         where id=$pid");
            $conn->exec("update paper set `arxiv`        ='$_POST[arxiv]'       where id=$pid");
            $conn->exec("update paper set `abbr`         ='$abbr'               where id=$pid");
            $conn->exec("update paper set `is_erratum`   ='$is_erratum'         where id=$pid");
            $conn->exec("update paper set `has_erratum`  ='$has_erratum'        where id=$pid");
            if ($is_erratum>0) {
                $conn->exec("update paper set `has_erratum`  ='$pid'   where id=$is_erratum");
            }
            if ($has_erratum>0) {
                $conn->exec("update paper set `is_erratum`   ='$pid'   where id=$has_erratum");
            }

            $sql = "select count(*) from relation where id=$pid";
            $row = $conn->query($sql)->fetch();
            $count = $row[0];
            if ($count == 0) {
                $conn->exec("insert into relation (id,related_rids) values ($pid,'')");
            }
            $conn->exec("update relation set `related_rids`   ='$relation'   where id=$pid");


            //$tag=TagParser($_POST["tag"]);
            //mysql_query("update paper set `tag`         = '$tag'               where id=$pid");
            //UpdateTagCount();

            //mysql_query("update note set `arxiv`       ='$_POST[arxiv]'       where `adscode`='$_POST[adscode]'");
            //mysql_query("update note set `update_time` = CURRENT_TIMESTAMP    where `adscode`='$_POST[adscode]'");

            //mysql_query("update fav  set `arxiv`       ='$_POST[arxiv]'       where id=$fid");
            //mysql_query("update fav  set `update_time` = CURRENT_TIMESTAMP    where id=$fid");

    }


    header("Location:ref-$pid");

?>
