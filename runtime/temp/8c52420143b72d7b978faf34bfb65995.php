<?php /*a:1:{s:70:"/www/wwwroot/movieboostvip.com/application/index/view/ctrl/junior.html";i:1668931804;}*/ ?>
<!DOCTYPE html><html lang="en" class="deeppurple-theme"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, viewport-fit=cover, user-scalable=no"><meta name="description" content=""><title><?php echo htmlentities(app('lang')->get('team_title')); ?></title><!-- Material design icons CSS --><link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"><!-- Roboto fonts CSS --><link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&amp;display=swap" rel="stylesheet"><!-- Bootstrap core CSS --><link href="/red/bootstrap/css/bootstrap.min.css" rel="stylesheet"><!-- Swiper CSS --><link href="/red/swiper/swiper-bundle.min.css" rel="stylesheet"><!-- Custom styles for this template --><link href="/red/style.css?v=V1.24" rel="stylesheet"><link rel="stylesheet" href="/static_new/css/public.css?v=V1.24"><style id="dialog-body-no-scroll">.body-no-scroll { position: absolute; overflow: hidden; width: 100%; }
    .text-white{
       color:black !important
    }
    </style></head><body class=""><div class="wrapper homepage"><!-- header --><div class="header" style="backdrop-filter:none"><div class="row no-gutters" style="backdrop-filter:none;background-image: linear-gradient(#FFFFFF, #D4E4FF);"><div class="col-auto"><a href="javascript:history.go(-1)" class="btn btn-link text-white"><i class="material-icons">chevron_left</i></a></div><div class="col text-center"><button class="btn w-100 text-white"><?php echo htmlentities(app('lang')->get('team_title')); ?></button></div><div class="col-auto"><a href="/index/my/msg" class="btn  btn-link text-white position-relative"><i class="material-icons">notifications_none</i></a></div></div></div><!-- header ends --><div class="container bg-template pb-4"><div class="row mt-4"><div class="col text-center" ><div class="btn-group w-100" role="group" aria-label="Basic" style=" hight:10px;"><button class="col btn btn-default btn-sm date-filter active"><span style="font-size:0.8rem"><?php echo htmlentities(app('lang')->get('team_all')); ?></span></button><button class="col btn btn-default btn-sm date-filter" start="<?php echo htmlentities($today); ?>"><span style="font-size:0.8rem"><?php echo htmlentities(app('lang')->get('team_today')); ?></span></button><button class="col btn btn-default btn-sm date-filter" start="<?php echo htmlentities($yesterday); ?>" end="<?php echo htmlentities($today); ?>"><span style="font-size:0.8rem"><?php echo htmlentities(app('lang')->get('team_yesterday')); ?></span></button><button class="col btn btn-default btn-sm date-filter" start="<?php echo htmlentities($week); ?>"><span style="font-size:0.8rem"><?php echo htmlentities(app('lang')->get('team_week')); ?></span></button></div></div></div><div class="row mt-1"><div class="col text-center"><a class="btn btn-link text-white"><i class="material-icons">date_range</i><span class="mt-1 date_range mbsc-comp" id="mobiscroll1615039385688">All</span></a></div></div></div><div class="container"><div class="card shadow border-0 mt-3"><div class="card-body border-bottom"><div class="row"><div class="col-7"><strong class="text-mute"><?php echo htmlentities(app('lang')->get('team_Summary')); ?>&nbsp;</strong></div><div class="col-5 text-right"><strong class="text-template team_rebate">Rs 0,00</strong></div></div></div><div class="card-body border-bottom"><div class="row"><div class="col"><p><span class="team_count">0</span><br><small class="text-mute text-secondary"><?php echo htmlentities(app('lang')->get('team_len')); ?></small></p></div><div class="col text-right"><p><span class="team_yj">Rs 0,00</span><br><small class="text-mute text-secondary"><?php echo htmlentities(app('lang')->get('team_ddyj')); ?></small></p></div></div></div></div></div><div class="container"><div class="card shadow border-0 mt-3"><div class="card-body border-bottom"><div class="row"><div class="col-7"><strong class="text-mute"><?php echo htmlentities(app('lang')->get('team_1')); ?>&nbsp;</strong><span class="badge badge-primary"><?php echo htmlentities(app('lang')->get('team_rate')); ?>&nbsp;<?php echo config('1_d_reward')*100; ?>%</span></div><div class="col-5 text-right"><strong class="text-template team1_rebate">Rs 0,00</strong></div></div></div><div class="card-body border-bottom"><div class="row"><div class="col"><p><span class="team1_count">0</span><br><small class="text-mute text-secondary"><?php echo htmlentities(app('lang')->get('team_len')); ?></small></p></div><div class="col text-right"><p><span class="team1_yj">Rs 0,00</span><br><small class="text-mute text-secondary"><?php echo htmlentities(app('lang')->get('team_ddyj')); ?></small></p></div></div></div></div></div><div class="container"><div class="card shadow border-0 mt-3"><div class="card-body border-bottom"><div class="row"><div class="col-7"><strong class="text-mute"><?php echo htmlentities(app('lang')->get('team_2')); ?>&nbsp;</strong><span class="badge badge-success"><?php echo htmlentities(app('lang')->get('team_rate')); ?>&nbsp;<?php echo config('2_d_reward')*100; ?>%</span></div><div class="col-5 text-right"><strong class="text-template ml-2 team2_rebate">Rs 0,00</strong></div></div></div><div class="card-body border-bottom"><div class="row"><div class="col"><p><span class="team2_count">0</span><br><small class="text-mute text-secondary"><?php echo htmlentities(app('lang')->get('team_len')); ?></small></p></div><div class="col text-right"><p><span class="team2_yj">Rs 0,00</span><br><small class="text-mute text-secondary"><?php echo htmlentities(app('lang')->get('team_ddyj')); ?></small></p></div></div></div></div></div><div class="container"><div class="card shadow border-0 mt-3"><div class="card-body border-bottom"><div class="row"><div class="col-7"><strong class="text-mute"><?php echo htmlentities(app('lang')->get('team_3')); ?>&nbsp;</strong><span class="badge badge-danger"><?php echo htmlentities(app('lang')->get('team_rate')); ?>&nbsp;<?php echo config('3_d_reward')*100; ?>%</span></div><div class="col-5 text-right"><strong class="text-template ml-2 team3_rebate">Rs 0,00</strong></div></div></div><div class="card-body border-bottom"><div class="row"><div class="col"><p><span class="team3_count">0</span><br><small class="text-mute text-secondary"><?php echo htmlentities(app('lang')->get('team_len')); ?></small></p></div><div class="col text-right"><p><span class="team3_yj">Rs 0,00</span><br><small class="text-mute text-secondary"><?php echo htmlentities(app('lang')->get('team_ddyj')); ?></small></p></div></div></div></div></div></div><div class="mint-popup mint-datetime mint-popup-bottom" style="z-index: 2003; display: none;"><div class="picker mint-datetime-picker"><div class="picker-toolbar"><span class="mint-datetime-action mint-datetime-cancel">Cancel</span><span class="mint-datetime-action mint-datetime-confirm">Confirm</span></div><div class="picker-items"></div></div></div><!-- jquery, popper and bootstrap js --><script src="/red/jquery-3.3.1.min.js"></script><script src="/red/popper.min.js"></script><script src="/red/bootstrap/js/bootstrap.min.js"></script><!-- swiper js --><script src="/red/swiper/swiper-bundle.min.js"></script><!-- cookie js --><script src="/red/jquery.cookie.js"></script><script charset="utf-8" src="/static_new/js/dialog.min.js"></script><script charset="utf-8" src="/static_new/js/common.js"></script><script charset="utf-8" src="/static_new/mobiscroll/mobiscroll.custom-3.0.0-beta6.min.js"></script><link rel="stylesheet" href="/static_new/mobiscroll/mobiscroll.custom-3.0.0-beta6.min.css"><!-- template custom js --><script src="/red/main.js?v=V1.24"></script><!-- page level script --><script>$(function() {

        $('.date-filter').click(function() {
          $('.date-filter').removeClass('active');
          $(this).css('background', '');
          $(this).addClass('active');
          query($(this).attr('start'), $(this).attr('end'));
        });
        $('.date-filter').eq(0).click();

        //https://docs.mobiscroll.com/3-2-6/jquery/range
        //https://docs.mobiscroll.com/jquery/languages
        $('.date_range').mobiscroll().range({
          theme: 'ios',
          lang: "en-us",
          display: 'bottom',
          showSelector: false,
          dateFormat: "yyyy-mm-dd",
          buttons: [
          //'set',
          {
            text: "OK",
            icon: 'checkmark',
            handler: 'set'
          },
          //'cancel',
          {
            text: "Cancel",
            icon: 'close',
            handler: 'cancel',
          },
          ],
          onSet: function(data, inst) {
            var v = data.valueText.split(" - ");
            var d0 = v[0];
            var d1 = v[1];
            $('.date-filter').removeClass('active');
            //fix the hover issue
            var bg = $('.date-filter:not(:hover)').eq(0).css('background');
            $('.date-filter:hover').css('background', bg);
            query(d0, d1);
          }
        });
      });
      function query(start, end) {
        var loading;
        $.ajax({
          type: "get",
          url: "?ajax=1",
          data: {
            start: start,
            end: end
          },
          dataType: "json",
          beforeSend: function() {
            loading = $(document).dialog({
              type: 'notice',
              infoIcon: '/static_new/img/loading.gif',
              infoText: 'loading...',
              autoClose: 0
            });
          },
          success: function(data) {
            loading.close();
            if (data) {
              $('.date_range').text(data.date_range);
              $('.team_count').text(data.team_count);
              $('.team_yj').text(data.team_yj);
              $('.team_rebate').text(data.team_rebate);
              $('.team1_count').text(data.team1_count);
              $('.team1_yj').text(data.team1_yj);
              $('.team1_rebate').text(data.team1_rebate);
              $('.team2_count').text(data.team2_count);
              $('.team2_yj').text(data.team2_yj);
              $('.team2_rebate').text(data.team2_rebate);
              $('.team3_count').text(data.team3_count);
              $('.team3_yj').text(data.team3_yj);
              $('.team3_rebate').text(data.team3_rebate);
            }
          },
          error: function(data) {
            alert(data);
          }
        });
      }</script></body></html>