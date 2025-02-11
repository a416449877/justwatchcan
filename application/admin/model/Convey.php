<?php

namespace app\admin\model;

use think\Model;
use think\Db;

class Convey extends Model
{

    protected $table = 'xy_convey';

    /**
     * 创建订单
     *
     * @param int $uid
     * @return array
     */
    public function create_order($uid,$cid=1,$goods_id=0)
    {   
       
      
        $add_id=99;
        $uinfo = Db::name('xy_users')->find($uid);
        
        if($uinfo['deal_status']!=2) return ['code'=>1,'info'=>lang('cscw')];
        
        $ulevel = Db::name('xy_level')->where('level',$uinfo['level'])->find();
         
        //echo config('deal_min_num');return;
        $min = $uinfo['balance']*config('deal_min_num')/100;
        $max = $uinfo['balance']*config('deal_max_num')/100;
        
        //$min = $ulevel['order_max_money'];
        //$max = $ulevel['order_max_money'];
  
        if($goods_id){
           $goods = Db::name('xy_goods_list')
                // ->where('goods_price','between',[$num/10,$num])
                ->where('id','=',$goods_id)
                ->find();
            $num = mt_rand($min,$max);//随机交易额
             $count = $num/$goods['goods_price'];
             $count=sprintf("%.2f",$count);
           $goods= ['count'=>$count,'id'=>$goods['id'],'num'=>$goods['goods_price'],'cid'=>$goods['cid']];
        }else{
            $goods = $this->rand_order($min,$max,$cid);
            if(empty($goods['id'])) return $goods;
           
        }
        
   

        $level = $uinfo['level']; 
        !$uinfo['level'] ? $level = 0 : '';
        //vip0三天试用过期
        /*
        if($uinfo['level']==0 && $uinfo['is_new']==2){
            return ['code'=>1,'info'=>"The three-day free trial is over, upgrade VIP1 and continue to grab orders."];
        }
        */
        //余额最低500
        //if($uinfo['balance']<config('deal_min_balance')) return ['code'=>1,'info'=>lang('yedy').config('deal_min_balance').','.lang('wfjy')];
        
        //余额最vip限制
        if($uinfo['balance']<$level['order_num']) return ['code'=>1,'info'=>lang('yedy').$level['order_num'].','.lang('wfjy')];
        
        
        //最大抢多大的单 

        if($goods['num']>$ulevel['order_max_money'] && !$goods_id){
            return ['code'=>1,'info'=>"VIP$level can only grab orders within $ulevel[order_max_money],Please re-capture the order."];
        }
        
           
        $xy_lixibao_list=Db::name('xy_lixibao_list')->where(['order_state'=>1])->select();
        $lixibao_money=0;
        foreach ($xy_lixibao_list as $k=>$v){
            $lixibao_money+=Db::name('xy_lixibao')->where(['uid'=>$uid,'sid'=>$v['id']])->sum('num');
        }
        
       // echo $uinfo['balance']+$lixibao_money."|".$ulevel['num_min'];return;
        if (!$goods_id &&($uinfo['balance']+$lixibao_money < $ulevel['num_min'])) {
            // return ['code'=>-1,'info'=>lang('zhyebz')];
            return ['code'=>-1,'info'=>'Account balance is less than '.$ulevel['num_min'].', please contact customer service to recharge'];
        }
        
        $id = getSn('UB');
        Db::startTrans();
       
        //通过商品id查找 佣金比例
         //var_dump($goods);exit;
   //     $cate = Db::name('xy_goods_cate')->find($goods['cid']);
     

     //   if(!$goods_id && ($goods['num'] > $uinfo['balance']+$lixibao_money)) return ['code'=>1,'info'=>lang('zhyebz')];
        
        
        //卡单
     /*    $where = [
            ['uid','=',session('user_id')],
           // ['addtime','between',$leveltime['addtime'].','.time()],
        ];
        $day_d_count = Db::name('xy_convey')->where($where)->where('status','in',[0,1,3,5])->whereTime('addtime','today')->count('id');
        $kd = Db::name('xy_convey')->where(['uid'=>$uid])->where('status','in',[0,1,3,5])->whereTime('addtime','today')->order('addtime desc')->find();
        $recharge = db('xy_recharge')->where(['status'=>2,'uid'=>$uid])->whereTime('addtime','today')->order('addtime desc')->find(); 
        $ka_sum = explode(',', $uinfo['ka_sum']);
        $ka_money_lv = explode(',', $uinfo['ka_money_lv']);
        
        

        if($day_d_count==$ka_sum[0]  && $ka_sum[0]!=0){
            if($recharge['addtime']>$kd['addtime']){
              $commission=$goods['num']*$ka_money_lv[0]/100;
            }
        }
        if($day_d_count==$ka_sum[1]  && $ka_sum[1]!=0){
            if($recharge['addtime']>$kd['addtime']){
              $commission=$goods['num']*$ka_money_lv[0]/100;
            }
        }
        if($day_d_count==$ka_sum[2]  && $ka_sum[2]!=0){
            if($recharge['addtime']>$kd['addtime']){
              $commission=$goods['num']*$ka_money_lv[0]/100;
            }
        }*/
        
      /*  if(!isset($commission)){*/
            $commission=$goods['num']*$ulevel['bili'];
      /*  }*/
        
            //echo $commission;return;
             $order_type=1;
        if($goods_id){
            $order_type=2;
        }
        $ka_multipue =$uinfo['lian_yongjin'];
        $ka_multipue = $ka_multipue == 0 ? 1 : $ka_multipue;
        $multiple = $goods_id ? $ka_multipue : 1;
        $res1 = Db::name($this->table) 
                ->insert([
                    'id'            => $id,
                    'uid'           => $uid,
                    'num'           => $goods['num'],
                    'addtime'       => time(),
                    'endtime'       => time()+config('deal_timeout'),
                    'add_id'        => $add_id,
                    'goods_id'      => $goods['id'],
                    'goods_count'   => $goods['count'],
                    //'commission'    => $goods['num']*config('vip_1_commission'),
                    //'commission'    => $goods['num']*$cate['bili'],  //交易佣金按照分类
                    //'commission'    => $goods['num']*$ulevel['bili'],  //交易佣金按照会员等级
                    'commission'    =>$commission * $multiple,
                    'level'         =>$uinfo['level'],
                    'type'=>$order_type,
                    'multiple' => $multiple,
                ]);
   
        if($uinfo['balance']<$goods['num']){  //如果账户余额小于商品价格  直接负数
             $b=$uinfo['balance']-$goods['num'];
                $update=['deal_status'=>3,'deal_time'=>strtotime(date('Y-m-d')),'deal_count'=>Db::raw('deal_count+1'),'balance'=>$b,'freeze_balance'=>$goods['num']];
        }else{
                 $update=['deal_status'=>3,'deal_time'=>strtotime(date('Y-m-d')),'deal_count'=>Db::raw('deal_count+1')];
         }
       /*  if($goods_id){
            $update['ka_good']=0;
            $update['ka_sum']=0;
        }*/
            
         $res = Db::name('xy_users')->where('id',$uid)->update($update);//将账户状态改为交易中
 
        if($res && $res1){
            Db::commit();
            return ['code'=>0,'info'=>lang('qd_ok'),'oid'=>$id];
        }else{
            Db::rollback();
            return ['code'=>1,'info'=>lang('qd_sb')];
        }
    }

