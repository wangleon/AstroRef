    <div class="bar" id="querybar">
    <div class="ye_r_t"><div class="ye_l_t"><div class="ye_r_b"><div class="ye_l_b">
    <h6>快速搜索</h6>


    <form name="search" id="search" action="index.php" method="post">
    <p><input type="text" name="query" id="query_q" value="请输入关键字"
        onfocus="this.value=(this.value=='请输入关键字')?'':this.value;"
        onblur="this.value=(this.value=='')?'请输入关键字':this.value;"
        onmouseover="this.select();"
        onmouseout="this.blur()" />
    <input type="hidden" name="type" value="search" /></p>
    <p>
    <select name="range" id="range" size="1">
       <option value="author" checked="checked">作者</option>
       <option value="title">标题</option>
       <option value="abstract">摘要</option>
       <!--<option value="note">笔记</option>-->
       <option value="all">全部</option>
    </select>
    <input type="submit" value="OK" id="querybar_submit" /></p>
    </form>


    </div></div></div></div>
    </div>



    <div class="bar" id="mosttags">
    <div class="ye_r_t"><div class="ye_l_t"><div class="ye_r_b"><div class="ye_l_b">
    <h6>最多标签</h6>

    <ul>
<?php
$res = mysql_query('select tag,count from ref.tags order by count desc limit 10');
while ($row=mysql_fetch_row($res)) {
    $link=TagReplace($row[0]);
    echo "<li><a href=\"tag-$link\">$row[0]</a>&nbsp;&nbsp;($row[1])</li>";
}
?>
    <li><a href="tags">查看所有标签</a></li>
    </ul>


    </div></div></div></div>
    </div>
