!function(t){"function"==typeof define&&define.amd?define(["jquery"],t):"object"==typeof module&&module.exports?module.exports=t(require("jquery")):t(jQuery)}(function(o){function a(t){var e,i=null;return t.getWidthFrom?(e=t.stickyElement.innerWidth()-t.stickyElement.width(),i=o(t.getWidthFrom).width()-e||null):t.widthFromWrapper&&(i=t.stickyWrapper.width()),i=null==i?t.stickyElement.width():i}function t(){for(var t=p.scrollTop(),e=d.height()-h,i=e<t?e-t:0,s=0,n=l.length;s<n;s++){var r=l[s],o=r.stickyWrapper.offset().top-r.topSpacing-i;r.stickyWrapper.css("height",r.stickyElement.outerHeight()),"bottom"===r.position?t+h-r.stickyElement.height()>o?(r.stickyElement.css({width:"",position:"",bottom:"","z-index":""}),r.className&&r.stickyElement.parent().hasClass(r.className)&&r.stickyElement.parent().removeClass(r.className)):(r.stickyElement.css("width",a(r)).css("position","fixed").css("bottom",r.bottomSpacing).css("z-index",r.zIndex),r.className&&!r.stickyElement.parent().hasClass(r.className)&&r.stickyElement.parent().addClass(r.className)):"top"===r.position&&(t<=o?(r.stickyElement.css({width:"",position:"",top:"","z-index":""}),r.stickyElement.parent().removeClass(r.className)):(r.stickyElement.css("width",a(r)).css("position","fixed").css("top",r.topSpacing).css("z-index",r.zIndex),r.className&&!r.stickyElement.parent().hasClass(r.className)&&r.stickyElement.parent().addClass(r.className)))}}function e(){h=p.height();for(var t=0,e=l.length;t<e;t++){var i=l[t],s=null;i.getWidthFrom?i.responsiveWidth&&(s=o(i.getWidthFrom).width()):i.widthFromWrapper&&(s=i.stickyWrapper.width()),null!=s&&i.stickyElement.css("width",s)}}var i=Array.prototype.slice,s=Array.prototype.splice,c={topSpacing:0,bottomSpacing:0,position:"top",className:"is-sticky",wrapperClassName:"sticky-wrapper",center:!1,getWidthFrom:"",widthFromWrapper:!0,responsiveWidth:!1,zIndex:"inherit"},p=o(window),d=o(document),l=[],h=p.height(),u={init:function(r){return this.each(function(){var t=o.extend({},c,r),e=o(this),i=e.attr("id"),s=i?i+"-"+c.wrapperClassName:c.wrapperClassName,n=o("<div></div>").attr("id",s).addClass(t.wrapperClassName);e.wrapAll(function(){if(0==o(this).parent("#"+s).length)return n});i=e.parent();t.center&&i.css({width:e.outerWidth(),marginLeft:"auto",marginRight:"auto"}),"right"===e.css("float")&&e.css({float:"none"}).parent().css({float:"right"}),t.stickyElement=e,t.stickyWrapper=i,t.currentTop=null,t.currentBottom=null,l.push(t),u.setWrapperHeight(this),u.setupChangeListeners(this)})},setWrapperHeight:function(t){var e=o(t),t=e.parent();t&&t.css("height",e.outerHeight())},setupChangeListeners:function(e){window.MutationObserver?new window.MutationObserver(function(t){(t[0].addedNodes.length||t[0].removedNodes.length)&&u.setWrapperHeight(e)}).observe(e,{subtree:!0,childList:!0}):window.addEventListener?(e.addEventListener("DOMNodeInserted",function(){u.setWrapperHeight(e)},!1),e.addEventListener("DOMNodeRemoved",function(){u.setWrapperHeight(e)},!1)):window.attachEvent&&(e.attachEvent("onDOMNodeInserted",function(){u.setWrapperHeight(e)}),e.attachEvent("onDOMNodeRemoved",function(){u.setWrapperHeight(e)}))},update:t,unstick:function(t){return this.each(function(){for(var t=o(this),e=-1,i=l.length;0<i--;)l[i].stickyElement.get(0)===this&&(s.call(l,i,1),e=i);-1!==e&&(t.unwrap(),t.css({width:"",position:"",top:"",bottom:"",float:"","z-index":""}))})}};window.addEventListener?(window.addEventListener("scroll",t,!1),window.addEventListener("resize",e,!1)):window.attachEvent&&(window.attachEvent("onscroll",t),window.attachEvent("onresize",e)),o.fn.sticky=function(t){return u[t]?u[t].apply(this,i.call(arguments,1)):"object"!=typeof t&&t?void o.error("Method "+t+" does not exist on jQuery.sticky"):u.init.apply(this,arguments)},o.fn.unstick=function(t){return u[t]?u[t].apply(this,i.call(arguments,1)):"object"!=typeof t&&t?void o.error("Method "+t+" does not exist on jQuery.sticky"):u.unstick.apply(this,arguments)},o(function(){setTimeout(t,0)})});