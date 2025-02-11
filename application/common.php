<?php

use think\Console;
// 注册系统常用指令
Console::addDefaultCommands([
    \app\index\command\Task::class,
]);
// 自动升级
function sj_dd()
{
    $uid = session('user_id');
    $user=\think\Db::name('xy_users')->where(['id'=>$uid])->find();
    $user['sum'] = $user['balance'] + $user['lixibao_balance'];
    $lixibao=\think\Db::name('xy_lixibao')->where(['uid'=>$uid,'is_qu'=>0])->sum('num');
    
    $udata = \think\Db::name('xy_users')
    ->where('parent_id',$uid)
    ->field('id,parent_id,balance+lixibao_balance sum')->select();

    $leveld = [];
    foreach($udata as $v){
        if($v['sum'] >= 20){
            $leveld[] = $v;
        }
    }

    $level=\think\Db::name('xy_level')->select();

    foreach ($level as $k=>$v){
        
        if($user['sum'] >= $v['num_min'] && count($leveld) >= $v['auto_vip_xu_num']){
            if($v['level']>$user['level']){
                \think\Db::table('xy_users')->where("id",$uid)->update(["level"=>$v['level']]);
            }
        }
        
    }
}

// 防止升级提现下级跑路判断
function tx_dd()
{
    $uid = session('user_id');
    $user=\think\Db::name('xy_users')->where(['id'=>$uid])->find();
    $user['sum'] = $user['balance'] + $user['lixibao_balance'];
    $lixibao=\think\Db::name('xy_lixibao')->where(['uid'=>$uid,'is_qu'=>0])->sum('num');
    
    $udata = \think\Db::name('xy_users')
    ->where('parent_id',$uid)
    ->field('id,parent_id,balance+lixibao_balance sum')->select();

    $leveld = [];
    foreach($udata as $v){
        if($v['sum'] >= 20){
            $leveld[] = $v;
        }
    }

    $level=\think\Db::name('xy_level')->select();

    $l = 0;
    foreach ($level as $k=>$v){
        // echo $user['sum'],'---',$v['num_min'],'<br>';
        // echo count($leveld),'---',$v['auto_vip_xu_num'],'<br>';
        if($user['sum'] >= $v['num_min'] && count($leveld) >= $v['auto_vip_xu_num']){
            // echo $v['level'],'<br>';
            if($v['level'] > $l){
                $l = $v['level'];
                // echo $v['level'],'<br>';
            }
        }
        
    }
    // echo $l,'---';
    // echo $user['level'];
    if($l < $user['level']){
        return false;
    }
    return true;
}


function translate($query, $from, $to)
{
    $args = array(
        'q' => $query,
        'appid' => APP_ID,
        'salt' => rand(10000,99999),
        'from' => $from,
        'to' => $to,

    );
    $args['sign'] = buildSign($query, APP_ID, $args['salt'], SEC_KEY);
    $ret = call(URL, $args);
    $ret = json_decode($ret, true);
    return $ret; 
}


function FractalPosts($appid,$appkey)
{
	$url='https://api.digitalbanks.com.br/api/oauth/token/';
	$str=base64_encode($appid.":".$appkey);
	$data=array(
	    "grant_type"=>"client_credentials",
	    "scope"=>"api_pix"
	);
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HEADER, FALSE);//不抓取头部信息。只返回数据
    curl_setopt($curl, CURLOPT_TIMEOUT,1000);//超时设置
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);//1表示不返回bool值
    curl_setopt($curl, CURLOPT_POST, TRUE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($curl, CURLOPT_DNS_USE_GLOBAL_CACHE, false );
    curl_setopt($curl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 );#禁用IPV6
    curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded','Authorization: Basic '.$str));//重点
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
    $response = curl_exec($curl);
    if (curl_errno($curl)) {
        return curl_error($curl);
    }
    curl_close($curl);
    return $response;
    
}
//加密<>
function buildSign($query, $appID, $salt, $secKey)
{/*{{{*/
    $str = $appID . $query . $salt . $secKey;
    $ret = md5($str);
    return $ret;
}/*}}}*/

function jsonUrl($url, $data = null)
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($curl,CURLOPT_HTTPHEADER,array(
        'Accept:application/json',
        'Content-Type: application/json;charset=UTF-8',
    ));
    if (!empty($data)){
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    }
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
    $output = curl_exec($curl);
    curl_close($curl);
    return $output;
}


function Posts($url, $data)

    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, FALSE);//不抓取头部信息。只返回数据
        curl_setopt($curl, CURLOPT_TIMEOUT,1000);//超时设置
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);//1表示不返回bool值
        curl_setopt($curl, CURLOPT_POST, TRUE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));//重点
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
        $response = curl_exec($curl);
        // var_dump($response);exit;
        if (curl_errno($curl)) {
            return curl_error($curl);
        }
        curl_close($curl);
        return $response;
    }

