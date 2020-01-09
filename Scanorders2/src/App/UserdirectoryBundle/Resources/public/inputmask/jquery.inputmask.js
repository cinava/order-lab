/*
 Input Mask plugin for jquery
 http://github.com/RobinHerbots/jquery.inputmask
 Copyright (c) 2010 - 2014 Robin Herbots
 Licensed under the MIT license (http://www.opensource.org/licenses/mit-license.php)
 Version: 2.4.17
*/
(function(d){if(void 0===d.fn.inputmask){var L=function(d){var c=document.createElement("input");d="on"+d;var a=d in c;a||(c.setAttribute(d,"return;"),a="function"==typeof c[d]);return a},J=function(e,c,a){return(e=a.aliases[e])?(e.alias&&J(e.alias,void 0,a),d.extend(!0,a,e),d.extend(!0,a,c),!0):!1},M=function(e){function c(a){e.numericInput&&(a=a.split("").reverse().join(""));var b=!1,g=0,c=e.greedy,s=e.repeat;"*"==s&&(c=!1);1==a.length&&!1==c&&0!=s&&(e.placeholder="");a=d.map(a.split(""),function(a,
d){var c=[];if(a==e.escapeChar)b=!0;else if(a!=e.optionalmarker.start&&a!=e.optionalmarker.end||b){var m=e.definitions[a];if(m&&!b)for(var k=0;k<m.cardinality;k++)c.push(e.placeholder.charAt((g+k)%e.placeholder.length));else c.push(a),b=!1;g+=c.length;return c}});for(var m=a.slice(),k=1;k<s&&c;k++)m=m.concat(a.slice());return{mask:m,repeat:s,greedy:c}}function a(a){e.numericInput&&(a=a.split("").reverse().join(""));var b=!1,g=!1,c=!1;return d.map(a.split(""),function(a,d){var k=[];if(a==e.escapeChar)g=
!0;else{if(a!=e.optionalmarker.start||g){if(a!=e.optionalmarker.end||g){var f=e.definitions[a];if(f&&!g){for(var l=f.prevalidator,y=l?l.length:0,A=1;A<f.cardinality;A++){var D=y>=A?l[A-1]:[],B=D.validator,D=D.cardinality;k.push({fn:B?"string"==typeof B?RegExp(B):new function(){this.test=B}:/./,cardinality:D?D:1,optionality:b,newBlockMarker:!0==b?c:!1,offset:0,casing:f.casing,def:f.definitionSymbol||a});!0==b&&(c=!1)}k.push({fn:f.validator?"string"==typeof f.validator?RegExp(f.validator):new function(){this.test=
f.validator}:/./,cardinality:f.cardinality,optionality:b,newBlockMarker:c,offset:0,casing:f.casing,def:f.definitionSymbol||a})}else k.push({fn:null,cardinality:0,optionality:b,newBlockMarker:c,offset:0,casing:null,def:a}),g=!1;c=!1;return k}b=!1}else b=!0;c=!0}})}function b(a){for(var b=a.length,d=0;d<b&&a.charAt(d)!=e.optionalmarker.start;d++);var g=[a.substring(0,d)];d<b&&g.push(a.substring(d+1,b));return g}function f(y,h,v){for(var p=0,s=0,m=h.length,k=0;k<m&&!(h.charAt(k)==e.optionalmarker.start&&
p++,h.charAt(k)==e.optionalmarker.end&&s++,0<p&&p==s);k++);p=[h.substring(0,k)];k<m&&p.push(h.substring(k+1,m));k=b(p[0]);1<k.length?(h=y+k[0]+(e.optionalmarker.start+k[1]+e.optionalmarker.end)+(1<p.length?p[1]:""),-1==d.inArray(h,g)&&""!=h&&(g.push(h),m=c(h),l.push({mask:h,_buffer:m.mask,buffer:m.mask.slice(),tests:a(h),lastValidPosition:-1,greedy:m.greedy,repeat:m.repeat,metadata:v})),h=y+k[0]+(1<p.length?p[1]:""),-1==d.inArray(h,g)&&""!=h&&(g.push(h),m=c(h),l.push({mask:h,_buffer:m.mask,buffer:m.mask.slice(),
tests:a(h),lastValidPosition:-1,greedy:m.greedy,repeat:m.repeat,metadata:v})),1<b(k[1]).length&&f(y+k[0],k[1]+p[1],v),1<p.length&&1<b(p[1]).length&&(f(y+k[0]+(e.optionalmarker.start+k[1]+e.optionalmarker.end),p[1],v),f(y+k[0],p[1],v))):(h=y+p,-1==d.inArray(h,g)&&""!=h&&(g.push(h),m=c(h),l.push({mask:h,_buffer:m.mask,buffer:m.mask.slice(),tests:a(h),lastValidPosition:-1,greedy:m.greedy,repeat:m.repeat,metadata:v})))}var l=[],g=[];d.isFunction(e.mask)&&(e.mask=e.mask.call(this,e));d.isArray(e.mask)?
d.each(e.mask,function(a,d){void 0!=d.mask?f("",d.mask.toString(),d):f("",d.toString())}):f("",e.mask.toString());return e.greedy?l:l.sort(function(a,d){return a.mask.length-d.mask.length})},ea=null!==navigator.userAgent.match(/msie 10/i),fa=null!==navigator.userAgent.match(/iphone/i),S=null!==navigator.userAgent.match(/android.*safari.*/i),W=null!==navigator.userAgent.match(/android.*chrome.*/i),ga=L("paste")?"paste":L("input")?"input":"propertychange",x=function(e,c,a){function b(){return e[c]}
function f(){return b().tests}function l(){return b()._buffer}function g(){return b().buffer}function y(n,r,q){function f(d,b,n,r){for(var g=p(d),e=n?1:0,q="",U=b.buffer,H=b.tests[g].cardinality;H>e;H--)q+=G(U,g-(H-1));n&&(q+=n);return null!=b.tests[g].fn?b.tests[g].fn.test(q,U,d,r,a):n==G(b._buffer,d,!0)||n==a.skipOptionalPartCharacter?{refresh:!0,c:G(b._buffer,d,!0),pos:d}:!1}if(q=!0===q){var l=f(n,b(),r,q);!0===l&&(l={pos:n});return l}var h=[],l=!1,y=c,t=g().slice(),u=b().lastValidPosition;k(n);
var z=[];d.each(e,function(a,d){if("object"==typeof d){c=a;var e=n,k=b().lastValidPosition,p;if(k==u){if(1<e-u)for(k=-1==k?0:k;k<e&&(p=f(k,b(),t[k],!0),!1!==p);k++)F(g(),k,t[k],!0),!0===p&&(p={pos:k}),p=p.pos||k,b().lastValidPosition<p&&(b().lastValidPosition=p);if(!v(e)&&!f(e,b(),r,q)){k=m(e)-e;for(p=0;p<k&&!1===f(++e,b(),r,q);p++);z.push(c)}}(b().lastValidPosition>=u||c==y)&&0<=e&&e<s()&&(l=f(e,b(),r,q),!1!==l&&(!0===l&&(l={pos:e}),p=l.pos||e,b().lastValidPosition<p&&(b().lastValidPosition=p)),
h.push({activeMasksetIndex:a,result:l}))}});c=y;return function(a,b){var g=!1;d.each(b,function(b,n){if(g=-1==d.inArray(n.activeMasksetIndex,a)&&!1!==n.result)return!1});if(g)b=d.map(b,function(b,n){if(-1==d.inArray(b.activeMasksetIndex,a))return b;e[b.activeMasksetIndex].lastValidPosition=u});else{var q=-1,c=-1,k;d.each(b,function(b,n){-1!=d.inArray(n.activeMasksetIndex,a)&&!1!==n.result&(-1==q||q>n.result.pos)&&(q=n.result.pos,c=n.activeMasksetIndex)});b=d.map(b,function(b,U){if(-1!=d.inArray(b.activeMasksetIndex,
a)){if(b.result.pos==q)return b;if(!1!==b.result){for(var H=n;H<q;H++)if(k=f(H,e[b.activeMasksetIndex],e[c].buffer[H],!0),!1===k){e[b.activeMasksetIndex].lastValidPosition=q-1;break}else F(e[b.activeMasksetIndex].buffer,H,e[c].buffer[H],!0),e[b.activeMasksetIndex].lastValidPosition=H;k=f(q,e[b.activeMasksetIndex],r,!0);!1!==k&&(F(e[b.activeMasksetIndex].buffer,q,r,!0),e[b.activeMasksetIndex].lastValidPosition=q);return b}}})}return b}(z,h)}function h(){var a=c,r={activeMasksetIndex:0,lastValidPosition:-1,
next:-1};d.each(e,function(a,d){"object"==typeof d&&(c=a,b().lastValidPosition>r.lastValidPosition?(r.activeMasksetIndex=a,r.lastValidPosition=b().lastValidPosition,r.next=m(b().lastValidPosition)):b().lastValidPosition==r.lastValidPosition&&(-1==r.next||r.next>m(b().lastValidPosition))&&(r.activeMasksetIndex=a,r.lastValidPosition=b().lastValidPosition,r.next=m(b().lastValidPosition)))});c=-1!=r.lastValidPosition&&e[a].lastValidPosition==r.lastValidPosition?a:r.activeMasksetIndex;a!=c&&(D(g(),m(r.lastValidPosition),
s()),b().writeOutBuffer=!0);t.data("_inputmask").activeMasksetIndex=c}function v(a){a=p(a);a=f()[a];return void 0!=a?a.fn:!1}function p(a){return a%f().length}function s(){return a.getMaskLength(l(),b().greedy,b().repeat,g(),a)}function m(a){var b=s();if(a>=b)return b;for(;++a<b&&!v(a););return a}function k(a){if(0>=a)return 0;for(;0<--a&&!v(a););return a}function F(a,b,d,e){e&&(b=x(a,b));e=f()[p(b)];var g=d;if(void 0!=g&&void 0!=e)switch(e.casing){case "upper":g=d.toUpperCase();break;case "lower":g=
d.toLowerCase()}a[b]=g}function G(a,b,d){d&&(b=x(a,b));return a[b]}function x(a,b){for(var d;void 0==a[b]&&a.length<s();)for(d=0;void 0!==l()[d];)a.push(l()[d++]);return b}function A(a,b,d){a._valueSet(b.join(""));void 0!=d&&u(a,d)}function D(a,b,d,e){for(var g=s();b<d&&b<g;b++)!0===e?v(b)||F(a,b,""):F(a,b,G(l().slice(),b,!0))}function B(a,b){var d=p(b);F(a,b,G(l(),d))}function O(b){return a.placeholder.charAt(b%a.placeholder.length)}function P(a,g,q,f,p){f=void 0!=f?f.slice():M(a._valueGet()).split("");
d.each(e,function(a,b){"object"==typeof b&&(b.buffer=b._buffer.slice(),b.lastValidPosition=-1,b.p=-1)});!0!==q&&(c=0);g&&a._valueSet("");s();d.each(f,function(e,c){if(!0===p){var f=b().p,f=-1==f?f:k(f),h=-1==f?e:m(f);-1==d.inArray(c,l().slice(f+1,h))&&d(a).trigger("_keypress",[!0,c.charCodeAt(0),g,q,e])}else d(a).trigger("_keypress",[!0,c.charCodeAt(0),g,q,e])});!0===q&&-1!=b().p&&(b().lastValidPosition=k(b().p))}function J(a){return d.inputmask.escapeRegex.call(this,a)}function M(a){return a.replace(RegExp("("+
J(l().join(""))+")*$"),"")}function V(a){var b=g(),d=b.slice(),e,c;for(c=d.length-1;0<=c;c--)if(e=p(c),f()[e].optionality)if(v(c)&&y(c,b[c],!0))break;else d.pop();else break;A(a,d)}function L(b,e){if(!f()||!0!==e&&b.hasClass("hasDatepicker"))return b[0]._valueGet();var c=d.map(g(),function(a,b){return v(b)&&y(b,a,!0)?a:null}),c=(z?c.reverse():c).join("");return void 0!=a.onUnMask?a.onUnMask.call(this,g().join(""),c):c}function K(b){!z||"number"!=typeof b||a.greedy&&""==a.placeholder||(b=g().length-
b);return b}function u(b,e,g){var c=b.jquery&&0<b.length?b[0]:b;if("number"==typeof e)e=K(e),g=K(g),d(b).is(":visible")&&(g="number"==typeof g?g:e,c.scrollLeft=c.scrollWidth,!1==a.insertMode&&e==g&&g++,c.setSelectionRange?(c.selectionStart=e,c.selectionEnd=S?e:g):c.createTextRange&&(b=c.createTextRange(),b.collapse(!0),b.moveEnd("character",g),b.moveStart("character",e),b.select()));else{if(!d(b).is(":visible"))return{begin:0,end:0};c.setSelectionRange?(e=c.selectionStart,g=c.selectionEnd):document.selection&&
document.selection.createRange&&(b=document.selection.createRange(),e=0-b.duplicate().moveStart("character",-1E5),g=e+b.text.length);e=K(e);g=K(g);return{begin:e,end:g}}}function R(b){if("*"!=a.repeat){var g=!1,f=0,m=c;d.each(e,function(a,d){if("object"==typeof d){c=a;var e=k(s());if(d.lastValidPosition>=f&&d.lastValidPosition==e){for(var m=!0,h=0;h<=e;h++){var u=v(h),y=p(h);if(u&&(void 0==b[h]||b[h]==O(h))||!u&&b[h]!=l()[y]){m=!1;break}}if(g=g||m)return!1}f=d.lastValidPosition}});c=m;return g}}function ca(n){function r(a){a=
d._data(a).events;d.each(a,function(a,b){d.each(b,function(a,b){if("inputmask"==b.namespace&&"setvalue"!=b.type&&"_keypress"!=b.type){var d=b.handler;b.handler=function(a){if(this.readOnly||this.disabled)a.preventDefault;else return d.apply(this,arguments)}}})})}function q(a){var b;Object.getOwnPropertyDescriptor&&(b=Object.getOwnPropertyDescriptor(a,"value"));if(b&&b.get){if(!a._valueGet){var e=b.get,g=b.set;a._valueGet=function(){return z?e.call(this).split("").reverse().join(""):e.call(this)};
a._valueSet=function(a){g.call(this,z?a.split("").reverse().join(""):a)};Object.defineProperty(a,"value",{get:function(){var a=d(this),b=d(this).data("_inputmask"),g=b.masksets,c=b.activeMasksetIndex;return b&&b.opts.autoUnmask?a.inputmask("unmaskedvalue"):e.call(this)!=g[c]._buffer.join("")?e.call(this):""},set:function(a){g.call(this,a);d(this).triggerHandler("setvalue.inputmask")}})}}else if(document.__lookupGetter__&&a.__lookupGetter__("value"))a._valueGet||(e=a.__lookupGetter__("value"),g=a.__lookupSetter__("value"),
a._valueGet=function(){return z?e.call(this).split("").reverse().join(""):e.call(this)},a._valueSet=function(a){g.call(this,z?a.split("").reverse().join(""):a)},a.__defineGetter__("value",function(){var a=d(this),b=d(this).data("_inputmask"),g=b.masksets,c=b.activeMasksetIndex;return b&&b.opts.autoUnmask?a.inputmask("unmaskedvalue"):e.call(this)!=g[c]._buffer.join("")?e.call(this):""}),a.__defineSetter__("value",function(a){g.call(this,a);d(this).triggerHandler("setvalue.inputmask")}));else if(a._valueGet||
(a._valueGet=function(){return z?this.value.split("").reverse().join(""):this.value},a._valueSet=function(a){this.value=z?a.split("").reverse().join(""):a}),void 0==d.valHooks.text||!0!=d.valHooks.text.inputmaskpatch)e=d.valHooks.text&&d.valHooks.text.get?d.valHooks.text.get:function(a){return a.value},g=d.valHooks.text&&d.valHooks.text.set?d.valHooks.text.set:function(a,b){a.value=b;return a},jQuery.extend(d.valHooks,{text:{get:function(a){var b=d(a);if(b.data("_inputmask")){if(b.data("_inputmask").opts.autoUnmask)return b.inputmask("unmaskedvalue");
a=e(a);b=b.data("_inputmask");return a!=b.masksets[b.activeMasksetIndex]._buffer.join("")?a:""}return e(a)},set:function(a,b){var e=d(a),c=g(a,b);e.data("_inputmask")&&e.triggerHandler("setvalue.inputmask");return c},inputmaskpatch:!0}})}function x(a,d,e,c){var h=g();if(!1!==c)for(;!v(a)&&0<=a-1;)a--;for(c=a;c<d&&c<s();c++)if(v(c)){B(h,c);var n=m(c),q=G(h,n);if(q!=O(n))if(n<s()&&!1!==y(c,q,!0)&&f()[p(c)].def==f()[p(n)].def)F(h,c,q,!0);else if(v(c))break}else B(h,c);void 0!=e&&F(h,k(d),e);if(!1==b().greedy){d=
M(h.join("")).split("");h.length=d.length;c=0;for(e=h.length;c<e;c++)h[c]=d[c];0==h.length&&(b().buffer=l().slice())}return a}function J(a,d,e){var c=g();if(G(c,a,!0)!=O(a))for(var h=k(d);h>a&&0<=h;h--)if(v(h)){var m=k(h),n=G(c,m);n!=O(m)&&!1!==y(m,n,!0)&&f()[p(h)].def==f()[p(m)].def&&(F(c,h,n,!0),B(c,m))}else B(c,h);void 0!=e&&G(c,a)==O(a)&&F(c,a,e);a=c.length;if(!1==b().greedy){e=M(c.join("")).split("");c.length=e.length;h=0;for(m=c.length;h<m;h++)c[h]=e[h];0==c.length&&(b().buffer=l().slice())}return d-
(a-c.length)}function L(d,c,f){if(a.numericInput||z){switch(c){case a.keyCode.BACKSPACE:c=a.keyCode.DELETE;break;case a.keyCode.DELETE:c=a.keyCode.BACKSPACE}if(z){var h=f.end;f.end=f.begin;f.begin=h}}h=!0;f.begin==f.end?(h=c==a.keyCode.BACKSPACE?f.begin-1:f.begin,a.isNumeric&&""!=a.radixPoint&&g()[h]==a.radixPoint&&(f.begin=g().length-1==h?f.begin:c==a.keyCode.BACKSPACE?h:m(h),f.end=f.begin),h=!1,c==a.keyCode.BACKSPACE?f.begin--:c==a.keyCode.DELETE&&f.end++):1!=f.end-f.begin||a.insertMode||(h=!1,
c==a.keyCode.BACKSPACE&&f.begin--);D(g(),f.begin,f.end);var k=s();if(!1==a.greedy)x(f.begin,k,void 0,!z&&c==a.keyCode.BACKSPACE&&!h);else{for(var n=f.begin,l=f.begin;l<f.end;l++)if(v(l)||!h)n=x(f.begin,k,void 0,!z&&c==a.keyCode.BACKSPACE&&!h);h||(f.begin=n)}c=m(-1);D(g(),f.begin,f.end,!0);P(d,!1,void 0==e[1]||c>=f.end,g());b().lastValidPosition<c?(b().lastValidPosition=-1,b().p=c):b().p=f.begin}function $(e){T=!1;var c=this,f=d(c),k=e.keyCode,n=u(c);k==a.keyCode.BACKSPACE||k==a.keyCode.DELETE||fa&&
127==k||e.ctrlKey&&88==k?(e.preventDefault(),88==k&&(I=g().join("")),L(c,k,n),h(),A(c,g(),b().p),c._valueGet()==l().join("")&&f.trigger("cleared"),a.showTooltip&&f.prop("title",b().mask)):k==a.keyCode.END||k==a.keyCode.PAGE_DOWN?setTimeout(function(){var d=m(b().lastValidPosition);a.insertMode||d!=s()||e.shiftKey||d--;u(c,e.shiftKey?n.begin:d,d)},0):k==a.keyCode.HOME&&!e.shiftKey||k==a.keyCode.PAGE_UP?u(c,0,e.shiftKey?n.begin:0):k==a.keyCode.ESCAPE||90==k&&e.ctrlKey?(P(c,!0,!1,I.split("")),f.click()):
k!=a.keyCode.INSERT||e.shiftKey||e.ctrlKey?!1!=a.insertMode||e.shiftKey||(k==a.keyCode.RIGHT?setTimeout(function(){var a=u(c);u(c,a.begin)},0):k==a.keyCode.LEFT&&setTimeout(function(){var a=u(c);u(c,a.begin-1)},0)):(a.insertMode=!a.insertMode,u(c,a.insertMode||n.begin!=s()?n.begin:n.begin-1));f=u(c);!0===a.onKeyDown.call(this,e,g(),a)&&u(c,f.begin,f.end);X=-1!=d.inArray(k,a.ignorables)}function aa(f,n,l,p,q,r){if(void 0==l&&T)return!1;T=!0;var t=d(this);f=f||window.event;l=l||f.which||f.charCode||
f.keyCode;if((!f.ctrlKey||!f.altKey)&&(f.ctrlKey||f.metaKey||X)&&!0!==n)return!0;if(l){!0!==n&&46==l&&!1==f.shiftKey&&","==a.radixPoint&&(l=44);var w,v,x=String.fromCharCode(l);n?(l=q?r:b().lastValidPosition+1,w={begin:l,end:l}):w=u(this);r=z?1<w.begin-w.end||1==w.begin-w.end&&a.insertMode:1<w.end-w.begin||1==w.end-w.begin&&a.insertMode;var D=c;r&&(c=D,d.each(e,function(a,d){"object"==typeof d&&(c=a,b().undoBuffer=g().join(""))}),L(this,a.keyCode.DELETE,w),a.insertMode||d.each(e,function(a,d){"object"==
typeof d&&(c=a,J(w.begin,s()),b().lastValidPosition=m(b().lastValidPosition))}),c=D);var B=g().join("").indexOf(a.radixPoint);a.isNumeric&&!0!==n&&-1!=B&&(a.greedy&&w.begin<=B?(w.begin=k(w.begin),w.end=w.begin):x==a.radixPoint&&(w.begin=B,w.end=w.begin));var C=w.begin;l=y(C,x,q);!0===q&&(l=[{activeMasksetIndex:c,result:l}]);var E=-1;d.each(l,function(d,e){c=e.activeMasksetIndex;b().writeOutBuffer=!0;var f=e.result;if(!1!==f){var h=!1,l=g();!0!==f&&(h=f.refresh,C=void 0!=f.pos?f.pos:C,x=void 0!=f.c?
f.c:x);if(!0!==h){if(!0==a.insertMode){f=s();for(l=l.slice();G(l,f,!0)!=O(f)&&f>=C;)f=0==f?-1:k(f);f>=C?(J(C,s(),x),l=b().lastValidPosition,f=m(l),f!=s()&&l>=C&&G(g(),f,!0)!=O(f)&&(b().lastValidPosition=f)):b().writeOutBuffer=!1}else F(l,C,x,!0);if(-1==E||E>m(C))E=m(C)}else!q&&(l=C<s()?C+1:C,-1==E||E>l)&&(E=l);E>b().p&&(b().p=E)}});!0!==q&&(c=D,h());if(!1!==p&&(d.each(l,function(a,b){if(b.activeMasksetIndex==c)return v=b,!1}),void 0!=v)){var K=this;setTimeout(function(){a.onKeyValidation.call(K,v.result,
a)},0);if(b().writeOutBuffer&&!1!==v.result){var I=g();p=n?void 0:a.numericInput?C>B?k(E):x==a.radixPoint?E-1:k(E-1):E;A(this,I,p);!0!==n&&setTimeout(function(){!0===R(I)&&t.trigger("complete");Q=!0;t.trigger("input")},0)}else r&&(b().buffer=b().undoBuffer.split(""))}a.showTooltip&&t.prop("title",b().mask);f.preventDefault()}}function S(b){var e=d(this),c=b.keyCode,f=g();W&&c==a.keyCode.BACKSPACE&&da==this._valueGet()&&$.call(this,b);a.onKeyUp.call(this,b,f,a);c==a.keyCode.TAB&&a.showMaskOnFocus&&
(e.hasClass("focus.inputmask")&&0==this._valueGet().length?(f=l().slice(),A(this,f),u(this,0),I=g().join("")):(A(this,f),f.join("")==l().join("")&&-1!=d.inArray(a.radixPoint,f)?(u(this,K(0)),e.click()):u(this,K(0),K(s()))))}function ba(a){if(!0===Q)return Q=!1,!0;a=d(this);da=g().join("");P(this,!1,!1);A(this,g());!0===R(g())&&a.trigger("complete");a.click()}t=d(n);if(t.is(":input")){t.data("_inputmask",{masksets:e,activeMasksetIndex:c,opts:a,isRTL:!1});a.showTooltip&&t.prop("title",b().mask);b().greedy=
b().greedy?b().greedy:0==b().repeat;if(null!=t.attr("maxLength")){var N=t.prop("maxLength");-1<N&&d.each(e,function(a,b){"object"==typeof b&&"*"==b.repeat&&(b.repeat=N)});s()>=N&&-1<N&&(N<l().length&&(l().length=N),!1==b().greedy&&(b().repeat=Math.round(N/l().length)),t.prop("maxLength",2*s()))}q(n);var T=!1,Q=!1,X=!1;a.numericInput&&(a.isNumeric=a.numericInput);("rtl"==n.dir||a.numericInput&&a.rightAlignNumerics||a.isNumeric&&a.rightAlignNumerics)&&t.css("text-align","right");if("rtl"==n.dir||a.numericInput){n.dir=
"ltr";t.removeAttr("dir");var Y=t.data("_inputmask");Y.isRTL=!0;t.data("_inputmask",Y);z=!0}t.unbind(".inputmask");t.removeClass("focus.inputmask");t.closest("form").bind("submit",function(){I!=g().join("")&&t.change()}).bind("reset",function(){setTimeout(function(){t.trigger("setvalue")},0)});t.bind("mouseenter.inputmask",function(){!d(this).hasClass("focus.inputmask")&&a.showMaskOnHover&&this._valueGet()!=g().join("")&&A(this,g())}).bind("blur.inputmask",function(){var b=d(this),f=this._valueGet(),
h=g();b.removeClass("focus.inputmask");I!=g().join("")&&b.change();a.clearMaskOnLostFocus&&""!=f&&(f==l().join("")?this._valueSet(""):V(this));!1===R(h)&&(b.trigger("incomplete"),a.clearIncomplete&&(d.each(e,function(a,b){"object"==typeof b&&(b.buffer=b._buffer.slice(),b.lastValidPosition=-1)}),c=0,a.clearMaskOnLostFocus?this._valueSet(""):(h=l().slice(),A(this,h))))}).bind("focus.inputmask",function(){var e=d(this),c=this._valueGet();a.showMaskOnFocus&&!e.hasClass("focus.inputmask")&&(!a.showMaskOnHover||
a.showMaskOnHover&&""==c)&&this._valueGet()!=g().join("")&&A(this,g(),m(b().lastValidPosition));e.addClass("focus.inputmask");I=g().join("")}).bind("mouseleave.inputmask",function(){var b=d(this);a.clearMaskOnLostFocus&&(b.hasClass("focus.inputmask")||this._valueGet()==b.attr("placeholder")||(this._valueGet()==l().join("")||""==this._valueGet()?this._valueSet(""):V(this)))}).bind("click.inputmask",function(){var e=this;setTimeout(function(){var c=u(e),f=g();if(c.begin==c.end){var c=z?K(c.begin):c.begin,
h=b().lastValidPosition,f=a.isNumeric?!1===a.skipRadixDance&&""!=a.radixPoint&&-1!=d.inArray(a.radixPoint,f)?a.numericInput?m(d.inArray(a.radixPoint,f)):d.inArray(a.radixPoint,f):m(h):m(h);c<f?v(c)?u(e,c):u(e,m(c)):u(e,f)}},0)}).bind("dblclick.inputmask",function(){var a=this;setTimeout(function(){u(a,0,m(b().lastValidPosition))},0)}).bind(ga+".inputmask dragdrop.inputmask drop.inputmask",function(b){if(!0===Q)return Q=!1,!0;var c=this,e=d(c);if("propertychange"==b.type&&c._valueGet().length<=s())return!0;
setTimeout(function(){var b=void 0!=a.onBeforePaste?a.onBeforePaste.call(this,c._valueGet()):c._valueGet();P(c,!0,!1,b.split(""),!0);!0===R(g())&&e.trigger("complete");e.click()},0)}).bind("setvalue.inputmask",function(){P(this,!0);I=g().join("");this._valueGet()==l().join("")&&this._valueSet("")}).bind("_keypress.inputmask",aa).bind("complete.inputmask",a.oncomplete).bind("incomplete.inputmask",a.onincomplete).bind("cleared.inputmask",a.oncleared).bind("keyup.inputmask",S);W?t.bind("input.inputmask",
ba):t.bind("keydown.inputmask",$).bind("keypress.inputmask",aa);ea&&t.bind("input.inputmask",ba);P(n,!0,!1);I=g().join("");var Z;try{Z=document.activeElement}catch(ca){}Z===n?(t.addClass("focus.inputmask"),u(n,m(b().lastValidPosition))):a.clearMaskOnLostFocus?g().join("")==l().join("")?n._valueSet(""):V(n):A(n,g());r(n)}}var z=!1,I=g().join(""),t,da;return{isComplete:function(a){return R(a)},unmaskedvalue:function(a,b){z=a.data("_inputmask").isRTL;return L(a,b)},mask:function(a){ca(a)}}};d.inputmask=
{defaults:{placeholder:"_",optionalmarker:{start:"[",end:"]"},quantifiermarker:{start:"{",end:"}"},groupmarker:{start:"(",end:")"},escapeChar:"\\",mask:null,oncomplete:d.noop,onincomplete:d.noop,oncleared:d.noop,repeat:0,greedy:!0,autoUnmask:!1,clearMaskOnLostFocus:!0,insertMode:!0,clearIncomplete:!1,aliases:{},onKeyUp:d.noop,onKeyDown:d.noop,onBeforePaste:void 0,onUnMask:void 0,showMaskOnFocus:!0,showMaskOnHover:!0,onKeyValidation:d.noop,skipOptionalPartCharacter:" ",showTooltip:!1,numericInput:!1,
isNumeric:!1,radixPoint:"",skipRadixDance:!1,rightAlignNumerics:!0,definitions:{9:{validator:"[0-9]",cardinality:1},a:{validator:"[A-Za-z\u0410-\u044f\u0401\u0451]",cardinality:1},"*":{validator:"[A-Za-z\u0410-\u044f\u0401\u04510-9]",cardinality:1}},keyCode:{ALT:18,BACKSPACE:8,CAPS_LOCK:20,COMMA:188,COMMAND:91,COMMAND_LEFT:91,COMMAND_RIGHT:93,CONTROL:17,DELETE:46,DOWN:40,END:35,ENTER:13,ESCAPE:27,HOME:36,INSERT:45,LEFT:37,MENU:93,NUMPAD_ADD:107,NUMPAD_DECIMAL:110,NUMPAD_DIVIDE:111,NUMPAD_ENTER:108,
NUMPAD_MULTIPLY:106,NUMPAD_SUBTRACT:109,PAGE_DOWN:34,PAGE_UP:33,PERIOD:190,RIGHT:39,SHIFT:16,SPACE:32,TAB:9,UP:38,WINDOWS:91},ignorables:[8,9,13,19,27,33,34,35,36,37,38,39,40,45,46,93,112,113,114,115,116,117,118,119,120,121,122,123],getMaskLength:function(e,c,a,b,d){d=e.length;c||("*"==a?d=b.length+1:1<a&&(d+=e.length*(a-1)));return d}},escapeRegex:function(d){return d.replace(RegExp("(\\/|\\.|\\*|\\+|\\?|\\||\\(|\\)|\\[|\\]|\\{|\\}|\\\\)","gim"),"\\$1")},format:function(d,c){}};d.fn.inputmask=function(e,
c){var a=d.extend(!0,{},d.inputmask.defaults,c),b,f=0;if("string"===typeof e)switch(e){case "mask":return J(a.alias,c,a),b=M(a),0==b.length?this:this.each(function(){x(d.extend(!0,{},b),0,a).mask(this)});case "unmaskedvalue":var l=d(this);return l.data("_inputmask")?(b=l.data("_inputmask").masksets,f=l.data("_inputmask").activeMasksetIndex,a=l.data("_inputmask").opts,x(b,f,a).unmaskedvalue(l)):l.val();case "remove":return this.each(function(){var c=d(this);if(c.data("_inputmask")){b=c.data("_inputmask").masksets;
f=c.data("_inputmask").activeMasksetIndex;a=c.data("_inputmask").opts;this._valueSet(x(b,f,a).unmaskedvalue(c,!0));c.removeData("_inputmask");c.unbind(".inputmask");c.removeClass("focus.inputmask");var e;Object.getOwnPropertyDescriptor&&(e=Object.getOwnPropertyDescriptor(this,"value"));e&&e.get?this._valueGet&&Object.defineProperty(this,"value",{get:this._valueGet,set:this._valueSet}):document.__lookupGetter__&&this.__lookupGetter__("value")&&this._valueGet&&(this.__defineGetter__("value",this._valueGet),
this.__defineSetter__("value",this._valueSet));try{delete this._valueGet,delete this._valueSet}catch(h){this._valueSet=this._valueGet=void 0}}});case "getemptymask":return this.data("_inputmask")?(b=this.data("_inputmask").masksets,f=this.data("_inputmask").activeMasksetIndex,b[f]._buffer.join("")):"";case "hasMaskedValue":return this.data("_inputmask")?!this.data("_inputmask").opts.autoUnmask:!1;case "isComplete":return b=this.data("_inputmask").masksets,f=this.data("_inputmask").activeMasksetIndex,
a=this.data("_inputmask").opts,x(b,f,a).isComplete(this[0]._valueGet().split(""));case "getmetadata":if(this.data("_inputmask"))return b=this.data("_inputmask").masksets,f=this.data("_inputmask").activeMasksetIndex,b[f].metadata;break;default:return J(e,c,a)||(a.mask=e),b=M(a),0==b.length?this:this.each(function(){x(d.extend(!0,{},b),f,a).mask(this)})}else{if("object"==typeof e)return a=d.extend(!0,{},d.inputmask.defaults,e),J(a.alias,e,a),b=M(a),0==b.length?this:this.each(function(){x(d.extend(!0,
{},b),f,a).mask(this)});if(void 0==e)return this.each(function(){var b=d(this).attr("data-inputmask");if(b&&""!=b)try{var b=b.replace(RegExp("'","g"),'"'),e=d.parseJSON("{"+b+"}");d.extend(!0,e,c);a=d.extend(!0,{},d.inputmask.defaults,e);J(a.alias,e,a);a.alias=void 0;d(this).inputmask(a)}catch(f){}})}}}})(jQuery);