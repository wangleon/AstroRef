<?php include_once("header.php") ?>

<div id="wrap">

<h2>添加文献</h2>

<div id="cen_body">
<p id="url_des">将文献的URL地址拷贝到下面的输入框，我们帮你自动抓取文献信息（标题、摘要、作者等）。</p>

<?php ShowErrorMsg(); ?>

<div id="url_form">
<div class="ye_r_t"><div class="ye_l_t"><div class="ye_r_b"><div class="ye_l_b">

    <form id="login" name="login" method="post" action="edit.php">
    <span id="url_label">文献的URL地址：</span>
    <input type="text" name="url" id="url_input" onfocus="this.select()" value=""/>
    <input id="url_submit" type="submit" value="导入文献" />
    </form>
</div></div></div></div>
</div>

<div id="url_detail">
    <h4>支持的数据库</h4>
    <ul>
        <li>
            <p><a href="http://adsabs.harvard.edu/">NASA ADS</a></p>
            <p>URL地址格式为 http://adsabs.harvard.edu/abs/2009ApJ...701L..68A </p>
        </li>
    </ul>
</div>
</div>

</div><!-- div id=wrap -->
<?php include_once("footer.php") ?>
