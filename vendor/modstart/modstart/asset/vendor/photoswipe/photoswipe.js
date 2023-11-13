!function(e,t){"function"==typeof define&&define.amd?define(t):"object"==typeof exports?module.exports=t():e.PhotoSwipe=t()}(this,function(){"use strict";return function(m,z,t,_){function e(){return{x:0,y:0}}function N(e,t){y.extend(x,t.publicMethods),Ge.push(e)}function U(e){var t=P();return t-1<e?e-t:e<0?t+e:e}function a(e,t){return Ke[e]||(Ke[e]=[]),Ke[e].push(t)}function H(e,t,n,i){i===x.currItem.initialZoomLevel?n[e]=x.currItem.initialPosition[e]:(n[e]=Qe(e,i),n[e]>t.min[e]?n[e]=t.min[e]:n[e]<t.max[e]&&(n[e]=t.max[e]))}function Y(e){var t="";g.escKey&&27===e.keyCode?t="close":g.arrowKeys&&(37===e.keyCode?t="prev":39===e.keyCode&&(t="next")),!t||e.ctrlKey||e.altKey||e.shiftKey||e.metaKey||(e.preventDefault?e.preventDefault():e.returnValue=!1,x[t]())}function W(e){e&&(Ae||Se||h||De)&&(e.preventDefault(),e.stopPropagation())}function B(){x.setScrollOffset(0,y.getScrollY())}function G(e){var t;"mousedown"===e.type&&0<e.button||(Qt?e.preventDefault():Te&&"mousedown"===e.type||(kt(e,!0)&&e.preventDefault(),C("pointerDown"),me&&((t=y.arraySearch(ht,e.pointerId,"id"))<0&&(t=ht.length),ht[t]={x:e.pageX,y:e.pageY,id:e.pointerId}),e=(t=Nt(e)).length,c=null,ct(),s&&1!==e||(s=Ze=!0,y.bind(window,Q,x),Ce=ze=Fe=De=Oe=Ae=Me=Se=!1,Pe=null,C("firstTouchStart",t),S(He,v),Ue.x=Ue.y=0,S(k,t[0]),S(ft,k),yt.x=b.x*Ye,xt=[{x:k.x,y:k.y}],be=we=D(),it(f,!0),Mt(),St()),!u&&1<e&&!h&&!Oe&&(te=f,u=Me=!(Se=!1),Ue.y=Ue.x=0,S(He,v),S(E,t[0]),S(pt,t[1]),Pt(E,pt,Ct),It.x=Math.abs(Ct.x)-v.x,It.y=Math.abs(Ct.y)-v.y,ke=Tt(E,pt))))}function X(e){var t;e.preventDefault(),me&&-1<(t=y.arraySearch(ht,e.pointerId,"id"))&&((t=ht[t]).x=e.pageX,t.y=e.pageY),s&&(t=Nt(e),Pe||Ae||u?c=t:R.x!==b.x*Ye?Pe="h":(e=Math.abs(t[0].x-k.x)-Math.abs(t[0].y-k.y),Math.abs(e)>=mt&&(Pe=0<e?"h":"v",c=t)))}function V(e){if(l.isOldAndroid){if(Te&&"mouseup"===e.type)return;-1<e.type.indexOf("touch")&&(clearTimeout(Te),Te=setTimeout(function(){Te=0},600))}C("pointerUp"),kt(e,!1)&&e.preventDefault(),me&&-1<(a=y.arraySearch(ht,e.pointerId,"id"))&&(t=ht.splice(a,1)[0],navigator.msPointerEnabled&&(t.type={4:"mouse",2:"touch",3:"pen"}[e.pointerType],t.type)||(t.type=e.pointerType||"mouse"));var t,n=(a=Nt(e)).length;if(2===(n="mouseup"===e.type?0:n))return!(c=null);1===n&&S(ft,a[0]),0!==n||Pe||h||(t||("mouseup"===e.type?t={x:e.pageX,y:e.pageY,type:"mouse"}:e.changedTouches&&e.changedTouches[0]&&(t={x:e.changedTouches[0].pageX,y:e.changedTouches[0].pageY,type:"touch"})),C("touchRelease",e,t));var i,o,a=-1;if(0===n&&(s=!1,y.unbind(window,Q,x),Mt(),u?a=0:-1!==bt&&(a=D()-bt)),bt=1===n?D():-1,e=-1!==a&&a<150?"zoom":"swipe",u&&n<2&&(u=!1,1===n&&(e="zoomPointerUp"),C("zoomGestureEnded")),c=null,Ae||Se||h||De)if(ct(),(Ie=Ie||Yt()).calculateSwipeSpeed("x"),De)Ft()<g.verticalDragRange?x.close():(i=v.y,o=Le,ut("verticalDrag",0,1,300,y.easing.cubic.out,function(e){v.y=(x.currItem.initialPosition.y-i)*e+i,T((1-o)*e+o),M()}),C("onVerticalDrag",1));else{if((Oe||h)&&0===n){if(Bt(e,Ie))return;e="zoomPointerUp"}h||("swipe"!==e?Xt():!Oe&&f>x.currItem.fitRatio&&Wt(Ie))}}var K,q,$,p,j,J,Q,ee,i,f,te,ne,ie,oe,ae,r,re,le,se,ce,ue,de,me,o,pe,fe,he,ye,xe,ge,l,ve,we,be,Ie,Ce,De,Te,s,Me,Se,Ae,Ee,Oe,c,u,ke,d,Re,h,Pe,Ze,Fe,Le,ze,_e,y={features:null,bind:function(e,t,n,i){var o=(i?"remove":"add")+"EventListener";t=t.split(" ");for(var a=0;a<t.length;a++)t[a]&&e[o](t[a],n,!1)},isArray:function(e){return e instanceof Array},createEl:function(e,t){t=document.createElement(t||"div");return e&&(t.className=e),t},getScrollY:function(){var e=window.pageYOffset;return void 0!==e?e:document.documentElement.scrollTop},unbind:function(e,t,n){y.bind(e,t,n,!0)},removeClass:function(e,t){t=new RegExp("(\\s|^)"+t+"(\\s|$)");e.className=e.className.replace(t," ").replace(/^\s\s*/,"").replace(/\s\s*$/,"")},addClass:function(e,t){y.hasClass(e,t)||(e.className+=(e.className?" ":"")+t)},hasClass:function(e,t){return e.className&&new RegExp("(^|\\s)"+t+"(\\s|$)").test(e.className)},getChildByClass:function(e,t){for(var n=e.firstChild;n;){if(y.hasClass(n,t))return n;n=n.nextSibling}},arraySearch:function(e,t,n){for(var i=e.length;i--;)if(e[i][n]===t)return i;return-1},extend:function(e,t,n){for(var i in t)!t.hasOwnProperty(i)||n&&e.hasOwnProperty(i)||(e[i]=t[i])},easing:{sine:{out:function(e){return Math.sin(e*(Math.PI/2))},inOut:function(e){return-(Math.cos(Math.PI*e)-1)/2}},cubic:{out:function(e){return--e*e*e+1}}},detectFeatures:function(){if(y.features)return y.features;for(var e,t,n,i,o,a=y.createEl().style,r="",l={},s=(l.oldIE=document.all&&!document.addEventListener,l.touch="ontouchstart"in window,window.requestAnimationFrame&&(l.raf=window.requestAnimationFrame,l.caf=window.cancelAnimationFrame),l.pointerEvent=!!window.PointerEvent||navigator.msPointerEnabled,l.pointerEvent||(e=navigator.userAgent,/iP(hone|od)/.test(navigator.platform)&&(t=navigator.appVersion.match(/OS (\d+)_(\d+)_?(\d+)?/))&&0<t.length&&1<=(t=parseInt(t[1],10))&&t<8&&(l.isOldIOSPhone=!0),t=(t=e.match(/Android\s([0-9\.]*)/))?t[1]:0,1<=(t=parseFloat(t))&&(t<4.4&&(l.isOldAndroid=!0),l.androidVersion=t),l.isMobileOpera=/opera mini|opera mobi/i.test(e)),["transform","perspective","animationName"]),c=["","webkit","Moz","ms","O"],u=0;u<4;u++){for(var r=c[u],d=0;d<3;d++)n=s[d],i=r+(r?n.charAt(0).toUpperCase()+n.slice(1):n),!l[n]&&i in a&&(l[n]=i);r&&!l.raf&&(r=r.toLowerCase(),l.raf=window[r+"RequestAnimationFrame"],l.raf)&&(l.caf=window[r+"CancelAnimationFrame"]||window[r+"CancelRequestAnimationFrame"])}return l.raf||(o=0,l.raf=function(e){var t=(new Date).getTime(),n=Math.max(0,16-(t-o)),i=window.setTimeout(function(){e(t+n)},n);return o=t+n,i},l.caf=function(e){clearTimeout(e)}),l.svg=!!document.createElementNS&&!!document.createElementNS("http://www.w3.org/2000/svg","svg").createSVGRect,y.features=l}},x=(y.detectFeatures(),y.features.oldIE&&(y.bind=function(e,t,n,i){t=t.split(" ");for(var o,a=(i?"detach":"attach")+"Event",r=function(){n.handleEvent.call(n)},l=0;l<t.length;l++)if(o=t[l])if("object"==typeof n&&n.handleEvent){if(i){if(!n["oldIE"+o])return!1}else n["oldIE"+o]=r;e[a]("on"+o,n["oldIE"+o])}else e[a]("on"+o,n)}),this),Ne=25,g={allowPanToNext:!0,spacing:.12,bgOpacity:1,mouseUsed:!1,loop:!0,pinchToClose:!0,closeOnScroll:!0,closeOnVerticalDrag:!0,verticalDragRange:.75,hideAnimationDuration:333,showAnimationDuration:333,showHideOpacity:!1,focus:!0,escKey:!0,arrowKeys:!0,mainScrollEndFriction:.35,panEndFriction:.35,isClickableElement:function(e){return"A"===e.tagName},getDoubleTapZoom:function(e,t){return e||t.initialZoomLevel<.7?1:1.33},maxSpreadZoom:1.33,modal:!0,scaleMode:"fit"},Ue=(y.extend(g,_),e()),He=e(),v=e(),w={},Ye=0,We={},b=e(),I=0,Be=!0,Ge=[],Xe={},Ve=!1,Ke={},C=function(e){var t=Ke[e];if(t){var n=Array.prototype.slice.call(arguments);n.shift();for(var i=0;i<t.length;i++)t[i].apply(x,n)}},D=function(){return(new Date).getTime()},T=function(e){Le=e,x.bg.style.opacity=e*g.bgOpacity},qe=function(e,t,n,i,o){(!Ve||o&&o!==x.currItem)&&(i/=(o||x.currItem).fitRatio),e[de]=ne+t+"px, "+n+"px"+ie+" scale("+i+")"},M=function(e){Re&&(e&&(f>x.currItem.fitRatio?Ve||(cn(x.currItem,!1,!0),Ve=!0):Ve&&(cn(x.currItem),Ve=!1)),qe(Re,v.x,v.y,f))},$e=function(e){e.container&&qe(e.container.style,e.initialPosition.x,e.initialPosition.y,e.initialZoomLevel,e)},je=function(e,t){t[de]=ne+e+"px, 0px"+ie},Je=function(e,t){var n;!g.loop&&t&&(t=p+(b.x*Ye-e)/b.x,n=Math.round(e-R.x),t<0&&0<n||t>=P()-1&&n<0)&&(e=R.x+n*g.mainScrollEndFriction),R.x=e,je(e,j)},Qe=function(e,t){var n=It[e]-We[e];return He[e]+Ue[e]+n-t/te*n},S=function(e,t){e.x=t.x,e.y=t.y,t.id&&(e.id=t.id)},et=function(e){e.x=Math.round(e.x),e.y=Math.round(e.y)},tt=null,nt=function(){tt&&(y.unbind(document,"mousemove",nt),y.addClass(m,"pswp--has_mouse"),g.mouseUsed=!0,C("mouseUsed")),tt=setTimeout(function(){tt=null},100)},it=function(e,t){e=ln(x.currItem,w,e);return t&&(d=e),e},ot=function(e){return(e=e||x.currItem).initialZoomLevel},at=function(e){return 0<(e=e||x.currItem).w?g.maxSpreadZoom:1},A={},rt=0,lt=function(e){A[e]&&(A[e].raf&&fe(A[e].raf),rt--,delete A[e])},st=function(e){A[e]&&lt(e),A[e]||(rt++,A[e]={})},ct=function(){for(var e in A)A.hasOwnProperty(e)&&lt(e)},ut=function(e,t,n,i,o,a,r){function l(){A[e]&&(s=D()-c,i<=s?(lt(e),a(n),r&&r()):(a((n-t)*o(s/i)+t),A[e].raf=pe(l)))}var s,c=D();st(e);l()},_={shout:C,listen:a,viewportSize:w,options:g,isMainScrollAnimating:function(){return h},getZoomLevel:function(){return f},getCurrentIndex:function(){return p},isDragging:function(){return s},isZooming:function(){return u},setScrollOffset:function(e,t){We.x=e,ge=We.y=t,C("updateScrollOffset",We)},applyZoomPan:function(e,t,n,i){v.x=t,v.y=n,f=e,M(i)},init:function(){if(!K&&!q){x.framework=y,x.template=m,x.bg=y.getChildByClass(m,"pswp__bg"),he=m.className,K=!0,l=y.detectFeatures(),pe=l.raf,fe=l.caf,de=l.transform,xe=l.oldIE,x.scrollWrap=y.getChildByClass(m,"pswp__scroll-wrap"),x.container=y.getChildByClass(x.scrollWrap,"pswp__container"),j=x.container.style,x.itemHolders=r=[{el:x.container.children[0],wrap:0,index:-1},{el:x.container.children[1],wrap:0,index:-1},{el:x.container.children[2],wrap:0,index:-1}],r[0].el.style.display=r[2].el.style.display="none",de?(t=l.perspective&&!o,ne="translate"+(t?"3d(":"("),ie=l.perspective?", 0px)":")"):(de="left",y.addClass(m,"pswp--ie"),je=function(e,t){t.left=e+"px"},$e=function(e){var t=1<e.fitRatio?1:e.fitRatio,n=e.container.style,i=t*e.w,t=t*e.h;n.width=i+"px",n.height=t+"px",n.left=e.initialPosition.x+"px",n.top=e.initialPosition.y+"px"},M=function(){var e,t,n,i;Re&&(e=Re,n=(i=1<(t=x.currItem).fitRatio?1:t.fitRatio)*t.w,i=i*t.h,e.width=n+"px",e.height=i+"px",e.left=v.x+"px",e.top=v.y+"px")}),i={resize:x.updateSize,orientationchange:function(){clearTimeout(ve),ve=setTimeout(function(){w.x!==x.scrollWrap.clientWidth&&x.updateSize()},500)},scroll:B,keydown:Y,click:W};var e,t=l.isOldIOSPhone||l.isOldAndroid||l.isMobileOpera;for(l.animationName&&l.transform&&!t||(g.showAnimationDuration=g.hideAnimationDuration=0),e=0;e<Ge.length;e++)x["init"+Ge[e]]();z&&(x.ui=new z(x,y)).init(),C("firstUpdate"),p=p||g.index||0,(isNaN(p)||p<0||p>=P())&&(p=0),x.currItem=en(p),(l.isOldIOSPhone||l.isOldAndroid)&&(Be=!1),m.setAttribute("aria-hidden","false"),g.modal&&(Be?m.style.position="fixed":(m.style.position="absolute",m.style.top=y.getScrollY()+"px")),void 0===ge&&(C("initialLayout"),ge=ye=y.getScrollY());var n="pswp--open ";for(g.mainClass&&(n+=g.mainClass+" "),g.showHideOpacity&&(n+="pswp--animate_opacity "),n=(n=(n+=o?"pswp--touch":"pswp--notouch")+(l.animationName?" pswp--css_animation":""))+(l.svg?" pswp--svg":""),y.addClass(m,n),x.updateSize(),J=-1,I=null,e=0;e<3;e++)je((e+J)*b.x,r[e].el.style);xe||y.bind(x.scrollWrap,ee,x),a("initialZoomInEnd",function(){x.setContent(r[0],p-1),x.setContent(r[2],p+1),r[0].el.style.display=r[2].el.style.display="block",g.focus&&m.focus(),y.bind(document,"keydown",x),l.transform&&y.bind(x.scrollWrap,"click",x),g.mouseUsed||y.bind(document,"mousemove",nt),y.bind(window,"resize scroll orientationchange",x),C("bindEvents")}),x.setContent(r[1],p),x.updateCurrItem(),C("afterInit"),Be||(oe=setInterval(function(){rt||s||u||f!==x.currItem.initialZoomLevel||x.updateSize()},1e3)),y.addClass(m,"pswp--visible")}},close:function(){K&&(q=!(K=!1),C("close"),y.unbind(window,"resize scroll orientationchange",x),y.unbind(window,"scroll",i.scroll),y.unbind(document,"keydown",x),y.unbind(document,"mousemove",nt),l.transform&&y.unbind(x.scrollWrap,"click",x),s&&y.unbind(window,Q,x),clearTimeout(ve),C("unbindEvents"),tn(x.currItem,null,!0,x.destroy))},destroy:function(){C("destroy"),$t&&clearTimeout($t),m.setAttribute("aria-hidden","true"),m.className=he,oe&&clearInterval(oe),y.unbind(x.scrollWrap,ee,x),y.unbind(window,"scroll",x),Mt(),ct(),Ke=null},panTo:function(e,t,n){n||(e>d.min.x?e=d.min.x:e<d.max.x&&(e=d.max.x),t>d.min.y?t=d.min.y:t<d.max.y&&(t=d.max.y)),v.x=e,v.y=t,M()},handleEvent:function(e){e=e||window.event,i[e.type]&&i[e.type](e)},goTo:function(e){var t=(e=U(e))-p;I=t,p=e,x.currItem=en(p),Ye-=t,Je(b.x*Ye),ct(),h=!1,x.updateCurrItem()},next:function(){x.goTo(p+1)},prev:function(){x.goTo(p-1)},updateCurrZoomItem:function(e){var t;e&&C("beforeChange",0),Re=r[1].el.children.length&&(t=r[1].el.children[0],y.hasClass(t,"pswp__zoom-wrap"))?t.style:null,d=x.currItem.bounds,te=f=x.currItem.initialZoomLevel,v.x=d.center.x,v.y=d.center.y,e&&C("afterChange")},invalidateCurrItems:function(){ae=!0;for(var e=0;e<3;e++)r[e].item&&(r[e].item.needsUpdate=!0)},updateCurrItem:function(e){if(0!==I){var t,n=Math.abs(I);if(!(e&&n<2)){x.currItem=en(p),Ve=!1,C("beforeChange",I),3<=n&&(J+=I+(0<I?-3:3),n=3);for(var i=0;i<n;i++)0<I?(t=r.shift(),r[2]=t,je((++J+2)*b.x,t.el.style),x.setContent(t,p-n+i+1+1)):(t=r.pop(),r.unshift(t),je(--J*b.x,t.el.style),x.setContent(t,p+n-i-1-1));Re&&1===Math.abs(I)&&(e=en(re)).initialZoomLevel!==f&&(ln(e,w),cn(e),$e(e)),I=0,x.updateCurrZoomItem(),re=p,C("afterChange")}}},updateSize:function(e){if(!Be&&g.modal){var t=y.getScrollY();if(ge!==t&&(m.style.top=t+"px",ge=t),!e&&Xe.x===window.innerWidth&&Xe.y===window.innerHeight)return;Xe.x=window.innerWidth,Xe.y=window.innerHeight,m.style.height=Xe.y+"px"}if(w.x=x.scrollWrap.clientWidth,w.y=x.scrollWrap.clientHeight,B(),b.x=w.x+Math.round(w.x*g.spacing),b.y=w.y,Je(b.x*Ye),C("beforeResize"),void 0!==J){for(var n,i,o,a=0;a<3;a++)n=r[a],je((a+J)*b.x,n.el.style),o=p+a-1,g.loop&&2<P()&&(o=U(o)),(i=en(o))&&(ae||i.needsUpdate||!i.bounds)?(x.cleanSlide(i),x.setContent(n,o),1===a&&(x.currItem=i,x.updateCurrZoomItem(!0)),i.needsUpdate=!1):-1===n.index&&0<=o&&x.setContent(n,o),i&&i.container&&(ln(i,w),cn(i),$e(i));ae=!1}te=f=x.currItem.initialZoomLevel,(d=x.currItem.bounds)&&(v.x=d.center.x,v.y=d.center.y,M(!0)),C("resize")},zoomTo:function(t,e,n,i,o){e&&(te=f,It.x=Math.abs(e.x)-v.x,It.y=Math.abs(e.y)-v.y,S(He,v));function a(e){1===e?(f=t,v.x=r.x,v.y=r.y):(f=(t-l)*e+l,v.x=(r.x-s.x)*e+s.x,v.y=(r.y-s.y)*e+s.y),o&&o(e),M(1===e)}var e=it(t,!1),r={},l=(H("x",e,r,t),H("y",e,r,t),f),s={x:v.x,y:v.y};et(r);n?ut("customZoomTo",0,1,n,i||y.easing.sine.inOut,a):a(1)}},dt=30,mt=10,E={},pt={},O={},k={},ft={},ht=[],yt={},xt=[],gt={},vt=0,wt=e(),bt=0,R=e(),It=e(),Ct=e(),Dt=function(e,t){return e.x===t.x&&e.y===t.y},Tt=function(e,t){return gt.x=Math.abs(e.x-t.x),gt.y=Math.abs(e.y-t.y),Math.sqrt(gt.x*gt.x+gt.y*gt.y)},Mt=function(){Ee&&(fe(Ee),Ee=null)},St=function(){s&&(Ee=pe(St),Ht())},At=function(){return!("fit"===g.scaleMode&&f===x.currItem.initialZoomLevel)},Et=function(e,t){return!(!e||e===document||e.getAttribute("class")&&-1<e.getAttribute("class").indexOf("pswp__scroll-wrap"))&&(t(e)?e:Et(e.parentNode,t))},Ot={},kt=function(e,t){return Ot.prevent=!Et(e.target,g.isClickableElement),C("preventDragEvent",e,t,Ot),Ot.prevent},Rt=function(e,t){return t.x=e.pageX,t.y=e.pageY,t.id=e.identifier,t},Pt=function(e,t,n){n.x=.5*(e.x+t.x),n.y=.5*(e.y+t.y)},Zt=function(e,t,n){var i;50<e-be&&((i=2<xt.length?xt.shift():{}).x=t,i.y=n,xt.push(i),be=e)},Ft=function(){var e=v.y-x.currItem.initialPosition.y;return 1-Math.abs(e/(w.y/2))},Lt={},zt={},_t=[],Nt=function(e){for(;0<_t.length;)_t.pop();return me?(_e=0,ht.forEach(function(e){0===_e?_t[0]=e:1===_e&&(_t[1]=e),_e++})):-1<e.type.indexOf("touch")?e.touches&&0<e.touches.length&&(_t[0]=Rt(e.touches[0],Lt),1<e.touches.length)&&(_t[1]=Rt(e.touches[1],zt)):(Lt.x=e.pageX,Lt.y=e.pageY,Lt.id="",_t[0]=Lt),_t},Ut=function(e,t){var n,i,o,a=v[e]+t[e],r=0<t[e],l=R.x+t.x,s=R.x-yt.x,c=a>d.min[e]||a<d.max[e]?g.panEndFriction:1,a=v[e]+t[e]*c;if((g.allowPanToNext||f===x.currItem.initialZoomLevel)&&(Re?"h"!==Pe||"x"!==e||Se||(r?(a>d.min[e]&&(c=g.panEndFriction,d.min[e],n=d.min[e]-He[e]),(n<=0||s<0)&&1<P()?(o=l,s<0&&l>yt.x&&(o=yt.x)):d.min.x!==d.max.x&&(i=a)):(a<d.max[e]&&(c=g.panEndFriction,d.max[e],n=He[e]-d.max[e]),(n<=0||0<s)&&1<P()?(o=l,0<s&&l<yt.x&&(o=yt.x)):d.min.x!==d.max.x&&(i=a))):o=l,"x"===e))return void 0!==o&&(Je(o,!0),Oe=o!==yt.x),d.min.x!==d.max.x&&(void 0!==i?v.x=i:Oe||(v.x+=t.x*c)),void 0!==o;h||Oe||f>x.currItem.fitRatio&&(v[e]+=t[e]*c)},Ht=function(){if(c){var e,t,n,i,o,a=c.length;if(0!==a)if(S(E,c[0]),O.x=E.x-k.x,O.y=E.y-k.y,u&&1<a)k.x=E.x,k.y=E.y,!O.x&&!O.y&&Dt(c[1],pt)||(S(pt,c[1]),Se||(Se=!0,C("zoomGestureStarted")),a=Tt(E,pt),(e=Gt(a))>x.currItem.initialZoomLevel+x.currItem.initialZoomLevel/15&&(ze=!0),t=1,n=ot(),i=at(),e<n?g.pinchToClose&&!ze&&te<=x.currItem.initialZoomLevel?(T(o=1-(n-e)/(n/1.2)),C("onPinchClose",o),Fe=!0):e=n-(t=1<(t=(n-e)/n)?1:t)*(n/3):i<e&&(e=i+(t=1<(t=(e-i)/(6*n))?1:t)*n),t<0&&(t=0),Pt(E,pt,wt),Ue.x+=wt.x-Ct.x,Ue.y+=wt.y-Ct.y,S(Ct,wt),v.x=Qe("x",e),v.y=Qe("y",e),Ce=f<e,f=e,M());else if(Pe&&(Ze&&(Ze=!1,Math.abs(O.x)>=mt&&(O.x-=c[0].x-ft.x),Math.abs(O.y)>=mt)&&(O.y-=c[0].y-ft.y),k.x=E.x,k.y=E.y,0!==O.x||0!==O.y)){if("v"===Pe&&g.closeOnVerticalDrag)if(!At())return Ue.y+=O.y,v.y+=O.y,o=Ft(),De=!0,C("onVerticalDrag",o),T(o),void M();Zt(D(),E.x,E.y),Ae=!0,d=x.currItem.bounds,Ut("x",O)||(Ut("y",O),et(v),M())}}},Yt=function(){var t,n,i={lastFlickOffset:{},lastFlickDist:{},lastFlickSpeed:{},slowDownRatio:{},slowDownRatioReverse:{},speedDecelerationRatio:{},speedDecelerationRatioAbs:{},distanceOffset:{},backAnimDestination:{},backAnimStarted:{},calculateSwipeSpeed:function(e){n=(1<xt.length?(t=D()-be+50,xt[xt.length-2]):(t=D()-we,ft))[e],i.lastFlickOffset[e]=k[e]-n,i.lastFlickDist[e]=Math.abs(i.lastFlickOffset[e]),20<i.lastFlickDist[e]?i.lastFlickSpeed[e]=i.lastFlickOffset[e]/t:i.lastFlickSpeed[e]=0,Math.abs(i.lastFlickSpeed[e])<.1&&(i.lastFlickSpeed[e]=0),i.slowDownRatio[e]=.95,i.slowDownRatioReverse[e]=1-i.slowDownRatio[e],i.speedDecelerationRatio[e]=1},calculateOverBoundsAnimOffset:function(t,e){i.backAnimStarted[t]||(v[t]>d.min[t]?i.backAnimDestination[t]=d.min[t]:v[t]<d.max[t]&&(i.backAnimDestination[t]=d.max[t]),void 0!==i.backAnimDestination[t]&&(i.slowDownRatio[t]=.7,i.slowDownRatioReverse[t]=1-i.slowDownRatio[t],i.speedDecelerationRatioAbs[t]<.05)&&(i.lastFlickSpeed[t]=0,i.backAnimStarted[t]=!0,ut("bounceZoomPan"+t,v[t],i.backAnimDestination[t],e||300,y.easing.sine.out,function(e){v[t]=e,M()})))},calculateAnimOffset:function(e){i.backAnimStarted[e]||(i.speedDecelerationRatio[e]=i.speedDecelerationRatio[e]*(i.slowDownRatio[e]+i.slowDownRatioReverse[e]-i.slowDownRatioReverse[e]*i.timeDiff/10),i.speedDecelerationRatioAbs[e]=Math.abs(i.lastFlickSpeed[e]*i.speedDecelerationRatio[e]),i.distanceOffset[e]=i.lastFlickSpeed[e]*i.speedDecelerationRatio[e]*i.timeDiff,v[e]+=i.distanceOffset[e])},panAnimLoop:function(){A.zoomPan&&(A.zoomPan.raf=pe(i.panAnimLoop),i.now=D(),i.timeDiff=i.now-i.lastNow,i.lastNow=i.now,i.calculateAnimOffset("x"),i.calculateAnimOffset("y"),M(),i.calculateOverBoundsAnimOffset("x"),i.calculateOverBoundsAnimOffset("y"),i.speedDecelerationRatioAbs.x<.05)&&i.speedDecelerationRatioAbs.y<.05&&(v.x=Math.round(v.x),v.y=Math.round(v.y),M(),lt("zoomPan"))}};return i},Wt=function(e){if(e.calculateSwipeSpeed("y"),d=x.currItem.bounds,e.backAnimDestination={},e.backAnimStarted={},Math.abs(e.lastFlickSpeed.x)<=.05&&Math.abs(e.lastFlickSpeed.y)<=.05)return e.speedDecelerationRatioAbs.x=e.speedDecelerationRatioAbs.y=0,e.calculateOverBoundsAnimOffset("x"),e.calculateOverBoundsAnimOffset("y"),!0;st("zoomPan"),e.lastNow=D(),e.panAnimLoop()},Bt=function(e,t){h||(vt=p),"swipe"===e&&(e=k.x-ft.x,a=t.lastFlickDist.x<10,dt<e&&(a||20<t.lastFlickOffset.x)?i=-1:e<-dt&&(a||t.lastFlickOffset.x<-20)&&(i=1)),i&&((p+=i)<0?(p=g.loop?P()-1:0,o=!0):p>=P()&&(p=g.loop?0:P()-1,o=!0),o&&!g.loop||(I+=i,Ye-=i,n=!0));var n,i,o,e=b.x*Ye,a=Math.abs(e-R.x),r=n||e>R.x==0<t.lastFlickSpeed.x?(r=0<Math.abs(t.lastFlickSpeed.x)?a/Math.abs(t.lastFlickSpeed.x):333,r=Math.min(r,400),Math.max(r,250)):333;return vt===p&&(n=!1),h=!0,C("mainScrollAnimStart"),ut("mainScroll",R.x,e,r,y.easing.cubic.out,Je,function(){ct(),h=!1,vt=-1,!n&&vt===p||x.updateCurrItem(),C("mainScrollAnimComplete")}),n&&x.updateCurrItem(!0),n},Gt=function(e){return 1/ke*e*te},Xt=function(){var e,t=f,n=ot(),i=at(),o=(f<n?t=n:i<f&&(t=i),Le);return Fe&&!Ce&&!ze&&f<n?x.close():(Fe&&(e=function(e){T((1-o)*e+o)}),x.zoomTo(t,0,200,y.easing.cubic.out,e)),!0};N("Gestures",{publicMethods:{initGestures:function(){function e(e,t,n,i,o){le=e+t,se=e+n,ce=e+i,ue=o?e+o:""}(me=l.pointerEvent)&&l.touch&&(l.touch=!1),me?navigator.msPointerEnabled?e("MSPointer","Down","Move","Up","Cancel"):e("pointer","down","move","up","cancel"):l.touch?(e("touch","start","move","end","cancel"),o=!0):e("mouse","down","move","up"),Q=se+" "+ce+" "+ue,ee=le,me&&!o&&(o=1<navigator.maxTouchPoints||1<navigator.msMaxTouchPoints),x.likelyTouchDevice=o,i[le]=G,i[se]=X,i[ce]=V,ue&&(i[ue]=i[ce]),l.touch&&(ee+=" mousedown",Q+=" mousemove mouseup",i.mousedown=i[le],i.mousemove=i[se],i.mouseup=i[ce]),o||(g.allowPanToNext=!1)}}});function Vt(e){function t(){e.loading=!1,e.loaded=!0,e.loadComplete?e.loadComplete(e):e.img=null,n.onload=n.onerror=null,n=null}e.loading=!0,e.loaded=!1;var n=e.img=y.createEl("pswp__img","img");n.onload=t,n.onerror=function(){e.loadError=!0,t()},n.src=e.src}function Kt(e,t){return e.src&&e.loadError&&e.container&&(t&&(e.container.innerHTML=""),e.container.innerHTML=g.errorMsg.replace("%url%",e.src),1)}function qt(){if(nn.length){for(var e,t=0;t<nn.length;t++)(e=nn[t]).holder.index===e.index&&sn(e.index,e.item,e.baseDiv,e.img,!1,e.clearPlaceholder);nn=[]}}var $t,jt,Jt,Qt,en,P,tn=function(a,e,r,t){function l(){lt("initialZoom"),r?(x.template.removeAttribute("style"),x.bg.removeAttribute("style")):(T(1),e&&(e.style.display="block"),y.addClass(m,"pswp--animated-in"),C("initialZoom"+(r?"OutEnd":"InEnd"))),t&&t(),Qt=!1}$t&&clearTimeout($t),Jt=Qt=!0,a.initialLayout?(s=a.initialLayout,a.initialLayout=null):s=g.getThumbBoundsFn&&g.getThumbBoundsFn(p);var s,c,u,d=r?g.hideAnimationDuration:g.showAnimationDuration;d&&s&&void 0!==s.x?(c=$,u=!x.currItem.src||x.currItem.loadError||g.showHideOpacity,a.miniImg&&(a.miniImg.style.webkitBackfaceVisibility="hidden"),r||(f=s.w/a.w,v.x=s.x,v.y=s.y-ye,x[u?"template":"bg"].style.opacity=.001,M()),st("initialZoom"),r&&!c&&y.removeClass(m,"pswp--animated-in"),u&&(r?y[(c?"remove":"add")+"Class"](m,"pswp--animate_opacity"):setTimeout(function(){y.addClass(m,"pswp--animate_opacity")},30)),$t=setTimeout(function(){var t,n,i,o,e;C("initialZoom"+(r?"Out":"In")),r?(t=s.w/a.w,n={x:v.x,y:v.y},i=f,o=Le,e=function(e){1===e?(f=t,v.x=s.x,v.y=s.y-ge):(f=(t-i)*e+i,v.x=(s.x-n.x)*e+n.x,v.y=(s.y-ge-n.y)*e+n.y),M(),u?m.style.opacity=1-e:T(o-e*o)},c?ut("initialZoom",0,1,d,y.easing.cubic.out,e,l):(e(1),$t=setTimeout(l,d+20))):(f=a.initialZoomLevel,S(v,a.initialPosition),M(),T(1),u?m.style.opacity=1:T(1),$t=setTimeout(l,d+20))},r?25:90)):(C("initialZoom"+(r?"Out":"In")),f=a.initialZoomLevel,S(v,a.initialPosition),M(),m.style.opacity=r?0:1,T(1),d?setTimeout(function(){l()},d):l())},Z={},nn=[],on={index:0,errorMsg:'<div class="pswp__error-msg"><a href="%url%" target="_blank">The image</a> could not be loaded.</div>',forceProgressiveLoading:!1,preload:[1,1],getNumItemsFn:function(){return jt.length}},an=function(){return{center:{x:0,y:0},max:{x:0,y:0},min:{x:0,y:0}}},rn=function(e,t,n){var i=e.bounds;i.center.x=Math.round((Z.x-t)/2),i.center.y=Math.round((Z.y-n)/2)+e.vGap.top,i.max.x=t>Z.x?Math.round(Z.x-t):i.center.x,i.max.y=n>Z.y?Math.round(Z.y-n)+e.vGap.top:i.center.y,i.min.x=t>Z.x?0:i.center.x,i.min.y=n>Z.y?e.vGap.top:i.center.y},ln=function(e,t,n){var i,o;return!e.src||e.loadError?(e.w=e.h=0,e.initialZoomLevel=e.fitRatio=1,e.bounds=an(),e.initialPosition=e.bounds.center,e.bounds):((i=!n)&&(e.vGap||(e.vGap={top:0,bottom:0}),C("parseVerticalMargin",e)),Z.x=t.x,Z.y=t.y-e.vGap.top-e.vGap.bottom,i&&(t=Z.x/e.w,o=Z.y/e.h,e.fitRatio=t<o?t:o,"orig"===(t=g.scaleMode)?n=1:"fit"===t&&(n=e.fitRatio),e.initialZoomLevel=n=1<n?1:n,e.bounds||(e.bounds=an())),n?(rn(e,e.w*n,e.h*n),i&&n===e.initialZoomLevel&&(e.initialPosition=e.bounds.center),e.bounds):void 0)},sn=function(e,t,n,i,o,a){t.loadError||i&&(t.imageAppended=!0,cn(t,i,t===x.currItem&&Ve),n.appendChild(i),a)&&setTimeout(function(){t&&t.loaded&&t.placeholder&&(t.placeholder.style.display="none",t.placeholder=null)},500)},cn=function(e,t,n){var i;e.src&&(t=t||e.container.lastChild,i=n?e.w:Math.round(e.w*e.fitRatio),n=n?e.h:Math.round(e.h*e.fitRatio),e.placeholder&&!e.loaded&&(e.placeholder.style.width=i+"px",e.placeholder.style.height=n+"px"),t.style.width=i+"px",t.style.height=n+"px")};N("Controller",{publicMethods:{lazyLoadItem:function(e){e=U(e);var t=en(e);t&&(!t.loaded&&!t.loading||ae)&&(C("gettingData",e,t),t.src)&&Vt(t)},initController:function(){y.extend(g,on,!0),x.items=jt=t,en=x.getItemAt,P=g.getNumItemsFn,g.loop,P()<3&&(g.loop=!1),a("beforeChange",function(e){for(var t=g.preload,n=null===e||0<=e,i=Math.min(t[0],P()),o=Math.min(t[1],P()),a=1;a<=(n?o:i);a++)x.lazyLoadItem(p+a);for(a=1;a<=(n?i:o);a++)x.lazyLoadItem(p-a)}),a("initialLayout",function(){x.currItem.initialLayout=g.getThumbBoundsFn&&g.getThumbBoundsFn(p)}),a("mainScrollAnimComplete",qt),a("initialZoomInEnd",qt),a("destroy",function(){for(var e,t=0;t<jt.length;t++)(e=jt[t]).container&&(e.container=null),e.placeholder&&(e.placeholder=null),e.img&&(e.img=null),e.preloader&&(e.preloader=null),e.loadError&&(e.loaded=e.loadError=!1);nn=null})},getItemAt:function(e){return 0<=e&&void 0!==jt[e]&&jt[e]},allowProgressiveImg:function(){return g.forceProgressiveLoading||!o||g.mouseUsed||1200<screen.width},setContent:function(t,n){g.loop&&(n=U(n));var e,i,o,a=x.getItemAt(t.index),a=(a&&(a.container=null),x.getItemAt(n));a?(C("gettingData",n,a),t.index=n,i=(t.item=a).container=y.createEl("pswp__zoom-wrap"),!a.src&&a.html&&(a.html.tagName?i.appendChild(a.html):i.innerHTML=a.html),Kt(a),ln(a,w),!a.src||a.loadError||a.loaded?a.src&&!a.loadError&&((e=y.createEl("pswp__img","img")).style.opacity=1,e.src=a.src,cn(a,e),sn(n,a,i,e,!0)):(a.loadComplete=function(e){if(K){if(t&&t.index===n){if(Kt(e,!0))return e.loadComplete=e.img=null,ln(e,w),$e(e),void(t.index===p&&x.updateCurrZoomItem());e.imageAppended?!Qt&&e.placeholder&&(e.placeholder.style.display="none",e.placeholder=null):l.transform&&(h||Qt)?nn.push({item:e,baseDiv:i,img:e.img,index:n,holder:t,clearPlaceholder:!0}):sn(n,e,i,e.img,h||Qt,!0)}e.loadComplete=null,e.img=null,C("imageLoadComplete",n,e)}},y.features.transform&&(o="pswp__img pswp__img--placeholder",o+=a.msrc?"":" pswp__img--placeholder--blank",o=y.createEl(o,a.msrc?"img":""),a.msrc&&(o.src=a.msrc),cn(a,o),i.appendChild(o),a.placeholder=o),a.loading||Vt(a),x.allowProgressiveImg()&&(!Jt&&l.transform?nn.push({item:a,baseDiv:i,img:a.img,index:n,holder:t}):sn(n,a,i,a.img,!0,!0))),Jt||n!==p?$e(a):(Re=i.style,tn(a,e||a.img)),t.el.innerHTML="",t.el.appendChild(i)):t.el.innerHTML=""},cleanSlide:function(e){e.img&&(e.img.onload=e.img.onerror=null),e.loaded=e.loading=e.img=e.imageAppended=!1}}});function un(e,t,n){var i=document.createEvent("CustomEvent"),t={origEvent:e,target:e.target,releasePoint:t,pointerType:n||"touch"};i.initCustomEvent("pswpTap",!0,!0,t),e.target.dispatchEvent(i)}function dn(){fn&&clearTimeout(fn),yn&&clearTimeout(yn)}function mn(){var e=Mn(),t={};if(!(e.length<5)){var n,i=e.split("&");for(a=0;a<i.length;a++)i[a]&&((n=i[a].split("=")).length<2||(t[n[0]]=n[1]));if(g.galleryPIDs){for(var o=t.pid,a=t.pid=0;a<jt.length;a++)if(jt[a].pid===o){t.pid=a;break}}else t.pid=parseInt(t.pid,10)-1;t.pid<0&&(t.pid=0)}return t}var pn,F,fn,hn,yn,xn,gn,vn,n,wn,bn,In,L,Cn,Dn={},Tn=(N("Tap",{publicMethods:{initTap:function(){a("firstTouchStart",x.onTapStart),a("touchRelease",x.onTapRelease),a("destroy",function(){Dn={},pn=null})},onTapStart:function(e){1<e.length&&(clearTimeout(pn),pn=null)},onTapRelease:function(e,t){var n,i,o;!t||Ae||Me||rt||(n=t,pn&&(clearTimeout(pn),pn=null,i=n,o=Dn,Math.abs(i.x-o.x)<Ne)&&Math.abs(i.y-o.y)<Ne?C("doubleTap",n):"mouse"===t.type?un(e,t,"mouse"):"BUTTON"===e.target.tagName.toUpperCase()||y.hasClass(e.target,"pswp__single-tap")?un(e,t):(S(Dn,n),pn=setTimeout(function(){un(e,t),pn=null},300)))}}}),N("DesktopZoom",{publicMethods:{initDesktopZoom:function(){xe||(o?a("mouseUsed",function(){x.setupDesktopZoom()}):x.setupDesktopZoom(!0))},setupDesktopZoom:function(e){F={};var t="wheel mousewheel DOMMouseScroll";a("bindEvents",function(){y.bind(m,t,x.handleMouseWheel)}),a("unbindEvents",function(){F&&y.unbind(m,t,x.handleMouseWheel)}),x.mouseZoomedIn=!1;function n(){x.mouseZoomedIn&&(y.removeClass(m,"pswp--zoomed-in"),x.mouseZoomedIn=!1),f<1?y.addClass(m,"pswp--zoom-allowed"):y.removeClass(m,"pswp--zoom-allowed"),o()}var i,o=function(){i&&(y.removeClass(m,"pswp--dragging"),i=!1)};a("resize",n),a("afterChange",n),a("pointerDown",function(){x.mouseZoomedIn&&(i=!0,y.addClass(m,"pswp--dragging"))}),a("pointerUp",o),e||n()},handleMouseWheel:function(e){if(f<=x.currItem.fitRatio)return g.modal&&(!g.closeOnScroll||rt||s?e.preventDefault():de&&2<Math.abs(e.deltaY)&&($=!0,x.close())),!0;if(e.stopPropagation(),F.x=0,"deltaX"in e)1===e.deltaMode?(F.x=18*e.deltaX,F.y=18*e.deltaY):(F.x=e.deltaX,F.y=e.deltaY);else if("wheelDelta"in e)e.wheelDeltaX&&(F.x=-.16*e.wheelDeltaX),e.wheelDeltaY?F.y=-.16*e.wheelDeltaY:F.y=-.16*e.wheelDelta;else{if(!("detail"in e))return;F.y=e.detail}it(f,!0);var t=v.x-F.x,n=v.y-F.y;(g.modal||t<=d.min.x&&t>=d.max.x&&n<=d.min.y&&n>=d.max.y)&&e.preventDefault(),x.panTo(t,n)},toggleDesktopZoom:function(e){e=e||{x:w.x/2+We.x,y:w.y/2+We.y};var t=g.getDoubleTapZoom(!0,x.currItem),n=f===t;x.mouseZoomedIn=!n,x.zoomTo(n?x.currItem.initialZoomLevel:t,e,333),y[(n?"remove":"add")+"Class"](m,"pswp--zoomed-in")}}}),{history:!0,galleryUID:1}),Mn=function(){return L.hash.substring(1)},Sn=function(){var e,t;yn&&clearTimeout(yn),rt||s?yn=setTimeout(Sn,500):(xn?clearTimeout(hn):xn=!0,t=p+1,(e=en(p)).hasOwnProperty("pid")&&(t=e.pid),e=n+"&gid="+g.galleryUID+"&pid="+t,wn||-1===L.hash.indexOf(e)&&(In=!0),t=L.href.split("#")[0]+"#"+e,Cn?"#"+e!==window.location.hash&&history[wn?"replaceState":"pushState"]("",document.title,t):wn?L.replace(t):L.hash=e,wn=!0,hn=setTimeout(function(){xn=!1},60))};N("History",{publicMethods:{initHistory:function(){var e,t;y.extend(g,Tn,!0),g.history&&(L=window.location,wn=bn=In=!1,n=Mn(),Cn="pushState"in history,-1<n.indexOf("gid=")&&(n=(n=n.split("&gid=")[0]).split("?gid=")[0]),a("afterChange",x.updateURL),a("unbindEvents",function(){y.unbind(window,"hashchange",x.onHashChange)}),e=function(){vn=!0,bn||(In?history.back():n?L.hash=n:Cn?history.pushState("",document.title,L.pathname+L.search):L.hash=""),dn()},a("unbindEvents",function(){$&&e()}),a("destroy",function(){vn||e()}),a("firstUpdate",function(){p=mn().pid}),-1<(t=n.indexOf("pid="))&&"&"===(n=n.substring(0,t)).slice(-1)&&(n=n.slice(0,-1)),setTimeout(function(){K&&y.bind(window,"hashchange",x.onHashChange)},40))},onHashChange:function(){Mn()===n?(bn=!0,x.close()):xn||(gn=!0,x.goTo(mn().pid),gn=!1)},updateURL:function(){dn(),gn||(wn?fn=setTimeout(Sn,800):Sn())}}}),y.extend(x,_)}});