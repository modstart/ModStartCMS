!function(t){"object"==typeof exports&&"undefined"!=typeof module?module.exports=t():"function"==typeof define&&define.amd?define([],t):("undefined"!=typeof window?window:"undefined"!=typeof global?global:"undefined"!=typeof self?self:this).Clipboard=t()}(function(){return function o(i,r,a){function c(n,t){if(!r[n]){if(!i[n]){var e="function"==typeof require&&require;if(!t&&e)return e(n,!0);if(l)return l(n,!0);t=new Error("Cannot find module '"+n+"'");throw t.code="MODULE_NOT_FOUND",t}e=r[n]={exports:{}};i[n][0].call(e.exports,function(t){var e=i[n][1][t];return c(e||t)},e,e.exports,o,i,r,a)}return r[n].exports}for(var l="function"==typeof require&&require,t=0;t<a.length;t++)c(a[t]);return c}({1:[function(t,e,n){var o;Element&&!Element.prototype.matches&&((o=Element.prototype).matches=o.matchesSelector||o.mozMatchesSelector||o.msMatchesSelector||o.oMatchesSelector||o.webkitMatchesSelector),e.exports=function(t,e){for(;t&&t!==document;){if(t.matches(e))return t;t=t.parentNode}}},{}],2:[function(t,e,n){var a=t("./closest");e.exports=function(t,e,n,o,i){var r=function(e,n,t,o){return function(t){t.delegateTarget=a(t.target,n),t.delegateTarget&&o.call(e,t)}}.apply(this,arguments);return t.addEventListener(n,r,i),{destroy:function(){t.removeEventListener(n,r,i)}}}},{"./closest":1}],3:[function(t,e,n){n.node=function(t){return void 0!==t&&t instanceof HTMLElement&&1===t.nodeType},n.nodeList=function(t){var e=Object.prototype.toString.call(t);return void 0!==t&&("[object NodeList]"===e||"[object HTMLCollection]"===e)&&"length"in t&&(0===t.length||n.node(t[0]))},n.string=function(t){return"string"==typeof t||t instanceof String},n.fn=function(t){return"[object Function]"===Object.prototype.toString.call(t)}},{}],4:[function(t,e,n){var s=t("./is"),u=t("delegate");e.exports=function(t,e,n){if(!t&&!e&&!n)throw new Error("Missing required arguments");if(!s.string(e))throw new TypeError("Second argument must be a String");if(!s.fn(n))throw new TypeError("Third argument must be a Function");if(s.node(t))return c=e,l=n,(a=t).addEventListener(c,l),{destroy:function(){a.removeEventListener(c,l)}};if(s.nodeList(t))return o=t,i=e,r=n,Array.prototype.forEach.call(o,function(t){t.addEventListener(i,r)}),{destroy:function(){Array.prototype.forEach.call(o,function(t){t.removeEventListener(i,r)})}};if(s.string(t))return u(document.body,t,e,n);throw new TypeError("First argument must be a String, HTMLElement, HTMLCollection, or NodeList");var o,i,r,a,c,l}},{"./is":3,delegate:2}],5:[function(t,e,n){e.exports=function(t){var e,n;return t="SELECT"===t.nodeName?(t.focus(),t.value):"INPUT"===t.nodeName||"TEXTAREA"===t.nodeName?(t.focus(),t.setSelectionRange(0,t.value.length),t.value):(t.hasAttribute("contenteditable")&&t.focus(),e=window.getSelection(),(n=document.createRange()).selectNodeContents(t),e.removeAllRanges(),e.addRange(n),e.toString())}},{}],6:[function(t,e,n){function o(){}o.prototype={on:function(t,e,n){var o=this.e||(this.e={});return(o[t]||(o[t]=[])).push({fn:e,ctx:n}),this},once:function(t,e,n){var o=this;function i(){o.off(t,i),e.apply(n,arguments)}return i._=e,this.on(t,i,n)},emit:function(t){for(var e=[].slice.call(arguments,1),n=((this.e||(this.e={}))[t]||[]).slice(),o=0,i=n.length;o<i;o++)n[o].fn.apply(n[o].ctx,e);return this},off:function(t,e){var n=this.e||(this.e={}),o=n[t],i=[];if(o&&e)for(var r=0,a=o.length;r<a;r++)o[r].fn!==e&&o[r].fn._!==e&&i.push(o[r]);return i.length?n[t]=i:delete n[t],this}},e.exports=o},{}],7:[function(t,e,n){var o,i;o=this,i=function(t,e){"use strict";var n=(e=e)&&e.__esModule?e:{default:e};var o="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(t){return typeof t}:function(t){return t&&"function"==typeof Symbol&&t.constructor===Symbol&&t!==Symbol.prototype?"symbol":typeof t};function i(t,e){for(var n=0;n<e.length;n++){var o=e[n];o.enumerable=o.enumerable||!1,o.configurable=!0,"value"in o&&(o.writable=!0),Object.defineProperty(t,o.key,o)}}(function(t,e,n){e&&i(t.prototype,e),n&&i(t,n)})(r,[{key:"resolveOptions",value:function(){var t=0<arguments.length&&void 0!==arguments[0]?arguments[0]:{};this.action=t.action,this.emitter=t.emitter,this.target=t.target,this.text=t.text,this.trigger=t.trigger,this.selectedText=""}},{key:"initSelection",value:function(){this.text?this.selectFake():this.target&&this.selectTarget()}},{key:"selectFake",value:function(){var t=this,e="rtl"==document.documentElement.getAttribute("dir"),e=(this.removeFake(),this.fakeHandlerCallback=function(){return t.removeFake()},this.fakeHandler=document.body.addEventListener("click",this.fakeHandlerCallback)||!0,this.fakeElem=document.createElement("textarea"),this.fakeElem.style.fontSize="12pt",this.fakeElem.style.border="0",this.fakeElem.style.padding="0",this.fakeElem.style.margin="0",this.fakeElem.style.position="absolute",this.fakeElem.style[e?"right":"left"]="-9999px",window.pageYOffset||document.documentElement.scrollTop);this.fakeElem.addEventListener("focus",window.scrollTo(0,e)),this.fakeElem.style.top=e+"px",this.fakeElem.setAttribute("readonly",""),this.fakeElem.value=this.text,document.body.appendChild(this.fakeElem),this.selectedText=(0,n.default)(this.fakeElem),this.copyText()}},{key:"removeFake",value:function(){this.fakeHandler&&(document.body.removeEventListener("click",this.fakeHandlerCallback),this.fakeHandler=null,this.fakeHandlerCallback=null),this.fakeElem&&(document.body.removeChild(this.fakeElem),this.fakeElem=null)}},{key:"selectTarget",value:function(){this.selectedText=(0,n.default)(this.target),this.copyText()}},{key:"copyText",value:function(){var e=void 0;try{e=document.execCommand(this.action)}catch(t){e=!1}this.handleResult(e)}},{key:"handleResult",value:function(t){this.emitter.emit(t?"success":"error",{action:this.action,text:this.selectedText,trigger:this.trigger,clearSelection:this.clearSelection.bind(this)})}},{key:"clearSelection",value:function(){this.target&&this.target.blur(),window.getSelection().removeAllRanges()}},{key:"destroy",value:function(){this.removeFake()}},{key:"action",set:function(){if(this._action=0<arguments.length&&void 0!==arguments[0]?arguments[0]:"copy","copy"!==this._action&&"cut"!==this._action)throw new Error('Invalid "action" value, use either "copy" or "cut"')},get:function(){return this._action}},{key:"target",set:function(t){if(void 0!==t){if(!t||"object"!==(void 0===t?"undefined":o(t))||1!==t.nodeType)throw new Error('Invalid "target" value, use a valid Element');if("copy"===this.action&&t.hasAttribute("disabled"))throw new Error('Invalid "target" attribute. Please use "readonly" instead of "disabled" attribute');if("cut"===this.action&&(t.hasAttribute("readonly")||t.hasAttribute("disabled")))throw new Error('Invalid "target" attribute. You can\'t cut text from elements with "readonly" or "disabled" attributes');this._target=t}},get:function(){return this._target}}]);e=r;function r(t){if(!(this instanceof r))throw new TypeError("Cannot call a class as a function");this.resolveOptions(t),this.initSelection()}t.exports=e},void 0!==n?i(e,t("select")):(i(i={exports:{}},o.select),o.clipboardAction=i.exports)},{select:5}],8:[function(t,e,n){var o,i;o=this,i=function(t,e,n,o){"use strict";var i=a(e),e=a(n),r=a(o);function a(t){return t&&t.__esModule?t:{default:t}}var c=function(t,e,n){return e&&l(t.prototype,e),n&&l(t,n),t};function l(t,e){for(var n=0;n<e.length;n++){var o=e[n];o.enumerable=o.enumerable||!1,o.configurable=!0,"value"in o&&(o.writable=!0),Object.defineProperty(t,o.key,o)}}n=function(t){var e=o;if("function"!=typeof t&&null!==t)throw new TypeError("Super expression must either be null or a function, not "+typeof t);function o(t,e){var n;if(this instanceof o)return(n=function(t,e){if(t)return!e||"object"!=typeof e&&"function"!=typeof e?t:e;throw new ReferenceError("this hasn't been initialised - super() hasn't been called")}(this,(o.__proto__||Object.getPrototypeOf(o)).call(this))).resolveOptions(e),n.listenClick(t),n;throw new TypeError("Cannot call a class as a function")}return e.prototype=Object.create(t&&t.prototype,{constructor:{value:e,enumerable:!1,writable:!0,configurable:!0}}),t&&(Object.setPrototypeOf?Object.setPrototypeOf(e,t):e.__proto__=t),c(o,[{key:"resolveOptions",value:function(){var t=0<arguments.length&&void 0!==arguments[0]?arguments[0]:{};this.action="function"==typeof t.action?t.action:this.defaultAction,this.target="function"==typeof t.target?t.target:this.defaultTarget,this.text="function"==typeof t.text?t.text:this.defaultText}},{key:"listenClick",value:function(t){var e=this;this.listener=(0,r.default)(t,"click",function(t){return e.onClick(t)})}},{key:"onClick",value:function(t){t=t.delegateTarget||t.currentTarget;this.clipboardAction&&(this.clipboardAction=null),this.clipboardAction=new i.default({action:this.action(t),target:this.target(t),text:this.text(t),trigger:t,emitter:this})}},{key:"defaultAction",value:function(t){return s("action",t)}},{key:"defaultTarget",value:function(t){t=s("target",t);if(t)return document.querySelector(t)}},{key:"defaultText",value:function(t){return s("text",t)}},{key:"destroy",value:function(){this.listener.destroy(),this.clipboardAction&&(this.clipboardAction.destroy(),this.clipboardAction=null)}}]),o}(e.default);function s(t,e){t="data-clipboard-"+t;if(e.hasAttribute(t))return e.getAttribute(t)}t.exports=n},void 0!==n?i(e,t("./clipboard-action"),t("tiny-emitter"),t("good-listener")):(i(i={exports:{}},o.clipboardAction,o.tinyEmitter,o.goodListener),o.clipboard=i.exports)},{"./clipboard-action":7,"good-listener":4,"tiny-emitter":6}]},{},[8])(8)});