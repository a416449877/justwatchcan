<?php

// +----------------------------------------------------------------------
// | ThinkAdmin
// +----------------------------------------------------------------------
// | www.xydai.cn 新源代网
// +----------------------------------------------------------------------

// +----------------------------------------------------------------------

// +----------------------------------------------------------------------
// |

// +----------------------------------------------------------------------

namespace app\admin\controller;

use app\admin\service\NodeService;
use library\Controller;
use library\tools\Data;
use think\Console;
use think\Db;
use think\exception\HttpResponseException;

/**
 * 系统公共操作
 * Class Index
 * @package app\admin\controller
 */
class Index extends Controller
{

    /**
     * 显示后台首页
     * @throws \ReflectionException
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index()
    {
       
        $daili_user=session('daili_user');
        if($daili_user){
             $this->title = '系统代理后台';
            NodeService::applyUserAuth(true);
        }else{
            $this->title = '系统管理后台';
             NodeService::applyUserAuth(true);
        }
        
        $this->menus = NodeService::getMenuNodeTree();
        
       
        if (empty($this->menus) && !NodeService::islogin()) {
            $this->redirect('@admin/login');
        } else {
           $this->fetch();
        }
    }

    /**
     * 后台环境信息
     * @auth true
     * @menu true
     */
    public function main()
    {

        $this->think_ver = \think\App::VERSION;
        $this->mysql_ver = Db::query('select version() as ver')[0]['ver'];
        
        $user = session('admin_user');
        $daili_user=session('daili_user');
        
        //if($user['authorize']== 2 || $user['authorize']== 3 && !empty($user['nodes']) ){
         if($daili_user!=null){
            
            //获取直属下级
            //$mobile = $user['phone'];
            $mobile = $daili_user['tel'];
            $uid = Db::name('xy_users')->where('tel', $mobile)->value('id');
            
            $ids=Db::name("xy_users")->where("tel",$user['phone'])->value("id");
            
            $ids1  = Db::name('xy_users')->where('parent_id', $uid)->field('id')->column('id');
            
            
            $array=array();
            $array=array_merge($array,$ids1); 
            
            for($i=1;$i<=1000;$i++){
                if(empty($ids1)){
                    break;
                }
                $ids1  = Db::name('xy_users')->where('parent_id',"in",$ids1)->field('id')->column('id');
                if(empty($ids1)){
                    $ids1=array();
                }
                $array=array_merge($array,$ids1);
                
            }
            for($i=0;$i<count($array);$i++){
                $array[$i]="$array[$i]";
            }
            $str=implode(',',$array);
            if($array==null){$str=0;}
            
        }
        
        $zhenr = DB::table('xy_users')->where('is_jia',1)->field('id')->select();
        
        //昨天
        $yes1 = strtotime( date("Y-m-d 00:00:00",strtotime("-1 day")) );
        $yes2 = strtotime( date("Y-m-d 23:59:59",strtotime("-1 day")) );
        // $where=[];
        // if(!empty($_POST['start_time'])){
        //     $start_time=$_POST['start_time'];
        //     $where=[
        //         "time"=>['>=',$start_time]
        //     ];
        // }
        //  $end_time=$_POST['end_time'];
        //     var_dump($end_time);exit;
        // if(!empty($_POST['end_time'])){
           
        //     $where=[
        //         "time"=>['<=',$end_time]
        //     ];
        // }
        // if(!empty($_POST['start_time']) && !empty($_POST['end_time']))
       // if($user['authorize']== 2 || $user['authorize']== 3 && !empty($user['nodes']) ){
        if($daili_user!=null){
        //签到
        // $this->sign=Db::table('xy_sign a')->where($whereUser)->count('uid');
        $res=Db::query("select uid from xy_sign where uid in ($str)");
        
        $this->sign=count($res);
        
        // $this->today_sign=Db::table('xy_sign a')->join("xy_users b","a.uid=b.id")->where("b.is_jia",0)->where("a.time",date("Y-m-d"))->where($whereUser)->count('a.uid');
        $time=date('Y-m-d');
        $time2=date("Y-m-d",strtotime("-1 day"));
        $res=Db::query("select uid from xy_sign where uid in ($str) and time='$time'");
        $this->today_sign=count($res);
        // $this->yes_sign=Db::table('xy_sign a')->join("xy_users b","a.uid=b.id")->where("b.is_jia",0)->where("a.time",date("Y-m-d",strtotime("-1 day")))->where($whereUser)->count('a.uid');
        $res=Db::query("select uid from xy_sign where uid in ($str) and time='$time2'");
        $this->yes_sign=count($res);
        
        
        // var_dump($this->today_sign);exit;
        
        
        
        
        
        
        
        //商品
        $this->goods_num = Db::name('xy_goods_list')->count('id');
        
        $this->today_goods_num = Db::name('xy_goods_list')->where('addtime','between',[strtotime(date('Y-m-d')),time()])->count('id');
        $this->yes_goods_num = Db::name('xy_goods_list')->where('addtime','between',[$yes1,$yes2])->count('id');


        //用户
        // $this->users_num = Db::name('xy_users')->where($whereUser2)->count('id');
        $res=Db::query("select id from xy_users where id in ($str)");
        $this->users_num=count($res);
        
        
        // $this->today_users_num = Db::name('xy_users')->where("is_jia",0)->where('addtime','between',[strtotime(date('Y-m-d')),time()])->where($whereUser2)->count('id');
        $time=strtotime(date('Y-m-d'));
        $time2=time();
        $res=Db::query("select id from xy_users where id in ($str) and addtime BETWEEN $time and $time2");
        $this->today_users_num=count($res);
        
        
        // $this->yes_users_num = Db::name('xy_users')->where("is_jia",0)->where('addtime','between',[$yes1,$yes2])->where($whereUser2)->count('id');
        $res=Db::query("select id from xy_users where id in ($str) and addtime BETWEEN $yes1 and $yes2");
        $this->yes_users_num=count($res);


        


        //订单数量
        // $this->order_num = Db::name('xy_convey a')->join("xy_users b","a.uid=b.id")->field("a.*,b.is_jia")->where("b.is_jia",0)->where($whereUser)->count('a.id');
        $res=Db::query("select uid from xy_convey where uid in ($str)");
        $this->order_num=count($res);
        
        // $this->today_order_num = Db::name('xy_convey a')->join("xy_users b","a.uid=b.id")->field("a.*,b.is_jia")->where("b.is_jia",0)->where('a.addtime','between',[strtotime(date('Y-m-d')),time()])->where($whereUser)->count('a.id');
        $res=Db::query("select uid from xy_convey where uid in ($str) and addtime between $time and $time2");
        $this->today_order_num=count($res);
        
        
        // $this->yes_order_num = Db::name('xy_convey a')->join("xy_users b","a.uid=b.id")->field("a.*,b.is_jia")->where("b.is_jia",0)->where('a.addtime','between',[$yes1,$yes2])->where($whereUser)->count('a.id');
        $res=Db::query("select uid from xy_convey where uid in ($str) and addtime between $yes1 and $yes2");
        $this->yes_order_num=count($res);
        
        
        
        
        //完成任务人数
        // $this->task=Db::name('xy_convey a')->join("xy_users b","a.uid=b.id")->field("a.*,b.is_jia")->where("b.is_jia",0)->where($whereUser)->count('a.uid');
        $res=Db::query("select uid from xy_convey where uid in ($str)");
        $this->task=count($res);
        
        // $this->today_task = Db::name('xy_convey a')->join("xy_users b","a.uid=b.id")->field("a.*,b.is_jia")->where("b.is_jia",0)->where('a.addtime','between',[strtotime(date('Y-m-d')),time()])->where($whereUser)->count('a.uid');
        $res=Db::query("select uid from xy_convey where uid in ($str) and addtime between $time and $time2");
        $this->today_task=count($res);
        
        // $this->yes_task = Db::name('xy_convey a')->join("xy_users b","a.uid=b.id")->field("a.*,b.is_jia")->where("b.is_jia",0)->where('a.addtime','between',[$yes1,$yes2])->where($whereUser)->count('a.uid');
        $res=Db::query("select uid from xy_convey where uid in ($str) and addtime between $yes1 and $yes2");
        $this->yes_task=count($res);
        
        
        
        
        //订单总额
        // $this->order_sum = Db::name('xy_convey a')->join("xy_users b","a.uid=b.id")->field("a.*,b.is_jia")->where("b.is_jia",0)->where($whereUser)->sum('a.num');
        $res=Db::query("select sum(num) num from xy_convey where uid in ($str)");
        $this->order_sum=$res[0]['num'];
        
        
        // $this->today_order_sum = Db::name('xy_convey a')->join("xy_users b","a.uid=b.id")->field("a.*,b.is_jia")->where("b.is_jia",0)->where('a.addtime','between',[strtotime(date('Y-m-d')),time()])->where($whereUser)->sum('a.num');
        $res=Db::query("select sum(num) num from xy_convey where uid in ($str) and addtime between $time and $time2");
        $this->today_order_sum=$res[0]['num'];
        
        // $this->yes_order_sum = Db::name('xy_convey a')->join("xy_users b","a.uid=b.id")->field("a.*,b.is_jia")->where("b.is_jia",0)->where('a.addtime','between',[$yes1,$yes2])->where($whereUser)->sum('a.num');
        $res=Db::query("select sum(num) num from xy_convey where uid in ($str) and addtime between $yes1 and $yes2");
        $this->today_order_sum=$res[0]['num'];
        
        
        //充值人数
        // $this->recharge_num = Db::name('xy_recharge a')->join("xy_users b","a.uid=b.id")->field("a.*,b.is_jia")->where("b.is_jia",0)->where('a.status',2)->where($whereUser)->count('a.uid');
        $res=Db::query("select uid from xy_recharge where uid in ($str) and status=2 group by uid");
        $this->recharge_num=count($res);
        
        
        
        
        // $this->today_recharge_num = Db::name('xy_recharge a')->join("xy_users b","a.uid=b.id")->field("a.*,b.is_jia")->where("b.is_jia",0)->where('a.status',2)->where('a.addtime','between',[strtotime(date('Y-m-d')),time()])->where($whereUser)->count('a.uid');
        $res=Db::query("select uid from xy_recharge where uid in ($str) and status=2 and addtime between $time and $time2 group by uid");
        $this->today_recharge_num=count($res);
        
        // $this->yes_recharge_num = Db::name('xy_recharge a')->join("xy_users b","a.uid=b.id")->field("a.*,b.is_jia")->where("b.is_jia",0)->where('a.status',2)->where('a.addtime','between',[$yes1,$yes2])->where($whereUser)->count('a.uid');
        $res=Db::query("select uid from xy_recharge where uid in ($str) and status=2 and addtime between $yes1 and $yes2 group by uid");
        $this->yes_recharge_num=count($res);
        
        
        //充值
        // $this->user_recharge = Db::name('xy_recharge a')->join("xy_users b","a.uid=b.id")->field("a.*,b.is_jia")->where("b.is_jia",0)->where('a.status',2)->where($whereUser)->sum('a.num');
        $res=Db::query("select sum(num) num from xy_recharge where uid in ($str) and status=2");
        $this->user_recharge=$res[0]['num'];
        
        
        // $this->today_user_recharge = Db::name('xy_recharge a')->join("xy_users b","a.uid=b.id")->field("a.*,b.is_jia")->where("b.is_jia",0)->where('a.status',2)->where('a.addtime','between',[strtotime(date('Y-m-d')),time()])->where($whereUser)->sum('a.num');
        $res=Db::query("select sum(num) num from xy_recharge where uid in ($str) and status=2 and  addtime between $time and $time2");
        $this->today_user_recharge=$res[0]['num'];
        
        
        // $this->yes_user_recharge = Db::name('xy_recharge a')->join("xy_users b","a.uid=b.id")->field("a.*,b.is_jia")->where("b.is_jia",0)->where('a.status',2)->where('a.addtime','between',[$yes1,$yes2])->where($whereUser)->sum('a.num');
        $res=Db::query("select sum(num) num from xy_recharge where uid in ($str) and status=2 and   addtime between $yes1 and $yes2");
        $this->yes_user_recharge=$res[0]['num'];
       
        
        
        
        //提现
        // $this->user_deposit = Db::name('xy_deposit a')->join("xy_users b","a.uid=b.id")->field("a.*,b.is_jia")->where("b.is_jia",0)->where('a.status',2)->where($whereUser)->sum('a.num');
        $res=Db::query("select sum(num) num from xy_deposit where uid in ($str) and status=2 and send_status=1");
        //var_dump($res);die;
        $aaa = Db::table('xy_deposit')->where('uid','in',$str)->where(['status'=>2])->sum('num');
        $this->user_deposit=$aaa;
        
        
        // $this->today_user_deposit = Db::name('xy_deposit a')->join("xy_users b","a.uid=b.id")->field("a.*,b.is_jia")->where("b.is_jia",0)->where('a.status',2)->where('a.addtime','between',[strtotime(date('Y-m-d')),time()])->where($whereUser)->sum('a.num');
        $res=Db::query("select sum(num) num from xy_deposit where uid in ($str) and status=2  and  endtime between $time and $time2 ");
        $sasdas = $res[0]['num'];
        if(!$sasdas){
            $sasdas = 0;
        }
        
        $this->today_user_deposit=$sasdas;
        
        
        // $this->yes_user_deposit = Db::name('xy_deposit a')->join("xy_users b","a.uid=b.id")->field("a.*,b.is_jia")->where("b.is_jia",0)->where('a.status',2)->where('a.addtime','between',[$yes1,$yes2])->where($whereUser)->sum('a.num');
        $res=Db::query("select sum(num) num from xy_deposit where uid in ($str) and status=2 and     endtime between $yes1 and $yes2");
        
        $asdas = $res[0]['num']; 
        if(!$asdas){
            $asdas = 0;
        }
        $this->yes_user_deposit=$asdas;
        
         

        //抢单佣金
        // $this->user_yongjin = Db::name('xy_convey a')->join("xy_users b","a.uid=b.id")->field("a.*,b.is_jia")->where("b.is_jia",0)->where('a.status',1)->where($whereUser)->sum('a.commission');
        $res=Db::query("select sum(num) num from xy_convey where uid in ($str) and status=1");
        $this->user_yongjin=$res[0]['num'];
        
        
        // $this->today_user_yongjin = Db::name('xy_convey a')->join("xy_users b","a.uid=b.id")->field("a.*,b.is_jia")->where("b.is_jia",0)->where('a.status',1)->where('a.addtime','between',[strtotime(date('Y-m-d')),time()])->where($whereUser)->sum('a.commission');
        $res=Db::query("select sum(num) num from xy_convey where uid in ($str) and status=1 and addtime between $time and $time2");
        $this->today_user_yongjin=$res[0]['num'];
        
        
        // $this->yes_user_yongjin = Db::name('xy_convey a')->join("xy_users b","a.uid=b.id")->field("a.*,b.is_jia")->where("b.is_jia",0)->where('a.status',1)->where('a.addtime','between',[$yes1,$yes2])->where($whereUser)->sum('a.commission');
        $res=Db::query("select sum(num) num from xy_convey where uid in ($str) and status=1 and addtime between $yes1 and $yes2");
        $this->yes_user_yongjin=$res[0]['num'];
        
        

         //利息宝
        // $this->user_lixibao = Db::name('xy_lixibao a')->join("xy_users b","a.uid=b.id")->field("a.*,b.is_jia")->where("b.is_jia",0)->where('a.type',1)->where('a.is_sy',0)->where($whereUser)->sum('a.num');
        $res=Db::query("select sum(num) num from xy_lixibao where uid in ($str) and type=1 and is_qu=0");
        $this->user_lixibao=$res[0]['num'];
        
        
        // $this->today_user_lixibao = Db::name('xy_lixibao a')->join("xy_users b","a.uid=b.id")->field("a.*,b.is_jia")->where("b.is_jia",0)->where('a.type',1)->where('a.is_sy',0)->where('a.addtime','between',[strtotime(date('Y-m-d')),time()])->where($whereUser)->sum('a.num');
        $res=Db::query("select sum(num) num from xy_lixibao where uid in ($str) and type=1 and is_qu=0 and addtime between $time and $time2");
        $this->today_user_lixibao=$res[0]['num'];
        
        
        // $this->yes_user_lixibao = Db::name('xy_lixibao a')->join("xy_users b","a.uid=b.id")->field("a.*,b.is_jia")->where("b.is_jia",0)->where('a.type',1)->where('a.is_sy',0)->where('a.addtime','between',[$yes1,$yes2])->where($whereUser)->sum('a.num');
        $res=Db::query("select sum(num) num from xy_lixibao where uid in ($str) and type=1 and is_qu=0 and addtime between $yes1 and $yes2");
        $this->yes_user_lixibao=$res[0]['num'];
       
        
        //下级返佣
        // $this->user_fanyong = Db::name('xy_balance_log a')->join("xy_users b","a.uid=b.id")->field("a.*,b.is_jia")->where("b.is_jia",0)->where('a.type',6)->where('a.status',1)->where($whereUser)->sum('a.num');
        $res=Db::query("select sum(num) num from xy_balance_log where uid in ($str) and type=6 and status=1");
        $this->user_fanyong=$res[0]['num'];
        
        
        // $this->today_user_fanyong = Db::name('xy_balance_log a')->join("xy_users b","a.uid=b.id")->field("a.*,b.is_jia")->where("b.is_jia",0)->where('a.type',6)->where('a.status',1)->where('a.addtime','between',[strtotime(date('Y-m-d')),time()])->where($whereUser)->sum('a.num');
        $res=Db::query("select sum(num) num from xy_balance_log where uid in ($str) and type=6 and status=1 and addtime between $time and $time2");
        $this->today_user_fanyong=$res[0]['num'];
        
         
        // $this->yes_user_fanyong = Db::name('xy_balance_log a')->join("xy_users b","a.uid=b.id")->field("a.*,b.is_jia")->where("b.is_jia",0)->where('a.type',6)->where('a.status',1)->where('a.addtime','between',[$yes1,$yes2])->where($whereUser)->sum('a.num');
        $res=Db::query("select sum(num) num from xy_balance_log where uid in ($str) and type=6 and status=1 and addtime between $yes1 and $yes2");
        $this->yes_user_fanyong=$res[0]['num'];
        
        $datas = Db::table('xy_users')->where('id','in',$str)->select();
        $diyic1 = 0;
        $diyic2 = 0;
        $diyic3 = 0;
        foreach($datas as $v){
            $ss = Db::name('xy_recharge')->where('status',2)->where('uid',$v['id'])->count();
            if($ss == 1){
                $diyic1 = $diyic1 + 1;
                $jr = Db::name('xy_recharge')->where("status = 2 && uid = {$v['id']} && addtime between $yes1 and $yes2")->count();
                 if($jr == 1){
                      $diyic3 =  $diyic3 + 1;
                 }
            }elseif($ss > 1){
                $diyic2 = $diyic2 + 1;
            }
            
            
            
            
        }
        $this->diyic1 = $diyic1;
         $this->diyic2 = $diyic2;
         $this->diyic3 = $diyic3;

        //用户余额
        // $this->user_yue = Db::name('xy_users')->where("is_jia",0)->where($whereUser2)->sum('balance');
        // $res=Db::query("select sum(balance) num from xy_users where id in ($str)");
        
        $res = DB::table('xy_users')->where('is_jia',1)->sum('balance');
        $res = 1;
        $this->user_yue=$res;
        
        
        
        // $this->user_djyue = Db::name('xy_users')->where("is_jia",0)->where($whereUser2)->sum('freeze_balance');
        $res=Db::query("select sum(freeze_balance) num from xy_users where id in ($str)");
        $this->user_djyue=$res[0]['num'];
        
        
        // $this->today_lxbsy = Db::name('xy_balance_log a')->join("xy_users b","a.uid=b.id")->field("a.*,b.is_jia")->where("b.is_jia",0)->where('a.type',23)->where('a.status',1)->where('a.addtime','between',[strtotime(date('Y-m-d')),time()])->where($whereUser)->sum('a.num');
        $res=Db::query("select sum(num) num from xy_balance_log where id in ($str) and type=23 and status=1 and addtime between $time and $time2");
        $this->today_lxbsy=$res[0]['num'];
        
        
        // $this->today_lxbzc = Db::name('xy_balance_log a')->join("xy_users b","a.uid=b.id")->field("a.*,b.is_jia")->where("b.is_jia",0)->where('a.type',22)->where('a.status',1)->where('a.addtime','between',[strtotime(date('Y-m-d')),time()])->where($whereUser)->sum('a.num');
        $res=Db::query("select sum(num) num from xy_balance_log where id in ($str) and type=22 and status=1 and addtime between $time and $time2");
        $this->today_lxbzc=$res[0]['num'];

        
     $this->today_sc_recharge=0;
     $res=Db::table('xy_recharge')->where(['status'=>2])->where('uid','in',$str)->whereTime('endtime','today')->group('uid')->select();
     //var_dump($res);return;
         foreach ($res as $k=>$v){
           $a=Db::table('xy_recharge')->where(['status'=>2,'uid'=>$v['uid']])->whereTime('endtime','today')->count();
           $b=Db::table('xy_recharge')->where(['status'=>2,'uid'=>$v['uid']])->count(); 
           if($a==$b){
               $this->today_sc_recharge+=1;
           }
         } 
        
        
        }else{
            //签到
        $this->sign=Db::table('xy_sign')
        ->alias('l')
        ->join('xy_users u','u.id = l.uid')
        ->where('u.is_jia',0)
        ->count('l.uid');
        $this->today_sign=Db::table('xy_sign')
        ->alias('l')
        ->join('xy_users u','u.id = l.uid')
        ->where('u.is_jia',0)
        ->where("l.time",date("Y-m-d"))->count('l.uid');
        $this->yes_sign=Db::table('xy_sign')
        ->alias('l')
        ->join('xy_users u','u.id = l.uid')
        ->where('u.is_jia',0)
        ->where("l.time",date("Y-m-d",strtotime("-1 day")))->count('l.uid');
        //商品
        $this->goods_num = Db::name('xy_goods_list')->count('id');
        
        $this->today_goods_num = Db::name('xy_goods_list')->where('addtime','between',[strtotime(date('Y-m-d')),time()])->count('id');
        $this->yes_goods_num = Db::name('xy_goods_list')->where('addtime','between',[$yes1,$yes2])->count('id');


        //用户
        $this->users_num = Db::name('xy_users')->where("is_jia",0)->count('id');
        
        $this->today_users_num = Db::name('xy_users')->where("is_jia",0)->where('addtime','between',[strtotime(date('Y-m-d')),time()])->count('id');
        
        
        $this->yes_users_num = Db::name('xy_users')->where("is_jia",0)->where('addtime','between',[$yes1,$yes2])->count('id');


        


        //订单数量
        $this->order_num = Db::name('xy_convey')->count('id');
        
        $this->today_order_num = Db::name('xy_convey')->where('addtime','between',[strtotime(date('Y-m-d')),time()])->count('id');
        
        $this->yes_order_num = Db::name('xy_convey')->where('addtime','between',[$yes1,$yes2])->count('id');
        
        
        
        
        //完成任务人数
        $this->task=Db::name('xy_convey')->alias('l')
        ->join('xy_users u','u.id = l.uid')
        ->where('u.is_jia',0)->group('l.uid')->count('l.uid');
        
        $this->today_task = Db::name('xy_convey')->alias('l')
        ->join('xy_users u','u.id = l.uid')
        ->where('u.is_jia',0)->group('l.uid')->where('l.addtime','between',[strtotime(date('Y-m-d')),time()])->count('l.uid');
        
        $this->yes_task = Db::name('xy_convey')->alias('l')
        ->join('xy_users u','u.id = l.uid')
        ->where('u.is_jia',0)->group('l.uid')->where('l.addtime','between',[$yes1,$yes2])->count('l.uid');
        
        
        //订单总额
        $this->order_sum = Db::name('xy_convey')->sum('num');
        
        $this->today_order_sum = Db::name('xy_convey')->where('addtime','between',[strtotime(date('Y-m-d')),time()])->sum('num');
        $this->yes_order_sum = Db::name('xy_convey')->where('addtime','between',[$yes1,$yes2])->sum('num');
        //充值人数
        $this->recharge_num = Db::name('xy_recharge')->alias('l')
        ->join('xy_users u','u.id = l.uid')
        ->where('u.is_jia',0)->group('l.uid')->where('l.status',2)->count('l.uid');

        $datas = Db::table('xy_users')->where('is_jia',0)->select();
        $diyic1 = 0;
        $diyic2 = 0;
        $diyic3 = 0;
        foreach($datas as $v){
            $ss = Db::name('xy_recharge')->where('status',2)->where('uid',$v['id'])->select();
           
            if(count($ss) == 1){
                $diyic1 = $diyic1 + 1;
                 
                
                 $jr = Db::name('xy_recharge')->where("status = 2 && uid = {$v['id']} && addtime between $yes1 and $yes2")->count();
                 if($jr == 1){
                      $diyic3 =  $diyic3 + 1;
                 }
            }elseif(count($ss) > 1){
                $diyic2 = $diyic2 + 1;
            }
            
            
        }
        $this->diyic1 = $diyic1;
         $this->diyic2 = $diyic2;
         $this->diyic3 = $diyic3;
        
        $this->today_recharge_num = Db::name('xy_recharge')->alias('l')
        ->join('xy_users u','u.id = l.uid')
        ->where('u.is_jia',0)->group('l.uid')->where('l.status',2)->where('l.addtime','between',[strtotime(date('Y-m-d')),time()])->count('l.uid');
        
        $this->yes_recharge_num = Db::name('xy_recharge')->alias('l')
        ->join('xy_users u','u.id = l.uid')
        ->where('u.is_jia',0)->group('l.uid')->where('l.status',2)->where('l.addtime','between',[$yes1,$yes2])->count('l.uid');
        
        
        //充值 
        $this->user_recharge = Db::name('xy_recharge')->alias('l')
        ->join('xy_users u','u.id = l.uid')
        ->where('u.is_jia',0)->where('l.status',2)->sum('l.num');
        $this->today_user_recharge = Db::name('xy_recharge')->alias('l')
        ->join('xy_users u','u.id = l.uid')
        ->where('u.is_jia',0)->where('l.status',2)->where('l.addtime','between',[strtotime(date('Y-m-d')),time()])->sum('l.num');
        $this->yes_user_recharge = Db::name('xy_recharge')->alias('l')
        ->join('xy_users u','u.id = l.uid')
        ->where('u.is_jia',0)->where('l.status',2)->where('l.addtime','between',[$yes1,$yes2])->sum('l.num');
        //提现
        $this->user_deposit = Db::name('xy_deposit')->alias('l')
        ->join('xy_users u','u.id = l.uid')
        ->where('u.is_jia',0)->where('l.send_status',0)->where('l.status',2)->sum('l.num');
        $this->today_user_deposit = Db::name('xy_deposit')->alias('l')
        ->join('xy_users u','u.id = l.uid')
        ->where('u.is_jia',0)->where('l.send_status',0)->where('l.status',2)->where('l.endtime','between',[strtotime(date('Y-m-d')),time()])->sum('l.num');
        $this->yes_user_deposit = Db::name('xy_deposit')->alias('l')
        ->join('xy_users u','u.id = l.uid')
        ->where('u.is_jia',0)->where('l.send_status',0)->where('l.status',2)->where('l.endtime','between',[$yes1,$yes2])->sum('l.num');
        
        
        //抢单佣金
        $this->user_yongjin = Db::name('xy_convey')->alias('l')
        ->join('xy_users u','u.id = l.uid')
        ->where('u.is_jia',0)->where('l.status',1)->sum('l.commission');
        $this->today_user_yongjin = Db::name('xy_convey')->alias('l')
        ->join('xy_users u','u.id = l.uid')
        ->where('u.is_jia',0)->where('l.status',1)->where('l.addtime','between',[strtotime(date('Y-m-d')),time()])->sum('l.commission');
        
        $this->yes_user_yongjin = Db::name('xy_convey')->alias('l')
        ->join('xy_users u','u.id = l.uid')
        ->where('u.is_jia',0)->where('l.status',1)->where('l.addtime','between',[$yes1,$yes2])->sum('l.commission');
        

         //利息宝
        $this->user_lixibao = Db::name('xy_lixibao')
        ->alias('l')
        ->join('xy_users u','u.id = l.uid')
        ->where('u.is_jia',0)
        ->where('l.type',1)
        ->where('l.is_sy',0)
        ->sum('l.num');
        
        $this->today_user_lixibao = Db::name('xy_lixibao')
        ->alias('l')
        ->join('xy_users u','u.id = l.uid')
        ->where('u.is_jia',0)
        ->where('l.addtime','between',[strtotime(date('Y-m-d')),time()])
        ->where('l.type',1)
        ->where('l.is_sy',0)
        ->sum('l.num');
        
        // Db::name('xy_lixibao')->where('type',1)->where('is_sy',0)->where('addtime','between',[strtotime(date('Y-m-d')),time()])->sum('num');
        
        $this->yes_user_lixibao = Db::name('xy_lixibao')
        ->alias('l')
        ->join('xy_users u','u.id = l.uid')
        ->where('u.is_jia',0)
        ->where('l.addtime','between',[$yes1,$yes2])
        ->where('l.type',1)
        ->where('l.is_sy',0)
        ->sum('l.num');
        // Db::name('xy_lixibao')->where('type',1)->where('is_sy',0)->where('addtime','between',[$yes1,$yes2])->sum('num');
       
        
        //下级返佣
        $this->user_fanyong = Db::name('xy_balance_log')->alias('l')
        ->join('xy_users u','u.id = l.uid')
        ->where('u.is_jia',0)->where('l.type',6)->where('l.status',1)->sum('l.num');
        
        $this->today_user_fanyong = Db::name('xy_balance_log')->alias('l')
        ->join('xy_users u','u.id = l.uid')
        ->where('u.is_jia',0)->where('l.type',6)->where('l.status',1)->where('l.addtime','between',[strtotime(date('Y-m-d')),time()])->sum('l.num');
        
         
        $this->yes_user_fanyong = Db::name('xy_balance_log')->alias('l')
        ->join('xy_users u','u.id = l.uid')
        ->where('u.is_jia',0)->where('l.type',6)->where('l.status',1)->where('l.addtime','between',[$yes1,$yes2])->sum('l.num');
        
    
        //用户余额
        $this->user_yue = Db::name('xy_users')->where("is_jia",0)->sum('balance');
        
        $this->user_djyue = Db::name('xy_users')->where("is_jia",0)->sum('freeze_balance');
        
        $this->today_lxbsy = Db::name('xy_balance_log')
        ->alias('b')
        ->join('xy_users u','u.id = b.uid')
        ->where('u.is_jia',0)
        ->where('b.type',23)->where('b.status',1)->where('b.addtime','between',[strtotime(date('Y-m-d')),time()])->sum('b.num');
        $this->today_lxbzc = Db::name('xy_balance_log')
        ->alias('b')
        ->join('xy_users u','u.id = b.uid')
        ->where('u.is_jia',0)
        ->where('b.type',22)->where('b.status',1)->where('b.addtime','between',[strtotime(date('Y-m-d')),time()])->sum('b.num');
        
        
     $this->today_sc_recharge=0;
     $res=Db::table('xy_recharge')->alias('l')
        ->join('xy_users u','u.id = l.uid')
        ->where('u.is_jia',0)->where(['l.status'=>2])->whereTime('l.endtime','today')->field('l.*')->group('l.uid')->select();
         foreach ($res as $k=>$v){
           $a=Db::table('xy_recharge')->where(['status'=>2,'uid'=>$v['uid']])->whereTime('endtime','today')->count();
           $b=Db::table('xy_recharge')->where(['status'=>2,'uid'=>$v['uid']])->count(); 
           if($a==$b){
               $this->today_sc_recharge+=1;
           }
         }

        
        }


        $isVersion = '';
        if (!session('check_update_version')){
            $isVersion = $this->Update(1);
        }
        $list  = [];
        $today = 30;
        for($i = 0; $i < $today; $i++){
            $start = strtotime( date("Y-m-d 00:00:00",strtotime("-{$i} day")) );
            $end   = strtotime( date("Y-m-d 23:59:59",strtotime("-{$i} day")) );
            if($i == 0){
                $list[$i]['user_total'] = Db::table('xy_users')->where('addtime', 'between', [$start, $end])->field('id')->count('id') + 0;
                $order = Db::table('xy_convey')->where('addtime', 'between', [$start, $end])->where('status', 1)->field('SUM(`num`) as money_total,COUNT(id) as order_total')->find();
                $recharge = Db::table('xy_recharge')->where('addtime', 'between', [$start, $end])->where('status', 2)->field('SUM(`num`) as money_total,COUNT(id) as order_total')->find();
                $withdraw = Db::table('xy_deposit')->where('addtime', 'between', [$start, $end])->where('status', 2)->field('SUM(`num`) as money_total,COUNT(id) as order_total')->find();
                $list[$i]['children_real'] = Db::table('xy_balance_log')->where('addtime', 'between', [$start, $end])->where('type', 6)->sum('num') + 0;
                $list[$i]['real'] = Db::table('xy_balance_log')->where('addtime', 'between', [$start, $end])->where('type', 3)->sum('num') + 0;
            }else{
                $list[$i]['user_total'] = Db::table('xy_users')->where('addtime', 'between', [$start, $end])->cache(true)->field('id')->count('id') + 0;
                $order = Db::table('xy_convey')->where('addtime', 'between', [$start, $end])->where('status', 1)->field('SUM(`num`) as money_total,COUNT(id) as order_total')->cache(true)->find();
                $recharge = Db::table('xy_recharge')->where('addtime', 'between', [$start, $end])->where('status', 2)->field('SUM(`num`) as money_total,COUNT(id) as order_total')->cache(true)->find();
                $withdraw = Db::table('xy_deposit')->where('addtime', 'between', [$start, $end])->where('status', 2)->field('SUM(`num`) as money_total,COUNT(id) as order_total')->cache(true)->find();
                 $list[$i]['children_real'] = Db::table('xy_balance_log')->where('addtime', 'between', [$start, $end])->where('type', 6)->cache(true)->sum('num') + 0;
                $list[$i]['real'] = Db::table('xy_balance_log')->where('addtime', 'between', [$start, $end])->where('type', 3)->cache(true)->sum('num') + 0;
            }
            $list[$i]['order_total'] = $order['order_total'] + 0;
            $list[$i]['money_total'] = $order['money_total'] + 0;
            $list[$i]['recharge_total'] = $recharge['order_total'] + 0;
            $list[$i]['recharge_money'] = $recharge['money_total'] + 0;
            $list[$i]['withdraw_total'] = $withdraw['order_total'] + 0;
            $list[$i]['withdraw_money'] = $withdraw['money_total'] + 0;
            $list[$i]['time'] =  date("Y-m-d",strtotime("-{$i} day"));
            
        }
        $this->assign('today', $today);
        $this->assign('list', $list);
        $this->assign('has_version', $isVersion);
        $this->fetch();
    }

