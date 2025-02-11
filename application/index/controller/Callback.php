<?php

namespace app\index\controller;

use library\Controller;
use think\facade\Request;
use think\Db;

/**
 * 验证登录控制器
 */
class Callback extends Controller
{
    public function yunfu()
    {
        /*$da['suc'] = file_get_contents("php://input");
        $da['suc1'] = serialize(parse_str($_POST));
        $da['suc2'] = serialize($_GET);
        $da['aa'] = date('Y-m-d H:i:s',time());
        $a = Db::name('hs_aaa')->where('id',1)->find();
        $suc = $a['suc'];
        $aa = array();*/
        $suc = file_get_contents("php://input");
        parse_str($suc,$result);
        if(empty($result)){
            exit('回调失败');
        }else{
            if($res['tradeResult'] != 1){
                exit('订单状态不正确');
            }
            $oinfo = Db::name('xy_recharge')->find($result['mchOrderNo']);
            if (!$oinfo) {
                exit('订单不存在');
            }
            if (floatval($result['amount']) != floatval($oinfo['num'])) {
                exit('金额不对');
            }
            if ($oinfo['status'] == 2) {
                exit('success');
            }
            if ($oinfo['status'] != 1) {
                exit('订单已处理');
            }
            Db::startTrans();
            $status = 2;
            $res = Db::name('xy_recharge')->where('id', $result['mchOrderNo'])->update(['endtime' => time(), 'status' => $status]);
            if ($oinfo['is_vip']) {
                $res1 = Db::name('xy_users')->where('id', $oinfo['uid'])->update(['level' => $oinfo['level']]);
            } else {
                $res1 = Db::name('xy_users')->where('id', $oinfo['uid'])->setInc('balance', $oinfo['num']);
            }
            $res2 = Db::name('xy_balance_log')
                ->insert(['uid' => $oinfo['uid'], 'oid' => $result['mchOrderNo'],
                    'num' => $oinfo['num'], 'type' => 1, 'status' => 1, 'addtime' => time(),]);
            if ($res && $res1) {
                Db::commit();

                if (!$oinfo['is_vip']) {
                    /************* 发放推广奖励 *********/
                    $uinfo = Db::name('xy_users')->field('id,active')->find($oinfo['uid']);
                    if ($uinfo['active'] === 0) {
                        Db::name('xy_users')->where('id', $uinfo['id'])->update(['active' => 1]);
                        //将账号状态改为已发放推广奖励
                        $userList = model('Users')->parent_user($uinfo['id'], 3);
                        if ($userList) {
                            foreach ($userList as $v) {
                                if ($v['status'] === 1 && ($oinfo['num'] * config($v['lv'] . '_reward') != 0)) {
                                    Db::name('xy_reward_log')
                                        ->insert([
                                            'uid' => $v['id'],
                                            'sid' => $uinfo['id'],
                                            'oid' => $result['mchOrderNo'],
                                            'num' => $oinfo['num'] * config($v['lv'] . '_reward'),
                                            'lv' => $v['lv'],
                                            'type' => 1,
                                            'status' => 1,
                                            'addtime' => time(),
                                        ]);
                                }
                            }
                        }
                    }
                }
                /************* 发放推广奖励 *********/
                exit('success');
            } else {
                Db::rollback();
                exit();
            }
        }
    }
    
    public function yundaifu()
    {
        $result=input("post.");
        $suc = json_encode($result);
        if(empty($result)){
            exit('回调失败');
        }else{
            $oinfo = Db::name('xy_deposit')->where('id',$result['tradeOrderId'])->find();
            if($result['result'] != "success"){
                if ($oinfo['status'] == 3) {
                    exit('success');
                } 
                Db::name('xy_users')->where('id',$oinfo['uid'])->setInc('balance',$result['amount']);
                Db::name('xy_deposit')->where('id',$result['tradeOrderId'])->update(['status' =>3,'endtime'=>time()]);
                exit('订单状态不正确');
            }
            if (!$oinfo) {
                exit('订单不存在');
            }
            // if (floatval($result['amount']) != floatval($oinfo['real_num'])) {
            //     exit('金额不对');
            // }
            if ($oinfo['status'] == 2) {
                exit('success');
            }
            Db::name('xy_deposit')->where('id',$result['tradeOrderId'])->update(['status' =>2,'endtime'=>time()]);
            Db::name('xy_balance_log')->where('oid',$result['tradeOrderId'])->update(['status'=>1]);
            exit("success");
        }
    }
    public function yundaifu2()
    {
        $result=input("post.");
        $suc = json_encode($result);
        if(empty($result)){
            exit('回调失败');
        }else{
            $oinfo = Db::name('xy_deposit')->where('id',$result['merissuingcode'])->find();
            if($result['returncode'] != "SUCCESS"){
                if ($oinfo['status'] == 3) {
                    exit('订单已处理');
                }
                Db::name('xy_users')->where('id',$oinfo['uid'])->setInc('balance',$result['amount']);
                Db::name('xy_deposit')->where('id',$result['merissuingcode'])->update(['status' =>3,'endtime'=>time()]);
                exit('订单状态不正确');
            }
            if (!$oinfo) {
                exit('订单不存在');
            }
            // if (floatval($result['amount']) != floatval($oinfo['real_num'])) {
            //     exit('金额不对');
            // }
            if ($oinfo['status'] == 2) {
                exit('OK');
            }
            Db::name('xy_deposit')->where('id',$result['merissuingcode'])->update(['status' =>2,'endtime'=>time()]);
            Db::name('xy_balance_log')->where('oid',$result['merissuingcode'])->update(['status'=>1]);
            exit("OK");
        }
    }
    
