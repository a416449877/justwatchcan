<?php

namespace app\index\controller;

use think\Controller;
use think\Request;
use think\Db;

class My extends Base
{
    protected $msg = ['__token__'  => '请不要重复提交！'];
    /**
     * 首页
     */
    public function index()
    {
        $this->info = db('xy_users')->field('username,tel,level,id,headpic,balance,freeze_balance,lixibao_balance,invite_code,is_new,vpi_balance,show_td,credit_num')->find(session('user_id'));
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
        $this->lv3 = [0,config('vip_3_num')];
        $this->lv2 = [0,config('vip_2_num')];
        $this->sell_y_num = db('xy_convey')->where('status',1)->where('uid',session('user_id'))->sum('commission');

        $level = $this->info['level'];
        !$level ? $level = 0 :'';

        $this->level_name = db('xy_level')->where('level',$level)->value('name');

        $this->info['lixibao_balance'] = number_format($this->info['lixibao_balance'],2);

        $this->rililv = config('lxb_bili')*100 .'%' ;
        $this->lxb_shouyi = db('xy_lixibao')->where('status',1)->where('uid',session('user_id'))->sum('num');
        $uinfo = db('xy_users')->where('id', session('user_id'))->find();
        $level_name='Free';
        if (!empty($uinfo['level'])){
            $order_num = db('xy_level')->where('level',$uinfo['level'])->value('order_num');
            $level_name = db('xy_level')->where('level',$uinfo['level'])->value('name');
            $level_nums = db('xy_level')->where('level',$uinfo['level'])->value('num');
        }
        $uid=session('user_id');
        $this->level_name = $level_name;
        $where=[
            'uid'=>$uid,
            'type'=>6,
            'status'=>1
        ];
        //团队收益
        $tuandui=Db::name('xy_balance_log')->where($where)->sum('num');
        $where=[
            'uid'=>$uid,
            'status'=>1
            
        ];
        //余额宝收益
        $balanceT = db('xy_balance_log')->where('uid', session('user_id'))->where('status', 1)->where('type', 23)->sum('num');
        //总收益
       // $zong=Db::name('xy_balance_log')->where($where)->where('type!=21&&type!=22&&type!=1&&type!=7&&type!=26')->sum('num');
        $this->assign('tuandui',$tuandui);
       // $this->assign('zong',$zong);
        $this->assign('yuebao',$balanceT);
        return $this->fetch();
    }

