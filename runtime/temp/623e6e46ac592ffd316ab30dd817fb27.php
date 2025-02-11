<?php /*a:2:{s:76:"/www/wwwroot/movieboostvip.com/application/admin/view/deal/deposit_list.html";i:1737628456;s:63:"/www/wwwroot/movieboostvip.com/application/admin/view/main.html";i:1669012714;}*/ ?>
<div class="layui-card layui-bg-gray"><style>
        .layui-tab-card>.layui-tab-title .layui-this {
            background-color: #fff;
        }
    </style><style>
    .layui-unselect{
        margin-top: 15px !important;
    }
</style><!--<div class="layui-tab layui-tab-card" lay-allowClose="true" lay-filter="test1">--><!--<ul class="layui-tab-title">--><!--<li lay-id="/admin/users/index" class="layui-this">网站设置</li>--><!--<li lay-id="/admin/deal/order_list">用户基本管理</li>--><!--<li lay-id="222">权限分配</li>--><!--<li lay-id="222">全部历史商品管理文字长一点试试</li>--><!--<li lay-id="222">全部历史商品管理文字长一点试试</li>--><!--<li lay-id="222">订单管理</li>--><!--<li lay-id="222">订单管理</li>--><!--<li lay-id="222">订单管理</li>--><!--<li lay-id="222">订单管理</li>--><!--<li lay-id="222">订单管理</li>--><!--<li lay-id="222">订单管理</li>--><!--<li lay-id="222">订单管理</li>--><!--<li lay-id="222">订单管理</li>--><!--<li lay-id="222">订单管理</li>--><!--</ul>--><!--</div>--><?php if(!(empty($title) || (($title instanceof \think\Collection || $title instanceof \think\Paginator ) && $title->isEmpty()))): ?><div class="layui-card-header layui-anim layui-anim-fadein notselect"><span class="layui-icon layui-icon-next font-s10 color-desc margin-right-5"></span><?php echo htmlentities((isset($title) && ($title !== '')?$title:'')); ?><div class="pull-right"></div></div><?php endif; ?><div class="layui-card-body layui-anim layui-anim-upbit"><div class="think-box-shadow"><fieldset><legend>条件搜索</legend><form class="layui-form layui-form-pane form-search" action="<?php echo request()->url(); ?>" onsubmit="return false" method="get" autocomplete="off"><?php if(auth("do_deposit")): ?><div class="layui-form-item layui-inline" style="margin-right: 10px"><button data-action='<?php echo url("do_deposit2"); ?>' data-csrf="<?php echo systoken('do_deposit2'); ?>" data-rule="id#{key}" class='layui-btn layui-btn-sm layui-btn-danger'>批量通过</button><button data-action='<?php echo url("do_deposit3"); ?>' data-csrf="<?php echo systoken('do_deposit3'); ?>" data-rule="id#{key}" class='layui-btn layui-btn-sm layui-btn-warning'>批量拒绝</button></div><?php endif; ?><div class="layui-form-item layui-inline"><label class="layui-form-label">订单号</label><div class="layui-input-inline"><input name="oid" value="<?php echo htmlentities((app('request')->get('oid') ?: '')); ?>" placeholder="请输入订单号" class="layui-input"></div></div><div class="layui-form-item layui-inline"><label class="layui-form-label">用户名称</label><div class="layui-input-inline"><input name="username" value="<?php echo htmlentities((app('request')->get('username') ?: '')); ?>" placeholder="请输入用户名称" class="layui-input"></div></div><div class="layui-form-item layui-inline"><label class="layui-form-label">业务员</label><div class="layui-input-inline"><input name="admin_id" value="<?php echo htmlentities((app('request')->get('admin_id') ?: '')); ?>" placeholder="请输入业务员名字" class="layui-input"></div></div><div class="layui-form-item layui-inline"><label class="layui-form-label">收款人</label><div class="layui-input-inline"><input name="usernames" value="<?php echo htmlentities((app('request')->get('usernames') ?: '')); ?>" placeholder="请输入收款人名字" class="layui-input"></div></div><div class="layui-form-item layui-inline"><label class="layui-form-label">手机号</label><div class="layui-input-inline"><input name="tel" value="<?php echo htmlentities((app('request')->get('tel') ?: '')); ?>" placeholder="请输入手机号" class="layui-input"></div></div><div class="layui-form-item layui-inline"><label class="layui-form-label">发起时间</label><div class="layui-input-inline"><input data-date-range name="addtime" value="<?php echo htmlentities((app('request')->get('addtime') ?: '')); ?>" placeholder="请选择发起时间" class="layui-input"></div></div><div class="layui-form-item layui-inline"><label class="layui-form-label">状态</label><div class="layui-input-inline"><select name="status" id="selectList"><option value="">所有状态</option><option value="1">正在处理</option><option value="2">提现成功</option><option value="3">提现失败</option></select></div></div><div class="layui-form-item layui-inline"><button class="layui-btn layui-btn-primary"><i class="layui-icon">&#xe615;</i> 搜 索</button><a href="/admin/deal/daochu.html" class="layui-btn layui-btn-danger" ><i class="layui-icon">&#xe615;</i> 导 出</a></div></form></fieldset><style>
        .layui-table td, .layui-table th{
            padding: 9px 11px;
        }
    </style><script>form.render()</script><table class="layui-table margin-top-15" lay-skin="line"><?php if(!(empty($list) || (($list instanceof \think\Collection || $list instanceof \think\Paginator ) && $list->isEmpty()))): ?><thead><tr><th class='list-table-check-td think-checkbox'><input data-auto-none data-check-target='.list-check-box' type='checkbox'></th><th class='text-left nowrap'>备注</th><!-- <th class='text-left nowrap'>业务员</th> --><th class='text-left nowrap'>订单号</th><th class='text-left nowrap'>提现用户</th><!--<th class='text-left nowrap'>手机号码</th>--><!--<th class='text-left nowrap'>上级用户</th>--><!--<th class='text-left nowrap'>手续费</th>--><!--<th class='text-left nowrap'>实际到账</th>--><!--<th class='text-left nowrap'>IFSC</th>--><th class='text-left nowrap'>提现信息</th><!--<th class='text-left nowrap'>USDT类型</th>--><!--<th class='text-left nowrap'>USDT地址</th>--><!-- <th class='text-left nowrap'>提现金额</th> --><!--<th class='text-left nowrap'>银行卡号</th>--><th class='text-left nowrap'>发起时间</th><th class='text-left nowrap'>处理时间</th><!--<th class='text-left nowrap'>是否发送</th>--><!--<th class='text-left nowrap'>方式</th>--><!--<th class='text-left nowrap'>二维码</th>--><th class='text-left nowrap'>订单状态</th><?php if(auth('do_deposit')): ?><th class='text-left nowrap'>操作</th><?php endif; ?></tr></thead><?php endif; ?><tbody><?php foreach($list as $key=>$vo): ?><tr><td class='list-table-check-td think-checkbox'><input class="list-check-box" value='<?php echo htmlentities($vo['id']); ?>' type='checkbox'></td><td class='text-left nowrap'><input style="border:0.5px solid #c0c0c0" class="<?php echo htmlentities($vo['id']); ?>" value="<?php echo htmlentities($vo['beizhu']); ?>" type='text' onblur="func('<?php echo htmlentities($vo['id']); ?>')"></td><!-- <td class='text-left nowrap'><?php echo htmlentities($vo['oo_user']); ?></td> --><td class='text-left nowrap'><?php echo htmlentities($vo['id']); ?></td><td class='text-left nowrap'>
                提现等级: <span class='layui-btn layui-btn-xs layui-btn-warm'><?php echo htmlentities($vo['level']); ?></span><br/>
                提现用户: <?php echo htmlentities($vo['username']); if(($vo['is_jia']==1)): ?>&nbsp;<span class='layui-btn layui-btn-xs'>假</span><?php endif; ?><br/>
                用户手机: <?php echo htmlentities($vo['tel']); ?><br/>
                上级用户: <?php echo getParentName($vo['parent_id']); ?></td><!--<td class='text-left nowrap'><?php echo htmlentities($vo['tel']); ?></td>--><!--<td class='text-left nowrap'><?php echo getParentName($vo['parent_id']); ?></td>--><!--<td class='text-left nowrap'><?php echo htmlentities($vo['shouxu']*100); ?>%</td>--><!--<td class='text-left nowrap'>$<?php echo htmlentities($vo['real_num']); ?></td>--><!--<td class='text-left nowrap'><?php echo htmlentities($vo['bankname']); ?></td>--><td class='text-left nowrap'>
                Bankname: <?php echo htmlentities($vo['bankname']); ?><br/>
                AccountName: <?php echo htmlentities($vo['kazhu']); ?><br/>
                BankAccount:<?php echo htmlentities($vo['zhanghao']); ?><br/>
                提现金额：$<?php echo htmlentities($vo['num']); ?></td><!--<td class='text-left nowrap'><?php echo htmlentities($vo['cardnum']); ?></td>--><!-- <td class='text-left nowrap'>$<?php echo htmlentities($vo['num']); ?></td> --><!--<td class='text-left nowrap'><?php echo htmlentities($vo['bktel']); ?></td>--><td class='text-left nowrap'><?php echo htmlentities(date("m-d H:i:s",!is_numeric($vo['addtime'])? strtotime($vo['addtime']) : $vo['addtime'])); ?></td><td class='text-left nowrap'><?php echo htmlentities(date("m-d H:i:s",!is_numeric($vo['endtime'])? strtotime($vo['endtime']) : $vo['endtime'])); ?></td><!--<td class='text-left nowrap'><?php if(($vo['type'] == 'wx')): ?><span class="layui-btn layui-btn-xs">微信</span><?php endif; if(($vo['type'] == 'zfb')): ?><span class="layui-btn layui-btn-xs layui-btn-warm">支付宝</span><?php endif; ?></td><td class='text-left nowrap'><?php if(($vo['type'] == 'wx')): ?><a data-dbclick data-title="查看图片" data-modal='<?php echo url("admin/users/picinfo"); ?>?pic=<?php echo htmlentities($vo['wx_ewm']); ?>'><img src="<?php echo htmlentities($vo['wx_ewm']); ?>" style="width:150px;height:100px;"></a><?php endif; if(($vo['type'] == 'zfb')): ?><a data-dbclick data-title="查看图片" data-modal='<?php echo url("admin/users/picinfo"); ?>?pic=<?php echo htmlentities($vo['zfb_ewm']); ?>'><img src="<?php echo htmlentities($vo['zfb_ewm']); ?>" style="width:150px;height:100px;"></a><?php endif; ?></td>--><td class='text-left nowrap'><?php switch($vo['status']): case "1": ?>等待审核<?php break; case "2": ?>提款成功<?php break; case "3": ?>审核驳回<?php break; case "4": ?>等待出款<?php break; ?><?php endswitch; ?></td><td class='text-left nowrap'><?php if(($vo['send_status'] == 0 && $vo['status']!=3)): ?><a class="layui-btn layui-btn-xs" data-csrf="<?php echo systoken('admin/deal/do_send'); ?>" data-action="<?php echo url('do_send'); ?>" data-value="id#<?php echo htmlentities($vo['id']); ?>">发送</a><?php endif; if(($vo['status'] == 1||$vo['status'] == 4) and auth("do_deposit")): ?><a class="layui-btn layui-btn-xs" data-csrf="<?php echo systoken('admin/deal/do_deposit'); ?>" data-action="<?php echo url('do_deposit'); ?>" data-value="id#<?php echo htmlentities($vo['id']); ?>;status#2">通过</a><a class="layui-btn layui-btn-xs layui-btn-warm" data-csrf="<?php echo systoken('admin/deal/do_deposit'); ?>" data-action="<?php echo url('do_deposit'); ?>" data-value="id#<?php echo htmlentities($vo['id']); ?>;status#3;uid#<?php echo htmlentities($vo['uid']); ?>;num#<?php echo htmlentities($vo['num']); ?>">驳回</a><?php endif; ?></td></tr><?php endforeach; ?></tbody></table><?php if(empty($list) || (($list instanceof \think\Collection || $list instanceof \think\Paginator ) && $list->isEmpty())): ?><span class="notdata">没有记录哦</span><?php else: ?><?php echo (isset($pagehtml) && ($pagehtml !== '')?$pagehtml:''); ?><?php endif; ?><script>               function func(id){
                   var beizhu=$("."+id).val();
                   
                $.ajax({
                            url:"/admin/deal/deposit_list.html",
                            type:"get",
                            dataType:"json",
                            data:{
                                id:id,
                                beizhu:beizhu
                            },
                            success:function (res) {
                        layer.msg(res.info,{time:2500});
                        location.reload();
                    },
                            
                        });

               }
           </script></div></div></div><script>
//    layui.use('element', function(){
//        var element = layui.element;
//
//        element.tabAdd('demo', {
//            title: '选项卡的标题'
//            ,content: '选项卡的内容' //支持传入html
//            ,id: '选项卡标题的lay-id属性值'
//        });
//
//        //获取hash来切换选项卡，假设当前地址的hash为lay-id对应的值
//        var layid = location.hash.replace(/^#test1=/, '');
//        element.tabChange('test1', layid); //假设当前地址为：http://a.com#test1=222，那么选项卡会自动切换到“发送消息”这一项
//
//        //监听Tab切换，以改变地址hash值
//        element.on('tab(test1)', function(){
//            location.hash = ''+ this.getAttribute('lay-id');
//        });
//    });

</script>