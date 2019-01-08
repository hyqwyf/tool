<?php 
//调用顺序
/**
 *@param $count 记录总数
 */
$pageinfo = Page::make($count);

$this->view->pager = $this->get_pager($pageinfo['currentPage'], $pageinfo['count']);

public function get_pager($page = 1, $total = 0 ,$limit = 10){
	$Pager = new Pager($total,$limit,$page);
	return $Pager->myde_write();
}
    class Page{
        /**
         * @param $count 记录总数
         * @param int $page第几页
         * @param int $pageSize 分页大小，如果为空，会在配置中读取
         * @return array pages 一共几页 offset 从第几条开始 limit 取几条
         */
        public static function make($count,$page = 0,$pageSize = 0)
        {
            if(empty($page)){
                $page = (int)$_REQUEST['page'] >= 1 ? $_REQUEST['page']: 1;
            }
            if(empty($pageSize)){
                $params = new ConfigIni(APP_PATH."/config". ENV ."/params.ini");
                $pageSize = $params['page']['size'];
            }

            $pages = ceil($count/$pageSize);

            if($pages < $page){
                $page = $pages;
            }

            $offset = ($page-1)* $pageSize;
            $limit = $pageSize;

            return [
                'pages'=>$pages,
                'offset'=>$offset,
                'limit'=>$limit,
                'currentPage'=>$page,
                'count'=>$count
            ];
        }

    }

    class Pager
{

    private $myde_total;          //总记录数
    private $myde_size;           //一页显示的记录数
    private $myde_page;           //当前页
    private $myde_page_count;     //总页数
    private $myde_i;              //起头页数
    private $myde_en;             //结尾页数
    private $myde_url;            //获取当前的url
    /*
    * $show_pages
    * 页面显示的格式，显示链接的页数为2*$show_pages+1。
    * 如$show_pages=2那么页面上显示就是[首页] [上页] 1 2 3 4 5 [下页] [尾页]
    */
    private $show_pages;

    public function __construct($myde_total = 1, $myde_size = 1, $myde_page = 1, $myde_url = '', $show_pages = 2)
    {
        $this->myde_total = $this->numeric($myde_total);
        if (empty($pageSize)) {
            $params = new ConfigIni(APP_PATH . "/config" . ENV . "/params.ini");
            $myde_size = $params['page']['size'];
        }
        $this->myde_size = $this->numeric($myde_size);
        $this->myde_page = $this->numeric($myde_page);
        $this->myde_page_count = ceil($this->myde_total / $this->myde_size);
        if (empty($myde_url)) {
            $url = 'http://' . $_SERVER['SERVER_NAME'] . ':' . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];

            if (preg_match('/page=/', $url)) {//有page参数替换
                $myde_url = preg_replace('/page=[0-9]{0,100}/', 'page={page}', $url);
            } else {//无page参数新增
                if (preg_match('/\?/', $url)) {
                    $myde_url = $url . '&page={page}';
                } else {
                    $myde_url = $url . '?page={page}';
                }
            }

        }
        $this->myde_url = $myde_url;
        if ($this->myde_total < 0)
            $this->myde_total = 0;
        if ($this->myde_page < 1)
            $this->myde_page = 1;
        if ($this->myde_page_count < 1)
            $this->myde_page_count = 1;
        if ($this->myde_page > $this->myde_page_count)
            $this->myde_page = $this->myde_page_count;
        $this->limit = ($this->myde_page - 1) * $this->myde_size;
        $this->myde_i = $this->myde_page - $show_pages;
        $this->myde_en = $this->myde_page + $show_pages;
        if ($this->myde_i < 1) {
            $this->myde_en = $this->myde_en + (1 - $this->myde_i);
            $this->myde_i = 1;
        }
        if ($this->myde_en > $this->myde_page_count) {
            $this->myde_i = $this->myde_i - ($this->myde_en - $this->myde_page_count);
            $this->myde_en = $this->myde_page_count;
        }
        if ($this->myde_i < 1)
            $this->myde_i = 1;
    }

//检测是否为数字
    private function numeric($num)
    {
        if (strlen($num)) {
            if (!preg_match("/^[0-9]+$/", $num)) {
                $num = 1;
            } else {
                $num = substr($num, 0, 11);
            }
        } else {
            $num = 1;
        }
        return $num;
    }

//地址替换
    private function page_replace($page)
    {
        return str_replace("{page}", $page, $this->myde_url);
    }

//上一页
    private function myde_prev()
    {
        if ($this->myde_page != 1) {
            return '<li ><a href="' . $this->page_replace($this->myde_page - 1) . '"><i class="ico-back"></i></a></li>';
        } else {
//            return '<li class="disabled"><a href="' . $this->page_replace($this->myde_page - 1) . '"><i class="ico-back"></i></a></li>';
        }
    }

//下一页
    private function myde_next()
    {
        if ($this->myde_page < $this->myde_page_count) {
            return '<li ><a href="' . $this->page_replace($this->myde_page + 1) . '"><i class="ico-next"></i></a></li>';
        } else {
//            return '<li class="disabled"><a href="' . $this->page_replace($this->myde_page + 1) . '"><i class="ico-next"></i></a></li>';

        }
    }


//输出
    public function myde_write($id = 'page')
    {
        $str = '<ul class="pagination">';
        //        $str = "<div id=" . $id . ">";
        //        $str.=$this->myde_home();
        $str .= $this->myde_prev();
        if ($this->myde_i > 1) {
            $str .= "<li class='disabled'><a href='javascript:void(0);'>...</a></li>";
        }
        for ($i = $this->myde_i; $i <= $this->myde_en; $i++) {
            if ($i == $this->myde_page) {
                $str .= "<li class='active'><a href='" . $this->page_replace($i) . "' class='cur'>$i</a></li>";
            } else {
                $str .= "<li class='waves-effect'><a href='" . $this->page_replace($i) . "' >$i</a></li>";
            }
        }

        if ($i <= $this->myde_page_count) {
            $str .= "<li class='disabled'><a href='javascript:void(0);'>...</a></li>";
        }

        $str .= $this->myde_next();
        $str .= "</ul>";
        return $str;
    }

}



 ?>