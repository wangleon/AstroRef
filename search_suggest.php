<?php
include_once("class.ref.php");
include_once("class.common.php");
$conn = connect_db("ref");

$text=$_GET["term"];
$result = array();
$sql = 'select abbr from ref.paper where abbr like "'.$text.'%" order by year desc limit 10';
$res = $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
foreach ($res as $row) {
    array_push($result,$row['abbr']);
}
echo json_encode($result);

?>