    /**
     * 修改密码
     * @param integer $id
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function pass($id)
    {
        $this->applyCsrfToken();
        if (intval($id) !== intval(session('admin_user.id'))) {
            $this->error('只能修改当前用户的密码！');
        }
        if (!NodeService::islogin()) {
            $this->error('需要登录才能操作哦！');
        }
        if ($this->request->isGet()) {
            $this->verify = true;
            $this->_form('SystemUser', 'admin@user/pass', 'id', [], ['id' => $id]);
        } else {
            $data = $this->_input([
                'password'    => $this->request->post('password'),
                'repassword'  => $this->request->post('repassword'),
                'oldpassword' => $this->request->post('oldpassword'),
            ], [
                'oldpassword' => 'require',
                'password'    => 'require|min:4',
                'repassword'  => 'require|confirm:password',
            ], [
                'oldpassword.require' => '旧密码不能为空！',
                'password.require'    => '登录密码不能为空！',
                'password.min'        => '登录密码长度不能少于4位有效字符！',
                'repassword.require'  => '重复密码不能为空！',
                'repassword.confirm'  => '重复密码与登录密码不匹配，请重新输入！',
            ]);
            $user = Db::name('SystemUser')->where(['id' => $id])->find();
            if (md5($data['oldpassword']) !== $user['password']) {
                $this->error('旧密码验证失败，请重新输入！');
            }
            $result = NodeService::checkpwd($data['password']);
            if (empty($result['code'])) $this->error($result['msg']);
            if (Data::save('SystemUser', ['id' => $user['id'], 'password' => md5($data['password'])])) {
                $this->success('密码修改成功，下次请使用新密码登录！', '');
            } else {
                $this->error('密码修改失败，请稍候再试！');
            }
        }
    }

    /**
     * 修改用户资料
     * @param integer $id 会员ID
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function info($id = 0)
    {
        if (!NodeService::islogin()) {
            $this->error('需要登录才能操作哦！');
        }
        $this->applyCsrfToken();
        if (intval($id) === intval(session('admin_user.id'))) {
            $this->_form('SystemUser', 'admin@user/form', 'id', [], ['id' => $id]);
        } else {
            $this->error('只能修改登录用户的资料！');
        }
    }

    /**
     * 清理运行缓存
     * @auth true
     */
    public function clearRuntime()
    {
        if (!NodeService::islogin()) {
            $this->error('需要登录才能操作哦！');
        }
        try {
            Console::call('clear');
            Console::call('xclean:session');
            $this->success('清理运行缓存成功！');
        } catch (HttpResponseException $exception) {
            throw $exception;
        } catch (\Exception $e) {
            $this->error("清理运行缓存失败，{$e->getMessage()}");
        }
    }

