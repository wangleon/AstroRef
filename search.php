<?php
include_once("class.ref.php");
include_once("../share/class.common.php");
$conn = connect_db("ref");

$abbr = trim($_GET["search_find"]);
$sql = "select id from ref.paper where abbr like \"$abbr\" limit 1";
$row = $conn->query($sql)->fetch();
header("Location:ref-$row[0]");

?>
