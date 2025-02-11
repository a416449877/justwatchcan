<?php /*a:1:{s:68:"/www/wwwroot/movieboostvip.com/application/index/view/my/mybank.html";i:1736996715;}*/ ?>
<!DOCTYPE html><html lang="zh"><head><meta charset="UTF-8"><meta http-equiv="X-UA-Compatible" content="IE=edge"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Withdrawal information </title><link rel="stylesheet" href="/static_en/css/css.css"></head><body class="bodybg"><div class="headerfix"><div class="back"><a href="javascript:;"><img src="/static_en/img/Icon-04.png" alt=""></a></div><h2>Withdrawal information </h2></div><div class="withdrawalinfo"><div class="t">Dear user, in order to protect the security of your funds, please do not enter your bank account passwords, Our staff will not ask for your bank card password</div><div class="form"><style>
			        .withdrawalinfo ul li select {
    line-height: 55px;
    height: 55px;
    background: none;
    text-align: right;
    flex: 1;
    font-size: 14px;
    color: #0d152c;
}
			    </style><ul><li><p>Recepient's name</p><input type="text" name="fullname" placeholder="Please enter your account name" autocomplete="off" value="<?php echo htmlentities($info['username']); ?>" disabled ></li><li><p>Bank</p><input type="text" name="bankname" placeholder="Please input the bank name" autocomplete="off" value="<?php echo htmlentities($info['bankname']); ?>" disabled ></li><li><p>Account number</p><input type="text" name="cardnum" placeholder="Please input your bank account number" autocomplete="off" value="<?php echo htmlentities($info['cardnum']); ?>" disabled ></li></ul></div></div><script type="text/javascript" src="/static_en/js/jquery.js"></script><script type="text/javascript" src="/static_en/js/public.js"></script></body></html>