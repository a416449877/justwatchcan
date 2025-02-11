<?php
namespace app\index\command;

use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use think\Db;

class Task extends Command
{
    protected function configure()
    {
        $this->setName('task')
        	->setDescription('Task');
    }

    protected function execute(Input $input, Output $output)
    {
        $system_flag = sysconf('flag');
        Db::name('xy_users')->where('deal_count', '<>', 0)->field('id,tel,level,deal_time,flag,is_flag,deal_count')->chunk(100, function($users) use ($system_flag) {
            foreach ($users as $user) {
                $uid = $user['id'];
                $flag = $user['flag'] == 0 ? $system_flag : $user['flag'];
                // 如果是隔日清空
                if($flag + 0 == 1 && $user['deal_time']!=strtotime(date('Y-m-d'))){
                    // 如果是
                    Db::name('xy_users')->where('id',$uid)->update(['deal_time'=>strtotime(date('Y-m-d')),'deal_count'=>0,'recharge_num'=>0,'deposit_num'=>0, 'is_flag' => 0, 'old_num' => 0, 'check_vip' => 1]);
                }
                
                if($flag + 0 == 2 && $user['deal_time']!=strtotime(date('Y-m-d'))){
                    $level = $user['level'];
                    !$user['level'] ? $level = 0 : '';
                    $order_num = Db::name('xy_level')->where('level',$level)->value('order_num') + 0;
                    // 如果满单了
                    if($order_num <= $user['deal_count'] + 0){
                        Db::name('xy_users')->where('id',$uid)->update(['deal_time'=>strtotime(date('Y-m-d')),'deal_count'=>0,'recharge_num'=>0,'deposit_num'=>0, 'is_flag' => 0, 'old_num' => 0, 'check_vip' => 1]);
                    }else{
                        Db::name('xy_users')->where('id',$uid)->update(['deal_time'=>strtotime(date('Y-m-d')),'is_flag'=>1,'recharge_num'=>0,'deposit_num'=>0, 'old_num' => $user['deal_count']]);
                    }
                }
                
               
            }
        });
        $output->writeln("success");
    }
}