<?php /*a:1:{s:76:"/www/wwwroot/movieboostvip.com/application/index/view/ctrl/edit_trading.html";i:1680199850;}*/ ?>
<!DOCTYPE html><html lang="zh"><head><meta charset="UTF-8"><meta http-equiv="X-UA-Compatible" content="IE=edge"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Security PIN</title><link rel="stylesheet" href="/static_en/css/css.css"></head><body class="bodybg"><div class="headerfix"><div class="back"><a href="javascript:;"><img src="/static_en/img/Icon-04.png" alt=""></a></div><h2>Security PIN</h2></div><div class="personalform"><form action="" method="post" name="formpassword" onsubmit="return checkPassword()"><ul><li><p>Old Password</p><input type="text" name="oldpassword" placeholder="Type Old Password" autocomplete="off"></li><li><p>New Password</p><input type="password" name="newpassword" placeholder="Type New Password" autocomplete="off"></li><li><p>Confirm New Password</p><input type="password" name="newpassworded" placeholder="Type Confirm New Password" autocomplete="off"></li></ul><button type="submit">Confirm</button></form><div class="tip">Forgot the old password, please contact customer service to retrieve it</div></div><script type="text/javascript" src="/static_en/js/jquery.js"></script><script type="text/javascript" src="/static_en/js/public.js"></script><script type="text/javascript">
	var num="<?php echo htmlentities($num); ?>";
		function checkPassword(){
			var fm=document.formpassword;
			var oldpassword=fm.oldpassword.value;
			var newpassword=fm.newpassword.value;
			var newpassworded=fm.newpassworded.value;

			
			if( newpassword=='' || newpassworded==''){
				msgShow('Please enter the <br/>complete bank information'); 
				return false;
			}
			
			if (newpassword!==newpassworded) {
				msgShow('The verification for your <br/> new password is mismatched');
				return false;
			}
			
			$.ajax({
              url: '/index/ctrl/edit_trading',
              data: {new_pwd:newpassword,old_pwd:oldpassword,num:num},
              type: 'POST',
              success: function(data) {
                if (data.code == 0) {
				  msgShow(data.info); 
                  setTimeout(function() {
                    window.location.href = '/index/my/index';
                  },
                  2000);
                } else {
                  msgShow(data.info); 
                }
              }
            });
			
			return false;
		}
	</script></body></html>