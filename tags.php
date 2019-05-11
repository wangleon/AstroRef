<?php
session_start();
include_once("class.ref.php");
include_once("header.php");

?>

<div id="wrap">
<div id="cen_body">


<div id="tags">
<ul>
<?php
$sql = "select tag from ref.tags order by tag";
$res = $conn->query($sql)->fetchAll();
foreach ($res as $row) {
    $tag = $row[0];
    $link=TagReplace($tag);
    echo "<li><a href=\"tag-$link\">$tag</a></li>\n";
}

?>
</ul>

</div><!--div id=tags-->



</div><!--div id=cen_body-->

<?php
    include_once("footer.php");
?>
