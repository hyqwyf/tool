<?php 


//----------------excel2 start-----------------------------
/**
 * @param $datas  具体数据
 * @param $titles 列名
 * @param $filename 文件名
 * vnd.ms-excel.numberformat:@: 规定输出格式为纯文本
 */
function export_to_excel($datas, $filename,$titles)
{
    $str = "<html xmlns:o=\"urn:schemas-microsoft-com:office:office\"\r\nxmlns:x=\"urn:schemas-microsoft-com:office:excel\"\r\nxmlns=\"http://www.w3.org/TR/REC-html40\">\r\n<head>\r\n<meta http-equiv=Content-Type content=\"text/html; charset=utf-8\">\r\n</head>\r\n<body>";
    $str .= "<table border=1>";
    //表头
    $str .= "<tr>";
    foreach ($titles as $title) {
        $str .= "<td style='width: 160px;'>{$title}</tdw>";
    }
    $str .= "</tr>\n";
    //具体数据
    foreach ($datas as $key => $rt) {
        $str .= "<tr>";
        foreach ($rt as $k => $v) {
            $str .= "<td style='width: 160px;vnd.ms-excel.numberformat:@'>{$v}</td>";
        }
        $str .= "</tr>\n";
    }
    $str .= "</table></body></html>";
    header("Content-type:application/vnd.ms-excel");
    header("Content-Disposition:filename=".$filename.".xls");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Content-Type:application/download");;
    header("Pragma: no-cache");
    exit($str);
}
//----------------excel end-----------------------------
 ?>