    public function recharge()
    {
        $data=input("post.");
        $put = json_encode($data);
        $log_file = APP_PATH . 'recharge.log';
        $log_file_final = APP_PATH . 'recharge_final.log';
        file_put_contents($log_file, date('Y-m-d H:i:s') . ': ' . $put . "\n", FILE_APPEND);
        if (!empty($data['tradeOrderId'])) {
            if ($data['result'] != 'success') {
                Db::name('xy_recharge')->where('id', $data['tradeOrderId'])->update(['endtime' => time(), 'status' => 3]);
                exit('success');
                file_put_contents($log_file, '======订单状态不正确!' . "\n", FILE_APPEND);
                http_response_code(500);
                exit;
            }
            $oinfo = Db::name('xy_recharge')->find($data['tradeOrderId']);
            if (!$oinfo) {
                file_put_contents($log_file, '======订单不存在!' . "\n", FILE_APPEND);
                http_response_code(500);
                exit;
            }
            if (floatval($data['amount']) != floatval($oinfo['num'])) {
                file_put_contents($log_file, '======金额不对!' . "\n", FILE_APPEND);
                http_response_code(500);
                exit;
            }
            if ($oinfo['status'] == 2) {
                exit('success');
            }
            if ($oinfo['status'] != 1) {
                file_put_contents($log_file, '======订单已处理!' . "\n", FILE_APPEND);
                http_response_code(500);
                exit;
            }
            //查询是否首充
            $count=Db::name('xy_recharge')->where(['uid'=>$oinfo['uid'],"status"=>2])->count();
            $auomt=$oinfo['num'];
            if($count==0){
                $auomt=$auomt+($auomt*config("firststroke"));
            }
            //查询活动充值
            if($oinfo['num']==config("activity")){
                $auomt=$auomt+($auomt*config("activitynum"));
            }
            $status = 2;
            $res = Db::name('xy_recharge')->where('id', $data['tradeOrderId'])->update(['endtime' => time(), 'status' => $status]);
            $res1 = Db::name('xy_users')->where('id', $oinfo['uid'])->setInc('balance', $auomt);
            $res2 = Db::name('xy_balance_log')
                ->insert(['uid' => $oinfo['uid'], 'oid' => $data['tradeOrderId'],
                    'num' => $auomt, 'type' => 1, 'status' => 1, 'addtime' => time(),]);
            if($count==0){
                $userList = model('admin/Users')->parent_user($oinfo['uid'],3);
                $userss = Db::name('xy_users')->where('id', $oinfo['uid'])->find();
                if($userList){
                    foreach($userList as $v){
                        if($v['level']>=1 && $v['level']>$userss['level']){
                            if($v['status']==1 && ($oinfo['num'] * config($v['lv'].'_reward') != 0)){
                                    $rows=Db::name('xy_reward_log')
                                    ->insert([
                                        'uid'=>$v['id'],
                                        'sid'=>$oinfo['uid'],
                                        'oid'=>$oinfo['id'],
                                        'num'=>$oinfo['num'] * config($v['lv'].'_reward'),
                                        'lv'=>$v['lv'],
                                        'type'=>1,
                                        'status'=>1,
                                        'addtime'=>time(),
                                    ]);
                                    Db::name('xy_balance_log')->insert([
                                        //记录返佣信息
                                        'uid'       => $v['id'],
                                        'oid'       => $oinfo['id'],
                                        'sid'       => $oinfo['uid'],
                                        'num'       => $oinfo['num']*config($v['lv'].'_reward'),
                                        'type'      => 1,
                                        'status'    => 98,
                                        'f_lv'        => $v['lv'],
                                        'addtime'   => time()
                                    ]);
                                    $num3 = $oinfo['num']*config($v['lv'].'_reward'); //佣金
                                    Db::name('xy_users')->where('id',$v['id'])->where('status',1)->setInc('balance',$num3);
                            }
                        }
                    }
                }
            }
            if ($res && $res1) {
                /************* 发放推广奖励 *********/
                end:
                exit('success');
            } else {
                file_put_contents($log_file, '======数据库插入失败!' . "\n", FILE_APPEND);
                file_put_contents($log_file_final, date('Y-m-d H:i:s') . ': ' . $put . "\n", FILE_APPEND);
                http_response_code(500);
                exit();
            }
        } else {
            http_response_code(500);
            exit();
        }
       
    }
    public function recharge2()
    {
        $data=input("post.");
        
        $put = json_encode($data);
        $log_file = APP_PATH . 'recharge.log';
        $log_file_final = APP_PATH . 'recharge_final.log';
        file_put_contents($log_file, date('Y-m-d H:i:s') . ': ' . $put . "\n", FILE_APPEND);
        
        if (!empty($data['merordercode'])) {
            
            if ($data['returncode'] != '00') {
                file_put_contents($log_file, '======订单状态不正确!' . "\n", FILE_APPEND);
                http_response_code(500);
                exit;
            }
            $oinfo = Db::name('xy_recharge')->find($data['merordercode']);
            if (!$oinfo) {
                file_put_contents($log_file, '======订单不存在!' . "\n", FILE_APPEND);
                http_response_code(500);
                exit;
            }
            if (floatval($data['amount']) != floatval($oinfo['num'])) {
                file_put_contents($log_file, '======金额不对!' . "\n", FILE_APPEND);
                http_response_code(500);
                exit;
            }
            if ($oinfo['status'] == 2) {
                exit('OK');
            }
            if ($oinfo['status'] != 1) {
                file_put_contents($log_file, '======订单已处理!' . "\n", FILE_APPEND);
                http_response_code(500);
                exit;
            }
            //查询是否首充
            $count=Db::name('xy_recharge')->where(['uid'=>$oinfo['uid'],"status"=>2])->count();
            $auomt=$oinfo['num'];
            if($count==0){
                $auomt=$auomt+($auomt*config("firststroke"));
            }
            //查询活动充值
            if($oinfo['num']==config("activity")){
                $auomt=$auomt+($auomt*config("activitynum"));
            }
            if($data['returncode']==00){
                $status = 2;
            }else{
                $status = 3;
            }
            
            $res = Db::name('xy_recharge')->where('id', $data['merordercode'])->update(['endtime' => time(), 'status' => $status]);
            $res1 = Db::name('xy_users')->where('id', $oinfo['uid'])->setInc('balance', $auomt);
            $res2 = Db::name('xy_balance_log')
                ->insert(['uid' => $oinfo['uid'], 'oid' => $data['merordercode'],
                    'num' => $auomt, 'type' => 1, 'status' => 1, 'addtime' => time(),]);
            if($count==0){
                $userList = model('admin/Users')->parent_user($oinfo['uid'],3);
                $userss = Db::name('xy_users')->where('id', $oinfo['uid'])->find();
                if($userList){
                    foreach($userList as $v){
                        if($v['level']>=1 && $v['level']>$userss['level']){
                            if($v['status']==1 && ($oinfo['num'] * config($v['lv'].'_reward') != 0)){
                                    $rows=Db::name('xy_reward_log')
                                    ->insert([
                                        'uid'=>$v['id'],
                                        'sid'=>$oinfo['uid'],
                                        'oid'=>$oinfo['id'],
                                        'num'=>$oinfo['num'] * config($v['lv'].'_reward'),
                                        'lv'=>$v['lv'],
                                        'type'=>1,
                                        'status'=>1,
                                        'addtime'=>time(),
                                    ]);
                                    Db::name('xy_balance_log')->insert([
                                        //记录返佣信息
                                        'uid'       => $v['id'],
                                        'oid'       => $oinfo['id'],
                                        'sid'       => $oinfo['uid'],
                                        'num'       => $oinfo['num']*config($v['lv'].'_reward'),
                                        'type'      => 1,
                                        'status'    => 98,
                                        'f_lv'        => $v['lv'],
                                        'addtime'   => time()
                                    ]);
                                    $num3 = $oinfo['num']*config($v['lv'].'_reward'); //佣金
                                    Db::name('xy_users')->where('id',$v['id'])->where('status',1)->setInc('balance',$num3);
                                    
                            }
                        }
                    }
                }
            }
            if ($res && $res1) {
                /************* 发放推广奖励 *********/
                end:
                exit('OK');
            } else {
                file_put_contents($log_file, '======数据库插入失败!' . "\n", FILE_APPEND);
                file_put_contents($log_file_final, date('Y-m-d H:i:s') . ': ' . $put . "\n", FILE_APPEND);
                http_response_code(500);
                exit();
            }
        } else {
            http_response_code(500);
            exit();
        }
       
    }
}