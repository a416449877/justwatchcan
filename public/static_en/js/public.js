var msgpop_html=`<div class="msgpop"></div>`;
document.write(msgpop_html);

var alertpop_html=`
<div class="alertpop">
	<div class="box">
		<div class="h2">Reelgood</div>
		<article></article>
		<div class="btn"><a href="javascript:;" onclick="alertHide()">Confirm</a></div>
	</div>
</div>`;
document.write(alertpop_html);

var selectpop_html=`
	<div class="selectpop">
		<div class="bg">
			<div class="box">
				<ul>
					<li>female</li>
					<li>male</li>
				</ul>
				<div class="selectpopclose" onclick="selectHide()">Cancel</div>
			</div>
		</div>	
	</div>
`;
document.write(selectpop_html);

var loadingpop_html=`
	<div class="loading"><img src="/static_en/img/loading.gif" alt=""><p>loading</p></div>
`;
document.write(loadingpop_html);


var confirmpop_html=`
<div class="confirmpop">
	<div class="box">
		<div class="h2">Reelgood</div>
		<article></article>
		<div class="btn"><a href="javascript:;" class="confirmHide" onclick="confirmHide()">Cancel</a><a href="javascript:;" class="confirmsubmit">Confirm</a></div>
	</div>
</div>`;
document.write(confirmpop_html);


// 返回
$('.headerfix .back').click(function(){
	history.go(-1);
});

//信息提示
function msgShow(msg,t=1500){
	$('.msgpop').html(msg).fadeIn(function(){
		setTimeout(function(){
			$('.msgpop').html('').hide();
		},t);
	});
}


// 请求接口
function Axios(url,data={}){
	loadingShow();
	/*
		$.post(url,data,function(res){
			loadingHide();
			if(res==1){
				alertShow('Incorrect user account or <br/> password!');
			}else{
				alertShow('Incorrect user account or <br/> password!');
			}
		},'json');
	*/
 }
 
 // 确定弹窗
function alertShow(msg=''){
	$('body').addClass('open');
	$('.alertpop article').html(msg);
	$('.alertpop').show();
	return false;
}
 
function alertHide(){
	$('body').removeClass('open');
	$('.alertpop').hide();
}

// 确定取消
function confirmShow(msg=''){
	$('body').addClass('open');
	$('.confirmpop article').html(msg);
	$('.confirmpop').show();
	return false;
}
 
function confirmHide(){
	$('body').removeClass('open');
	$('.confirmpop').hide();
}

// 下拉选择框
function selectShow(){
	$('.selectpop').fadeIn(0,function(){
		$('.selectpop .box').animate({'bottom':'10px'});
	});
}
function selectHide(){
	$('.selectpop .box').animate({'bottom':'-100%'},500,function(){
		$('.selectpop').fadeOut(0);
	});
}

// Loading加载
function loadingShow(){
	$('.loading').show();
}

function loadingHide(){
	$('.loading').hide();
}