    /**
     * 随机生成订单
     */
    private function rand_order($min,$max,$cid)
    {    
        
        $num = mt_rand($min,$max);//随机交易额

        $goods = Db::name('xy_goods_list')
            ->orderRaw('rand()')
             ->where('goods_price','between',[$min,$max])
            // ->where('cid','=',$cid)
            ->find();

        if(!$goods){
            $goods = Db::name('xy_goods_list')
            ->orderRaw('rand()')
             ->where('goods_price','>',$min)
            // ->where('cid','=',$cid)
            ->find();
        }
               
    
        if (!$goods) {
          
            return ['code'=>-1,'info'=>lang('qdsbkcbz')];
        }
        
        
        $count = $num/$goods['goods_price'];
        //echo $count;return;
        $count=sprintf("%.2f",$count);
        //echo $min;return;
        //if($count*$goods['goods_price']<$min||$count*$goods['goods_price']>$max){
           // self::rand_order($min,$max,$cid);
        //}
        //return ['count'=>$count,'id'=>$goods['id'],'num'=>$count*$goods['goods_price'],'cid'=>$goods['cid']];
        return ['count'=>$count,'id'=>$goods['id'],'num'=>$goods['goods_price'],'cid'=>$goods['cid']];
    }

    /**
     * 处理订单
     *
     * @param string $oid      订单号
     * @param int    $status   操作      1会员确认付款 2会员取消订单 3后台强制付款 4后台强制取消
     * @param int    $uid      用户ID    传参则进行用户判断
     * @param int    $uid      收货地址
     * @return array
     */
    public function do_order($oid,$status,$uid='',$add_id='')
    {
        $info = Db::name('xy_convey')->find($oid);
         $userinfo= Db::name('xy_users')->where('id',$info['uid'])->find();
        if(!$info) return ['code'=>1,'info'=>'订单号不存在'];
        if($uid && $info['uid']!=$uid) return ['code'=>1,'info'=>'参数错误，请确认订单号!'];
        if(!in_array($info['status'],[0,5])) return ['code'=>1,'info'=>lang('ddycl')];

        //TODO 判断余额是否足够
        $userPrice = Db::name('xy_users')->where('id',$info['uid'])->value('balance');
        
        $xy_lixibao_list=Db::name('xy_lixibao_list')->where(['order_state'=>1])->select();
        $lixibao_money=0;
        foreach ($xy_lixibao_list as $k=>$v){
            $lixibao_money+=Db::name('xy_lixibao')->where(['uid'=>$info['uid'],'sid'=>$v['id']])->sum('num');
        }
        
       // if ($userPrice < $info['num']) return ['code'=>1,'info'=>lang('zhyebz')];
         if ($userPrice < 0) return ['code'=>1,'info'=>lang('zhyebz')];
        //$tmp = ['endtime'=>time(),'status'=>$status];
        $tmp = ['endtime'=>time()+config('deal_feedze'),'status'=>5];
        $add_id?$tmp['add_id']=$add_id:'';
        Db::startTrans();
        $res = Db::name('xy_convey')->where('id',$oid)->update($tmp);
        if(in_array($status,[1,3])){
            //返回已经冻结的款项
            //确认付款
            try {
               
                if($userinfo['freeze_balance']>0){
                    Db::name('xy_users')->where('id',$info['uid'])->setInc('balance',$userinfo['freeze_balance']);
                    Db::name('xy_users')->where('id',$info['uid'])->setDec('freeze_balance',$userinfo['freeze_balance']);
                    
                }
                 
                $res1 = Db::name('xy_users')
                        ->where('id', $info['uid'])
                       // ->dec('balance',$info['num'])
                        ->inc('freeze_balance',$info['num']+$info['commission']) //冻结商品金额 + 佣金
                        ->update(['deal_status' => 1,'status'=>1]);
            } catch (\Throwable $th) {
                Db::rollback();
                return ['code'=>1,'info'=>lang('zhyebz')];
            }
            $res2 = Db::name('xy_balance_log')->insert([
                'uid'           => $info['uid'],
                'oid'           => $oid,
                'num'           => $info['num'],
                'type'          => 2,
                'status'        => 2,
                'addtime'       => time()
            ]);
            if($status==3) Db::name('xy_message')->insert(['uid'=>$info['uid'],'type'=>2,'title'=>'System Notification','content'=>'The transaction order'.$oid.'has been forcibly paid by the system, if you have any questions, please contact customer service','addtime'=>time()]);
            //系统通知
            //
            if($res && $res1 && $res2){
                Db::commit();
                $c_status = Db::name('xy_convey')->where('id',$oid)->value('c_status');
                //判断是否已返还佣金
                if($c_status===0) $this->deal_reward($info['uid'],$oid,$info['num'],$info['commission']);
                //查看订单类型 判断是否有连单
                 /*   if($info['type']==2){
                        if($userinfo['lian_good']){
                            Db::name('xy_users')->where('id',$info['uid'])->update(['deal_status'=>2,'lian_good'=>0]);//将账户状态改为等待交易
                            $lian=$this->create_order($info['uid'],1,$userinfo['lian_good']);

                        }
                    }*/
                return ['code'=>0,'info'=>lang('czcg')];
            }else {
                Db::rollback();
                return ['code'=>1,'info'=>'操作失败'];
            }
        }elseif (in_array($status,[2,4])) {
            $res1 = Db::name('xy_users')->where('id',$info['uid'])->update(['deal_status'=>1]);
            if($status==4) Db::name('xy_message')->insert(['uid'=>$info['uid'],'type'=>2,'title'=>'System Notification','content'=>'The transaction order'.$oid.'has been forcibly cancelled by the system. If you have any questions, please contact customer service','addtime'=>time()]);
            //系统通知
            if($res && $res1!==false){
                Db::commit();
                return ['code'=>0,'info'=>lang('czcg')];
            }else {
                Db::rollback();
                return ['code'=>1,'info'=>'操作失败','data'=>$res1];
            }
        }
    }

