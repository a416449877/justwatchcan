<?php /*a:3:{s:70:"/www/wwwroot/movieboostvip.com/application/admin/view/users/index.html";i:1737564292;s:63:"/www/wwwroot/movieboostvip.com/application/admin/view/main.html";i:1669012714;s:77:"/www/wwwroot/movieboostvip.com/application/admin/view/users/index_search.html";i:1680104086;}*/ ?>
<div class="layui-card layui-bg-gray"><style>
        .layui-tab-card>.layui-tab-title .layui-this {
            background-color: #fff;
        }
    </style><style>
    .layui-unselect{
        margin-top: 15px !important;
    }
</style><!--<div class="layui-tab layui-tab-card" lay-allowClose="true" lay-filter="test1">--><!--<ul class="layui-tab-title">--><!--<li lay-id="/admin/users/index" class="layui-this">网站设置</li>--><!--<li lay-id="/admin/deal/order_list">用户基本管理</li>--><!--<li lay-id="222">权限分配</li>--><!--<li lay-id="222">全部历史商品管理文字长一点试试</li>--><!--<li lay-id="222">全部历史商品管理文字长一点试试</li>--><!--<li lay-id="222">订单管理</li>--><!--<li lay-id="222">订单管理</li>--><!--<li lay-id="222">订单管理</li>--><!--<li lay-id="222">订单管理</li>--><!--<li lay-id="222">订单管理</li>--><!--<li lay-id="222">订单管理</li>--><!--<li lay-id="222">订单管理</li>--><!--<li lay-id="222">订单管理</li>--><!--<li lay-id="222">订单管理</li>--><!--</ul>--><!--</div>--><?php if(!(empty($title) || (($title instanceof \think\Collection || $title instanceof \think\Paginator ) && $title->isEmpty()))): ?><div class="layui-card-header layui-anim layui-anim-fadein notselect"><span class="layui-icon layui-icon-next font-s10 color-desc margin-right-5"></span><?php echo htmlentities((isset($title) && ($title !== '')?$title:'')); ?><div class="pull-right"><?php if(auth("add_users")): ?><button data-modal='<?php echo url("add_users"); ?>' data-title="添加会员" class='layui-btn'>添加会员</button><?php endif; ?></div></div><?php endif; ?><div class="layui-card-body layui-anim layui-anim-upbit"><style>
    .layui-table-cell{
   height:auto;
   overflow:visible;
   text-overflow:inherit;
   white-space:normal;
 }

</style><script>
    // 复制剪切板
function copyContent(content) {
    var oInput = document.createElement('input');
    oInput.value = content;
    document.body.appendChild(oInput);
    oInput.select(); // 选择对象
    document.execCommand("Copy"); // 执行浏览器复制命令
    oInput.className = 'oInput';//设置class名
    document.getElementsByClassName("oInput")[0].remove();//移除这个input
    layer.msg('复制成功！', {icon: 1, time: 3000});
};

