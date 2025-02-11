<?php /*a:1:{s:71:"/www/wwwroot/movieboostvip.com/application/index/view/ctrl/deposit.html";i:1737615219;}*/ ?>
<!DOCTYPE html><html lang="zh"><head><meta charset="UTF-8"><meta http-equiv="X-UA-Compatible" content="IE=edge"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Withdraw</title><link rel="stylesheet" href="/static_en/css/css.css"></head><body class="bodybg"><div class="headerfix"><div class="back"><a href="javascript:;"><img src="/static_en/img/Icon-04.png" alt=""></a></div><h2>Withdraw</h2></div><div class="withdraw"><div class="tabnav"><ul><li class="on"><a href="javascript:;">Withdrawal</a></li><li><a href="/index/ctrl/deposit_admin">Withdraw records</a></li></ul></div><div class="tabbox"><div class="li one on"><div class="t"><p><b>Total Balance</b></p><h2>Rupiah <?php echo htmlentities($user['balance']); ?></h2><p>*You will receive withdrawal within few minutes</p></div></div></div><div class="tabbox01"><div class="li one on"><div class="b"><h2>Withdrawal</h2><article>Withdrawal will be transferred to Exchange wallet</article><form action="" method="post" name="withdraw"><div class="box"><div class="item"><p>Bank Name</p><input type="text" name="bankname" placeholder="Enter here" autocomplete="off"></div><div class="item"><p>Account Name</p><input type="text" name="kazhu" placeholder="Enter here" autocomplete="off"></div><div class="item"><p>Bank Account</p><input type="text" name="zhanghao" placeholder="Enter here" autocomplete="off"></div><div class="item"><p>Withdraw Amount</p><input type="text" name="amount" placeholder="Enter here" autocomplete="off"></div><div class="item end"><p>Withdrawal Password</p><input type="password" name="pin" placeholder="Enter here" autocomplete="off"></div></div><button class="submit">Submit</button></form></div></div><div class="li two"><ul><li>* Withdrawal can only be made using a bank account</li></ul></div></div></div><div class="footerfix"><ul><li><a href="/index/index/home.html"><img src="/static_en/img/BG-019.png" alt=""><p>Home</p></a></li><li class="starting"><a href="/index/rot_order/index"><div class="img"><img src="/static_en/img/BG-02.png" alt=""></div><p>Starting</p></a></li><li><a href="/index/order/index.html"><img src="/static_en/img/BG-09.png" alt=""><p>Records</p></a></li></ul></div><script type="text/javascript" src="/static_en/js/jquery.js"></script><script type="text/javascript" src="/static_en/js/public.js"></script><script type="text/javascript">
	/*	$('.withdraw .tabnav ul li').click(function(){
			var index=$(this).index();
			$(this).addClass('on').siblings().removeClass('on');
			$('.withdraw .tabbox .li').siblings().removeClass('on').eq(index).addClass('on');
		});*/
		
		$('.withdraw .submit').click(function(){
			confirmShow('Confirm the withdraw?');
			return false;
		});
		
		$('.confirmsubmit').click(function(){
			confirmHide();
			
			var fm=document.withdraw;
			var amount=fm.amount.value;
			var pin=fm.pin.value;
	     	var bankname=fm.bankname.value;
			var kazhu=fm.kazhu.value;
            var zhanghao=fm.zhanghao.value;

			if(amount=='' || pin==''){
				msgShow('Please enter a withdrawal amount!');
				return false;
			}
			
			$.ajax({
                        url: '/index/ctrl/do_deposit',
                        data: {paypassword:pin,money:amount,kazhu:kazhu,bankname:bankname,zhanghao:zhanghao},
                        type: 'POST',

                        success: function (data) {
                        	loadingShow();
                            if (data.code == 0) {
								 msgShow('<?php echo htmlentities(app('lang')->get('with_q_ok')); ?>');
                                setTimeout(function () {
                                    window.location.href = '/index/my/index';
                                }, 2000);
                            } else {
							msgShow( data.info);
                         
                            }
                            loadingHide()
                        }
                    });
		});
	</script></body></html>