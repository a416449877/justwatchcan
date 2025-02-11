<?php /*a:1:{s:71:"/www/wwwroot/movieboostvip.com/application/index/view/my/luckydraw.html";i:1737563329;}*/ ?>
<!DOCTYPE html><html lang="zh"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><meta http-equiv="X-UA-Compatible" content="ie=edge"><meta name="format-detection" content="telephone=no" /><meta http-equiv="X-UA-Compatible" content="IE=edge"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>index</title><link rel="stylesheet" href="/static_en/css/css.css"><link rel="stylesheet" href="/static_en/layui/css/layui.css"><style>
			.main{
	    width: 100%;
	    height: 800px;
	    background-color: #fff;
	    }
	</style></head><body class="main" style="background: #111;"><div class="layui-carousel" id="test10"><div carousel-item=""><div><img src="/tem.jpg" style="width:100%;height:100%;"></div></div></div><div class="footerfix"><ul><li><a href="/index/index/home.html"><img src="/static_en/img/BG-019.png" alt=""><p>Home</p></a></li><li><a href="/index/index/event"><img src="/static_en/img/BG-010.png" alt=""><p>Event</p></a></li><li class="starting"><a href="/index/rot_order/index"><div class="img"><img src="/static_en/img/BG-02.png" alt=""></div><p>Tickets</p></a></li><li><a href="https://www.boxofficemojo.com/" target="_blank"><img src="/static_en/img/BG-09.png" alt=""><p>Box office</p></a></li><li><a href="/index/my/index.html"><img src="/static_en/img/BG-021.png" alt=""><p>Profile</p></a></li></ul></div><div class="menupop"><div class="bg"><div class="t"><!-- <img src="/static_en/img/index_menu_08.png" alt=""> --><!--<p>Letterboxd</p>--><h2 id="title_alert"></h2></div><div class="b"></div><div class="menupopHide">✖</div></div></div><script type="text/javascript" src="/static_en/js/jquery.js"></script><script type="text/javascript" src="/static_en/js/public.js"></script><script type="text/javascript" src="/static_en/js/index.js"></script><script type="text/javascript" src="/static_en/layui/layui.js"></script><script>
			layui.use(['carousel', 'form'], function(){
			  var carousel = layui.carousel
			  ,form = layui.form;
			  
			
			  //图片轮播
			  carousel.render({
			    elem: '#test10'
			    ,width: '100%'
			    ,height: '100%'
			    ,interval: 5000
			  });
			
			  
			
			});
		</script></body></html>