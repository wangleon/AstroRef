<?php
session_start();
include_once("class.common.php");
$conn = connect_db("ref");

$text=$_GET["term"];
$result = array();
$sql = "select tag from ref.tags where tag like '%$text%' order by tag limit 10";
$res = $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
foreach ($res as $row) {
    array_push($result,$row['tag']);
}
echo json_encode($result);

?>
