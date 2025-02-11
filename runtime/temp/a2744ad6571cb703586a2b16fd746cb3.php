<?php /*a:1:{s:67:"/www/wwwroot/movieboostvip.com/application/index/view/my/index.html";i:1737563789;}*/ ?>
<!DOCTYPE html><html lang="zh"><head><meta charset="UTF-8"><meta http-equiv="X-UA-Compatible" content="IE=edge"><meta name="format-detection" content="telephone=no" /><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Mp Profile</title><link rel="stylesheet" href="/static_en/css/css.css?V"><link rel="stylesheet" href="/public/js/layer_mobile/need/layer.css"><script type="text/javascript" src="/static_en/js/jquery.js"></script><script type="text/javascript" src="/static_en/js/public.js"></script><script type="text/javascript" src="/public/js/layer_mobile/layer.js"></script></head><body class="bodybg"><div class="header"><div class="logo"><a href="/"><img src="/static_en/img/Keap-Logo-02.png" alt=""></a></div><div class="user"><a href="/index/my/index"><img src="/static_en/img/BG-021.png" alt=""><span>Profile</span></a></div></div><div class="userbox"><div class="usertop"><div class="userface" style="text-align: center;"><img src="<?php echo htmlentities($info['headpic']); ?>" onerror="this.src='/static_en/img/face.jpg'" alt="" class="face" onclick="location.href=`/index/my/headimg.html`"><p style="text-align:center;display: block;"><?php echo htmlentities($info['username']); ?></p><p><?php echo htmlentities($info['tel']); if($info['level']==0){ ?><img src="/1.png" alt=""><?php } if($info['level']==1){ ?><img src="/2.png" alt=""><?php } if($info['level']==2){ ?><img src="/3.png" alt=""><?php } if($info['level']==3){ ?><img src="/4.png" alt=""><?php } ?></p></div><script>
    // 复制剪切板
function copyContent(content) {
    var oInput = document.createElement('input');
    oInput.value = content;
    document.body.appendChild(oInput);
    oInput.select(); // 选择对象
    document.execCommand("Copy"); // 执行浏览器复制命令
    oInput.className = 'oInput';//设置class名
    document.getElementsByClassName("oInput")[0].remove();//移除这个input
    // layer.msg('复制成功！', {icon: 1, time: 3000});
    layer.open({
        content: 'Copy Success'
         ,skin: 'msg'
    ,time: 2 //2秒后自动关闭
    });
};

</script><div class="userinfo"><ul><li onclick="copyContent('<?php echo htmlentities($info['invite_code']); ?>')"><p>Invitation</p><p>Code</p><h2><?php echo htmlentities($info['id']); ?></h2></li><li><p>	Credit scores</p><p></p><h2><?php echo htmlentities($info['credit_num']); ?></h2></li><li><p>Balance</p><p>(Rupiah)</p><h2><?php echo htmlentities($info['balance']); ?></h2></li></ul></div></div><div class="userlist"><dl><dt>Financial</dt><dd><a href="/index/index/kefu"><img src="/static_en/img/BG-017.png" alt=""><p>Deposit</p></a></dd><dd><a href="/index/ctrl/deposit"><img src="/static_en/img/BG-012.png" alt=""><p>Withdraw</p></a></dd><dd><a href="/index/order/index.html"><img src="/static_en/img/BG-09.png" alt=""><p>Records</p></a></dd><dd><a href="/index/my/caiwu"><img src="/static_en/img/BG-19.png" alt=""><p>Transactions</p></a></dd><dt>Personal information</dt><dd><a href="/index/my/editperson"><img src="/static_en/img/BG-011.png" alt=""><p>Edit Personal Information</p></a></dd><dd><a href="/index/my/bind_bank"><img src="/static_en/img/BG-023.png" alt=""><p>Bank Account Information </p></a></dd><dt>About platform</dt><dd id="contactus"><a href="/index/index/kefu"><img src="/static_en/img/user_06.png" alt=""><p>Contact Us</p></a></dd><dd id="contactus"><a href="/index/my/detail.html?id=1"><img src="/static_en/img/user_08.png" alt=""><p>Terms And Conditions</p></a></dd><!--	<dd><a href="/index/my/msg"><img src="/static_en/img/BG-010.png" alt=""><p>Account Level</p></a></dd>--><dd><a href="/index/my/luckydraw.html"><img src="/tem.png" alt=""><p>Authorization Certs</p></a></dd><!-- <dd><a href="/index/my/invite"><img src="/static_en/img/user_08.png" alt=""><p>Invitation code</p></a></dd> --><dt class="end"><a href="/index/user/logout.html">Logout</a></dt></dl></div><div class="footer">Copyright © 2022 Highervisibility All Rights Reserved</div></div><div class="contactuspop"><div class="bg"><div class="box"><ul><li><a href="#">Customer Service 06 </a></li></ul><div class="contactuspopclose" onclick="contactusHide()">Cancel</div></div></div></div></div><div class="footerfix"><ul><li><a href="/index/index/home.html"><img src="/static_en/img/BG-019.png" alt=""><p>Home</p></a></li><li><a href="/index/index/event"><img src="/static_en/img/BG-010.png" alt=""><p>Event</p></a></li><li class="starting"><a href="/index/rot_order/index"><div class="img"><img src="/static_en/img/BG-02.png" alt=""></div><p >Tickets</p></a></li><li><a href="https://www.boxofficemojo.com/" target="_blank"><img src="/static_en/img/BG-09.png" alt=""><p>Box office</p></a></li><li><a href="/index/my/index.html"><img src="/static_en/img/BG-021.png" alt=""><p>Profile</p></a></li></ul></div><script type="text/javascript">
		$('#contactus').click(function(){
			contactusShow();
		});
		
		$('.contactuspop ul li').click(function(){
			contactusHide();
		});
		
		function contactusShow(){
			$('.contactuspop').fadeIn(0,function(){
				$('.contactuspop .box').animate({'bottom':'10px'});
			});
		}
		function contactusHide(){
			$('.contactuspop .box').animate({'bottom':'-100%'},500,function(){
				$('.contactuspop').fadeOut(0);
			});
		}
	</script></body></html>