    /**
     * 获取收货地址
     */
    public function get_address()
    {
        $id = session('user_id');
        $data = db('xy_member_address')->where('uid',$id)->select();
        if($data)
            return json(['code'=>0,'info'=>'获取成功','data'=>$data]);
        else
            return json(['code'=>1,'info'=>'暂未数据']);
    }
    //在线抽奖
    public function luckydraw()
    {
        $uid = session('user_id');
        $this->userinfo = db('xy_users')->where('id',$uid)->find();
        // $cj_set = db('xy_cj_set')->value('num');
        // if((time() - $this->userinfo['cj_time']) > 86400){
        //     db('xy_users')->where('id',$uid)->update(['cj_num'=>$cj_set]);
        // }
        //邀请人送次数
        $countid=Db::name('xy_users')->where('parent_id',$uid)->count('id');
        $countnum=Db::name('xy_cj_log')->where('tel',$this->userinfo['tel'])->count('id');
        //充值送次数
        // $countnum2=Db::name('xy_recharge')->where('uid',$uid)->sum('num');
        // $countnum2=floor($countnum2/500);
        $cjcount=floor($countid/10)-$countnum;
        Db::name('xy_users')->where('id',$uid)->update(['cj_num'=>$cjcount]);
        if(request()->isPost()) {
            // if($this->userinfo['id_status'] == 0){
            //     return json(['code'=>3]);
            // }
            // if($this->userinfo['level'] < 1){
            //     return json(['code'=>2,'info'=>lang('普通会员不可抽奖，请升级VIP会员')]);
            // }
            if($this->userinfo['cj_num'] >= 1){
                $num = $this->userinfo['cj_num'] - 1;
                db('xy_users')->where('id',$uid)->update(['cj_num'=>$num,'cj_time'=>time()]);
                return json(['code'=>0,'info'=>lang('www')]);
            }else{
                return json(['code'=>1,'info'=>"You have no chance to draw a lottery, invite members to get a chance to draw a lottery"]);
            }
            
        }else{
            $this->jp1 = db('xy_cj_jp')->where('id',1)->find();
            $this->jp2 = db('xy_cj_jp')->where('id',2)->find();
            $this->jp3 = db('xy_cj_jp')->where('id',3)->find();
            $this->jp4 = db('xy_cj_jp')->where('id',4)->find();
            $this->jp5 = db('xy_cj_jp')->where('id',5)->find();
            $this->jp6 = db('xy_cj_jp')->where('id',6)->find();
            $this->jp7 = db('xy_cj_jp')->where('id',7)->find();
            $this->jp8 = db('xy_cj_jp')->where('id',8)->find();
            $this->jp9 = db('xy_cj_jp')->where('id',9)->find();
            $this->jp10 = db('xy_cj_jp')->where('id',10)->find();
            $this->content = db('xy_cj_set')->where('id',1)->value('content');
            $this->shengyu = db('xy_users')->where('id',$uid)->value('cj_num');
            $this->log_list = db('xy_cj_log')->select();
            $this->userinfo = db('xy_users')->select();
            return $this->fetch(); 
        }
    }
    public function cj_do()
    {
        $uid = session('user_id');
        $id = input('post.id/s', '');
        $jp = input('post.jp/s', '');
        //查询奖品
        $jpinfo = db('xy_cj_jp')->find($id);
        //添加log
        $uinfo = db('xy_users')->find($uid);
        $data = ['tel' => $uinfo['tel'], 'jp' => $jp, 'jid' => $id, 'create_time' => time()];
        if($jpinfo['type'] == 4){
            return json(['code'=>0,'info'=>"Keep going"]);
        }
        $res = Db::name('xy_cj_log')->data($data)->insert();
        //更新数据
        // if($jpinfo['type'] == 1){
        //     $res1 = db('xy_users')->where('id',$uid)->update(['level'=>$jpinfo['lid']]);
        // }
        if($jpinfo['type'] == 2){
            $res1 = db('xy_users')->where('id',$uid)->setInc('balance',$jpinfo['num']);
        }
        // if($jpinfo['type'] == 3){
        //     if($jpinfo['cid'] == 1){
        //         $res1 = db('xy_users')->where('id',$uid)->setInc('task_dznum',$jpinfo['num']);
        //     }
        //     if($jpinfo['cid'] == 2){
        //         $res1 = db('xy_users')->where('id',$uid)->setInc('task_wxnum',$jpinfo['num']);
        //     }
        //     if($jpinfo['cid'] == 3){
        //         $res1 = db('xy_users')->where('id',$uid)->setInc('task_pddnum',$jpinfo['num']);
        //     }
        //     if($jpinfo['cid'] == 4){
        //         $res1 = db('xy_users')->where('id',$uid)->setInc('task_tbnum',$jpinfo['num']);
        //     }
        //     if($jpinfo['cid'] == 5){
        //         $res1 = db('xy_users')->where('id',$uid)->setInc('task_tmnum',$jpinfo['num']);
        //     }
        //     if($jpinfo['cid'] == 6){
        //         $res1 = db('xy_users')->where('id',$uid)->setInc('task_jdnum',$jpinfo['num']);
        //     }
            
        // }
        if($res && $res1){
            Db::name('xy_balance_log')->insert([
                        //记录返佣信息
                        'uid'       => $uid,
                        'oid'       => "抽奖赠送",
                        'num'       => $jpinfo['num'],
                        'type'      => 0,
                        'status'    => 1,
                        'addtime'   => time()
                    ]);
            return json(['code'=>0,'info'=>"Received successfully"]);
        }else{
            return json(['code'=>1,'info'=>"Failed to claim"]);
        }
        
    }
    
