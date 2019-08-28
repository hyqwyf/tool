
<?php
header("Content-type:text/html;charset=utf-8");
// 连接数据库
$con = mysql_connect("数据库地址","数据库账号","数据库密码");
if (!$con){die('Could not connect: ' . mysql_error());}
 
mysql_select_db("数据库名", $con);
 
// 每页显示条数
$pageLine = 5;
 
// 计算总记录数
$ZongPage = mysql_query("select count(*) from 表名");
 
// 计算总页数
$sum = mysql_fetch_row($ZongPage);
$pageCount = ceil($sum[0]/$pageLine);   
 
// 定义页码变量
@$tmp = $_GET['page'];
 
 
// 计算分页起始值
$num = ($tmp - 1) * $pageLine;
 
// 查询语句
$result = mysql_query("SELECT 字段  FROM  表名 ORDER BY id DESC LIMIT " . $num . ",$pageLine");
 
// 遍历输出
while($row = mysql_fetch_array($result))
  {
      echo $row['字段'];
      echo "<br/>";
  }
 
//分页按钮
//上一页
$lastpage = $tmp-1;
//下一页
$nextpage = $tmp+1;
 
//防止翻过界
if (@$tmp > $pageCount) {
    echo "没有那么多页啦，请返回";
}
 
//如果页码大于总页数，则显示没有了
if(@$tmp <= 1){
    echo "<a href=\"fenye.php?page=$nextpage\">下一页</a>";
}else if(@$tmp > 1 && @$tmp < $pageCount){
    echo "<a href=\"fenye.php?page=$lastpage\">上一页</a>";
    echo "<a href=\"fenye.php?page=$nextpage\">下一页</a>";
}else if(@$tmp = $pageCount){
    echo "<a href=\"fenye.php?page=$lastpage\">上一页</a>";
}
 
// 关闭数据库连接
mysql_close($con);
?>
