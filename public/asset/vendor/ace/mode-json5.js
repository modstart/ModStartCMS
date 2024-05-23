define("ace/mode/json_highlight_rules",["require","exports","module","ace/lib/oop","ace/mode/text_highlight_rules"],function(e,t,n){"use strict";var i=e("../lib/oop"),o=e("./text_highlight_rules").TextHighlightRules,e=function(){this.$rules={start:[{token:"variable",regex:'["](?:(?:\\\\.)|(?:[^"\\\\]))*?["]\\s*(?=:)'},{token:"string",regex:'"',next:"string"},{token:"constant.numeric",regex:"0[xX][0-9a-fA-F]+\\b"},{token:"constant.numeric",regex:"[+-]?\\d+(?:(?:\\.\\d*)?(?:[eE][+-]?\\d+)?)?\\b"},{token:"constant.language.boolean",regex:"(?:true|false)\\b"},{token:"text",regex:"['](?:(?:\\\\.)|(?:[^'\\\\]))*?[']"},{token:"comment",regex:"\\/\\/.*$"},{token:"comment.start",regex:"\\/\\*",next:"comment"},{token:"paren.lparen",regex:"[[({]"},{token:"paren.rparen",regex:"[\\])}]"},{token:"text",regex:"\\s+"}],string:[{token:"constant.language.escape",regex:/\\(?:x[0-9a-fA-F]{2}|u[0-9a-fA-F]{4}|["\\\/bfnrt])/},{token:"string",regex:'"|$',next:"start"},{defaultToken:"string"}],comment:[{token:"comment.end",regex:"\\*\\/",next:"start"},{defaultToken:"comment"}]}};i.inherits(e,o),t.JsonHighlightRules=e}),define("ace/mode/json5_highlight_rules",["require","exports","module","ace/lib/oop","ace/mode/json_highlight_rules"],function(e,t,n){"use strict";var i=e("../lib/oop"),o=e("./json_highlight_rules").JsonHighlightRules,e=function(){o.call(this);var e,t=[{token:"variable",regex:/[a-zA-Z$_\u00a1-\uffff][\w$\u00a1-\uffff]*\s*(?=:)/},{token:"variable",regex:/['](?:(?:\\.)|(?:[^'\\]))*?[']\s*(?=:)/},{token:"constant.language.boolean",regex:/(?:null)\b/},{token:"string",regex:/'/,next:[{token:"constant.language.escape",regex:/\\(?:x[0-9a-fA-F]{2}|u[0-9a-fA-F]{4}|["\/bfnrt]|$)/,consumeLineEnd:!0},{token:"string",regex:/'|$/,next:"start"},{defaultToken:"string"}]},{token:"string",regex:/"(?![^"]*":)/,next:[{token:"constant.language.escape",regex:/\\(?:x[0-9a-fA-F]{2}|u[0-9a-fA-F]{4}|["\/bfnrt]|$)/,consumeLineEnd:!0},{token:"string",regex:/"|$/,next:"start"},{defaultToken:"string"}]},{token:"constant.numeric",regex:/[+-]?(?:Infinity|NaN)\b/}];for(e in this.$rules)this.$rules[e].unshift.apply(this.$rules[e],t);this.normalizeRules()};i.inherits(e,o),t.Json5HighlightRules=e}),define("ace/mode/matching_brace_outdent",["require","exports","module","ace/range"],function(e,t,n){"use strict";var o=e("../range").Range,e=function(){};(function(){this.checkOutdent=function(e,t){return!!/^\s+$/.test(e)&&/^\s*\}/.test(t)},this.autoOutdent=function(e,t){var n=e.getLine(t).match(/^(\s*\})/);if(!n)return 0;var i=n[1].length,n=e.findMatchingBracket({row:t,column:i});if(!n||n.row==t)return 0;n=this.$getIndent(e.getLine(n.row));e.replace(new o(t,0,t,i-1),n)},this.$getIndent=function(e){return e.match(/^\s*/)[0]}}).call(e.prototype),t.MatchingBraceOutdent=e}),define("ace/mode/folding/cstyle",["require","exports","module","ace/lib/oop","ace/range","ace/mode/folding/fold_mode"],function(e,t,n){"use strict";var i=e("../../lib/oop"),c=e("../../range").Range,e=e("./fold_mode").FoldMode,t=t.FoldMode=function(e){e&&(this.foldingStartMarker=new RegExp(this.foldingStartMarker.source.replace(/\|[^|]*?$/,"|"+e.start)),this.foldingStopMarker=new RegExp(this.foldingStopMarker.source.replace(/\|[^|]*?$/,"|"+e.end)))};i.inherits(t,e),function(){this.foldingStartMarker=/([\{\[\(])[^\}\]\)]*$|^\s*(\/\*)/,this.foldingStopMarker=/^[^\[\{\(]*([\}\]\)])|^[\s\*]*(\*\/)/,this.singleLineBlockCommentRe=/^\s*(\/\*).*\*\/\s*$/,this.tripleStarBlockCommentRe=/^\s*(\/\*\*\*).*\*\/\s*$/,this.startRegionRe=/^\s*(\/\*|\/\/)#?region\b/,this._getFoldWidgetBase=this.getFoldWidget,this.getFoldWidget=function(e,t,n){var i=e.getLine(n);if(this.singleLineBlockCommentRe.test(i)&&!this.startRegionRe.test(i)&&!this.tripleStarBlockCommentRe.test(i))return"";n=this._getFoldWidgetBase(e,t,n);return!n&&this.startRegionRe.test(i)?"start":n},this.getFoldWidgetRange=function(e,t,n,i){var o=e.getLine(n);if(this.startRegionRe.test(o))return this.getCommentRegionBlock(e,o,n);var r=o.match(this.foldingStartMarker);if(r){var s=r.index;if(r[1])return this.openingBracketBlock(e,r[1],n,s);var a=e.getCommentFoldRange(n,s+r[0].length,1);return a&&!a.isMultiLine()&&(i?a=this.getSectionRange(e,n):"all"!=t&&(a=null)),a}if("markbegin"!==t&&(r=o.match(this.foldingStopMarker))){s=r.index+r[0].length;return r[1]?this.closingBracketBlock(e,r[1],n,s):e.getCommentFoldRange(n,s,-1)}},this.getSectionRange=function(e,t){for(var n=(a=e.getLine(t)).search(/\S/),i=t,o=a.length,r=t+=1,s=e.getLength();++t<s;){var a,g=(a=e.getLine(t)).search(/\S/);if(-1!==g){if(g<n)break;var l=this.getFoldWidgetRange(e,"all",t);if(l){if(l.start.row<=i)break;if(l.isMultiLine())t=l.end.row;else if(n==g)break}r=t}}return new c(i,o,r,e.getLine(r).length)},this.getCommentRegionBlock=function(e,t,n){for(var i=t.search(/\s*$/),o=e.getLength(),r=n,s=/^\s*(?:\/\*|\/\/|--)#?(end)?region\b/,a=1;++n<o;){t=e.getLine(n);var g=s.exec(t);if(g&&(g[1]?a--:a++,!a))break}if(r<n)return new c(r,i,n,t.length)}}.call(t.prototype)}),define("ace/mode/json5",["require","exports","module","ace/lib/oop","ace/mode/text","ace/mode/json5_highlight_rules","ace/mode/matching_brace_outdent","ace/mode/behaviour/cstyle","ace/mode/folding/cstyle"],function(e,t,n){"use strict";var i=e("../lib/oop"),o=e("./text").Mode,r=e("./json5_highlight_rules").Json5HighlightRules,s=e("./matching_brace_outdent").MatchingBraceOutdent,a=e("./behaviour/cstyle").CstyleBehaviour,g=e("./folding/cstyle").FoldMode,e=function(){this.HighlightRules=r,this.$outdent=new s,this.$behaviour=new a,this.foldingRules=new g};i.inherits(e,o),function(){this.lineCommentStart="//",this.blockComment={start:"/*",end:"*/"},this.checkOutdent=function(e,t,n){return this.$outdent.checkOutdent(t,n)},this.autoOutdent=function(e,t,n){this.$outdent.autoOutdent(t,n)},this.$id="ace/mode/json5"}.call(e.prototype),t.Mode=e}),window.require(["ace/mode/json5"],function(e){"object"==typeof module&&"object"==typeof exports&&module&&(module.exports=e)});