<?php
session_start();
include_once("class.ref.php");
include_once("class.common.php");
$conn=connect_db("ref");
//$theme="plus";

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>

    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <link rel="shortcut icon" href="http://<?php echo $_SERVER['SERVER_NAME'] ?>/ref/favicon.ico" type="image/x-icon" />
    <link rel="stylesheet" href="./css/crossbar.css" type="text/css" media="all" />
    <link rel="stylesheet" href="./css/plus.css" type="text/css" media="all" />
    <script type="text/javascript" src="./js/plus_fixheader.js"></script>
    <script type="text/javascript" src="../share/js/jquery/jquery.min.js"></script>
    <script type="text/javascript" src="../share/js/jquery-ui/jquery-ui.min.js"></script>
    <script type="text/javascript" src="./js/search_suggest.js"></script>
    <link rel="stylesheet" href="./css/ref.css" type="text/css" media="all" />
    <link rel="stylesheet" href="./css/search_suggest.css" type="text/css" media="all" />
    <link rel="stylesheet" href="./css/<?php echo str_replace("php","css",basename($_SERVER["SCRIPT_NAME"])) ?>" type="text/css" media="all" />


<?php
if (isset($script)) {
    foreach($script as $value) {
        echo "    <script type=\"text/javascript\" src=\"$value\"></script>\n";
    }
}
?>
<?php
echo "    <title>";
if (isset($page_title)){
    echo $page_title;
} else {
    echo "文献管理";
}
echo "</title>";
?>

</head>



<body>

<div id="header">

<?php include('crossbar.php'); ?>

    <div id="titlebar">

    <div id="title">
        <h1 id="title1">References</h1>
    </div>

    <!-- ----------------------------- -->

    <div id="nav_items">

<?php
    $bkg_lst = array('index','url','tag');
    $url_lst = array('./'   ,'./url','./tags');
    $pag_lst = array('index.php', 'url.php', 'tags.php');
    for($i=0;$i<count($bkg_lst);$i++) {
        echo "<a class=\"";
        if ($i==0) {
            echo "first-nav ";
        } elseif($i==count($bkg_lst)-1) {
            echo "last-nav ";
        }

        if ($pag_lst[$i]==basename($_SERVER['SCRIPT_NAME'])) {
            echo "current-nav ";
        } else {
            echo "other-nav ";
        }

        echo "\" href=\"$url_lst[$i]\"><span class=\"nav_bg\" id=\"nav-$bkg_lst[$i]\"></span></a>\n";
    }
?>
    </div>

    <!-- ----------------------------- -->

    <form method="get" id="search_bar" action="search.php">
        <input type="text" name="search_find" id="search_find" />
        <input type="submit" id="search_submit" value="" />
    </form>

    <!-- ----------------------------- -->

    <div id="user_info">
        <span id="user_name">
        <?php
        if (isset($_SESSION["user"])) {
            echo $_SESSION["user"];
        } else {
            echo "Welcome";
        }
        ?>
        </span>
        <span id="user_action">
        <?php
        if (isset($_SESSION["user"])) {
            echo "<a href=\"logout\">Log out</a>";
        } else {
            echo "<a href=\"\">Log in</a>";
        }
        ?>
        </span>
    </div>

    <!-- ----------------------------- -->
    </div><!--titlebar-->

    <div id="header_shadow"></div>

</div><!--header-->

<div id="header-bg"></div>