    /**
     * 压缩发布系统
     * @auth true
     */
    public function buildOptimize()
    {
        if (!NodeService::islogin()) {
            $this->error('需要登录才能操作哦！');
        }
        try {
            Console::call('optimize:route');
            Console::call('optimize:schema');
            Console::call('optimize:autoload');
            Console::call('optimize:config');
            $this->success('压缩发布成功！');
        } catch (HttpResponseException $exception) {
            throw $exception;
        } catch (\Exception $e) {
            $this->error("压缩发布失败，{$e->getMessage()}");
        }
    }


    public function Update($isreturn)
    {
        $version = config("version");
        $isHtml = $isreturn?0:1;
        $con = '已经是最新版';
        session('check_update_version',1);
        if($isreturn ) return $con;

        echo $con;die;
    }


    public function order_info()
    {
        if (!NodeService::islogin()) {
            $this->error('需要登录才能操作哦！');
        }
        
        $user = session('admin_user');
        $daili_user=session('daili_user');
        //if($user['authorize']== 2 || $user['authorize']== 3  && !empty($user['nodes']) ){
        if($daili_user!=null){
            //获取直属下级
            //$mobile = $user['phone'];
            $mobile = $daili_user['tel'];
            $uid = db('xy_users')->where('tel', $mobile)->value('id');

             $ids1  = db('xy_users')->where('parent_id', $uid)->field('id')->column('id');

             $ids1 ? $ids2  = db('xy_users')->where('parent_id','in', $ids1)->field('id')->column('id') : $ids2 = [];

              $ids2 ? $ids3  = db('xy_users')->where('parent_id','in', $ids2)->field('id')->column('id') : $ids3 = [];

             $ids3 ? $ids4  = db('xy_users')->where('parent_id','in', $ids3)->field('id')->column('id') : $ids4 = [];

             $idsAll = array_merge([$uid],$ids1,$ids2 ,$ids3 ,$ids4);  //所有ids
            $uid = db('xy_users')->where('tel', $mobile)->value('id');
            $ids=Db::name("xy_users")->where("tel",$user['phone'])->value("id");
            $ids1  = db('xy_users')->where('parent_id', $uid)->field('id')->column('id');
            $array=array();
            $array=array_merge($array,$ids1);
            for($i=1;$i<=1000;$i++){
                if(empty($ids1)){
                    break;
                }
                $ids1  = Db::name('xy_users')->where('parent_id',"in",$ids1)->field('id')->column('id');
                if(empty($ids1)){
                    $ids1=array();
                }
                $array=array_merge($array,$ids1);
            }
            $where[] = ['id','in',$array];
            
        //$query = $this->_query('xy_recharge')->alias('xr');
        $recharge=db('xy_recharge')
            //->leftJoin('system_user ad','u.admin_id=ad.id')
            ->where($where)
            ->count();
            
        //$query = $this->_query('xy_deposit')->alias('xd');
        $deposit=db('xy_deposit')
            //->leftJoin('xy_bankinfo bk','bk.id=xd.bk_id')
            //->leftJoin('system_user oo','u.admin_id=oo.id')
            ->where($where)
            ->count();    
        }else{
           $deposit = db('xy_deposit')->where('status',1)->count('id');
           $recharge = db('xy_recharge')->where('status',1)->count('id');
        }

  
        echo json_encode(['deposit'=>$deposit,'recharge'=>$recharge]);

    }

    public function clear()
    {
        $isVersion = $this->Update(0);
    }

}
