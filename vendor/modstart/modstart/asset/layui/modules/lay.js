!function(f){"use strict";function l(t){return new r(t)}var t,h=f.document,r=function(t){for(var e=0,n="object"==typeof t?[t]:(this.selector=t,h.querySelectorAll(t||null));e<n.length;e++)this.push(n[e])};r.prototype=[],r.prototype.constructor=r,l.extend=function(){var t=1,e=arguments,o=function(t,e){for(var n in t=t||("array"===layui._typeof(e)?[]:{}),e)t[n]=e[n]&&e[n].constructor===Object?o(t[n],e[n]):e[n];return t};for(e[0]="object"==typeof e[0]?e[0]:{};t<e.length;t++)"object"==typeof e[t]&&o(e[0],e[t]);return e[0]},l.v="1.0.8",l.ie=(t=navigator.userAgent.toLowerCase(),!!(f.ActiveXObject||"ActiveXObject"in f)&&((t.match(/msie\s(\d+)/)||[])[1]||"11")),l.layui=layui||{},l.getPath=layui.cache.dir,l.stope=layui.stope,l.each=function(){return layui.each.apply(layui,arguments),this},l.digit=function(t,e,n){var o="";e=e||2;for(var r=(t=String(t)).length;r<e;r++)o+="0";return t<Math.pow(10,e)?o+(0|t):t},l.elem=function(t,e){var n=h.createElement(t);return l.each(e||{},function(t,e){n.setAttribute(t,e)}),n},l.hasScrollbar=function(){return h.body.scrollHeight>(f.innerHeight||h.documentElement.clientHeight)},l.position=function(t,e,n){var o,r,i,c,u,a,s;e&&(n=n||{},t!==h&&t!==l("body")[0]||(n.clickType="right"),o="right"===n.clickType?{left:(u=n.e||f.event||{}).clientX,top:u.clientY,right:u.clientX,bottom:u.clientY}:t.getBoundingClientRect(),r=e.offsetWidth,a=e.offsetHeight,s=function(t){return h.body[t=t?"scrollLeft":"scrollTop"]|h.documentElement[t]},i=o.left,c=o.bottom,"center"===n.align?i-=(r-t.offsetWidth)/2:"right"===n.align&&(i=i-r+t.offsetWidth),(i=i+r+5>(u=function(t){return h.documentElement[t?"clientWidth":"clientHeight"]})("width")?u("width")-r-5:i)<5&&(i=5),c+a+5>u()&&(o.top>a+5?c=o.top-a-10:"right"===n.clickType&&(c=u()-a-10)<0&&(c=0)),(a=n.position)&&(e.style.position=a),e.style.left=i+("fixed"===a?0:s(1))+"px",e.style.top=c+("fixed"===a?0:s())+"px",l.hasScrollbar()||(s=e.getBoundingClientRect(),!n.SYSTEM_RELOAD&&s.bottom+5>u()&&(n.SYSTEM_RELOAD=!0,setTimeout(function(){l.position(t,e,n)},50))))},l.options=function(t,e){t=l(t),e=e||"lay-options";try{return new Function("return "+(t.attr(e)||"{}"))()}catch(t){return hint.error("parseerror："+t,"error"),{}}},l.isTopElem=function(n){var t=[h,l("body")[0]],o=!1;return l.each(t,function(t,e){if(e===n)return o=!0}),o},r.addStr=function(n,t){return n=n.replace(/\s+/," "),t=t.replace(/\s+/," ").split(" "),l.each(t,function(t,e){new RegExp("\\b"+e+"\\b").test(n)||(n=n+" "+e)}),n.replace(/^\s|\s$/,"")},r.removeStr=function(n,t){return n=n.replace(/\s+/," "),t=t.replace(/\s+/," ").split(" "),l.each(t,function(t,e){e=new RegExp("\\b"+e+"\\b");e.test(n)&&(n=n.replace(e,""))}),n.replace(/\s+/," ").replace(/^\s|\s$/,"")},r.prototype.find=function(o){var r=this,i=0,c=[],u="object"==typeof o;return this.each(function(t,e){for(var n=u?e.contains(o):e.querySelectorAll(o||null);i<n.length;i++)c.push(n[i]);r.shift()}),u||(r.selector=(r.selector?r.selector+" ":"")+o),l.each(c,function(t,e){r.push(e)}),r},r.prototype.each=function(t){return l.each.call(this,this,t)},r.prototype.addClass=function(n,o){return this.each(function(t,e){e.className=r[o?"removeStr":"addStr"](e.className,n)})},r.prototype.removeClass=function(t){return this.addClass(t,!0)},r.prototype.hasClass=function(n){var o=!1;return this.each(function(t,e){new RegExp("\\b"+n+"\\b").test(e.className)&&(o=!0)}),o},r.prototype.css=function(e,o){function r(t){return isNaN(t)?t:t+"px"}var t=this;return"string"==typeof e&&void 0===o?function(){if(0<t.length)return t[0].style[e]}():t.each(function(t,n){"object"==typeof e?l.each(e,function(t,e){n.style[t]=r(e)}):n.style[e]=r(o)})},r.prototype.width=function(n){var o=this;return void 0===n?function(){if(0<o.length)return o[0].offsetWidth}():o.each(function(t,e){o.css("width",n)})},r.prototype.height=function(n){var o=this;return void 0===n?function(){if(0<o.length)return o[0].offsetHeight}():o.each(function(t,e){o.css("height",n)})},r.prototype.attr=function(n,o){var t=this;return void 0===o?function(){if(0<t.length)return t[0].getAttribute(n)}():t.each(function(t,e){e.setAttribute(n,o)})},r.prototype.removeAttr=function(n){return this.each(function(t,e){e.removeAttribute(n)})},r.prototype.html=function(n){var t=this;return void 0===n?function(){if(0<t.length)return t[0].innerHTML}():this.each(function(t,e){e.innerHTML=n})},r.prototype.val=function(n){var t=this;return void 0===n?function(){if(0<t.length)return t[0].value}():this.each(function(t,e){e.value=n})},r.prototype.append=function(n){return this.each(function(t,e){"object"==typeof n?e.appendChild(n):e.innerHTML=e.innerHTML+n})},r.prototype.remove=function(n){return this.each(function(t,e){n?e.removeChild(n):e.parentNode.removeChild(e)})},r.prototype.on=function(n,o){return this.each(function(t,e){e.attachEvent?e.attachEvent("on"+n,function(t){t.target=t.srcElement,o.call(e,t)}):e.addEventListener(n,o,!1)})},r.prototype.off=function(n,o){return this.each(function(t,e){e.detachEvent?e.detachEvent("on"+n,o):e.removeEventListener(n,o,!1)})},f.lay=l,f.layui&&layui.define&&layui.define(function(t){t("lay",l)})}(window,window.document);