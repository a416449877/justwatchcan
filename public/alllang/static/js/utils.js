~function ($) {
  "use strict";
  var defaults = {
    autoInit: true,
    router: false,
  };
  $.smConfig = $.extend(defaults, $.config);
}($);

var formatReg = {
  phoneNumber: new RegExp(/^1{1}[3456789]{1}\d{9}$/),
  creditCard: new RegExp(/^([0-9]{13,19})$/),
  email: new RegExp(/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/),
  password: new RegExp(/^(([_a-zA-Z]+[0-9]+)|([0-9]+[_a-zA-Z]+))[_a-zA-Z0-9]*$/),
  securityCode: new RegExp(/^([0-9]{5})$/),
  qqCode: new RegExp(/^([1-9]([0-9]{5,13}))$/),
  blankSpace: new RegExp(/\s/),
};

var monthNames = ['一月', '二月', '三月', '四月', '五月', '六月', '七月', '八月', '九月', '十月', '十一月', '十二月'];
var monthNamesShort = monthNames;
var dayNames = ['周日', '周一', '周二', '周三', '周四', '周五', '周六'];
var dayNamesShort = dayNames;
var defaultCalendarOptions = {
  // monthNames: monthNames,
  monthNamesShort: monthNamesShort,
  // dayNames: dayNames,
  // dayNamesShort: dayNamesShort,
};

var overscroll = function(el) {
  el.addEventListener('touchstart', function() {
    var top = el.scrollTop,
        totalScroll = el.scrollHeight,
        currentScroll = top + el.offsetHeight;
    if (top === 0) {
      el.scrollTop = 1;
    } else if (currentScroll === totalScroll) {
      el.scrollTop = top - 1;
    }
  });
  el.addEventListener('touchmove', function(event) {
  if(el.offsetHeight < el.scrollHeight)
    event._isScroller = true;
  });
}
if (document.querySelector('.scroll-wrapper') != null) {
  overscroll(document.querySelector('.scroll-wrapper'));
  document.body.addEventListener('touchmove', function (e) {
    if(e._isScroller) return;
    e.preventDefault(); 
  }, {passive: false});
}

$('.page-back').on('click', function() {
  window.history.go(-1);
});

function clearNoNum(obj) { 
  obj.value = obj.value.replace(/[^\d.]/g,""); // 清除数字和'.'以外的字符
  obj.value = obj.value.replace(/\.{2,}/g,"."); //只保留第一个. 清除多余的
  obj.value = obj.value.replace(".","$#$").replace(/\./g,"").replace("$#$","."); 
  obj.value = obj.value.replace(/^(\-)*(\d+)\.(\d\d).*$/,'$1$2.$3'); //只能输入两个小数 
  if(obj.value.indexOf(".")< 0 && obj.value !="") { // 控制的是如果没有小数点，首位不能为类似于 01、02的金额 
    obj.value= parseFloat(obj.value); 
  } 
} 