//发起网络请求
function call($url, $args=null, $method="post", $testflag = 0, $timeout = CURL_TIMEOUT, $headers=array())
{/*{{{*/
    $ret = false;
    $i = 0; 
    while($ret === false) 
    {
        if($i > 1)
            break;
        if($i > 0) 
        {
            sleep(1);
        }
        $ret = callOnce($url, $args, $method, false, $timeout, $headers);
        $i++;
    }
    return $ret;
}/*}}}*/

function callOnce($url, $args=null, $method="post", $withCookie = false, $timeout = CURL_TIMEOUT, $headers=array())
{/*{{{*/
    $ch = curl_init();
    if($method == "post") 
    {
        $data = convert($args);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_POST, 1);
    }
    else 
    {
        $data = convert($args);
        if($data) 
        {
            if(stripos($url, "?") > 0) 
            {
                $url .= "&$data";
            }
            else 
            {
                $url .= "?$data";
            }
        }
    }
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    if(!empty($headers)) 
    {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }
    if($withCookie)
    {
        curl_setopt($ch, CURLOPT_COOKIEJAR, $_COOKIE);
    }
    $r = curl_exec($ch);
    curl_close($ch);
    return $r;
}/*}}}*/

function convert(&$args)
{/*{{{*/
    $data = '';
    if (is_array($args))
    {
        foreach ($args as $key=>$val)
        {
            if (is_array($val))
            {
                foreach ($val as $k=>$v)
                {
                    $data .= $key.'['.$k.']='.rawurlencode($v).'&';
                }
            }
            else
            {
                $data .="$key=".rawurlencode($val)."&";
            }
        }
        return trim($data, "&");
    }
    return $args;
}/*}}}*/
function isAllChinese($str){
    if (preg_match("/([\x81-\xfe][\x40-\xfe])/", $str, $match)) {
        return true;//全是中文
    } else {
        return false;//不全是中文
    }
}

function is_mobile($tel){
    if(strlen($tel)!=10){
        return true;
    }else{
        return false;
    }
}

/*
 * 检查图片是不是bases64编码的
 */
function is_image_base64($base64) {
    if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64, $result)){
        return true;
    }else{
        return false;
    }
}

function check_pic($dir,$type_img){
    $new_files = $dir.date("YmdHis"). '-' . rand(0,9999999) . "{$type_img}";
    if(!file_exists($new_files))
        return $new_files;
    else
        return check_pic($dir,$type_img);  
}

/**
 * 获取数组中的某一列
 * @param array $arr 数组
 * @param string $key_name  列名
 * @return array  返回那一列的数组
 */
function get_arr_column($arr, $key_name)
{
	$arr2 = array();
	foreach($arr as $key => $val){
		$arr2[] = $val[$key_name];        
	}
	return $arr2;
}

//保留两位小数
function tow_float($number){
    return (floor($number * 100) / 100); 
}

//生成订单号
function getSn($head='')
{
    @date_default_timezone_set("PRC");
    $order_id_main = date('YmdHis') . mt_rand(1000, 9999);
    //唯一订单号码（YYMMDDHHIISSNNN）
    $osn = $head.substr($order_id_main,2); //生成订单号
    return $osn;
}

/**
 * 修改本地配置文件
 *
 * @param array $name   ['配置名']
 * @param array $value  ['参数']
 * @return void
 */
function setconfig($name, $value)
{
    if (is_array($name) and is_array($value)) {
        for ($i = 0; $i < count($name); $i++) {
            $names[$i] = '/\'' . $name[$i] . '\'(.*?),/';
            $values[$i] = "'". $name[$i]. "'". "=>" . "'".$value[$i] ."',";
        }
        $fileurl = APP_PATH . "../config/app.php";
        $string = file_get_contents($fileurl); //加载配置文件
        $string = preg_replace($names, $values, $string); // 正则查找然后替换
        file_put_contents($fileurl, $string); // 写入配置文件
        return true;
    } else {
        return false;
    }
}

//生成随机用户名
function get_username()
{
    $chars1 = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $chars2 = "abcdefghijklmnopqrstuvwxyz0123456789";
    $username = "";
    for ( $i = 0; $i < mt_rand(2,3); $i++ )
    {
        $username .= $chars1[mt_rand(0,25)];
    }
    $username .='_';

    for ( $i = 0; $i < mt_rand(4,6); $i++ )
    {
        $username .= $chars2[mt_rand(0,35)];
    }
    return $username;
}

/**
 * 判断当前时间是否在指定时间段之内
 * @param integer $a 起始时间
 * @param integer $b 结束时间
 * @return boolean
 */
function check_time( $a, $b)
{
    $nowtime = time();
    $start = strtotime($a.':00:00');
    $end = strtotime($b.':00:00');

    if ($nowtime >= $end || $nowtime <= $start){
        return true;
    }else{
        return false;
    }
}
//递归
function getTree($data,$pid,$list=['pid','id'],$level=1,$astrict=3,$static=false)
{
    static $arr = array();
    $arr = $static ? [] : $arr;
    if($level <= $astrict || !$astrict){
        foreach($data as $v){
            if($v[$list[0]] == $pid){
                $v['level'] = $level;
                $arr[] = $v;
                getTree($data,$v[$list[1]],$list,$level + 1,$astrict);
            }
        }
    }
    return $arr;
}



