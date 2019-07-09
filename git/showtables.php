<?php
$db = new mysqli("localhost",'root','k3o3m3nmkl33m','uchewu',3306);
 
if ($db->connect_errno)
{
    die("数据库连接失败: " . $db->connect_error);
}
 
$res = $db->query("SHOW tables");
 
$rt = array();
if ($res instanceof mysqli_result)
{
    while (($row = $res->fetch_assoc()) != FALSE)
    {

        $rt[] = $row;
    }
}
 
echo '<pre>';
 
print_r($rt);
 
echo '</pre>';
 
 
@$db->close();
 ?>
