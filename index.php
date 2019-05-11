<?php
session_start();
include_once("header.php");
?>
    <div id="content">
    <div id="wrap">
<?php
    // in each case, both $sql1 (for counting the number of results) and
    // $sql2 (for displaying the results) are generated.
    if ($_POST['type']=='search') {
        if ($_POST['range']=='author') {
            $author = $_POST['query'];
            $sql1 = "select count(id) from paper where author like '|$author%'";
            $sql2 = "select id from paper where author like '|$author%'
                     order by adscode";
        }

        echo "<h2>搜索结果</h2>\n";
        $items_per_page = 0;
    } elseif (isset($_GET['tag'])) {
        $tag = TagInverseReplace($_GET['tag']);
        $sql1 = "select count(ref.tag.id) from ref.tag,ref.paper
                 where ref.tag.id=ref.paper.id and ref.tag.tag like '%|$tag|%'";
        $sql2 = "select ref.tag.id from ref.tag,ref.paper
                 where ref.tag.id=ref.paper.id and ref.tag.tag like '%|$tag|%'
                 order by ref.paper.year";
        $items_per_page = 0;
        echo "<h2>标签：$tag</h2>";
    } else {
        $sql1 = "select count(id) from paper";
        $sql2 = "select id from paper order by create_time desc";
        echo "<h2>最新收录的文献</h2>\n";
        $items_per_page = 10;
    }

    echo "<div id=\"cen_body\">\n";
    echo "<div id=\"main_body\">\n";

    $count = $conn->query($sql1)->fetch()[0];
    if ($items_per_page > 0) {
        if (isset($_GET["page"])) {
            $start=($_GET["page"]-1)*$items_per_page;
            $current_page=$_GET["page"];
        } else {
            $start=0;
            $current_page=1;
        }

        $sql2 .= " limit $start,$items_per_page";
    }

    foreach ($conn->query($sql2)->fetchAll(PDO::FETCH_ASSOC) as $row) {
        show_item($conn, $row['id']);
    }
    // show page navigation bar
    if ($items_per_page > 0) {
        show_page_nav($count,$current_page,$items_per_page,$_SERVER["REQUEST_URI"]);
    }

?>
</div><!--div id=main_body-->

<div class="bar" id="siderbar">
<?php //include_once("querybar.php")?>
<?php //include_once("tagbar.php")?>
</div><!--div id=siderbar-->

</div><!--div id=cen_body-->


<?php
//include_once("footer.php");
?>
