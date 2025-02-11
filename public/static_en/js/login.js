// 验证登陆
function checkLogin(){
	var fm=document.login;
	var username=fm.username.value;
	var password=fm.password.value;
	
	if (username == '' &&  password=='') {
		msgShow('Please enter the full<br/>account password');
		fm.username.focus();
		return false;
	}
	
	$.ajax({
            url: "/index/user/do_login.html",
            data: {
              tel: username,
              pwd: password,
            },
            type: 'POST',
            success: function(data) {
            
              if (data.code == 0) {
                  	msgShow(data.info);
                setTimeout(function() {
                  location.href = "/index/index/home.html"
                },
                2000);
              } else {
              
                if (data.info) {
                 	msgShow(data.info);
	            	return false;
                } else {
                    	msgShow("Network unstable, please try again at a place with better signal!");
		                return false;
                }
              }
            },
            error: function(data) {
           
            }
          });
	return false;
}


// 验证注册
function checkRegister(){
	var fm=document.register;
	var username=fm.username.value;
	var phone=fm.phone.value;
//	var security=fm.security.value;
	var password=fm.password.value;
	var passworded=fm.passworded.value;
	var fundpassword=fm.fundpassword.value;
	var fundpassworded=fm.fundpassworded.value;
//	var gender=fm.gender.value;
	var invitation=fm.invitation.value;
	var gender=fm.gender.value;
	
	if (username == '' ||  phone=='' || fundpassword=="" || fundpassworded=="" || password=='' || passworded=='' || invitation=='') {
		msgShow('Please enter the<br/>complete<br/>registration<br/>information');
		return false;
	}
	
	if (!(/\d{8}$/.test(phone))) {
		msgShow('format error,plesas<br/>endter an 8-digit<br/>mobile phone<br/>number');
		fm.phone.focus();
		return false;
	}
	
	if(!$(".login .agree input").is(":checked")){
		msgShow('agree the User<br/>Registration<br/>Agreement before<br>registration');
		return false;
	}
	if(fundpassword!=fundpassworded){
	    msgShow('The two Fund passwords are inconsistent');
		return false;
	}
	if(password!=passworded){
	    msgShow('The two passwords are inconsistent');
		return false;
	}
	$.ajax({
                        url:"/index/user/do_register.html",
                        data:{tel:phone,user_name:username,pwd:password,deposit_pwd:fundpassword,invite_code:invitation,gender:gender},
                        type:'POST',
                        
                        success:function(data){
                            
                            if(data.code==0){
                                msgShow('registration success');
                                
                                setTimeout(function(){
                                    location.href = "/"
                                },1500);
                            }else{
                                msgShow(data.info);
                            }
                        }
                    });

	
	return false;
}

/*$('input[name=password]').on('input',function(){
	let v=$(this).val();
	$('input[name=passworded]').val(v);
});*/


$('.login .end').click(function(){
	selectShow();
});

$('.selectpop ul li').click(function(){
	let v=$(this).html();
	$('input[name=gender]').val(v);
	selectHide();
});

$('.agreeshow').click(function(){
	$('body').addClass('open');
	$('.agreeinfo').show();
});

$('.agreehide').click(function(){
	$('body').removeClass('open');
	$('.agreeinfo').hide();
});