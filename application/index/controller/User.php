<?php

// +----------------------------------------------------------------------
// | ThinkAdmin
// +----------------------------------------------------------------------
// | 版权所有 2014~2019 
// +----------------------------------------------------------------------

// +----------------------------------------------------------------------

// +----------------------------------------------------------------------
// | 

// +----------------------------------------------------------------------

namespace app\index\controller;

use library\Controller;
use think\Db;

/**
 * 登录控制器
 */
class User extends Controller
{

    protected $table = 'xy_users';
    

    /**
     * 空操作 用于显示错误页面
     
    public function _empty($name){

        return $this->fetch($name);
    }
    /*
public function xiazaipic(){
    $all=db::name('xy_goods_list')->select();
  for ($i=0;$i<273;$i++){

if(!is_dir('/www/wwwroot/shuadan/public'.$all[$i]['goods_pic'])){

     
      $shuzu=explode('/',$all[$i]['goods_pic']);
$dir='/www/wwwroot/shuadan/public/upload/'.$shuzu['2'];
echo $i.'目录为'.$dir;
      if(!is_dir($dir)){   mkdir($dir, 0755, true);}
      file_put_contents('/www/wwwroot/shuadan/public'.$all[$i]['goods_pic'],file_get_contents('https://www.letterboxduk.com/'.$all[$i]['goods_pic']));

}



  }
  echo '完成';
    exit();
}
*/



    //用户登录页面
    public function login()
    {
        
        // if(session('user_id')!='' && session('pwd')!='') $this->redirect('index/index');
        return $this->fetch();
    }
    public function ss(){
        $id="mfPJgUYJwjfCkLgWKsnYzCJ48LsPzo42fXMOtXlh";
        $key="zI2XXXHfgl8Rm7NHMr5ubUygCGaXrKCjEi9cEPBlenXyw7qaSjR8f27yo0iJnFfsh5FA25fMj9CWBswJshNc0lQhUKRM27hhaxV5QdPr5DNpuHrc9kjRkGtBHOx1a26g";
        $res=FractalPosts($id,$key);
        dump($res);
    }

