<?php

namespace app\index\controller;

use think\Controller;
use think\Request;
use think\Db;

/**
 * 下单控制器
 */
class RotOrder extends Base
{
    /**
     * 首页
     */
    public function index()
    {
        // echo date('Y-m-d H:i:s');
       //Add available balance
        $this->info = Db::name('xy_users')->field('username,tel,level,id,headpic,balance,freeze_balance,lixibao_balance,invite_code,is_new,vpi_balance,show_td')->find(session('user_id'));
        /*
        if($this->info['is_new']==1){
            $this->info['balances']=$this->info['balance']-config("registration")-$this->info['vpi_balance'];
        }else{
            $this->info['balances']=$this->info['balance']-$this->info['vpi_balance'];
        }
        */
        if($this->info['is_new']==1){
            $this->info['balances']=$this->info['balance'];
        }else{
            $this->info['balances']=$this->info['balance'];
        }
        //end 
        
        $where = [
            ['uid','=',session('user_id')],
            ['addtime','between',strtotime(date('Y-m-d')).','.time()],
        ];
        $this->day_deal = Db::name('xy_convey')->where($where)->where('status','in',[1,3,5])->sum('commission');
//        $this->day_l_count = Db::name('xy_convey')->where($where)->where('status',5)->count('num');//交易冻结单数

        $yes1 = strtotime( date("Y-m-d 00:00:00",strtotime("-1 day")) );
        $yes2 = strtotime( date("Y-m-d 23:59:59",strtotime("-1 day")) );
        $this->price = Db::name('xy_users')->where('id',session('user_id'))->sum('balance');

        
        $this->lock_deal = Db::name('xy_users')->where('id',session('user_id'))->sum('freeze_balance');
        // $this->yes_team_num = Db::name('xy_reward_log')->where('uid',session('user_id'))->where('addtime','between',[$yes1,$yes2])->where('status',1)->sum('num');//获取下级返佣数额
        // $this->today_team_num = Db::name('xy_reward_log')->where('uid',session('user_id'))->where('addtime','between',[strtotime('Y-m-d'),time()])->where('status',1)->sum('num');//获取下级返佣数额
 
        //分类
        $type = input('get.type/d',1);
        $this->cate = Db::name('xy_goods_cate')->alias('c')
            ->leftJoin('xy_level u','u.id=c.level_id')
            ->field('c.name,c.cate_info,c.cate_pic,u.name as levelname,u.pic,u.level,u.bili,u.order_num')
            ->find($type);;
        $this->beizhu = Db::name('xy_index_msg')->where('id',9)->value('content');;


        // $this->yes_user_yongjin = Db::name('xy_convey')->where('uid',session('user_id'))->where('status',1)->where('addtime','between',[$yes1,$yes2])->sum('commission');
        // $this->user_yongjin = Db::name('xy_convey')->where('uid',session('user_id'))->where('status',1)->sum('commission');


        $member_level = Db::name('xy_level')->order('level asc')->select();;
        $order_num = $member_level[0]['order_num'];
        $level_name='Free';
        $level_nums='0.00';
        $uinfo = Db::name('xy_users')->where('id', session('user_id'))->find();
        $ulevel = Db::name('xy_level')->where('level', $uinfo['level'])->find();
        if (!empty($uinfo['level'])){
            $order_num = Db::name('xy_level')->where('level',$uinfo['level'])->value('order_num');
            
            $level_name = Db::name('xy_level')->where('level',$uinfo['level'])->value('name');
            $level_nums = Db::name('xy_level')->where('level',$uinfo['level'])->value('num');
        }
        // 修改订单数据
        if($uinfo['old_num'] + 0 > 0){
            // $order_num = $order_num - $uinfo['old_num'];
        }
        
        //判断等级升级时间
        $leveltime=Db::name('xy_upgrade')->where('uid',session('user_id'))->where("status",2)->order('id desc')->find();
        
        // $day_d_count = Db::name('xy_convey')->whereTime('addtime','today')->where(['uid'=>session('user_id'),'level'=>$uinfo['level']])->where('status','in',[0,1,3,5])->count('id');
        // $this->day_d_count=$day_d_count;
        $this->day_d_count=$uinfo['deal_count'];
        $id = session('user_id');
        $list = Db::name('xy_member_address')->where('uid',$id)->find();
        $this->assign('list',$list);
        $this->assign('ulevel',$ulevel);
        $this->level_name = $level_name;
        $this->order_num = $order_num;
        $this->level_nums = $level_nums;
        return $this->fetch();
    }
  /**
    *提交抢单
    */
    public function submit_order()
    {
        
       
        $tmp = $this->check_deal();
        
        if($tmp) return json($tmp);
        
        $res = check_time(9,22);
        //if($res) return json(['code'=>1,'info'=>'禁止在9:00~22:00以外的时间段执行当前操作!']);
        
       
        
        $res = check_time(config('order_time_1'),config('order_time_2'));
        
        $str = config('order_time_1').":00  - ".config('order_time_2').":00";
        
        if($res) return json(['code'=>-1,'info'=>lang('good_xin').$str]);
    
        $uid = session('user_id');
        //检查交易状态
        // $sleep = mt_rand(config('min_time'),config('max_time'));
        $res = Db::name('xy_users')->where('id',$uid)->update(['deal_status'=>2]);//将账户状态改为等待交易
        if($res === false) return json(['code'=>1,'info'=>lang('qd_error')]);
        // session_write_close();//解决sleep造成的进程阻塞问题
        // sleep($sleep);
        //
        //判断等级升级时间
        $leveltime=Db::name('xy_upgrade')->where('uid',session('user_id'))->where("status",2)->order('id desc')->find();
        
        
        //判断今天刷单次数是否超过
        $user=Db::name("xy_users")->where("id",session('user_id'))->find();
        $where = [
            ['uid','=',session('user_id')],
           // ['addtime','between',$leveltime['addtime'].','.time()],
        ];
        $day_d_count = Db::name('xy_convey')->where(['level'=>$user['level']])->where($where)->where('status','in',[0,1,3,5])->whereTime('addtime','today')->count('id');
        
        $level=Db::name("xy_level")->where("level",$user['level'])->find(); 
        //判断当前等级总刷单次数是否超过
        $where = [
            ['uid','=',session('user_id')],
            ['addtime','between',$leveltime['addtime'].','.time()], 
        ];
        $level_all = Db::name('xy_convey')->where($where)->whereTime('addtime','today')->where('status','in',[0,1,3,5])->count('id');
        $user_level=$user['level']+1;
        if($level_all>=$level['order_max_num']){
            return json(['code'=>-4,'info'=>"Sorry, VIP$user[level] members capture more orders, you can upgrade VIP$user_level to continue to capture orders.",'url'=>'/index/index/home']);
        }
        if($user['deal_count']>=$level['order_num']){
            return json(['code'=>1,'info'=>lang("sdnum")]);
        }
        
 
        //卡单
        // if($user['ka_sum']!=0 && ($day_d_count+1)>=$user['ka_sum']){
        if($user['ka_sum']!=0 && ($user['deal_count']+1)>=$user['ka_sum']){
           if($user['ka_good']){
                $res = model('admin/Convey')->create_order($uid,1,$user['ka_good']);
                if($res['code']==0){
                    //清空卡单商品
                     Db::name('xy_users')->where('id',$user['id'])->update(['ka_good'=>0]);
                }
                return json($res);
            }
            if($user['lian_good']){
                $res = model('admin/Convey')->create_order($uid,1,$user['lian_good']);
                if($res['code']==0){
                    //清空卡单商品
                     Db::name('xy_users')->where('id',$user['id'])->update(['lian_good'=>0]);
                }
                return json($res);
            }
            if($user['lian_good3']){
                $res = model('admin/Convey')->create_order($uid,1,$user['lian_good3']);
                if($res['code']==0){
                    //清空卡单商品
                     Db::name('xy_users')->where('id',$user['id'])->update(['lian_good3'=>0]);
                }
                return json($res);
            }
            if($user['lian_good4']){
                $res = model('admin/Convey')->create_order($uid,1,$user['lian_good4']);
                if($res['code']==0){
                    //清空卡单商品
                     Db::name('xy_users')->where('id',$user['id'])->update(['lian_good4'=>0]);
                }
                return json($res);
            }
            if(!$user['ka_good'] && !$user['lian_good'] && !$user['lian_good3'] && !$user['lian_good4']){
                Db::name('xy_users')->where('id',$user['id'])->update(['ka_sum'=>0]);
            }
            
        }
        
       /* $kd = Db::name('xy_convey')->where($where)->where('status','in',[0,1,3,5])->whereTime('addtime','today')->order('addtime desc')->find();
        $recharge = db('xy_recharge')->where(['status'=>2,'uid'=>$uid])->whereTime('addtime','today')->order('addtime desc')->find(); 
        $ka_sum = explode(',', $user['ka_sum']);
        //echo $day_d_count.'|'.$ka_sum[0];return;
        if($day_d_count==$ka_sum[0]  && $ka_sum[0]!=0){
            if(!$recharge || $recharge['addtime']<$kd['addtime']){
                $res = model('admin/Convey')->create_order($uid,1,$user['ka_good']);
                return json($res);
        //      return json(['code'=>1,'info'=>"Please continue to purchase the order after recharging $user[ka_money] USDT"]);
            }
        }
        if($day_d_count==$ka_sum[1]  && $ka_sum[1]!=0){
            if(!$recharge || $recharge['addtime']<$kd['addtime']){
                $res = model('admin/Convey')->create_order($uid,1,$user['ka_good']);
                return json($res);
           //   return json(['code'=>1,'info'=>"Please continue to purchase the order after recharging $user[ka_money] USDT"]);
            }
        }
        if($day_d_count==$ka_sum[2]  && $ka_sum[2]!=0){
            if(!$recharge || $recharge['addtime']<$kd['addtime']){
                $res = model('admin/Convey')->create_order($uid,1,$user['ka_good']);
                return json($res); 
         //      return json(['code'=>1,'info'=>"Please continue to purchase the order after recharging $user[ka_money] USDT"]);
            }
        }*/
        
        //$cid = input('post.cid/d',1);
        // $cid=$user['level'];
        // $cid = Db::name('xy_goods_cate')->where('level_id','=',$cid)->value('id');
        //echo $cid;return;
        // $count = Db::name('xy_goods_list')->where('cid','=',$cid)->count();
        $count = Db::name('xy_goods_list')->count();
        $cid = 1;
        if($count < 1) return json(['code'=>1,'info'=>lang('qd_error_kucun')]);
        //  var_dump( model('admin/Convey')->create_order($uid,$cid));exit;
        $res = model('admin/Convey')->create_order($uid,$cid);
     
        return json($res);
    }

    /**
     * 停止抢单
     */
    public function stop_submit_order()
    {
        $uid = session('user_id');
        $res = Db::name('xy_users')->where('id',$uid)->where('deal_status',2)->update(['deal_status'=>1]);
        if($res){
            return json(['code'=>0,'info'=>'操作成功!']);
        }else{
            return json(['code'=>1,'info'=>'操作失败!']);
        }
    }

}