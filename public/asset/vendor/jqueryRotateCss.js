/*!
 /**
 * Monkey patch jQuery 1.3.1+ to add support for setting or animating CSS
 * scale and rotation independently.
 * https://github.com/zachstronaut/jquery-animate-css-rotate-scale
 * Released under dual MIT/GPL license just like jQuery.
 * 2009-2012 Zachary Johnson www.zachstronaut.com
 */
!function(t){function a(t){var a=t.data("_ARS_data");return a||(a={rotateUnits:"deg",scale:1,rotate:0},t.data("_ARS_data",a)),a}function e(t,a){t.css("transform","rotate("+a.rotate+a.rotateUnits+") scale("+a.scale+","+a.scale+")")}t.fn.rotate=function(r){var o,n=t(this),s=a(n);return void 0===r?s.rotate+s.rotateUnits:((o=r.toString().match(/^(-?\d+(\.\d+)?)(.+)?$/))&&(o[3]&&(s.rotateUnits=o[3]),s.rotate=o[1],e(n,s)),this)},t.fn.scale=function(r){var o=t(this),n=a(o);return void 0===r?n.scale:(n.scale=r,e(o,n),this)};var r=t.fx.prototype.cur;t.fx.prototype.cur=function(){return"rotate"==this.prop?parseFloat(t(this.elem).rotate()):"scale"==this.prop?parseFloat(t(this.elem).scale()):r.apply(this,arguments)},t.fx.step.rotate=function(e){var r=a(t(e.elem));t(e.elem).rotate(e.now+r.rotateUnits)},t.fx.step.scale=function(a){t(a.elem).scale(a.now)};var o=t.fn.animate;t.fn.animate=function(e){if(void 0!==e.rotate){var r=e.rotate.toString().match(/^(([+-]=)?(-?\d+(\.\d+)?))(.+)?$/);r&&r[5]&&(a(t(this)).rotateUnits=r[5]),e.rotate=r[1]}return o.apply(this,arguments)}}(jQuery);