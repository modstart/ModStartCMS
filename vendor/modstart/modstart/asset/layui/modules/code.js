layui.define("jquery",function(e){"use strict";var t=layui.$;e("code",function(i){var e=[];(i=i||{}).elem=t(i.elem||".layui-code"),i.lang="lang"in i?i.lang:"code",i.elem.each(function(){e.push(this)}),layui.each(e.reverse(),function(e,a){var l=t(a),a=l.html();(l.attr("lay-encode")||i.encode)&&(a=a.replace(/&(?!#?[a-zA-Z0-9]+;)/g,"&amp;").replace(/</g,"&lt;").replace(/>/g,"&gt;").replace(/'/g,"&#39;").replace(/"/g,"&quot;")),l.html('<ol class="layui-code-ol"><li>'+a.replace(/[\r\t\n]+/g,"</li><li>")+"</li></ol>"),l.find(">.layui-code-h3")[0]||l.prepend('<h3 class="layui-code-h3">'+(l.attr("lay-title")||i.title||"&lt;/&gt;")+'<a href="javascript:;">'+(l.attr("lay-lang")||i.lang||"")+"</a></h3>");a=l.find(">.layui-code-ol");l.addClass("layui-box layui-code-view"),(l.attr("lay-skin")||i.skin)&&l.addClass("layui-code-"+(l.attr("lay-skin")||i.skin)),0<(a.find("li").length/100|0)&&a.css("margin-left",(a.find("li").length/100|0)+"px"),(l.attr("lay-height")||i.height)&&a.css("max-height",l.attr("lay-height")||i.height)})})}).addcss("modules/code.css?v=2","skincodecss");