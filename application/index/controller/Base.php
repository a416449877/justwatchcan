<?php
namespace app\index\controller;

use library\Controller;
use think\facade\Request;
use think\Db;

/**
 * 验证登录控制器
 */
class Base extends Controller
{
    protected $rule = ['__token__' => 'token'];
    protected $msg  = ['__token__'  => '无效token！'];

    function __construct() {
        parent::__construct();
        // $this->kfcode = db('xy_script')->where('id',1)->value('script');
        $this->kfcode = db('xy_cs')->where('id',1)->value('url');
               $this->kfcode2 = db('xy_cs')->where('id',2)->value('url');
        $lang=cookie('think_var');
        if(!$lang){
           cookie('think_var','en'); 
        }
        
        $uid = session('user_id');
        $pwd = session('pwd');
        // if($pwd==null){
        //     session('pwd','');
        //     session('user_id','');
        //     $this->redirect('User/login');
        // }
        // $userinfo=Db::name('xy_users')->where(['id'=>$uid])->find();
        // if($userinfo['pwd'] != $pwd){
        //     session('pwd','');
        //     session('user_id','');
        //     $this->redirect('User/login');
        // }
        
        if (!$uid) {
            $uid = cookie('user_id');
        }

        if(!$uid && request()->isPost()){
            $this->error('Please log in first');
        }
        if(!$uid) $this->redirect('User/login');
        /***实时监测账号状态***/
        // $uinfo = db('xy_users')->find($uid);
        // if($uinfo['status']!=1){
        //     \Session::delete('user_id');
        //     $this->redirect('User/login');
        // }
        /***实时新用户的体验金额***/
        $uinfo = db('xy_users')->find($uid);
        if($uinfo['is_new']==1){
            $time=config("registered_day")*60*60*24;
            if(time()-$uinfo['addtime']>$time){
                Db::table('xy_users')->where("id",$uid)->setField("is_new",2);
                Db::table('xy_users')->where("id",$uid)->setDec("balance",config('registration'));
            }
        }
        $this->console = db('xy_script')->where('id',1)->value('script');
        //自动升级vip
        sj_dd();
//         //自动升级vip
//         $level=Db::name('xy_level')->select();
//         $balance=Db::table('xy_users')->where("id",$uid)->find();//value("balance");
//         $lixibao=Db::name('xy_lixibao')->where(['uid'=>$uid,'is_qu'=>0])->sum('num');
//         $user=Db::name('xy_users')->where(['id'=>$uid])->find();

//         $pid_count=Db::table('xy_users')->where(["parent_id"=>$uid])->where('balance','>=',20)->count();
//   //Db::table('xy_users')->where('id',$uid)->update(['level'=>0]);
//         foreach ($level as $k=>$v){
            
//               if($balance['balance']+$balance['lixibao_balance']+$lixibao>=$v['num_min'] && $pid_count>=$v['auto_vip_xu_num']){
                  
//                   if($v['level']>$user['level']){
                    
//                   Db::table('xy_users')->where("id",$uid)->update(["level"=>$v['level']]);
//                   }
//               } 
//         }


         $lixibaos = Db::name('xy_lixibao')->where(['is_qu'=>0,'uid'=>session('user_id')])->select();
        
         if($lixibaos){
             foreach ($lixibaos as $k=>$v){
                
                 $lixibao = Db::name('xy_lixibao')->find($v['id']);
                 if (!$lixibao) {
                     return json(['code' => 1, 'info' => lang('cscw')]);
                 }
                 if ($lixibao['is_qu']) {
                     return json(['code' => 1, 'info' =>  lang('cscw')]);
                 }
                 $price = $lixibao['num'];
    
                 if ($uinfo['lixibao_balance'] < $price) {
                     return json(['code' => 1, 'info' => lang('money_not')]);
                 }
                 //利息宝参数
                 $lxbParam = Db::name('xy_lixibao_list')->find($lixibao['sid']);
                
     
                 $issy = 0;
                    if (time() < $lixibao['endtime']) {
                        //未到期
                        //return json(['code' => 1, 'info' => lang('sjwd')]);
                           //if(time() < $lixibao['endtime'] && $lxbParam['time_state']==0){
                           // $issy = 1;
                           // $real_num=0;
                        //}
                    } else  {
                        
                        $issy = 1;
                        $real_num=$lixibao['yuji_num'];
                        
                        $oldprice = $price;
                        $shouxu = $lxbParam['shouxu'];
                        if ($shouxu) {
                            $price = $price - $price * $shouxu;
                        }
                        
                        $res = Db::name('xy_lixibao')->where('id', $v['id'])->update([
                         'endtime' => time(),
                         'is_qu' => 1,
                         'is_sy' => $issy,
                         'shouxu' => $oldprice * $shouxu,
                         "real_num"=>$real_num
                        ]);
        
    
                        Db::name('xy_users')->where('id', $uid)->setInc('balance', $price+$real_num);  //余额 +
                        Db::name('xy_users')->where('id', $uid)->setDec('lixibao_balance', $price);  //余额 +
                         $res1 = Db::name('xy_balance_log')->insert([
                             //记录返佣信息
                             'uid' => $uid,
                             'oid' => $v['id'],
                             'num' => $price,
                             'type' => 22,
                             'addtime' => time()
                         ]);
                         //利息宝记录转出
                         Db::name('xy_balance_log')->insert([
                             //记录返佣信息
                             'uid' => $uid,
                             'oid' => $v['id'],
                             'num' => $real_num,
                             'type' => 23,
                             'addtime' => time()
                         ]);
                       
                    }
    
             }
         }
         
         
         
    }

