<?php 

class ControllerBase extends Controller
{
    public function set_token($action = 'action'){
        $token['token_key'] = md5(time().md5(rand(0,9999999999)));
        $token['token_value'] = md5(md5(time()).rand(0,9999999999));
        $token['expire_time'] = time() + 600;

        $_SESSION['token_' . $action] = $token;

        $this->view->token_key = $token['token_key'];
        $this->view->token_value =$token['token_value'];

        return ['token_key'=>$token['token_key'],'token_value'=>$token['token_value']];
    }

    /**
     * 检测token
     */
    public function check_token($action = 'action', $url = ''){
        if(!isset($_SESSION['token_' . $action])){
            $this->error("请刷新页面后再试", $url);
        }
        $token = $_SESSION['token_' . $action];

        if($token['expire_time'] < time()){
            $this->error("页面停留过久，请刷新后再试", $url);
        }

        if(empty($this->request->getPost($token['token_key'])) || $this->request->getPost($token['token_key']) != $token['token_value']){
            $this->error("安全验证失败，请刷新后再试", $url);
        }
        unset($_SESSION['token_' . $action]);
        return true;
    }


}

class buyController extends ControllerBase
{

	public function step1(){
     /*页面的html部分

      <form id="" method="POST" action="/model/buy/step2">
        <input type="hidden" name="<?php echo $token_key;?>" value="<?php echo $token_value;?>">
        <input type="text" name="" value="">
        <input type="text" name="" value="">
        <input type="text" name="" value="">
      </form>
     */
		$this->set_token('action_name');


	} 	
	public function step2(){

		$this->check_token('action_name');
	} 
}



 ?>