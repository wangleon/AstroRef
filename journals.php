<?php
session_start();
include_once("sysconf.inc");
include_once("connect.php");
include_once("header.php");

?>

<div id="wrap">
<div id="cen_body">

<div id="journals">
<ul>
<?php
$query="select * from paper order by adscode";
$res=mysql_query($query);
while ($row=mysql_fetch_array($res)) {
    echo "<li><a href=\"ref-$row[id]\">link</a>&nbsp;&nbsp;&nbsp;&nbsp;$row[adscode], ".AuthorAbbr($row["author"],1).", $row[year], $row[journal2], $row[volume], $row[page]. &lt;ref-$row[id]&gt;</li>\n";

}


?>
</ul>

</div><!--div id=journals-->

</div><!--div id=cen_body-->

<?php
    include_once("footer.php");
?>
