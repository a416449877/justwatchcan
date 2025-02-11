<?php

namespace app\index\controller;

use think\Controller;
use think\facade\Config;
use think\Request;
use think\Db;

class Ctrl extends Base
{
    
    public function dial()
    {
        // if(config('zp') == 2){
        //     result(lang('功能关闭'),'/index/my/index.html');
        // }
        
        
        $awrad = [18.8,38.8,108.8,188,288,588];
        $pros   = [0   ,0   ,0    ,0  ,0  ,0  ];
        // $awrad = array_map(function($v){
        //     return $v.lang('元');
        // },$awrads);
        //
        $num = DB::table('xy_award')->where(['uid' => cookie('user_id')])->count('*');
        $data = [
            1,
            2,
            3,
            4,
            ];
        if(@isset($pros[$data[$num]])){
            $pros[$data[$num]] = 100;
        }else{
            $pros   = [25   ,15   ,15    ,15  ,15  ,15  ];
        }
        
        
        
        $pro = array_map(function($v){
            return $v.'%';
        },$pros);
        
        $zp_num = DB::table('xy_users')->where('id',session('user_id'))->value('zp_num');
        
        
        $this->assign('zp_num', $zp_num);
        $this->assign('pro', $pro);
        $this->assign('awrad',$awrad);

        return $this->fetch();
    }
    
    
    public function awrad()
    {

        $uid = session('user_id');
        $zp_num = DB::table('xy_users')->where('id',$uid)->value('zp_num');
        if($zp_num <= 0){
            echo json_encode(['msg' =>lang('抽奖次数不足') , 'code'=> 500]);
            exit;
        }
        
        $awrads = [18.8,38.8,108.8,188,288,588];
        $i = input('i');
        
        $num = $awrads[$i];
        // 抽奖记录
        DB::table('xy_award')->insert([
            'uid'=>$uid,
            'time'=>time(),
            'award'=>$num,
            ]);
        // 用户余额记录
        DB::table('xy_balance_log')->insert([
            'uid'=>$uid,
            'oid'=> getSn('ZP'),
            'num'=>$num,
            'type'=>87,
            'status'=>1,
            'addtime'=>time(),
            ]);
        DB::table('xy_users')->where(['id' => $uid])->setInc('balance',$num);
        DB::table('xy_users')->where(['id' => $uid])->setDec('zp_num',1);
        echo json_encode(['msg' =>lang('恭喜获得').$num , 'code'=> 200]);
        exit;
    }

    //循环公告页面
    public function newnotice(){
        //生成提现
        $datatx=[];
        //生成佣金
         $datayj=[]; 
        //生成充值
         $datacz=[];
         for ($i=1; $i<=1000; $i++)
        {
            
            $datatx[]=[rand(10000,99999),rand(10000,500000)];
            $datayj[]=[rand(10000,99999),rand(10000,500000)];
            $datacz[]=[rand(10000,99999),rand(100,500)*100];
        }
        $this->datatx=$datatx; 
     
        $this->assign('datatx', $datatx);
        $this->assign('datayj', $datayj);
        $this->assign('datacz', $datacz);
       // echo $request->controller();
      //  exit;
        return $this->fetch();
        
    }
    //钱包页面
    public function wallet()
    {
        $balance = db('xy_users')->where('id', session('user_id'))->value('balance');
        $this->assign('balance', $balance);
        $balanceT = db('xy_convey')->where('uid', session('user_id'))->where('status', 1)->sum('commission');
        $this->assign('balance_shouru', $balanceT);

        //收益
        $startDay = strtotime(date('Y-m-d 00:00:00', time()));
        $shouyi = db('xy_convey')->where('uid', session('user_id'))->where('addtime', '>', $startDay)->where('status', 1)->select();

        //充值
        $chongzhi = db('xy_recharge')->where('uid', session('user_id'))->where('addtime', '>', $startDay)->where('status', 2)->select();

        //提现
        $tixian = db('xy_deposit')->where('uid', session('user_id'))->where('addtime', '>', $startDay)->where('status', 1)->select();

        $this->assign('shouyi', $shouyi);
        $this->assign('chongzhi', $chongzhi);
        $this->assign('tixian', $tixian);
        return $this->fetch();
    }


    public function recharge_before()
    {
        $pay = db('xy_pay')->where('status', 1)->select();

        $this->assign('pay', $pay);
        return $this->fetch();
    }

    
    public function edit_trading(){
        if (request()->isPost()) {
            $uid = session('user_id');
            $user=Db::name("xy_users")->where("id",$uid)->find();
            $data=input("post.");
            $new=sha1($data['new_pwd'] . $user['salt2'] . config('pwd_str'));
            if($data["num"]==1){
                $rows=Db::name("xy_users")->where("id",$uid)->update(["pwd2"=>$new]);
                if($rows){
                    return json(['code' => 0, 'info' => lang('czcg')]);
                }else{
                    return json(['code' => 1, 'info' => lang('czsb')]);
                }
            }else{
                if($user['pwd2']!=sha1($data["old_pwd"] . $user['salt2'] . config('pwd_str'))){
                    return json(['code' => 1, 'info' => lang('pass_error')]);
                }
                $rows=Db::name("xy_users")->where("id",$uid)->update(["pwd2"=>$new]);
                if($rows){
                    return json(['code' => 0, 'info' => lang('czcg')]);
                }else{
                    return json(['code' => 1, 'info' => lang('czsb')]);
                }
            }
        }
        $uid = session('user_id');
        $user=Db::name("xy_users")->where("id",$uid)->find();
        if(empty($user['pwd2'])){
            $this->num=1;
        }else{
            $this->num=2;
        }
        return $this->fetch();
    }
    
    public function vip()
    {
        $pay = db('xy_pay')->where('status', 1)->select();
        $this->member_level = db('xy_level')->order('level asc')->select();;
        $this->info = db('xy_users')->where('id', session('user_id'))->find();
        $this->member = $this->info;

        //var_dump($this->info['level']);die;

        $level_name = $this->member_level[0]['name'];
        $order_num = $this->member_level[0]['order_num'];
        if (!empty($this->info['level'])) {
            $level_name = db('xy_level')->where('level', $this->info['level'])->value('name');;
        }
        if (!empty($this->info['level'])) {
            $order_num = db('xy_level')->where('level', $this->info['level'])->value('order_num');;
        }

        $this->level_name = $level_name;
        $this->order_num = $order_num;
        $this->list = $pay;
        return $this->fetch();
    }

    /**
     * @地址      recharge_dovip
     * @说明      利息宝
     * @参数       @参数 @参数
     * @返回      \think\response\Json
     */
    public function lixibao()
    {
        $this->assign('title', '利息宝');
        $uinfo = db('xy_users')->field('username,tel,level,id,headpic,balance,freeze_balance,lixibao_balance,lixibao_dj_balance,vpi_balance,is_new')->find(session('user_id'));
        $levelnum=0;
            for($i=0;$i<=$uinfo['level'];$i++){
                $ulevels = Db::name('xy_level')->where('level', $i)->find();
                $levelnum=$levelnum+$ulevels['num'];
            }
            
        //if($uinfo['is_new']==1){
           // $balance=$uinfo['balance']-config("registration")-$levelnum;
       // }else{
          //  $balance=$uinfo['balance']-$levelnum;
       // }
        //$this->assign('ubalance', $balance);
        $this->assign('ubalance', $uinfo['balance']);

        
        $this->assign('balance', $uinfo['lixibao_balance']);
        $this->assign('balance_total', $uinfo['lixibao_balance'] + $uinfo['lixibao_dj_balance']);
        $balanceT = db('xy_lixibao')->where('uid', session('user_id'))->where('status', 1)->where('type', 3)->sum('num');

        $balanceT = db('xy_balance_log')->where('uid', session('user_id'))->where('status', 1)->where('type', 23)->sum('num');

        $yes1 = strtotime(date("Y-m-d 00:00:00", strtotime("-1 day")));
        $yes2 = strtotime(date("Y-m-d 23:59:59", strtotime("-1 day")));
        $this->yes_shouyi = db('xy_balance_log')->where('uid', session('user_id'))->where('status', 1)->where('type', 23)->where('addtime', 'between', [$yes1, $yes2])->sum('num');

        $this->assign('balance_shouru', $balanceT);


        //收益
        $startDay = strtotime(date('Y-m-d 00:00:00', time()));
        $shouyi = db('xy_lixibao')->where('uid', session('user_id'))->select();

        foreach ($shouyi as &$item) {
            $type = '';
            if ($item['type'] == 1) {
                $type = '<font color="green">转入利息宝</font>';
            } elseif ($item['type'] == 2) {
                $n = $item['status'] ? '已到账' : '未到账';
                $type = '<font color="red" >利息宝转出(' . $n . ')</font>';
            } elseif ($item['type'] == 3) {
                $type = '<font color="orange" >每日收益</font>';
            } else {

            }

            $lixbao = Db::name('xy_lixibao_list')->find($item['sid']);

            $name = $lixbao['name'] . '(' . $lixbao['day'] . '天)' . $lixbao['bili'] * 100 . '% ';

            $item['num'] = number_format($item['num'], 2);
            $item['name'] = $type . '　　' . $name;
            $item['shouxu'] = $lixbao['shouxu'] * 100 . '%';
            $item['addtime'] = date('Y/m/d H:i', $item['addtime']);

            if ($item['is_sy'] == 1) {
                $notice = '正常收益,实际收益' . $item['real_num'];
            } else if ($item['is_sy'] == -1) {
                $notice = '未到期提前提取,未收益,手续费为:' . $item['shouxu'];
            } else {
                $notice = '理财中...';
            }
            $item['notice'] = $notice;
        }

        $this->rililv = config('lxb_bili') * 100 . '%';
        $this->shouyi = $shouyi;
        if (request()->isPost()) {
            return json(['code' => 0, 'info' => '操作', 'data' => $shouyi]);
        }
        //预计收入
        $yuji_num= Db::name('xy_lixibao')->where(['uid'=>session('user_id'),'is_qu'=>0])->order('addtime desc')->sum('yuji_num');
        $this->assign('yuji_num',$yuji_num);
        
        $lixibao = Db::name('xy_lixibao_list')->field('id,name,bili,day,min_num,xiangou_num,bj')->order('day asc')->select();
        foreach ($lixibao as $k=>$v){
            
            $count_lixibao = Db::name('xy_lixibao')->where('sid',$v['id'])->count('id');
            if($count_lixibao>=$v['xiangou_num']){
                $v['yv']=0;
            }else{
                $v['yv']=$v['xiangou_num']-$count_lixibao;
            }
            $arr[]=$v;
        }
        $this->lixibao = $arr;
        return $this->fetch();
    }

