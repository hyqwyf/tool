<?php
$db = new mysqli("localhost",'root','k3o3m3nmkl33m','uchewu',3306);
 
if ($db->connect_errno)
{
    die("数据库连接失败: " . $db->connect_error);
}
$table = $_GET['table'];
if (!$table) {
	header("Content-Type: text/html;charset=utf-8");
	die('请在地址栏拼接table参数');
}
 
$res = $db->query("SHOW COLUMNS FROM $table");
 
$rt = array();
if ($res instanceof mysqli_result)
{
    while (($row = $res->fetch_assoc()) != FALSE)
    {
        $row['CanBeNull'] = $row['Null'] === 'YES';   //字段值是否可以为空，是的话值为'YES'
        $rt[] = $row;
    }
}
 
echo '<pre>';
function i_array_column($input, $columnKey, $indexKey=null){
    if(!function_exists('array_column')){
        $columnKeyIsNumber  = (is_numeric($columnKey))?true:false;
        $indexKeyIsNull            = (is_null($indexKey))?true :false;
        $indexKeyIsNumber     = (is_numeric($indexKey))?true:false;
        $result                         = array();
        foreach((array)$input as $key=>$row){
            if($columnKeyIsNumber){
                $tmp= array_slice($row, $columnKey, 1);
                $tmp= (is_array($tmp) && !empty($tmp))?current($tmp):null;
            }else{
                $tmp= isset($row[$columnKey])?$row[$columnKey]:null;
            }
            if(!$indexKeyIsNull){
                if($indexKeyIsNumber){
                  $key = array_slice($row, $indexKey, 1);
                  $key = (is_array($key) && !empty($key))?current($key):null;
                  $key = is_null($key)?0:$key;
                }else{
                  $key = isset($row[$indexKey])?$row[$indexKey]:0;
                }
            }
            $result[$key] = $tmp;
        }
        return $result;
    }else{
        return array_column($input, $columnKey, $indexKey);
    }
}
 
// print_r(i_array_column($rt,'Field'));
$rt =i_array_column($rt,'Field');
// var_dump(implode(',', $c));
var_dump($rt);die();
$colums = array();
foreach ($rt as $value) {
	array_push($colums, '"'.$value.'",');
}
var_dump($colums);
 
echo '</pre>';
 
 
@$db->close();
 ?>