    //签到
    public function sign(){
        $uid = session('user_id');
        $where=[
            "uid"=>$uid,
            "time"=>date("Y-m-d")
        ];
        //vpi0不能签到
        $user=Db::name("xy_users")->where("id",$uid)->find();
        if($user['level']==0){
            return json(["code"=>0,"msg"=>lang("djbg")]);
        }
        //查询今天是否签到
        $count=Db::name("xy_sign")->where($where)->count();
        if($count>0){
            return json(["code"=>0,"msg"=>lang("daysign")]);
        }
        //查询昨天是否签到
        $sign=Db::name("xy_sign")->where($where)->find();
        if(empty($sign)){
            $num=1;
        }else{
            $num=$sign['count']+1;
        }
        $arr=[
            "uid"=>$uid,
            "time"=>date("Y-m-d"),
            "count"=>$num
        ];
        $rows=Db::name("xy_sign")->insert($arr);
        $money=[
          10=>20,
          30=>80
        ];
        if($rows){
            if(empty($money[$num])){
                return json(["code"=>0,"msg"=>lang("czcg")]);
            }else{
                $lixibao = Db::name('xy_lixibao_list')->where("day",config('lixisign'))->find();
                Db::name('xy_users')->where('id', $uid)->setInc('lixibao_balance', $money[$num]);
                $endtime = time() + $lixibao['day'] * 24 * 60 * 60;
                $yuji = $money[$num] * $lixibao['bili'] * $lixibao['day'];//利息
                $res = Db::name('xy_lixibao')->insert([
                    'uid' => $uid,
                    'num' => $money[$num],
                    'addtime' => time(),
                    'endtime' => $endtime,
                    'sid' => $lixibao['id'],
                    'yuji_num' => $yuji,
                    'type' => 1,
                    'status' => 0,
                ]);
                return json(["code"=>0,"msg"=>lang("czcg")]);
            }
        }else{
            return json(["code"=>1,"msg"=>lang("czsb")]);
        }
    }
    public function reload()
    {
        $id = session('user_id');;
        $user = db('xy_users')->find($id);

        $n = ($id%20);

        $dir = './upload/qrcode/user/'.$n . '/' . $id . '.png';
        if(file_exists($dir)) {
            unlink($dir);
        }

        $res = model('admin/Users')->create_qrcode($user['invite_code'],$id);
        if(0 && $res['code']!==0){
            return $this->error('失败');
        }
        return $this->success('成功');
    }


    /**c
     * 添加收货地址
     */
    public function add_address()
    {
        if(request()->isPost()){
            $uid = session('user_id');
            $name = input('post.name/s','');
            $tel = input('post.tel/s','');
            $address = input('post.address/s','');
            $area = input('post.area/s','');
            $token = input("token");//获取提交过来的令牌
            $data = ['__token__' => $token];
            $validate   = \Validate::make($this->rule,$this->msg);
            if (!$validate->check($data)) {
                return json(['code'=>1,'info'=>$validate->getError()]);
            }
            $data = [
                'uid'       => $uid,
                'name'      => $name,
                'tel'       => $tel,
                'area'      => $area,
                'address'   => $address,
                'addtime'   => time()
            ];
            $tmp = db('xy_member_address')->where('uid',$uid)->find();
            if(!$tmp) $data['is_default']=1;
            $res = db('xy_member_address')->insert($data);
            if($res)
                return json(['code'=>0,'info'=>lang("czcg")]);
            else
                return json(['code'=>1,'info'=>lang("czsb")]);
        }
        return json(['code'=>1,'info'=>'错误请求']);
    }