</script><div class="think-box-shadow"><fieldset><legend>条件搜索</legend><form class="layui-form layui-form-pane form-search" action="<?php echo request()->url(); ?>" onsubmit="return false" method="get" autocomplete="off"><div class="layui-form-item layui-inline"><label class="layui-form-label">用户名称</label><div class="layui-input-inline"><input name="username" value="<?php echo htmlentities((app('request')->get('username') ?: '')); ?>" placeholder="请输入用户名称" class="layui-input"></div></div><div class="layui-form-item layui-inline"><label class="layui-form-label">手机号码</label><div class="layui-input-inline"><input name="tel" value="<?php echo htmlentities((app('request')->get('tel') ?: '')); ?>" placeholder="请输入手机号码" class="layui-input"></div></div><div class="layui-form-item layui-inline"><label class="layui-form-label">业务员</label><div class="layui-input-inline"><input name="admin_id" value="<?php echo htmlentities((app('request')->get('admin_id') ?: '')); ?>" placeholder="请输入业务员" class="layui-input"></div></div><div class="layui-form-item layui-inline"><label class="layui-form-label">注册时间</label><div class="layui-input-inline"><input data-date-range name="addtime" value="<?php echo htmlentities((app('request')->get('addtime') ?: '')); ?>" placeholder="请选择注册时间" class="layui-input"></div></div><div class="layui-form-item layui-inline"><label class="layui-form-label">状态</label><div class="layui-input-inline"><select name="is_jia" id="selectList"><option value="">所有状态</option><option value="-1">真人</option><option value="1">假人</option></select></div></div><div class="layui-form-item layui-inline"><label class="layui-form-label">VIP等级</label><div class="layui-input-inline"><select name="vip_state" id="vip_state"><option value="">所有状态</option><option value="1">vip1</option><option value="2">vip2</option><option value="3">vip3</option><option value="4">vip4</option><option value="5">vip5</option><option value="6">vip6</option><option value="7">vip7</option><option value="8">vip8</option><option value="9">vip9</option></select></div></div><div class="layui-form-item layui-inline"><label class="layui-form-label">用户层级</label><div class="layui-input-inline"><select name="level_state" id="level_state"><option value="">所有</option><option value="1">1级</option><option value="2">2级</option><option value="3">3级</option></select></div></div><div class="layui-form-item layui-inline"><button class="layui-btn layui-btn-primary"><i class="layui-icon">&#xe615;</i> 搜 索</button><?php if(auth("edit_users")): ?><a href="/admin/users/daochu.html" class="layui-btn layui-btn-danger" ><i class="layui-icon">&#xe615;</i> 导 出</a><?php endif; ?></div></form><!--<button onclick="tx()" style="background:red;color:white" class="layui-btn"> 提现至少完成订单/天</button>--><!--<button onclick="cz()" style="background:red;color:white" class="layui-btn"> 解决需要充值多少钱</button>--><!--<button onclick="kd()" style="background:red;color:white" class="layui-btn"> 今日第几单卡</button>--><!--<button onclick="lv()" style="background:red;color:white" class="layui-btn"> 赠送充值百分比</button>--><!-- <button onclick="all()" style="background:red;color:white" class="layui-btn">全选</button>--></fieldset><script>
    var test = "<?php echo htmlentities((app('request')->get('is_jia') ?: '0')); ?>";
    $("#selectList").find("option[value="+test+"]").prop("selected",true);
    form.render();
    
    function kd(){
               var date = document.getElementsByName("hobby");
            //然后我们去得到这个多选框的长度
            var thisLength = date.length;
            //使用字符串数组，用于存放选择好了的数据
            var str = [];
            for(var i = 0;i < thisLength;i++) {
                if (date[i].checked == true) {
                    if(date[i].value!=''){
                     str[i] = date[i].value;//这个是获取多选框中的值
                    }
                }
            }
            if(str.length==0){
                  layer.msg('请勾选用户！',{time:2500});
                  return; 
            }
            var a='';
             for(var j in str){
                a+=str[j]+',';
            }
            
          layer.prompt({title:'今日第几单卡', 
         success: function (layero) {
              $("input", layero).prop("placeholder", "设置几单卡");
              $("input", layero).prop("value",0);

          }
     ,formType: 0,btn:['确定','取消'],}, function (value, index){
                $.ajax({
                    type: 'POST',
                    url:  "<?php echo url('kd'); ?>",
                    data: {
                       // 'value': value+','+$('#two').val()+','+$('#three').val(),
                        'value': value,
                        'id': a
                    },
                    success:function (res) {
                        layer.msg('设置成功',{time:2500});
                        location.reload();
                    }
                });
        
          
          
        })
        //增加输入框            
      // $(".layui-layer-content").append("<br/><input type='text' value='0' id='two' class='layui-layer-input' value='' placeholder='设置几单卡' >");
      // $(".layui-layer-content").append("<br/><input type='text' value='0' id='three' class='layui-layer-input' value='' placeholder='设置几单卡' >");
       //$(".layui-layer-content").append("<br/><input type=\"text\" id= \"zxr2\" class=\"layui-input\" placeholder=\"输入注销人\"/>");
    }
    
    function all(){
                var date = document.getElementsByName("hobby");
            //然后我们去得到这个多选框的长度
            var thisLength = date.length;
            for(var i = 0;i < thisLength;i++) {
                date[i].checked == true

            }
    }
    
    
    function cz(){
        var date = document.getElementsByName("hobby");
            //然后我们去得到这个多选框的长度
            var thisLength = date.length;
            //使用字符串数组，用于存放选择好了的数据
            var str = [];
            for(var i = 0;i < thisLength;i++) {
                if (date[i].checked == true) {
                    if(date[i].value!=''){
                     str[i] = date[i].value;//这个是获取多选框中的值
                    }
                }
            }
            if(str.length==0){
                  layer.msg('请勾选用户！',{time:2500});
                  return; 
            }
            var a='';
             for(var j in str){
                a+=str[j]+',';
            }
            
            layer.prompt({
            formType: 0,
            value: '',
            title: '解决需要充值多少钱',
            btn: ['确定','取消'], //按钮，
            btnAlign: 'c'
        }, function(value,index){ 
            console.log(value);
                  $.ajax({
                    type: 'POST',
                    url:  "<?php echo url('user_cz'); ?>",
                    data: {
                        'value': value,
                        'id': a
                    },
                    success:function (res) {
                        layer.msg('设置成功',{time:2500});
                        location.reload();
                    }
                });

        });
    }
    
    
    function lv(){
           var date = document.getElementsByName("hobby");
            //然后我们去得到这个多选框的长度
            var thisLength = date.length;
            //使用字符串数组，用于存放选择好了的数据
            var str = [];
            for(var i = 0;i < thisLength;i++) {
                if (date[i].checked == true) {
                    if(date[i].value!=''){
                     str[i] = date[i].value;//这个是获取多选框中的值
                    }
                }
            }
            if(str.length==0){
                  layer.msg('请勾选用户！',{time:2500});
                  return; 
            }
            var a='';
             for(var j in str){
                a+=str[j]+',';
            }
            
          layer.prompt({title:'赠送充值百分比', 
         success: function (layero) {
              $("input", layero).prop("placeholder", "赠送充值百分比");
              $("input", layero).prop("value",0);

          }
     ,formType: 0,btn:['确定','取消'],}, function (value, index){
                $.ajax({
                    type: 'POST',
                    url:  "<?php echo url('order_lv'); ?>",
                    data: {
                        'value': value+','+$('#two').val()+','+$('#three').val(),
                        //'value': value,
                        'id': a
                    },
                    success:function (res) {
                        layer.msg('设置成功',{time:2500});
                        location.reload();
                    }
                });
        
          
          
        })
        //增加输入框            
       $(".layui-layer-content").append("<br/><input type='text' value='0' id='two' class='layui-layer-input' value='' placeholder='赠送充值百分比' >");
       $(".layui-layer-content").append("<br/><input type='text' value='0' id='three' class='layui-layer-input' value='' placeholder='赠送充值百分比' >");
    }
    
    function tx(){
        var date = document.getElementsByName("hobby");
            //然后我们去得到这个多选框的长度
            var thisLength = date.length;
            //使用字符串数组，用于存放选择好了的数据
            var str = [];
            for(var i = 0;i < thisLength;i++) {
                if (date[i].checked == true) {
                    if(date[i].value!=''){
                     str[i] = date[i].value;//这个是获取多选框中的值
                    }
                }
            }
            if(str.length==0){
                  layer.msg('请勾选用户！',{time:2500});
                  return; 
            }
            var a='';
             for(var j in str){
                a+=str[j]+',';
            }
            
            layer.prompt({
            formType: 0,
            value: '',
            title: '提现至少完成订单/天',
            btn: ['确定','取消'], //按钮，
            btnAlign: 'c'
        }, function(value,index){
            console.log(value);
                  $.ajax({
                    type: 'POST',
                    url:  "<?php echo url('order_rw'); ?>",
                    data: {
                        'value': value,
                        'id': a
                    },
                    success:function (res) {
                        layer.msg('设置成功',{time:2500});
                        location.reload();
                    }
                });

        });

    }
    
    
    
