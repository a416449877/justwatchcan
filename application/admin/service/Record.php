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

namespace app\admin\service;

use library\tools\Node;
use think\Db;
use think\facade\Request;

/**
 * 系统日志服务管理
 * Class LogService
 * @package app\admin\service
 */
class Record
{
    
    private $controller;
    
    private $action;
    
    private $username;
    
    private $request;
    
    private $record = [
        'users/edit_users_bk'     => '编辑用户银行卡',
        'users/edit_users_ankou'  => '用户加扣款',
        'users/edit_users'        => '编辑用户资料',
        'users/edit_password'     => '修改用户密码',
        'users/edit_users_status' => '编辑用户状态',
        'users/edit_users_order'  => '重置今天任务量',
        'users/ka_users'          => '卡单设置',
        'deal/do_deposit'         => '提现处理',
    ];
    
    private $record_type = [
        'users/edit_users_bk'     => 1,
        'users/edit_users_ankou'  => 2,
        'users/edit_users'        => 3,
        'users/edit_password'     => 4,
        'users/edit_users_status' => 5,
        'users/edit_users_order'  => 6,
        'users/ka_users'          => 7,
        'deal/do_deposit'         => 8,
    ];
    
    public function __construct()
    {
        $this->request    = request();
        $this->controller = $this->request->controller();
        $this->action     = $this->request->action();
        $this->username   = session('?admin_user') && isset(session('admin_user')['username']) ? session('admin_user')['username'] : '';
    }
    
    public function appEnd($params)
    {
        $action = strtolower($this->controller) . '/' . strtolower($this->action);
        if($this->request->isPost() && isset($this->record[$action])){
            $post     = $this->request->post();
            $user_id  = $this->getUserIdByAction($this->controller, $this->action, $post);
            $username = db('xy_users')->where('id', $user_id)->value('username');
            $user_ip  = $this->request->ip();
            $insert   = [
                'action_user' => $this->username,
                'user_name'   => $username,
                'memo'        => $this->record[$action],
                'type'        => $this->record_type[$action],
                'action_ip'   => $user_ip,
                'update_time' => time(),
                'params'      => json_encode($post),
                'user_agent'  => $this->request->header('user-agent')
            ];
            db('xy_logs')->insert($insert);
        }
    }
    
    private function getUserIdByAction($controller, $action, $post){
        $user_id = $post['id'];
        if(strtolower($controller) == 'users'){
            switch ($action) {
                case 'edit_users_bk':
                    $bank_id = $post['id'];
                    $user_id = db('xy_bankinfo')->where('id', '=', $bank_id)->value('uid') + 0;
                    break;
                default:
                    $user_id = $post['id'];
                    break;
            }
        }elseif(strtolower($controller) == 'deal'){
            if($action == 'do_deposit'){
                $user_id = db('xy_deposit')->where('id', '=', $post['id'])->value('uid') + 0;
            }
        }
        return $user_id;
    }
}