    /**
     * 编辑收货地址
     */
    public function edit_address()
    {
        if(request()->isPost()){
            $uid        = session('user_id');
            $name       = input('post.shoujianren/s','');
            $tel        = input('post.shouhuohaoma/s','');
            $address    = input('post.address/s','');

            $area       = input('post.area/s','');


            $ainfo = db('xy_member_address')->where('uid',$uid)->find();
            if ($ainfo) {
                $res = db('xy_member_address')
                    ->where('id',$ainfo['id'])
                    ->update([
                        'uid'       => $uid,
                        'name'      => $name,
                        'tel'       => $tel,
                        'area'      => $area,
                        'address'   => $address,
                        //'addtime'   => time()
                    ]);
            }else{
                $res = db('xy_member_address')
                    ->insert([
                        'uid'       => $uid,
                        'name'      => $name,
                        'tel'       => $tel,
                        'area'      => $area,
                        'address'   => $address,
                        'addtime'   => time()
                    ]);
            }

            if($res)
                return json(['code'=>0,'info'=>lang("czcg")]);
            else
                return json(['code'=>1,'info'=>lang("czsb")]);
        }elseif (request()->isGet()) {
            $id = session('user_id');
            $this->info = db('xy_member_address')->where('uid',$id)->find();

            return $this->fetch();
        }
    }

    public function team()
    {
        $uid = session('user_id');
        //$this->info = db('xy_member_address')->where('uid',$id)->find();
        $uids = model('admin/Users')->child_user($uid,5);
        array_push($uids,$uid);
        $uids ? $where[] = ['uid','in',$uids] : $where[] = ['uid','in',[-1]];

        $datum['sl'] = count($uids);
        $datum['yj'] = db('xy_convey')->where('status',1)->where($where)->sum('num');
        $datum['cz'] = db('xy_recharge')->where('status',2)->where($where)->sum('num');
        $datum['tx'] = db('xy_deposit')->where('status',2)->where($where)->sum('num');


        //
        $uids1 = model('admin/Users')->child_user($uid,1);
        $uids1 ? $where1[] = ['sid','in',$uids1] : $where1[] = ['sid','in',[-1]];
        $datum['log1'] = db('xy_balance_log')->where('uid',$uid)->where($where1)->where('f_lv',1)->sum('num');

        $uids2 = model('admin/Users')->child_user($uid,2);
        $uids2 ? $where2[] = ['sid','in',$uids2] : $where2[] = ['sid','in',[-1]];
        $datum['log2'] = db('xy_balance_log')->where('uid',$uid)->where($where2)->where('f_lv',2)->sum('num');

        $uids3 = model('admin/Users')->child_user($uid,3);
        $uids3 ? $where3[] = ['sid','in',$uids3] : $where3[] = ['sid','in',[-1]];
        $datum['log3'] = db('xy_balance_log')->where('uid',$uid)->where($where3)->where('f_lv',3)->sum('num');


        $uids5 = model('admin/Users')->child_user($uid,5);
        $uids5 ? $where5[] = ['sid','in',$uids5] : $where5[] = ['sid','in',[-1]];
        $datum['yj2'] = db('xy_convey')->where('status',1)->where($where)->sum('commission');
        $datum['yj3'] = db('xy_balance_log')->where('uid',$uid)->where($where5)->where('type',6)->sum('num');;


        $this->info = $datum;

        return $this->fetch();
    }

    public function caiwu()
    {
        $id = session('user_id');
       // $day      = input('get.day/s','');
        $where=[];
      /*  if ($day) {
            $start = strtotime("-$day days");
            $where[] = ['addtime','between',[$start,time()]];
        }

        $start      = input('get.start/s','');
        $end        = input('get.end/s','');
        if ($start || $end) {
            $start ? $start = strtotime($start) : $start = strtotime('2020-01-01');
            $end ? $end = strtotime($end.' 23:59:59') : $end = time();
            $where[] = ['addtime','between',[$start,$end]];
        }


        $this->start = $start ? date('Y-m-d',$start) : '';
        $this->end = $end ? date('Y-m-d',$end) : '';
*/
        $this->type = $type = input('get.type/d',0);

        if ($type == 1) {
            $where['type'] = 7;
        }elseif($type==2) {
            $where['type'] = 1;
        }


        $this->_query('xy_balance_log')
            ->where('uid',$id)->where($where)->order('id desc')->page();
        //var_dump($_REQUEST);die;
    }