</script><table class="layui-table margin-top-15" lay-filter="tab" lay-skin="line"><?php if(!(empty($list) || (($list instanceof \think\Collection || $list instanceof \think\Paginator ) && $list->isEmpty()))): ?><thead><tr><th lay-data="{field:'checkbox',width:80}" class='text-left nowrap'><input type="checkbox"></th><th lay-data="{field:'id',width:80}" class='text-left nowrap'>ID</th><th lay-data="{field:'tel',width:180}" class='text-left nowrap'>账号 等级 用户名</th><!-- <th lay-data="{field:'username'}" class='text-left nowrap'>(等级)用户名</th>  --><th lay-data="{field:'balance',width:150 ,sort:true}" class='text-left nowrap'>
                账户余额
            </th><!-- <th lay-data="{field:'parent_name'}" class='text-left nowrap'>上级用户</th> --><!--<th lay-data="{field:'username3'}" class='text-left nowrap'>业务员归属</th>--><!-- <th lay-data="{field:'childs'}" class='text-left nowrap'>直推人数</th> --><!-- <th lay-data="{field:'freeze_balance'}" class='text-left nowrap'>冻结金额</th> --><!-- <th lay-data="{field:'lixibao_balance',sort:true}" class='text-left nowrap'>利息宝金额</th> --><th lay-data="{field:'invite_code',width:130 }" class='text-left nowrap'>邀请码</th><th lay-data="{field:'order_num',width:100 }" class='text-left nowrap'>今日订单数</th><th lay-data="{field:'beizhu',width:300 }" class='text-left nowrap'>订单备注</th><!-- <th lay-data="{field:'gender'}" class='text-left nowrap'>性别</th> --><th lay-data="{field:'addtime',width:200}" class='text-left nowrap'>注册时间</th><th lay-data="{field:'lixibao_balance'}" class='text-left nowrap'>登陆ip</th><th lay-data="{field:'ip'}" class='text-left nowrap'>最后交易日期</th><!--  --><th lay-data="{field:'edit',width:430,fixed: 'right',style:'height:120px;top:-16px'}" class='text-left nowrap'>操作</th></tr></thead><?php endif; ?><tbody><?php foreach($list as $key=>$vo): ?><tr style="height:200px"><td class='text-left nowrap'><input type="checkbox" name="hobby" value="<?php echo htmlentities($vo['id']); ?>" checked></td><td class='text-left nowrap'><?php echo htmlentities($vo['id']); ?></td><td class='text-left nowrap'>账号：<?php echo htmlentities($vo['tel']); ?><br/>用户名：<span class='layui-btn layui-btn-xs layui-btn-warm' align='left'><?php echo htmlentities($vo['level']); ?></span>&nbsp;<?php echo htmlentities($vo['username']); ?><br/>上级用户：<?php echo htmlentities($vo['parent_name']); ?></td><td class='text-left nowrap'>
                可用余额：
                <?php
                  
                        echo $vo['balance'];
                    
                ?><br/>
                冻结金额：<?php echo htmlentities($vo['freeze_balance']); ?><br/>
                任务佣金：<br/><!--<br/><?php
                
                    if($vo['level']==1){
                        echo $vo['balance']-500;
                    }
                    elseif($vo['level']==0){
                        echo $vo['balance'];
                    }
                    elseif($vo['level']==2){
                        echo $vo['balance']-1000-500;
                    }
                    elseif($vo['level']==3){
                        echo $vo['balance']-3500-1000-500;
                    }
                    elseif($vo['level']==4){
                        echo $vo['balance']-8000-3500-1000-500;
                    }
                    elseif($vo['level']==5){
                        echo $vo['balance']-20000-8000-3500-1000-500;
                    }
                    elseif($vo['level']==6){
                        echo $vo['balance']-42000-20000-8000-3500-1000-500;
                    }
                    elseif($vo['level']==7){
                        echo $vo['balance']-98000-42000-20000-8000-3500-1000-500;
                    }
                    elseif($vo['level']==8){
                        echo $vo['balance']-158000-98000-42000-20000-8000-3500-1000-500;
                    }
                ?>
                --></td><!-- <td class='text-left nowrap'><?php echo htmlentities($vo['parent_name']); ?></td> --><!--<td class='text-left nowrap'><?php echo htmlentities($vo['username3']); ?></td>--><!-- <td class='text-left nowrap'><?php echo htmlentities($vo['childs']); ?></td> --><!-- <td class='text-left nowrap'><?php echo htmlentities($vo['freeze_balance']); ?></td> --><!-- <td class='text-left nowrap'><?php echo htmlentities($vo['lixibao_balance']); ?></td> --><td class='text-left nowrap' onclick="copyContent('<?php echo htmlentities($vo['invite_code']); ?>')"><label onclick="copyContent('<?php echo htmlentities($vo['invite_code']); ?>')" class="layui-btn layui-btn-normal"><?php echo htmlentities($vo['invite_code']); ?></label><br/>
            直推人数：<?php echo htmlentities($vo['childs']); ?></td><td class='text-left nowrap'><?php echo htmlentities((isset($vo['deal_count']) && ($vo['deal_count'] !== '')?$vo['deal_count']:0)); ?>/<?php echo htmlentities($vo['order_num']); ?></td><td class='text-left nowrap'><?php echo htmlentities($vo['beizhu']); ?></td><!----><!-- <td class='text-left nowrap'><?php if($vo['gender'] == '0'): ?>男<?php else: ?>女<?php endif; ?></td> --><td class='text-left nowrap'><?php echo htmlentities(format_datetime($vo['addtime'])); ?></td><td class='text-left nowrap'><?php echo htmlentities($vo['login_ip']); ?></td><td class='text-left nowrap'><?php echo htmlentities(format_datetime($vo['deal_time'])); ?></td><!-- <td class='text-left nowrap'><?php if($vo['is_jia']>0): ?><a class="layui-btn layui-btn-danger layui-btn-xs" >假人</a><?php else: ?><a class="layui-btn layui-btn-normal layui-btn-xs" >真人</a><?php endif; ?></td> --><td class='text-left nowrap'><!--<a data-dbclick class="layui-btn layui-btn-xs layui-btn-danger" data-title="暗扣设置" data-modal='<?php echo url("admin/users/edit_users_ankou"); ?>?id=<?php echo htmlentities($vo['id']); ?>'>暗扣设置</a>--><a data-dbclick class="layui-btn layui-btn-xs" data-title="编辑菜单" data-modal='<?php echo url("admin/users/credit_score"); ?>?id=<?php echo htmlentities($vo['id']); ?>'>信用分设置</a><?php if(auth("edit_users_ankou")): ?><a data-dbclick class="layui-btn layui-btn-xs layui-btn-danger" data-title="加扣款" data-modal='<?php echo url("admin/users/edit_users_ankou"); ?>?id=<?php echo htmlentities($vo['id']); ?>'>加扣款</a><?php endif; if(auth("ka_users")): ?><a data-dbclick class="layui-btn layui-btn-xs" data-title="编辑菜单" data-modal='<?php echo url("admin/users/ka_users"); ?>?id=<?php echo htmlentities($vo['id']); ?>'>卡单设置</a><?php endif; if(auth("edit_info")): ?><a data-dbclick class="layui-btn layui-btn-xs" data-title="编辑菜单" data-modal='<?php echo url("admin/users/edit_info"); ?>?id=<?php echo htmlentities($vo['id']); ?>'>单数模式</a><?php endif; if(auth("edit_users_address")): ?><a data-dbclick class="layui-btn layui-btn-xs" data-title="银行卡信息" data-modal='<?php echo url("admin/users/edit_users_bk"); ?>?id=<?php echo htmlentities($vo['id']); ?>'>结算卡</a><?php endif; if(auth("edit_users")): ?><a data-dbclick class="layui-btn layui-btn-xs" data-title="编辑菜单" data-modal='<?php echo url("admin/users/edit_users"); ?>?id=<?php echo htmlentities($vo['id']); ?>'>基础资料</a><?php endif; if(auth("tuandui")): ?><a data-dbclick class="layui-btn layui-btn-xs layui-btn-danger" data-title="查看团队" data-reload="true" data-open='<?php echo url("admin/users/tuandui"); ?>?id=<?php echo htmlentities($vo['id']); ?>'>查看团队</a><br/><?php endif; if(auth("edit_users_order")): ?><a class="layui-btn layui-btn-xs layui-btn-warm" data-action="<?php echo url('edit_users_order',['id'=>$vo['id']]); ?>" data-value="id#<?php echo htmlentities($vo['id']); ?>;status#2" data-confirm="是否重置今天任务量？" style='background:red;'>重置今天任务量</a><?php endif; if(auth("caiwu")): ?><a data-dbclick class="layui-btn layui-btn-xs layui-btn-normal" data-title="查看账变" data-reload="true" data-open='<?php echo url("admin/users/caiwu"); ?>?id=<?php echo htmlentities($vo['id']); ?>'>账变信息</a><?php endif; if(auth("edit_password")): ?><a data-dbclick class="layui-btn layui-btn-xs layui-btn-danger" data-title="修改登陆密码" data-modal='<?php echo url("admin/users/edit_password"); ?>?id=<?php echo htmlentities($vo['id']); ?>&type=1'>修改登陆密码</a><?php endif; if(auth("edit_pay_password")): ?><a data-dbclick class="layui-btn layui-btn-xs layui-btn-danger" data-title="修改支付密码" data-modal='<?php echo url("admin/users/edit_password"); ?>?id=<?php echo htmlentities($vo['id']); ?>&type=2'>修改支付密码</a><?php endif; if(($vo['status'] == 1) and auth("edit_users_status")): ?><a class="layui-btn layui-btn-xs layui-btn-warm" data-action="<?php echo url('edit_users_status',['status'=>2,'id'=>$vo['id']]); ?>" data-value="id#<?php echo htmlentities($vo['id']); ?>;status#2" style='background:red;'>禁用</a><?php elseif(($vo['status'] == 2) and auth("edit_users_status")): ?><a class="layui-btn layui-btn-xs layui-btn-warm" data-action="<?php echo url('edit_users_status',['status'=>1,'id'=>$vo['id']]); ?>" data-value="id#<?php echo htmlentities($vo['id']); ?>;status#1" style='background:green;'>启用</a><?php endif; if(($vo['is_jia'] == 1) and auth("edit_users_status")): ?><a class="layui-btn layui-btn-xs layui-btn-warm" data-action="<?php echo url('edit_users_status2',['status'=>-1,'id'=>$vo['id']]); ?>" data-value="id#<?php echo htmlentities($vo['id']); ?>;status#-1" style='background:red;'>设为真人</a><?php else: ?><a class="layui-btn layui-btn-xs layui-btn-warm" data-action="<?php echo url('edit_users_status2',['status'=>1,'id'=>$vo['id']]); ?>" data-value="id#<?php echo htmlentities($vo['id']); ?>;status#1" style='background:green;'>设为假人</a><?php endif; if(auth("edit_users_bank")): ?><a data-dbclick class="layui-btn layui-btn-xs" data-title="银行卡信息" data-modal='<?php echo url("admin/users/edit_users_bank"); ?>?id=<?php echo htmlentities($vo['id']); ?>'>银行卡信息</a><?php endif; if(auth("beizhu")): ?><a data-dbclick class="layui-btn layui-btn-xs layui-btn-danger" data-title="修改备注" data-modal='<?php echo url("admin/users/beizhu"); ?>?id=<?php echo htmlentities($vo['id']); ?>&type=1'>修改备注</a><?php endif; ?><a data-dbclick class="layui-btn layui-btn-xs layui-btn-warm" data-title="修改备注" data-modal='<?php echo url("admin/users/gugekouling"); ?>?id=<?php echo htmlentities($vo['id']); ?>&type=1'>谷歌口令</a></td><!--     <td class='text-left nowrap'>--><!--    <?php if(auth("edit_users")): ?>--><!--    <a data-dbclick class="layui-btn layui-btn-xs layui-btn-danger" data-title="暗扣设置" data-modal='<?php echo url("admin/users/edit_users_ankou"); ?>?id=<?php echo htmlentities($vo['id']); ?>'>暗扣设置</a>--><!--    <a data-dbclick class="layui-btn layui-btn-xs" data-title="编辑菜单" data-modal='<?php echo url("admin/users/ka_users"); ?>?id=<?php echo htmlentities($vo['id']); ?>'>卡单设置</a>--><!--    <a class="layui-btn layui-btn-xs layui-btn-warm" data-action="<?php echo url('edit_users_order',['id'=>$vo['id']]); ?>" data-value="id#<?php echo htmlentities($vo['id']); ?>;status#2" style='background:red;'>重置任务量</a>--><!--    <a data-dbclick class="layui-btn layui-btn-xs" data-title="编辑菜单" data-modal='<?php echo url("admin/users/edit_info"); ?>?id=<?php echo htmlentities($vo['id']); ?>'>单数模式</a>--><!--    <a data-dbclick class="layui-btn layui-btn-xs" data-title="编辑菜单" data-modal='<?php echo url("admin/users/edit_users"); ?>?id=<?php echo htmlentities($vo['id']); ?>'>编 辑</a>--><!--    <?php if(($vo['status'] == 1) and auth("edit_users_status")): ?>--><!--    <a class="layui-btn layui-btn-xs layui-btn-warm" data-action="<?php echo url('edit_users_status',['status'=>2,'id'=>$vo['id']]); ?>" data-value="id#<?php echo htmlentities($vo['id']); ?>;status#2" style='background:red;'>禁用</a>--><!--    <?php elseif(($vo['status'] == 2) and auth("edit_users_status")): ?>--><!--    <a class="layui-btn layui-btn-xs layui-btn-warm" data-action="<?php echo url('edit_users_status',['status'=>1,'id'=>$vo['id']]); ?>" data-value="id#<?php echo htmlentities($vo['id']); ?>;status#1" style='background:green;'>启用</a>--><!--    <?php endif; ?>--><!--    <a data-dbclick class="layui-btn layui-btn-xs layui-btn-danger" data-title="跟进记录" data-modal='<?php echo url("admin/users/edit_users_genjin"); ?>?id=<?php echo htmlentities($vo['id']); ?>&tel=<?php echo htmlentities($vo['tel']); ?>'>跟进记录</a>--><!--    <a data-dbclick class="layui-btn layui-btn-xs" data-title="银行卡信息" data-modal='<?php echo url("admin/users/edit_users_bk"); ?>?id=<?php echo htmlentities($vo['id']); ?>'>银行卡信息</a>--><!--    <a data-dbclick class="layui-btn layui-btn-xs layui-btn-danger" data-title="收货地址信息" data-modal='<?php echo url("admin/users/edit_users_address"); ?>?id=<?php echo htmlentities($vo['id']); ?>'>地址信息</a>--><!--    <a class="layui-btn layui-btn-xs layui-btn" onClick="del_user(<?php echo htmlentities($vo['id']); ?>)" style='background:red;'>删除</a>--><!--    <?php endif; ?>--><!--    <a class="layui-btn layui-btn-xs layui-btn" data-action="<?php echo url('edit_users_ewm',['status'=>2,'id'=>$vo['id']]); ?>" data-value="id#<?php echo htmlentities($vo['id']); ?>;status#<?php echo htmlentities($vo['invite_code']); ?>" style='background:red;'>刷新二维码</a>--><!--    <?php if(auth("tuandui")): ?>--><!--    <a data-dbclick class="layui-btn layui-btn-xs layui-btn-danger" data-title="查看团队" data-reload="true" data-open='<?php echo url("admin/users/tuandui"); ?>?id=<?php echo htmlentities($vo['id']); ?>'>查看团队</a>--><!--    <a data-dbclick class="layui-btn layui-btn-xs layui-btn-normal" data-title="查看账变" data-reload="true" data-open='<?php echo url("admin/users/caiwu"); ?>?id=<?php echo htmlentities($vo['id']); ?>'>账变</a>--><!--    <?php endif; ?>--><!--   </td>--></tr><?php endforeach; ?></tbody></table><script>
        function del_user(id){
            layer.confirm("确认要删除吗，删除后不能恢复",{ title: "删除确认" },function(index){
                $.ajax({
                    type: 'POST',
                    url: "<?php echo url('delete_user'); ?>",
                    data: {
                        'id': id,
                        '_csrf_': "<?php echo systoken('admin/users/delete_user'); ?>"
                    },
                    success:function (res) {
                        layer.msg(res.info,{time:2500});
                        location.reload();
                    }
                });
            },function(){});
        }
    </script><script>
        var table = layui.table;
        //转换静态表格
        var limit = Number('<?php echo htmlentities(app('request')->get('limit')); ?>'); 
        if(limit==0) limit=20;
        table.init('tab', {
            cellMinWidth:120,
            skin: 'line,row',
            size: 'lg',
            limit: limit
        });
    </script><?php if(empty($list) || (($list instanceof \think\Collection || $list instanceof \think\Paginator ) && $list->isEmpty())): ?><span class="notdata">没有记录哦</span><?php else: ?><?php echo (isset($pagehtml) && ($pagehtml !== '')?$pagehtml:''); ?><?php endif; ?></div></div></div><script>
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