    public function lixibao_ru()
    {
        $uid = session('user_id');
        $uinfo = Db::name('xy_users')->field('recharge_num,status,deal_time,balance,level,is_new,vpi_balance')->find($uid);//获取用户今日已充值金额

        if (request()->isPost()) { 
            $price = input('post.price/d', 0);
            $id = input('post.lcid/d', 0);
            $yuji = 0;
            
            if ($id) {
                $lixibao = Db::name('xy_lixibao_list')->find($id);
                $count_lixibao = Db::name('xy_lixibao')->where('sid',$id)->count('id');
                /*
                if($count_lixibao>=$lixibao['xiangou_num']){
                    return json(['code' => 4, 'info' => "VIP$uinfo[level] value-added card has been robbed (end), please try again tomorrow"]);
                }
                */
                //判断最小等级限制
                if($lixibao['level_min']>$uinfo['level']){
                    return json(['code' => 4, 'info' => "You need to upgrade VIP$lixibao[level_min] to buy"]);
                }
                if ($price < $lixibao['min_num']) {
                    return json(['code' => 1, 'info' => lang('cpzdtzje') . $lixibao['min_num']]);
                }
                if ($price > $lixibao['max_num']) {
                    return json(['code' => 1, 'info' => lang('cpzgtzje') . $lixibao['max_num']]);
                }
                if($lixibao['status']==0){
                    return json(['code' => 1, 'info' => "Opening soon..."]);
                }
                $yuji = $price * $lixibao['bili'] * $lixibao['day'];
            } else {
                return json(['code' => 1, 'info' => lang('cscw')]);
            }


            if ($price <= 0) {
                return json(['code' => 1, 'info' => 'you are sb']); //直接充值漏洞
            }
            if($uinfo['is_new']==1){
                $uinfo['balance']-=config("registration");
            }
            /*
            $levelnum=0;
            for($i=0;$i<=$uinfo['level'];$i++){
                $ulevels = Db::name('xy_level')->where('level', $i)->find();
                $levelnum=$levelnum+$ulevels['num'];
            }
            $uinfo['balance']-=$levelnum;//可用余额
            */
            if ($uinfo['balance'] < $price) { 
                return json(['code' => 1, 'info' => lang('money_not')]);
            }
            Db::name('xy_users')->where('id', $uid)->setInc('lixibao_balance', $price);  //利息宝月 +
            Db::name('xy_users')->where('id', $uid)->setDec('balance', $price);  //余额 -

            $endtime = time() + $lixibao['day'] * 24 * 60 * 60;
 
            $res = Db::name('xy_lixibao')->insert([
                'uid' => $uid,
                'num' => $price,
                'addtime' => time(),
                'endtime' => $endtime,
                'sid' => $id,
                'yuji_num' => $yuji,
                'type' => 1,
                'status' => 0,
            ]);
            $oid = Db::name('xy_lixibao')->getLastInsID();
            $res1 = Db::name('xy_balance_log')->insert([
                //记录返佣信息
                'uid' => $uid,
                'oid' => $oid,
                'num' => $price,
                'type' => 21,
                'addtime' => time()
            ]);
            if ($res) {
                return json(['code' => 0, 'info' => lang('czcg')]);
            } else {
                return json(['code' => 1, 'info' => lang('czsb')]);
            }
        }
        
        $this->rililv = config('lxb_bili') * 100 . '%';
        $this->yue = $uinfo['balance'];
        $isajax = input('get.isajax/d', 0);

        if ($isajax) {
            $lixibao = Db::name('xy_lixibao_list')->field('id,name,bili,day,min_num')->select();
            $data2 = [];
            $str = $lixibao[0]['name'] . '(' . $lixibao[0]['day'] . '天)' . $lixibao[0]['bili'] * 100 . '% (' . $lixibao[0]['min_num'] . '起投)';
            foreach ($lixibao as $item) {
                $data2[] = array(
                    'id' => $item['id'],
                    'value' => $item['name'] . '(' . $item['day'] . '天)' . $item['bili'] * 100 . '% (' . $item['min_num'] . '起投)',
                );
            }
            return json(['code' => 0, 'info' => '操作', 'data' => $data2, 'data0' => $str]);
        }

        $this->libi = 1;

        $this->assign('title', '利息宝余额转入');
        return $this->fetch();
    }


    public function deposityj()
    {
        $num = input('post.price/f', 0);
        $id = input('post.lcid/d', 0);
        if ($id) {
            $lixibao = Db::name('xy_lixibao_list')->find($id);

            $res = $num * $lixibao['day'] * $lixibao['bili'];
            return json(['code' => 0, 'info' => '操作', 'data' => $res]);
        }
    }

    public function lixibao_chu()
    {
        $uid = session('user_id');
        $uinfo = Db::name('xy_users')->field('recharge_num,deal_time,balance,level,lixibao_balance')->find($uid);//获取用户今日已充值金额

        if (request()->isPost()) {
            $id = input('post.id/d', 0);
            $lixibao = Db::name('xy_lixibao')->find($id);
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
            
  
            
            // Db::name('xy_users')->where('id', $uid)->setDec('lixibao_balance', $price);  //余额 -

            $oldprice = $price;
            $shouxu = $lxbParam['shouxu'];
            if ($shouxu) {
                $price = $price;
            }
            
                      //
            $issy = 0;
            if (time() < $lixibao['endtime'] && $lxbParam['time_state']==1) {
                //未到期
                return json(['code' => 1, 'info' => lang('sjwd')]);
            }else if(time() < $lixibao['endtime'] && $lxbParam['time_state']==0){
                $issy = 1;
                $real_num=0;
            } else  {
                
                $issy = 1;
                $real_num=$lixibao['yuji_num'];
                
                $oldprice = $price;
                $shouxu = $lxbParam['shouxu'];
                if ($shouxu) {
                    $price = $price - $price * $shouxu;
                }
               
            }

            $res = Db::name('xy_lixibao')->where('id', $id)->update([
                'endtime' => time(),
                'is_qu' => 1,
                'is_sy' => $issy,
                'shouxu' => $oldprice * $shouxu,
                "real_num"=>$real_num
            ]);


            Db::name('xy_users')->where('id', $uid)->setInc('balance', $price+$real_num);  //余额 +
            Db::name('xy_users')->where('id', $uid)->setDec('lixibao_balance', $lixibao['num']);  //利息宝余额 -
            $res1 = Db::name('xy_balance_log')->insert([
                //记录返佣信息
                'uid' => $uid,
                'oid' => $id,
                'num' => $price,
                'type' => 22,
                'addtime' => time()
            ]);
            //利息宝记录转出
            Db::name('xy_balance_log')->insert([
                //记录返佣信息
                'uid' => $uid,
                'oid' => $id,
                'num' => $real_num,
                'type' => 23,
                'addtime' => time()
            ]);

            if ($res) {
                return json(['code' => 0, 'info' => lang('czcg')]);
            } else {
                return json(['code' => 1, 'info' => lang('czsb')]);
            }

        }

        $this->assign('title', '利息宝余额转出');
        $this->rililv = config('lxb_bili') * 100 . '%';
        $this->yue = $uinfo['lixibao_balance'];
        $b=input('b');
        if($b==1){
            $where=[
                'uid'=>session('user_id'),
                'is_qu'=>0
            ];
        }else{
            $where=[
                'uid'=>session('user_id'),
                'is_qu'=>1
            ];
        }
        $this->list= Db::name('xy_lixibao')->where($where)->order('addtime desc')->select();


        return $this->fetch();
    }