    public function headimg()
    {
        $uid = session('user_id');
        if(request()->isPost()) {
            $username = input('post.pic/s', '');
            $res = db('xy_users')->where('id',session('user_id'))->update(['headpic'=>$username]);
            if($res!==false){
                return json(['code'=>0,'info'=>lang("czcg")]);
            }else{
                return json(['code'=>1,'info'=>lang("czsb")]);
            }
        }
        $this->info = db('xy_users')->find($uid);
        return $this->fetch();
    }
     public function editperson(){
         $uid = session('user_id');
          $this->info = db('xy_users')->find($uid);
        return $this->fetch();
     }
    public function bind_bank()
    {
        $id = input('post.id/d',0);
        $uid = session('user_id');
        $info = db('xy_bankinfo')->where('uid',$uid)->where('type','2')->find();
        if($info){ return $this->redirect('Index/kefu'); }
        $uinfo = db('xy_users')->find($uid);

        if(request()->isPost()){
            $username = input('post.username/s','');
            $bankname = input('post.bankname/s','');
            $cardnum = input('post.id_number/s','');
            // $email = input('post.$email/s','');
            $tel = input('post.tel/s','');
            $site  = input('post.document_type/s','');
            $email  = input('post.email/s','');
            $qq  = input('post.qq/s','');
            $address  = input('post.address/s','');
            $document_id  = input('post.document_id/s','');
            $bank_code  = input('post.bank_code/s','');
            $account_type  = input('post.account_type/s','');
            $account_digit  = input('post.account_digit/s','');

            //同一身份证号和银行卡号只绑定一次
            $res = db('xy_bankinfo')->where('document_id',$document_id)->find();
            $res2 = db('xy_bankinfo')->where('cardnum',$cardnum)->find();
            // $res3 = db('xy_bankinfo')->where('bankname',$bankname)->find();
           
            
            
            //if ($res2 && $res2['uid'] != $uid) {
               // return json(['code'=>1,'info'=>lang('bdcfu')]);
            //}
            // if ($res3 && $res3['uid'] != $uid) {
            //     return json(['code'=>1,'info'=>"IFSC Code duplicate"]);
            // }
           // if ($res && $res['uid'] != $uid) {
                //return json(['code'=>1,'info'=>lang('bdcfu')]);
           // }
            

            $data =array(
                'username' =>$username,
                'bankname' =>$bankname,
                'cardnum' =>$cardnum,
                'site' =>$site,
                'email'=>$email,
                'address' =>$address,
                'qq' =>$qq,
                'tel' =>$tel,
                'status' =>1,
                'type' =>2,
                'uid' =>$uid,
                'document_id' =>$document_id,
                'bank_code' =>$bank_code,
                'account_type' =>$account_type,
                'account_digit' =>$account_digit,
            );
            if ($info) {
                $res = db('xy_bankinfo')->where('uid',$uid)->update($data);
            }else{
                $res = db('xy_bankinfo')->insert($data);
            }
            db('xy_logs')->insert([
                'action_user' => $uinfo['username'],
                'user_name'   => $uinfo['username'],
                'memo'        => '用户自己修改银行信息',
                'type'        => 100,
                'action_ip'   => request()->ip(),
                'update_time' => time(),
                'params'      => json_encode(request()->post()),
                'user_agent'  => request()->header('user-agent')
            ]);
            if($res){
                return json(['code'=>0,'info'=>lang('czcg')]);
            }else{
                return json(['code'=>1,'info'=>lang('czsb')]);
            }
        }


        $this->info = $info;
        return $this->fetch();
    }


