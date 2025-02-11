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
 * 应用入口
 * Class Index
 * @package app\index\controller
 */
class Index extends Base
{
    /**
     * 入口跳转链接
     */
     public function kefu(){
         return $this->fetch();
     }
    public function index()
    {
        $this->redirect('home');
    }
      public function event()
    {
      return $this->fetch();
    }
    public function dds()
    {
        echo '<pre>';
        // $uid = 48466;
        // $uinfo = DB::table('xy_users')->find($uid);
        // $lixibaos = Db::name('xy_lixibao')->where(['is_qu'=>0,'uid'=>$uid])->sum('num');
        // print_r($lixibaos);
        
        $t = [];
        $udata = DB::table('xy_users')->field('tel,lixibao_balance,id')->select();
        
        foreach ($udata as &$v){
            $v['lx'] = Db::name('xy_lixibao')->where(['is_qu'=>0,'uid'=>$v['id']])->sum('num');
            if($v['lx'] != $v['lixibao_balance']){
                $t[] = $v;
                // $v['lixibao_balance'] = $v['lx'];
                // DB::table('xy_users')->update($v);
            }
        }
        print_r($t);
        exit;
         if($lixibaos){
             foreach ($lixibaos as $k=>$v){
                
                 $lixibao = Db::name('xy_lixibao')->find($v['id']);

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
    public function level()
    {   $uid = session('user_id');
        $this->level_list = Db::table('xy_level')->select();
        $this->info = db('xy_users')->find($uid);
        return $this->fetch();
    }
    public function home()
    {   
        $this->info = Db::name('xy_index_msg')->field('content')->select();
        $this->balance = Db::name('xy_users')->where('id',session('user_id'))->sum('balance');
        $this->banner = Db::name('xy_banner')->select();
        //if($this->banner) $this->banner = explode('|',$this->banner);
        $this->notice = db('xy_index_msg')->where('id',1)->value('content');
        $this->hezuo = db('xy_index_msg')->where('id',4)->value('content');
        $this->jianjie = db('xy_index_msg')->where('id',2)->value('content');
        $this->guize = db('xy_index_msg')->where('id',3)->value('content');
        $this->gundong = db('xy_index_msg')->where('id',8)->value('content');
        $this->tanchunag = db('xy_index_msg')->where('id',11)->value('content');
        
        $this->level_list = Db::table('xy_level')->select();
   
        $dev = new \org\Mobile();
        $t = $dev->isMobile();
        if (!$t) {
            if (config('app_only')) {
                header('Location:/app');
            }
        }
        $list = db('xy_convey')
            ->alias('xc')
            ->leftJoin('xy_users u','u.id=xc.uid')
            ->field('xc.*,u.username,u.tel')
            ->where('xc.status',1)
            ->limit(15)
            ->order('xc.id desc')
           ->select();
        
        $list2 = [
            ['username' => 'michl', 'num' =>  23.98, 'addtime' =>  time() - rand(1000,999999)],
            ['username' => 'rs2000', 'num' =>  103.02, 'addtime' =>  time() - rand(1000,999999)],
            ['username' => 'todas', 'num' =>  3.00, 'addtime' =>  time() - rand(1000,999999)],
            ['username' => 'earnings', 'num' =>  9.5, 'addtime' =>  time() - rand(1000,999999)],
            ['username' => 'cd2100', 'num' =>  19.05, 'addtime' =>  time() - rand(1000,999999)],
        ];
        if (count($list) < 5 ) {
            $list = array_merge($list,$list2);
        }
        $today_income_uids=[];
        if ($list) {
            foreach ($list as &$item) {
                if(empty($item['commission'])){
                    $item['commission']=rand(1,10);
                }
                $item['tel'] = $item['username'];
                $item['num'] ='获得返佣'.$item['num'] ;
                $item['addtime'] = date('m-d H:i', $item['addtime']); 
                $item['today_income']=$item['commission']*rand(1,10)+date('m')+date('d')*7+date('Y');
            }
        }   
        $this->list = $list;

        $this->assign('pic','/upload/qrcode/user/'.(session('user_id')%20).'/'.session('user_id').'-1.png');
        $this->cate = db('xy_goods_cate')->alias('c')
            ->leftJoin('xy_level u','u.id=c.level_id')
            ->field('c.name,c.min,c.id,c.bili,c.cate_info,c.cate_pic,u.num_min,u.name as levelname,u.pic,u.level,u.bili as bili_level')
            ->order('c.id asc')->select();


        $uid = session('user_id');
        //一天的
        $this->lixibao = db('xy_lixibao_list')->order('id asc')->find();
        
        //
        $yes1 = strtotime( date("Y-m-d 00:00:00",strtotime("-1 day")) );
        $yes2 = strtotime( date("Y-m-d 23:59:59",strtotime("-1 day")) );
        $this->tod_user_yongjin = db('xy_convey')->where('uid',$uid)->where('status',1)->where('addtime','between',[strtotime('Y-m-d 00:00:00'),time()])->sum('commission');
        $this->yes_user_yongjin = db('xy_convey')->where('uid',$uid)->where('status',1)->where('addtime','between',[$yes1,$yes2])->sum('commission');
        $this->user_yongjin = db('xy_convey')->where('uid',$uid)->where('status',1)->sum('commission');
        
        $this->lixi_count=Db::table('xy_lixibao')->where('uid',session('user_id'))->sum('yuji_num');
        $this->lixi_count_today=Db::table('xy_lixibao')->where('uid',session('user_id'))->where('addtime','between',[strtotime('Y-m-d 00:00:00'),time()])->sum('yuji_num');
        $this->today_income=$this->tod_user_yongjin+$this->lixi_count_today;
        $this->banner=Db::name("xy_banner")->select();

        $this->info = db('xy_users')->find($uid);
        
        // $list = db('xy_users')->field('username,tel,level,id,headpic,balance,freeze_balance,lixibao_balance,invite_code,is_new,vpi_balance,show_td')->find(session('user_id'));
        $levelnum=0;
            for($i=0;$i<=$this->info['level'];$i++){
                $ulevels = Db::name('xy_level')->where('level', $i)->find();
                $levelnum=$levelnum+$ulevels['num'];
            }
        if($this->info['is_new']==1){
            $this->info['balances']=$this->info['balance']-config("registration")-$levelnum;
        }else{
            $this->info['balances']=$this->info['balance']-$levelnum;
        }
        $where=[
            'status'=>1,
            'uid'=>$uid
        ];
        $count=Db::name('xy_convey')->where($where)->count();
        $today=strtotime(date("Y-m-d"));
        // var_dump($today);exit;
       $where = [
            ['uid','=',session('user_id')],
            ['addtime','between',strtotime(date('Y-m-d')).','.time()],
        ];
         $leveltime=Db::name('xy_upgrade')->where('uid',$uid)->where("status",2)->order('id desc')->find();
        //今日刷单数
         $user = Db::name('xy_users')->where('id',session('user_id'))->find();

        $todaycount = Db::name('xy_convey')->whereTime('addtime','today')->where(['uid'=>session('user_id'),'level'=>$user['level']])->where('status','in',[0,1,3,5])->count('id');
        
        // var_dump($todaycount);exit;
        $where = [
            ['uid','=',session('user_id')],
            ['addtime','between',strtotime(date('Y-m-d')).','.time()],
            ['status','=',1]
           
        ];
        //今日收益
        $todaymoney = Db::name('xy_balance_log')->where($where)->where('type!=21&&type!=22&&type!=26&&type!=1&&type!=7')->sum('num');
        
        //最大刷单数
        $shangxian=Db::name('xy_level')->where(['level'=>$this->info['level']])->find();
        
      
       
       $home_foot=Db::name('xy_home_foot')->select();

       
        // var_dump($this->info['level']);exit;
        // echo "<pre>";
        $this->assign('shangxian',$shangxian['order_num']);
        $this->assign('count',$count);
        $this->assign('todaycount',$todaycount);
        $this->assign('todaymoney',$todaymoney);
        $this->assign('keyong',$this->info['balances']);
        $this->assign('freeze_balance',$this->info['freeze_balance']);
        $this->assign('home_foot',$home_foot);
        $content = Db::name('xy_index_msg')->where('id', 8)->value('content');
        $this->assign('margin_queue', strip_tags($content));
        // var_dump($this->info['balances']);exit;
        return $this->fetch();
    }
    
    
    
    
    //获取首页图文
    public function get_msg()
    {
        $type = input('post.type/d',1);
        $data = Db::name('xy_index_msg')->find($type);
        if($data)
            return json(['code'=>0,'info'=>'请求成功','data'=>$data]);
        else
            return json(['code'=>1,'info'=>'暂无数据']);
    }




    //获取首页图文
    public function getTongji()
    {
        $type = input('post.type/d',1);
        $data = array();

        $data['user'] = db('xy_users')->where('status',1)->where('addtime','between',[strtotime(date('Y-m-d'))-24*3600,time()])->count('id');
        $data['goods'] = db('xy_goods_list')->count('id');;
        $data['price'] = db('xy_convey')->where('status',1)->where('endtime','between',[strtotime(date('Y-m-d'))-24*3600,strtotime(date('Y-m-d'))])->sum('num');
        $user_order = db('xy_convey')->where('status',1)->where('addtime','between',[strtotime(date('Y-m-d')),time()])->field('uid')->Distinct(true)->select();
        $data['num'] = count($user_order);

        if($data){
            return json(['code'=>0,'info'=>'请求成功','data'=>$data]);
        } else {
            return json(['code' => 1, 'info' => '暂无数据']);
        }
    }
    
    //升级vpi
    public function upgrade()
    {
        if(request()->isPost()){
            $uid=session('user_id');
            $level=input("post.level");
            if(empty($level)){
                return json(["code"=>1,"info"=>lang("cscw")]);
            }
            $rows=Db::name("xy_users")->where("id",$uid)->find();
            $upgrade=Db::name("xy_upgrade")->where(["status"=>1,"uid"=>$uid])->count();
            
            $lishi=Db::name("xy_upgrade")->where(["status"=>2,"uid"=>$uid])->order('id desc')->find();
            if($lishi['level']>=$level){
                return json(["code"=>1,"info"=>"Upgrading VIp$level requires inviting subordinates"]);
            }
            
            //if($upgrade>0){
                //return json(["code"=>1,"info"=>lang('zzshehe')]);
            //}
            
            //你可以只买一个VIP比你目前的VIP更高一个级别
            //echo $rows['level'];return;
            if($rows['level']==0){
                if($level!=$rows['level']+1){
                    return json(["code"=>1,"info"=>"You can only buy a VIP that is one level higher than your current VIP"]);
                }
            }else{
                if($level!=$rows['level']+1){
                    return json(["code"=>1,"info"=>"You can only buy a VIP that is one level higher than your current VIP"]);
                }
            }
            
            $levelFind=Db::name("xy_level")->where("level",$level)->find();
            $levelFinds=Db::name("xy_level")->where("level",$level-1)->find();
            $levelFindss=Db::name("xy_level")->where("level",$level-2)->find();
            //必须刷完当前等级单数,v0排除
            
            if($rows['level']!=0){
                $leveltime=Db::name('xy_upgrade')->where('uid',$uid)->where("status",2)->order('id desc')->find();
                $ordernum=Db::name('xy_convey')->where('addtime','between',$leveltime['addtime'].','.time())->where(['uid'=>session('user_id')])->where('status','in',[0,1,3,5])->count('id');
                if($ordernum<$levelFinds['order_num']){
                    $countorder=$levelFinds['order_num']-$ordernum;
                    return json(["code"=>1,"info"=>"There are $countorder orders for your VIP($levelFinds[level]), you can only upgrade after completion"]);
                }
            }
            
            if($rows['is_new']==1){
                $rows['balance']-=config("registration");
            }
            $levelnum=0;
            for($i=0;$i<=$rows['level'];$i++){
                $ulevels = Db::name('xy_level')->where('level', $i)->find();
                $levelnum=$levelnum+$ulevels['num'];
            }
            // var_dump($levelnum);exit;
            //$rows['balance']-=$levelnum;//可用余额
            //升级的钱
            $money=$levelFind['num'];
            
            if($rows["balance"]<$money){
                return json(["code"=>2,"info"=>lang('money_not'),"url"=>"/index/ctrl/recharge"]);
            }
            Db::name("xy_users")->where("id",$uid)->setInc("vpi_balance",$money);
            $id=Db::name("xy_upgrade")->insertGetId([
                "uid"=>$uid,
                "level"=>$level,
                "money"=>$money,
                "addtime"=>time(),
                "status"=>2
            ]);
            $res2 = Db::name('xy_balance_log')->insert([
                'uid'           => $uid,
                'oid'           => $id,
                'num'           => $money,
                'type'          => 24,
                'status'        => 2,
                'addtime'       => time()
            ]);
            //直接升级
            // Db::name("xy_upgrade")->where("id",$id)->setField("status",2);
            
            
            //自动升级vip
            $level=Db::name('xy_level')->where(['level'=>$level])->find();
            $balance=Db::table('xy_users')->where("id",$uid)->value("balance");
            $lixibao=Db::name('xy_lixibao')->where(['uid'=>$uid])->sum('num');
            $user=Db::name('xy_users')->where(['id'=>$uid])->find();
    
            $pid_count=Db::table('xy_users')->where(["parent_id"=>$uid])->where('balance','>=',20)->count();
       
            //foreach ($level as $k=>$v){
                
                  if($balance+$lixibao>=$level['num_min'] && $pid_count>=$level['auto_vip_xu_num']){
                      if($level['level']>$user['level']){
                        Db::table('xy_users')->where("id",$uid)->update(["level"=>$level['level']]);
                      }
                  }else if($balance+$lixibao<$level['num_min'] || $pid_count<$level['auto_vip_xu_num']){
                      return json(["code"=>1,"info"=>"Por favor, invite a los afiliados o recargue el saldo"]);
                  }
           // }
            
             //Db::name("xy_users")->where("id",$uid)->setField("level",$level);
            
            return json(["code"=>0,"info"=>lang('tjcgqdd')]);
        }
    }

    function getDanmu()
    {
        $barrages=    //弹幕内容
            array(
                array(
                    'info'   => '用户173***4985开通会员成功',
                    'href'   => '',

                ),
                array(
                    'info'   => '用户136***1524开通会员成功',
                    'href'   => '',
                    'color'  =>  '#ff6600'

                ),
                array(
                    'info'   => '用户139***7878开通会员成功',
                    'href'   => '',
                    'bottom' => 450 ,
                ),
                array(
                    'info'   => '用户159***7888开通会员成功',
                    'href'   => '',
                    'close'  =>false,

                ),array(
                'info'   => '用户151***7799开通会员成功',
                'href'   => '',

                )
            );

        echo   json_encode($barrages);
    }
    
    
    public function ss(){
        Db::name("xy_users")->where("id >0 ")->update(["ka_money_lv"=>'0,0,0']);
    }

}
