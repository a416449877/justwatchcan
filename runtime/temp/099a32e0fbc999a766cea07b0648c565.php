<?php /*a:1:{s:69:"/www/wwwroot/movieboostvip.com/application/index/view/my/headimg.html";i:1680269392;}*/ ?>
<!DOCTYPE html><!-- saved from url=(0042)http://qiang6-www.baomiche.com/#/SetupHead --><html data-dpr="1" style="font-size: 37.5px;"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"><meta name="viewport" content="width=device-width,user-scalable=no,initial-scale=1,maximum-scale=1,minimum-scale=1"><title><?php echo htmlentities(app('lang')->get('set_head_img')); ?></title><link href="/static_new6/css/app.7b22fa66c2af28f12bf32977d4b82694.css" rel="stylesheet"><script charset="utf-8" src="/static_new/js/jquery.min.js"></script><script charset="utf-8" src="/static_new/js/common.js"></script><link rel="stylesheet" href="/static_new/css/public.css"><script charset="utf-8" src="/static_new/js/dialog.min.js"></script><style type="text/css" title="fading circle style">
        .circle-color-8 > div::before {
            background-color: #ccc;
        }
    </style></head><body style="font-size: 12px;"><div id="app"><div data-v-530a0f34="" class="main"><div data-v-530a0f34="" class="header" style="backdrop-filter:none;background-image: linear-gradient(#330000, #330000);"><div class="left_btn" onclick="window.history.back(-1)"><img
                    src="/static_en/img/Icon-04.png"
                    alt="" class="return"></div><div class="Maintitle"><h3><?php echo htmlentities(app('lang')->get('set_head_img')); ?></h3></div><div class="right_btn"></div></div><div data-v-530a0f34="" class="box"><div data-v-530a0f34="" class="head"><img data-v-530a0f34="" id="thisImg" src="<?php echo htmlentities($info['headpic']); ?>" alt="" onerror="this.src='/static_indonesia/headimg/1.png'" class=""><button data-v-530a0f34="" class="save-btn" style="color:#fff;"><?php echo htmlentities(app('lang')->get('lxset')); ?></button></div><ul data-v-530a0f34="" id="myUi"><li data-v-530a0f34=""><img data-v-530a0f34="" src="/static_indonesia/headimg/1.png" alt="" class=""></li><li data-v-530a0f34=""><img data-v-530a0f34="" src="/static_indonesia/headimg/2.png" alt="" class=""></li><li data-v-530a0f34=""><img data-v-530a0f34="" src="/static_indonesia/headimg/3.png" alt="" class=""></li><li data-v-530a0f34=""><img data-v-530a0f34="" src="/static_indonesia/headimg/4.png" alt="" class=""></li><li data-v-530a0f34=""><img data-v-530a0f34="" src="/static_indonesia/headimg/5.png" alt="" class=""></li><li data-v-530a0f34=""><img data-v-530a0f34="" src="/static_indonesia/headimg/6.png" alt="" class=""></li><li data-v-530a0f34=""><img data-v-530a0f34="" src="/static_indonesia/headimg/7.png" alt="" class=""></li><li data-v-530a0f34=""><img data-v-530a0f34="" src="/static_indonesia/headimg/8.png" alt="" class=""></li><li data-v-530a0f34=""><img data-v-530a0f34="" src="/static_indonesia/headimg/9.png" alt="" class=""></li><li data-v-530a0f34=""><img data-v-530a0f34="" src="/static_indonesia/headimg/10.png" alt="" class=""></li><li data-v-530a0f34=""><img data-v-530a0f34="" src="/static_indonesia/headimg/11.png" alt="" class=""></li><li data-v-530a0f34=""><img data-v-530a0f34="" src="/static_indonesia/headimg/12.png" alt="" class=""></li><li data-v-530a0f34=""><img data-v-530a0f34="" src="/static_indonesia/headimg/13.png" alt="" class=""></li><li data-v-530a0f34=""><img data-v-530a0f34="" src="/static_indonesia/headimg/15.png" alt="" class=""></li><li data-v-530a0f34=""><img data-v-530a0f34="" src="/static_indonesia/headimg/16.png" alt="" class=""></li></ul></div></div></div><script>
    var pic= '';
    $(function () {

        $("#myUi li img").on('click', function () {
            $('#thisImg').attr('src',$(this).attr('src'));
            pic = $(this).attr('src');
        })

        /*点击登录*/
        $(".save-btn").on('click', function () {
            if (1) {
                var loading = null;
                $.ajax({
                    url: "",
                    data: {pic:pic},
                    type: 'POST',
                    beforeSend: function () {
                        loading = $(document).dialog({
                            type: 'notice',
                            infoIcon: '/static_new/img/loading.gif',
                            infoText: 'Loading',
                            autoClose: 0
                        });
                    },
                    success: function (data) {

                        if (data.code == 0) {
                            $(document).dialog({infoText: '<?php echo htmlentities(app('lang')->get('save_ok')); ?>'});
                            setTimeout(function () {
                                window.history.back(-1);
                            }, 2000);
                        } else {
                            loading.close();
                            $(document).dialog({infoText: data.info});
                        }
                    }
                });
            }
        });

    })
</script></body></html>