    public function bind_usdt()
    {
        $id = input('post.id/d',0);
        $uid = session('user_id');
        $info = db('xy_bankinfo')->where('uid',$uid)->where('type','1')->find(); if($info){ return $this->redirect('Index/kefu'); }
        $uinfo = db('xy_users')->find($uid);

        if(request()->isPost()){
            $username = input('post.username/s','');
            $bankname = input('post.bankname/s','');
            $cardnum = input('post.id_number/s','');
            // $email = input('post.$email/s','');
            $tel = input('post.tel/s','');
            $site  = input('post.document_type/s','');
            $email  = input('post.email/s','');
            $qq  = input('post.qq/s','');
            $address  = input('post.address/s','');
            $document_id  = input('post.document_id/s','');
            $bank_code  = input('post.bank_code/s','');
            $account_type  = input('post.account_type/s','');
            $account_digit  = input('post.account_digit/s','');

            //同一身份证号和银行卡号只绑定一次
            $res = db('xy_bankinfo')->where('document_id',$document_id)->find();
            $res2 = db('xy_bankinfo')->where('cardnum',$cardnum)->find();
            // $res3 = db('xy_bankinfo')->where('bankname',$bankname)->find();
           
            
            
            //if ($res2 && $res2['uid'] != $uid) {
               // return json(['code'=>1,'info'=>lang('bdcfu')]);
            //}
            // if ($res3 && $res3['uid'] != $uid) {
            //     return json(['code'=>1,'info'=>"IFSC Code duplicate"]);
            // }
           // if ($res && $res['uid'] != $uid) {
                //return json(['code'=>1,'info'=>lang('bdcfu')]);
           // }
            

            $data =array(
                'username' =>$username,
                'bankname' =>$bankname,
                'cardnum' =>$cardnum,
                'site' =>$site,
                'email'=>$email,
                'address' =>$address,
                'qq' =>$qq,
                'tel' =>$tel,
                'status' =>1,
                'uid' =>$uid,
                'type'=>'1',
                'document_id' =>$document_id,
                'bank_code' =>$bank_code,
                'account_type' =>$account_type,
                'account_digit' =>$account_digit,
            );
            if ($info) {
                return json(['code'=>1,'info'=>lang('czsb')]);
              //  $res = db('xy_bankinfo')->where('uid',$uid)->update($data);
            }else{
                $res = db('xy_bankinfo')->insert($data);
            }
            db('xy_logs')->insert([
                'action_user' => $uinfo['username'],
                'user_name'   => $uinfo['username'],
                'memo'        => '用户自己修改银行信息',
                'type'        => 100,
                'action_ip'   => request()->ip(),
                'update_time' => time(),
                'params'      => json_encode(request()->post()),
                'user_agent'  => request()->header('user-agent')
            ]);
            if($res){
                return json(['code'=>0,'info'=>lang('czcg')]);
            }else{
                return json(['code'=>1,'info'=>lang('czsb')]);
            }
        }


        $this->info = $info;
        return $this->fetch();
    }


    /**
     * 设置默认收货地址
     */
    public function set_address()
    {
        if(request()->isPost()){
            $id = input('post.id/d',0);
            Db::startTrans();
            $res = db('xy_member_address')->where('uid',session('user_id'))->update(['is_default'=>0]);
            $res1 = db('xy_member_address')->where('id',$id)->update(['is_default'=>1]);
            if($res && $res1){
                Db::commit();
                return json(['code'=>0,'info'=>lang("czcg")]);
            }else{
                Db::rollback();
                return json(['code'=>1,'info'=>lang("czsb")]);
            }
        }
        return json(['code'=>1,'info'=>'错误请求']);
    }

