<?php
session_start();
include_once("class.ref.php");
include_once("class.common.php");
$conn = connect_db("ref");
if (isset($_GET["ads"])) {
    $ads = urldecode($_GET["ads"]);
    $rid = check_adscode($conn,$ads);
    if ($rid==0) {
        header("location:http://adsabs.harvard.edu/abs/$ads");
    } else {
        header("location:ref-$rid");
    }

} elseif (isset($_GET["arxiv"])) {
    $arxiv = urldecode($_GET["arxiv"]);
    $rid = check_arxiv($conn,$arxiv);
    if ($rid==0) {
        header("location:https://arxiv.org/abs/$arxiv");
    } else {
        header("location:ref-$rid");
    }

}

?>