      function get_real_ip()
    {
    
        $ip=FALSE;
    
        //客户端IP 或 NONE 
        if(!empty($_SERVER["HTTP_CLIENT_IP"])){
    
            $ip = $_SERVER["HTTP_CLIENT_IP"];
    
        }
    
        //多重代理服务器下的客户端真实IP地址（可能伪造）,如果没有使用代理，此字段为空
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    
                if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
         $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
     }
    
        }
        //客户端IP 或 (最后一个)代理服务器 IP
        return ($ip ? $ip : $_SERVER['REMOTE_ADDR']);
    }
    //用户登录接口
    public function do_login()
    {    
        
        // $this->applyCsrfToken();//验证令牌
        $tel = input('post.tel/s','');
        /*if(!is_mobile($tel)){
            return json(['code'=>1,'info'=>lang('sjhmgzbzq')]);
        }*/
        $num = Db::table($this->table)->where(['tel|username'=>$tel])->count();
        if(!$num){
            return json(['code'=>1,'info'=>lang('zhbcz')]);
        }

        $pwd         = input('post.pwd/s', ''); 
        $keep        = input('post.keep/b', false);    
        $jizhu        = input('post.jizhu/s', 0);


        $userinfo = Db::table($this->table)->field('id,pwd,salt,pwd_error_num,allow_login_time,status,login_status,headpic')->where('tel|username',$tel)->find();
        // dump(Db::table($this->table)->getLastSql());
        if(!$userinfo)return json(['code'=>3,'info'=>lang('not_user').'1']);
        if($userinfo['status'] != 1)return json(['code'=>1,'info'=>"Your account has been disabled, please contact customer service!"]);
        //if($userinfo['login_status'])return ['code'=>1,'info'=>'此账号已在别处登录状态'];
        if($userinfo['allow_login_time'] && ($userinfo['allow_login_time'] > time()) && ($userinfo['pwd_error_num'] > config('pwd_error_num')))return ['code'=>1,'info'=>'Too many consecutive incorrect passwords, please Try again in '.config('allow_login_min').'minutes'];  
        if($userinfo['pwd'] != sha1($pwd.$userinfo['salt'].config('pwd_str'))){
            Db::table($this->table)->where('id',$userinfo['id'])->update(['pwd_error_num'=>Db::raw('pwd_error_num+1'),'allow_login_time'=>(time()+(config('allow_login_min') * 60))]);
            return json(['code'=>1,'info'=>lang('pass_error')]);  
        }
        
        Db::table($this->table)->where('id',$userinfo['id'])->update(['pwd_error_num'=>0,'allow_login_time'=>0,'login_status'=>1,'login_ip'=>$this->get_real_ip()]);
        session('user_id',$userinfo['id']);
        session('pwd',$userinfo['pwd']);
        session('avatar',$userinfo['headpic']);
        

        if ($jizhu) {
            cookie('tel',$tel);
            cookie('pwd',$pwd);
        }

        if($keep){
            Cookie::forever('user_id',$userinfo['id']);
            Cookie::forever('tel',$tel);
            Cookie::forever('pwd',$pwd);
        }
        return json(['code'=>0,'info'=>'Login successful!']);  
    }

    /**
     * 用户注册接口
     */
    public function do_register()
    {
        $tel = input('post.tel/s',''); 
        $user_name   = input('post.user_name/s', '');
        //$user_name = '';    //交给模型随机生成用户名
        $verify      = input('post.verify/d', '');       //短信验证码
        $pwd         = input('post.pwd/s', '');
        $pwd2        = input('post.deposit_pwd/s', '');
        $invite_code = input('post.invite_code/s', '');     //邀请码
        $code = input('post.code/s', '');     //验证码
        $gender = input('post.gender/d', 0);  
        //echo  mt_rand(10000,99999);;return;
        if(!$invite_code) return json(['code'=>1,'info'=>'邀请码不能为空']);
        
        if($code!=session('code')){
            return json(['code'=>1,'info'=>'The verification code is incorrect']);
        }

        /*if(config('app.verify')){
            $verify_msg = Db::table('xy_verify_msg')->field('msg,addtime')->where(['tel'=>$tel,'type'=>1])->find();
            //if(!$verify_msg)return json(['code'=>1,'info'=>lang('yzmbcz')]);
            //if($verify != $verify_msg['msg'])return json(['code'=>1,'info'=>lang('Verification code error')]);
            //if(($verify_msg['addtime'] + (config('app.zhangjun_sms.min')*60)) < time())//return json(['code'=>1,'info'=>'The verification code is invalid']);
        }*/

        $pid = 0;
        if($invite_code) {
            $parentinfo = Db::table($this->table)->field('id,status,invite_code')->where('id',$invite_code)->find();
            if(!$parentinfo) return json(['code'=>1,'info'=>lang('code_not')]);
            if($parentinfo['status'] != 1) return json(['code'=>1,'info'=>'This recommended user has been disabled']);

            $pid = $parentinfo['id'];
        }else{
            $pid = 0;
        }

        $res = model('admin/Users')->add_users($tel,$user_name,$pwd,$pid,'',$pwd2,$gender);
        return json($res);
    }


    public function logout(){
        \Session::delete('user_id');
        $this->redirect('login');
    }

    /**
     * 重置密码
     */
    public function do_forget()
    {
        app(\think\Controller::class)->enginet();
        if(!request()->isPost()) return json(['code'=>1,'info'=>'错误请求']);
        $tel = input('post.tel/s','');
        $pwd = input('post.pwd/s','');
        $verify = input('post.verify/d',0);
        if(config('app.verify')){
            $verify_msg = Db::table('xy_verify_msg')->field('msg,addtime')->where(['tel'=>$tel,'type'=>2])->find();
            if(!$verify_msg)return json(['code'=>1,'info'=>lang('yzmbcz')]);
            if($verify != $verify_msg['msg'])return json(['code'=>1,'info'=>lang('Verification code error')]);
            if(($verify_msg['addtime'] + (config('app.zhangjun_sms.min')*60)) < time())return json(['code'=>1,'info'=>lang('yzmysx')]);
        }
        $res = model('admin/Users')->reset_pwd($tel,$pwd);
        return json($res);
    }
    
    public function lang_set(){
        $lang=input('lang');
        cookie('think_var',$lang);
        $this->redirect('/index',302);
    }

    public function register()
    {
        $param = \Request::param(true);
        $this->invite_code = isset($param[1]) ? trim($param[1]) : '';  
        return $this->fetch();
    }
    
      public function send_msg(){
       $code = mt_rand(10000000,99999999);
        //$code = 1234;
        $tel=$_POST['tel'];
        $apiKey = "1lrHnOIc";
        $apiSecret = "hVFE3I26";
        $apiId = "D8PHd9KI";
        
        $url = "https://api.onbuka.com/v3/sendSms";
        
        $timeStamp = time();
        $sign = md5($apiKey.$apiSecret.$timeStamp);
        
        $headers = array('Content-Type:application/json;charset=UTF-8',"Sign:$sign","Timestamp:$timeStamp","Api-Key:$apiKey");
       $data=array(
           'appId'=>$apiId,
           'numbers'=>$tel,
           'content'=>$code,
           'senderId'=>''
           
        );

       $data = json_encode($data);

        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 600);
        curl_setopt($ch, CURLOPT_HTTPHEADER,$headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS , $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        
        $output = curl_exec($ch);
        curl_close($ch); 
        $output=json_decode($output,TRUE);
        //var_dump($output);
        if($output['status']==0){ 
            session('code',$code);
            return json(array('code'=>$output['status'],'info'=>'send successfully'));
        }else{
            return json(array('code'=>$output['status'],'info'=>'fail to send'));
        }
    }

    /*  public function reset_qrcode()
    {
        $uinfo = Db::name('xy_users')->field('id,invite_code')->select();
        foreach ($uinfo as $v) {
            $model = model('admin/Users');
            $model->create_qrcode($v['invite_code'],$v['id']);
        }
        return '重新生成用户二维码图片成功';
    } */
}