    /**
     * 删除收货地址
     */
    public function del_address()
    {
        if(request()->isPost()){
            $id = input('post.id/d',0);
            $info = db('xy_member_address')->find($id);
            if($info['is_default']==1){
                return json(['code'=>1,'info'=>'不能删除默认收货地址']);
            }
            $res = db('xy_member_address')->where('id',$id)->delete();
            if($res)
                return json(['code'=>0,'info'=>lang("czcg")]);
            else
                return json(['code'=>1,'info'=>lang("czsb")]);
        }
        return json(['code'=>1,'info'=>'错误请求']);
    }

    public function get_bot(){
        $data = model('admin/Users')->get_botuser(session('user_id'),3);
        halt($data);
    }





    public function yue(){
        $uid = session('user_id');
        $this->info = db('xy_users')->find($uid);
        return $this->fetch();
    }


    public function edit_username(){
        $uid = session('user_id');
        if(request()->isPost()) {
            $username = input('post.username/s', '');
            $res = db('xy_users')->where('id',session('user_id'))->update(['username'=>$username]);
            if($res!==false){
                return json(['code'=>0,'info'=>lang("czcg")]);
            }else{
                return json(['code'=>1,'info'=>lang("czsb")]);
            }
        }
        $this->info = db('xy_users')->find($uid);
        return $this->fetch();
    }



    /**
     * 用户账号充值
     */
    public function user_recharge()
    {
        $tel = input('post.tel/s','');
        $num = input('post.num/d',0);
        $pic = input('post.pic/s','');
        $real_name = input('post.real_name/s','');
        $uid = session('user_id');

        if(!$pic || !$num ) return json(['code'=>1,'info'=>'参数错误']);
        //if(!is_mobile($tel)) return json(['code'=>1,'info'=>'手机号码格式不正确']);

        if (is_image_base64($pic))
            $pic = '/' . $this->upload_base64('xy',$pic);  //调用图片上传的方法
        else
            return json(['code'=>1,'info'=>'图片格式错误']);
        $id = getSn('SY');
        $res = db('xy_recharge')
            ->insert([
                'id'        => $id,
                'uid'       => $uid,
                'tel'       => $tel,
                'real_name' => $real_name,
                'pic'       => $pic,
                'num'       => $num,
                'addtime'   => time()
            ]);
        if($res)
            return json(['code'=>0,'info'=>'提交成功']);
        else
            return json(['code'=>1,'info'=>'提交失败，请稍后再试']);
    }

    //邀请界面
    public function invite()
    {
        $uid = session('user_id');
        $this->assign('pic','/upload/qrcode/user/'.($uid%20).'/'.$uid.'.png');

        $user = db('xy_users')->find($uid);

        $url = SITE_URL . url('@index/user/register/invite_code/'.$user['invite_code']);
        $this->assign('url',$url);
        $this->assign('invite_code',$user['invite_code']);
        return $this->fetch();
    }

    //我的资料
    public function do_my_info()
    {
        if(request()->isPost()){
            $headpic    = input('post.headpic/s','');
            $wx_ewm    = input('post.wx_ewm/s','');
            $zfb_ewm    = input('post.zfb_ewm/s','');
            $nickname   = input('post.nickname/s','');
            $sign       = input('post.sign/s','');
            $data = [
                'nickname'  => $nickname,
                'signiture' => $sign
            ];

            if($headpic){
                if (is_image_base64($headpic))
                    $headpic = '/' . $this->upload_base64('xy',$headpic);  //调用图片上传的方法
                else
                    return json(['code'=>1,'info'=>'图片格式错误']);
                $data['headpic'] = $headpic;
            }

            if($wx_ewm){
                if (is_image_base64($wx_ewm))
                    $wx_ewm = '/' . $this->upload_base64('xy',$wx_ewm);  //调用图片上传的方法
                else
                    return json(['code'=>1,'info'=>'图片格式错误']);
                $data['wx_ewm'] = $wx_ewm;
            }

            if($zfb_ewm){
                if (is_image_base64($zfb_ewm))
                    $zfb_ewm = '/' . $this->upload_base64('xy',$zfb_ewm);  //调用图片上传的方法
                else
                    return json(['code'=>1,'info'=>'图片格式错误']);
                $data['zfb_ewm'] = $zfb_ewm;
            }




            $res = db('xy_users')->where('id',session('user_id'))->update($data);
            if($res!==false){
                if($headpic) session('avatar',$headpic);
                return json(['code'=>0,'info'=>lang("czcg")]);
            }else{
                return json(['code'=>1,'info'=>lang("czsb")]);
            }
        }elseif(request()->isGet()){
            $info = db('xy_users')->field('username,headpic,nickname,signiture sign,wx_ewm,zfb_ewm')->find(session('user_id'));
            return json(['code'=>0,'info'=>'请求成功','data'=>$info]);
        }
    }