    /**
     * 交易返佣
     *
     * @return void
     */
    public function deal_reward($uid,$oid,$num,$cnum)
    {
        //$res = Db::name('xy_users')->where('id',$uid)->where('status',1)->setInc('balance',$num+$cnum);
        $rows = Db::name('xy_users')->where('id',$uid)->where('status',1)->find();
        //$res = Db::name('xy_users')->where('id',$uid)->where('status',1)->setInc('balance',$num+$cnum);
        $res = Db::name('xy_users')->where('id',$uid)->where('status',1)->setInc('balance',$cnum);

        $res2 = Db::name('xy_users')->where('id',$uid)->where('status',1)->setDec('freeze_balance',$num+$cnum);

        if($res){
                $res1 = Db::name('xy_balance_log')->insert([
                    //记录返佣信息
                    'uid'       => $uid,
                    'oid'       => $oid,
                    //'num'       => $num+$cnum,
                    'num'       => $cnum,
                    'type'      => 3,
                    'addtime'   => time()
                ]);
                //将订单状态改为已返回佣金
                Db::name('xy_convey')->where('id',$oid)->update(['c_status'=>1,'status'=>1]);
                Db::name('xy_reward_log')->insert(['oid'=>$oid,'uid'=>$uid,'num'=>$num,'addtime'=>time(),'type'=>2]);//记录充值返佣订单
                 /************* 发放交易奖励 *********/
                 //上级返佣
               /*  if($rows['level']!=0){*/
                    $userList = model('admin/Users')->parent_user($uid,5);
                    if($userList){
                        foreach($userList as $v){
                            if($v['level']<2){
                               // continue;
                            }
                            if($rows['level']>$v['level']){
                              //  continue;
                            }
                            if($v['status']===1){
                                Db::name('xy_reward_log')
                                ->insert([
                                    'uid'       => $v['id'],
                                    'sid'       => $uid,
                                    'oid'       => $oid,
                                    'num'       => $cnum*config($v['lv'].'_d_reward'),
                                    'lv'        => $v['lv'],
                                    'type'      => 2,
                                    'status'    => 1,
                                    'addtime'   => time(),
                                ]);
                                $res1 = Db::name('xy_balance_log')->insert([
                                    //记录返佣信息
                                    'uid'       => $v['id'],
                                    'oid'       => $oid,
                                    'sid'       => $uid,
                                    'num'       => $cnum*config($v['lv'].'_d_reward'),
                                    'type'      => 6,
                                    'status'    => 1,
                                    'f_lv'        => $v['lv'],
                                    'addtime'   => time()  
                                ]);

                                $num3 = $cnum*config($v['lv'].'_d_reward'); //佣金
                                $res = Db::name('xy_users')->where('id',$v['id'])->where('status',1)->setInc('balance',$num3);
                            }
                        }
                    }
               /*  }*/
                 /************* 发放交易奖励 *********/
        }else{
            $res1 = Db::name('xy_convey')->where('id',$oid)->update(['c_status'=>2]);//记录账号异常
        }
    }
}