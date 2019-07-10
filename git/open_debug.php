<?php
function v($c){
	var_dump($c);die();
}
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
	class mysqlSql{//封装函数
		const ERROR_CODE=203;
		const SUCCESS_CODE=200;
		const NO_DATA=202;
		private static $dbcon=false;
		private $host;
		private $port;
		private $user;
		private $pass;
		private $db;
		private $charset;
		private $link;
		protected $field;
		protected $need_column_methold = array('update','insert');
		public function __construct($table,$methold){//构造函数
			//线上
			// $this->user =  'root';
			// $this->pass =  'k3o3m3nmkl33m';
			// $this->db =  'uchewu';			
            //本地
			$this->user =  'root';
			$this->pass =  '123456';
			$this->db =  'car';

			$this->host =  'localhost';
			$this->port =  '3306';
			$this->charset= 'utf8';
			$this->db_connect();
			$this->db_charset();
			$this->db_usedb();
			if (in_array($methold, $this->need_column_methold)) {
				$this->getColumn($table);
			}
		}
		 //连接数据库
		private function db_connect(){
			@$this->link=mysqli_connect($this->host.':'.$this->port,$this->user,$this->pass);
			if(mysqli_connect_errno($this->link)){
				echo "数据库连接失败<br>";
				echo "错误编码".mysqli_connect_errno()."<br>";
				echo "错误信息".mysqli_connect_error()."<br>";
				exit;
			}
		}
		//设置字符集
		private function db_charset(){
			mysqli_query($this->link,"set names {$this->charset}");
		}
    	//选择数据库
		private function db_usedb(){
			mysqli_query($this->link,"use {$this->db}");
		}
   		//私有的克隆
		private function __clone(){
			die('clone is not allowed');
		}
		public static function getIntance($table,$methold){
			if(self::$dbcon==false){
				self::$dbcon=new self($table,$methold);
			}
			return self::$dbcon;
		}
		protected function getColumn($table){
			// $res = $this->link->query("SHOW COLUMNS FROM $table");
			$res = mysqli_query($this->link,"SHOW COLUMNS FROM $table");

			$rt = array();
			if ($res instanceof mysqli_result)
			{
				while (($row = $res->fetch_assoc()) != FALSE)
				{
			        $row['CanBeNull'] = $row['Null'] === 'YES';   //字段值是否可以为空，是的话值为'YES'
			        $rt[] = $row;
			    }
			}
			return $this->field = i_array_column($rt,'Field');
		}
		/**  
		* 数据添加
		* 
		* @access public 
		* @param mixed $table 数据库表名 
		* @param mixed $data 插入的数据 
		* @return json 返回类型
		*/
		public function zengjia($table){
			// v($this->field);
            //获取增加的数据
			if ( !empty($_GET['data'])) {
				// V($_GET['data']);
				if(!json_decode(stripslashes($_GET['data'])) == NULL){
					$insertdata = get_object_vars(json_decode(stripslashes($_GET['data'])));
					// switch ($table) {
						// case 'user':
					$valstr = '';
					foreach ($this->field as $column) {//拼接插入字段对应的值
						// $valstr.='"';
						$valstr.= empty($insertdata[$column])?'"",':'"'.addslashes($insertdata[$column]).'",';
					}
					//去除最后一个的逗号
					$valstr = trim($valstr,',');
					// $sql = "INSERT INTO ".$table .'('. implode(',', $this->field).") VALUES ('".$insertdata['username']."',".$insertdata['password'].")";
					$sql = "INSERT INTO ".$table .'('. implode(',', $this->field).") VALUES (".$valstr.")";
					// 		break;

					// 	default:
					// 		# code...
					// 		break;
					// }
					// $row = $this->link->query($sql);
					// v($sql);
					$row = mysqli_query($this->link,$sql);
					if ($row) {//插入成功
						$this->json_return(self::SUCCESS_CODE,'insert success','');
					}else{//插入失败
						$this->json_return(self::ERROR_CODE,'insert failed','');
					}

				}else{
					$this->json_return(self::ERROR_CODE,'Parameter error','');
				}
			}else{
				$this->json_return(self::ERROR_CODE,'Parameter error','');
			}
		}
		/**  
		* 数据更新
		* 
		* @access public 
		* @param mixed $table 数据库表名 
		* @param mixed $key 更新条件 
		* @param mixed $val 更新条件的值 
		* @param mixed $data 更新的数据内容 
		* @return json 返回类型
		*/
		public function gengxin($table){
			if (empty($_GET['key']) || empty($_GET['val']) || empty($_GET['data'])) {//获取更新条件
				$this->json_return(self::ERROR_CODE,'Parameter error','');
			}else{
				$key = addslashes($_GET['key']);//更新条件键
				$val = addslashes($_GET['val']);//更新条件值
				$sql1 = "SELECT * from ".$table." where $key = '$val'";
				$result1= mysqli_query($this->link,$sql1);//查询需要更新的数据是否存在
				if(!mysqli_num_rows($result1) > 0) {//需要更新的数据不存在
					$this->json_return(self::ERROR_CODE,'No update  data','');
				}else{
		             //获取更新的数据
					if(!json_decode(stripslashes($_GET['data'])) == NULL){
						$updata = get_object_vars(json_decode(stripslashes($_GET['data'])));
						// $sql = "UPDATE ".$table." SET username='".$data['username']."',password='".$data['username']."' where id=".$id;
						// if (empty($updata[$key])) {//避免未传key而更改数据库的值
						// if($row = mysqli_fetch_assoc($result1)) {// 输出数据
						// 	v($row);
						// }
						// }
						$olddata = mysqli_fetch_assoc($result1);
						$updata['id']=$olddata['id'];
						$sql = "UPDATE ".$table." SET ";
						foreach ($this->field as $column) {//拼接更新的字段对应的值
							$str = empty($updata[$column])?'""':addslashes($updata[$column]);
							$sql.= $column ."= '".$str ."',";
						}
						//去除最后一个的逗号
						$sql = trim($sql,',');
						$sql .= 'where '.$key.'='.$val;
						$result= mysqli_query($this->link,$sql);
						if ($result) {//更新成功
							$this->json_return(self::SUCCESS_CODE,'update success',$result);
						}else{//更新失败
							$this->json_return(self::ERROR_CODE,'update field','');
						}
					}else{//没有更新内容
						$this->json_return(self::ERROR_CODE,'Parameter error','');
					}
				}
			}
		}
		/**  
		* 数据删除
		* 
		* @access public 
		* @param mixed $table 数据库表名 
		* @param mixed $key 删除条件 
		* @param mixed $val 删除条件的值 
		* @return json 返回类型
		*/
		public function shanchu($table){//数据库删除
			if (empty($_GET['key']) || empty($_GET['val'])) {//获取更新条件
				$this->json_return(self::ERROR_CODE,'No deletion condition','');
			}else{
				$key = addslashes($_GET['key']);//删除条件键
				$val = addslashes($_GET['val']);//删除条件值
	            //查询要删除的数据是否存在
				$sql = "SELECT * FROM ".$table." WHERE $key= '$val'";
				$result= mysqli_query($this->link,$sql);
				if ($result !== false) {
					if (mysqli_num_rows($result) > 0) {//存在符合删除条件的数据
						//删除数据
						$sql = "DELETE FROM ".$table." WHERE $key= '$val'";
						$row = mysqli_query($this->link,$sql);
						if ($row) {//删除成功
							$this->json_return(self::SUCCESS_CODE,'delete success','');
						}else{//删除失败
							$this->json_return(self::ERROR_CODE,'delete failed','');
						}
					} else {//没有符合删除条件的数据
						$this->json_return(self::NO_DATA,'no data','');
					}
				}else{
					$this->json_return(self::NO_DATA,'no data','');
				}
			}
		}
		/**  
		* 查询单条数据
		* 
		* @access public 
		* @param mixed $table 数据库表名 
		* @param mixed $id 查询条件 
		* @return json 返回类型
		*/
		public function chaxunOne($table){//数据库查询单条记录
			if (empty($_GET['key']) || empty($_GET['val'])) {
				$this->json_return(self::ERROR_CODE,'Parameter error','');
			}else{
				$key = addslashes($_GET['key']);//查询条件键
				$val = addslashes($_GET['val']);//查询条件值
				$sql = "SELECT * FROM ".$table." WHERE $key= '".$val."'";
				$result= mysqli_query($this->link,$sql);
				v($sql);
				if ($result!== false) {
					if (mysqli_num_rows($result) > 0) {
						if($row = mysqli_fetch_assoc($result)) {// 输出数据
							$this->json_return(self::SUCCESS_CODE,'success',$row);
						}
					} else {//无符合查询条件数据
						$this->json_return(self::NO_DATA,'no data','');
					}
				}else{//无符合查询条件数据
					$this->json_return(self::NO_DATA,'no data','');
				}
			}
		}
		/**  
		* 查询所有数据
		* 
		* @access public 
		* @param mixed $table 数据库表名 
		* @return json 返回类型
		*/
		public function chaxunAll($table){//数据库查询全部记录
			$datas = array();
			$sql = "SELECT * FROM ".$table;
			if (!empty($_GET['key']) && !empty($_GET['val'])){
				$key = addslashes($_GET['key']);
				$val = addslashes($_GET['val']);
				$sql.=" WHERE $key = '".$val."'";
			}
			$result= mysqli_query($this->link,$sql);
			if ($result !== false) {
				if (mysqli_num_rows($result) > 0) {
					while($row = mysqli_fetch_assoc($result)) {// 输出数据
					// if ($vals!=null||$vals!="") {
						// print_r($row);
					// }
					//$sqls = "DELETE FROM cy_weizhang WHERE id=269";
					//mysqli_query($conn,$sqls);
						array_push($datas,$row);//合并符合条件的多条数据
					}
					$this->json_return(self::SUCCESS_CODE,'success',$datas);	
				} else {//无符合查询条件数据
					$this->json_return(self::NO_DATA,"no data",'');
				}
			}else{//无符合查询条件数据
				$this->json_return(self::NO_DATA,"no data",'');			

			}
		}

		/**  
		* 统一返回格式
		* 
		* @access public 
		* @param mixed $code 返回代码
		* @param mixed $msg 返回信息
		* @param mixed $data 返回数据 
		* @return json 返回类型
		*/
		public function json_return($code,$msg,$data){
			$data = array(
				'code'=>$code,
				'msg'=>$msg,
				'data'=>$data,
			);
			return print_r(json_encode($data));	
		}
	}

	if (empty($_GET['methold']) || empty($_GET['table'])) {
		$db->json_return($db::ERROR_CODE,"Parameter error",'');
	}else{
		$methold = $_GET['methold'];//获取请求执行的操作
		$table = $_GET['table'];//获取要操作的表
		$db = mysqlSql::getIntance($table,$methold);

		switch ($methold) {
			case 'insert':
			$db->zengjia($table);//添加数据
			break;		
			case 'delete':
			$db->shanchu($table);//删除数据
			break;		
			case 'queryone':
			$db->chaxunOne($table);//查询单条
			break;			
			case 'queryall':
			$db->chaxunall($table);//查询所有
			break;		
			case 'update':
			$db->gengxin($table);//更新数据
			break;
			
			default:
			$db->json_return($db::ERROR_CODE,"No method",'');//
			break;
		}

	}

	?>