    /**
     * 空操作 用于显示错误页面
     */
    public function _empty($name){
        return $this->fetch($name);
    }

    //图片上传为base64为的图片
  /*  public function upload_base64($type,$img){
        if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $img, $result)){
            $type_img = $result[2];  //得到图片的后缀
            //上传 的文件目录

            $App = new \think\App();
            $new_files = $App->getRootPath() . 'upload'. DIRECTORY_SEPARATOR . $type. DIRECTORY_SEPARATOR . date('Y') . DIRECTORY_SEPARATOR . date('m-d') . DIRECTORY_SEPARATOR ;

            if(!file_exists($new_files)) {
                //检查是否有该文件夹，如果没有就创建，并给予最高权限
                //服务器给文件夹权限
                mkdir($new_files, 0777,true);
            }
            //$new_files = $new_files.date("YmdHis"). '-' . rand(0,99999999999) . ".{$type_img}";
            $new_files = check_pic($new_files,".{$type_img}");
            if (file_put_contents($new_files, base64_decode(str_replace($result[1], '', $img)))){
                //上传成功后  得到信息
                $filenames=str_replace('\\', '/', $new_files);
                $file_name=substr($filenames,strripos($filenames,"/upload"));
                return $file_name;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
*/
    /**
     * 检查交易状态
     */
    public function check_deal()
    {
        $uid = session('user_id');
        $uinfo = db('xy_users')->field('deal_status,status,balance,level,deal_count,deal_time,deal_reward_count dc')->find($uid);
        if($uinfo['status']==2) return ['code'=>1,'info'=>lang('gzhybjy')];
        if($uinfo['deal_status']==0) return ['code'=>1,'info'=>lang('gzhybdj')];
        if($uinfo['deal_status']==3) return ['code'=>-2,'info'=>lang('gzhczwwcdd')];
        
        //$count = db('xy_convey')->where('addtime','between',[strtotime(date('Y-m-d')),time()])->where('uid',session('user_id'))->where('status',2)->count('id');//统计当天完成交易的订单
        // if($count>=config('deal_count')) return ['code'=>1,'info'=>'今日交易次数已达上限!'];
        
        
        if($uinfo['deal_time']==strtotime(date('Y-m-d'))){
            //交易次数限制
            $level = $uinfo['level'];
            !$uinfo['level'] ? $level = 0 : '';
            $ulevel = Db::name('xy_level')->where('level',$level)->find();
            if ($uinfo['deal_count'] >= $ulevel['order_num']) {
                return ['code'=>1,'info'=>lang('hyddjycsbz')];
            }

            //if($uinfo['deal_count'] >= config('deal_count')+$uinfo['dc']) return ['code'=>1,'info'=>'今日交易次数已达上限!'];
        }else{
            //重置最后交易时间
            // db('xy_users')->where('id',$uid)->update(['deal_time'=>strtotime(date('Y-m-d')),'deal_count'=>0,'recharge_num'=>0,'deposit_num'=>0]);
            db('xy_users')->where('id',$uid)->update(['deal_time'=>strtotime(date('Y-m-d')),'recharge_num'=>0,'deposit_num'=>0]);
            //'deal_count'=>0,
        }

        return false;
    }

}