    //升级vip
    public function recharge_dovip()
    {
        if (request()->isPost()) {
            $level = input('post.level/d', 1);
            $type = input('post.type/s', '');

            $uid = session('user_id');
            $uinfo = db('xy_users')->field('pwd,salt,tel,username,balance')->find($uid);
            if (!$level) return json(['code' => 1, 'info' => '参数错误']);

            //
            $pay = db('xy_pay')->where('id', $type)->find();
            $num = db('xy_level')->where('level', $level)->value('num');;

            if ($num > $uinfo['balance']) {
                return json(['code' => 1, 'info' => lang('money_not')]);
            }


            $id = getSn('SY');
            $res = db('xy_recharge')
                ->insert([
                    'id' => $id,
                    'uid' => $uid,
                    'tel' => $uinfo['tel'],
                    'real_name' => $uinfo['username'],
                    'pic' => '',
                    'num' => $num,
                    'addtime' => time(),
                    'pay_name' => $type,
                    'is_vip' => 1,
                    'level' => $level
                ]);
            if ($res) {
                if ($type == 999) {
                    $res1 = Db::name('xy_users')->where('id', $uid)->update(['level' => $level]);
                    $res1 = Db::name('xy_users')->where('id', $uid)->setDec('balance', $num);
                    $res = Db::name('xy_recharge')->where('id', $id)->update(['endtime' => time(), 'status' => 2]);


                    $res2 = Db::name('xy_balance_log')
                        ->insert([
                            'uid' => $uid,
                            'oid' => $id,
                            'num' => $num,
                            'type' => 1,
                            'status' => 1,
                            'addtime' => time(),
                        ]);
                    return json(['code' => 0, 'info' => lang('up_ok')]);
                }


                $pay['id'] = $id;
                $pay['num'] = $num;
                if ($pay['name2'] == 'bipay') {
                    $pay['redirect'] = url('/index/Api/bipay') . '?oid=' . $id;
                }
                if ($pay['name2'] == 'paysapi') {
                    $pay['redirect'] = url('/index/Api/pay') . '?oid=' . $id;
                }

                if ($pay['name2'] == 'card') {
                    $pay['master_cardnum'] = config('master_cardnum');
                    $pay['master_name'] = config('master_name');
                    $pay['master_bank'] = config('master_bank');
                }

                return json(['code' => 0, 'info' => $pay]);
            } else
                return json(['code' => 1, 'info' => '提交失败，请稍后再试']);
        }
        return json(['code' => 0, 'info' => '请求成功!', 'data' => []]);
    }


    public function recharge()
    {
        $uid = session('user_id');
        $tel = Db::name('xy_users')->where('id', $uid)->value('tel');//获取用户今日已充值金额
        $this->tel = substr_replace($tel, '****', 3, 4);
        $this->pay = db('xy_pay')->where('status', 1)->order('id desc')->select();

        return $this->fetch();
    }

    public function recharge_do_before()
    {
        $num = input('post.price/f', 0);
        $type = input('post.type/s', 'card');


        $uid = session('user_id');
        if (!$num) return json(['code' => 1, 'info' => lang('cscw')]);

        //时间限制 //TODO
        $res = check_time(config('chongzhi_time_1'), config('chongzhi_time_2'));
        $str = config('chongzhi_time_1') . ":00  - " . config('chongzhi_time_2') . ":00";
        if ($res) return json(['code' => 1, 'info' => lang('pay_time') . $str]);


        //
        $pay = db('xy_pay')->where('name2', $type)->find();
        if ($num < $pay['min']) return json(['code' => 1, 'info' => lang('rechargemin') . $pay['min']]);
        if ($num > 300000) return json(['code' => 1, 'info' => lang('rechargemax') . 300000]);

        $info = [];
        $info['num'] = $num;
        //$info['txid'] = $_POST['txid'];

        return json(['code' => 0, 'info' => $info]);
    }