    // 消息
    public function activity()
    {
        $where[] = ['title','like','%' . '活动' . '%'];

        $this->msg = db('xy_index_msg')->where($where)->select();
        return $this->fetch();
    }



    // 消息
    public function msg()
    {
        $this->info = db('xy_message')->alias('m')
            // ->leftJoin('xy_users u','u.id=m.sid')
            ->leftJoin('xy_reads r','r.mid=m.id and r.uid='.session('user_id'))
            ->field('m.*,r.id rid')
            ->where('m.uid','in',[0,session('user_id')])
            ->order('addtime desc')
            ->select();

        $this->msg = db('xy_index_msg')->where('status',1)->select();
        return $this->fetch();
    }

    // 消息
    public function detail()
    {
        $id = input('get.id/d',0);

        $this->msg = db('xy_home_foot')->where('id',$id)->find();
        
//    return json(['code'=>1,'data'=>$this->msg]);

  return $this->fetch();
    }
    // 消息
    public function details()
    {
        $id = input('get.id/d',0);

        $this->msg = db('xy_home_foot')->where('id',$id)->find();
        


        return $this->fetch('detail');
    }

    //记录阅读情况
    public function reads()
    {
        if(\request()->isPost()){
            $id = input('post.id/d',0);
            db('xy_reads')->insert(['mid'=>$id,'uid'=>session('user_id'),'addtime'=>time()]);
            return $this->success('成功');
        }
    }

    public function gonggao()
    {
        
    }

    //修改绑定手机号
    public function reset_tel()
    {
        $pwd = input('post.pwd','');
        $verify = input('post.verify/s','');
        $tel = input('post.tel/s','');
        $userinfo = Db::table('xy_users')->field('id,pwd,salt')->find(session('user_id'));
        if($userinfo['pwd'] != sha1($pwd.$userinfo['salt'].config('pwd_str'))) return json(['code'=>1,'info'=>'登录密码错误']);
        if(config('app.verify')){
            $verify_msg = Db::table('xy_verify_msg')->field('msg,addtime')->where(['tel'=>$tel,'type'=>3])->find();
            if(!$verify_msg) return json(['code'=>1,'info'=>'验证码不存在']);
            if($verify != $verify_msg['msg']) return json(['code'=>1,'info'=>'验证码错误']);
            if(($verify_msg['addtime'] + (config('app.zhangjun_sms.min')*60)) < time())return json(['code'=>1,'info'=>'验证码已失效']);
        }
        $res = db('xy_users')->where('id',session('user_id'))->update(['tel'=>$tel]);
        if($res!==false)
            return json(['code'=>0,'info'=>lang("czcg")]);
        else
            return json(['code'=>1,'info'=>lang("czsb")]);
    }

    //团队佣金列表
    public function get_team_reward()
    {
        $uid = session('user_id');
        $lv = input('post.lv/d',1);
        $num = Db::name('xy_reward_log')->where('uid',$uid)->where('addtime','between',strtotime(date('Y-m-d')) . ',' . time())->where('lv',$lv)->where('status',1)->sum('num');

        if($num) return json(['code'=>0,'info'=>'请求成功','data'=>$num]);
        return json(['code'=>1,'info'=>'暂无数据']);
    }
}