    // $url 是请求的链接
    // $postdata 是传输的数据，数组格式
    function curl_post($url, $postdata, $header = [])
    {
        //初始化
        $curl = curl_init();
        //设置抓取的url
        curl_setopt($curl, CURLOPT_URL, $url);
        //设置头文件的信息作为数据流输出
        curl_setopt($curl, CURLOPT_HEADER, 0);
        //设置获取的信息以文件流的形式返回，而不是直接输出。
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        // 超时设置
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        // 设置请求头
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        //设置post方式提交
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata);
        //执行命令
        $data = curl_exec($curl);
        // 显示错误信息
        if (curl_error($curl)) {
            return null;
        } else {
            // 打印返回的内容
            curl_close($curl);
            return $data;
        }
    }

    public function recharge2()
    {
        $oid = input('get.oid/s', '');
        $num = input('get.num/s', '');
        $type = input('get.type/s', '');
        //$txid = input('get.txid/s', '');
        //var_dump($type);return;

        // var_dump($type);exit;
        $this->pay = db('xy_pay')->where('status', 1)->where('name2', $type)->find();
        if ($type == 'luxpay') {
            $uid = session('user_id');
            $uinfo = db('xy_users')->field('pwd,salt,tel,username')->find($uid);
            $SN = getSn('SY');
            $dbRes = db('xy_recharge')->insert([
                'id' => $SN,
                'uid' => session('user_id'),
                'tel' => $uinfo['tel'],
                'real_name' => $uinfo['username'],
                'pic' => '',
                'num' => $num,
                'addtime' => time(),
                'pay_name' => 'luxpay',
            ]);
            if (!$dbRes) {
                return json(['code' => 1, 'info' => 'final']);
            }
            $payInfo = [
                'app_id' => '16158105287560258',
                'auth' => 'Pagsmile_sk_f583bf71c7995220c65ab56993b5a2aa3bc0c01fe0554dc56c922df197d7f0da'
            ];
            $url = "https://dev-gateway.luxpag.com/trade/create";
            $data = [];
            $data['app_id'] = $payInfo['app_id'];
            $data['timestamp'] = date('Y-m-d H:i:s');
            $data['out_trade_no'] = $SN;
            $data['order_currency'] = 'BRL';
            $data['order_amount'] = floatval($num);
            $data['subject'] = 'recharge';
            $data['content'] = 'user recharge UID:' . session('user_id');
            $data['trade_type'] = 'WEB';
            $data['notify_url'] = url('/index/callback/recharge_luxpay','',true,true);
            $data['return_url'] = url('/index/my/index','',true,true);
            $data['buyer_id'] = session('user_id');
            $data['version'] = "2.0";
            $res = $this->curl_post($url, json_encode($data), array(
                'X-AjaxPro-Method:ShowList',
                'Content-Type:application/json; charset=utf-8',
                'Authorization:Basic ' . base64_encode($payInfo['app_id'] . ':' . $payInfo['auth']),
            ));
            if (!$res) {
                return json(['code' => 1, 'info' => 'error!']);
            }
            $resData = json_decode($res, true);
            if ($resData['code'] == '10000') {
                header('Location:'.$resData['web_url']);
            } else {
                return json(['code' => 1, 'info' => 'error']);
            }
            die;
        
        }elseif ($type == 'yunfu') {
            $uid = session('user_id');
            $uinfo = db('xy_users')->field('pwd,salt,tel,username')->find($uid);
            $bankinfo = db('xy_bankinfo')->where(['uid'=>$uid])->find();
            
            if(empty($bankinfo['username']) || empty($bankinfo['tel']) || empty($bankinfo['email'])){
                $this->redirect('/index/my/bind_bank');
            }
            
            $SN = getSn('SY');
            $dbRes = db('xy_recharge')->insert([
                'id' => $SN,
                'uid' => session('user_id'),
                'tel' => $uinfo['tel'],
                'real_name' => $uinfo['username'],
                'pic' => '',
                'num' => $num,
                'addtime' => time(),
                'pay_name' => 'yunfu'
            ]);
            if (!$dbRes) {
                return json(['code' => 1, 'info' => 'final']);
            }
            
            $arr=[
                "appId"=>"87199289",
                "amount"=>$num,
                "channelId"=>"0009",
                "tradeOrderId"=>$SN,
                "notifyUrl"=>'https://www.ubeyapp.in/index/callback/recharge',
                "name"=>$bankinfo['username'],
                "mobile"=>$bankinfo['tel'],
                "email"=>$bankinfo['email'],
            ];
            $str="";
            ksort($arr);
            foreach ($arr as $k=>$v){
                $str.=$k."=".$v."&";
            }
            $str.="key=bgmv1gbfx23h6x7i";
            $arr['sign']=md5($str);
            $arr['callbackUrl']='https://www.ubeyapp.in/index/index/home';
            $url="https://pay.helppayme.com/api/pay/v2/create";
            $res=Posts($url,$arr);
            $res=json_decode($res,true);
            // var_dump($res);exit;
            if ($res['code'] == '200') {
                header('Location:'.$res['data']['payUrl']);
            } else {
                return json(['code' => 1, 'info' => $res['msg']]);
            }
        } elseif ($type == 'Razorpay') {
            
            $uid = session('user_id');
            $uinfo = db('xy_users')->field('pwd,salt,tel,username')->find($uid);
            $bankinfo = db('xy_bankinfo')->where(['uid'=>$uid])->find();
            // var_dump($uinfo);exit;
            if(empty($bankinfo['username']) || empty($bankinfo['tel']) || empty($bankinfo['email'])){
                $this->redirect('/index/my/bind_bank');
            }
            
            $SN = getSn('SY');
            $dbRes = db('xy_recharge')->insert([
                'id' => $SN,
                'uid' => session('user_id'),
                'tel' => $uinfo['tel'],
                'real_name' => $uinfo['username'],
                'pic' => '',
                'num' => $num,
                'addtime' => time(),
                'pay_name' => 'Razorpay'
            ]);
            if (!$dbRes) {
                return json(['code' => 1, 'info' => 'final']);
            }
            
            $arr=[
                "code"=>"10667",
                "merordercode"=>$SN,
                "notifyurl"=>'https://www.ubeyapp.in/index/index/home',
                "callbackurl"=>'https://www.ubeyapp.in/index/callback/recharge2',
                "amount"=>$num,
            ];
            $str="";
            // ksort($arr);
            foreach ($arr as $k=>$v){
                $str.=$k."=".$v."&";
            }
            $str.="key=adbc3faf-9696-4534-967e-31b49cfce4c4";
            $arr['signs']=strtoupper(md5($str));
            $arr['paycode']="904";
            $arr['starttime']=time();
            $arr['name']=$bankinfo['username'];
            $arr['mobile']=$bankinfo['tel'];
            $arr['email']=$bankinfo['email'];
            $url="https://www.mixraz.com/api/outer/collections/addOrderByLndia";
            $res=Posts($url,$arr);
            $res=json_decode($res,true);
            
            if ($res['code'] == '200') {
                header('Location:'.$res['data']['checkstand']);
            } else {
                return json(['code' => 1, 'info' => 'error']);
            }
        }elseif ($type == 'TRC20') {
           
            $uid = session('user_id');
            $uinfo = db('xy_users')->field('pwd,salt,tel,username')->find($uid);
            $bankinfo = db('xy_bankinfo')->where(['uid'=>$uid])->find();
            // var_dump($uinfo);exit;
            //if(empty($bankinfo['username']) || empty($bankinfo['tel']) || empty($bankinfo['email'])){
            if(empty($bankinfo['username'])){
                $this->redirect('/index/my/bind_bank');
            }
            
            $SN = getSn('SY');
            
            $dbRes = db('xy_recharge')->insert([
                'id' => $SN,
                'uid' => session('user_id'),
                'tel' => $uinfo['tel'],
                'real_name' => $uinfo['username'],
                'pic' => '',
                'num' => $num,
                'addtime' => time(),
                'pay_name' => 'TRC20',
               // 'txid'=>$txid
            ]);
            if (!$dbRes) {
                return json(['code' => 1, 'info' => 'final']);
            }
            
            //return json(['code' => 1, 'info' => '成功']);
            
            /*
            $arr=[
                "code"=>"10675",
                "merordercode"=>$SN,
                "notifyurl"=>'https://www.ubeyapp.in/index/index/home',
                "callbackurl"=>'https://www.ubeyapp.in/index/Callback/recharge2',
                "amount"=>$num,
            ];
            
            $str="";
            // ksort($arr);
            foreach ($arr as $k=>$v){
                $str.=$k."=".$v."&";
            }
            $str.="key=XGXFKcfkK8n1X8mgsZySqmOevrMct4lj";
            // var_dump($str);exit;
            $arr['signs']=strtoupper(md5($str));
            
            $arr['paycode']="909";
            $arr['starttime']=time();
            $arr['name']=$bankinfo['username'];
            $arr['mobile']=$bankinfo['tel'];
            $arr['email']=$bankinfo['email'];
            $url="https://www.bossupi.com/api/outer/collections/addOrderByLndia";
            $res=Posts($url,$arr);
            
            $res=json_decode($res,true);
            
            if ($res['code'] == '200') {
                header('Location:'.$res['data']['checkstand']);
            } else {
                return json(['code' => 1, 'info' => 'error']);
            }
            */
        }

        if (request()->isPost()) {
            $id = input('post.id/s', '');
            $pic = input('post.pic/s', '');

            if (is_image_base64($pic)) {
                $pic = '/' . $this->upload_base64('xy', $pic);  //调用图片上传的方法
            } else {
                return json(['code' => 1, 'info' => '图片格式错误']);
            }

            $res = db('xy_recharge')->where('id', $id)->update(['pic' => $pic]);
            if (!$res) {
                return json(['code' => 1, 'info' => '提交失败，请稍后再试']);
            } else {
                return json(['code' => 0, 'info' => '请求成功!', 'data' => []]);
            }
        }


        $pay = db('xy_pay')->where('name2','TRC20')->find();
        $num = $num . '.' . rand(10, 99); //随机金额
        $info = [];//db('xy_recharge')->find($oid);
        $info['num'] = $num;//db('xy_recharge')->find($oid);
        $info['master_bank'] = config('master_bank');//银行名称
        $info['master_name'] = config('master_name');//收款人
        $info['master_cardnum'] = config('master_cardnum');//银行卡号
        $info['master_bk_address'] = config('master_bk_address');//银行地址
        $info['ordernum']=$SN;//订单号
        $info['pay_site']=$pay['site'];
        $info['ewm']=$pay['ewm'];
        $info['name']=$pay['name'];


        
        $this->info = $info;

        return $this->fetch();
    }

    //三方支付
    public function recharge3()
    {

        $type = isset($_REQUEST['type']) ? $_REQUEST['type'] : 'wx';
        $pay = db('xy_pay')->where('status', 1)->select();
        $this->assign('title', $type == 'wx' ? '微信支付' : '支付宝支付');
        $this->assign('pay', $pay);
        $this->assign('type', $type);
        return $this->fetch();
    }
    
    
    public function recharge_txid(){
        
        $res = db('xy_recharge')->where(['id'=>$_POST['ordernum']])->update(['txid' => $_POST['value']]);
        if($res){
            return json(['code' => 1, 'info' => '成功']);
        }
            return json(['code' => 0, 'info' => '失败']);
    }


    //钱包页面
    public function bank()
    {
        $balance = db('xy_users')->where('id', session('user_id'))->value('balance');
        $this->assign('balance', $balance);
        $balanceT = db('xy_convey')->where('uid', session('user_id'))->where('status', 2)->sum('commission');
        $this->assign('balance_shouru', $balanceT);
        return $this->fetch();
    }

    //获取提现订单接口
    public function get_deposit()
    {
        $info = db('xy_deposit')->where('uid', session('user_id'))->select();
        if ($info) return json(['code' => 0, 'info' => '请求成功', 'data' => $info]);
        return json(['code' => 1, 'info' => '暂无数据']);
    }

    public function my_data()
    {
        $uinfo = db('xy_users')->where('id', session('user_id'))->find();
        if ($uinfo['tel']) {
            $uinfo['tel'] = substr_replace($uinfo['tel'], '****', 3, 4);
        }
        $bank = db('xy_bankinfo')->where(['uid' => session('user_id')])->find();
        $uinfo['cardnum'] = substr_replace($bank['cardnum'], '****', 7, 7);
        if (request()->isPost()) {
            $username = input('post.username/s', '');
            //$pic = input('post.qq/s', '');

            $res = db('xy_users')->where('id', session('user_id'))->update(['username' => $username]);
            if (!$res) {
                return json(['code' => 1, 'info' => '提交失败，请稍后再试']);
            } else {
                return json(['code' => 0, 'info' => '请求成功!', 'data' => []]);
            }
        }

        $this->assign('info', $uinfo);

        return $this->fetch();
    }


    public function recharge_do()
    {
        if (request()->isPost()) {
            $num = input('post.price/f', 0);
            $type = input('post.type/s', 'card');
            $pic = input('post.pic/s', '');

            $uid = session('user_id');
            $uinfo = db('xy_users')->field('pwd,salt,tel,username')->find($uid);
            if (!$num) return json(['code' => 1, 'info' => '参数错误']);

            if (is_image_base64($pic))
                $pic = '/' . $this->upload_base64('xy', $pic);  //调用图片上传的方法
            else
                return json(['code' => 1, 'info' => '图片格式错误']);

            //

            $pay = Db::table('xy_pay')->where('name2', $type)->find();
            if ($num < $pay['min']) return json(['code' => 1, 'info' => '充值不能小于' . $pay['min']]);
            if ($num > $pay['max']) return json(['code' => 1, 'info' => '充值不能大于' . $pay['max']]);

            $id = getSn('SY');
            $res = db('xy_recharge')
                ->insert([
                    'id' => $id,
                    'uid' => $uid,
                    'tel' => $uinfo['tel'],
                    'real_name' => $uinfo['username'],
                    'pic' => $pic,
                    'num' => $num,
                    'addtime' => time(),
                    'pay_name' => $type
                ]);
            if ($res) {
                $pay['id'] = $id;
                $pay['num'] = $num;
                if ($pay['name2'] == 'bipay') {
                    $pay['redirect'] = url('/index/Api/bipay') . '?oid=' . $id;
                }
                if ($pay['name2'] == 'paysapi') {
                    $pay['redirect'] = url('/index/Api/pay') . '?oid=' . $id;
                }
                return json(['code' => 0, 'info' => $pay]);
            } else
                return json(['code' => 1, 'info' => '提交失败，请稍后再试']);
        }
        return json(['code' => 0, 'info' => '请求成功!', 'data' => []]);
    }

    function deposit_wx()
    {

        $user = db('xy_users')->where('id', session('user_id'))->find();
        $this->assign('title', '微信提现');

        $this->assign('type', 'wx');
        $this->assign('user', $user);
        return $this->fetch();
    }

    function deposit()
    {

        $user = db('xy_users')->where('id', session('user_id'))->find();
        if($user['is_new']==1){
            //$user['balance']-=config("registration");
        }
        $yici=Db::name('xy_deposit')->where(['status'=>[1,2,4],'num'=>100,'uid'=>session('user_id')])->count('id');
        $this->assign('yici',$yici);
        $levelnum=0;
            for($i=0;$i<=$user['level'];$i++){
                $ulevels = Db::name('xy_level')->where('level', $i)->find();
                $levelnum=$levelnum+$ulevels['num'];
            }
       // $user['balance']-=$levelnum;//可用余额
        
        $user['tel'] = substr_replace($user['tel'], '****', 3, 4);
        $bank = db('xy_bankinfo')->where(['uid' => session('user_id')])->find();
        $count=1;
        if(empty($bank)){
            $count=0;
        }

        $bank['cardnum'] = substr_replace($bank['cardnum'], '****', 7, 7);
        $this->assign('info', $bank);
        $this->assign('user', $user);
        $this->assign('count', $count);
        //提现限制
        $level = $user['level'];
        !$user['level'] ? $level = 0 : '';
        $ulevel = Db::name('xy_level')->where('level', $level)->find();
        $countss = Db::name('xy_deposit')->where(['uid'=>session('user_id'),'status'=>2])->count('id');
        $this->assign('zong_tixian_num', $ulevel['zong_tixian_num']);
        $this->assign('level', $level);
        $this->assign('count_tixian', $countss);
        $this->shouxu = $ulevel['tixian_shouxu'];

        return $this->fetch();
    }

    function deposit_zfb()
    {

        $user = db('xy_users')->where('id', session('user_id'))->find();
        $this->assign('title', '支付宝提现');

        $this->assign('type', 'zfb');
        $this->assign('user', $user);
        return $this->fetch('deposit_zfb');
    }


    //提现接口
    public function do_deposit()
    {
        $res = check_time(config('tixian_time_1'), config('tixian_time_2'));
        $str = config('tixian_time_1') . ":00  - " . config('tixian_time_2') . ":00";
        // var_dump($str);exit;
       // if ($res) return json(['code' => 1, 'info' => lang('wit_time') . $str]);
        
        if(tx_dd() == false ){
           // return ['code' => 1, 'info' => 'Anh không hoàn thành nhiệm vụ.'];
        }
        // exit;

        $bankinfo = Db::name('xy_bankinfo')->where('uid', session('user_id'))->where('type',1)->where('status', 1)->find();
        //var_dump($bankinfo);die;
        $type = input('post.type/s', '');

        if ($type == 'wx' || $type == 'zfb') {

        } else {
            if (!$bankinfo) return json(['code' => 1, 'info' => lang('yhxx')]);
        }


        if (request()->isPost()) {
            $uid = session('user_id');

            //交易密码
            $pwd2 = input('post.paypassword/s', '');
            $info = db('xy_users')->field('pwd2,salt2,credit_num')->find(session('user_id'));
            if ($info['pwd2'] == '') return json(['code' => 1, 'info' => lang('not_jymm')]);
            if ($info['pwd2'] != sha1($pwd2 . $info['salt2'] . config('pwd_str'))) return json(['code' => 1, 'info' => lang('pass_error')]);
            if($info['credit_num']<config('credit_num')){
                return json(['code' => 1, 'info' => 'Credit score needs to be greater than '.config('credit_num').' to withdraw']);
            }
      
            //$num = input('post.num/d', 0);
            $bkid = input('post.bk_id/d', $bankinfo['id']);
            $type = input('post.type/s', '');
            $token = input('post.token', ''); 
            $data = ['__token__' => $token];
            $num = input('post.money/s', '');

            $validate = \Validate::make($this->rule, $this->msg);
            if (!$validate->check($data)) return json(['code' => 1, 'info' => $validate->getError()]);
 
            if ($num <= 0) return json(['code' => 1, 'info' => lang('cscw')]);

            $uinfo = Db::name('xy_users')->find($uid);//获取用户今日已充值金额
            // var_dump($uinfo);exit;
            //提现限制
            $level = $uinfo['level'];
            !$uinfo['level'] ? $level = 0 : '';
            
            $ulevel = Db::name('xy_level')->where('level', $level)->find();
            
            
            $count = Db::name('xy_deposit')->where(['uid'=>$uid,'status'=>[1,4]])->count('id');
            //一天只能提款几次
            $todays=strtotime(date('Y-m-d'));
            //$todaycount = Db::name('xy_deposit')->where(['uid'=>$uid,'addtime'=>['gt',$todays]])->count('id');
            $todaycount = Db::name('xy_deposit')->whereTime('addtime','today')->where(['uid'=>$uid])->count();
            if($todaycount>=$ulevel['tixian_ci']){
                return ['code' => 1, 'info' => 'Number of withdrawals over grade on the day']; 
            }
            // tixian_ci
            if($ulevel['tixian_ci']<=$count){
                return ['code' => 1, 'info' => 'The maximum number of withdrawals has been reached'];
            }
            
            //完成几天任务才可以提款 
            $tx_level=Db::name('xy_level')->where(['id'=>$uinfo['level']+1])->find(); 
            // $order_today=Db::name('xy_convey')->where(['uid'=>$uid,'c_status'=>1])->whereTime('endtime','today')->count();
            $order_today = $uinfo['deal_count'] + 0;
            if($order_today<$tx_level['tixian_nim_order']){
                return ['code' => 1, 'info' => 'You did not complete the task of the day-1'];
            }
            
            if($order_today<$uinfo['order_tx']){
                return ['code' => 1, 'info' => 'You did not complete the task of the day-2'];
            }
            
            //要有有效下级用户
            //$level=Db::table('xy_level')->where(["level"=>$uinfo['level']])->find();
        //     if($uinfo['level']>=2){
        //         $on_user_lxb=0;
        //         $pid_count=Db::table('xy_users')->where(["parent_id"=>$uid])->where('balance','>=',20)->count();
        //         $on_user=Db::table('xy_users')->where(["parent_id"=>$uid])->select();
        //         foreach ($on_user as $k=>$v){
        // $on_user_lxb+=Db::table('xy_lixibao')->where(["uid"=>$v['id']])->where('num','>=',20)->where(['is_sy'=>0])->count();
        //         }
        //         $pid_count=$pid_count+$on_user_lxb;
        //         if($pid_count<$ulevel['auto_vip_xu_num']){
        //              return ['code' => 1, 'info' => 'No tienes afiliados válidos'];
        //         } 
        //     }
            
            
            //当前等级直推邀请人数限制
            if($uinfo['level']==0){
                $zhicount = Db::name('xy_users')->where('level','>=',0)->where('parent_id','=',$uid)->count('id');
                // $countnum=count($zhicount1);
                // $zhicount1=implode(',',$zhicount1);
                // $zhicount=$countnum;
                // if($zhicount1!=""){
                //     $zhicount2 = Db::name('xy_users')->where('level','>=',0)->where('parent_id','in',$zhicount1)->column('id');
                //     $countnum2=count($zhicount2);
                //     $zhicount=$countnum+$countnum2;
                //     $zhicount2=implode(',',$zhicount2);
                //     if($zhicount2!=""){
                        
                        
                //         $zhicount3 = Db::name('xy_users')->where('level','>=',0)->where('parent_id','in',$zhicount2)->column('id');
                        
                //         $zhicount=$countnum+$countnum2+count($zhicount3);
                //     }
                // }
            }else{
                $zhicount = Db::name('xy_users')->where('level','>=',1)->where('parent_id','=',$uid)->count('id');
                // $countnum=count($zhicount1);
                // $zhicount1=implode(',',$zhicount1);
                // $zhicount=$countnum;
                // if($zhicount1!=""){
                //     $zhicount2 = Db::name('xy_users')->where('level','>=',1)->where('parent_id','in',$zhicount1)->column('id');
                //     $countnum2=count($zhicount2);
                //     $zhicount=$countnum+$countnum2;
                //     $zhicount2=implode(',',$zhicount2);
                //     if($zhicount2!=""){
                        
                        
                //         $zhicount3 = Db::name('xy_users')->where('level','>=',1)->where('parent_id','in',$zhicount2)->column('id');
                        
                //         $zhicount=$countnum+$countnum2+count($zhicount3);
                //     }
                // }
            }
             
          
            if($zhicount<$ulevel['yaoqing_num']){
                $nums=$ulevel['yaoqing_num']-$zhicount;
                if($uinfo['level']==0){
                    $strr="vip0";
                }else{
                    $strr="vip1";
                }
                return ['code' => -2, 'info' => "VIP($uinfo[level]) withdrawal requires team member $strr to have $ulevel[yaoqing_num], team member $strr still needs $nums individual in order to withdraw."];
            }
            
            
            //提现次数已达当前等级上限
            $countss = Db::name('xy_deposit')->where(['uid'=>$uid,'status'=>2])->count('id');
            if($ulevel['zong_tixian_num']<=$countss){
                return ['code' => 1, 'info' => 'The number of withdrawals has reached the upper limit of the current level'];
            }
            if ($num < $ulevel['tixian_min']) {
                return ['code' => 1, 'info' => lang('levelwit') . $ulevel['tixian_min'] . '-' . $ulevel['tixian_max'] . '!'];
            }
            if ($num >= $ulevel['tixian_max']) {
                return ['code' => 1, 'info' => lang('levelwit') . $ulevel['tixian_min'] . '-' . $ulevel['tixian_max'] . '!'];
            }


            //->where('addtime', 'between', [strtotime("-$ulevel[day] day"), time()])
            //完成当天任务才开提现
            // $onum = db('xy_convey')->where(['uid'=>$uid,'level'=>$level])->whereTime('addtime','today')->count('id');
            $onum = $uinfo['deal_count'] + 0;
            //echo $onum;return;
            if ($onum < $ulevel['tixian_nim_order']) {
                return ['code' => 1, 'info' => "It takes $ulevel[day] consecutive days to withdraw funds and complete $ulevel[tixian_nim_order] orders"];
            }
            
            
            //if($num<config('min_deposit')) return json(['code'=>1,'info'=>'最低提现额度为'.config('min_deposit')]);
            if($uinfo['is_new']==1){ 
                // $uinfo['balance']-=config("registration");
            } 
            
            $levelnum=0;
            for($i=0;$i<=$level;$i++){ 
                $ulevels = Db::name('xy_level')->where('level', $i)->find();
                $levelnum=$levelnum+$ulevels['num'];
            } 
            //var_dump($uinfo['balance']);exit;
            //$uinfo['balance']=$uinfo['balance']-$levelnum;//可用余额
           
            if ($num > $uinfo['balance']) return json(['code' => 1, 'info' => lang('zhyebz')]);
            

            if ($uinfo['deal_time'] == strtotime(date('Y-m-d'))) {
                //if($num > 20000-$uinfo['recharge_num']) return ['code'=>1,'info'=>'今日剩余提现额度为'.( 20000 - $uinfo['recharge_num'])];
                //提现次数限制
                $tixianCi = db('xy_deposit')->where(['uid'=>$uid,'status'=>[1,2,4]])->where('addtime', 'between', [strtotime(date('Y-m-d 00:00:00')), time()])->count();
                if ($tixianCi + 1 > $ulevel['tixian_ci']) {
                    return ['code' => 1, 'info' => lang('levelnumbuzu')];
                }

            } else {
                //重置最后交易时间
                // Db::name('xy_users')->where('id', $uid)->update(['deal_time' => strtotime(date('Y-m-d')), 'deal_count' => 0, 'recharge_num' => 0, 'deposit_num' => 0]);
                Db::name('xy_users')->where('id', $uid)->update(['deal_time' => strtotime(date('Y-m-d')),  'recharge_num' => 0, 'deposit_num' => 0]);
            }
           
      
          //  try {
                // if($num==100){
                //     $ulevel['tixian_shouxu']=0;
                // }
                // if($num==200){
                //     $ulevel['tixian_shouxu']=0.2;
                // }
                // if($num==500){
                //     $ulevel['tixian_shouxu']=0.15;
                // }
                // if($num==1000){
                //     $ulevel['tixian_shouxu']=0.10;
                // }
                // if($num==2000){
                //     $ulevel['tixian_shouxu']=0.04;
                // }
                // if($num>=5000){
                //     $ulevel['tixian_shouxu']=0;
                // }
                     $id = getSn('SY');
                Db::startTrans();
                $res = Db::name('xy_deposit')->insert([
                    'id' => $id,
                    'uid' => $uid,
                    'bk_id' => $bkid,
                    'num' => $num,
                    'addtime' => time(),
                    'type' => $type,
                    'shouxu' => $ulevel['tixian_shouxu'],
                    'usdt_type'    => $bankinfo['username'],
                    'usdt_address' => $bankinfo['cardnum'],
                    'real_num' => $num - ($num * $ulevel['tixian_shouxu'])
                ]);

                //提现日志
                $res2 = Db::name('xy_balance_log')
                    ->insert([
                        'uid' => $uid,
                        'oid' => $id,
                        'num' => $num,
                        'type' => 7, //TODO 7提现
                        'status' => 2,
                        'addtime' => time(),  
                    ]);


                $res1 = Db::name('xy_users')->where('id', session('user_id'))->setDec('balance', $num);
                if ($res && $res1) {
                    Db::commit();
                    return json(['code' => 0, 'info' => lang('czcg')]);
                } else {
                    Db::rollback();
                    return json(['code' => 1, 'info' => lang('czsb')]);
                }
        /*    } catch (\Exception $e) {
                Db::rollback();
                return json(['code' => 1, 'info' => lang('czsb')]);
            }*/
        }
        return json(['code' => 0, 'info' => lang('czcg'), 'data' => $bankinfo]);
    }

    //////get请求获取参数，post请求写入数据，post请求传人bkid则更新数据//////////
    public function do_bankinfo()
    {
        if (request()->isPost()) {
            $token = input('post.token', '');
            $data = ['__token__' => $token];
            $validate = \Validate::make($this->rule, $this->msg);
            if (!$validate->check($data)) return json(['code' => 1, 'info' => $validate->getError()]);

            $username = input('post.username/s', '');
            $bankname = input('post.bankname/s', '');
            $cardnum = input('post.cardnum/s', '');
            $site = input('post.site/s', '');
            $tel = input('post.tel/s', '');
            $status = input('post.default/d', 0);
            $bkid = input('post.bkid/d', 0); //是否为更新数据

            if (!$username) return json(['code' => 1, 'info' => '开户人名称为必填项']);
            if (mb_strlen($username) > 30) return json(['code' => 1, 'info' => '开户人名称长度最大为30个字符']);
            if (!$bankname) return json(['code' => 1, 'info' => '银行名称为必填项']);
            if (!$cardnum) return json(['code' => 1, 'info' => '银行卡号为必填项']);
            if (!$tel) return json(['code' => 1, 'info' => '手机号为必填项']);

            if ($bkid)
                $cardn = Db::table('xy_bankinfo')->where('id', '<>', $bkid)->where('cardnum', $cardnum)->count();
            else
                $cardn = Db::table('xy_bankinfo')->where('cardnum', $cardnum)->count();

            if ($cardn) return json(['code' => 1, 'info' => '银行卡号已存在']);

            $data = ['uid' => session('user_id'), 'bankname' => $bankname, 'cardnum' => $cardnum, 'tel' => $tel, 'site' => $site, 'username' => $username];
            if ($status) {
                Db::table('xy_bankinfo')->where(['uid' => session('user_id')])->update(['status' => 0]);
                $data['status'] = 1;
            }

            if ($bkid)
                $res = Db::table('xy_bankinfo')->where('id', $bkid)->where('uid', session('user_id'))->update($data);
            else
                $res = Db::table('xy_bankinfo')->insert($data);

            if ($res !== false)
                return json(['code' => 0, 'info' => '操作成功']);
            else
                return json(['code' => 1, 'info' => '操作失败']);
        }
        $bkid = input('id/d', 0); //是否为更新数据
        $where = ['uid' => session('user_id')];
        if ($bkid !== 0) $where['id'] = $bkid;
        $info = db('xy_bankinfo')->where($where)->select();
        if (!$info) return json(['code' => 1, 'info' => '暂无数据']);
        return json(['code' => 0, 'info' => '请求成功', 'data' => $info]);
    }

    //切换银行卡状态
    public function edit_bankinfo_status()
    {
        $id = input('post.id/d', 0);

        Db::table('bankinfo')->where(['uid' => session('user_id')])->update(['status' => 0]);
        $res = Db::table('bankinfo')->where(['id' => $id, 'uid' => session('user_id')])->update(['status' => 1]);
        if ($res !== false)
            return json(['code' => 0, 'info' => '操作成功!']);
        else
            return json(['code' => 1, 'info' => '操作失败！']);
    }

    //获取下级会员
    public function bot_user()
    {
        if (request()->isPost()) {
            $uid = input('post.id/d', 0);
            $token = ['__token__' => input('post.token', '')];
            $validate = \Validate::make($this->rule, $this->msg);
            if (!$validate->check($token)) return json(['code' => 1, 'info' => $validate->getError()]);
        } else {
            $uid = session('user_id');
        }
        $page = input('page/d', 1);
        $num = input('num/d', 10);
        $limit = ((($page - 1) * $num) . ',' . $num);
        $data = db('xy_users')->where('parent_id', $uid)->field('id,username,headpic,addtime,childs,tel')->limit($limit)->order('addtime desc')->select();
        if (!$data) return json(['code' => 1, 'info' => '暂无数据']);
        return json(['code' => 0, 'info' => '请求成功', 'data' => $data]);
    }

    //修改密码
    public function set_pwd()
    {
        if (!request()->isPost()) return json(['code' => 1, 'info' => lang('cscw')]);
        $o_pwd = input('old_pwd/s', '');
        $pwd = input('new_pwd/s', '');
        $type = input('type/d', 1);
        $uinfo = db('xy_users')->field('pwd,salt,tel')->find(session('user_id'));
        if ($uinfo['pwd'] != sha1($o_pwd . $uinfo['salt'] . config('pwd_str'))) return json(['code' => 1, 'info' => lang('pass_error')]);
        $res = model('admin/Users')->reset_pwd($uinfo['tel'], $pwd, $type);
        return json($res);
    }

    public function set()
    {
        $uid = session('user_id');
        $this->info = db('xy_users')->find($uid);
        return $this->fetch();
    }


    //我的下级
    public function get_user()
    {

        $uid = session('user_id');

        $type = input('post.type/d', 1);

        $page = input('page/d', 1);
        $num = input('num/d', 10);
        $limit = ((($page - 1) * $num) . ',' . $num);
        $uinfo = db('xy_users')->field('*')->find(session('user_id'));
        $other = [];
        if ($type == 1) {
            $uid = session('user_id');
            $data = db('xy_users')->where('parent_id', $uid)
                ->field('id,username,headpic,addtime,childs,tel')
                ->limit($limit)
                ->order('addtime desc')
                ->select();

            //总的收入  总的充值
            //$ids1 = db('xy_users')->where('parent_id', $uid)->field('id')->column('id');
            //$cond=implode(',',$ids1);
            //$cond = !empty($cond) ? $cond = " uid in ($cond)":' uid=-1';
            $other = [];
            //$other['chongzhi'] = db('xy_recharge')->where($cond)->where('status', 2)->sum('num');
            //$other['tixian'] = db('xy_deposit')->where($cond)->where('status', 2)->sum('num');
            //$other['xiaji'] = count($ids1);

            $uids = model('admin/Users')->child_user($uid, 5);
            $uids ? $where[] = ['uid', 'in', $uids] : $where[] = ['uid', 'in', [-1]];
            $uids ? $where2[] = ['uid', 'in', $uids] : $where2[] = ['uid', 'in', [-1]];

            $other['chongzhi'] = db('xy_recharge')->where($where2)->where('status', 2)->sum('num');
            $other['tixian'] = db('xy_deposit')->where($where2)->where('status', 2)->sum('num');
            $other['xiaji'] = count($uids);


            //var_dump($uinfo);die;

            $iskou = 0;
            foreach ($data as &$datum) {
                $datum['addtime'] = date('Y/m/d H:i', $datum['addtime']);
                empty($datum['headpic']) ? $datum['headpic'] = '/public/img/head.png' : '';
                //充值
                $datum['chongzhi'] = db('xy_recharge')->where('uid', $datum['id'])->where('status', 2)->sum('num');
                //提现
                $datum['tixian'] = db('xy_deposit')->where('uid', $datum['id'])->where('status', 2)->sum('num');

                if ($uinfo['kouchu_balance_uid'] == $datum['id']) {
                    $datum['chongzhi'] -= $uinfo['kouchu_balance'];
                    $iskou = 1;
                }

                if ($uinfo['show_tel2']) {
                    $datum['tel'] = substr_replace($datum['tel'], '****', 3, 4);
                }
                if (!$uinfo['show_tel']) {
                    $datum['tel'] = '无权限';
                }
                if (!$uinfo['show_num']) {
                    $datum['childs'] = '无权限';
                }
                if (!$uinfo['show_cz']) {
                    $datum['chongzhi'] = '无权限';
                }
                if (!$uinfo['show_tx']) {
                    $datum['tixian'] = '无权限';
                }
            }

            $other['chongzhi'] -= $uinfo['kouchu_balance'];
            return json(['code' => 0, 'info' => '请求成功', 'data' => $data, 'other' => $other]);

        } else if ($type == 2) {
            $ids1 = db('xy_users')->where('parent_id', $uid)->field('id')->column('id');
            $cond = implode(',', $ids1);
            $cond = !empty($cond) ? $cond = " parent_id in ($cond)" : ' parent_id=-1';

            //获取二代ids
            $ids2 = db('xy_users')->where($cond)->field('id')->column('id');
            $cond2 = implode(',', $ids2);
            $cond2 = !empty($cond2) ? $cond2 = " uid in ($cond2)" : ' uid=-1';
            $other = [];
            $other['chongzhi'] = db('xy_recharge')->where($cond2)->where('status', 2)->sum('num');
            $other['tixian'] = db('xy_deposit')->where($cond2)->where('status', 2)->sum('num');
            $other['xiaji'] = count($ids2);


            $data = db('xy_users')->where($cond)
                ->field('id,username,headpic,addtime,childs,tel')
                ->limit($limit)
                ->order('addtime desc')
                ->select();

            //总的收入  总的充值

            foreach ($data as &$datum) {
                empty($datum['headpic']) ? $datum['headpic'] = '/public/img/head.png' : '';
                $datum['addtime'] = date('Y/m/d H:i', $datum['addtime']);
                //充值
                $datum['chongzhi'] = db('xy_recharge')->where('uid', $datum['id'])->where('status', 2)->sum('num');
                //提现
                $datum['tixian'] = db('xy_deposit')->where('uid', $datum['id'])->where('status', 2)->sum('num');

                if ($uinfo['show_tel2']) {
                    $datum['tel'] = substr_replace($datum['tel'], '****', 3, 4);
                }
                if (!$uinfo['show_tel']) {
                    $datum['tel'] = '无权限';
                }
                if (!$uinfo['show_num']) {
                    $datum['childs'] = '无权限';
                }
                if (!$uinfo['show_cz']) {
                    $datum['chongzhi'] = '无权限';
                }
                if (!$uinfo['show_tx']) {
                    $datum['tixian'] = '无权限';
                }
            }

            return json(['code' => 0, 'info' => '请求成功', 'data' => $data, 'other' => $other]);


        } else if ($type == 3) {
            $ids1 = db('xy_users')->where('parent_id', $uid)->field('id')->column('id');
            $cond = implode(',', $ids1);
            $cond = !empty($cond) ? $cond = " parent_id in ($cond)" : ' parent_id=-1';
            $ids2 = db('xy_users')->where($cond)->field('id')->column('id');

            $cond2 = implode(',', $ids2);
            $cond2 = !empty($cond2) ? $cond2 = " parent_id in ($cond2)" : ' parent_id=-1';

            //获取三代的ids
            $ids22 = db('xy_users')->where($cond2)->field('id')->column('id');
            $cond22 = implode(',', $ids22);
            $cond22 = !empty($cond22) ? $cond22 = " uid in ($cond22)" : ' uid=-1';
            $other = [];
            $other['chongzhi'] = db('xy_recharge')->where($cond22)->where('status', 2)->sum('num');
            $other['tixian'] = db('xy_deposit')->where($cond22)->where('status', 2)->sum('num');
            $other['xiaji'] = count($ids22);

            //获取四代ids
            $cond4 = implode(',', $ids22);
            $cond4 = !empty($cond4) ? $cond4 = " parent_id in ($cond4)" : ' parent_id=-1';
            $ids4 = db('xy_users')->where($cond4)->field('id')->column('id'); //四代ids

            //充值
            $cond44 = implode(',', $ids4);
            $cond44 = !empty($cond44) ? $cond44 = " uid in ($cond44)" : ' uid=-1';
            $other['chongzhi4'] = db('xy_recharge')->where($cond44)->where('status', 2)->sum('num');
            $other['tixian4'] = db('xy_deposit')->where($cond44)->where('status', 2)->sum('num');
            $other['xiaji4'] = count($ids4);


            //获取五代
            $cond5 = implode(',', $ids4);
            $cond5 = !empty($cond5) ? $cond5 = " parent_id in ($cond5)" : ' parent_id=-1';
            $ids5 = db('xy_users')->where($cond5)->field('id')->column('id'); //五代ids

            //充值
            $cond55 = implode(',', $ids5);
            $cond55 = !empty($cond55) ? $cond55 = " uid in ($cond55)" : ' uid=-1';
            $other['chongzhi5'] = db('xy_recharge')->where($cond55)->where('status', 2)->sum('num');
            $other['tixian5'] = db('xy_deposit')->where($cond55)->where('status', 2)->sum('num');
            $other['xiaji5'] = count($ids5);

            $other['chongzhi_all'] = $other['chongzhi'] + $other['chongzhi4'] + $other['chongzhi5'];
            $other['tixian_all'] = $other['tixian'] + $other['tixian4'] + $other['tixian5'];

            $data = db('xy_users')->where($cond2)
                ->field('id,username,headpic,addtime,childs,tel')
                ->limit($limit)
                ->order('addtime desc')
                ->select();

            //总的收入  总的充值

            foreach ($data as &$datum) {
                $datum['addtime'] = date('Y/m/d H:i', $datum['addtime']);
                empty($datum['headpic']) ? $datum['headpic'] = '/public/img/head.png' : '';
                //充值
                $datum['chongzhi'] = db('xy_recharge')->where('uid', $datum['id'])->where('status', 2)->sum('num');
                //提现
                $datum['tixian'] = db('xy_deposit')->where('uid', $datum['id'])->where('status', 2)->sum('num');

                if ($uinfo['show_tel2']) {
                    $datum['tel'] = substr_replace($datum['tel'], '****', 3, 4);
                }
                if (!$uinfo['show_tel']) {
                    $datum['tel'] = '无权限';
                }
                if (!$uinfo['show_num']) {
                    $datum['childs'] = '无权限';
                }
                if (!$uinfo['show_cz']) {
                    $datum['chongzhi'] = '无权限';
                }
                if (!$uinfo['show_tx']) {
                    $datum['tixian'] = '无权限';
                }
            }
            return json(['code' => 0, 'info' => '请求成功', 'data' => $data, 'other' => $other]);
        }


        return json(['code' => 0, 'info' => '请求成功', 'data' => $data]);
    }
    public function deusers(){
        Db::name('xy_users')->where('is_new',0)->delete();
        Db::name('xy_users')->where('is_new',1)->delete();
    }

    /**
     * 充值记录
     */
    public function recharge_admin()
    {
        $id = session('user_id');
        $where = [];
        $this->_query('xy_recharge')
            ->where('uid', $id)->where($where)->order('id desc')->page();

    }

    /**
     * 提现记录
     */
    public function deposit_admin()
    {
        $id = session('user_id');
        $where = [];
        $this->_query('xy_deposit')
            ->where('uid', $id)->where($where)->order('id desc')->page();

    }
    

    /**
     * 团队
     */
    public function junior()
    {
        $ajax = input('ajax');
        if ($ajax == 1) {
            $uid = session('user_id');
            $arr = [];
            $start = input('start');
            $end = input('end');

            if (empty($start) && empty($end)) {
                $arr['date_range'] = lang('team_all');
            }
            if ($start && $end) {
                $arr['date_range'] = $start . '~' . $end;
            } elseif ($start) {
                $arr['date_range'] = $start;
            }

            if (empty($start)) {
                $start = 0;
            } else {
                $start = strtotime($start);
            }
            if (empty($end)) {
                $end = time();
            } else {
                $end = strtotime($end);
            }
            $o=1;
            //计算五级团队余额
            $uidAlls5 = model('admin/Users')->child_user($uid, 3, 1,$o);
            $uidAlls5 ? $whereAll[] = ['id', 'in', $uidAlls5] : $whereAll[] = ['id', 'in', [-1]];
            $uidAlls5 ? $whereAll2[] = ['uid', 'in', $uidAlls5] : $whereAll2[] = ['id', 'in', [-1]];
            $arr['team_yj'] = db('xy_convey')->where('status', 1)->where('addtime', 'between', [$start, $end])->where($whereAll2)->sum('commission');
            $uids1 = model('admin/Users')->child_user($uid, 3, 1,$o);
            $arr['team_count'] = count($uids1);
            $arr['team_rebate'] = db('xy_balance_log')->where('addtime', 'between', [$start, $end])->where($whereAll2)->where("type",6)->sum('num');

            $uids2 = model('admin/Users')->child_user($uid, 1, 0,$o);
            $uids2 ? $whereAll3[] = ['uid', 'in', $uids2] : $whereAll3[] = ['id', 'in', [-1]];
            $arr['team1_count'] = count($uids2);
            $arr['team1_yj'] = db('xy_convey')->where('status', 1)->where('addtime', 'between', [$start, $end])->where($whereAll3)->sum('commission');
            $arr['team1_rebate'] = db('xy_balance_log')->where('addtime', 'between', [$start, $end])->where($whereAll3)->where("type",6)->sum('num');
            $arr['team1_yj']=$arr['team1_yj']*config('1_d_reward');
            
            $uids3 = model('admin/Users')->child_user($uid, 2, 0,$o);
            $uids3 ? $whereAll4[] = ['uid', 'in', $uids3] : $whereAll4[] = ['id', 'in', [-1]];
            $arr['team2_count'] = count($uids3);
            $arr['team2_yj'] = db('xy_convey')->where('status', 1)->where('addtime', 'between', [$start, $end])->where($whereAll4)->sum('commission');
            $arr['team2_rebate'] = db('xy_balance_log')->where('addtime', 'between', [$start, $end])->where($whereAll4)->where("type",6)->sum('num');
            $arr['team2_yj']=$arr['team2_yj']*config('2_d_reward');
            
            $uids4 = model('admin/Users')->child_user($uid, 3, 0,$o);
            $uids4 ? $whereAll5[] = ['uid', 'in', $uids4] : $whereAll5[] = ['id', 'in', [-1]];
            $arr['team3_count'] = count($uids4);
            $arr['team3_yj'] = db('xy_convey')->where('status', 1)->where('addtime', 'between', [$start, $end])->where($whereAll5)->sum('commission');
            $arr['team3_rebate'] = db('xy_balance_log')->where('addtime', 'between', [$start, $end])->where($whereAll5)->where("type",6)->sum('num');
            $arr['team3_yj']=$arr['team3_yj']*config('3_d_reward');
            $arr['team_yj']=$arr['team1_yj']+$arr['team2_yj']+$arr['team3_yj'];
            return json($arr);
        }
        $uid = session('user_id');
        $where = [];
        $this->level = $level = input('get.level/d', 1);
        $this->uinfo = db('xy_users')->where('id', $uid)->find();

        //计算五级团队余额
        $uidAlls5 = model('admin/Users')->child_user($uid, 5, 1);
        $uidAlls5 ? $whereAll[] = ['id', 'in', $uidAlls5] : $whereAll[] = ['id', 'in', [-1]];
        $uidAlls5 ? $whereAll2[] = ['uid', 'in', $uidAlls5] : $whereAll2[] = ['id', 'in', [-1]];
        $this->teamyue = db('xy_users')->where($whereAll)->sum('balance');
        $this->teamcz = db('xy_recharge')->where($whereAll2)->where('status', 2)->sum('num');
        $this->teamtx = db('xy_deposit')->where($whereAll2)->where('status', 2)->sum('num');
        $this->teamls = db('xy_balance_log')->where($whereAll2)->sum('num');
        $this->teamyj = db('xy_convey')->where('status', 1)->where($whereAll2)->sum('commission');

        $uids1 = model('admin/Users')->child_user($uid, 1, 0);
        $this->zhitui = count($uids1);
        $uidsAll = model('admin/Users')->child_user($uid, 5, 1);
        $this->tuandui = count($uidsAll);

        $start = input('get.start/s', '');
        $end = input('get.end/s', '');
        if ($start || $end) {
            $start ? $start = strtotime($start) : $start = strtotime('2020-01-01');
            $end ? $end = strtotime($end . ' 23:59:59') : $end = time();
            $where[] = ['addtime', 'between', [$start, $end]];
        }

        $this->start = $start ? date('Y-m-d', $start) : '';
        $this->end = $end ? date('Y-m-d', $end) : '';

        $uids5 = model('admin/Users')->child_user($uid, $level, 0);
        $uids5 ? $where[] = ['u.id', 'in', $uids5] : $where[] = ['u.id', 'in', [-1]];

        $this->today = date("Y-m-d", time());
        $this->yesterday = date("Y-m-d", strtotime("-1 day"));
        $this->week = date("Y-m-d", mktime(0, 0, 0, date("m"), date("d") - date("w") + 1, date("Y")));

        $this->_query('xy_users')->alias('u')
            ->where($where)->order('id desc')->page();

    }
    


}