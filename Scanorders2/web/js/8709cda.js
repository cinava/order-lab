/*! jQuery v1.11.0 | (c) 2005, 2014 jQuery Foundation, Inc. | jquery.org/license */
!function(a,b){"object"==typeof module&&"object"==typeof module.exports?module.exports=a.document?b(a,!0):function(a){if(!a.document)throw new Error("jQuery requires a window with a document");return b(a)}:b(a)}("undefined"!=typeof window?window:this,function(a,b){var c=[],d=c.slice,e=c.concat,f=c.push,g=c.indexOf,h={},i=h.toString,j=h.hasOwnProperty,k="".trim,l={},m="1.11.0",n=function(a,b){return new n.fn.init(a,b)},o=/^[\s\uFEFF\xA0]+|[\s\uFEFF\xA0]+$/g,p=/^-ms-/,q=/-([\da-z])/gi,r=function(a,b){return b.toUpperCase()};n.fn=n.prototype={jquery:m,constructor:n,selector:"",length:0,toArray:function(){return d.call(this)},get:function(a){return null!=a?0>a?this[a+this.length]:this[a]:d.call(this)},pushStack:function(a){var b=n.merge(this.constructor(),a);return b.prevObject=this,b.context=this.context,b},each:function(a,b){return n.each(this,a,b)},map:function(a){return this.pushStack(n.map(this,function(b,c){return a.call(b,c,b)}))},slice:function(){return this.pushStack(d.apply(this,arguments))},first:function(){return this.eq(0)},last:function(){return this.eq(-1)},eq:function(a){var b=this.length,c=+a+(0>a?b:0);return this.pushStack(c>=0&&b>c?[this[c]]:[])},end:function(){return this.prevObject||this.constructor(null)},push:f,sort:c.sort,splice:c.splice},n.extend=n.fn.extend=function(){var a,b,c,d,e,f,g=arguments[0]||{},h=1,i=arguments.length,j=!1;for("boolean"==typeof g&&(j=g,g=arguments[h]||{},h++),"object"==typeof g||n.isFunction(g)||(g={}),h===i&&(g=this,h--);i>h;h++)if(null!=(e=arguments[h]))for(d in e)a=g[d],c=e[d],g!==c&&(j&&c&&(n.isPlainObject(c)||(b=n.isArray(c)))?(b?(b=!1,f=a&&n.isArray(a)?a:[]):f=a&&n.isPlainObject(a)?a:{},g[d]=n.extend(j,f,c)):void 0!==c&&(g[d]=c));return g},n.extend({expando:"jQuery"+(m+Math.random()).replace(/\D/g,""),isReady:!0,error:function(a){throw new Error(a)},noop:function(){},isFunction:function(a){return"function"===n.type(a)},isArray:Array.isArray||function(a){return"array"===n.type(a)},isWindow:function(a){return null!=a&&a==a.window},isNumeric:function(a){return a-parseFloat(a)>=0},isEmptyObject:function(a){var b;for(b in a)return!1;return!0},isPlainObject:function(a){var b;if(!a||"object"!==n.type(a)||a.nodeType||n.isWindow(a))return!1;try{if(a.constructor&&!j.call(a,"constructor")&&!j.call(a.constructor.prototype,"isPrototypeOf"))return!1}catch(c){return!1}if(l.ownLast)for(b in a)return j.call(a,b);for(b in a);return void 0===b||j.call(a,b)},type:function(a){return null==a?a+"":"object"==typeof a||"function"==typeof a?h[i.call(a)]||"object":typeof a},globalEval:function(b){b&&n.trim(b)&&(a.execScript||function(b){a.eval.call(a,b)})(b)},camelCase:function(a){return a.replace(p,"ms-").replace(q,r)},nodeName:function(a,b){return a.nodeName&&a.nodeName.toLowerCase()===b.toLowerCase()},each:function(a,b,c){var d,e=0,f=a.length,g=s(a);if(c){if(g){for(;f>e;e++)if(d=b.apply(a[e],c),d===!1)break}else for(e in a)if(d=b.apply(a[e],c),d===!1)break}else if(g){for(;f>e;e++)if(d=b.call(a[e],e,a[e]),d===!1)break}else for(e in a)if(d=b.call(a[e],e,a[e]),d===!1)break;return a},trim:k&&!k.call("\ufeff\xa0")?function(a){return null==a?"":k.call(a)}:function(a){return null==a?"":(a+"").replace(o,"")},makeArray:function(a,b){var c=b||[];return null!=a&&(s(Object(a))?n.merge(c,"string"==typeof a?[a]:a):f.call(c,a)),c},inArray:function(a,b,c){var d;if(b){if(g)return g.call(b,a,c);for(d=b.length,c=c?0>c?Math.max(0,d+c):c:0;d>c;c++)if(c in b&&b[c]===a)return c}return-1},merge:function(a,b){var c=+b.length,d=0,e=a.length;while(c>d)a[e++]=b[d++];if(c!==c)while(void 0!==b[d])a[e++]=b[d++];return a.length=e,a},grep:function(a,b,c){for(var d,e=[],f=0,g=a.length,h=!c;g>f;f++)d=!b(a[f],f),d!==h&&e.push(a[f]);return e},map:function(a,b,c){var d,f=0,g=a.length,h=s(a),i=[];if(h)for(;g>f;f++)d=b(a[f],f,c),null!=d&&i.push(d);else for(f in a)d=b(a[f],f,c),null!=d&&i.push(d);return e.apply([],i)},guid:1,proxy:function(a,b){var c,e,f;return"string"==typeof b&&(f=a[b],b=a,a=f),n.isFunction(a)?(c=d.call(arguments,2),e=function(){return a.apply(b||this,c.concat(d.call(arguments)))},e.guid=a.guid=a.guid||n.guid++,e):void 0},now:function(){return+new Date},support:l}),n.each("Boolean Number String Function Array Date RegExp Object Error".split(" "),function(a,b){h["[object "+b+"]"]=b.toLowerCase()});function s(a){var b=a.length,c=n.type(a);return"function"===c||n.isWindow(a)?!1:1===a.nodeType&&b?!0:"array"===c||0===b||"number"==typeof b&&b>0&&b-1 in a}var t=function(a){var b,c,d,e,f,g,h,i,j,k,l,m,n,o,p,q,r,s="sizzle"+-new Date,t=a.document,u=0,v=0,w=eb(),x=eb(),y=eb(),z=function(a,b){return a===b&&(j=!0),0},A="undefined",B=1<<31,C={}.hasOwnProperty,D=[],E=D.pop,F=D.push,G=D.push,H=D.slice,I=D.indexOf||function(a){for(var b=0,c=this.length;c>b;b++)if(this[b]===a)return b;return-1},J="checked|selected|async|autofocus|autoplay|controls|defer|disabled|hidden|ismap|loop|multiple|open|readonly|required|scoped",K="[\\x20\\t\\r\\n\\f]",L="(?:\\\\.|[\\w-]|[^\\x00-\\xa0])+",M=L.replace("w","w#"),N="\\["+K+"*("+L+")"+K+"*(?:([*^$|!~]?=)"+K+"*(?:(['\"])((?:\\\\.|[^\\\\])*?)\\3|("+M+")|)|)"+K+"*\\]",O=":("+L+")(?:\\(((['\"])((?:\\\\.|[^\\\\])*?)\\3|((?:\\\\.|[^\\\\()[\\]]|"+N.replace(3,8)+")*)|.*)\\)|)",P=new RegExp("^"+K+"+|((?:^|[^\\\\])(?:\\\\.)*)"+K+"+$","g"),Q=new RegExp("^"+K+"*,"+K+"*"),R=new RegExp("^"+K+"*([>+~]|"+K+")"+K+"*"),S=new RegExp("="+K+"*([^\\]'\"]*?)"+K+"*\\]","g"),T=new RegExp(O),U=new RegExp("^"+M+"$"),V={ID:new RegExp("^#("+L+")"),CLASS:new RegExp("^\\.("+L+")"),TAG:new RegExp("^("+L.replace("w","w*")+")"),ATTR:new RegExp("^"+N),PSEUDO:new RegExp("^"+O),CHILD:new RegExp("^:(only|first|last|nth|nth-last)-(child|of-type)(?:\\("+K+"*(even|odd|(([+-]|)(\\d*)n|)"+K+"*(?:([+-]|)"+K+"*(\\d+)|))"+K+"*\\)|)","i"),bool:new RegExp("^(?:"+J+")$","i"),needsContext:new RegExp("^"+K+"*[>+~]|:(even|odd|eq|gt|lt|nth|first|last)(?:\\("+K+"*((?:-\\d)?\\d*)"+K+"*\\)|)(?=[^-]|$)","i")},W=/^(?:input|select|textarea|button)$/i,X=/^h\d$/i,Y=/^[^{]+\{\s*\[native \w/,Z=/^(?:#([\w-]+)|(\w+)|\.([\w-]+))$/,$=/[+~]/,_=/'|\\/g,ab=new RegExp("\\\\([\\da-f]{1,6}"+K+"?|("+K+")|.)","ig"),bb=function(a,b,c){var d="0x"+b-65536;return d!==d||c?b:0>d?String.fromCharCode(d+65536):String.fromCharCode(d>>10|55296,1023&d|56320)};try{G.apply(D=H.call(t.childNodes),t.childNodes),D[t.childNodes.length].nodeType}catch(cb){G={apply:D.length?function(a,b){F.apply(a,H.call(b))}:function(a,b){var c=a.length,d=0;while(a[c++]=b[d++]);a.length=c-1}}}function db(a,b,d,e){var f,g,h,i,j,m,p,q,u,v;if((b?b.ownerDocument||b:t)!==l&&k(b),b=b||l,d=d||[],!a||"string"!=typeof a)return d;if(1!==(i=b.nodeType)&&9!==i)return[];if(n&&!e){if(f=Z.exec(a))if(h=f[1]){if(9===i){if(g=b.getElementById(h),!g||!g.parentNode)return d;if(g.id===h)return d.push(g),d}else if(b.ownerDocument&&(g=b.ownerDocument.getElementById(h))&&r(b,g)&&g.id===h)return d.push(g),d}else{if(f[2])return G.apply(d,b.getElementsByTagName(a)),d;if((h=f[3])&&c.getElementsByClassName&&b.getElementsByClassName)return G.apply(d,b.getElementsByClassName(h)),d}if(c.qsa&&(!o||!o.test(a))){if(q=p=s,u=b,v=9===i&&a,1===i&&"object"!==b.nodeName.toLowerCase()){m=ob(a),(p=b.getAttribute("id"))?q=p.replace(_,"\\$&"):b.setAttribute("id",q),q="[id='"+q+"'] ",j=m.length;while(j--)m[j]=q+pb(m[j]);u=$.test(a)&&mb(b.parentNode)||b,v=m.join(",")}if(v)try{return G.apply(d,u.querySelectorAll(v)),d}catch(w){}finally{p||b.removeAttribute("id")}}}return xb(a.replace(P,"$1"),b,d,e)}function eb(){var a=[];function b(c,e){return a.push(c+" ")>d.cacheLength&&delete b[a.shift()],b[c+" "]=e}return b}function fb(a){return a[s]=!0,a}function gb(a){var b=l.createElement("div");try{return!!a(b)}catch(c){return!1}finally{b.parentNode&&b.parentNode.removeChild(b),b=null}}function hb(a,b){var c=a.split("|"),e=a.length;while(e--)d.attrHandle[c[e]]=b}function ib(a,b){var c=b&&a,d=c&&1===a.nodeType&&1===b.nodeType&&(~b.sourceIndex||B)-(~a.sourceIndex||B);if(d)return d;if(c)while(c=c.nextSibling)if(c===b)return-1;return a?1:-1}function jb(a){return function(b){var c=b.nodeName.toLowerCase();return"input"===c&&b.type===a}}function kb(a){return function(b){var c=b.nodeName.toLowerCase();return("input"===c||"button"===c)&&b.type===a}}function lb(a){return fb(function(b){return b=+b,fb(function(c,d){var e,f=a([],c.length,b),g=f.length;while(g--)c[e=f[g]]&&(c[e]=!(d[e]=c[e]))})})}function mb(a){return a&&typeof a.getElementsByTagName!==A&&a}c=db.support={},f=db.isXML=function(a){var b=a&&(a.ownerDocument||a).documentElement;return b?"HTML"!==b.nodeName:!1},k=db.setDocument=function(a){var b,e=a?a.ownerDocument||a:t,g=e.defaultView;return e!==l&&9===e.nodeType&&e.documentElement?(l=e,m=e.documentElement,n=!f(e),g&&g!==g.top&&(g.addEventListener?g.addEventListener("unload",function(){k()},!1):g.attachEvent&&g.attachEvent("onunload",function(){k()})),c.attributes=gb(function(a){return a.className="i",!a.getAttribute("className")}),c.getElementsByTagName=gb(function(a){return a.appendChild(e.createComment("")),!a.getElementsByTagName("*").length}),c.getElementsByClassName=Y.test(e.getElementsByClassName)&&gb(function(a){return a.innerHTML="<div class='a'></div><div class='a i'></div>",a.firstChild.className="i",2===a.getElementsByClassName("i").length}),c.getById=gb(function(a){return m.appendChild(a).id=s,!e.getElementsByName||!e.getElementsByName(s).length}),c.getById?(d.find.ID=function(a,b){if(typeof b.getElementById!==A&&n){var c=b.getElementById(a);return c&&c.parentNode?[c]:[]}},d.filter.ID=function(a){var b=a.replace(ab,bb);return function(a){return a.getAttribute("id")===b}}):(delete d.find.ID,d.filter.ID=function(a){var b=a.replace(ab,bb);return function(a){var c=typeof a.getAttributeNode!==A&&a.getAttributeNode("id");return c&&c.value===b}}),d.find.TAG=c.getElementsByTagName?function(a,b){return typeof b.getElementsByTagName!==A?b.getElementsByTagName(a):void 0}:function(a,b){var c,d=[],e=0,f=b.getElementsByTagName(a);if("*"===a){while(c=f[e++])1===c.nodeType&&d.push(c);return d}return f},d.find.CLASS=c.getElementsByClassName&&function(a,b){return typeof b.getElementsByClassName!==A&&n?b.getElementsByClassName(a):void 0},p=[],o=[],(c.qsa=Y.test(e.querySelectorAll))&&(gb(function(a){a.innerHTML="<select t=''><option selected=''></option></select>",a.querySelectorAll("[t^='']").length&&o.push("[*^$]="+K+"*(?:''|\"\")"),a.querySelectorAll("[selected]").length||o.push("\\["+K+"*(?:value|"+J+")"),a.querySelectorAll(":checked").length||o.push(":checked")}),gb(function(a){var b=e.createElement("input");b.setAttribute("type","hidden"),a.appendChild(b).setAttribute("name","D"),a.querySelectorAll("[name=d]").length&&o.push("name"+K+"*[*^$|!~]?="),a.querySelectorAll(":enabled").length||o.push(":enabled",":disabled"),a.querySelectorAll("*,:x"),o.push(",.*:")})),(c.matchesSelector=Y.test(q=m.webkitMatchesSelector||m.mozMatchesSelector||m.oMatchesSelector||m.msMatchesSelector))&&gb(function(a){c.disconnectedMatch=q.call(a,"div"),q.call(a,"[s!='']:x"),p.push("!=",O)}),o=o.length&&new RegExp(o.join("|")),p=p.length&&new RegExp(p.join("|")),b=Y.test(m.compareDocumentPosition),r=b||Y.test(m.contains)?function(a,b){var c=9===a.nodeType?a.documentElement:a,d=b&&b.parentNode;return a===d||!(!d||1!==d.nodeType||!(c.contains?c.contains(d):a.compareDocumentPosition&&16&a.compareDocumentPosition(d)))}:function(a,b){if(b)while(b=b.parentNode)if(b===a)return!0;return!1},z=b?function(a,b){if(a===b)return j=!0,0;var d=!a.compareDocumentPosition-!b.compareDocumentPosition;return d?d:(d=(a.ownerDocument||a)===(b.ownerDocument||b)?a.compareDocumentPosition(b):1,1&d||!c.sortDetached&&b.compareDocumentPosition(a)===d?a===e||a.ownerDocument===t&&r(t,a)?-1:b===e||b.ownerDocument===t&&r(t,b)?1:i?I.call(i,a)-I.call(i,b):0:4&d?-1:1)}:function(a,b){if(a===b)return j=!0,0;var c,d=0,f=a.parentNode,g=b.parentNode,h=[a],k=[b];if(!f||!g)return a===e?-1:b===e?1:f?-1:g?1:i?I.call(i,a)-I.call(i,b):0;if(f===g)return ib(a,b);c=a;while(c=c.parentNode)h.unshift(c);c=b;while(c=c.parentNode)k.unshift(c);while(h[d]===k[d])d++;return d?ib(h[d],k[d]):h[d]===t?-1:k[d]===t?1:0},e):l},db.matches=function(a,b){return db(a,null,null,b)},db.matchesSelector=function(a,b){if((a.ownerDocument||a)!==l&&k(a),b=b.replace(S,"='$1']"),!(!c.matchesSelector||!n||p&&p.test(b)||o&&o.test(b)))try{var d=q.call(a,b);if(d||c.disconnectedMatch||a.document&&11!==a.document.nodeType)return d}catch(e){}return db(b,l,null,[a]).length>0},db.contains=function(a,b){return(a.ownerDocument||a)!==l&&k(a),r(a,b)},db.attr=function(a,b){(a.ownerDocument||a)!==l&&k(a);var e=d.attrHandle[b.toLowerCase()],f=e&&C.call(d.attrHandle,b.toLowerCase())?e(a,b,!n):void 0;return void 0!==f?f:c.attributes||!n?a.getAttribute(b):(f=a.getAttributeNode(b))&&f.specified?f.value:null},db.error=function(a){throw new Error("Syntax error, unrecognized expression: "+a)},db.uniqueSort=function(a){var b,d=[],e=0,f=0;if(j=!c.detectDuplicates,i=!c.sortStable&&a.slice(0),a.sort(z),j){while(b=a[f++])b===a[f]&&(e=d.push(f));while(e--)a.splice(d[e],1)}return i=null,a},e=db.getText=function(a){var b,c="",d=0,f=a.nodeType;if(f){if(1===f||9===f||11===f){if("string"==typeof a.textContent)return a.textContent;for(a=a.firstChild;a;a=a.nextSibling)c+=e(a)}else if(3===f||4===f)return a.nodeValue}else while(b=a[d++])c+=e(b);return c},d=db.selectors={cacheLength:50,createPseudo:fb,match:V,attrHandle:{},find:{},relative:{">":{dir:"parentNode",first:!0}," ":{dir:"parentNode"},"+":{dir:"previousSibling",first:!0},"~":{dir:"previousSibling"}},preFilter:{ATTR:function(a){return a[1]=a[1].replace(ab,bb),a[3]=(a[4]||a[5]||"").replace(ab,bb),"~="===a[2]&&(a[3]=" "+a[3]+" "),a.slice(0,4)},CHILD:function(a){return a[1]=a[1].toLowerCase(),"nth"===a[1].slice(0,3)?(a[3]||db.error(a[0]),a[4]=+(a[4]?a[5]+(a[6]||1):2*("even"===a[3]||"odd"===a[3])),a[5]=+(a[7]+a[8]||"odd"===a[3])):a[3]&&db.error(a[0]),a},PSEUDO:function(a){var b,c=!a[5]&&a[2];return V.CHILD.test(a[0])?null:(a[3]&&void 0!==a[4]?a[2]=a[4]:c&&T.test(c)&&(b=ob(c,!0))&&(b=c.indexOf(")",c.length-b)-c.length)&&(a[0]=a[0].slice(0,b),a[2]=c.slice(0,b)),a.slice(0,3))}},filter:{TAG:function(a){var b=a.replace(ab,bb).toLowerCase();return"*"===a?function(){return!0}:function(a){return a.nodeName&&a.nodeName.toLowerCase()===b}},CLASS:function(a){var b=w[a+" "];return b||(b=new RegExp("(^|"+K+")"+a+"("+K+"|$)"))&&w(a,function(a){return b.test("string"==typeof a.className&&a.className||typeof a.getAttribute!==A&&a.getAttribute("class")||"")})},ATTR:function(a,b,c){return function(d){var e=db.attr(d,a);return null==e?"!="===b:b?(e+="","="===b?e===c:"!="===b?e!==c:"^="===b?c&&0===e.indexOf(c):"*="===b?c&&e.indexOf(c)>-1:"$="===b?c&&e.slice(-c.length)===c:"~="===b?(" "+e+" ").indexOf(c)>-1:"|="===b?e===c||e.slice(0,c.length+1)===c+"-":!1):!0}},CHILD:function(a,b,c,d,e){var f="nth"!==a.slice(0,3),g="last"!==a.slice(-4),h="of-type"===b;return 1===d&&0===e?function(a){return!!a.parentNode}:function(b,c,i){var j,k,l,m,n,o,p=f!==g?"nextSibling":"previousSibling",q=b.parentNode,r=h&&b.nodeName.toLowerCase(),t=!i&&!h;if(q){if(f){while(p){l=b;while(l=l[p])if(h?l.nodeName.toLowerCase()===r:1===l.nodeType)return!1;o=p="only"===a&&!o&&"nextSibling"}return!0}if(o=[g?q.firstChild:q.lastChild],g&&t){k=q[s]||(q[s]={}),j=k[a]||[],n=j[0]===u&&j[1],m=j[0]===u&&j[2],l=n&&q.childNodes[n];while(l=++n&&l&&l[p]||(m=n=0)||o.pop())if(1===l.nodeType&&++m&&l===b){k[a]=[u,n,m];break}}else if(t&&(j=(b[s]||(b[s]={}))[a])&&j[0]===u)m=j[1];else while(l=++n&&l&&l[p]||(m=n=0)||o.pop())if((h?l.nodeName.toLowerCase()===r:1===l.nodeType)&&++m&&(t&&((l[s]||(l[s]={}))[a]=[u,m]),l===b))break;return m-=e,m===d||m%d===0&&m/d>=0}}},PSEUDO:function(a,b){var c,e=d.pseudos[a]||d.setFilters[a.toLowerCase()]||db.error("unsupported pseudo: "+a);return e[s]?e(b):e.length>1?(c=[a,a,"",b],d.setFilters.hasOwnProperty(a.toLowerCase())?fb(function(a,c){var d,f=e(a,b),g=f.length;while(g--)d=I.call(a,f[g]),a[d]=!(c[d]=f[g])}):function(a){return e(a,0,c)}):e}},pseudos:{not:fb(function(a){var b=[],c=[],d=g(a.replace(P,"$1"));return d[s]?fb(function(a,b,c,e){var f,g=d(a,null,e,[]),h=a.length;while(h--)(f=g[h])&&(a[h]=!(b[h]=f))}):function(a,e,f){return b[0]=a,d(b,null,f,c),!c.pop()}}),has:fb(function(a){return function(b){return db(a,b).length>0}}),contains:fb(function(a){return function(b){return(b.textContent||b.innerText||e(b)).indexOf(a)>-1}}),lang:fb(function(a){return U.test(a||"")||db.error("unsupported lang: "+a),a=a.replace(ab,bb).toLowerCase(),function(b){var c;do if(c=n?b.lang:b.getAttribute("xml:lang")||b.getAttribute("lang"))return c=c.toLowerCase(),c===a||0===c.indexOf(a+"-");while((b=b.parentNode)&&1===b.nodeType);return!1}}),target:function(b){var c=a.location&&a.location.hash;return c&&c.slice(1)===b.id},root:function(a){return a===m},focus:function(a){return a===l.activeElement&&(!l.hasFocus||l.hasFocus())&&!!(a.type||a.href||~a.tabIndex)},enabled:function(a){return a.disabled===!1},disabled:function(a){return a.disabled===!0},checked:function(a){var b=a.nodeName.toLowerCase();return"input"===b&&!!a.checked||"option"===b&&!!a.selected},selected:function(a){return a.parentNode&&a.parentNode.selectedIndex,a.selected===!0},empty:function(a){for(a=a.firstChild;a;a=a.nextSibling)if(a.nodeType<6)return!1;return!0},parent:function(a){return!d.pseudos.empty(a)},header:function(a){return X.test(a.nodeName)},input:function(a){return W.test(a.nodeName)},button:function(a){var b=a.nodeName.toLowerCase();return"input"===b&&"button"===a.type||"button"===b},text:function(a){var b;return"input"===a.nodeName.toLowerCase()&&"text"===a.type&&(null==(b=a.getAttribute("type"))||"text"===b.toLowerCase())},first:lb(function(){return[0]}),last:lb(function(a,b){return[b-1]}),eq:lb(function(a,b,c){return[0>c?c+b:c]}),even:lb(function(a,b){for(var c=0;b>c;c+=2)a.push(c);return a}),odd:lb(function(a,b){for(var c=1;b>c;c+=2)a.push(c);return a}),lt:lb(function(a,b,c){for(var d=0>c?c+b:c;--d>=0;)a.push(d);return a}),gt:lb(function(a,b,c){for(var d=0>c?c+b:c;++d<b;)a.push(d);return a})}},d.pseudos.nth=d.pseudos.eq;for(b in{radio:!0,checkbox:!0,file:!0,password:!0,image:!0})d.pseudos[b]=jb(b);for(b in{submit:!0,reset:!0})d.pseudos[b]=kb(b);function nb(){}nb.prototype=d.filters=d.pseudos,d.setFilters=new nb;function ob(a,b){var c,e,f,g,h,i,j,k=x[a+" "];if(k)return b?0:k.slice(0);h=a,i=[],j=d.preFilter;while(h){(!c||(e=Q.exec(h)))&&(e&&(h=h.slice(e[0].length)||h),i.push(f=[])),c=!1,(e=R.exec(h))&&(c=e.shift(),f.push({value:c,type:e[0].replace(P," ")}),h=h.slice(c.length));for(g in d.filter)!(e=V[g].exec(h))||j[g]&&!(e=j[g](e))||(c=e.shift(),f.push({value:c,type:g,matches:e}),h=h.slice(c.length));if(!c)break}return b?h.length:h?db.error(a):x(a,i).slice(0)}function pb(a){for(var b=0,c=a.length,d="";c>b;b++)d+=a[b].value;return d}function qb(a,b,c){var d=b.dir,e=c&&"parentNode"===d,f=v++;return b.first?function(b,c,f){while(b=b[d])if(1===b.nodeType||e)return a(b,c,f)}:function(b,c,g){var h,i,j=[u,f];if(g){while(b=b[d])if((1===b.nodeType||e)&&a(b,c,g))return!0}else while(b=b[d])if(1===b.nodeType||e){if(i=b[s]||(b[s]={}),(h=i[d])&&h[0]===u&&h[1]===f)return j[2]=h[2];if(i[d]=j,j[2]=a(b,c,g))return!0}}}function rb(a){return a.length>1?function(b,c,d){var e=a.length;while(e--)if(!a[e](b,c,d))return!1;return!0}:a[0]}function sb(a,b,c,d,e){for(var f,g=[],h=0,i=a.length,j=null!=b;i>h;h++)(f=a[h])&&(!c||c(f,d,e))&&(g.push(f),j&&b.push(h));return g}function tb(a,b,c,d,e,f){return d&&!d[s]&&(d=tb(d)),e&&!e[s]&&(e=tb(e,f)),fb(function(f,g,h,i){var j,k,l,m=[],n=[],o=g.length,p=f||wb(b||"*",h.nodeType?[h]:h,[]),q=!a||!f&&b?p:sb(p,m,a,h,i),r=c?e||(f?a:o||d)?[]:g:q;if(c&&c(q,r,h,i),d){j=sb(r,n),d(j,[],h,i),k=j.length;while(k--)(l=j[k])&&(r[n[k]]=!(q[n[k]]=l))}if(f){if(e||a){if(e){j=[],k=r.length;while(k--)(l=r[k])&&j.push(q[k]=l);e(null,r=[],j,i)}k=r.length;while(k--)(l=r[k])&&(j=e?I.call(f,l):m[k])>-1&&(f[j]=!(g[j]=l))}}else r=sb(r===g?r.splice(o,r.length):r),e?e(null,g,r,i):G.apply(g,r)})}function ub(a){for(var b,c,e,f=a.length,g=d.relative[a[0].type],i=g||d.relative[" "],j=g?1:0,k=qb(function(a){return a===b},i,!0),l=qb(function(a){return I.call(b,a)>-1},i,!0),m=[function(a,c,d){return!g&&(d||c!==h)||((b=c).nodeType?k(a,c,d):l(a,c,d))}];f>j;j++)if(c=d.relative[a[j].type])m=[qb(rb(m),c)];else{if(c=d.filter[a[j].type].apply(null,a[j].matches),c[s]){for(e=++j;f>e;e++)if(d.relative[a[e].type])break;return tb(j>1&&rb(m),j>1&&pb(a.slice(0,j-1).concat({value:" "===a[j-2].type?"*":""})).replace(P,"$1"),c,e>j&&ub(a.slice(j,e)),f>e&&ub(a=a.slice(e)),f>e&&pb(a))}m.push(c)}return rb(m)}function vb(a,b){var c=b.length>0,e=a.length>0,f=function(f,g,i,j,k){var m,n,o,p=0,q="0",r=f&&[],s=[],t=h,v=f||e&&d.find.TAG("*",k),w=u+=null==t?1:Math.random()||.1,x=v.length;for(k&&(h=g!==l&&g);q!==x&&null!=(m=v[q]);q++){if(e&&m){n=0;while(o=a[n++])if(o(m,g,i)){j.push(m);break}k&&(u=w)}c&&((m=!o&&m)&&p--,f&&r.push(m))}if(p+=q,c&&q!==p){n=0;while(o=b[n++])o(r,s,g,i);if(f){if(p>0)while(q--)r[q]||s[q]||(s[q]=E.call(j));s=sb(s)}G.apply(j,s),k&&!f&&s.length>0&&p+b.length>1&&db.uniqueSort(j)}return k&&(u=w,h=t),r};return c?fb(f):f}g=db.compile=function(a,b){var c,d=[],e=[],f=y[a+" "];if(!f){b||(b=ob(a)),c=b.length;while(c--)f=ub(b[c]),f[s]?d.push(f):e.push(f);f=y(a,vb(e,d))}return f};function wb(a,b,c){for(var d=0,e=b.length;e>d;d++)db(a,b[d],c);return c}function xb(a,b,e,f){var h,i,j,k,l,m=ob(a);if(!f&&1===m.length){if(i=m[0]=m[0].slice(0),i.length>2&&"ID"===(j=i[0]).type&&c.getById&&9===b.nodeType&&n&&d.relative[i[1].type]){if(b=(d.find.ID(j.matches[0].replace(ab,bb),b)||[])[0],!b)return e;a=a.slice(i.shift().value.length)}h=V.needsContext.test(a)?0:i.length;while(h--){if(j=i[h],d.relative[k=j.type])break;if((l=d.find[k])&&(f=l(j.matches[0].replace(ab,bb),$.test(i[0].type)&&mb(b.parentNode)||b))){if(i.splice(h,1),a=f.length&&pb(i),!a)return G.apply(e,f),e;break}}}return g(a,m)(f,b,!n,e,$.test(a)&&mb(b.parentNode)||b),e}return c.sortStable=s.split("").sort(z).join("")===s,c.detectDuplicates=!!j,k(),c.sortDetached=gb(function(a){return 1&a.compareDocumentPosition(l.createElement("div"))}),gb(function(a){return a.innerHTML="<a href='#'></a>","#"===a.firstChild.getAttribute("href")})||hb("type|href|height|width",function(a,b,c){return c?void 0:a.getAttribute(b,"type"===b.toLowerCase()?1:2)}),c.attributes&&gb(function(a){return a.innerHTML="<input/>",a.firstChild.setAttribute("value",""),""===a.firstChild.getAttribute("value")})||hb("value",function(a,b,c){return c||"input"!==a.nodeName.toLowerCase()?void 0:a.defaultValue}),gb(function(a){return null==a.getAttribute("disabled")})||hb(J,function(a,b,c){var d;return c?void 0:a[b]===!0?b.toLowerCase():(d=a.getAttributeNode(b))&&d.specified?d.value:null}),db}(a);n.find=t,n.expr=t.selectors,n.expr[":"]=n.expr.pseudos,n.unique=t.uniqueSort,n.text=t.getText,n.isXMLDoc=t.isXML,n.contains=t.contains;var u=n.expr.match.needsContext,v=/^<(\w+)\s*\/?>(?:<\/\1>|)$/,w=/^.[^:#\[\.,]*$/;function x(a,b,c){if(n.isFunction(b))return n.grep(a,function(a,d){return!!b.call(a,d,a)!==c});if(b.nodeType)return n.grep(a,function(a){return a===b!==c});if("string"==typeof b){if(w.test(b))return n.filter(b,a,c);b=n.filter(b,a)}return n.grep(a,function(a){return n.inArray(a,b)>=0!==c})}n.filter=function(a,b,c){var d=b[0];return c&&(a=":not("+a+")"),1===b.length&&1===d.nodeType?n.find.matchesSelector(d,a)?[d]:[]:n.find.matches(a,n.grep(b,function(a){return 1===a.nodeType}))},n.fn.extend({find:function(a){var b,c=[],d=this,e=d.length;if("string"!=typeof a)return this.pushStack(n(a).filter(function(){for(b=0;e>b;b++)if(n.contains(d[b],this))return!0}));for(b=0;e>b;b++)n.find(a,d[b],c);return c=this.pushStack(e>1?n.unique(c):c),c.selector=this.selector?this.selector+" "+a:a,c},filter:function(a){return this.pushStack(x(this,a||[],!1))},not:function(a){return this.pushStack(x(this,a||[],!0))},is:function(a){return!!x(this,"string"==typeof a&&u.test(a)?n(a):a||[],!1).length}});var y,z=a.document,A=/^(?:\s*(<[\w\W]+>)[^>]*|#([\w-]*))$/,B=n.fn.init=function(a,b){var c,d;if(!a)return this;if("string"==typeof a){if(c="<"===a.charAt(0)&&">"===a.charAt(a.length-1)&&a.length>=3?[null,a,null]:A.exec(a),!c||!c[1]&&b)return!b||b.jquery?(b||y).find(a):this.constructor(b).find(a);if(c[1]){if(b=b instanceof n?b[0]:b,n.merge(this,n.parseHTML(c[1],b&&b.nodeType?b.ownerDocument||b:z,!0)),v.test(c[1])&&n.isPlainObject(b))for(c in b)n.isFunction(this[c])?this[c](b[c]):this.attr(c,b[c]);return this}if(d=z.getElementById(c[2]),d&&d.parentNode){if(d.id!==c[2])return y.find(a);this.length=1,this[0]=d}return this.context=z,this.selector=a,this}return a.nodeType?(this.context=this[0]=a,this.length=1,this):n.isFunction(a)?"undefined"!=typeof y.ready?y.ready(a):a(n):(void 0!==a.selector&&(this.selector=a.selector,this.context=a.context),n.makeArray(a,this))};B.prototype=n.fn,y=n(z);var C=/^(?:parents|prev(?:Until|All))/,D={children:!0,contents:!0,next:!0,prev:!0};n.extend({dir:function(a,b,c){var d=[],e=a[b];while(e&&9!==e.nodeType&&(void 0===c||1!==e.nodeType||!n(e).is(c)))1===e.nodeType&&d.push(e),e=e[b];return d},sibling:function(a,b){for(var c=[];a;a=a.nextSibling)1===a.nodeType&&a!==b&&c.push(a);return c}}),n.fn.extend({has:function(a){var b,c=n(a,this),d=c.length;return this.filter(function(){for(b=0;d>b;b++)if(n.contains(this,c[b]))return!0})},closest:function(a,b){for(var c,d=0,e=this.length,f=[],g=u.test(a)||"string"!=typeof a?n(a,b||this.context):0;e>d;d++)for(c=this[d];c&&c!==b;c=c.parentNode)if(c.nodeType<11&&(g?g.index(c)>-1:1===c.nodeType&&n.find.matchesSelector(c,a))){f.push(c);break}return this.pushStack(f.length>1?n.unique(f):f)},index:function(a){return a?"string"==typeof a?n.inArray(this[0],n(a)):n.inArray(a.jquery?a[0]:a,this):this[0]&&this[0].parentNode?this.first().prevAll().length:-1},add:function(a,b){return this.pushStack(n.unique(n.merge(this.get(),n(a,b))))},addBack:function(a){return this.add(null==a?this.prevObject:this.prevObject.filter(a))}});function E(a,b){do a=a[b];while(a&&1!==a.nodeType);return a}n.each({parent:function(a){var b=a.parentNode;return b&&11!==b.nodeType?b:null},parents:function(a){return n.dir(a,"parentNode")},parentsUntil:function(a,b,c){return n.dir(a,"parentNode",c)},next:function(a){return E(a,"nextSibling")},prev:function(a){return E(a,"previousSibling")},nextAll:function(a){return n.dir(a,"nextSibling")},prevAll:function(a){return n.dir(a,"previousSibling")},nextUntil:function(a,b,c){return n.dir(a,"nextSibling",c)},prevUntil:function(a,b,c){return n.dir(a,"previousSibling",c)},siblings:function(a){return n.sibling((a.parentNode||{}).firstChild,a)},children:function(a){return n.sibling(a.firstChild)},contents:function(a){return n.nodeName(a,"iframe")?a.contentDocument||a.contentWindow.document:n.merge([],a.childNodes)}},function(a,b){n.fn[a]=function(c,d){var e=n.map(this,b,c);return"Until"!==a.slice(-5)&&(d=c),d&&"string"==typeof d&&(e=n.filter(d,e)),this.length>1&&(D[a]||(e=n.unique(e)),C.test(a)&&(e=e.reverse())),this.pushStack(e)}});var F=/\S+/g,G={};function H(a){var b=G[a]={};return n.each(a.match(F)||[],function(a,c){b[c]=!0}),b}n.Callbacks=function(a){a="string"==typeof a?G[a]||H(a):n.extend({},a);var b,c,d,e,f,g,h=[],i=!a.once&&[],j=function(l){for(c=a.memory&&l,d=!0,f=g||0,g=0,e=h.length,b=!0;h&&e>f;f++)if(h[f].apply(l[0],l[1])===!1&&a.stopOnFalse){c=!1;break}b=!1,h&&(i?i.length&&j(i.shift()):c?h=[]:k.disable())},k={add:function(){if(h){var d=h.length;!function f(b){n.each(b,function(b,c){var d=n.type(c);"function"===d?a.unique&&k.has(c)||h.push(c):c&&c.length&&"string"!==d&&f(c)})}(arguments),b?e=h.length:c&&(g=d,j(c))}return this},remove:function(){return h&&n.each(arguments,function(a,c){var d;while((d=n.inArray(c,h,d))>-1)h.splice(d,1),b&&(e>=d&&e--,f>=d&&f--)}),this},has:function(a){return a?n.inArray(a,h)>-1:!(!h||!h.length)},empty:function(){return h=[],e=0,this},disable:function(){return h=i=c=void 0,this},disabled:function(){return!h},lock:function(){return i=void 0,c||k.disable(),this},locked:function(){return!i},fireWith:function(a,c){return!h||d&&!i||(c=c||[],c=[a,c.slice?c.slice():c],b?i.push(c):j(c)),this},fire:function(){return k.fireWith(this,arguments),this},fired:function(){return!!d}};return k},n.extend({Deferred:function(a){var b=[["resolve","done",n.Callbacks("once memory"),"resolved"],["reject","fail",n.Callbacks("once memory"),"rejected"],["notify","progress",n.Callbacks("memory")]],c="pending",d={state:function(){return c},always:function(){return e.done(arguments).fail(arguments),this},then:function(){var a=arguments;return n.Deferred(function(c){n.each(b,function(b,f){var g=n.isFunction(a[b])&&a[b];e[f[1]](function(){var a=g&&g.apply(this,arguments);a&&n.isFunction(a.promise)?a.promise().done(c.resolve).fail(c.reject).progress(c.notify):c[f[0]+"With"](this===d?c.promise():this,g?[a]:arguments)})}),a=null}).promise()},promise:function(a){return null!=a?n.extend(a,d):d}},e={};return d.pipe=d.then,n.each(b,function(a,f){var g=f[2],h=f[3];d[f[1]]=g.add,h&&g.add(function(){c=h},b[1^a][2].disable,b[2][2].lock),e[f[0]]=function(){return e[f[0]+"With"](this===e?d:this,arguments),this},e[f[0]+"With"]=g.fireWith}),d.promise(e),a&&a.call(e,e),e},when:function(a){var b=0,c=d.call(arguments),e=c.length,f=1!==e||a&&n.isFunction(a.promise)?e:0,g=1===f?a:n.Deferred(),h=function(a,b,c){return function(e){b[a]=this,c[a]=arguments.length>1?d.call(arguments):e,c===i?g.notifyWith(b,c):--f||g.resolveWith(b,c)}},i,j,k;if(e>1)for(i=new Array(e),j=new Array(e),k=new Array(e);e>b;b++)c[b]&&n.isFunction(c[b].promise)?c[b].promise().done(h(b,k,c)).fail(g.reject).progress(h(b,j,i)):--f;return f||g.resolveWith(k,c),g.promise()}});var I;n.fn.ready=function(a){return n.ready.promise().done(a),this},n.extend({isReady:!1,readyWait:1,holdReady:function(a){a?n.readyWait++:n.ready(!0)},ready:function(a){if(a===!0?!--n.readyWait:!n.isReady){if(!z.body)return setTimeout(n.ready);n.isReady=!0,a!==!0&&--n.readyWait>0||(I.resolveWith(z,[n]),n.fn.trigger&&n(z).trigger("ready").off("ready"))}}});function J(){z.addEventListener?(z.removeEventListener("DOMContentLoaded",K,!1),a.removeEventListener("load",K,!1)):(z.detachEvent("onreadystatechange",K),a.detachEvent("onload",K))}function K(){(z.addEventListener||"load"===event.type||"complete"===z.readyState)&&(J(),n.ready())}n.ready.promise=function(b){if(!I)if(I=n.Deferred(),"complete"===z.readyState)setTimeout(n.ready);else if(z.addEventListener)z.addEventListener("DOMContentLoaded",K,!1),a.addEventListener("load",K,!1);else{z.attachEvent("onreadystatechange",K),a.attachEvent("onload",K);var c=!1;try{c=null==a.frameElement&&z.documentElement}catch(d){}c&&c.doScroll&&!function e(){if(!n.isReady){try{c.doScroll("left")}catch(a){return setTimeout(e,50)}J(),n.ready()}}()}return I.promise(b)};var L="undefined",M;for(M in n(l))break;l.ownLast="0"!==M,l.inlineBlockNeedsLayout=!1,n(function(){var a,b,c=z.getElementsByTagName("body")[0];c&&(a=z.createElement("div"),a.style.cssText="border:0;width:0;height:0;position:absolute;top:0;left:-9999px;margin-top:1px",b=z.createElement("div"),c.appendChild(a).appendChild(b),typeof b.style.zoom!==L&&(b.style.cssText="border:0;margin:0;width:1px;padding:1px;display:inline;zoom:1",(l.inlineBlockNeedsLayout=3===b.offsetWidth)&&(c.style.zoom=1)),c.removeChild(a),a=b=null)}),function(){var a=z.createElement("div");if(null==l.deleteExpando){l.deleteExpando=!0;try{delete a.test}catch(b){l.deleteExpando=!1}}a=null}(),n.acceptData=function(a){var b=n.noData[(a.nodeName+" ").toLowerCase()],c=+a.nodeType||1;return 1!==c&&9!==c?!1:!b||b!==!0&&a.getAttribute("classid")===b};var N=/^(?:\{[\w\W]*\}|\[[\w\W]*\])$/,O=/([A-Z])/g;function P(a,b,c){if(void 0===c&&1===a.nodeType){var d="data-"+b.replace(O,"-$1").toLowerCase();if(c=a.getAttribute(d),"string"==typeof c){try{c="true"===c?!0:"false"===c?!1:"null"===c?null:+c+""===c?+c:N.test(c)?n.parseJSON(c):c}catch(e){}n.data(a,b,c)}else c=void 0}return c}function Q(a){var b;for(b in a)if(("data"!==b||!n.isEmptyObject(a[b]))&&"toJSON"!==b)return!1;return!0}function R(a,b,d,e){if(n.acceptData(a)){var f,g,h=n.expando,i=a.nodeType,j=i?n.cache:a,k=i?a[h]:a[h]&&h;if(k&&j[k]&&(e||j[k].data)||void 0!==d||"string"!=typeof b)return k||(k=i?a[h]=c.pop()||n.guid++:h),j[k]||(j[k]=i?{}:{toJSON:n.noop}),("object"==typeof b||"function"==typeof b)&&(e?j[k]=n.extend(j[k],b):j[k].data=n.extend(j[k].data,b)),g=j[k],e||(g.data||(g.data={}),g=g.data),void 0!==d&&(g[n.camelCase(b)]=d),"string"==typeof b?(f=g[b],null==f&&(f=g[n.camelCase(b)])):f=g,f
}}function S(a,b,c){if(n.acceptData(a)){var d,e,f=a.nodeType,g=f?n.cache:a,h=f?a[n.expando]:n.expando;if(g[h]){if(b&&(d=c?g[h]:g[h].data)){n.isArray(b)?b=b.concat(n.map(b,n.camelCase)):b in d?b=[b]:(b=n.camelCase(b),b=b in d?[b]:b.split(" ")),e=b.length;while(e--)delete d[b[e]];if(c?!Q(d):!n.isEmptyObject(d))return}(c||(delete g[h].data,Q(g[h])))&&(f?n.cleanData([a],!0):l.deleteExpando||g!=g.window?delete g[h]:g[h]=null)}}}n.extend({cache:{},noData:{"applet ":!0,"embed ":!0,"object ":"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"},hasData:function(a){return a=a.nodeType?n.cache[a[n.expando]]:a[n.expando],!!a&&!Q(a)},data:function(a,b,c){return R(a,b,c)},removeData:function(a,b){return S(a,b)},_data:function(a,b,c){return R(a,b,c,!0)},_removeData:function(a,b){return S(a,b,!0)}}),n.fn.extend({data:function(a,b){var c,d,e,f=this[0],g=f&&f.attributes;if(void 0===a){if(this.length&&(e=n.data(f),1===f.nodeType&&!n._data(f,"parsedAttrs"))){c=g.length;while(c--)d=g[c].name,0===d.indexOf("data-")&&(d=n.camelCase(d.slice(5)),P(f,d,e[d]));n._data(f,"parsedAttrs",!0)}return e}return"object"==typeof a?this.each(function(){n.data(this,a)}):arguments.length>1?this.each(function(){n.data(this,a,b)}):f?P(f,a,n.data(f,a)):void 0},removeData:function(a){return this.each(function(){n.removeData(this,a)})}}),n.extend({queue:function(a,b,c){var d;return a?(b=(b||"fx")+"queue",d=n._data(a,b),c&&(!d||n.isArray(c)?d=n._data(a,b,n.makeArray(c)):d.push(c)),d||[]):void 0},dequeue:function(a,b){b=b||"fx";var c=n.queue(a,b),d=c.length,e=c.shift(),f=n._queueHooks(a,b),g=function(){n.dequeue(a,b)};"inprogress"===e&&(e=c.shift(),d--),e&&("fx"===b&&c.unshift("inprogress"),delete f.stop,e.call(a,g,f)),!d&&f&&f.empty.fire()},_queueHooks:function(a,b){var c=b+"queueHooks";return n._data(a,c)||n._data(a,c,{empty:n.Callbacks("once memory").add(function(){n._removeData(a,b+"queue"),n._removeData(a,c)})})}}),n.fn.extend({queue:function(a,b){var c=2;return"string"!=typeof a&&(b=a,a="fx",c--),arguments.length<c?n.queue(this[0],a):void 0===b?this:this.each(function(){var c=n.queue(this,a,b);n._queueHooks(this,a),"fx"===a&&"inprogress"!==c[0]&&n.dequeue(this,a)})},dequeue:function(a){return this.each(function(){n.dequeue(this,a)})},clearQueue:function(a){return this.queue(a||"fx",[])},promise:function(a,b){var c,d=1,e=n.Deferred(),f=this,g=this.length,h=function(){--d||e.resolveWith(f,[f])};"string"!=typeof a&&(b=a,a=void 0),a=a||"fx";while(g--)c=n._data(f[g],a+"queueHooks"),c&&c.empty&&(d++,c.empty.add(h));return h(),e.promise(b)}});var T=/[+-]?(?:\d*\.|)\d+(?:[eE][+-]?\d+|)/.source,U=["Top","Right","Bottom","Left"],V=function(a,b){return a=b||a,"none"===n.css(a,"display")||!n.contains(a.ownerDocument,a)},W=n.access=function(a,b,c,d,e,f,g){var h=0,i=a.length,j=null==c;if("object"===n.type(c)){e=!0;for(h in c)n.access(a,b,h,c[h],!0,f,g)}else if(void 0!==d&&(e=!0,n.isFunction(d)||(g=!0),j&&(g?(b.call(a,d),b=null):(j=b,b=function(a,b,c){return j.call(n(a),c)})),b))for(;i>h;h++)b(a[h],c,g?d:d.call(a[h],h,b(a[h],c)));return e?a:j?b.call(a):i?b(a[0],c):f},X=/^(?:checkbox|radio)$/i;!function(){var a=z.createDocumentFragment(),b=z.createElement("div"),c=z.createElement("input");if(b.setAttribute("className","t"),b.innerHTML="  <link/><table></table><a href='/a'>a</a>",l.leadingWhitespace=3===b.firstChild.nodeType,l.tbody=!b.getElementsByTagName("tbody").length,l.htmlSerialize=!!b.getElementsByTagName("link").length,l.html5Clone="<:nav></:nav>"!==z.createElement("nav").cloneNode(!0).outerHTML,c.type="checkbox",c.checked=!0,a.appendChild(c),l.appendChecked=c.checked,b.innerHTML="<textarea>x</textarea>",l.noCloneChecked=!!b.cloneNode(!0).lastChild.defaultValue,a.appendChild(b),b.innerHTML="<input type='radio' checked='checked' name='t'/>",l.checkClone=b.cloneNode(!0).cloneNode(!0).lastChild.checked,l.noCloneEvent=!0,b.attachEvent&&(b.attachEvent("onclick",function(){l.noCloneEvent=!1}),b.cloneNode(!0).click()),null==l.deleteExpando){l.deleteExpando=!0;try{delete b.test}catch(d){l.deleteExpando=!1}}a=b=c=null}(),function(){var b,c,d=z.createElement("div");for(b in{submit:!0,change:!0,focusin:!0})c="on"+b,(l[b+"Bubbles"]=c in a)||(d.setAttribute(c,"t"),l[b+"Bubbles"]=d.attributes[c].expando===!1);d=null}();var Y=/^(?:input|select|textarea)$/i,Z=/^key/,$=/^(?:mouse|contextmenu)|click/,_=/^(?:focusinfocus|focusoutblur)$/,ab=/^([^.]*)(?:\.(.+)|)$/;function bb(){return!0}function cb(){return!1}function db(){try{return z.activeElement}catch(a){}}n.event={global:{},add:function(a,b,c,d,e){var f,g,h,i,j,k,l,m,o,p,q,r=n._data(a);if(r){c.handler&&(i=c,c=i.handler,e=i.selector),c.guid||(c.guid=n.guid++),(g=r.events)||(g=r.events={}),(k=r.handle)||(k=r.handle=function(a){return typeof n===L||a&&n.event.triggered===a.type?void 0:n.event.dispatch.apply(k.elem,arguments)},k.elem=a),b=(b||"").match(F)||[""],h=b.length;while(h--)f=ab.exec(b[h])||[],o=q=f[1],p=(f[2]||"").split(".").sort(),o&&(j=n.event.special[o]||{},o=(e?j.delegateType:j.bindType)||o,j=n.event.special[o]||{},l=n.extend({type:o,origType:q,data:d,handler:c,guid:c.guid,selector:e,needsContext:e&&n.expr.match.needsContext.test(e),namespace:p.join(".")},i),(m=g[o])||(m=g[o]=[],m.delegateCount=0,j.setup&&j.setup.call(a,d,p,k)!==!1||(a.addEventListener?a.addEventListener(o,k,!1):a.attachEvent&&a.attachEvent("on"+o,k))),j.add&&(j.add.call(a,l),l.handler.guid||(l.handler.guid=c.guid)),e?m.splice(m.delegateCount++,0,l):m.push(l),n.event.global[o]=!0);a=null}},remove:function(a,b,c,d,e){var f,g,h,i,j,k,l,m,o,p,q,r=n.hasData(a)&&n._data(a);if(r&&(k=r.events)){b=(b||"").match(F)||[""],j=b.length;while(j--)if(h=ab.exec(b[j])||[],o=q=h[1],p=(h[2]||"").split(".").sort(),o){l=n.event.special[o]||{},o=(d?l.delegateType:l.bindType)||o,m=k[o]||[],h=h[2]&&new RegExp("(^|\\.)"+p.join("\\.(?:.*\\.|)")+"(\\.|$)"),i=f=m.length;while(f--)g=m[f],!e&&q!==g.origType||c&&c.guid!==g.guid||h&&!h.test(g.namespace)||d&&d!==g.selector&&("**"!==d||!g.selector)||(m.splice(f,1),g.selector&&m.delegateCount--,l.remove&&l.remove.call(a,g));i&&!m.length&&(l.teardown&&l.teardown.call(a,p,r.handle)!==!1||n.removeEvent(a,o,r.handle),delete k[o])}else for(o in k)n.event.remove(a,o+b[j],c,d,!0);n.isEmptyObject(k)&&(delete r.handle,n._removeData(a,"events"))}},trigger:function(b,c,d,e){var f,g,h,i,k,l,m,o=[d||z],p=j.call(b,"type")?b.type:b,q=j.call(b,"namespace")?b.namespace.split("."):[];if(h=l=d=d||z,3!==d.nodeType&&8!==d.nodeType&&!_.test(p+n.event.triggered)&&(p.indexOf(".")>=0&&(q=p.split("."),p=q.shift(),q.sort()),g=p.indexOf(":")<0&&"on"+p,b=b[n.expando]?b:new n.Event(p,"object"==typeof b&&b),b.isTrigger=e?2:3,b.namespace=q.join("."),b.namespace_re=b.namespace?new RegExp("(^|\\.)"+q.join("\\.(?:.*\\.|)")+"(\\.|$)"):null,b.result=void 0,b.target||(b.target=d),c=null==c?[b]:n.makeArray(c,[b]),k=n.event.special[p]||{},e||!k.trigger||k.trigger.apply(d,c)!==!1)){if(!e&&!k.noBubble&&!n.isWindow(d)){for(i=k.delegateType||p,_.test(i+p)||(h=h.parentNode);h;h=h.parentNode)o.push(h),l=h;l===(d.ownerDocument||z)&&o.push(l.defaultView||l.parentWindow||a)}m=0;while((h=o[m++])&&!b.isPropagationStopped())b.type=m>1?i:k.bindType||p,f=(n._data(h,"events")||{})[b.type]&&n._data(h,"handle"),f&&f.apply(h,c),f=g&&h[g],f&&f.apply&&n.acceptData(h)&&(b.result=f.apply(h,c),b.result===!1&&b.preventDefault());if(b.type=p,!e&&!b.isDefaultPrevented()&&(!k._default||k._default.apply(o.pop(),c)===!1)&&n.acceptData(d)&&g&&d[p]&&!n.isWindow(d)){l=d[g],l&&(d[g]=null),n.event.triggered=p;try{d[p]()}catch(r){}n.event.triggered=void 0,l&&(d[g]=l)}return b.result}},dispatch:function(a){a=n.event.fix(a);var b,c,e,f,g,h=[],i=d.call(arguments),j=(n._data(this,"events")||{})[a.type]||[],k=n.event.special[a.type]||{};if(i[0]=a,a.delegateTarget=this,!k.preDispatch||k.preDispatch.call(this,a)!==!1){h=n.event.handlers.call(this,a,j),b=0;while((f=h[b++])&&!a.isPropagationStopped()){a.currentTarget=f.elem,g=0;while((e=f.handlers[g++])&&!a.isImmediatePropagationStopped())(!a.namespace_re||a.namespace_re.test(e.namespace))&&(a.handleObj=e,a.data=e.data,c=((n.event.special[e.origType]||{}).handle||e.handler).apply(f.elem,i),void 0!==c&&(a.result=c)===!1&&(a.preventDefault(),a.stopPropagation()))}return k.postDispatch&&k.postDispatch.call(this,a),a.result}},handlers:function(a,b){var c,d,e,f,g=[],h=b.delegateCount,i=a.target;if(h&&i.nodeType&&(!a.button||"click"!==a.type))for(;i!=this;i=i.parentNode||this)if(1===i.nodeType&&(i.disabled!==!0||"click"!==a.type)){for(e=[],f=0;h>f;f++)d=b[f],c=d.selector+" ",void 0===e[c]&&(e[c]=d.needsContext?n(c,this).index(i)>=0:n.find(c,this,null,[i]).length),e[c]&&e.push(d);e.length&&g.push({elem:i,handlers:e})}return h<b.length&&g.push({elem:this,handlers:b.slice(h)}),g},fix:function(a){if(a[n.expando])return a;var b,c,d,e=a.type,f=a,g=this.fixHooks[e];g||(this.fixHooks[e]=g=$.test(e)?this.mouseHooks:Z.test(e)?this.keyHooks:{}),d=g.props?this.props.concat(g.props):this.props,a=new n.Event(f),b=d.length;while(b--)c=d[b],a[c]=f[c];return a.target||(a.target=f.srcElement||z),3===a.target.nodeType&&(a.target=a.target.parentNode),a.metaKey=!!a.metaKey,g.filter?g.filter(a,f):a},props:"altKey bubbles cancelable ctrlKey currentTarget eventPhase metaKey relatedTarget shiftKey target timeStamp view which".split(" "),fixHooks:{},keyHooks:{props:"char charCode key keyCode".split(" "),filter:function(a,b){return null==a.which&&(a.which=null!=b.charCode?b.charCode:b.keyCode),a}},mouseHooks:{props:"button buttons clientX clientY fromElement offsetX offsetY pageX pageY screenX screenY toElement".split(" "),filter:function(a,b){var c,d,e,f=b.button,g=b.fromElement;return null==a.pageX&&null!=b.clientX&&(d=a.target.ownerDocument||z,e=d.documentElement,c=d.body,a.pageX=b.clientX+(e&&e.scrollLeft||c&&c.scrollLeft||0)-(e&&e.clientLeft||c&&c.clientLeft||0),a.pageY=b.clientY+(e&&e.scrollTop||c&&c.scrollTop||0)-(e&&e.clientTop||c&&c.clientTop||0)),!a.relatedTarget&&g&&(a.relatedTarget=g===a.target?b.toElement:g),a.which||void 0===f||(a.which=1&f?1:2&f?3:4&f?2:0),a}},special:{load:{noBubble:!0},focus:{trigger:function(){if(this!==db()&&this.focus)try{return this.focus(),!1}catch(a){}},delegateType:"focusin"},blur:{trigger:function(){return this===db()&&this.blur?(this.blur(),!1):void 0},delegateType:"focusout"},click:{trigger:function(){return n.nodeName(this,"input")&&"checkbox"===this.type&&this.click?(this.click(),!1):void 0},_default:function(a){return n.nodeName(a.target,"a")}},beforeunload:{postDispatch:function(a){void 0!==a.result&&(a.originalEvent.returnValue=a.result)}}},simulate:function(a,b,c,d){var e=n.extend(new n.Event,c,{type:a,isSimulated:!0,originalEvent:{}});d?n.event.trigger(e,null,b):n.event.dispatch.call(b,e),e.isDefaultPrevented()&&c.preventDefault()}},n.removeEvent=z.removeEventListener?function(a,b,c){a.removeEventListener&&a.removeEventListener(b,c,!1)}:function(a,b,c){var d="on"+b;a.detachEvent&&(typeof a[d]===L&&(a[d]=null),a.detachEvent(d,c))},n.Event=function(a,b){return this instanceof n.Event?(a&&a.type?(this.originalEvent=a,this.type=a.type,this.isDefaultPrevented=a.defaultPrevented||void 0===a.defaultPrevented&&(a.returnValue===!1||a.getPreventDefault&&a.getPreventDefault())?bb:cb):this.type=a,b&&n.extend(this,b),this.timeStamp=a&&a.timeStamp||n.now(),void(this[n.expando]=!0)):new n.Event(a,b)},n.Event.prototype={isDefaultPrevented:cb,isPropagationStopped:cb,isImmediatePropagationStopped:cb,preventDefault:function(){var a=this.originalEvent;this.isDefaultPrevented=bb,a&&(a.preventDefault?a.preventDefault():a.returnValue=!1)},stopPropagation:function(){var a=this.originalEvent;this.isPropagationStopped=bb,a&&(a.stopPropagation&&a.stopPropagation(),a.cancelBubble=!0)},stopImmediatePropagation:function(){this.isImmediatePropagationStopped=bb,this.stopPropagation()}},n.each({mouseenter:"mouseover",mouseleave:"mouseout"},function(a,b){n.event.special[a]={delegateType:b,bindType:b,handle:function(a){var c,d=this,e=a.relatedTarget,f=a.handleObj;return(!e||e!==d&&!n.contains(d,e))&&(a.type=f.origType,c=f.handler.apply(this,arguments),a.type=b),c}}}),l.submitBubbles||(n.event.special.submit={setup:function(){return n.nodeName(this,"form")?!1:void n.event.add(this,"click._submit keypress._submit",function(a){var b=a.target,c=n.nodeName(b,"input")||n.nodeName(b,"button")?b.form:void 0;c&&!n._data(c,"submitBubbles")&&(n.event.add(c,"submit._submit",function(a){a._submit_bubble=!0}),n._data(c,"submitBubbles",!0))})},postDispatch:function(a){a._submit_bubble&&(delete a._submit_bubble,this.parentNode&&!a.isTrigger&&n.event.simulate("submit",this.parentNode,a,!0))},teardown:function(){return n.nodeName(this,"form")?!1:void n.event.remove(this,"._submit")}}),l.changeBubbles||(n.event.special.change={setup:function(){return Y.test(this.nodeName)?(("checkbox"===this.type||"radio"===this.type)&&(n.event.add(this,"propertychange._change",function(a){"checked"===a.originalEvent.propertyName&&(this._just_changed=!0)}),n.event.add(this,"click._change",function(a){this._just_changed&&!a.isTrigger&&(this._just_changed=!1),n.event.simulate("change",this,a,!0)})),!1):void n.event.add(this,"beforeactivate._change",function(a){var b=a.target;Y.test(b.nodeName)&&!n._data(b,"changeBubbles")&&(n.event.add(b,"change._change",function(a){!this.parentNode||a.isSimulated||a.isTrigger||n.event.simulate("change",this.parentNode,a,!0)}),n._data(b,"changeBubbles",!0))})},handle:function(a){var b=a.target;return this!==b||a.isSimulated||a.isTrigger||"radio"!==b.type&&"checkbox"!==b.type?a.handleObj.handler.apply(this,arguments):void 0},teardown:function(){return n.event.remove(this,"._change"),!Y.test(this.nodeName)}}),l.focusinBubbles||n.each({focus:"focusin",blur:"focusout"},function(a,b){var c=function(a){n.event.simulate(b,a.target,n.event.fix(a),!0)};n.event.special[b]={setup:function(){var d=this.ownerDocument||this,e=n._data(d,b);e||d.addEventListener(a,c,!0),n._data(d,b,(e||0)+1)},teardown:function(){var d=this.ownerDocument||this,e=n._data(d,b)-1;e?n._data(d,b,e):(d.removeEventListener(a,c,!0),n._removeData(d,b))}}}),n.fn.extend({on:function(a,b,c,d,e){var f,g;if("object"==typeof a){"string"!=typeof b&&(c=c||b,b=void 0);for(f in a)this.on(f,b,c,a[f],e);return this}if(null==c&&null==d?(d=b,c=b=void 0):null==d&&("string"==typeof b?(d=c,c=void 0):(d=c,c=b,b=void 0)),d===!1)d=cb;else if(!d)return this;return 1===e&&(g=d,d=function(a){return n().off(a),g.apply(this,arguments)},d.guid=g.guid||(g.guid=n.guid++)),this.each(function(){n.event.add(this,a,d,c,b)})},one:function(a,b,c,d){return this.on(a,b,c,d,1)},off:function(a,b,c){var d,e;if(a&&a.preventDefault&&a.handleObj)return d=a.handleObj,n(a.delegateTarget).off(d.namespace?d.origType+"."+d.namespace:d.origType,d.selector,d.handler),this;if("object"==typeof a){for(e in a)this.off(e,b,a[e]);return this}return(b===!1||"function"==typeof b)&&(c=b,b=void 0),c===!1&&(c=cb),this.each(function(){n.event.remove(this,a,c,b)})},trigger:function(a,b){return this.each(function(){n.event.trigger(a,b,this)})},triggerHandler:function(a,b){var c=this[0];return c?n.event.trigger(a,b,c,!0):void 0}});function eb(a){var b=fb.split("|"),c=a.createDocumentFragment();if(c.createElement)while(b.length)c.createElement(b.pop());return c}var fb="abbr|article|aside|audio|bdi|canvas|data|datalist|details|figcaption|figure|footer|header|hgroup|mark|meter|nav|output|progress|section|summary|time|video",gb=/ jQuery\d+="(?:null|\d+)"/g,hb=new RegExp("<(?:"+fb+")[\\s/>]","i"),ib=/^\s+/,jb=/<(?!area|br|col|embed|hr|img|input|link|meta|param)(([\w:]+)[^>]*)\/>/gi,kb=/<([\w:]+)/,lb=/<tbody/i,mb=/<|&#?\w+;/,nb=/<(?:script|style|link)/i,ob=/checked\s*(?:[^=]|=\s*.checked.)/i,pb=/^$|\/(?:java|ecma)script/i,qb=/^true\/(.*)/,rb=/^\s*<!(?:\[CDATA\[|--)|(?:\]\]|--)>\s*$/g,sb={option:[1,"<select multiple='multiple'>","</select>"],legend:[1,"<fieldset>","</fieldset>"],area:[1,"<map>","</map>"],param:[1,"<object>","</object>"],thead:[1,"<table>","</table>"],tr:[2,"<table><tbody>","</tbody></table>"],col:[2,"<table><tbody></tbody><colgroup>","</colgroup></table>"],td:[3,"<table><tbody><tr>","</tr></tbody></table>"],_default:l.htmlSerialize?[0,"",""]:[1,"X<div>","</div>"]},tb=eb(z),ub=tb.appendChild(z.createElement("div"));sb.optgroup=sb.option,sb.tbody=sb.tfoot=sb.colgroup=sb.caption=sb.thead,sb.th=sb.td;function vb(a,b){var c,d,e=0,f=typeof a.getElementsByTagName!==L?a.getElementsByTagName(b||"*"):typeof a.querySelectorAll!==L?a.querySelectorAll(b||"*"):void 0;if(!f)for(f=[],c=a.childNodes||a;null!=(d=c[e]);e++)!b||n.nodeName(d,b)?f.push(d):n.merge(f,vb(d,b));return void 0===b||b&&n.nodeName(a,b)?n.merge([a],f):f}function wb(a){X.test(a.type)&&(a.defaultChecked=a.checked)}function xb(a,b){return n.nodeName(a,"table")&&n.nodeName(11!==b.nodeType?b:b.firstChild,"tr")?a.getElementsByTagName("tbody")[0]||a.appendChild(a.ownerDocument.createElement("tbody")):a}function yb(a){return a.type=(null!==n.find.attr(a,"type"))+"/"+a.type,a}function zb(a){var b=qb.exec(a.type);return b?a.type=b[1]:a.removeAttribute("type"),a}function Ab(a,b){for(var c,d=0;null!=(c=a[d]);d++)n._data(c,"globalEval",!b||n._data(b[d],"globalEval"))}function Bb(a,b){if(1===b.nodeType&&n.hasData(a)){var c,d,e,f=n._data(a),g=n._data(b,f),h=f.events;if(h){delete g.handle,g.events={};for(c in h)for(d=0,e=h[c].length;e>d;d++)n.event.add(b,c,h[c][d])}g.data&&(g.data=n.extend({},g.data))}}function Cb(a,b){var c,d,e;if(1===b.nodeType){if(c=b.nodeName.toLowerCase(),!l.noCloneEvent&&b[n.expando]){e=n._data(b);for(d in e.events)n.removeEvent(b,d,e.handle);b.removeAttribute(n.expando)}"script"===c&&b.text!==a.text?(yb(b).text=a.text,zb(b)):"object"===c?(b.parentNode&&(b.outerHTML=a.outerHTML),l.html5Clone&&a.innerHTML&&!n.trim(b.innerHTML)&&(b.innerHTML=a.innerHTML)):"input"===c&&X.test(a.type)?(b.defaultChecked=b.checked=a.checked,b.value!==a.value&&(b.value=a.value)):"option"===c?b.defaultSelected=b.selected=a.defaultSelected:("input"===c||"textarea"===c)&&(b.defaultValue=a.defaultValue)}}n.extend({clone:function(a,b,c){var d,e,f,g,h,i=n.contains(a.ownerDocument,a);if(l.html5Clone||n.isXMLDoc(a)||!hb.test("<"+a.nodeName+">")?f=a.cloneNode(!0):(ub.innerHTML=a.outerHTML,ub.removeChild(f=ub.firstChild)),!(l.noCloneEvent&&l.noCloneChecked||1!==a.nodeType&&11!==a.nodeType||n.isXMLDoc(a)))for(d=vb(f),h=vb(a),g=0;null!=(e=h[g]);++g)d[g]&&Cb(e,d[g]);if(b)if(c)for(h=h||vb(a),d=d||vb(f),g=0;null!=(e=h[g]);g++)Bb(e,d[g]);else Bb(a,f);return d=vb(f,"script"),d.length>0&&Ab(d,!i&&vb(a,"script")),d=h=e=null,f},buildFragment:function(a,b,c,d){for(var e,f,g,h,i,j,k,m=a.length,o=eb(b),p=[],q=0;m>q;q++)if(f=a[q],f||0===f)if("object"===n.type(f))n.merge(p,f.nodeType?[f]:f);else if(mb.test(f)){h=h||o.appendChild(b.createElement("div")),i=(kb.exec(f)||["",""])[1].toLowerCase(),k=sb[i]||sb._default,h.innerHTML=k[1]+f.replace(jb,"<$1></$2>")+k[2],e=k[0];while(e--)h=h.lastChild;if(!l.leadingWhitespace&&ib.test(f)&&p.push(b.createTextNode(ib.exec(f)[0])),!l.tbody){f="table"!==i||lb.test(f)?"<table>"!==k[1]||lb.test(f)?0:h:h.firstChild,e=f&&f.childNodes.length;while(e--)n.nodeName(j=f.childNodes[e],"tbody")&&!j.childNodes.length&&f.removeChild(j)}n.merge(p,h.childNodes),h.textContent="";while(h.firstChild)h.removeChild(h.firstChild);h=o.lastChild}else p.push(b.createTextNode(f));h&&o.removeChild(h),l.appendChecked||n.grep(vb(p,"input"),wb),q=0;while(f=p[q++])if((!d||-1===n.inArray(f,d))&&(g=n.contains(f.ownerDocument,f),h=vb(o.appendChild(f),"script"),g&&Ab(h),c)){e=0;while(f=h[e++])pb.test(f.type||"")&&c.push(f)}return h=null,o},cleanData:function(a,b){for(var d,e,f,g,h=0,i=n.expando,j=n.cache,k=l.deleteExpando,m=n.event.special;null!=(d=a[h]);h++)if((b||n.acceptData(d))&&(f=d[i],g=f&&j[f])){if(g.events)for(e in g.events)m[e]?n.event.remove(d,e):n.removeEvent(d,e,g.handle);j[f]&&(delete j[f],k?delete d[i]:typeof d.removeAttribute!==L?d.removeAttribute(i):d[i]=null,c.push(f))}}}),n.fn.extend({text:function(a){return W(this,function(a){return void 0===a?n.text(this):this.empty().append((this[0]&&this[0].ownerDocument||z).createTextNode(a))},null,a,arguments.length)},append:function(){return this.domManip(arguments,function(a){if(1===this.nodeType||11===this.nodeType||9===this.nodeType){var b=xb(this,a);b.appendChild(a)}})},prepend:function(){return this.domManip(arguments,function(a){if(1===this.nodeType||11===this.nodeType||9===this.nodeType){var b=xb(this,a);b.insertBefore(a,b.firstChild)}})},before:function(){return this.domManip(arguments,function(a){this.parentNode&&this.parentNode.insertBefore(a,this)})},after:function(){return this.domManip(arguments,function(a){this.parentNode&&this.parentNode.insertBefore(a,this.nextSibling)})},remove:function(a,b){for(var c,d=a?n.filter(a,this):this,e=0;null!=(c=d[e]);e++)b||1!==c.nodeType||n.cleanData(vb(c)),c.parentNode&&(b&&n.contains(c.ownerDocument,c)&&Ab(vb(c,"script")),c.parentNode.removeChild(c));return this},empty:function(){for(var a,b=0;null!=(a=this[b]);b++){1===a.nodeType&&n.cleanData(vb(a,!1));while(a.firstChild)a.removeChild(a.firstChild);a.options&&n.nodeName(a,"select")&&(a.options.length=0)}return this},clone:function(a,b){return a=null==a?!1:a,b=null==b?a:b,this.map(function(){return n.clone(this,a,b)})},html:function(a){return W(this,function(a){var b=this[0]||{},c=0,d=this.length;if(void 0===a)return 1===b.nodeType?b.innerHTML.replace(gb,""):void 0;if(!("string"!=typeof a||nb.test(a)||!l.htmlSerialize&&hb.test(a)||!l.leadingWhitespace&&ib.test(a)||sb[(kb.exec(a)||["",""])[1].toLowerCase()])){a=a.replace(jb,"<$1></$2>");try{for(;d>c;c++)b=this[c]||{},1===b.nodeType&&(n.cleanData(vb(b,!1)),b.innerHTML=a);b=0}catch(e){}}b&&this.empty().append(a)},null,a,arguments.length)},replaceWith:function(){var a=arguments[0];return this.domManip(arguments,function(b){a=this.parentNode,n.cleanData(vb(this)),a&&a.replaceChild(b,this)}),a&&(a.length||a.nodeType)?this:this.remove()},detach:function(a){return this.remove(a,!0)},domManip:function(a,b){a=e.apply([],a);var c,d,f,g,h,i,j=0,k=this.length,m=this,o=k-1,p=a[0],q=n.isFunction(p);if(q||k>1&&"string"==typeof p&&!l.checkClone&&ob.test(p))return this.each(function(c){var d=m.eq(c);q&&(a[0]=p.call(this,c,d.html())),d.domManip(a,b)});if(k&&(i=n.buildFragment(a,this[0].ownerDocument,!1,this),c=i.firstChild,1===i.childNodes.length&&(i=c),c)){for(g=n.map(vb(i,"script"),yb),f=g.length;k>j;j++)d=i,j!==o&&(d=n.clone(d,!0,!0),f&&n.merge(g,vb(d,"script"))),b.call(this[j],d,j);if(f)for(h=g[g.length-1].ownerDocument,n.map(g,zb),j=0;f>j;j++)d=g[j],pb.test(d.type||"")&&!n._data(d,"globalEval")&&n.contains(h,d)&&(d.src?n._evalUrl&&n._evalUrl(d.src):n.globalEval((d.text||d.textContent||d.innerHTML||"").replace(rb,"")));i=c=null}return this}}),n.each({appendTo:"append",prependTo:"prepend",insertBefore:"before",insertAfter:"after",replaceAll:"replaceWith"},function(a,b){n.fn[a]=function(a){for(var c,d=0,e=[],g=n(a),h=g.length-1;h>=d;d++)c=d===h?this:this.clone(!0),n(g[d])[b](c),f.apply(e,c.get());return this.pushStack(e)}});var Db,Eb={};function Fb(b,c){var d=n(c.createElement(b)).appendTo(c.body),e=a.getDefaultComputedStyle?a.getDefaultComputedStyle(d[0]).display:n.css(d[0],"display");return d.detach(),e}function Gb(a){var b=z,c=Eb[a];return c||(c=Fb(a,b),"none"!==c&&c||(Db=(Db||n("<iframe frameborder='0' width='0' height='0'/>")).appendTo(b.documentElement),b=(Db[0].contentWindow||Db[0].contentDocument).document,b.write(),b.close(),c=Fb(a,b),Db.detach()),Eb[a]=c),c}!function(){var a,b,c=z.createElement("div"),d="-webkit-box-sizing:content-box;-moz-box-sizing:content-box;box-sizing:content-box;display:block;padding:0;margin:0;border:0";c.innerHTML="  <link/><table></table><a href='/a'>a</a><input type='checkbox'/>",a=c.getElementsByTagName("a")[0],a.style.cssText="float:left;opacity:.5",l.opacity=/^0.5/.test(a.style.opacity),l.cssFloat=!!a.style.cssFloat,c.style.backgroundClip="content-box",c.cloneNode(!0).style.backgroundClip="",l.clearCloneStyle="content-box"===c.style.backgroundClip,a=c=null,l.shrinkWrapBlocks=function(){var a,c,e,f;if(null==b){if(a=z.getElementsByTagName("body")[0],!a)return;f="border:0;width:0;height:0;position:absolute;top:0;left:-9999px",c=z.createElement("div"),e=z.createElement("div"),a.appendChild(c).appendChild(e),b=!1,typeof e.style.zoom!==L&&(e.style.cssText=d+";width:1px;padding:1px;zoom:1",e.innerHTML="<div></div>",e.firstChild.style.width="5px",b=3!==e.offsetWidth),a.removeChild(c),a=c=e=null}return b}}();var Hb=/^margin/,Ib=new RegExp("^("+T+")(?!px)[a-z%]+$","i"),Jb,Kb,Lb=/^(top|right|bottom|left)$/;a.getComputedStyle?(Jb=function(a){return a.ownerDocument.defaultView.getComputedStyle(a,null)},Kb=function(a,b,c){var d,e,f,g,h=a.style;return c=c||Jb(a),g=c?c.getPropertyValue(b)||c[b]:void 0,c&&(""!==g||n.contains(a.ownerDocument,a)||(g=n.style(a,b)),Ib.test(g)&&Hb.test(b)&&(d=h.width,e=h.minWidth,f=h.maxWidth,h.minWidth=h.maxWidth=h.width=g,g=c.width,h.width=d,h.minWidth=e,h.maxWidth=f)),void 0===g?g:g+""}):z.documentElement.currentStyle&&(Jb=function(a){return a.currentStyle},Kb=function(a,b,c){var d,e,f,g,h=a.style;return c=c||Jb(a),g=c?c[b]:void 0,null==g&&h&&h[b]&&(g=h[b]),Ib.test(g)&&!Lb.test(b)&&(d=h.left,e=a.runtimeStyle,f=e&&e.left,f&&(e.left=a.currentStyle.left),h.left="fontSize"===b?"1em":g,g=h.pixelLeft+"px",h.left=d,f&&(e.left=f)),void 0===g?g:g+""||"auto"});function Mb(a,b){return{get:function(){var c=a();if(null!=c)return c?void delete this.get:(this.get=b).apply(this,arguments)}}}!function(){var b,c,d,e,f,g,h=z.createElement("div"),i="border:0;width:0;height:0;position:absolute;top:0;left:-9999px",j="-webkit-box-sizing:content-box;-moz-box-sizing:content-box;box-sizing:content-box;display:block;padding:0;margin:0;border:0";h.innerHTML="  <link/><table></table><a href='/a'>a</a><input type='checkbox'/>",b=h.getElementsByTagName("a")[0],b.style.cssText="float:left;opacity:.5",l.opacity=/^0.5/.test(b.style.opacity),l.cssFloat=!!b.style.cssFloat,h.style.backgroundClip="content-box",h.cloneNode(!0).style.backgroundClip="",l.clearCloneStyle="content-box"===h.style.backgroundClip,b=h=null,n.extend(l,{reliableHiddenOffsets:function(){if(null!=c)return c;var a,b,d,e=z.createElement("div"),f=z.getElementsByTagName("body")[0];if(f)return e.setAttribute("className","t"),e.innerHTML="  <link/><table></table><a href='/a'>a</a><input type='checkbox'/>",a=z.createElement("div"),a.style.cssText=i,f.appendChild(a).appendChild(e),e.innerHTML="<table><tr><td></td><td>t</td></tr></table>",b=e.getElementsByTagName("td"),b[0].style.cssText="padding:0;margin:0;border:0;display:none",d=0===b[0].offsetHeight,b[0].style.display="",b[1].style.display="none",c=d&&0===b[0].offsetHeight,f.removeChild(a),e=f=null,c},boxSizing:function(){return null==d&&k(),d},boxSizingReliable:function(){return null==e&&k(),e},pixelPosition:function(){return null==f&&k(),f},reliableMarginRight:function(){var b,c,d,e;if(null==g&&a.getComputedStyle){if(b=z.getElementsByTagName("body")[0],!b)return;c=z.createElement("div"),d=z.createElement("div"),c.style.cssText=i,b.appendChild(c).appendChild(d),e=d.appendChild(z.createElement("div")),e.style.cssText=d.style.cssText=j,e.style.marginRight=e.style.width="0",d.style.width="1px",g=!parseFloat((a.getComputedStyle(e,null)||{}).marginRight),b.removeChild(c)}return g}});function k(){var b,c,h=z.getElementsByTagName("body")[0];h&&(b=z.createElement("div"),c=z.createElement("div"),b.style.cssText=i,h.appendChild(b).appendChild(c),c.style.cssText="-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box;position:absolute;display:block;padding:1px;border:1px;width:4px;margin-top:1%;top:1%",n.swap(h,null!=h.style.zoom?{zoom:1}:{},function(){d=4===c.offsetWidth}),e=!0,f=!1,g=!0,a.getComputedStyle&&(f="1%"!==(a.getComputedStyle(c,null)||{}).top,e="4px"===(a.getComputedStyle(c,null)||{width:"4px"}).width),h.removeChild(b),c=h=null)}}(),n.swap=function(a,b,c,d){var e,f,g={};for(f in b)g[f]=a.style[f],a.style[f]=b[f];e=c.apply(a,d||[]);for(f in b)a.style[f]=g[f];return e};var Nb=/alpha\([^)]*\)/i,Ob=/opacity\s*=\s*([^)]*)/,Pb=/^(none|table(?!-c[ea]).+)/,Qb=new RegExp("^("+T+")(.*)$","i"),Rb=new RegExp("^([+-])=("+T+")","i"),Sb={position:"absolute",visibility:"hidden",display:"block"},Tb={letterSpacing:0,fontWeight:400},Ub=["Webkit","O","Moz","ms"];function Vb(a,b){if(b in a)return b;var c=b.charAt(0).toUpperCase()+b.slice(1),d=b,e=Ub.length;while(e--)if(b=Ub[e]+c,b in a)return b;return d}function Wb(a,b){for(var c,d,e,f=[],g=0,h=a.length;h>g;g++)d=a[g],d.style&&(f[g]=n._data(d,"olddisplay"),c=d.style.display,b?(f[g]||"none"!==c||(d.style.display=""),""===d.style.display&&V(d)&&(f[g]=n._data(d,"olddisplay",Gb(d.nodeName)))):f[g]||(e=V(d),(c&&"none"!==c||!e)&&n._data(d,"olddisplay",e?c:n.css(d,"display"))));for(g=0;h>g;g++)d=a[g],d.style&&(b&&"none"!==d.style.display&&""!==d.style.display||(d.style.display=b?f[g]||"":"none"));return a}function Xb(a,b,c){var d=Qb.exec(b);return d?Math.max(0,d[1]-(c||0))+(d[2]||"px"):b}function Yb(a,b,c,d,e){for(var f=c===(d?"border":"content")?4:"width"===b?1:0,g=0;4>f;f+=2)"margin"===c&&(g+=n.css(a,c+U[f],!0,e)),d?("content"===c&&(g-=n.css(a,"padding"+U[f],!0,e)),"margin"!==c&&(g-=n.css(a,"border"+U[f]+"Width",!0,e))):(g+=n.css(a,"padding"+U[f],!0,e),"padding"!==c&&(g+=n.css(a,"border"+U[f]+"Width",!0,e)));return g}function Zb(a,b,c){var d=!0,e="width"===b?a.offsetWidth:a.offsetHeight,f=Jb(a),g=l.boxSizing()&&"border-box"===n.css(a,"boxSizing",!1,f);if(0>=e||null==e){if(e=Kb(a,b,f),(0>e||null==e)&&(e=a.style[b]),Ib.test(e))return e;d=g&&(l.boxSizingReliable()||e===a.style[b]),e=parseFloat(e)||0}return e+Yb(a,b,c||(g?"border":"content"),d,f)+"px"}n.extend({cssHooks:{opacity:{get:function(a,b){if(b){var c=Kb(a,"opacity");return""===c?"1":c}}}},cssNumber:{columnCount:!0,fillOpacity:!0,fontWeight:!0,lineHeight:!0,opacity:!0,order:!0,orphans:!0,widows:!0,zIndex:!0,zoom:!0},cssProps:{"float":l.cssFloat?"cssFloat":"styleFloat"},style:function(a,b,c,d){if(a&&3!==a.nodeType&&8!==a.nodeType&&a.style){var e,f,g,h=n.camelCase(b),i=a.style;if(b=n.cssProps[h]||(n.cssProps[h]=Vb(i,h)),g=n.cssHooks[b]||n.cssHooks[h],void 0===c)return g&&"get"in g&&void 0!==(e=g.get(a,!1,d))?e:i[b];if(f=typeof c,"string"===f&&(e=Rb.exec(c))&&(c=(e[1]+1)*e[2]+parseFloat(n.css(a,b)),f="number"),null!=c&&c===c&&("number"!==f||n.cssNumber[h]||(c+="px"),l.clearCloneStyle||""!==c||0!==b.indexOf("background")||(i[b]="inherit"),!(g&&"set"in g&&void 0===(c=g.set(a,c,d)))))try{i[b]="",i[b]=c}catch(j){}}},css:function(a,b,c,d){var e,f,g,h=n.camelCase(b);return b=n.cssProps[h]||(n.cssProps[h]=Vb(a.style,h)),g=n.cssHooks[b]||n.cssHooks[h],g&&"get"in g&&(f=g.get(a,!0,c)),void 0===f&&(f=Kb(a,b,d)),"normal"===f&&b in Tb&&(f=Tb[b]),""===c||c?(e=parseFloat(f),c===!0||n.isNumeric(e)?e||0:f):f}}),n.each(["height","width"],function(a,b){n.cssHooks[b]={get:function(a,c,d){return c?0===a.offsetWidth&&Pb.test(n.css(a,"display"))?n.swap(a,Sb,function(){return Zb(a,b,d)}):Zb(a,b,d):void 0},set:function(a,c,d){var e=d&&Jb(a);return Xb(a,c,d?Yb(a,b,d,l.boxSizing()&&"border-box"===n.css(a,"boxSizing",!1,e),e):0)}}}),l.opacity||(n.cssHooks.opacity={get:function(a,b){return Ob.test((b&&a.currentStyle?a.currentStyle.filter:a.style.filter)||"")?.01*parseFloat(RegExp.$1)+"":b?"1":""},set:function(a,b){var c=a.style,d=a.currentStyle,e=n.isNumeric(b)?"alpha(opacity="+100*b+")":"",f=d&&d.filter||c.filter||"";c.zoom=1,(b>=1||""===b)&&""===n.trim(f.replace(Nb,""))&&c.removeAttribute&&(c.removeAttribute("filter"),""===b||d&&!d.filter)||(c.filter=Nb.test(f)?f.replace(Nb,e):f+" "+e)}}),n.cssHooks.marginRight=Mb(l.reliableMarginRight,function(a,b){return b?n.swap(a,{display:"inline-block"},Kb,[a,"marginRight"]):void 0}),n.each({margin:"",padding:"",border:"Width"},function(a,b){n.cssHooks[a+b]={expand:function(c){for(var d=0,e={},f="string"==typeof c?c.split(" "):[c];4>d;d++)e[a+U[d]+b]=f[d]||f[d-2]||f[0];return e}},Hb.test(a)||(n.cssHooks[a+b].set=Xb)}),n.fn.extend({css:function(a,b){return W(this,function(a,b,c){var d,e,f={},g=0;if(n.isArray(b)){for(d=Jb(a),e=b.length;e>g;g++)f[b[g]]=n.css(a,b[g],!1,d);return f}return void 0!==c?n.style(a,b,c):n.css(a,b)
},a,b,arguments.length>1)},show:function(){return Wb(this,!0)},hide:function(){return Wb(this)},toggle:function(a){return"boolean"==typeof a?a?this.show():this.hide():this.each(function(){V(this)?n(this).show():n(this).hide()})}});function $b(a,b,c,d,e){return new $b.prototype.init(a,b,c,d,e)}n.Tween=$b,$b.prototype={constructor:$b,init:function(a,b,c,d,e,f){this.elem=a,this.prop=c,this.easing=e||"swing",this.options=b,this.start=this.now=this.cur(),this.end=d,this.unit=f||(n.cssNumber[c]?"":"px")},cur:function(){var a=$b.propHooks[this.prop];return a&&a.get?a.get(this):$b.propHooks._default.get(this)},run:function(a){var b,c=$b.propHooks[this.prop];return this.pos=b=this.options.duration?n.easing[this.easing](a,this.options.duration*a,0,1,this.options.duration):a,this.now=(this.end-this.start)*b+this.start,this.options.step&&this.options.step.call(this.elem,this.now,this),c&&c.set?c.set(this):$b.propHooks._default.set(this),this}},$b.prototype.init.prototype=$b.prototype,$b.propHooks={_default:{get:function(a){var b;return null==a.elem[a.prop]||a.elem.style&&null!=a.elem.style[a.prop]?(b=n.css(a.elem,a.prop,""),b&&"auto"!==b?b:0):a.elem[a.prop]},set:function(a){n.fx.step[a.prop]?n.fx.step[a.prop](a):a.elem.style&&(null!=a.elem.style[n.cssProps[a.prop]]||n.cssHooks[a.prop])?n.style(a.elem,a.prop,a.now+a.unit):a.elem[a.prop]=a.now}}},$b.propHooks.scrollTop=$b.propHooks.scrollLeft={set:function(a){a.elem.nodeType&&a.elem.parentNode&&(a.elem[a.prop]=a.now)}},n.easing={linear:function(a){return a},swing:function(a){return.5-Math.cos(a*Math.PI)/2}},n.fx=$b.prototype.init,n.fx.step={};var _b,ac,bc=/^(?:toggle|show|hide)$/,cc=new RegExp("^(?:([+-])=|)("+T+")([a-z%]*)$","i"),dc=/queueHooks$/,ec=[jc],fc={"*":[function(a,b){var c=this.createTween(a,b),d=c.cur(),e=cc.exec(b),f=e&&e[3]||(n.cssNumber[a]?"":"px"),g=(n.cssNumber[a]||"px"!==f&&+d)&&cc.exec(n.css(c.elem,a)),h=1,i=20;if(g&&g[3]!==f){f=f||g[3],e=e||[],g=+d||1;do h=h||".5",g/=h,n.style(c.elem,a,g+f);while(h!==(h=c.cur()/d)&&1!==h&&--i)}return e&&(g=c.start=+g||+d||0,c.unit=f,c.end=e[1]?g+(e[1]+1)*e[2]:+e[2]),c}]};function gc(){return setTimeout(function(){_b=void 0}),_b=n.now()}function hc(a,b){var c,d={height:a},e=0;for(b=b?1:0;4>e;e+=2-b)c=U[e],d["margin"+c]=d["padding"+c]=a;return b&&(d.opacity=d.width=a),d}function ic(a,b,c){for(var d,e=(fc[b]||[]).concat(fc["*"]),f=0,g=e.length;g>f;f++)if(d=e[f].call(c,b,a))return d}function jc(a,b,c){var d,e,f,g,h,i,j,k,m=this,o={},p=a.style,q=a.nodeType&&V(a),r=n._data(a,"fxshow");c.queue||(h=n._queueHooks(a,"fx"),null==h.unqueued&&(h.unqueued=0,i=h.empty.fire,h.empty.fire=function(){h.unqueued||i()}),h.unqueued++,m.always(function(){m.always(function(){h.unqueued--,n.queue(a,"fx").length||h.empty.fire()})})),1===a.nodeType&&("height"in b||"width"in b)&&(c.overflow=[p.overflow,p.overflowX,p.overflowY],j=n.css(a,"display"),k=Gb(a.nodeName),"none"===j&&(j=k),"inline"===j&&"none"===n.css(a,"float")&&(l.inlineBlockNeedsLayout&&"inline"!==k?p.zoom=1:p.display="inline-block")),c.overflow&&(p.overflow="hidden",l.shrinkWrapBlocks()||m.always(function(){p.overflow=c.overflow[0],p.overflowX=c.overflow[1],p.overflowY=c.overflow[2]}));for(d in b)if(e=b[d],bc.exec(e)){if(delete b[d],f=f||"toggle"===e,e===(q?"hide":"show")){if("show"!==e||!r||void 0===r[d])continue;q=!0}o[d]=r&&r[d]||n.style(a,d)}if(!n.isEmptyObject(o)){r?"hidden"in r&&(q=r.hidden):r=n._data(a,"fxshow",{}),f&&(r.hidden=!q),q?n(a).show():m.done(function(){n(a).hide()}),m.done(function(){var b;n._removeData(a,"fxshow");for(b in o)n.style(a,b,o[b])});for(d in o)g=ic(q?r[d]:0,d,m),d in r||(r[d]=g.start,q&&(g.end=g.start,g.start="width"===d||"height"===d?1:0))}}function kc(a,b){var c,d,e,f,g;for(c in a)if(d=n.camelCase(c),e=b[d],f=a[c],n.isArray(f)&&(e=f[1],f=a[c]=f[0]),c!==d&&(a[d]=f,delete a[c]),g=n.cssHooks[d],g&&"expand"in g){f=g.expand(f),delete a[d];for(c in f)c in a||(a[c]=f[c],b[c]=e)}else b[d]=e}function lc(a,b,c){var d,e,f=0,g=ec.length,h=n.Deferred().always(function(){delete i.elem}),i=function(){if(e)return!1;for(var b=_b||gc(),c=Math.max(0,j.startTime+j.duration-b),d=c/j.duration||0,f=1-d,g=0,i=j.tweens.length;i>g;g++)j.tweens[g].run(f);return h.notifyWith(a,[j,f,c]),1>f&&i?c:(h.resolveWith(a,[j]),!1)},j=h.promise({elem:a,props:n.extend({},b),opts:n.extend(!0,{specialEasing:{}},c),originalProperties:b,originalOptions:c,startTime:_b||gc(),duration:c.duration,tweens:[],createTween:function(b,c){var d=n.Tween(a,j.opts,b,c,j.opts.specialEasing[b]||j.opts.easing);return j.tweens.push(d),d},stop:function(b){var c=0,d=b?j.tweens.length:0;if(e)return this;for(e=!0;d>c;c++)j.tweens[c].run(1);return b?h.resolveWith(a,[j,b]):h.rejectWith(a,[j,b]),this}}),k=j.props;for(kc(k,j.opts.specialEasing);g>f;f++)if(d=ec[f].call(j,a,k,j.opts))return d;return n.map(k,ic,j),n.isFunction(j.opts.start)&&j.opts.start.call(a,j),n.fx.timer(n.extend(i,{elem:a,anim:j,queue:j.opts.queue})),j.progress(j.opts.progress).done(j.opts.done,j.opts.complete).fail(j.opts.fail).always(j.opts.always)}n.Animation=n.extend(lc,{tweener:function(a,b){n.isFunction(a)?(b=a,a=["*"]):a=a.split(" ");for(var c,d=0,e=a.length;e>d;d++)c=a[d],fc[c]=fc[c]||[],fc[c].unshift(b)},prefilter:function(a,b){b?ec.unshift(a):ec.push(a)}}),n.speed=function(a,b,c){var d=a&&"object"==typeof a?n.extend({},a):{complete:c||!c&&b||n.isFunction(a)&&a,duration:a,easing:c&&b||b&&!n.isFunction(b)&&b};return d.duration=n.fx.off?0:"number"==typeof d.duration?d.duration:d.duration in n.fx.speeds?n.fx.speeds[d.duration]:n.fx.speeds._default,(null==d.queue||d.queue===!0)&&(d.queue="fx"),d.old=d.complete,d.complete=function(){n.isFunction(d.old)&&d.old.call(this),d.queue&&n.dequeue(this,d.queue)},d},n.fn.extend({fadeTo:function(a,b,c,d){return this.filter(V).css("opacity",0).show().end().animate({opacity:b},a,c,d)},animate:function(a,b,c,d){var e=n.isEmptyObject(a),f=n.speed(b,c,d),g=function(){var b=lc(this,n.extend({},a),f);(e||n._data(this,"finish"))&&b.stop(!0)};return g.finish=g,e||f.queue===!1?this.each(g):this.queue(f.queue,g)},stop:function(a,b,c){var d=function(a){var b=a.stop;delete a.stop,b(c)};return"string"!=typeof a&&(c=b,b=a,a=void 0),b&&a!==!1&&this.queue(a||"fx",[]),this.each(function(){var b=!0,e=null!=a&&a+"queueHooks",f=n.timers,g=n._data(this);if(e)g[e]&&g[e].stop&&d(g[e]);else for(e in g)g[e]&&g[e].stop&&dc.test(e)&&d(g[e]);for(e=f.length;e--;)f[e].elem!==this||null!=a&&f[e].queue!==a||(f[e].anim.stop(c),b=!1,f.splice(e,1));(b||!c)&&n.dequeue(this,a)})},finish:function(a){return a!==!1&&(a=a||"fx"),this.each(function(){var b,c=n._data(this),d=c[a+"queue"],e=c[a+"queueHooks"],f=n.timers,g=d?d.length:0;for(c.finish=!0,n.queue(this,a,[]),e&&e.stop&&e.stop.call(this,!0),b=f.length;b--;)f[b].elem===this&&f[b].queue===a&&(f[b].anim.stop(!0),f.splice(b,1));for(b=0;g>b;b++)d[b]&&d[b].finish&&d[b].finish.call(this);delete c.finish})}}),n.each(["toggle","show","hide"],function(a,b){var c=n.fn[b];n.fn[b]=function(a,d,e){return null==a||"boolean"==typeof a?c.apply(this,arguments):this.animate(hc(b,!0),a,d,e)}}),n.each({slideDown:hc("show"),slideUp:hc("hide"),slideToggle:hc("toggle"),fadeIn:{opacity:"show"},fadeOut:{opacity:"hide"},fadeToggle:{opacity:"toggle"}},function(a,b){n.fn[a]=function(a,c,d){return this.animate(b,a,c,d)}}),n.timers=[],n.fx.tick=function(){var a,b=n.timers,c=0;for(_b=n.now();c<b.length;c++)a=b[c],a()||b[c]!==a||b.splice(c--,1);b.length||n.fx.stop(),_b=void 0},n.fx.timer=function(a){n.timers.push(a),a()?n.fx.start():n.timers.pop()},n.fx.interval=13,n.fx.start=function(){ac||(ac=setInterval(n.fx.tick,n.fx.interval))},n.fx.stop=function(){clearInterval(ac),ac=null},n.fx.speeds={slow:600,fast:200,_default:400},n.fn.delay=function(a,b){return a=n.fx?n.fx.speeds[a]||a:a,b=b||"fx",this.queue(b,function(b,c){var d=setTimeout(b,a);c.stop=function(){clearTimeout(d)}})},function(){var a,b,c,d,e=z.createElement("div");e.setAttribute("className","t"),e.innerHTML="  <link/><table></table><a href='/a'>a</a><input type='checkbox'/>",a=e.getElementsByTagName("a")[0],c=z.createElement("select"),d=c.appendChild(z.createElement("option")),b=e.getElementsByTagName("input")[0],a.style.cssText="top:1px",l.getSetAttribute="t"!==e.className,l.style=/top/.test(a.getAttribute("style")),l.hrefNormalized="/a"===a.getAttribute("href"),l.checkOn=!!b.value,l.optSelected=d.selected,l.enctype=!!z.createElement("form").enctype,c.disabled=!0,l.optDisabled=!d.disabled,b=z.createElement("input"),b.setAttribute("value",""),l.input=""===b.getAttribute("value"),b.value="t",b.setAttribute("type","radio"),l.radioValue="t"===b.value,a=b=c=d=e=null}();var mc=/\r/g;n.fn.extend({val:function(a){var b,c,d,e=this[0];{if(arguments.length)return d=n.isFunction(a),this.each(function(c){var e;1===this.nodeType&&(e=d?a.call(this,c,n(this).val()):a,null==e?e="":"number"==typeof e?e+="":n.isArray(e)&&(e=n.map(e,function(a){return null==a?"":a+""})),b=n.valHooks[this.type]||n.valHooks[this.nodeName.toLowerCase()],b&&"set"in b&&void 0!==b.set(this,e,"value")||(this.value=e))});if(e)return b=n.valHooks[e.type]||n.valHooks[e.nodeName.toLowerCase()],b&&"get"in b&&void 0!==(c=b.get(e,"value"))?c:(c=e.value,"string"==typeof c?c.replace(mc,""):null==c?"":c)}}}),n.extend({valHooks:{option:{get:function(a){var b=n.find.attr(a,"value");return null!=b?b:n.text(a)}},select:{get:function(a){for(var b,c,d=a.options,e=a.selectedIndex,f="select-one"===a.type||0>e,g=f?null:[],h=f?e+1:d.length,i=0>e?h:f?e:0;h>i;i++)if(c=d[i],!(!c.selected&&i!==e||(l.optDisabled?c.disabled:null!==c.getAttribute("disabled"))||c.parentNode.disabled&&n.nodeName(c.parentNode,"optgroup"))){if(b=n(c).val(),f)return b;g.push(b)}return g},set:function(a,b){var c,d,e=a.options,f=n.makeArray(b),g=e.length;while(g--)if(d=e[g],n.inArray(n.valHooks.option.get(d),f)>=0)try{d.selected=c=!0}catch(h){d.scrollHeight}else d.selected=!1;return c||(a.selectedIndex=-1),e}}}}),n.each(["radio","checkbox"],function(){n.valHooks[this]={set:function(a,b){return n.isArray(b)?a.checked=n.inArray(n(a).val(),b)>=0:void 0}},l.checkOn||(n.valHooks[this].get=function(a){return null===a.getAttribute("value")?"on":a.value})});var nc,oc,pc=n.expr.attrHandle,qc=/^(?:checked|selected)$/i,rc=l.getSetAttribute,sc=l.input;n.fn.extend({attr:function(a,b){return W(this,n.attr,a,b,arguments.length>1)},removeAttr:function(a){return this.each(function(){n.removeAttr(this,a)})}}),n.extend({attr:function(a,b,c){var d,e,f=a.nodeType;if(a&&3!==f&&8!==f&&2!==f)return typeof a.getAttribute===L?n.prop(a,b,c):(1===f&&n.isXMLDoc(a)||(b=b.toLowerCase(),d=n.attrHooks[b]||(n.expr.match.bool.test(b)?oc:nc)),void 0===c?d&&"get"in d&&null!==(e=d.get(a,b))?e:(e=n.find.attr(a,b),null==e?void 0:e):null!==c?d&&"set"in d&&void 0!==(e=d.set(a,c,b))?e:(a.setAttribute(b,c+""),c):void n.removeAttr(a,b))},removeAttr:function(a,b){var c,d,e=0,f=b&&b.match(F);if(f&&1===a.nodeType)while(c=f[e++])d=n.propFix[c]||c,n.expr.match.bool.test(c)?sc&&rc||!qc.test(c)?a[d]=!1:a[n.camelCase("default-"+c)]=a[d]=!1:n.attr(a,c,""),a.removeAttribute(rc?c:d)},attrHooks:{type:{set:function(a,b){if(!l.radioValue&&"radio"===b&&n.nodeName(a,"input")){var c=a.value;return a.setAttribute("type",b),c&&(a.value=c),b}}}}}),oc={set:function(a,b,c){return b===!1?n.removeAttr(a,c):sc&&rc||!qc.test(c)?a.setAttribute(!rc&&n.propFix[c]||c,c):a[n.camelCase("default-"+c)]=a[c]=!0,c}},n.each(n.expr.match.bool.source.match(/\w+/g),function(a,b){var c=pc[b]||n.find.attr;pc[b]=sc&&rc||!qc.test(b)?function(a,b,d){var e,f;return d||(f=pc[b],pc[b]=e,e=null!=c(a,b,d)?b.toLowerCase():null,pc[b]=f),e}:function(a,b,c){return c?void 0:a[n.camelCase("default-"+b)]?b.toLowerCase():null}}),sc&&rc||(n.attrHooks.value={set:function(a,b,c){return n.nodeName(a,"input")?void(a.defaultValue=b):nc&&nc.set(a,b,c)}}),rc||(nc={set:function(a,b,c){var d=a.getAttributeNode(c);return d||a.setAttributeNode(d=a.ownerDocument.createAttribute(c)),d.value=b+="","value"===c||b===a.getAttribute(c)?b:void 0}},pc.id=pc.name=pc.coords=function(a,b,c){var d;return c?void 0:(d=a.getAttributeNode(b))&&""!==d.value?d.value:null},n.valHooks.button={get:function(a,b){var c=a.getAttributeNode(b);return c&&c.specified?c.value:void 0},set:nc.set},n.attrHooks.contenteditable={set:function(a,b,c){nc.set(a,""===b?!1:b,c)}},n.each(["width","height"],function(a,b){n.attrHooks[b]={set:function(a,c){return""===c?(a.setAttribute(b,"auto"),c):void 0}}})),l.style||(n.attrHooks.style={get:function(a){return a.style.cssText||void 0},set:function(a,b){return a.style.cssText=b+""}});var tc=/^(?:input|select|textarea|button|object)$/i,uc=/^(?:a|area)$/i;n.fn.extend({prop:function(a,b){return W(this,n.prop,a,b,arguments.length>1)},removeProp:function(a){return a=n.propFix[a]||a,this.each(function(){try{this[a]=void 0,delete this[a]}catch(b){}})}}),n.extend({propFix:{"for":"htmlFor","class":"className"},prop:function(a,b,c){var d,e,f,g=a.nodeType;if(a&&3!==g&&8!==g&&2!==g)return f=1!==g||!n.isXMLDoc(a),f&&(b=n.propFix[b]||b,e=n.propHooks[b]),void 0!==c?e&&"set"in e&&void 0!==(d=e.set(a,c,b))?d:a[b]=c:e&&"get"in e&&null!==(d=e.get(a,b))?d:a[b]},propHooks:{tabIndex:{get:function(a){var b=n.find.attr(a,"tabindex");return b?parseInt(b,10):tc.test(a.nodeName)||uc.test(a.nodeName)&&a.href?0:-1}}}}),l.hrefNormalized||n.each(["href","src"],function(a,b){n.propHooks[b]={get:function(a){return a.getAttribute(b,4)}}}),l.optSelected||(n.propHooks.selected={get:function(a){var b=a.parentNode;return b&&(b.selectedIndex,b.parentNode&&b.parentNode.selectedIndex),null}}),n.each(["tabIndex","readOnly","maxLength","cellSpacing","cellPadding","rowSpan","colSpan","useMap","frameBorder","contentEditable"],function(){n.propFix[this.toLowerCase()]=this}),l.enctype||(n.propFix.enctype="encoding");var vc=/[\t\r\n\f]/g;n.fn.extend({addClass:function(a){var b,c,d,e,f,g,h=0,i=this.length,j="string"==typeof a&&a;if(n.isFunction(a))return this.each(function(b){n(this).addClass(a.call(this,b,this.className))});if(j)for(b=(a||"").match(F)||[];i>h;h++)if(c=this[h],d=1===c.nodeType&&(c.className?(" "+c.className+" ").replace(vc," "):" ")){f=0;while(e=b[f++])d.indexOf(" "+e+" ")<0&&(d+=e+" ");g=n.trim(d),c.className!==g&&(c.className=g)}return this},removeClass:function(a){var b,c,d,e,f,g,h=0,i=this.length,j=0===arguments.length||"string"==typeof a&&a;if(n.isFunction(a))return this.each(function(b){n(this).removeClass(a.call(this,b,this.className))});if(j)for(b=(a||"").match(F)||[];i>h;h++)if(c=this[h],d=1===c.nodeType&&(c.className?(" "+c.className+" ").replace(vc," "):"")){f=0;while(e=b[f++])while(d.indexOf(" "+e+" ")>=0)d=d.replace(" "+e+" "," ");g=a?n.trim(d):"",c.className!==g&&(c.className=g)}return this},toggleClass:function(a,b){var c=typeof a;return"boolean"==typeof b&&"string"===c?b?this.addClass(a):this.removeClass(a):this.each(n.isFunction(a)?function(c){n(this).toggleClass(a.call(this,c,this.className,b),b)}:function(){if("string"===c){var b,d=0,e=n(this),f=a.match(F)||[];while(b=f[d++])e.hasClass(b)?e.removeClass(b):e.addClass(b)}else(c===L||"boolean"===c)&&(this.className&&n._data(this,"__className__",this.className),this.className=this.className||a===!1?"":n._data(this,"__className__")||"")})},hasClass:function(a){for(var b=" "+a+" ",c=0,d=this.length;d>c;c++)if(1===this[c].nodeType&&(" "+this[c].className+" ").replace(vc," ").indexOf(b)>=0)return!0;return!1}}),n.each("blur focus focusin focusout load resize scroll unload click dblclick mousedown mouseup mousemove mouseover mouseout mouseenter mouseleave change select submit keydown keypress keyup error contextmenu".split(" "),function(a,b){n.fn[b]=function(a,c){return arguments.length>0?this.on(b,null,a,c):this.trigger(b)}}),n.fn.extend({hover:function(a,b){return this.mouseenter(a).mouseleave(b||a)},bind:function(a,b,c){return this.on(a,null,b,c)},unbind:function(a,b){return this.off(a,null,b)},delegate:function(a,b,c,d){return this.on(b,a,c,d)},undelegate:function(a,b,c){return 1===arguments.length?this.off(a,"**"):this.off(b,a||"**",c)}});var wc=n.now(),xc=/\?/,yc=/(,)|(\[|{)|(}|])|"(?:[^"\\\r\n]|\\["\\\/bfnrt]|\\u[\da-fA-F]{4})*"\s*:?|true|false|null|-?(?!0\d)\d+(?:\.\d+|)(?:[eE][+-]?\d+|)/g;n.parseJSON=function(b){if(a.JSON&&a.JSON.parse)return a.JSON.parse(b+"");var c,d=null,e=n.trim(b+"");return e&&!n.trim(e.replace(yc,function(a,b,e,f){return c&&b&&(d=0),0===d?a:(c=e||b,d+=!f-!e,"")}))?Function("return "+e)():n.error("Invalid JSON: "+b)},n.parseXML=function(b){var c,d;if(!b||"string"!=typeof b)return null;try{a.DOMParser?(d=new DOMParser,c=d.parseFromString(b,"text/xml")):(c=new ActiveXObject("Microsoft.XMLDOM"),c.async="false",c.loadXML(b))}catch(e){c=void 0}return c&&c.documentElement&&!c.getElementsByTagName("parsererror").length||n.error("Invalid XML: "+b),c};var zc,Ac,Bc=/#.*$/,Cc=/([?&])_=[^&]*/,Dc=/^(.*?):[ \t]*([^\r\n]*)\r?$/gm,Ec=/^(?:about|app|app-storage|.+-extension|file|res|widget):$/,Fc=/^(?:GET|HEAD)$/,Gc=/^\/\//,Hc=/^([\w.+-]+:)(?:\/\/(?:[^\/?#]*@|)([^\/?#:]*)(?::(\d+)|)|)/,Ic={},Jc={},Kc="*/".concat("*");try{Ac=location.href}catch(Lc){Ac=z.createElement("a"),Ac.href="",Ac=Ac.href}zc=Hc.exec(Ac.toLowerCase())||[];function Mc(a){return function(b,c){"string"!=typeof b&&(c=b,b="*");var d,e=0,f=b.toLowerCase().match(F)||[];if(n.isFunction(c))while(d=f[e++])"+"===d.charAt(0)?(d=d.slice(1)||"*",(a[d]=a[d]||[]).unshift(c)):(a[d]=a[d]||[]).push(c)}}function Nc(a,b,c,d){var e={},f=a===Jc;function g(h){var i;return e[h]=!0,n.each(a[h]||[],function(a,h){var j=h(b,c,d);return"string"!=typeof j||f||e[j]?f?!(i=j):void 0:(b.dataTypes.unshift(j),g(j),!1)}),i}return g(b.dataTypes[0])||!e["*"]&&g("*")}function Oc(a,b){var c,d,e=n.ajaxSettings.flatOptions||{};for(d in b)void 0!==b[d]&&((e[d]?a:c||(c={}))[d]=b[d]);return c&&n.extend(!0,a,c),a}function Pc(a,b,c){var d,e,f,g,h=a.contents,i=a.dataTypes;while("*"===i[0])i.shift(),void 0===e&&(e=a.mimeType||b.getResponseHeader("Content-Type"));if(e)for(g in h)if(h[g]&&h[g].test(e)){i.unshift(g);break}if(i[0]in c)f=i[0];else{for(g in c){if(!i[0]||a.converters[g+" "+i[0]]){f=g;break}d||(d=g)}f=f||d}return f?(f!==i[0]&&i.unshift(f),c[f]):void 0}function Qc(a,b,c,d){var e,f,g,h,i,j={},k=a.dataTypes.slice();if(k[1])for(g in a.converters)j[g.toLowerCase()]=a.converters[g];f=k.shift();while(f)if(a.responseFields[f]&&(c[a.responseFields[f]]=b),!i&&d&&a.dataFilter&&(b=a.dataFilter(b,a.dataType)),i=f,f=k.shift())if("*"===f)f=i;else if("*"!==i&&i!==f){if(g=j[i+" "+f]||j["* "+f],!g)for(e in j)if(h=e.split(" "),h[1]===f&&(g=j[i+" "+h[0]]||j["* "+h[0]])){g===!0?g=j[e]:j[e]!==!0&&(f=h[0],k.unshift(h[1]));break}if(g!==!0)if(g&&a["throws"])b=g(b);else try{b=g(b)}catch(l){return{state:"parsererror",error:g?l:"No conversion from "+i+" to "+f}}}return{state:"success",data:b}}n.extend({active:0,lastModified:{},etag:{},ajaxSettings:{url:Ac,type:"GET",isLocal:Ec.test(zc[1]),global:!0,processData:!0,async:!0,contentType:"application/x-www-form-urlencoded; charset=UTF-8",accepts:{"*":Kc,text:"text/plain",html:"text/html",xml:"application/xml, text/xml",json:"application/json, text/javascript"},contents:{xml:/xml/,html:/html/,json:/json/},responseFields:{xml:"responseXML",text:"responseText",json:"responseJSON"},converters:{"* text":String,"text html":!0,"text json":n.parseJSON,"text xml":n.parseXML},flatOptions:{url:!0,context:!0}},ajaxSetup:function(a,b){return b?Oc(Oc(a,n.ajaxSettings),b):Oc(n.ajaxSettings,a)},ajaxPrefilter:Mc(Ic),ajaxTransport:Mc(Jc),ajax:function(a,b){"object"==typeof a&&(b=a,a=void 0),b=b||{};var c,d,e,f,g,h,i,j,k=n.ajaxSetup({},b),l=k.context||k,m=k.context&&(l.nodeType||l.jquery)?n(l):n.event,o=n.Deferred(),p=n.Callbacks("once memory"),q=k.statusCode||{},r={},s={},t=0,u="canceled",v={readyState:0,getResponseHeader:function(a){var b;if(2===t){if(!j){j={};while(b=Dc.exec(f))j[b[1].toLowerCase()]=b[2]}b=j[a.toLowerCase()]}return null==b?null:b},getAllResponseHeaders:function(){return 2===t?f:null},setRequestHeader:function(a,b){var c=a.toLowerCase();return t||(a=s[c]=s[c]||a,r[a]=b),this},overrideMimeType:function(a){return t||(k.mimeType=a),this},statusCode:function(a){var b;if(a)if(2>t)for(b in a)q[b]=[q[b],a[b]];else v.always(a[v.status]);return this},abort:function(a){var b=a||u;return i&&i.abort(b),x(0,b),this}};if(o.promise(v).complete=p.add,v.success=v.done,v.error=v.fail,k.url=((a||k.url||Ac)+"").replace(Bc,"").replace(Gc,zc[1]+"//"),k.type=b.method||b.type||k.method||k.type,k.dataTypes=n.trim(k.dataType||"*").toLowerCase().match(F)||[""],null==k.crossDomain&&(c=Hc.exec(k.url.toLowerCase()),k.crossDomain=!(!c||c[1]===zc[1]&&c[2]===zc[2]&&(c[3]||("http:"===c[1]?"80":"443"))===(zc[3]||("http:"===zc[1]?"80":"443")))),k.data&&k.processData&&"string"!=typeof k.data&&(k.data=n.param(k.data,k.traditional)),Nc(Ic,k,b,v),2===t)return v;h=k.global,h&&0===n.active++&&n.event.trigger("ajaxStart"),k.type=k.type.toUpperCase(),k.hasContent=!Fc.test(k.type),e=k.url,k.hasContent||(k.data&&(e=k.url+=(xc.test(e)?"&":"?")+k.data,delete k.data),k.cache===!1&&(k.url=Cc.test(e)?e.replace(Cc,"$1_="+wc++):e+(xc.test(e)?"&":"?")+"_="+wc++)),k.ifModified&&(n.lastModified[e]&&v.setRequestHeader("If-Modified-Since",n.lastModified[e]),n.etag[e]&&v.setRequestHeader("If-None-Match",n.etag[e])),(k.data&&k.hasContent&&k.contentType!==!1||b.contentType)&&v.setRequestHeader("Content-Type",k.contentType),v.setRequestHeader("Accept",k.dataTypes[0]&&k.accepts[k.dataTypes[0]]?k.accepts[k.dataTypes[0]]+("*"!==k.dataTypes[0]?", "+Kc+"; q=0.01":""):k.accepts["*"]);for(d in k.headers)v.setRequestHeader(d,k.headers[d]);if(k.beforeSend&&(k.beforeSend.call(l,v,k)===!1||2===t))return v.abort();u="abort";for(d in{success:1,error:1,complete:1})v[d](k[d]);if(i=Nc(Jc,k,b,v)){v.readyState=1,h&&m.trigger("ajaxSend",[v,k]),k.async&&k.timeout>0&&(g=setTimeout(function(){v.abort("timeout")},k.timeout));try{t=1,i.send(r,x)}catch(w){if(!(2>t))throw w;x(-1,w)}}else x(-1,"No Transport");function x(a,b,c,d){var j,r,s,u,w,x=b;2!==t&&(t=2,g&&clearTimeout(g),i=void 0,f=d||"",v.readyState=a>0?4:0,j=a>=200&&300>a||304===a,c&&(u=Pc(k,v,c)),u=Qc(k,u,v,j),j?(k.ifModified&&(w=v.getResponseHeader("Last-Modified"),w&&(n.lastModified[e]=w),w=v.getResponseHeader("etag"),w&&(n.etag[e]=w)),204===a||"HEAD"===k.type?x="nocontent":304===a?x="notmodified":(x=u.state,r=u.data,s=u.error,j=!s)):(s=x,(a||!x)&&(x="error",0>a&&(a=0))),v.status=a,v.statusText=(b||x)+"",j?o.resolveWith(l,[r,x,v]):o.rejectWith(l,[v,x,s]),v.statusCode(q),q=void 0,h&&m.trigger(j?"ajaxSuccess":"ajaxError",[v,k,j?r:s]),p.fireWith(l,[v,x]),h&&(m.trigger("ajaxComplete",[v,k]),--n.active||n.event.trigger("ajaxStop")))}return v},getJSON:function(a,b,c){return n.get(a,b,c,"json")},getScript:function(a,b){return n.get(a,void 0,b,"script")}}),n.each(["get","post"],function(a,b){n[b]=function(a,c,d,e){return n.isFunction(c)&&(e=e||d,d=c,c=void 0),n.ajax({url:a,type:b,dataType:e,data:c,success:d})}}),n.each(["ajaxStart","ajaxStop","ajaxComplete","ajaxError","ajaxSuccess","ajaxSend"],function(a,b){n.fn[b]=function(a){return this.on(b,a)}}),n._evalUrl=function(a){return n.ajax({url:a,type:"GET",dataType:"script",async:!1,global:!1,"throws":!0})},n.fn.extend({wrapAll:function(a){if(n.isFunction(a))return this.each(function(b){n(this).wrapAll(a.call(this,b))});if(this[0]){var b=n(a,this[0].ownerDocument).eq(0).clone(!0);this[0].parentNode&&b.insertBefore(this[0]),b.map(function(){var a=this;while(a.firstChild&&1===a.firstChild.nodeType)a=a.firstChild;return a}).append(this)}return this},wrapInner:function(a){return this.each(n.isFunction(a)?function(b){n(this).wrapInner(a.call(this,b))}:function(){var b=n(this),c=b.contents();c.length?c.wrapAll(a):b.append(a)})},wrap:function(a){var b=n.isFunction(a);return this.each(function(c){n(this).wrapAll(b?a.call(this,c):a)})},unwrap:function(){return this.parent().each(function(){n.nodeName(this,"body")||n(this).replaceWith(this.childNodes)}).end()}}),n.expr.filters.hidden=function(a){return a.offsetWidth<=0&&a.offsetHeight<=0||!l.reliableHiddenOffsets()&&"none"===(a.style&&a.style.display||n.css(a,"display"))},n.expr.filters.visible=function(a){return!n.expr.filters.hidden(a)};var Rc=/%20/g,Sc=/\[\]$/,Tc=/\r?\n/g,Uc=/^(?:submit|button|image|reset|file)$/i,Vc=/^(?:input|select|textarea|keygen)/i;function Wc(a,b,c,d){var e;if(n.isArray(b))n.each(b,function(b,e){c||Sc.test(a)?d(a,e):Wc(a+"["+("object"==typeof e?b:"")+"]",e,c,d)});else if(c||"object"!==n.type(b))d(a,b);else for(e in b)Wc(a+"["+e+"]",b[e],c,d)}n.param=function(a,b){var c,d=[],e=function(a,b){b=n.isFunction(b)?b():null==b?"":b,d[d.length]=encodeURIComponent(a)+"="+encodeURIComponent(b)};if(void 0===b&&(b=n.ajaxSettings&&n.ajaxSettings.traditional),n.isArray(a)||a.jquery&&!n.isPlainObject(a))n.each(a,function(){e(this.name,this.value)});else for(c in a)Wc(c,a[c],b,e);return d.join("&").replace(Rc,"+")},n.fn.extend({serialize:function(){return n.param(this.serializeArray())},serializeArray:function(){return this.map(function(){var a=n.prop(this,"elements");return a?n.makeArray(a):this}).filter(function(){var a=this.type;return this.name&&!n(this).is(":disabled")&&Vc.test(this.nodeName)&&!Uc.test(a)&&(this.checked||!X.test(a))}).map(function(a,b){var c=n(this).val();return null==c?null:n.isArray(c)?n.map(c,function(a){return{name:b.name,value:a.replace(Tc,"\r\n")}}):{name:b.name,value:c.replace(Tc,"\r\n")}}).get()}}),n.ajaxSettings.xhr=void 0!==a.ActiveXObject?function(){return!this.isLocal&&/^(get|post|head|put|delete|options)$/i.test(this.type)&&$c()||_c()}:$c;var Xc=0,Yc={},Zc=n.ajaxSettings.xhr();a.ActiveXObject&&n(a).on("unload",function(){for(var a in Yc)Yc[a](void 0,!0)}),l.cors=!!Zc&&"withCredentials"in Zc,Zc=l.ajax=!!Zc,Zc&&n.ajaxTransport(function(a){if(!a.crossDomain||l.cors){var b;return{send:function(c,d){var e,f=a.xhr(),g=++Xc;if(f.open(a.type,a.url,a.async,a.username,a.password),a.xhrFields)for(e in a.xhrFields)f[e]=a.xhrFields[e];a.mimeType&&f.overrideMimeType&&f.overrideMimeType(a.mimeType),a.crossDomain||c["X-Requested-With"]||(c["X-Requested-With"]="XMLHttpRequest");for(e in c)void 0!==c[e]&&f.setRequestHeader(e,c[e]+"");f.send(a.hasContent&&a.data||null),b=function(c,e){var h,i,j;if(b&&(e||4===f.readyState))if(delete Yc[g],b=void 0,f.onreadystatechange=n.noop,e)4!==f.readyState&&f.abort();else{j={},h=f.status,"string"==typeof f.responseText&&(j.text=f.responseText);try{i=f.statusText}catch(k){i=""}h||!a.isLocal||a.crossDomain?1223===h&&(h=204):h=j.text?200:404}j&&d(h,i,j,f.getAllResponseHeaders())},a.async?4===f.readyState?setTimeout(b):f.onreadystatechange=Yc[g]=b:b()},abort:function(){b&&b(void 0,!0)}}}});function $c(){try{return new a.XMLHttpRequest}catch(b){}}function _c(){try{return new a.ActiveXObject("Microsoft.XMLHTTP")}catch(b){}}n.ajaxSetup({accepts:{script:"text/javascript, application/javascript, application/ecmascript, application/x-ecmascript"},contents:{script:/(?:java|ecma)script/},converters:{"text script":function(a){return n.globalEval(a),a}}}),n.ajaxPrefilter("script",function(a){void 0===a.cache&&(a.cache=!1),a.crossDomain&&(a.type="GET",a.global=!1)}),n.ajaxTransport("script",function(a){if(a.crossDomain){var b,c=z.head||n("head")[0]||z.documentElement;return{send:function(d,e){b=z.createElement("script"),b.async=!0,a.scriptCharset&&(b.charset=a.scriptCharset),b.src=a.url,b.onload=b.onreadystatechange=function(a,c){(c||!b.readyState||/loaded|complete/.test(b.readyState))&&(b.onload=b.onreadystatechange=null,b.parentNode&&b.parentNode.removeChild(b),b=null,c||e(200,"success"))},c.insertBefore(b,c.firstChild)},abort:function(){b&&b.onload(void 0,!0)}}}});var ad=[],bd=/(=)\?(?=&|$)|\?\?/;n.ajaxSetup({jsonp:"callback",jsonpCallback:function(){var a=ad.pop()||n.expando+"_"+wc++;return this[a]=!0,a}}),n.ajaxPrefilter("json jsonp",function(b,c,d){var e,f,g,h=b.jsonp!==!1&&(bd.test(b.url)?"url":"string"==typeof b.data&&!(b.contentType||"").indexOf("application/x-www-form-urlencoded")&&bd.test(b.data)&&"data");return h||"jsonp"===b.dataTypes[0]?(e=b.jsonpCallback=n.isFunction(b.jsonpCallback)?b.jsonpCallback():b.jsonpCallback,h?b[h]=b[h].replace(bd,"$1"+e):b.jsonp!==!1&&(b.url+=(xc.test(b.url)?"&":"?")+b.jsonp+"="+e),b.converters["script json"]=function(){return g||n.error(e+" was not called"),g[0]},b.dataTypes[0]="json",f=a[e],a[e]=function(){g=arguments},d.always(function(){a[e]=f,b[e]&&(b.jsonpCallback=c.jsonpCallback,ad.push(e)),g&&n.isFunction(f)&&f(g[0]),g=f=void 0}),"script"):void 0}),n.parseHTML=function(a,b,c){if(!a||"string"!=typeof a)return null;"boolean"==typeof b&&(c=b,b=!1),b=b||z;var d=v.exec(a),e=!c&&[];return d?[b.createElement(d[1])]:(d=n.buildFragment([a],b,e),e&&e.length&&n(e).remove(),n.merge([],d.childNodes))};var cd=n.fn.load;n.fn.load=function(a,b,c){if("string"!=typeof a&&cd)return cd.apply(this,arguments);var d,e,f,g=this,h=a.indexOf(" ");return h>=0&&(d=a.slice(h,a.length),a=a.slice(0,h)),n.isFunction(b)?(c=b,b=void 0):b&&"object"==typeof b&&(f="POST"),g.length>0&&n.ajax({url:a,type:f,dataType:"html",data:b}).done(function(a){e=arguments,g.html(d?n("<div>").append(n.parseHTML(a)).find(d):a)}).complete(c&&function(a,b){g.each(c,e||[a.responseText,b,a])}),this},n.expr.filters.animated=function(a){return n.grep(n.timers,function(b){return a===b.elem}).length};var dd=a.document.documentElement;function ed(a){return n.isWindow(a)?a:9===a.nodeType?a.defaultView||a.parentWindow:!1}n.offset={setOffset:function(a,b,c){var d,e,f,g,h,i,j,k=n.css(a,"position"),l=n(a),m={};"static"===k&&(a.style.position="relative"),h=l.offset(),f=n.css(a,"top"),i=n.css(a,"left"),j=("absolute"===k||"fixed"===k)&&n.inArray("auto",[f,i])>-1,j?(d=l.position(),g=d.top,e=d.left):(g=parseFloat(f)||0,e=parseFloat(i)||0),n.isFunction(b)&&(b=b.call(a,c,h)),null!=b.top&&(m.top=b.top-h.top+g),null!=b.left&&(m.left=b.left-h.left+e),"using"in b?b.using.call(a,m):l.css(m)}},n.fn.extend({offset:function(a){if(arguments.length)return void 0===a?this:this.each(function(b){n.offset.setOffset(this,a,b)});var b,c,d={top:0,left:0},e=this[0],f=e&&e.ownerDocument;if(f)return b=f.documentElement,n.contains(b,e)?(typeof e.getBoundingClientRect!==L&&(d=e.getBoundingClientRect()),c=ed(f),{top:d.top+(c.pageYOffset||b.scrollTop)-(b.clientTop||0),left:d.left+(c.pageXOffset||b.scrollLeft)-(b.clientLeft||0)}):d},position:function(){if(this[0]){var a,b,c={top:0,left:0},d=this[0];return"fixed"===n.css(d,"position")?b=d.getBoundingClientRect():(a=this.offsetParent(),b=this.offset(),n.nodeName(a[0],"html")||(c=a.offset()),c.top+=n.css(a[0],"borderTopWidth",!0),c.left+=n.css(a[0],"borderLeftWidth",!0)),{top:b.top-c.top-n.css(d,"marginTop",!0),left:b.left-c.left-n.css(d,"marginLeft",!0)}}},offsetParent:function(){return this.map(function(){var a=this.offsetParent||dd;while(a&&!n.nodeName(a,"html")&&"static"===n.css(a,"position"))a=a.offsetParent;return a||dd})}}),n.each({scrollLeft:"pageXOffset",scrollTop:"pageYOffset"},function(a,b){var c=/Y/.test(b);n.fn[a]=function(d){return W(this,function(a,d,e){var f=ed(a);return void 0===e?f?b in f?f[b]:f.document.documentElement[d]:a[d]:void(f?f.scrollTo(c?n(f).scrollLeft():e,c?e:n(f).scrollTop()):a[d]=e)},a,d,arguments.length,null)}}),n.each(["top","left"],function(a,b){n.cssHooks[b]=Mb(l.pixelPosition,function(a,c){return c?(c=Kb(a,b),Ib.test(c)?n(a).position()[b]+"px":c):void 0})}),n.each({Height:"height",Width:"width"},function(a,b){n.each({padding:"inner"+a,content:b,"":"outer"+a},function(c,d){n.fn[d]=function(d,e){var f=arguments.length&&(c||"boolean"!=typeof d),g=c||(d===!0||e===!0?"margin":"border");return W(this,function(b,c,d){var e;return n.isWindow(b)?b.document.documentElement["client"+a]:9===b.nodeType?(e=b.documentElement,Math.max(b.body["scroll"+a],e["scroll"+a],b.body["offset"+a],e["offset"+a],e["client"+a])):void 0===d?n.css(b,c,g):n.style(b,c,d,g)},b,f?d:void 0,f,null)}})}),n.fn.size=function(){return this.length},n.fn.andSelf=n.fn.addBack,"function"==typeof define&&define.amd&&define("jquery",[],function(){return n});var fd=a.jQuery,gd=a.$;return n.noConflict=function(b){return a.$===n&&(a.$=gd),b&&a.jQuery===n&&(a.jQuery=fd),n},typeof b===L&&(a.jQuery=a.$=n),n});

/*!
 * Bootstrap v3.1.0 (http://getbootstrap.com)
 * Copyright 2011-2014 Twitter, Inc.
 * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
 */
if("undefined"==typeof jQuery)throw new Error("Bootstrap requires jQuery");+function(a){"use strict";function b(){var a=document.createElement("bootstrap"),b={WebkitTransition:"webkitTransitionEnd",MozTransition:"transitionend",OTransition:"oTransitionEnd otransitionend",transition:"transitionend"};for(var c in b)if(void 0!==a.style[c])return{end:b[c]};return!1}a.fn.emulateTransitionEnd=function(b){var c=!1,d=this;a(this).one(a.support.transition.end,function(){c=!0});var e=function(){c||a(d).trigger(a.support.transition.end)};return setTimeout(e,b),this},a(function(){a.support.transition=b()})}(jQuery),+function(a){"use strict";var b='[data-dismiss="alert"]',c=function(c){a(c).on("click",b,this.close)};c.prototype.close=function(b){function c(){f.trigger("closed.bs.alert").remove()}var d=a(this),e=d.attr("data-target");e||(e=d.attr("href"),e=e&&e.replace(/.*(?=#[^\s]*$)/,""));var f=a(e);b&&b.preventDefault(),f.length||(f=d.hasClass("alert")?d:d.parent()),f.trigger(b=a.Event("close.bs.alert")),b.isDefaultPrevented()||(f.removeClass("in"),a.support.transition&&f.hasClass("fade")?f.one(a.support.transition.end,c).emulateTransitionEnd(150):c())};var d=a.fn.alert;a.fn.alert=function(b){return this.each(function(){var d=a(this),e=d.data("bs.alert");e||d.data("bs.alert",e=new c(this)),"string"==typeof b&&e[b].call(d)})},a.fn.alert.Constructor=c,a.fn.alert.noConflict=function(){return a.fn.alert=d,this},a(document).on("click.bs.alert.data-api",b,c.prototype.close)}(jQuery),+function(a){"use strict";var b=function(c,d){this.$element=a(c),this.options=a.extend({},b.DEFAULTS,d),this.isLoading=!1};b.DEFAULTS={loadingText:"loading..."},b.prototype.setState=function(b){var c="disabled",d=this.$element,e=d.is("input")?"val":"html",f=d.data();b+="Text",f.resetText||d.data("resetText",d[e]()),d[e](f[b]||this.options[b]),setTimeout(a.proxy(function(){"loadingText"==b?(this.isLoading=!0,d.addClass(c).attr(c,c)):this.isLoading&&(this.isLoading=!1,d.removeClass(c).removeAttr(c))},this),0)},b.prototype.toggle=function(){var a=!0,b=this.$element.closest('[data-toggle="buttons"]');if(b.length){var c=this.$element.find("input");"radio"==c.prop("type")&&(c.prop("checked")&&this.$element.hasClass("active")?a=!1:b.find(".active").removeClass("active")),a&&c.prop("checked",!this.$element.hasClass("active")).trigger("change")}a&&this.$element.toggleClass("active")};var c=a.fn.button;a.fn.button=function(c){return this.each(function(){var d=a(this),e=d.data("bs.button"),f="object"==typeof c&&c;e||d.data("bs.button",e=new b(this,f)),"toggle"==c?e.toggle():c&&e.setState(c)})},a.fn.button.Constructor=b,a.fn.button.noConflict=function(){return a.fn.button=c,this},a(document).on("click.bs.button.data-api","[data-toggle^=button]",function(b){var c=a(b.target);c.hasClass("btn")||(c=c.closest(".btn")),c.button("toggle"),b.preventDefault()})}(jQuery),+function(a){"use strict";var b=function(b,c){this.$element=a(b),this.$indicators=this.$element.find(".carousel-indicators"),this.options=c,this.paused=this.sliding=this.interval=this.$active=this.$items=null,"hover"==this.options.pause&&this.$element.on("mouseenter",a.proxy(this.pause,this)).on("mouseleave",a.proxy(this.cycle,this))};b.DEFAULTS={interval:5e3,pause:"hover",wrap:!0},b.prototype.cycle=function(b){return b||(this.paused=!1),this.interval&&clearInterval(this.interval),this.options.interval&&!this.paused&&(this.interval=setInterval(a.proxy(this.next,this),this.options.interval)),this},b.prototype.getActiveIndex=function(){return this.$active=this.$element.find(".item.active"),this.$items=this.$active.parent().children(),this.$items.index(this.$active)},b.prototype.to=function(b){var c=this,d=this.getActiveIndex();return b>this.$items.length-1||0>b?void 0:this.sliding?this.$element.one("slid.bs.carousel",function(){c.to(b)}):d==b?this.pause().cycle():this.slide(b>d?"next":"prev",a(this.$items[b]))},b.prototype.pause=function(b){return b||(this.paused=!0),this.$element.find(".next, .prev").length&&a.support.transition&&(this.$element.trigger(a.support.transition.end),this.cycle(!0)),this.interval=clearInterval(this.interval),this},b.prototype.next=function(){return this.sliding?void 0:this.slide("next")},b.prototype.prev=function(){return this.sliding?void 0:this.slide("prev")},b.prototype.slide=function(b,c){var d=this.$element.find(".item.active"),e=c||d[b](),f=this.interval,g="next"==b?"left":"right",h="next"==b?"first":"last",i=this;if(!e.length){if(!this.options.wrap)return;e=this.$element.find(".item")[h]()}if(e.hasClass("active"))return this.sliding=!1;var j=a.Event("slide.bs.carousel",{relatedTarget:e[0],direction:g});return this.$element.trigger(j),j.isDefaultPrevented()?void 0:(this.sliding=!0,f&&this.pause(),this.$indicators.length&&(this.$indicators.find(".active").removeClass("active"),this.$element.one("slid.bs.carousel",function(){var b=a(i.$indicators.children()[i.getActiveIndex()]);b&&b.addClass("active")})),a.support.transition&&this.$element.hasClass("slide")?(e.addClass(b),e[0].offsetWidth,d.addClass(g),e.addClass(g),d.one(a.support.transition.end,function(){e.removeClass([b,g].join(" ")).addClass("active"),d.removeClass(["active",g].join(" ")),i.sliding=!1,setTimeout(function(){i.$element.trigger("slid.bs.carousel")},0)}).emulateTransitionEnd(1e3*d.css("transition-duration").slice(0,-1))):(d.removeClass("active"),e.addClass("active"),this.sliding=!1,this.$element.trigger("slid.bs.carousel")),f&&this.cycle(),this)};var c=a.fn.carousel;a.fn.carousel=function(c){return this.each(function(){var d=a(this),e=d.data("bs.carousel"),f=a.extend({},b.DEFAULTS,d.data(),"object"==typeof c&&c),g="string"==typeof c?c:f.slide;e||d.data("bs.carousel",e=new b(this,f)),"number"==typeof c?e.to(c):g?e[g]():f.interval&&e.pause().cycle()})},a.fn.carousel.Constructor=b,a.fn.carousel.noConflict=function(){return a.fn.carousel=c,this},a(document).on("click.bs.carousel.data-api","[data-slide], [data-slide-to]",function(b){var c,d=a(this),e=a(d.attr("data-target")||(c=d.attr("href"))&&c.replace(/.*(?=#[^\s]+$)/,"")),f=a.extend({},e.data(),d.data()),g=d.attr("data-slide-to");g&&(f.interval=!1),e.carousel(f),(g=d.attr("data-slide-to"))&&e.data("bs.carousel").to(g),b.preventDefault()}),a(window).on("load",function(){a('[data-ride="carousel"]').each(function(){var b=a(this);b.carousel(b.data())})})}(jQuery),+function(a){"use strict";var b=function(c,d){this.$element=a(c),this.options=a.extend({},b.DEFAULTS,d),this.transitioning=null,this.options.parent&&(this.$parent=a(this.options.parent)),this.options.toggle&&this.toggle()};b.DEFAULTS={toggle:!0},b.prototype.dimension=function(){var a=this.$element.hasClass("width");return a?"width":"height"},b.prototype.show=function(){if(!this.transitioning&&!this.$element.hasClass("in")){var b=a.Event("show.bs.collapse");if(this.$element.trigger(b),!b.isDefaultPrevented()){var c=this.$parent&&this.$parent.find("> .panel > .in");if(c&&c.length){var d=c.data("bs.collapse");if(d&&d.transitioning)return;c.collapse("hide"),d||c.data("bs.collapse",null)}var e=this.dimension();this.$element.removeClass("collapse").addClass("collapsing")[e](0),this.transitioning=1;var f=function(){this.$element.removeClass("collapsing").addClass("collapse in")[e]("auto"),this.transitioning=0,this.$element.trigger("shown.bs.collapse")};if(!a.support.transition)return f.call(this);var g=a.camelCase(["scroll",e].join("-"));this.$element.one(a.support.transition.end,a.proxy(f,this)).emulateTransitionEnd(350)[e](this.$element[0][g])}}},b.prototype.hide=function(){if(!this.transitioning&&this.$element.hasClass("in")){var b=a.Event("hide.bs.collapse");if(this.$element.trigger(b),!b.isDefaultPrevented()){var c=this.dimension();this.$element[c](this.$element[c]())[0].offsetHeight,this.$element.addClass("collapsing").removeClass("collapse").removeClass("in"),this.transitioning=1;var d=function(){this.transitioning=0,this.$element.trigger("hidden.bs.collapse").removeClass("collapsing").addClass("collapse")};return a.support.transition?void this.$element[c](0).one(a.support.transition.end,a.proxy(d,this)).emulateTransitionEnd(350):d.call(this)}}},b.prototype.toggle=function(){this[this.$element.hasClass("in")?"hide":"show"]()};var c=a.fn.collapse;a.fn.collapse=function(c){return this.each(function(){var d=a(this),e=d.data("bs.collapse"),f=a.extend({},b.DEFAULTS,d.data(),"object"==typeof c&&c);!e&&f.toggle&&"show"==c&&(c=!c),e||d.data("bs.collapse",e=new b(this,f)),"string"==typeof c&&e[c]()})},a.fn.collapse.Constructor=b,a.fn.collapse.noConflict=function(){return a.fn.collapse=c,this},a(document).on("click.bs.collapse.data-api","[data-toggle=collapse]",function(b){var c,d=a(this),e=d.attr("data-target")||b.preventDefault()||(c=d.attr("href"))&&c.replace(/.*(?=#[^\s]+$)/,""),f=a(e),g=f.data("bs.collapse"),h=g?"toggle":d.data(),i=d.attr("data-parent"),j=i&&a(i);g&&g.transitioning||(j&&j.find('[data-toggle=collapse][data-parent="'+i+'"]').not(d).addClass("collapsed"),d[f.hasClass("in")?"addClass":"removeClass"]("collapsed")),f.collapse(h)})}(jQuery),+function(a){"use strict";function b(b){a(d).remove(),a(e).each(function(){var d=c(a(this)),e={relatedTarget:this};d.hasClass("open")&&(d.trigger(b=a.Event("hide.bs.dropdown",e)),b.isDefaultPrevented()||d.removeClass("open").trigger("hidden.bs.dropdown",e))})}function c(b){var c=b.attr("data-target");c||(c=b.attr("href"),c=c&&/#[A-Za-z]/.test(c)&&c.replace(/.*(?=#[^\s]*$)/,""));var d=c&&a(c);return d&&d.length?d:b.parent()}var d=".dropdown-backdrop",e="[data-toggle=dropdown]",f=function(b){a(b).on("click.bs.dropdown",this.toggle)};f.prototype.toggle=function(d){var e=a(this);if(!e.is(".disabled, :disabled")){var f=c(e),g=f.hasClass("open");if(b(),!g){"ontouchstart"in document.documentElement&&!f.closest(".navbar-nav").length&&a('<div class="dropdown-backdrop"/>').insertAfter(a(this)).on("click",b);var h={relatedTarget:this};if(f.trigger(d=a.Event("show.bs.dropdown",h)),d.isDefaultPrevented())return;f.toggleClass("open").trigger("shown.bs.dropdown",h),e.focus()}return!1}},f.prototype.keydown=function(b){if(/(38|40|27)/.test(b.keyCode)){var d=a(this);if(b.preventDefault(),b.stopPropagation(),!d.is(".disabled, :disabled")){var f=c(d),g=f.hasClass("open");if(!g||g&&27==b.keyCode)return 27==b.which&&f.find(e).focus(),d.click();var h=" li:not(.divider):visible a",i=f.find("[role=menu]"+h+", [role=listbox]"+h);if(i.length){var j=i.index(i.filter(":focus"));38==b.keyCode&&j>0&&j--,40==b.keyCode&&j<i.length-1&&j++,~j||(j=0),i.eq(j).focus()}}}};var g=a.fn.dropdown;a.fn.dropdown=function(b){return this.each(function(){var c=a(this),d=c.data("bs.dropdown");d||c.data("bs.dropdown",d=new f(this)),"string"==typeof b&&d[b].call(c)})},a.fn.dropdown.Constructor=f,a.fn.dropdown.noConflict=function(){return a.fn.dropdown=g,this},a(document).on("click.bs.dropdown.data-api",b).on("click.bs.dropdown.data-api",".dropdown form",function(a){a.stopPropagation()}).on("click.bs.dropdown.data-api",e,f.prototype.toggle).on("keydown.bs.dropdown.data-api",e+", [role=menu], [role=listbox]",f.prototype.keydown)}(jQuery),+function(a){"use strict";var b=function(b,c){this.options=c,this.$element=a(b),this.$backdrop=this.isShown=null,this.options.remote&&this.$element.find(".modal-content").load(this.options.remote,a.proxy(function(){this.$element.trigger("loaded.bs.modal")},this))};b.DEFAULTS={backdrop:!0,keyboard:!0,show:!0},b.prototype.toggle=function(a){return this[this.isShown?"hide":"show"](a)},b.prototype.show=function(b){var c=this,d=a.Event("show.bs.modal",{relatedTarget:b});this.$element.trigger(d),this.isShown||d.isDefaultPrevented()||(this.isShown=!0,this.escape(),this.$element.on("click.dismiss.bs.modal",'[data-dismiss="modal"]',a.proxy(this.hide,this)),this.backdrop(function(){var d=a.support.transition&&c.$element.hasClass("fade");c.$element.parent().length||c.$element.appendTo(document.body),c.$element.show().scrollTop(0),d&&c.$element[0].offsetWidth,c.$element.addClass("in").attr("aria-hidden",!1),c.enforceFocus();var e=a.Event("shown.bs.modal",{relatedTarget:b});d?c.$element.find(".modal-dialog").one(a.support.transition.end,function(){c.$element.focus().trigger(e)}).emulateTransitionEnd(300):c.$element.focus().trigger(e)}))},b.prototype.hide=function(b){b&&b.preventDefault(),b=a.Event("hide.bs.modal"),this.$element.trigger(b),this.isShown&&!b.isDefaultPrevented()&&(this.isShown=!1,this.escape(),a(document).off("focusin.bs.modal"),this.$element.removeClass("in").attr("aria-hidden",!0).off("click.dismiss.bs.modal"),a.support.transition&&this.$element.hasClass("fade")?this.$element.one(a.support.transition.end,a.proxy(this.hideModal,this)).emulateTransitionEnd(300):this.hideModal())},b.prototype.enforceFocus=function(){a(document).off("focusin.bs.modal").on("focusin.bs.modal",a.proxy(function(a){this.$element[0]===a.target||this.$element.has(a.target).length||this.$element.focus()},this))},b.prototype.escape=function(){this.isShown&&this.options.keyboard?this.$element.on("keyup.dismiss.bs.modal",a.proxy(function(a){27==a.which&&this.hide()},this)):this.isShown||this.$element.off("keyup.dismiss.bs.modal")},b.prototype.hideModal=function(){var a=this;this.$element.hide(),this.backdrop(function(){a.removeBackdrop(),a.$element.trigger("hidden.bs.modal")})},b.prototype.removeBackdrop=function(){this.$backdrop&&this.$backdrop.remove(),this.$backdrop=null},b.prototype.backdrop=function(b){var c=this.$element.hasClass("fade")?"fade":"";if(this.isShown&&this.options.backdrop){var d=a.support.transition&&c;if(this.$backdrop=a('<div class="modal-backdrop '+c+'" />').appendTo(document.body),this.$element.on("click.dismiss.bs.modal",a.proxy(function(a){a.target===a.currentTarget&&("static"==this.options.backdrop?this.$element[0].focus.call(this.$element[0]):this.hide.call(this))},this)),d&&this.$backdrop[0].offsetWidth,this.$backdrop.addClass("in"),!b)return;d?this.$backdrop.one(a.support.transition.end,b).emulateTransitionEnd(150):b()}else!this.isShown&&this.$backdrop?(this.$backdrop.removeClass("in"),a.support.transition&&this.$element.hasClass("fade")?this.$backdrop.one(a.support.transition.end,b).emulateTransitionEnd(150):b()):b&&b()};var c=a.fn.modal;a.fn.modal=function(c,d){return this.each(function(){var e=a(this),f=e.data("bs.modal"),g=a.extend({},b.DEFAULTS,e.data(),"object"==typeof c&&c);f||e.data("bs.modal",f=new b(this,g)),"string"==typeof c?f[c](d):g.show&&f.show(d)})},a.fn.modal.Constructor=b,a.fn.modal.noConflict=function(){return a.fn.modal=c,this},a(document).on("click.bs.modal.data-api",'[data-toggle="modal"]',function(b){var c=a(this),d=c.attr("href"),e=a(c.attr("data-target")||d&&d.replace(/.*(?=#[^\s]+$)/,"")),f=e.data("bs.modal")?"toggle":a.extend({remote:!/#/.test(d)&&d},e.data(),c.data());c.is("a")&&b.preventDefault(),e.modal(f,this).one("hide",function(){c.is(":visible")&&c.focus()})}),a(document).on("show.bs.modal",".modal",function(){a(document.body).addClass("modal-open")}).on("hidden.bs.modal",".modal",function(){a(document.body).removeClass("modal-open")})}(jQuery),+function(a){"use strict";var b=function(a,b){this.type=this.options=this.enabled=this.timeout=this.hoverState=this.$element=null,this.init("tooltip",a,b)};b.DEFAULTS={animation:!0,placement:"top",selector:!1,template:'<div class="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>',trigger:"hover focus",title:"",delay:0,html:!1,container:!1},b.prototype.init=function(b,c,d){this.enabled=!0,this.type=b,this.$element=a(c),this.options=this.getOptions(d);for(var e=this.options.trigger.split(" "),f=e.length;f--;){var g=e[f];if("click"==g)this.$element.on("click."+this.type,this.options.selector,a.proxy(this.toggle,this));else if("manual"!=g){var h="hover"==g?"mouseenter":"focusin",i="hover"==g?"mouseleave":"focusout";this.$element.on(h+"."+this.type,this.options.selector,a.proxy(this.enter,this)),this.$element.on(i+"."+this.type,this.options.selector,a.proxy(this.leave,this))}}this.options.selector?this._options=a.extend({},this.options,{trigger:"manual",selector:""}):this.fixTitle()},b.prototype.getDefaults=function(){return b.DEFAULTS},b.prototype.getOptions=function(b){return b=a.extend({},this.getDefaults(),this.$element.data(),b),b.delay&&"number"==typeof b.delay&&(b.delay={show:b.delay,hide:b.delay}),b},b.prototype.getDelegateOptions=function(){var b={},c=this.getDefaults();return this._options&&a.each(this._options,function(a,d){c[a]!=d&&(b[a]=d)}),b},b.prototype.enter=function(b){var c=b instanceof this.constructor?b:a(b.currentTarget)[this.type](this.getDelegateOptions()).data("bs."+this.type);return clearTimeout(c.timeout),c.hoverState="in",c.options.delay&&c.options.delay.show?void(c.timeout=setTimeout(function(){"in"==c.hoverState&&c.show()},c.options.delay.show)):c.show()},b.prototype.leave=function(b){var c=b instanceof this.constructor?b:a(b.currentTarget)[this.type](this.getDelegateOptions()).data("bs."+this.type);return clearTimeout(c.timeout),c.hoverState="out",c.options.delay&&c.options.delay.hide?void(c.timeout=setTimeout(function(){"out"==c.hoverState&&c.hide()},c.options.delay.hide)):c.hide()},b.prototype.show=function(){var b=a.Event("show.bs."+this.type);if(this.hasContent()&&this.enabled){if(this.$element.trigger(b),b.isDefaultPrevented())return;var c=this,d=this.tip();this.setContent(),this.options.animation&&d.addClass("fade");var e="function"==typeof this.options.placement?this.options.placement.call(this,d[0],this.$element[0]):this.options.placement,f=/\s?auto?\s?/i,g=f.test(e);g&&(e=e.replace(f,"")||"top"),d.detach().css({top:0,left:0,display:"block"}).addClass(e),this.options.container?d.appendTo(this.options.container):d.insertAfter(this.$element);var h=this.getPosition(),i=d[0].offsetWidth,j=d[0].offsetHeight;if(g){var k=this.$element.parent(),l=e,m=document.documentElement.scrollTop||document.body.scrollTop,n="body"==this.options.container?window.innerWidth:k.outerWidth(),o="body"==this.options.container?window.innerHeight:k.outerHeight(),p="body"==this.options.container?0:k.offset().left;e="bottom"==e&&h.top+h.height+j-m>o?"top":"top"==e&&h.top-m-j<0?"bottom":"right"==e&&h.right+i>n?"left":"left"==e&&h.left-i<p?"right":e,d.removeClass(l).addClass(e)}var q=this.getCalculatedOffset(e,h,i,j);this.applyPlacement(q,e),this.hoverState=null;var r=function(){c.$element.trigger("shown.bs."+c.type)};a.support.transition&&this.$tip.hasClass("fade")?d.one(a.support.transition.end,r).emulateTransitionEnd(150):r()}},b.prototype.applyPlacement=function(b,c){var d,e=this.tip(),f=e[0].offsetWidth,g=e[0].offsetHeight,h=parseInt(e.css("margin-top"),10),i=parseInt(e.css("margin-left"),10);isNaN(h)&&(h=0),isNaN(i)&&(i=0),b.top=b.top+h,b.left=b.left+i,a.offset.setOffset(e[0],a.extend({using:function(a){e.css({top:Math.round(a.top),left:Math.round(a.left)})}},b),0),e.addClass("in");var j=e[0].offsetWidth,k=e[0].offsetHeight;if("top"==c&&k!=g&&(d=!0,b.top=b.top+g-k),/bottom|top/.test(c)){var l=0;b.left<0&&(l=-2*b.left,b.left=0,e.offset(b),j=e[0].offsetWidth,k=e[0].offsetHeight),this.replaceArrow(l-f+j,j,"left")}else this.replaceArrow(k-g,k,"top");d&&e.offset(b)},b.prototype.replaceArrow=function(a,b,c){this.arrow().css(c,a?50*(1-a/b)+"%":"")},b.prototype.setContent=function(){var a=this.tip(),b=this.getTitle();a.find(".tooltip-inner")[this.options.html?"html":"text"](b),a.removeClass("fade in top bottom left right")},b.prototype.hide=function(){function b(){"in"!=c.hoverState&&d.detach(),c.$element.trigger("hidden.bs."+c.type)}var c=this,d=this.tip(),e=a.Event("hide.bs."+this.type);return this.$element.trigger(e),e.isDefaultPrevented()?void 0:(d.removeClass("in"),a.support.transition&&this.$tip.hasClass("fade")?d.one(a.support.transition.end,b).emulateTransitionEnd(150):b(),this.hoverState=null,this)},b.prototype.fixTitle=function(){var a=this.$element;(a.attr("title")||"string"!=typeof a.attr("data-original-title"))&&a.attr("data-original-title",a.attr("title")||"").attr("title","")},b.prototype.hasContent=function(){return this.getTitle()},b.prototype.getPosition=function(){var b=this.$element[0];return a.extend({},"function"==typeof b.getBoundingClientRect?b.getBoundingClientRect():{width:b.offsetWidth,height:b.offsetHeight},this.$element.offset())},b.prototype.getCalculatedOffset=function(a,b,c,d){return"bottom"==a?{top:b.top+b.height,left:b.left+b.width/2-c/2}:"top"==a?{top:b.top-d,left:b.left+b.width/2-c/2}:"left"==a?{top:b.top+b.height/2-d/2,left:b.left-c}:{top:b.top+b.height/2-d/2,left:b.left+b.width}},b.prototype.getTitle=function(){var a,b=this.$element,c=this.options;return a=b.attr("data-original-title")||("function"==typeof c.title?c.title.call(b[0]):c.title)},b.prototype.tip=function(){return this.$tip=this.$tip||a(this.options.template)},b.prototype.arrow=function(){return this.$arrow=this.$arrow||this.tip().find(".tooltip-arrow")},b.prototype.validate=function(){this.$element[0].parentNode||(this.hide(),this.$element=null,this.options=null)},b.prototype.enable=function(){this.enabled=!0},b.prototype.disable=function(){this.enabled=!1},b.prototype.toggleEnabled=function(){this.enabled=!this.enabled},b.prototype.toggle=function(b){var c=b?a(b.currentTarget)[this.type](this.getDelegateOptions()).data("bs."+this.type):this;c.tip().hasClass("in")?c.leave(c):c.enter(c)},b.prototype.destroy=function(){clearTimeout(this.timeout),this.hide().$element.off("."+this.type).removeData("bs."+this.type)};var c=a.fn.tooltip;a.fn.tooltip=function(c){return this.each(function(){var d=a(this),e=d.data("bs.tooltip"),f="object"==typeof c&&c;(e||"destroy"!=c)&&(e||d.data("bs.tooltip",e=new b(this,f)),"string"==typeof c&&e[c]())})},a.fn.tooltip.Constructor=b,a.fn.tooltip.noConflict=function(){return a.fn.tooltip=c,this}}(jQuery),+function(a){"use strict";var b=function(a,b){this.init("popover",a,b)};if(!a.fn.tooltip)throw new Error("Popover requires tooltip.js");b.DEFAULTS=a.extend({},a.fn.tooltip.Constructor.DEFAULTS,{placement:"right",trigger:"click",content:"",template:'<div class="popover"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>'}),b.prototype=a.extend({},a.fn.tooltip.Constructor.prototype),b.prototype.constructor=b,b.prototype.getDefaults=function(){return b.DEFAULTS},b.prototype.setContent=function(){var a=this.tip(),b=this.getTitle(),c=this.getContent();a.find(".popover-title")[this.options.html?"html":"text"](b),a.find(".popover-content")[this.options.html?"string"==typeof c?"html":"append":"text"](c),a.removeClass("fade top bottom left right in"),a.find(".popover-title").html()||a.find(".popover-title").hide()},b.prototype.hasContent=function(){return this.getTitle()||this.getContent()},b.prototype.getContent=function(){var a=this.$element,b=this.options;return a.attr("data-content")||("function"==typeof b.content?b.content.call(a[0]):b.content)},b.prototype.arrow=function(){return this.$arrow=this.$arrow||this.tip().find(".arrow")},b.prototype.tip=function(){return this.$tip||(this.$tip=a(this.options.template)),this.$tip};var c=a.fn.popover;a.fn.popover=function(c){return this.each(function(){var d=a(this),e=d.data("bs.popover"),f="object"==typeof c&&c;(e||"destroy"!=c)&&(e||d.data("bs.popover",e=new b(this,f)),"string"==typeof c&&e[c]())})},a.fn.popover.Constructor=b,a.fn.popover.noConflict=function(){return a.fn.popover=c,this}}(jQuery),+function(a){"use strict";function b(c,d){var e,f=a.proxy(this.process,this);this.$element=a(a(c).is("body")?window:c),this.$body=a("body"),this.$scrollElement=this.$element.on("scroll.bs.scroll-spy.data-api",f),this.options=a.extend({},b.DEFAULTS,d),this.selector=(this.options.target||(e=a(c).attr("href"))&&e.replace(/.*(?=#[^\s]+$)/,"")||"")+" .nav li > a",this.offsets=a([]),this.targets=a([]),this.activeTarget=null,this.refresh(),this.process()}b.DEFAULTS={offset:10},b.prototype.refresh=function(){var b=this.$element[0]==window?"offset":"position";this.offsets=a([]),this.targets=a([]);{var c=this;this.$body.find(this.selector).map(function(){var d=a(this),e=d.data("target")||d.attr("href"),f=/^#./.test(e)&&a(e);return f&&f.length&&f.is(":visible")&&[[f[b]().top+(!a.isWindow(c.$scrollElement.get(0))&&c.$scrollElement.scrollTop()),e]]||null}).sort(function(a,b){return a[0]-b[0]}).each(function(){c.offsets.push(this[0]),c.targets.push(this[1])})}},b.prototype.process=function(){var a,b=this.$scrollElement.scrollTop()+this.options.offset,c=this.$scrollElement[0].scrollHeight||this.$body[0].scrollHeight,d=c-this.$scrollElement.height(),e=this.offsets,f=this.targets,g=this.activeTarget;if(b>=d)return g!=(a=f.last()[0])&&this.activate(a);if(g&&b<=e[0])return g!=(a=f[0])&&this.activate(a);for(a=e.length;a--;)g!=f[a]&&b>=e[a]&&(!e[a+1]||b<=e[a+1])&&this.activate(f[a])},b.prototype.activate=function(b){this.activeTarget=b,a(this.selector).parentsUntil(this.options.target,".active").removeClass("active");var c=this.selector+'[data-target="'+b+'"],'+this.selector+'[href="'+b+'"]',d=a(c).parents("li").addClass("active");d.parent(".dropdown-menu").length&&(d=d.closest("li.dropdown").addClass("active")),d.trigger("activate.bs.scrollspy")};var c=a.fn.scrollspy;a.fn.scrollspy=function(c){return this.each(function(){var d=a(this),e=d.data("bs.scrollspy"),f="object"==typeof c&&c;e||d.data("bs.scrollspy",e=new b(this,f)),"string"==typeof c&&e[c]()})},a.fn.scrollspy.Constructor=b,a.fn.scrollspy.noConflict=function(){return a.fn.scrollspy=c,this},a(window).on("load",function(){a('[data-spy="scroll"]').each(function(){var b=a(this);b.scrollspy(b.data())})})}(jQuery),+function(a){"use strict";var b=function(b){this.element=a(b)};b.prototype.show=function(){var b=this.element,c=b.closest("ul:not(.dropdown-menu)"),d=b.data("target");if(d||(d=b.attr("href"),d=d&&d.replace(/.*(?=#[^\s]*$)/,"")),!b.parent("li").hasClass("active")){var e=c.find(".active:last a")[0],f=a.Event("show.bs.tab",{relatedTarget:e});if(b.trigger(f),!f.isDefaultPrevented()){var g=a(d);this.activate(b.parent("li"),c),this.activate(g,g.parent(),function(){b.trigger({type:"shown.bs.tab",relatedTarget:e})})}}},b.prototype.activate=function(b,c,d){function e(){f.removeClass("active").find("> .dropdown-menu > .active").removeClass("active"),b.addClass("active"),g?(b[0].offsetWidth,b.addClass("in")):b.removeClass("fade"),b.parent(".dropdown-menu")&&b.closest("li.dropdown").addClass("active"),d&&d()}var f=c.find("> .active"),g=d&&a.support.transition&&f.hasClass("fade");g?f.one(a.support.transition.end,e).emulateTransitionEnd(150):e(),f.removeClass("in")};var c=a.fn.tab;a.fn.tab=function(c){return this.each(function(){var d=a(this),e=d.data("bs.tab");e||d.data("bs.tab",e=new b(this)),"string"==typeof c&&e[c]()})},a.fn.tab.Constructor=b,a.fn.tab.noConflict=function(){return a.fn.tab=c,this},a(document).on("click.bs.tab.data-api",'[data-toggle="tab"], [data-toggle="pill"]',function(b){b.preventDefault(),a(this).tab("show")})}(jQuery),+function(a){"use strict";var b=function(c,d){this.options=a.extend({},b.DEFAULTS,d),this.$window=a(window).on("scroll.bs.affix.data-api",a.proxy(this.checkPosition,this)).on("click.bs.affix.data-api",a.proxy(this.checkPositionWithEventLoop,this)),this.$element=a(c),this.affixed=this.unpin=this.pinnedOffset=null,this.checkPosition()};b.RESET="affix affix-top affix-bottom",b.DEFAULTS={offset:0},b.prototype.getPinnedOffset=function(){if(this.pinnedOffset)return this.pinnedOffset;this.$element.removeClass(b.RESET).addClass("affix");var a=this.$window.scrollTop(),c=this.$element.offset();return this.pinnedOffset=c.top-a},b.prototype.checkPositionWithEventLoop=function(){setTimeout(a.proxy(this.checkPosition,this),1)},b.prototype.checkPosition=function(){if(this.$element.is(":visible")){var c=a(document).height(),d=this.$window.scrollTop(),e=this.$element.offset(),f=this.options.offset,g=f.top,h=f.bottom;"top"==this.affixed&&(e.top+=d),"object"!=typeof f&&(h=g=f),"function"==typeof g&&(g=f.top(this.$element)),"function"==typeof h&&(h=f.bottom(this.$element));var i=null!=this.unpin&&d+this.unpin<=e.top?!1:null!=h&&e.top+this.$element.height()>=c-h?"bottom":null!=g&&g>=d?"top":!1;if(this.affixed!==i){this.unpin&&this.$element.css("top","");var j="affix"+(i?"-"+i:""),k=a.Event(j+".bs.affix");this.$element.trigger(k),k.isDefaultPrevented()||(this.affixed=i,this.unpin="bottom"==i?this.getPinnedOffset():null,this.$element.removeClass(b.RESET).addClass(j).trigger(a.Event(j.replace("affix","affixed"))),"bottom"==i&&this.$element.offset({top:c-h-this.$element.height()}))}}};var c=a.fn.affix;a.fn.affix=function(c){return this.each(function(){var d=a(this),e=d.data("bs.affix"),f="object"==typeof c&&c;e||d.data("bs.affix",e=new b(this,f)),"string"==typeof c&&e[c]()})},a.fn.affix.Constructor=b,a.fn.affix.noConflict=function(){return a.fn.affix=c,this},a(window).on("load",function(){a('[data-spy="affix"]').each(function(){var b=a(this),c=b.data();c.offset=c.offset||{},c.offsetBottom&&(c.offset.bottom=c.offsetBottom),c.offsetTop&&(c.offset.top=c.offsetTop),b.affix(c)})})}(jQuery);
/* =========================================================
 * bootstrap-datepicker.js
 * Repo: https://github.com/eternicode/bootstrap-datepicker/
 * Demo: http://eternicode.github.io/bootstrap-datepicker/
 * Docs: http://bootstrap-datepicker.readthedocs.org/
 * Forked from http://www.eyecon.ro/bootstrap-datepicker
 * =========================================================
 * Started by Stefan Petre; improvements by Andrew Rowls + contributors
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ========================================================= */

(function($, undefined){

	var $window = $(window);

	function UTCDate(){
		return new Date(Date.UTC.apply(Date, arguments));
	}
	function UTCToday(){
		var today = new Date();
		return UTCDate(today.getFullYear(), today.getMonth(), today.getDate());
	}
	function alias(method){
		return function(){
			return this[method].apply(this, arguments);
		};
	}

	var DateArray = (function(){
		var extras = {
			get: function(i){
				return this.slice(i)[0];
			},
			contains: function(d){
				// Array.indexOf is not cross-browser;
				// $.inArray doesn't work with Dates
				var val = d && d.valueOf();
				for (var i=0, l=this.length; i < l; i++)
					if (this[i].valueOf() === val)
						return i;
				return -1;
			},
			remove: function(i){
				this.splice(i,1);
			},
			replace: function(new_array){
				if (!new_array)
					return;
				if (!$.isArray(new_array))
					new_array = [new_array];
				this.clear();
				this.push.apply(this, new_array);
			},
			clear: function(){
				this.length = 0;
			},
			copy: function(){
				var a = new DateArray();
				a.replace(this);
				return a;
			}
		};

		return function(){
			var a = [];
			a.push.apply(a, arguments);
			$.extend(a, extras);
			return a;
		};
	})();


	// Picker object

	var Datepicker = function(element, options){
		this.dates = new DateArray();
		this.viewDate = UTCToday();
		this.focusDate = null;

		this._process_options(options);

		this.element = $(element);
		this.isInline = false;
		this.isInput = this.element.is('input');
		this.component = this.element.is('.date') ? this.element.find('.add-on, .input-group-addon, .btn') : false;
		this.hasInput = this.component && this.element.find('input').length;
		if (this.component && this.component.length === 0)
			this.component = false;

		this.picker = $(DPGlobal.template);
		this._buildEvents();
		this._attachEvents();

		if (this.isInline){
			this.picker.addClass('datepicker-inline').appendTo(this.element);
		}
		else {
			this.picker.addClass('datepicker-dropdown dropdown-menu');
		}

		if (this.o.rtl){
			this.picker.addClass('datepicker-rtl');
		}

		this.viewMode = this.o.startView;

		if (this.o.calendarWeeks)
			this.picker.find('tfoot th.today')
						.attr('colspan', function(i, val){
							return parseInt(val) + 1;
						});

		this._allow_update = false;

		this.setStartDate(this._o.startDate);
		this.setEndDate(this._o.endDate);
		this.setDaysOfWeekDisabled(this.o.daysOfWeekDisabled);

		this.fillDow();
		this.fillMonths();

		this._allow_update = true;

		this.update();
		this.showMode();

		if (this.isInline){
			this.show();
		}
	};

	Datepicker.prototype = {
		constructor: Datepicker,

		_process_options: function(opts){
			// Store raw options for reference
			this._o = $.extend({}, this._o, opts);
			// Processed options
			var o = this.o = $.extend({}, this._o);

			// Check if "de-DE" style date is available, if not language should
			// fallback to 2 letter code eg "de"
			var lang = o.language;
			if (!dates[lang]){
				lang = lang.split('-')[0];
				if (!dates[lang])
					lang = defaults.language;
			}
			o.language = lang;

			switch (o.startView){
				case 2:
				case 'decade':
					o.startView = 2;
					break;
				case 1:
				case 'year':
					o.startView = 1;
					break;
				default:
					o.startView = 0;
			}

			switch (o.minViewMode){
				case 1:
				case 'months':
					o.minViewMode = 1;
					break;
				case 2:
				case 'years':
					o.minViewMode = 2;
					break;
				default:
					o.minViewMode = 0;
			}

			o.startView = Math.max(o.startView, o.minViewMode);

			// true, false, or Number > 0
			if (o.multidate !== true){
				o.multidate = Number(o.multidate) || false;
				if (o.multidate !== false)
					o.multidate = Math.max(0, o.multidate);
				else
					o.multidate = 1;
			}
			o.multidateSeparator = String(o.multidateSeparator);

			o.weekStart %= 7;
			o.weekEnd = ((o.weekStart + 6) % 7);

			var format = DPGlobal.parseFormat(o.format);
			if (o.startDate !== -Infinity){
				if (!!o.startDate){
					if (o.startDate instanceof Date)
						o.startDate = this._local_to_utc(this._zero_time(o.startDate));
					else
						o.startDate = DPGlobal.parseDate(o.startDate, format, o.language);
				}
				else {
					o.startDate = -Infinity;
				}
			}
			if (o.endDate !== Infinity){
				if (!!o.endDate){
					if (o.endDate instanceof Date)
						o.endDate = this._local_to_utc(this._zero_time(o.endDate));
					else
						o.endDate = DPGlobal.parseDate(o.endDate, format, o.language);
				}
				else {
					o.endDate = Infinity;
				}
			}

			o.daysOfWeekDisabled = o.daysOfWeekDisabled||[];
			if (!$.isArray(o.daysOfWeekDisabled))
				o.daysOfWeekDisabled = o.daysOfWeekDisabled.split(/[,\s]*/);
			o.daysOfWeekDisabled = $.map(o.daysOfWeekDisabled, function(d){
				return parseInt(d, 10);
			});

			var plc = String(o.orientation).toLowerCase().split(/\s+/g),
				_plc = o.orientation.toLowerCase();
			plc = $.grep(plc, function(word){
				return (/^auto|left|right|top|bottom$/).test(word);
			});
			o.orientation = {x: 'auto', y: 'auto'};
			if (!_plc || _plc === 'auto')
				; // no action
			else if (plc.length === 1){
				switch (plc[0]){
					case 'top':
					case 'bottom':
						o.orientation.y = plc[0];
						break;
					case 'left':
					case 'right':
						o.orientation.x = plc[0];
						break;
				}
			}
			else {
				_plc = $.grep(plc, function(word){
					return (/^left|right$/).test(word);
				});
				o.orientation.x = _plc[0] || 'auto';

				_plc = $.grep(plc, function(word){
					return (/^top|bottom$/).test(word);
				});
				o.orientation.y = _plc[0] || 'auto';
			}
		},
		_events: [],
		_secondaryEvents: [],
		_applyEvents: function(evs){
			for (var i=0, el, ch, ev; i < evs.length; i++){
				el = evs[i][0];
				if (evs[i].length === 2){
					ch = undefined;
					ev = evs[i][1];
				}
				else if (evs[i].length === 3){
					ch = evs[i][1];
					ev = evs[i][2];
				}
				el.on(ev, ch);
			}
		},
		_unapplyEvents: function(evs){
			for (var i=0, el, ev, ch; i < evs.length; i++){
				el = evs[i][0];
				if (evs[i].length === 2){
					ch = undefined;
					ev = evs[i][1];
				}
				else if (evs[i].length === 3){
					ch = evs[i][1];
					ev = evs[i][2];
				}
				el.off(ev, ch);
			}
		},
		_buildEvents: function(){
			if (this.isInput){ // single input
				this._events = [
					[this.element, {
						focus: $.proxy(this.show, this),
						keyup: $.proxy(function(e){
							if ($.inArray(e.keyCode, [27,37,39,38,40,32,13,9]) === -1)
								this.update();
						}, this),
						keydown: $.proxy(this.keydown, this)
					}]
				];
			}
			else if (this.component && this.hasInput){ // component: input + button
				this._events = [
					// For components that are not readonly, allow keyboard nav
					[this.element.find('input'), {
						focus: $.proxy(this.show, this),
						keyup: $.proxy(function(e){
							if ($.inArray(e.keyCode, [27,37,39,38,40,32,13,9]) === -1)
								this.update();
						}, this),
						keydown: $.proxy(this.keydown, this)
					}],
					[this.component, {
						click: $.proxy(this.show, this)
					}]
				];
			}
			else if (this.element.is('div')){  // inline datepicker
				this.isInline = true;
			}
			else {
				this._events = [
					[this.element, {
						click: $.proxy(this.show, this)
					}]
				];
			}
			this._events.push(
				// Component: listen for blur on element descendants
				[this.element, '*', {
					blur: $.proxy(function(e){
						this._focused_from = e.target;
					}, this)
				}],
				// Input: listen for blur on element
				[this.element, {
					blur: $.proxy(function(e){
						this._focused_from = e.target;
					}, this)
				}]
			);

			this._secondaryEvents = [
				[this.picker, {
					click: $.proxy(this.click, this)
				}],
				[$(window), {
					resize: $.proxy(this.place, this)
				}],
				[$(document), {
					'mousedown touchstart': $.proxy(function(e){
						// Clicked outside the datepicker, hide it
						if (!(
							this.element.is(e.target) ||
							this.element.find(e.target).length ||
							this.picker.is(e.target) ||
							this.picker.find(e.target).length
						)){
							this.hide();
						}
					}, this)
				}]
			];
		},
		_attachEvents: function(){
			this._detachEvents();
			this._applyEvents(this._events);
		},
		_detachEvents: function(){
			this._unapplyEvents(this._events);
		},
		_attachSecondaryEvents: function(){
			this._detachSecondaryEvents();
			this._applyEvents(this._secondaryEvents);
		},
		_detachSecondaryEvents: function(){
			this._unapplyEvents(this._secondaryEvents);
		},
		_trigger: function(event, altdate){
			var date = altdate || this.dates.get(-1),
				local_date = this._utc_to_local(date);

			this.element.trigger({
				type: event,
				date: local_date,
				dates: $.map(this.dates, this._utc_to_local),
				format: $.proxy(function(ix, format){
					if (arguments.length === 0){
						ix = this.dates.length - 1;
						format = this.o.format;
					}
					else if (typeof ix === 'string'){
						format = ix;
						ix = this.dates.length - 1;
					}
					format = format || this.o.format;
					var date = this.dates.get(ix);
					return DPGlobal.formatDate(date, format, this.o.language);
				}, this)
			});
		},

		show: function(){
			if (!this.isInline)
				this.picker.appendTo('body');
			this.picker.show();
			this.place();
			this._attachSecondaryEvents();
			this._trigger('show');
		},

		hide: function(){
			if (this.isInline)
				return;
			if (!this.picker.is(':visible'))
				return;
			this.focusDate = null;
			this.picker.hide().detach();
			this._detachSecondaryEvents();
			this.viewMode = this.o.startView;
			this.showMode();

			if (
				this.o.forceParse &&
				(
					this.isInput && this.element.val() ||
					this.hasInput && this.element.find('input').val()
				)
			)
				this.setValue();
			this._trigger('hide');
		},

		remove: function(){
			this.hide();
			this._detachEvents();
			this._detachSecondaryEvents();
			this.picker.remove();
			delete this.element.data().datepicker;
			if (!this.isInput){
				delete this.element.data().date;
			}
		},

		_utc_to_local: function(utc){
			return utc && new Date(utc.getTime() + (utc.getTimezoneOffset()*60000));
		},
		_local_to_utc: function(local){
			return local && new Date(local.getTime() - (local.getTimezoneOffset()*60000));
		},
		_zero_time: function(local){
			return local && new Date(local.getFullYear(), local.getMonth(), local.getDate());
		},
		_zero_utc_time: function(utc){
			return utc && new Date(Date.UTC(utc.getUTCFullYear(), utc.getUTCMonth(), utc.getUTCDate()));
		},

		getDates: function(){
			return $.map(this.dates, this._utc_to_local);
		},

		getUTCDates: function(){
			return $.map(this.dates, function(d){
				return new Date(d);
			});
		},

		getDate: function(){
			return this._utc_to_local(this.getUTCDate());
		},

		getUTCDate: function(){
			return new Date(this.dates.get(-1));
		},

		setDates: function(){
			var args = $.isArray(arguments[0]) ? arguments[0] : arguments;
			this.update.apply(this, args);
			this._trigger('changeDate');
			this.setValue();
		},

		setUTCDates: function(){
			var args = $.isArray(arguments[0]) ? arguments[0] : arguments;
			this.update.apply(this, $.map(args, this._utc_to_local));
			this._trigger('changeDate');
			this.setValue();
		},

		setDate: alias('setDates'),
		setUTCDate: alias('setUTCDates'),

		setValue: function(){
			var formatted = this.getFormattedDate();
			if (!this.isInput){
				if (this.component){
					this.element.find('input').val(formatted).change();
				}
			}
			else {
				this.element.val(formatted).change();
			}
		},

		getFormattedDate: function(format){
			if (format === undefined)
				format = this.o.format;

			var lang = this.o.language;
			return $.map(this.dates, function(d){
				return DPGlobal.formatDate(d, format, lang);
			}).join(this.o.multidateSeparator);
		},

		setStartDate: function(startDate){
			this._process_options({startDate: startDate});
			this.update();
			this.updateNavArrows();
		},

		setEndDate: function(endDate){
			this._process_options({endDate: endDate});
			this.update();
			this.updateNavArrows();
		},

		setDaysOfWeekDisabled: function(daysOfWeekDisabled){
			this._process_options({daysOfWeekDisabled: daysOfWeekDisabled});
			this.update();
			this.updateNavArrows();
		},

		place: function(){
			if (this.isInline)
				return;
			var calendarWidth = this.picker.outerWidth(),
				calendarHeight = this.picker.outerHeight(),
				visualPadding = 10,
				windowWidth = $window.width(),
				windowHeight = $window.height(),
				scrollTop = $window.scrollTop();

			var zIndex = parseInt(this.element.parents().filter(function(){
					return $(this).css('z-index') !== 'auto';
				}).first().css('z-index'))+10;
			var offset = this.component ? this.component.parent().offset() : this.element.offset();
			var height = this.component ? this.component.outerHeight(true) : this.element.outerHeight(false);
			var width = this.component ? this.component.outerWidth(true) : this.element.outerWidth(false);
			var left = offset.left,
				top = offset.top;

			this.picker.removeClass(
				'datepicker-orient-top datepicker-orient-bottom '+
				'datepicker-orient-right datepicker-orient-left'
			);

			if (this.o.orientation.x !== 'auto'){
				this.picker.addClass('datepicker-orient-' + this.o.orientation.x);
				if (this.o.orientation.x === 'right')
					left -= calendarWidth - width;
			}
			// auto x orientation is best-placement: if it crosses a window
			// edge, fudge it sideways
			else {
				// Default to left
				this.picker.addClass('datepicker-orient-left');
				if (offset.left < 0)
					left -= offset.left - visualPadding;
				else if (offset.left + calendarWidth > windowWidth)
					left = windowWidth - calendarWidth - visualPadding;
			}

			// auto y orientation is best-situation: top or bottom, no fudging,
			// decision based on which shows more of the calendar
			var yorient = this.o.orientation.y,
				top_overflow, bottom_overflow;
			if (yorient === 'auto'){
				top_overflow = -scrollTop + offset.top - calendarHeight;
				bottom_overflow = scrollTop + windowHeight - (offset.top + height + calendarHeight);
				if (Math.max(top_overflow, bottom_overflow) === bottom_overflow)
					yorient = 'top';
				else
					yorient = 'bottom';
			}
			this.picker.addClass('datepicker-orient-' + yorient);
			if (yorient === 'top')
				top += height;
			else
				top -= calendarHeight + parseInt(this.picker.css('padding-top'));

			this.picker.css({
				top: top,
				left: left,
				zIndex: zIndex
			});
		},

		_allow_update: true,
		update: function(){
			if (!this._allow_update)
				return;

			var oldDates = this.dates.copy(),
				dates = [],
				fromArgs = false;
			if (arguments.length){
				$.each(arguments, $.proxy(function(i, date){
					if (date instanceof Date)
						date = this._local_to_utc(date);
					dates.push(date);
				}, this));
				fromArgs = true;
			}
			else {
				dates = this.isInput
						? this.element.val()
						: this.element.data('date') || this.element.find('input').val();
				if (dates && this.o.multidate)
					dates = dates.split(this.o.multidateSeparator);
				else
					dates = [dates];
				delete this.element.data().date;
			}

			dates = $.map(dates, $.proxy(function(date){
				return DPGlobal.parseDate(date, this.o.format, this.o.language);
			}, this));
			dates = $.grep(dates, $.proxy(function(date){
				return (
					date < this.o.startDate ||
					date > this.o.endDate ||
					!date
				);
			}, this), true);
			this.dates.replace(dates);

			if (this.dates.length)
				this.viewDate = new Date(this.dates.get(-1));
			else if (this.viewDate < this.o.startDate)
				this.viewDate = new Date(this.o.startDate);
			else if (this.viewDate > this.o.endDate)
				this.viewDate = new Date(this.o.endDate);

			if (fromArgs){
				// setting date by clicking
				this.setValue();
			}
			else if (dates.length){
				// setting date by typing
				if (String(oldDates) !== String(this.dates))
					this._trigger('changeDate');
			}
			if (!this.dates.length && oldDates.length)
				this._trigger('clearDate');

			this.fill();
		},

		fillDow: function(){
			var dowCnt = this.o.weekStart,
				html = '<tr>';
			if (this.o.calendarWeeks){
				var cell = '<th class="cw">&nbsp;</th>';
				html += cell;
				this.picker.find('.datepicker-days thead tr:first-child').prepend(cell);
			}
			while (dowCnt < this.o.weekStart + 7){
				html += '<th class="dow">'+dates[this.o.language].daysMin[(dowCnt++)%7]+'</th>';
			}
			html += '</tr>';
			this.picker.find('.datepicker-days thead').append(html);
		},

		fillMonths: function(){
			var html = '',
			i = 0;
			while (i < 12){
				html += '<span class="month">'+dates[this.o.language].monthsShort[i++]+'</span>';
			}
			this.picker.find('.datepicker-months td').html(html);
		},

		setRange: function(range){
			if (!range || !range.length)
				delete this.range;
			else
				this.range = $.map(range, function(d){
					return d.valueOf();
				});
			this.fill();
		},

		getClassNames: function(date){
			var cls = [],
				year = this.viewDate.getUTCFullYear(),
				month = this.viewDate.getUTCMonth(),
				today = new Date();
			if (date.getUTCFullYear() < year || (date.getUTCFullYear() === year && date.getUTCMonth() < month)){
				cls.push('old');
			}
			else if (date.getUTCFullYear() > year || (date.getUTCFullYear() === year && date.getUTCMonth() > month)){
				cls.push('new');
			}
			if (this.focusDate && date.valueOf() === this.focusDate.valueOf())
				cls.push('focused');
			// Compare internal UTC date with local today, not UTC today
			if (this.o.todayHighlight &&
				date.getUTCFullYear() === today.getFullYear() &&
				date.getUTCMonth() === today.getMonth() &&
				date.getUTCDate() === today.getDate()){
				cls.push('today');
			}
			if (this.dates.contains(date) !== -1)
				cls.push('active');
			if (date.valueOf() < this.o.startDate || date.valueOf() > this.o.endDate ||
				$.inArray(date.getUTCDay(), this.o.daysOfWeekDisabled) !== -1){
				cls.push('disabled');
			}
			if (this.range){
				if (date > this.range[0] && date < this.range[this.range.length-1]){
					cls.push('range');
				}
				if ($.inArray(date.valueOf(), this.range) !== -1){
					cls.push('selected');
				}
			}
			return cls;
		},

		fill: function(){
			var d = new Date(this.viewDate),
				year = d.getUTCFullYear(),
				month = d.getUTCMonth(),
				startYear = this.o.startDate !== -Infinity ? this.o.startDate.getUTCFullYear() : -Infinity,
				startMonth = this.o.startDate !== -Infinity ? this.o.startDate.getUTCMonth() : -Infinity,
				endYear = this.o.endDate !== Infinity ? this.o.endDate.getUTCFullYear() : Infinity,
				endMonth = this.o.endDate !== Infinity ? this.o.endDate.getUTCMonth() : Infinity,
				todaytxt = dates[this.o.language].today || dates['en'].today || '',
				cleartxt = dates[this.o.language].clear || dates['en'].clear || '',
				tooltip;
			this.picker.find('.datepicker-days thead th.datepicker-switch')
						.text(dates[this.o.language].months[month]+' '+year);
			this.picker.find('tfoot th.today')
						.text(todaytxt)
						.toggle(this.o.todayBtn !== false);
			this.picker.find('tfoot th.clear')
						.text(cleartxt)
						.toggle(this.o.clearBtn !== false);
			this.updateNavArrows();
			this.fillMonths();
			var prevMonth = UTCDate(year, month-1, 28),
				day = DPGlobal.getDaysInMonth(prevMonth.getUTCFullYear(), prevMonth.getUTCMonth());
			prevMonth.setUTCDate(day);
			prevMonth.setUTCDate(day - (prevMonth.getUTCDay() - this.o.weekStart + 7)%7);
			var nextMonth = new Date(prevMonth);
			nextMonth.setUTCDate(nextMonth.getUTCDate() + 42);
			nextMonth = nextMonth.valueOf();
			var html = [];
			var clsName;
			while (prevMonth.valueOf() < nextMonth){
				if (prevMonth.getUTCDay() === this.o.weekStart){
					html.push('<tr>');
					if (this.o.calendarWeeks){
						// ISO 8601: First week contains first thursday.
						// ISO also states week starts on Monday, but we can be more abstract here.
						var
							// Start of current week: based on weekstart/current date
							ws = new Date(+prevMonth + (this.o.weekStart - prevMonth.getUTCDay() - 7) % 7 * 864e5),
							// Thursday of this week
							th = new Date(Number(ws) + (7 + 4 - ws.getUTCDay()) % 7 * 864e5),
							// First Thursday of year, year from thursday
							yth = new Date(Number(yth = UTCDate(th.getUTCFullYear(), 0, 1)) + (7 + 4 - yth.getUTCDay())%7*864e5),
							// Calendar week: ms between thursdays, div ms per day, div 7 days
							calWeek =  (th - yth) / 864e5 / 7 + 1;
						html.push('<td class="cw">'+ calWeek +'</td>');

					}
				}
				clsName = this.getClassNames(prevMonth);
				clsName.push('day');

				if (this.o.beforeShowDay !== $.noop){
					var before = this.o.beforeShowDay(this._utc_to_local(prevMonth));
					if (before === undefined)
						before = {};
					else if (typeof(before) === 'boolean')
						before = {enabled: before};
					else if (typeof(before) === 'string')
						before = {classes: before};
					if (before.enabled === false)
						clsName.push('disabled');
					if (before.classes)
						clsName = clsName.concat(before.classes.split(/\s+/));
					if (before.tooltip)
						tooltip = before.tooltip;
				}

				clsName = $.unique(clsName);
				html.push('<td class="'+clsName.join(' ')+'"' + (tooltip ? ' title="'+tooltip+'"' : '') + '>'+prevMonth.getUTCDate() + '</td>');
				if (prevMonth.getUTCDay() === this.o.weekEnd){
					html.push('</tr>');
				}
				prevMonth.setUTCDate(prevMonth.getUTCDate()+1);
			}
			this.picker.find('.datepicker-days tbody').empty().append(html.join(''));

			var months = this.picker.find('.datepicker-months')
						.find('th:eq(1)')
							.text(year)
							.end()
						.find('span').removeClass('active');

			$.each(this.dates, function(i, d){
				if (d.getUTCFullYear() === year)
					months.eq(d.getUTCMonth()).addClass('active');
			});

			if (year < startYear || year > endYear){
				months.addClass('disabled');
			}
			if (year === startYear){
				months.slice(0, startMonth).addClass('disabled');
			}
			if (year === endYear){
				months.slice(endMonth+1).addClass('disabled');
			}

			html = '';
			year = parseInt(year/10, 10) * 10;
			var yearCont = this.picker.find('.datepicker-years')
								.find('th:eq(1)')
									.text(year + '-' + (year + 9))
									.end()
								.find('td');
			year -= 1;
			var years = $.map(this.dates, function(d){
					return d.getUTCFullYear();
				}),
				classes;
			for (var i = -1; i < 11; i++){
				classes = ['year'];
				if (i === -1)
					classes.push('old');
				else if (i === 10)
					classes.push('new');
				if ($.inArray(year, years) !== -1)
					classes.push('active');
				if (year < startYear || year > endYear)
					classes.push('disabled');
				html += '<span class="' + classes.join(' ') + '">'+year+'</span>';
				year += 1;
			}
			yearCont.html(html);
		},

		updateNavArrows: function(){
			if (!this._allow_update)
				return;

			var d = new Date(this.viewDate),
				year = d.getUTCFullYear(),
				month = d.getUTCMonth();
			switch (this.viewMode){
				case 0:
					if (this.o.startDate !== -Infinity && year <= this.o.startDate.getUTCFullYear() && month <= this.o.startDate.getUTCMonth()){
						this.picker.find('.prev').css({visibility: 'hidden'});
					}
					else {
						this.picker.find('.prev').css({visibility: 'visible'});
					}
					if (this.o.endDate !== Infinity && year >= this.o.endDate.getUTCFullYear() && month >= this.o.endDate.getUTCMonth()){
						this.picker.find('.next').css({visibility: 'hidden'});
					}
					else {
						this.picker.find('.next').css({visibility: 'visible'});
					}
					break;
				case 1:
				case 2:
					if (this.o.startDate !== -Infinity && year <= this.o.startDate.getUTCFullYear()){
						this.picker.find('.prev').css({visibility: 'hidden'});
					}
					else {
						this.picker.find('.prev').css({visibility: 'visible'});
					}
					if (this.o.endDate !== Infinity && year >= this.o.endDate.getUTCFullYear()){
						this.picker.find('.next').css({visibility: 'hidden'});
					}
					else {
						this.picker.find('.next').css({visibility: 'visible'});
					}
					break;
			}
		},

		click: function(e){
			e.preventDefault();
			var target = $(e.target).closest('span, td, th'),
				year, month, day;
			if (target.length === 1){
				switch (target[0].nodeName.toLowerCase()){
					case 'th':
						switch (target[0].className){
							case 'datepicker-switch':
								this.showMode(1);
								break;
							case 'prev':
							case 'next':
								var dir = DPGlobal.modes[this.viewMode].navStep * (target[0].className === 'prev' ? -1 : 1);
								switch (this.viewMode){
									case 0:
										this.viewDate = this.moveMonth(this.viewDate, dir);
										this._trigger('changeMonth', this.viewDate);
										break;
									case 1:
									case 2:
										this.viewDate = this.moveYear(this.viewDate, dir);
										if (this.viewMode === 1)
											this._trigger('changeYear', this.viewDate);
										break;
								}
								this.fill();
								break;
							case 'today':
								var date = new Date();
								date = UTCDate(date.getFullYear(), date.getMonth(), date.getDate(), 0, 0, 0);

								this.showMode(-2);
								var which = this.o.todayBtn === 'linked' ? null : 'view';
								this._setDate(date, which);
								break;
							case 'clear':
								var element;
								if (this.isInput)
									element = this.element;
								else if (this.component)
									element = this.element.find('input');
								if (element)
									element.val("").change();
								this.update();
								this._trigger('changeDate');
								if (this.o.autoclose)
									this.hide();
								break;
						}
						break;
					case 'span':
						if (!target.is('.disabled')){
							this.viewDate.setUTCDate(1);
							if (target.is('.month')){
								day = 1;
								month = target.parent().find('span').index(target);
								year = this.viewDate.getUTCFullYear();
								this.viewDate.setUTCMonth(month);
								this._trigger('changeMonth', this.viewDate);
								if (this.o.minViewMode === 1){
									this._setDate(UTCDate(year, month, day));
								}
							}
							else {
								day = 1;
								month = 0;
								year = parseInt(target.text(), 10)||0;
								this.viewDate.setUTCFullYear(year);
								this._trigger('changeYear', this.viewDate);
								if (this.o.minViewMode === 2){
									this._setDate(UTCDate(year, month, day));
								}
							}
							this.showMode(-1);
							this.fill();
						}
						break;
					case 'td':
						if (target.is('.day') && !target.is('.disabled')){
							day = parseInt(target.text(), 10)||1;
							year = this.viewDate.getUTCFullYear();
							month = this.viewDate.getUTCMonth();
							if (target.is('.old')){
								if (month === 0){
									month = 11;
									year -= 1;
								}
								else {
									month -= 1;
								}
							}
							else if (target.is('.new')){
								if (month === 11){
									month = 0;
									year += 1;
								}
								else {
									month += 1;
								}
							}
							this._setDate(UTCDate(year, month, day));
						}
						break;
				}
			}
			if (this.picker.is(':visible') && this._focused_from){
				$(this._focused_from).focus();
			}
			delete this._focused_from;
		},

		_toggle_multidate: function(date){
			var ix = this.dates.contains(date);
			if (!date){
				this.dates.clear();
			}
			else if (ix !== -1){
				this.dates.remove(ix);
			}
			else {
				this.dates.push(date);
			}
			if (typeof this.o.multidate === 'number')
				while (this.dates.length > this.o.multidate)
					this.dates.remove(0);
		},

		_setDate: function(date, which){
			if (!which || which === 'date')
				this._toggle_multidate(date && new Date(date));
			if (!which || which  === 'view')
				this.viewDate = date && new Date(date);

			this.fill();
			this.setValue();
			this._trigger('changeDate');
			var element;
			if (this.isInput){
				element = this.element;
			}
			else if (this.component){
				element = this.element.find('input');
			}
			if (element){
				element.change();
			}
			if (this.o.autoclose && (!which || which === 'date')){
				this.hide();
			}
		},

		moveMonth: function(date, dir){
			if (!date)
				return undefined;
			if (!dir)
				return date;
			var new_date = new Date(date.valueOf()),
				day = new_date.getUTCDate(),
				month = new_date.getUTCMonth(),
				mag = Math.abs(dir),
				new_month, test;
			dir = dir > 0 ? 1 : -1;
			if (mag === 1){
				test = dir === -1
					// If going back one month, make sure month is not current month
					// (eg, Mar 31 -> Feb 31 == Feb 28, not Mar 02)
					? function(){
						return new_date.getUTCMonth() === month;
					}
					// If going forward one month, make sure month is as expected
					// (eg, Jan 31 -> Feb 31 == Feb 28, not Mar 02)
					: function(){
						return new_date.getUTCMonth() !== new_month;
					};
				new_month = month + dir;
				new_date.setUTCMonth(new_month);
				// Dec -> Jan (12) or Jan -> Dec (-1) -- limit expected date to 0-11
				if (new_month < 0 || new_month > 11)
					new_month = (new_month + 12) % 12;
			}
			else {
				// For magnitudes >1, move one month at a time...
				for (var i=0; i < mag; i++)
					// ...which might decrease the day (eg, Jan 31 to Feb 28, etc)...
					new_date = this.moveMonth(new_date, dir);
				// ...then reset the day, keeping it in the new month
				new_month = new_date.getUTCMonth();
				new_date.setUTCDate(day);
				test = function(){
					return new_month !== new_date.getUTCMonth();
				};
			}
			// Common date-resetting loop -- if date is beyond end of month, make it
			// end of month
			while (test()){
				new_date.setUTCDate(--day);
				new_date.setUTCMonth(new_month);
			}
			return new_date;
		},

		moveYear: function(date, dir){
			return this.moveMonth(date, dir*12);
		},

		dateWithinRange: function(date){
			return date >= this.o.startDate && date <= this.o.endDate;
		},

		keydown: function(e){
			if (this.picker.is(':not(:visible)')){
				if (e.keyCode === 27) // allow escape to hide and re-show picker
					this.show();
				return;
			}
			var dateChanged = false,
				dir, newDate, newViewDate,
				focusDate = this.focusDate || this.viewDate;
			switch (e.keyCode){
				case 27: // escape
					if (this.focusDate){
						this.focusDate = null;
						this.viewDate = this.dates.get(-1) || this.viewDate;
						this.fill();
					}
					else
						this.hide();
					e.preventDefault();
					break;
				case 37: // left
				case 39: // right
					if (!this.o.keyboardNavigation)
						break;
					dir = e.keyCode === 37 ? -1 : 1;
					if (e.ctrlKey){
						newDate = this.moveYear(this.dates.get(-1) || UTCToday(), dir);
						newViewDate = this.moveYear(focusDate, dir);
						this._trigger('changeYear', this.viewDate);
					}
					else if (e.shiftKey){
						newDate = this.moveMonth(this.dates.get(-1) || UTCToday(), dir);
						newViewDate = this.moveMonth(focusDate, dir);
						this._trigger('changeMonth', this.viewDate);
					}
					else {
						newDate = new Date(this.dates.get(-1) || UTCToday());
						newDate.setUTCDate(newDate.getUTCDate() + dir);
						newViewDate = new Date(focusDate);
						newViewDate.setUTCDate(focusDate.getUTCDate() + dir);
					}
					if (this.dateWithinRange(newDate)){
						this.focusDate = this.viewDate = newViewDate;
						this.setValue();
						this.fill();
						e.preventDefault();
					}
					break;
				case 38: // up
				case 40: // down
					if (!this.o.keyboardNavigation)
						break;
					dir = e.keyCode === 38 ? -1 : 1;
					if (e.ctrlKey){
						newDate = this.moveYear(this.dates.get(-1) || UTCToday(), dir);
						newViewDate = this.moveYear(focusDate, dir);
						this._trigger('changeYear', this.viewDate);
					}
					else if (e.shiftKey){
						newDate = this.moveMonth(this.dates.get(-1) || UTCToday(), dir);
						newViewDate = this.moveMonth(focusDate, dir);
						this._trigger('changeMonth', this.viewDate);
					}
					else {
						newDate = new Date(this.dates.get(-1) || UTCToday());
						newDate.setUTCDate(newDate.getUTCDate() + dir * 7);
						newViewDate = new Date(focusDate);
						newViewDate.setUTCDate(focusDate.getUTCDate() + dir * 7);
					}
					if (this.dateWithinRange(newDate)){
						this.focusDate = this.viewDate = newViewDate;
						this.setValue();
						this.fill();
						e.preventDefault();
					}
					break;
				case 32: // spacebar
					// Spacebar is used in manually typing dates in some formats.
					// As such, its behavior should not be hijacked.
					break;
				case 13: // enter
					focusDate = this.focusDate || this.dates.get(-1) || this.viewDate;
					this._toggle_multidate(focusDate);
					dateChanged = true;
					this.focusDate = null;
					this.viewDate = this.dates.get(-1) || this.viewDate;
					this.setValue();
					this.fill();
					if (this.picker.is(':visible')){
						e.preventDefault();
						if (this.o.autoclose)
							this.hide();
					}
					break;
				case 9: // tab
					this.focusDate = null;
					this.viewDate = this.dates.get(-1) || this.viewDate;
					this.fill();
					this.hide();
					break;
			}
			if (dateChanged){
				if (this.dates.length)
					this._trigger('changeDate');
				else
					this._trigger('clearDate');
				var element;
				if (this.isInput){
					element = this.element;
				}
				else if (this.component){
					element = this.element.find('input');
				}
				if (element){
					element.change();
				}
			}
		},

		showMode: function(dir){
			if (dir){
				this.viewMode = Math.max(this.o.minViewMode, Math.min(2, this.viewMode + dir));
			}
			this.picker
				.find('>div')
				.hide()
				.filter('.datepicker-'+DPGlobal.modes[this.viewMode].clsName)
					.css('display', 'block');
			this.updateNavArrows();
		}
	};

	var DateRangePicker = function(element, options){
		this.element = $(element);
		this.inputs = $.map(options.inputs, function(i){
			return i.jquery ? i[0] : i;
		});
		delete options.inputs;

		$(this.inputs)
			.datepicker(options)
			.bind('changeDate', $.proxy(this.dateUpdated, this));

		this.pickers = $.map(this.inputs, function(i){
			return $(i).data('datepicker');
		});
		this.updateDates();
	};
	DateRangePicker.prototype = {
		updateDates: function(){
			this.dates = $.map(this.pickers, function(i){
				return i.getUTCDate();
			});
			this.updateRanges();
		},
		updateRanges: function(){
			var range = $.map(this.dates, function(d){
				return d.valueOf();
			});
			$.each(this.pickers, function(i, p){
				p.setRange(range);
			});
		},
		dateUpdated: function(e){
			// `this.updating` is a workaround for preventing infinite recursion
			// between `changeDate` triggering and `setUTCDate` calling.  Until
			// there is a better mechanism.
			if (this.updating)
				return;
			this.updating = true;

			var dp = $(e.target).data('datepicker'),
				new_date = dp.getUTCDate(),
				i = $.inArray(e.target, this.inputs),
				l = this.inputs.length;
			if (i === -1)
				return;

			$.each(this.pickers, function(i, p){
				if (!p.getUTCDate())
					p.setUTCDate(new_date);
			});

			if (new_date < this.dates[i]){
				// Date being moved earlier/left
				while (i >= 0 && new_date < this.dates[i]){
					this.pickers[i--].setUTCDate(new_date);
				}
			}
			else if (new_date > this.dates[i]){
				// Date being moved later/right
				while (i < l && new_date > this.dates[i]){
					this.pickers[i++].setUTCDate(new_date);
				}
			}
			this.updateDates();

			delete this.updating;
		},
		remove: function(){
			$.map(this.pickers, function(p){ p.remove(); });
			delete this.element.data().datepicker;
		}
	};

	function opts_from_el(el, prefix){
		// Derive options from element data-attrs
		var data = $(el).data(),
			out = {}, inkey,
			replace = new RegExp('^' + prefix.toLowerCase() + '([A-Z])');
		prefix = new RegExp('^' + prefix.toLowerCase());
		function re_lower(_,a){
			return a.toLowerCase();
		}
		for (var key in data)
			if (prefix.test(key)){
				inkey = key.replace(replace, re_lower);
				out[inkey] = data[key];
			}
		return out;
	}

	function opts_from_locale(lang){
		// Derive options from locale plugins
		var out = {};
		// Check if "de-DE" style date is available, if not language should
		// fallback to 2 letter code eg "de"
		if (!dates[lang]){
			lang = lang.split('-')[0];
			if (!dates[lang])
				return;
		}
		var d = dates[lang];
		$.each(locale_opts, function(i,k){
			if (k in d)
				out[k] = d[k];
		});
		return out;
	}

	var old = $.fn.datepicker;
	$.fn.datepicker = function(option){
		var args = Array.apply(null, arguments);
		args.shift();
		var internal_return;
		this.each(function(){
			var $this = $(this),
				data = $this.data('datepicker'),
				options = typeof option === 'object' && option;
			if (!data){
				var elopts = opts_from_el(this, 'date'),
					// Preliminary otions
					xopts = $.extend({}, defaults, elopts, options),
					locopts = opts_from_locale(xopts.language),
					// Options priority: js args, data-attrs, locales, defaults
					opts = $.extend({}, defaults, locopts, elopts, options);
				if ($this.is('.input-daterange') || opts.inputs){
					var ropts = {
						inputs: opts.inputs || $this.find('input').toArray()
					};
					$this.data('datepicker', (data = new DateRangePicker(this, $.extend(opts, ropts))));
				}
				else {
					$this.data('datepicker', (data = new Datepicker(this, opts)));
				}
			}
			if (typeof option === 'string' && typeof data[option] === 'function'){
				internal_return = data[option].apply(data, args);
				if (internal_return !== undefined)
					return false;
			}
		});
		if (internal_return !== undefined)
			return internal_return;
		else
			return this;
	};

	var defaults = $.fn.datepicker.defaults = {
		autoclose: false,
		beforeShowDay: $.noop,
		calendarWeeks: false,
		clearBtn: false,
		daysOfWeekDisabled: [],
		endDate: Infinity,
		forceParse: true,
		format: 'mm/dd/yyyy',
		keyboardNavigation: true,
		language: 'en',
		minViewMode: 0,
		multidate: false,
		multidateSeparator: ',',
		orientation: "auto",
		rtl: false,
		startDate: -Infinity,
		startView: 0,
		todayBtn: false,
		todayHighlight: false,
		weekStart: 0
	};
	var locale_opts = $.fn.datepicker.locale_opts = [
		'format',
		'rtl',
		'weekStart'
	];
	$.fn.datepicker.Constructor = Datepicker;
	var dates = $.fn.datepicker.dates = {
		en: {
			days: ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"],
			daysShort: ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"],
			daysMin: ["Su", "Mo", "Tu", "We", "Th", "Fr", "Sa", "Su"],
			months: ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"],
			monthsShort: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
			today: "Today",
			clear: "Clear"
		}
	};

	var DPGlobal = {
		modes: [
			{
				clsName: 'days',
				navFnc: 'Month',
				navStep: 1
			},
			{
				clsName: 'months',
				navFnc: 'FullYear',
				navStep: 1
			},
			{
				clsName: 'years',
				navFnc: 'FullYear',
				navStep: 10
		}],
		isLeapYear: function(year){
			return (((year % 4 === 0) && (year % 100 !== 0)) || (year % 400 === 0));
		},
		getDaysInMonth: function(year, month){
			return [31, (DPGlobal.isLeapYear(year) ? 29 : 28), 31, 30, 31, 30, 31, 31, 30, 31, 30, 31][month];
		},
		validParts: /dd?|DD?|mm?|MM?|yy(?:yy)?/g,
		nonpunctuation: /[^ -\/:-@\[\u3400-\u9fff-`{-~\t\n\r]+/g,
		parseFormat: function(format){
			// IE treats \0 as a string end in inputs (truncating the value),
			// so it's a bad format delimiter, anyway
			var separators = format.replace(this.validParts, '\0').split('\0'),
				parts = format.match(this.validParts);
			if (!separators || !separators.length || !parts || parts.length === 0){
				throw new Error("Invalid date format.");
			}
			return {separators: separators, parts: parts};
		},
		parseDate: function(date, format, language){
			if (!date)
				return undefined;
			if (date instanceof Date)
				return date;
			if (typeof format === 'string')
				format = DPGlobal.parseFormat(format);
			var part_re = /([\-+]\d+)([dmwy])/,
				parts = date.match(/([\-+]\d+)([dmwy])/g),
				part, dir, i;
			if (/^[\-+]\d+[dmwy]([\s,]+[\-+]\d+[dmwy])*$/.test(date)){
				date = new Date();
				for (i=0; i < parts.length; i++){
					part = part_re.exec(parts[i]);
					dir = parseInt(part[1]);
					switch (part[2]){
						case 'd':
							date.setUTCDate(date.getUTCDate() + dir);
							break;
						case 'm':
							date = Datepicker.prototype.moveMonth.call(Datepicker.prototype, date, dir);
							break;
						case 'w':
							date.setUTCDate(date.getUTCDate() + dir * 7);
							break;
						case 'y':
							date = Datepicker.prototype.moveYear.call(Datepicker.prototype, date, dir);
							break;
					}
				}
				return UTCDate(date.getUTCFullYear(), date.getUTCMonth(), date.getUTCDate(), 0, 0, 0);
			}
			parts = date && date.match(this.nonpunctuation) || [];
			date = new Date();
			var parsed = {},
				setters_order = ['yyyy', 'yy', 'M', 'MM', 'm', 'mm', 'd', 'dd'],
				setters_map = {
					yyyy: function(d,v){
						return d.setUTCFullYear(v);
					},
					yy: function(d,v){
						return d.setUTCFullYear(2000+v);
					},
					m: function(d,v){
						if (isNaN(d))
							return d;
						v -= 1;
						while (v < 0) v += 12;
						v %= 12;
						d.setUTCMonth(v);
						while (d.getUTCMonth() !== v)
							d.setUTCDate(d.getUTCDate()-1);
						return d;
					},
					d: function(d,v){
						return d.setUTCDate(v);
					}
				},
				val, filtered;
			setters_map['M'] = setters_map['MM'] = setters_map['mm'] = setters_map['m'];
			setters_map['dd'] = setters_map['d'];
			date = UTCDate(date.getFullYear(), date.getMonth(), date.getDate(), 0, 0, 0);
			var fparts = format.parts.slice();
			// Remove noop parts
			if (parts.length !== fparts.length){
				fparts = $(fparts).filter(function(i,p){
					return $.inArray(p, setters_order) !== -1;
				}).toArray();
			}
			// Process remainder
			function match_part(){
				var m = this.slice(0, parts[i].length),
					p = parts[i].slice(0, m.length);
				return m === p;
			}
			if (parts.length === fparts.length){
				var cnt;
				for (i=0, cnt = fparts.length; i < cnt; i++){
					val = parseInt(parts[i], 10);
					part = fparts[i];
					if (isNaN(val)){
						switch (part){
							case 'MM':
								filtered = $(dates[language].months).filter(match_part);
								val = $.inArray(filtered[0], dates[language].months) + 1;
								break;
							case 'M':
								filtered = $(dates[language].monthsShort).filter(match_part);
								val = $.inArray(filtered[0], dates[language].monthsShort) + 1;
								break;
						}
					}
					parsed[part] = val;
				}
				var _date, s;
				for (i=0; i < setters_order.length; i++){
					s = setters_order[i];
					if (s in parsed && !isNaN(parsed[s])){
						_date = new Date(date);
						setters_map[s](_date, parsed[s]);
						if (!isNaN(_date))
							date = _date;
					}
				}
			}
			return date;
		},
		formatDate: function(date, format, language){
			if (!date)
				return '';
			if (typeof format === 'string')
				format = DPGlobal.parseFormat(format);
			var val = {
				d: date.getUTCDate(),
				D: dates[language].daysShort[date.getUTCDay()],
				DD: dates[language].days[date.getUTCDay()],
				m: date.getUTCMonth() + 1,
				M: dates[language].monthsShort[date.getUTCMonth()],
				MM: dates[language].months[date.getUTCMonth()],
				yy: date.getUTCFullYear().toString().substring(2),
				yyyy: date.getUTCFullYear()
			};
			val.dd = (val.d < 10 ? '0' : '') + val.d;
			val.mm = (val.m < 10 ? '0' : '') + val.m;
			date = [];
			var seps = $.extend([], format.separators);
			for (var i=0, cnt = format.parts.length; i <= cnt; i++){
				if (seps.length)
					date.push(seps.shift());
				date.push(val[format.parts[i]]);
			}
			return date.join('');
		},
		headTemplate: '<thead>'+
							'<tr>'+
								'<th class="prev">&laquo;</th>'+
								'<th colspan="5" class="datepicker-switch"></th>'+
								'<th class="next">&raquo;</th>'+
							'</tr>'+
						'</thead>',
		contTemplate: '<tbody><tr><td colspan="7"></td></tr></tbody>',
		footTemplate: '<tfoot>'+
							'<tr>'+
								'<th colspan="7" class="today"></th>'+
							'</tr>'+
							'<tr>'+
								'<th colspan="7" class="clear"></th>'+
							'</tr>'+
						'</tfoot>'
	};
	DPGlobal.template = '<div class="datepicker">'+
							'<div class="datepicker-days">'+
								'<table class=" table-condensed">'+
									DPGlobal.headTemplate+
									'<tbody></tbody>'+
									DPGlobal.footTemplate+
								'</table>'+
							'</div>'+
							'<div class="datepicker-months">'+
								'<table class="table-condensed">'+
									DPGlobal.headTemplate+
									DPGlobal.contTemplate+
									DPGlobal.footTemplate+
								'</table>'+
							'</div>'+
							'<div class="datepicker-years">'+
								'<table class="table-condensed">'+
									DPGlobal.headTemplate+
									DPGlobal.contTemplate+
									DPGlobal.footTemplate+
								'</table>'+
							'</div>'+
						'</div>';

	$.fn.datepicker.DPGlobal = DPGlobal;


	/* DATEPICKER NO CONFLICT
	* =================== */

	$.fn.datepicker.noConflict = function(){
		$.fn.datepicker = old;
		return this;
	};


	/* DATEPICKER DATA-API
	* ================== */

	$(document).on(
		'focus.datepicker.data-api click.datepicker.data-api',
		'[data-provide="datepicker"]',
		function(e){
			var $this = $(this);
			if ($this.data('datepicker'))
				return;
			e.preventDefault();
			// component click requires us to explicitly show it
			$this.datepicker('show');
		}
	);
	$(function(){
		$('[data-provide="datepicker-inline"]').datepicker();
	});

}(window.jQuery));

/*
Copyright 2012 Igor Vaynberg

Version: 3.4.2 Timestamp: Mon Aug 12 15:04:12 PDT 2013

This software is licensed under the Apache License, Version 2.0 (the "Apache License") or the GNU
General Public License version 2 (the "GPL License"). You may choose either license to govern your
use of this software only upon the condition that you accept all of the terms of either the Apache
License or the GPL License.

You may obtain a copy of the Apache License and the GPL License at:

    http://www.apache.org/licenses/LICENSE-2.0
    http://www.gnu.org/licenses/gpl-2.0.html

Unless required by applicable law or agreed to in writing, software distributed under the
Apache License or the GPL Licesnse is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR
CONDITIONS OF ANY KIND, either express or implied. See the Apache License and the GPL License for
the specific language governing permissions and limitations under the Apache License and the GPL License.
*/
(function ($) {
    if(typeof $.fn.each2 == "undefined") {
        $.extend($.fn, {
            /*
            * 4-10 times faster .each replacement
            * use it carefully, as it overrides jQuery context of element on each iteration
            */
            each2 : function (c) {
                var j = $([0]), i = -1, l = this.length;
                while (
                    ++i < l
                    && (j.context = j[0] = this[i])
                    && c.call(j[0], i, j) !== false //"this"=DOM, i=index, j=jQuery object
                );
                return this;
            }
        });
    }
})(jQuery);

(function ($, undefined) {
    "use strict";
    /*global document, window, jQuery, console */

    if (window.Select2 !== undefined) {
        return;
    }

    var KEY, AbstractSelect2, SingleSelect2, MultiSelect2, nextUid, sizer,
        lastMousePosition={x:0,y:0}, $document, scrollBarDimensions,

    KEY = {
        TAB: 9,
        ENTER: 13,
        ESC: 27,
        SPACE: 32,
        LEFT: 37,
        UP: 38,
        RIGHT: 39,
        DOWN: 40,
        SHIFT: 16,
        CTRL: 17,
        ALT: 18,
        PAGE_UP: 33,
        PAGE_DOWN: 34,
        HOME: 36,
        END: 35,
        BACKSPACE: 8,
        DELETE: 46,
        isArrow: function (k) {
            k = k.which ? k.which : k;
            switch (k) {
            case KEY.LEFT:
            case KEY.RIGHT:
            case KEY.UP:
            case KEY.DOWN:
                return true;
            }
            return false;
        },
        isControl: function (e) {
            var k = e.which;
            switch (k) {
            case KEY.SHIFT:
            case KEY.CTRL:
            case KEY.ALT:
                return true;
            }

            if (e.metaKey) return true;

            return false;
        },
        isFunctionKey: function (k) {
            k = k.which ? k.which : k;
            return k >= 112 && k <= 123;
        }
    },
    MEASURE_SCROLLBAR_TEMPLATE = "<div class='select2-measure-scrollbar'></div>",

    DIACRITICS = {"\u24B6":"A","\uFF21":"A","\u00C0":"A","\u00C1":"A","\u00C2":"A","\u1EA6":"A","\u1EA4":"A","\u1EAA":"A","\u1EA8":"A","\u00C3":"A","\u0100":"A","\u0102":"A","\u1EB0":"A","\u1EAE":"A","\u1EB4":"A","\u1EB2":"A","\u0226":"A","\u01E0":"A","\u00C4":"A","\u01DE":"A","\u1EA2":"A","\u00C5":"A","\u01FA":"A","\u01CD":"A","\u0200":"A","\u0202":"A","\u1EA0":"A","\u1EAC":"A","\u1EB6":"A","\u1E00":"A","\u0104":"A","\u023A":"A","\u2C6F":"A","\uA732":"AA","\u00C6":"AE","\u01FC":"AE","\u01E2":"AE","\uA734":"AO","\uA736":"AU","\uA738":"AV","\uA73A":"AV","\uA73C":"AY","\u24B7":"B","\uFF22":"B","\u1E02":"B","\u1E04":"B","\u1E06":"B","\u0243":"B","\u0182":"B","\u0181":"B","\u24B8":"C","\uFF23":"C","\u0106":"C","\u0108":"C","\u010A":"C","\u010C":"C","\u00C7":"C","\u1E08":"C","\u0187":"C","\u023B":"C","\uA73E":"C","\u24B9":"D","\uFF24":"D","\u1E0A":"D","\u010E":"D","\u1E0C":"D","\u1E10":"D","\u1E12":"D","\u1E0E":"D","\u0110":"D","\u018B":"D","\u018A":"D","\u0189":"D","\uA779":"D","\u01F1":"DZ","\u01C4":"DZ","\u01F2":"Dz","\u01C5":"Dz","\u24BA":"E","\uFF25":"E","\u00C8":"E","\u00C9":"E","\u00CA":"E","\u1EC0":"E","\u1EBE":"E","\u1EC4":"E","\u1EC2":"E","\u1EBC":"E","\u0112":"E","\u1E14":"E","\u1E16":"E","\u0114":"E","\u0116":"E","\u00CB":"E","\u1EBA":"E","\u011A":"E","\u0204":"E","\u0206":"E","\u1EB8":"E","\u1EC6":"E","\u0228":"E","\u1E1C":"E","\u0118":"E","\u1E18":"E","\u1E1A":"E","\u0190":"E","\u018E":"E","\u24BB":"F","\uFF26":"F","\u1E1E":"F","\u0191":"F","\uA77B":"F","\u24BC":"G","\uFF27":"G","\u01F4":"G","\u011C":"G","\u1E20":"G","\u011E":"G","\u0120":"G","\u01E6":"G","\u0122":"G","\u01E4":"G","\u0193":"G","\uA7A0":"G","\uA77D":"G","\uA77E":"G","\u24BD":"H","\uFF28":"H","\u0124":"H","\u1E22":"H","\u1E26":"H","\u021E":"H","\u1E24":"H","\u1E28":"H","\u1E2A":"H","\u0126":"H","\u2C67":"H","\u2C75":"H","\uA78D":"H","\u24BE":"I","\uFF29":"I","\u00CC":"I","\u00CD":"I","\u00CE":"I","\u0128":"I","\u012A":"I","\u012C":"I","\u0130":"I","\u00CF":"I","\u1E2E":"I","\u1EC8":"I","\u01CF":"I","\u0208":"I","\u020A":"I","\u1ECA":"I","\u012E":"I","\u1E2C":"I","\u0197":"I","\u24BF":"J","\uFF2A":"J","\u0134":"J","\u0248":"J","\u24C0":"K","\uFF2B":"K","\u1E30":"K","\u01E8":"K","\u1E32":"K","\u0136":"K","\u1E34":"K","\u0198":"K","\u2C69":"K","\uA740":"K","\uA742":"K","\uA744":"K","\uA7A2":"K","\u24C1":"L","\uFF2C":"L","\u013F":"L","\u0139":"L","\u013D":"L","\u1E36":"L","\u1E38":"L","\u013B":"L","\u1E3C":"L","\u1E3A":"L","\u0141":"L","\u023D":"L","\u2C62":"L","\u2C60":"L","\uA748":"L","\uA746":"L","\uA780":"L","\u01C7":"LJ","\u01C8":"Lj","\u24C2":"M","\uFF2D":"M","\u1E3E":"M","\u1E40":"M","\u1E42":"M","\u2C6E":"M","\u019C":"M","\u24C3":"N","\uFF2E":"N","\u01F8":"N","\u0143":"N","\u00D1":"N","\u1E44":"N","\u0147":"N","\u1E46":"N","\u0145":"N","\u1E4A":"N","\u1E48":"N","\u0220":"N","\u019D":"N","\uA790":"N","\uA7A4":"N","\u01CA":"NJ","\u01CB":"Nj","\u24C4":"O","\uFF2F":"O","\u00D2":"O","\u00D3":"O","\u00D4":"O","\u1ED2":"O","\u1ED0":"O","\u1ED6":"O","\u1ED4":"O","\u00D5":"O","\u1E4C":"O","\u022C":"O","\u1E4E":"O","\u014C":"O","\u1E50":"O","\u1E52":"O","\u014E":"O","\u022E":"O","\u0230":"O","\u00D6":"O","\u022A":"O","\u1ECE":"O","\u0150":"O","\u01D1":"O","\u020C":"O","\u020E":"O","\u01A0":"O","\u1EDC":"O","\u1EDA":"O","\u1EE0":"O","\u1EDE":"O","\u1EE2":"O","\u1ECC":"O","\u1ED8":"O","\u01EA":"O","\u01EC":"O","\u00D8":"O","\u01FE":"O","\u0186":"O","\u019F":"O","\uA74A":"O","\uA74C":"O","\u01A2":"OI","\uA74E":"OO","\u0222":"OU","\u24C5":"P","\uFF30":"P","\u1E54":"P","\u1E56":"P","\u01A4":"P","\u2C63":"P","\uA750":"P","\uA752":"P","\uA754":"P","\u24C6":"Q","\uFF31":"Q","\uA756":"Q","\uA758":"Q","\u024A":"Q","\u24C7":"R","\uFF32":"R","\u0154":"R","\u1E58":"R","\u0158":"R","\u0210":"R","\u0212":"R","\u1E5A":"R","\u1E5C":"R","\u0156":"R","\u1E5E":"R","\u024C":"R","\u2C64":"R","\uA75A":"R","\uA7A6":"R","\uA782":"R","\u24C8":"S","\uFF33":"S","\u1E9E":"S","\u015A":"S","\u1E64":"S","\u015C":"S","\u1E60":"S","\u0160":"S","\u1E66":"S","\u1E62":"S","\u1E68":"S","\u0218":"S","\u015E":"S","\u2C7E":"S","\uA7A8":"S","\uA784":"S","\u24C9":"T","\uFF34":"T","\u1E6A":"T","\u0164":"T","\u1E6C":"T","\u021A":"T","\u0162":"T","\u1E70":"T","\u1E6E":"T","\u0166":"T","\u01AC":"T","\u01AE":"T","\u023E":"T","\uA786":"T","\uA728":"TZ","\u24CA":"U","\uFF35":"U","\u00D9":"U","\u00DA":"U","\u00DB":"U","\u0168":"U","\u1E78":"U","\u016A":"U","\u1E7A":"U","\u016C":"U","\u00DC":"U","\u01DB":"U","\u01D7":"U","\u01D5":"U","\u01D9":"U","\u1EE6":"U","\u016E":"U","\u0170":"U","\u01D3":"U","\u0214":"U","\u0216":"U","\u01AF":"U","\u1EEA":"U","\u1EE8":"U","\u1EEE":"U","\u1EEC":"U","\u1EF0":"U","\u1EE4":"U","\u1E72":"U","\u0172":"U","\u1E76":"U","\u1E74":"U","\u0244":"U","\u24CB":"V","\uFF36":"V","\u1E7C":"V","\u1E7E":"V","\u01B2":"V","\uA75E":"V","\u0245":"V","\uA760":"VY","\u24CC":"W","\uFF37":"W","\u1E80":"W","\u1E82":"W","\u0174":"W","\u1E86":"W","\u1E84":"W","\u1E88":"W","\u2C72":"W","\u24CD":"X","\uFF38":"X","\u1E8A":"X","\u1E8C":"X","\u24CE":"Y","\uFF39":"Y","\u1EF2":"Y","\u00DD":"Y","\u0176":"Y","\u1EF8":"Y","\u0232":"Y","\u1E8E":"Y","\u0178":"Y","\u1EF6":"Y","\u1EF4":"Y","\u01B3":"Y","\u024E":"Y","\u1EFE":"Y","\u24CF":"Z","\uFF3A":"Z","\u0179":"Z","\u1E90":"Z","\u017B":"Z","\u017D":"Z","\u1E92":"Z","\u1E94":"Z","\u01B5":"Z","\u0224":"Z","\u2C7F":"Z","\u2C6B":"Z","\uA762":"Z","\u24D0":"a","\uFF41":"a","\u1E9A":"a","\u00E0":"a","\u00E1":"a","\u00E2":"a","\u1EA7":"a","\u1EA5":"a","\u1EAB":"a","\u1EA9":"a","\u00E3":"a","\u0101":"a","\u0103":"a","\u1EB1":"a","\u1EAF":"a","\u1EB5":"a","\u1EB3":"a","\u0227":"a","\u01E1":"a","\u00E4":"a","\u01DF":"a","\u1EA3":"a","\u00E5":"a","\u01FB":"a","\u01CE":"a","\u0201":"a","\u0203":"a","\u1EA1":"a","\u1EAD":"a","\u1EB7":"a","\u1E01":"a","\u0105":"a","\u2C65":"a","\u0250":"a","\uA733":"aa","\u00E6":"ae","\u01FD":"ae","\u01E3":"ae","\uA735":"ao","\uA737":"au","\uA739":"av","\uA73B":"av","\uA73D":"ay","\u24D1":"b","\uFF42":"b","\u1E03":"b","\u1E05":"b","\u1E07":"b","\u0180":"b","\u0183":"b","\u0253":"b","\u24D2":"c","\uFF43":"c","\u0107":"c","\u0109":"c","\u010B":"c","\u010D":"c","\u00E7":"c","\u1E09":"c","\u0188":"c","\u023C":"c","\uA73F":"c","\u2184":"c","\u24D3":"d","\uFF44":"d","\u1E0B":"d","\u010F":"d","\u1E0D":"d","\u1E11":"d","\u1E13":"d","\u1E0F":"d","\u0111":"d","\u018C":"d","\u0256":"d","\u0257":"d","\uA77A":"d","\u01F3":"dz","\u01C6":"dz","\u24D4":"e","\uFF45":"e","\u00E8":"e","\u00E9":"e","\u00EA":"e","\u1EC1":"e","\u1EBF":"e","\u1EC5":"e","\u1EC3":"e","\u1EBD":"e","\u0113":"e","\u1E15":"e","\u1E17":"e","\u0115":"e","\u0117":"e","\u00EB":"e","\u1EBB":"e","\u011B":"e","\u0205":"e","\u0207":"e","\u1EB9":"e","\u1EC7":"e","\u0229":"e","\u1E1D":"e","\u0119":"e","\u1E19":"e","\u1E1B":"e","\u0247":"e","\u025B":"e","\u01DD":"e","\u24D5":"f","\uFF46":"f","\u1E1F":"f","\u0192":"f","\uA77C":"f","\u24D6":"g","\uFF47":"g","\u01F5":"g","\u011D":"g","\u1E21":"g","\u011F":"g","\u0121":"g","\u01E7":"g","\u0123":"g","\u01E5":"g","\u0260":"g","\uA7A1":"g","\u1D79":"g","\uA77F":"g","\u24D7":"h","\uFF48":"h","\u0125":"h","\u1E23":"h","\u1E27":"h","\u021F":"h","\u1E25":"h","\u1E29":"h","\u1E2B":"h","\u1E96":"h","\u0127":"h","\u2C68":"h","\u2C76":"h","\u0265":"h","\u0195":"hv","\u24D8":"i","\uFF49":"i","\u00EC":"i","\u00ED":"i","\u00EE":"i","\u0129":"i","\u012B":"i","\u012D":"i","\u00EF":"i","\u1E2F":"i","\u1EC9":"i","\u01D0":"i","\u0209":"i","\u020B":"i","\u1ECB":"i","\u012F":"i","\u1E2D":"i","\u0268":"i","\u0131":"i","\u24D9":"j","\uFF4A":"j","\u0135":"j","\u01F0":"j","\u0249":"j","\u24DA":"k","\uFF4B":"k","\u1E31":"k","\u01E9":"k","\u1E33":"k","\u0137":"k","\u1E35":"k","\u0199":"k","\u2C6A":"k","\uA741":"k","\uA743":"k","\uA745":"k","\uA7A3":"k","\u24DB":"l","\uFF4C":"l","\u0140":"l","\u013A":"l","\u013E":"l","\u1E37":"l","\u1E39":"l","\u013C":"l","\u1E3D":"l","\u1E3B":"l","\u017F":"l","\u0142":"l","\u019A":"l","\u026B":"l","\u2C61":"l","\uA749":"l","\uA781":"l","\uA747":"l","\u01C9":"lj","\u24DC":"m","\uFF4D":"m","\u1E3F":"m","\u1E41":"m","\u1E43":"m","\u0271":"m","\u026F":"m","\u24DD":"n","\uFF4E":"n","\u01F9":"n","\u0144":"n","\u00F1":"n","\u1E45":"n","\u0148":"n","\u1E47":"n","\u0146":"n","\u1E4B":"n","\u1E49":"n","\u019E":"n","\u0272":"n","\u0149":"n","\uA791":"n","\uA7A5":"n","\u01CC":"nj","\u24DE":"o","\uFF4F":"o","\u00F2":"o","\u00F3":"o","\u00F4":"o","\u1ED3":"o","\u1ED1":"o","\u1ED7":"o","\u1ED5":"o","\u00F5":"o","\u1E4D":"o","\u022D":"o","\u1E4F":"o","\u014D":"o","\u1E51":"o","\u1E53":"o","\u014F":"o","\u022F":"o","\u0231":"o","\u00F6":"o","\u022B":"o","\u1ECF":"o","\u0151":"o","\u01D2":"o","\u020D":"o","\u020F":"o","\u01A1":"o","\u1EDD":"o","\u1EDB":"o","\u1EE1":"o","\u1EDF":"o","\u1EE3":"o","\u1ECD":"o","\u1ED9":"o","\u01EB":"o","\u01ED":"o","\u00F8":"o","\u01FF":"o","\u0254":"o","\uA74B":"o","\uA74D":"o","\u0275":"o","\u01A3":"oi","\u0223":"ou","\uA74F":"oo","\u24DF":"p","\uFF50":"p","\u1E55":"p","\u1E57":"p","\u01A5":"p","\u1D7D":"p","\uA751":"p","\uA753":"p","\uA755":"p","\u24E0":"q","\uFF51":"q","\u024B":"q","\uA757":"q","\uA759":"q","\u24E1":"r","\uFF52":"r","\u0155":"r","\u1E59":"r","\u0159":"r","\u0211":"r","\u0213":"r","\u1E5B":"r","\u1E5D":"r","\u0157":"r","\u1E5F":"r","\u024D":"r","\u027D":"r","\uA75B":"r","\uA7A7":"r","\uA783":"r","\u24E2":"s","\uFF53":"s","\u00DF":"s","\u015B":"s","\u1E65":"s","\u015D":"s","\u1E61":"s","\u0161":"s","\u1E67":"s","\u1E63":"s","\u1E69":"s","\u0219":"s","\u015F":"s","\u023F":"s","\uA7A9":"s","\uA785":"s","\u1E9B":"s","\u24E3":"t","\uFF54":"t","\u1E6B":"t","\u1E97":"t","\u0165":"t","\u1E6D":"t","\u021B":"t","\u0163":"t","\u1E71":"t","\u1E6F":"t","\u0167":"t","\u01AD":"t","\u0288":"t","\u2C66":"t","\uA787":"t","\uA729":"tz","\u24E4":"u","\uFF55":"u","\u00F9":"u","\u00FA":"u","\u00FB":"u","\u0169":"u","\u1E79":"u","\u016B":"u","\u1E7B":"u","\u016D":"u","\u00FC":"u","\u01DC":"u","\u01D8":"u","\u01D6":"u","\u01DA":"u","\u1EE7":"u","\u016F":"u","\u0171":"u","\u01D4":"u","\u0215":"u","\u0217":"u","\u01B0":"u","\u1EEB":"u","\u1EE9":"u","\u1EEF":"u","\u1EED":"u","\u1EF1":"u","\u1EE5":"u","\u1E73":"u","\u0173":"u","\u1E77":"u","\u1E75":"u","\u0289":"u","\u24E5":"v","\uFF56":"v","\u1E7D":"v","\u1E7F":"v","\u028B":"v","\uA75F":"v","\u028C":"v","\uA761":"vy","\u24E6":"w","\uFF57":"w","\u1E81":"w","\u1E83":"w","\u0175":"w","\u1E87":"w","\u1E85":"w","\u1E98":"w","\u1E89":"w","\u2C73":"w","\u24E7":"x","\uFF58":"x","\u1E8B":"x","\u1E8D":"x","\u24E8":"y","\uFF59":"y","\u1EF3":"y","\u00FD":"y","\u0177":"y","\u1EF9":"y","\u0233":"y","\u1E8F":"y","\u00FF":"y","\u1EF7":"y","\u1E99":"y","\u1EF5":"y","\u01B4":"y","\u024F":"y","\u1EFF":"y","\u24E9":"z","\uFF5A":"z","\u017A":"z","\u1E91":"z","\u017C":"z","\u017E":"z","\u1E93":"z","\u1E95":"z","\u01B6":"z","\u0225":"z","\u0240":"z","\u2C6C":"z","\uA763":"z"};

    $document = $(document);

    nextUid=(function() { var counter=1; return function() { return counter++; }; }());


    function stripDiacritics(str) {
        var ret, i, l, c;

        if (!str || str.length < 1) return str;

        ret = "";
        for (i = 0, l = str.length; i < l; i++) {
            c = str.charAt(i);
            ret += DIACRITICS[c] || c;
        }
        return ret;
    }

    function indexOf(value, array) {
        var i = 0, l = array.length;
        for (; i < l; i = i + 1) {
            if (equal(value, array[i])) return i;
        }
        return -1;
    }

    function measureScrollbar () {
        var $template = $( MEASURE_SCROLLBAR_TEMPLATE );
        $template.appendTo('body');

        var dim = {
            width: $template.width() - $template[0].clientWidth,
            height: $template.height() - $template[0].clientHeight
        };
        $template.remove();

        return dim;
    }

    /**
     * Compares equality of a and b
     * @param a
     * @param b
     */
    function equal(a, b) {
        if (a === b) return true;
        if (a === undefined || b === undefined) return false;
        if (a === null || b === null) return false;
        // Check whether 'a' or 'b' is a string (primitive or object).
        // The concatenation of an empty string (+'') converts its argument to a string's primitive.
        if (a.constructor === String) return a+'' === b+''; // a+'' - in case 'a' is a String object
        if (b.constructor === String) return b+'' === a+''; // b+'' - in case 'b' is a String object
        return false;
    }

    /**
     * Splits the string into an array of values, trimming each value. An empty array is returned for nulls or empty
     * strings
     * @param string
     * @param separator
     */
    function splitVal(string, separator) {
        var val, i, l;
        if (string === null || string.length < 1) return [];
        val = string.split(separator);
        for (i = 0, l = val.length; i < l; i = i + 1) val[i] = $.trim(val[i]);
        return val;
    }

    function getSideBorderPadding(element) {
        return element.outerWidth(false) - element.width();
    }

    function installKeyUpChangeEvent(element) {
        var key="keyup-change-value";
        element.on("keydown", function () {
            if ($.data(element, key) === undefined) {
                $.data(element, key, element.val());
            }
        });
        element.on("keyup", function () {
            var val= $.data(element, key);
            if (val !== undefined && element.val() !== val) {
                $.removeData(element, key);
                element.trigger("keyup-change");
            }
        });
    }

    $document.on("mousemove", function (e) {
        lastMousePosition.x = e.pageX;
        lastMousePosition.y = e.pageY;
    });

    /**
     * filters mouse events so an event is fired only if the mouse moved.
     *
     * filters out mouse events that occur when mouse is stationary but
     * the elements under the pointer are scrolled.
     */
    function installFilteredMouseMove(element) {
        element.on("mousemove", function (e) {
            var lastpos = lastMousePosition;
            if (lastpos === undefined || lastpos.x !== e.pageX || lastpos.y !== e.pageY) {
                $(e.target).trigger("mousemove-filtered", e);
            }
        });
    }

    /**
     * Debounces a function. Returns a function that calls the original fn function only if no invocations have been made
     * within the last quietMillis milliseconds.
     *
     * @param quietMillis number of milliseconds to wait before invoking fn
     * @param fn function to be debounced
     * @param ctx object to be used as this reference within fn
     * @return debounced version of fn
     */
    function debounce(quietMillis, fn, ctx) {
        ctx = ctx || undefined;
        var timeout;
        return function () {
            var args = arguments;
            window.clearTimeout(timeout);
            timeout = window.setTimeout(function() {
                fn.apply(ctx, args);
            }, quietMillis);
        };
    }

    /**
     * A simple implementation of a thunk
     * @param formula function used to lazily initialize the thunk
     * @return {Function}
     */
    function thunk(formula) {
        var evaluated = false,
            value;
        return function() {
            if (evaluated === false) { value = formula(); evaluated = true; }
            return value;
        };
    };

    function installDebouncedScroll(threshold, element) {
        var notify = debounce(threshold, function (e) { element.trigger("scroll-debounced", e);});
        element.on("scroll", function (e) {
            if (indexOf(e.target, element.get()) >= 0) notify(e);
        });
    }

    function focus($el) {
        if ($el[0] === document.activeElement) return;

        /* set the focus in a 0 timeout - that way the focus is set after the processing
            of the current event has finished - which seems like the only reliable way
            to set focus */
        window.setTimeout(function() {
            var el=$el[0], pos=$el.val().length, range;

            $el.focus();

            /* make sure el received focus so we do not error out when trying to manipulate the caret.
                sometimes modals or others listeners may steal it after its set */
            if ($el.is(":visible") && el === document.activeElement) {

                /* after the focus is set move the caret to the end, necessary when we val()
                    just before setting focus */
                if(el.setSelectionRange)
                {
                    el.setSelectionRange(pos, pos);
                }
                else if (el.createTextRange) {
                    range = el.createTextRange();
                    range.collapse(false);
                    range.select();
                }
            }
        }, 0);
    }

    function getCursorInfo(el) {
        el = $(el)[0];
        var offset = 0;
        var length = 0;
        if ('selectionStart' in el) {
            offset = el.selectionStart;
            length = el.selectionEnd - offset;
        } else if ('selection' in document) {
            el.focus();
            var sel = document.selection.createRange();
            length = document.selection.createRange().text.length;
            sel.moveStart('character', -el.value.length);
            offset = sel.text.length - length;
        }
        return { offset: offset, length: length };
    }

    function killEvent(event) {
        event.preventDefault();
        event.stopPropagation();
    }
    function killEventImmediately(event) {
        event.preventDefault();
        event.stopImmediatePropagation();
    }

    function measureTextWidth(e) {
        if (!sizer){
            var style = e[0].currentStyle || window.getComputedStyle(e[0], null);
            sizer = $(document.createElement("div")).css({
                position: "absolute",
                left: "-10000px",
                top: "-10000px",
                display: "none",
                fontSize: style.fontSize,
                fontFamily: style.fontFamily,
                fontStyle: style.fontStyle,
                fontWeight: style.fontWeight,
                letterSpacing: style.letterSpacing,
                textTransform: style.textTransform,
                whiteSpace: "nowrap"
            });
            sizer.attr("class","select2-sizer");
            $("body").append(sizer);
        }
        sizer.text(e.val());
        return sizer.width();
    }

    function syncCssClasses(dest, src, adapter) {
        var classes, replacements = [], adapted;

        classes = dest.attr("class");
        if (classes) {
            classes = '' + classes; // for IE which returns object
            $(classes.split(" ")).each2(function() {
                if (this.indexOf("select2-") === 0) {
                    replacements.push(this);
                }
            });
        }
        classes = src.attr("class");
        if (classes) {
            classes = '' + classes; // for IE which returns object
            $(classes.split(" ")).each2(function() {
                if (this.indexOf("select2-") !== 0) {
                    adapted = adapter(this);
                    if (adapted) {
                        replacements.push(this);
                    }
                }
            });
        }
        dest.attr("class", replacements.join(" "));
    }


    function markMatch(text, term, markup, escapeMarkup) {
        var match=stripDiacritics(text.toUpperCase()).indexOf(stripDiacritics(term.toUpperCase())),
            tl=term.length;

        if (match<0) {
            markup.push(escapeMarkup(text));
            return;
        }

        markup.push(escapeMarkup(text.substring(0, match)));
        markup.push("<span class='select2-match'>");
        markup.push(escapeMarkup(text.substring(match, match + tl)));
        markup.push("</span>");
        markup.push(escapeMarkup(text.substring(match + tl, text.length)));
    }

    function defaultEscapeMarkup(markup) {
        var replace_map = {
            '\\': '&#92;',
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#39;',
            "/": '&#47;'
        };

        return String(markup).replace(/[&<>"'\/\\]/g, function (match) {
            return replace_map[match];
        });
    }

    /**
     * Produces an ajax-based query function
     *
     * @param options object containing configuration paramters
     * @param options.params parameter map for the transport ajax call, can contain such options as cache, jsonpCallback, etc. see $.ajax
     * @param options.transport function that will be used to execute the ajax request. must be compatible with parameters supported by $.ajax
     * @param options.url url for the data
     * @param options.data a function(searchTerm, pageNumber, context) that should return an object containing query string parameters for the above url.
     * @param options.dataType request data type: ajax, jsonp, other datatatypes supported by jQuery's $.ajax function or the transport function if specified
     * @param options.quietMillis (optional) milliseconds to wait before making the ajaxRequest, helps debounce the ajax function if invoked too often
     * @param options.results a function(remoteData, pageNumber) that converts data returned form the remote request to the format expected by Select2.
     *      The expected format is an object containing the following keys:
     *      results array of objects that will be used as choices
     *      more (optional) boolean indicating whether there are more results available
     *      Example: {results:[{id:1, text:'Red'},{id:2, text:'Blue'}], more:true}
     */
    function ajax(options) {
        var timeout, // current scheduled but not yet executed request
            handler = null,
            quietMillis = options.quietMillis || 100,
            ajaxUrl = options.url,
            self = this;

        return function (query) {
            window.clearTimeout(timeout);
            timeout = window.setTimeout(function () {
                var data = options.data, // ajax data function
                    url = ajaxUrl, // ajax url string or function
                    transport = options.transport || $.fn.select2.ajaxDefaults.transport,
                    // deprecated - to be removed in 4.0  - use params instead
                    deprecated = {
                        type: options.type || 'GET', // set type of request (GET or POST)
                        cache: options.cache || false,
                        jsonpCallback: options.jsonpCallback||undefined,
                        dataType: options.dataType||"json"
                    },
                    params = $.extend({}, $.fn.select2.ajaxDefaults.params, deprecated);

                data = data ? data.call(self, query.term, query.page, query.context) : null;
                url = (typeof url === 'function') ? url.call(self, query.term, query.page, query.context) : url;

                if (handler) { handler.abort(); }

                if (options.params) {
                    if ($.isFunction(options.params)) {
                        $.extend(params, options.params.call(self));
                    } else {
                        $.extend(params, options.params);
                    }
                }

                $.extend(params, {
                    url: url,
                    dataType: options.dataType,
                    data: data,
                    success: function (data) {
                        // TODO - replace query.page with query so users have access to term, page, etc.
                        var results = options.results(data, query.page);
                        query.callback(results);
                    }
                });
                handler = transport.call(self, params);
            }, quietMillis);
        };
    }

    /**
     * Produces a query function that works with a local array
     *
     * @param options object containing configuration parameters. The options parameter can either be an array or an
     * object.
     *
     * If the array form is used it is assumed that it contains objects with 'id' and 'text' keys.
     *
     * If the object form is used ti is assumed that it contains 'data' and 'text' keys. The 'data' key should contain
     * an array of objects that will be used as choices. These objects must contain at least an 'id' key. The 'text'
     * key can either be a String in which case it is expected that each element in the 'data' array has a key with the
     * value of 'text' which will be used to match choices. Alternatively, text can be a function(item) that can extract
     * the text.
     */
    function local(options) {
        var data = options, // data elements
            dataText,
            tmp,
            text = function (item) { return ""+item.text; }; // function used to retrieve the text portion of a data item that is matched against the search

         if ($.isArray(data)) {
            tmp = data;
            data = { results: tmp };
        }

         if ($.isFunction(data) === false) {
            tmp = data;
            data = function() { return tmp; };
        }

        var dataItem = data();
        if (dataItem.text) {
            text = dataItem.text;
            // if text is not a function we assume it to be a key name
            if (!$.isFunction(text)) {
                dataText = dataItem.text; // we need to store this in a separate variable because in the next step data gets reset and data.text is no longer available
                text = function (item) { return item[dataText]; };
            }
        }

        return function (query) {
            var t = query.term, filtered = { results: [] }, process;
            if (t === "") {
                query.callback(data());
                return;
            }

            process = function(datum, collection) {
                var group, attr;
                datum = datum[0];
                if (datum.children) {
                    group = {};
                    for (attr in datum) {
                        if (datum.hasOwnProperty(attr)) group[attr]=datum[attr];
                    }
                    group.children=[];
                    $(datum.children).each2(function(i, childDatum) { process(childDatum, group.children); });
                    if (group.children.length || query.matcher(t, text(group), datum)) {
                        collection.push(group);
                    }
                } else {
                    if (query.matcher(t, text(datum), datum)) {
                        collection.push(datum);
                    }
                }
            };

            $(data().results).each2(function(i, datum) { process(datum, filtered.results); });
            query.callback(filtered);
        };
    }

    // TODO javadoc
    function tags(data) {
        var isFunc = $.isFunction(data);
        return function (query) {
            var t = query.term, filtered = {results: []};
            $(isFunc ? data() : data).each(function () {
                var isObject = this.text !== undefined,
                    text = isObject ? this.text : this;
                if (t === "" || query.matcher(t, text)) {
                    filtered.results.push(isObject ? this : {id: this, text: this});
                }
            });
            query.callback(filtered);
        };
    }

    /**
     * Checks if the formatter function should be used.
     *
     * Throws an error if it is not a function. Returns true if it should be used,
     * false if no formatting should be performed.
     *
     * @param formatter
     */
    function checkFormatter(formatter, formatterName) {
        if ($.isFunction(formatter)) return true;
        if (!formatter) return false;
        throw new Error(formatterName +" must be a function or a falsy value");
    }

    function evaluate(val) {
        return $.isFunction(val) ? val() : val;
    }

    function countResults(results) {
        var count = 0;
        $.each(results, function(i, item) {
            if (item.children) {
                count += countResults(item.children);
            } else {
                count++;
            }
        });
        return count;
    }

    /**
     * Default tokenizer. This function uses breaks the input on substring match of any string from the
     * opts.tokenSeparators array and uses opts.createSearchChoice to create the choice object. Both of those
     * two options have to be defined in order for the tokenizer to work.
     *
     * @param input text user has typed so far or pasted into the search field
     * @param selection currently selected choices
     * @param selectCallback function(choice) callback tho add the choice to selection
     * @param opts select2's opts
     * @return undefined/null to leave the current input unchanged, or a string to change the input to the returned value
     */
    function defaultTokenizer(input, selection, selectCallback, opts) {
        var original = input, // store the original so we can compare and know if we need to tell the search to update its text
            dupe = false, // check for whether a token we extracted represents a duplicate selected choice
            token, // token
            index, // position at which the separator was found
            i, l, // looping variables
            separator; // the matched separator

        if (!opts.createSearchChoice || !opts.tokenSeparators || opts.tokenSeparators.length < 1) return undefined;

        while (true) {
            index = -1;

            for (i = 0, l = opts.tokenSeparators.length; i < l; i++) {
                separator = opts.tokenSeparators[i];
                index = input.indexOf(separator);
                if (index >= 0) break;
            }

            if (index < 0) break; // did not find any token separator in the input string, bail

            token = input.substring(0, index);
            input = input.substring(index + separator.length);

            if (token.length > 0) {
                token = opts.createSearchChoice.call(this, token, selection);
                if (token !== undefined && token !== null && opts.id(token) !== undefined && opts.id(token) !== null) {
                    dupe = false;
                    for (i = 0, l = selection.length; i < l; i++) {
                        if (equal(opts.id(token), opts.id(selection[i]))) {
                            dupe = true; break;
                        }
                    }

                    if (!dupe) selectCallback(token);
                }
            }
        }

        if (original!==input) return input;
    }

    /**
     * Creates a new class
     *
     * @param superClass
     * @param methods
     */
    function clazz(SuperClass, methods) {
        var constructor = function () {};
        constructor.prototype = new SuperClass;
        constructor.prototype.constructor = constructor;
        constructor.prototype.parent = SuperClass.prototype;
        constructor.prototype = $.extend(constructor.prototype, methods);
        return constructor;
    }

    AbstractSelect2 = clazz(Object, {

        // abstract
        bind: function (func) {
            var self = this;
            return function () {
                func.apply(self, arguments);
            };
        },

        // abstract
        init: function (opts) {
            var results, search, resultsSelector = ".select2-results", disabled, readonly;

            // prepare options
            this.opts = opts = this.prepareOpts(opts);

            this.id=opts.id;

            // destroy if called on an existing component
            if (opts.element.data("select2") !== undefined &&
                opts.element.data("select2") !== null) {
                opts.element.data("select2").destroy();
            }

            this.container = this.createContainer();

            this.containerId="s2id_"+(opts.element.attr("id") || "autogen"+nextUid());
            this.containerSelector="#"+this.containerId.replace(/([;&,\.\+\*\~':"\!\^#$%@\[\]\(\)=>\|])/g, '\\$1');
            this.container.attr("id", this.containerId);

            // cache the body so future lookups are cheap
            this.body = thunk(function() { return opts.element.closest("body"); });

            syncCssClasses(this.container, this.opts.element, this.opts.adaptContainerCssClass);

            this.container.attr("style", opts.element.attr("style"));
            this.container.css(evaluate(opts.containerCss));
            this.container.addClass(evaluate(opts.containerCssClass));

            this.elementTabIndex = this.opts.element.attr("tabindex");

            // swap container for the element
            this.opts.element
                .data("select2", this)
                .attr("tabindex", "-1")
                .before(this.container);
            this.container.data("select2", this);

            this.dropdown = this.container.find(".select2-drop");
            this.dropdown.addClass(evaluate(opts.dropdownCssClass));
            this.dropdown.data("select2", this);

            syncCssClasses(this.dropdown, this.opts.element, this.opts.adaptDropdownCssClass);

            this.results = results = this.container.find(resultsSelector);
            this.search = search = this.container.find("input.select2-input");

            this.queryCount = 0;
            this.resultsPage = 0;
            this.context = null;

            // initialize the container
            this.initContainer();

            installFilteredMouseMove(this.results);
            this.dropdown.on("mousemove-filtered touchstart touchmove touchend", resultsSelector, this.bind(this.highlightUnderEvent));

            installDebouncedScroll(80, this.results);
            this.dropdown.on("scroll-debounced", resultsSelector, this.bind(this.loadMoreIfNeeded));

            // do not propagate change event from the search field out of the component
            $(this.container).on("change", ".select2-input", function(e) {e.stopPropagation();});
            $(this.dropdown).on("change", ".select2-input", function(e) {e.stopPropagation();});

            // if jquery.mousewheel plugin is installed we can prevent out-of-bounds scrolling of results via mousewheel
            if ($.fn.mousewheel) {
                results.mousewheel(function (e, delta, deltaX, deltaY) {
                    var top = results.scrollTop(), height;
                    if (deltaY > 0 && top - deltaY <= 0) {
                        results.scrollTop(0);
                        killEvent(e);
                    } else if (deltaY < 0 && results.get(0).scrollHeight - results.scrollTop() + deltaY <= results.height()) {
                        results.scrollTop(results.get(0).scrollHeight - results.height());
                        killEvent(e);
                    }
                });
            }

            installKeyUpChangeEvent(search);
            search.on("keyup-change input paste", this.bind(this.updateResults));
            search.on("focus", function () { search.addClass("select2-focused"); });
            search.on("blur", function () { search.removeClass("select2-focused");});

            this.dropdown.on("mouseup", resultsSelector, this.bind(function (e) {
                if ($(e.target).closest(".select2-result-selectable").length > 0) {
                    this.highlightUnderEvent(e);
                    this.selectHighlighted(e);
                }
            }));

            // trap all mouse events from leaving the dropdown. sometimes there may be a modal that is listening
            // for mouse events outside of itself so it can close itself. since the dropdown is now outside the select2's
            // dom it will trigger the popup close, which is not what we want
            this.dropdown.on("click mouseup mousedown", function (e) { e.stopPropagation(); });

            if ($.isFunction(this.opts.initSelection)) {
                // initialize selection based on the current value of the source element
                this.initSelection();

                // if the user has provided a function that can set selection based on the value of the source element
                // we monitor the change event on the element and trigger it, allowing for two way synchronization
                this.monitorSource();
            }

            if (opts.maximumInputLength !== null) {
                this.search.attr("maxlength", opts.maximumInputLength);
            }

            var disabled = opts.element.prop("disabled");
            if (disabled === undefined) disabled = false;
            this.enable(!disabled);

            var readonly = opts.element.prop("readonly");
            if (readonly === undefined) readonly = false;
            this.readonly(readonly);

            // Calculate size of scrollbar
            scrollBarDimensions = scrollBarDimensions || measureScrollbar();

            this.autofocus = opts.element.prop("autofocus");
            opts.element.prop("autofocus", false);
            if (this.autofocus) this.focus();

            this.nextSearchTerm = undefined;
        },

        // abstract
        destroy: function () {
            var element=this.opts.element, select2 = element.data("select2");

            this.close();

            if (this.propertyObserver) { delete this.propertyObserver; this.propertyObserver = null; }

            if (select2 !== undefined) {
                select2.container.remove();
                select2.dropdown.remove();
                element
                    .removeClass("select2-offscreen")
                    .removeData("select2")
                    .off(".select2")
                    .prop("autofocus", this.autofocus || false);
                if (this.elementTabIndex) {
                    element.attr({tabindex: this.elementTabIndex});
                } else {
                    element.removeAttr("tabindex");
                }
                element.show();
            }
        },

        // abstract
        optionToData: function(element) {
            if (element.is("option")) {
                return {
                    id:element.prop("value"),
                    text:element.text(),
                    element: element.get(),
                    css: element.attr("class"),
                    disabled: element.prop("disabled"),
                    locked: equal(element.attr("locked"), "locked") || equal(element.data("locked"), true)
                };
            } else if (element.is("optgroup")) {
                return {
                    text:element.attr("label"),
                    children:[],
                    element: element.get(),
                    css: element.attr("class")
                };
            }
        },

        // abstract
        prepareOpts: function (opts) {
            var element, select, idKey, ajaxUrl, self = this;

            element = opts.element;

            if (element.get(0).tagName.toLowerCase() === "select") {
                this.select = select = opts.element;
            }

            if (select) {
                // these options are not allowed when attached to a select because they are picked up off the element itself
                // Modified by Oleg to keep user input text
                $.each(["id", "multiple", "ajax", "query", "createSearchChoice", "initSelection", "data", "tags"], function () {
//                $.each(["id", "multiple", "ajax", "query", "initSelection", "data", "tags"], function () {
                    if (this in opts) {
                        throw new Error("Option '" + this + "' is not allowed for Select2 when attached to a <select> element.");
                    }
                });
            }

            opts = $.extend({}, {
                populateResults: function(container, results, query) {
                    var populate,  data, result, children, id=this.opts.id;

                    populate=function(results, container, depth) {

                        var i, l, result, selectable, disabled, compound, node, label, innerContainer, formatted;

                        results = opts.sortResults(results, container, query);

                        for (i = 0, l = results.length; i < l; i = i + 1) {

                            result=results[i];

                            disabled = (result.disabled === true);
                            selectable = (!disabled) && (id(result) !== undefined);

                            compound=result.children && result.children.length > 0;

                            node=$("<li></li>");
                            node.addClass("select2-results-dept-"+depth);
                            node.addClass("select2-result");
                            node.addClass(selectable ? "select2-result-selectable" : "select2-result-unselectable");
                            if (disabled) { node.addClass("select2-disabled"); }
                            if (compound) { node.addClass("select2-result-with-children"); }
                            node.addClass(self.opts.formatResultCssClass(result));

                            label=$(document.createElement("div"));
                            label.addClass("select2-result-label");

                            formatted=opts.formatResult(result, label, query, self.opts.escapeMarkup);
                            if (formatted!==undefined) {
                                label.html(formatted);
                            }

                            node.append(label);

                            if (compound) {

                                innerContainer=$("<ul></ul>");
                                innerContainer.addClass("select2-result-sub");
                                populate(result.children, innerContainer, depth+1);
                                node.append(innerContainer);
                            }

                            node.data("select2-data", result);
                            container.append(node);
                        }
                    };

                    populate(results, container, 0);
                }
            }, $.fn.select2.defaults, opts);

            if (typeof(opts.id) !== "function") {
                idKey = opts.id;
                opts.id = function (e) { return e[idKey]; };
            }

            if ($.isArray(opts.element.data("select2Tags"))) {
                if ("tags" in opts) {
                    throw "tags specified as both an attribute 'data-select2-tags' and in options of Select2 " + opts.element.attr("id");
                }
                opts.tags=opts.element.data("select2Tags");
            }

            if (select) {
                opts.query = this.bind(function (query) {
                    var data = { results: [], more: false },
                        term = query.term,
                        children, placeholderOption, process;

                    process=function(element, collection) {
                        var group;
                        if (element.is("option")) {
                            if (query.matcher(term, element.text(), element)) {
                                collection.push(self.optionToData(element));
                            }
                        } else if (element.is("optgroup")) {
                            group=self.optionToData(element);
                            element.children().each2(function(i, elm) { process(elm, group.children); });
                            if (group.children.length>0) {
                                collection.push(group);
                            }
                        }
                    };

                    children=element.children();

                    // ignore the placeholder option if there is one
                    if (this.getPlaceholder() !== undefined && children.length > 0) {
                        placeholderOption = this.getPlaceholderOption();
                        if (placeholderOption) {
                            children=children.not(placeholderOption);
                        }
                    }

                    children.each2(function(i, elm) { process(elm, data.results); });

                    query.callback(data);
                });
                // this is needed because inside val() we construct choices from options and there id is hardcoded
                opts.id=function(e) { return e.id; };
                opts.formatResultCssClass = function(data) { return data.css; };
            } else {
                if (!("query" in opts)) {

                    if ("ajax" in opts) {
                        ajaxUrl = opts.element.data("ajax-url");
                        if (ajaxUrl && ajaxUrl.length > 0) {
                            opts.ajax.url = ajaxUrl;
                        }
                        opts.query = ajax.call(opts.element, opts.ajax);
                    } else if ("data" in opts) {
                        opts.query = local(opts.data);
                    } else if ("tags" in opts) {
                        opts.query = tags(opts.tags);
                        if (opts.createSearchChoice === undefined) {
                            opts.createSearchChoice = function (term) { return {id: $.trim(term), text: $.trim(term)}; };
                        }
                        if (opts.initSelection === undefined) {
                            opts.initSelection = function (element, callback) {
                                var data = [];
                                $(splitVal(element.val(), opts.separator)).each(function () {
                                    var id = this, text = this, tags=opts.tags;
                                    if ($.isFunction(tags)) tags=tags();
                                    $(tags).each(function() { if (equal(this.id, id)) { text = this.text; return false; } });
                                    data.push({id: id, text: text});
                                });

                                callback(data);
                            };
                        }
                    }
                }
            }
            if (typeof(opts.query) !== "function") {
                throw "query function not defined for Select2 " + opts.element.attr("id");
            }

            return opts;
        },

        /**
         * Monitor the original element for changes and update select2 accordingly
         */
        // abstract
        monitorSource: function () {
            var el = this.opts.element, sync;

            el.on("change.select2", this.bind(function (e) {
                if (this.opts.element.data("select2-change-triggered") !== true) {
                    this.initSelection();
                }
            }));

            sync = this.bind(function () {

                var enabled, readonly, self = this;

                // sync enabled state
                var disabled = el.prop("disabled");
                if (disabled === undefined) disabled = false;
                this.enable(!disabled);

                var readonly = el.prop("readonly");
                if (readonly === undefined) readonly = false;
                this.readonly(readonly);

                syncCssClasses(this.container, this.opts.element, this.opts.adaptContainerCssClass);
                this.container.addClass(evaluate(this.opts.containerCssClass));

                syncCssClasses(this.dropdown, this.opts.element, this.opts.adaptDropdownCssClass);
                this.dropdown.addClass(evaluate(this.opts.dropdownCssClass));

            });

            // mozilla and IE
            el.on("propertychange.select2 DOMAttrModified.select2", sync);


            // hold onto a reference of the callback to work around a chromium bug
            if (this.mutationCallback === undefined) {
                this.mutationCallback = function (mutations) {
                    mutations.forEach(sync);
                }
            }

            // safari and chrome
            if (typeof WebKitMutationObserver !== "undefined") {
                if (this.propertyObserver) { delete this.propertyObserver; this.propertyObserver = null; }
                this.propertyObserver = new WebKitMutationObserver(this.mutationCallback);
                this.propertyObserver.observe(el.get(0), { attributes:true, subtree:false });
            }
        },

        // abstract
        triggerSelect: function(data) {
            var evt = $.Event("select2-selecting", { val: this.id(data), object: data });
            this.opts.element.trigger(evt);
            return !evt.isDefaultPrevented();
        },

        /**
         * Triggers the change event on the source element
         */
        // abstract
        triggerChange: function (details) {

            details = details || {};
            details= $.extend({}, details, { type: "change", val: this.val() });
            // prevents recursive triggering
            this.opts.element.data("select2-change-triggered", true);
            this.opts.element.trigger(details);
            this.opts.element.data("select2-change-triggered", false);

            // some validation frameworks ignore the change event and listen instead to keyup, click for selects
            // so here we trigger the click event manually
            this.opts.element.click();

            // ValidationEngine ignorea the change event and listens instead to blur
            // so here we trigger the blur event manually if so desired
            if (this.opts.blurOnChange)
                this.opts.element.blur();
        },

        //abstract
        isInterfaceEnabled: function()
        {
            return this.enabledInterface === true;
        },

        // abstract
        enableInterface: function() {
            var enabled = this._enabled && !this._readonly,
                disabled = !enabled;

            if (enabled === this.enabledInterface) return false;

            this.container.toggleClass("select2-container-disabled", disabled);
            this.close();
            this.enabledInterface = enabled;

            return true;
        },

        // abstract
        enable: function(enabled) {
            if (enabled === undefined) enabled = true;
            if (this._enabled === enabled) return;
            this._enabled = enabled;

            this.opts.element.prop("disabled", !enabled);
            this.enableInterface();
        },

        // abstract
        disable: function() {
            this.enable(false);
        },

        // abstract
        readonly: function(enabled) {
            if (enabled === undefined) enabled = false;
            if (this._readonly === enabled) return false;
            this._readonly = enabled;

            this.opts.element.prop("readonly", enabled);
            this.enableInterface();
            return true;
        },

        // abstract
        opened: function () {
            return this.container.hasClass("select2-dropdown-open");
        },

        // abstract
        positionDropdown: function() {
            var $dropdown = this.dropdown,
                offset = this.container.offset(),
                height = this.container.outerHeight(false),
                width = this.container.outerWidth(false),
                dropHeight = $dropdown.outerHeight(false),
                viewPortRight = $(window).scrollLeft() + $(window).width(),
                viewportBottom = $(window).scrollTop() + $(window).height(),
                dropTop = offset.top + height,
                dropLeft = offset.left,
                enoughRoomBelow = dropTop + dropHeight <= viewportBottom,
                enoughRoomAbove = (offset.top - dropHeight) >= this.body().scrollTop(),
                dropWidth = $dropdown.outerWidth(false),
                enoughRoomOnRight = dropLeft + dropWidth <= viewPortRight,
                aboveNow = $dropdown.hasClass("select2-drop-above"),
                bodyOffset,
                above,
                css,
                resultsListNode;

            if (this.opts.dropdownAutoWidth) {
                resultsListNode = $('.select2-results', $dropdown)[0];
                $dropdown.addClass('select2-drop-auto-width');
                $dropdown.css('width', '');
                // Add scrollbar width to dropdown if vertical scrollbar is present
                dropWidth = $dropdown.outerWidth(false) + (resultsListNode.scrollHeight === resultsListNode.clientHeight ? 0 : scrollBarDimensions.width);
                dropWidth > width ? width = dropWidth : dropWidth = width;
                enoughRoomOnRight = dropLeft + dropWidth <= viewPortRight;
            }
            else {
                this.container.removeClass('select2-drop-auto-width');
            }

            //console.log("below/ droptop:", dropTop, "dropHeight", dropHeight, "sum", (dropTop+dropHeight)+" viewport bottom", viewportBottom, "enough?", enoughRoomBelow);
            //console.log("above/ offset.top", offset.top, "dropHeight", dropHeight, "top", (offset.top-dropHeight), "scrollTop", this.body().scrollTop(), "enough?", enoughRoomAbove);

            // fix positioning when body has an offset and is not position: static
            if (this.body().css('position') !== 'static') {
                bodyOffset = this.body().offset();
                dropTop -= bodyOffset.top;
                dropLeft -= bodyOffset.left;
            }

            // always prefer the current above/below alignment, unless there is not enough room
            if (aboveNow) {
                above = true;
                if (!enoughRoomAbove && enoughRoomBelow) above = false;
            } else {
                above = false;
                if (!enoughRoomBelow && enoughRoomAbove) above = true;
            }

            if (!enoughRoomOnRight) {
               dropLeft = offset.left + width - dropWidth;
            }

            if (above) {
                dropTop = offset.top - dropHeight;
                this.container.addClass("select2-drop-above");
                $dropdown.addClass("select2-drop-above");
            }
            else {
                this.container.removeClass("select2-drop-above");
                $dropdown.removeClass("select2-drop-above");
            }

            css = $.extend({
                top: dropTop,
                left: dropLeft,
                width: width
            }, evaluate(this.opts.dropdownCss));

            $dropdown.css(css);
        },

        // abstract
        shouldOpen: function() {
            var event;

            if (this.opened()) return false;

            if (this._enabled === false || this._readonly === true) return false;

            event = $.Event("select2-opening");
            this.opts.element.trigger(event);
            return !event.isDefaultPrevented();
        },

        // abstract
        clearDropdownAlignmentPreference: function() {
            // clear the classes used to figure out the preference of where the dropdown should be opened
            this.container.removeClass("select2-drop-above");
            this.dropdown.removeClass("select2-drop-above");
        },

        /**
         * Opens the dropdown
         *
         * @return {Boolean} whether or not dropdown was opened. This method will return false if, for example,
         * the dropdown is already open, or if the 'open' event listener on the element called preventDefault().
         */
        // abstract
        open: function () {

            if (!this.shouldOpen()) return false;

            this.opening();

            return true;
        },

        /**
         * Performs the opening of the dropdown
         */
        // abstract
        opening: function() {
            var cid = this.containerId,
                scroll = "scroll." + cid,
                resize = "resize."+cid,
                orient = "orientationchange."+cid,
                mask, maskCss;

            this.container.addClass("select2-dropdown-open").addClass("select2-container-active");

            this.clearDropdownAlignmentPreference();

            if(this.dropdown[0] !== this.body().children().last()[0]) {
                this.dropdown.detach().appendTo(this.body());
            }

            // create the dropdown mask if doesnt already exist
            mask = $("#select2-drop-mask");
            if (mask.length == 0) {
                mask = $(document.createElement("div"));
                mask.attr("id","select2-drop-mask").attr("class","select2-drop-mask");
                mask.hide();
                mask.appendTo(this.body());
                mask.on("mousedown touchstart click", function (e) {
                    var dropdown = $("#select2-drop"), self;
                    if (dropdown.length > 0) {
                        self=dropdown.data("select2");
                        if (self.opts.selectOnBlur) {
                            self.selectHighlighted({noFocus: true});
                        }
                        self.close({focus:false});
                        e.preventDefault();
                        e.stopPropagation();
                    }
                });
            }

            // ensure the mask is always right before the dropdown
            if (this.dropdown.prev()[0] !== mask[0]) {
                this.dropdown.before(mask);
            }

            // move the global id to the correct dropdown
            $("#select2-drop").removeAttr("id");
            this.dropdown.attr("id", "select2-drop");

            // show the elements
            mask.show();

            this.positionDropdown();
            this.dropdown.show();
            this.positionDropdown();

            this.dropdown.addClass("select2-drop-active");

            // attach listeners to events that can change the position of the container and thus require
            // the position of the dropdown to be updated as well so it does not come unglued from the container
            var that = this;
            this.container.parents().add(window).each(function () {
                $(this).on(resize+" "+scroll+" "+orient, function (e) {
                    that.positionDropdown();
                });
            });


        },

        // abstract
        close: function () {
            if (!this.opened()) return;

            var cid = this.containerId,
                scroll = "scroll." + cid,
                resize = "resize."+cid,
                orient = "orientationchange."+cid;

            // unbind event listeners
            this.container.parents().add(window).each(function () { $(this).off(scroll).off(resize).off(orient); });

            this.clearDropdownAlignmentPreference();

            $("#select2-drop-mask").hide();
            this.dropdown.removeAttr("id"); // only the active dropdown has the select2-drop id
            this.dropdown.hide();
            this.container.removeClass("select2-dropdown-open");
            this.results.empty();


            this.clearSearch();
            this.search.removeClass("select2-active");
            this.opts.element.trigger($.Event("select2-close"));
        },

        /**
         * Opens control, sets input value, and updates results.
         */
        // abstract
        externalSearch: function (term) {
            this.open();
            this.search.val(term);
            this.updateResults(false);
        },

        // abstract
        clearSearch: function () {

        },

        //abstract
        getMaximumSelectionSize: function() {
            return evaluate(this.opts.maximumSelectionSize);
        },

        // abstract
        ensureHighlightVisible: function () {
            var results = this.results, children, index, child, hb, rb, y, more;

            index = this.highlight();

            if (index < 0) return;

            if (index == 0) {

                // if the first element is highlighted scroll all the way to the top,
                // that way any unselectable headers above it will also be scrolled
                // into view

                results.scrollTop(0);
                return;
            }

            children = this.findHighlightableChoices().find('.select2-result-label');

            child = $(children[index]);

            hb = child.offset().top + child.outerHeight(true);

            // if this is the last child lets also make sure select2-more-results is visible
            if (index === children.length - 1) {
                more = results.find("li.select2-more-results");
                if (more.length > 0) {
                    hb = more.offset().top + more.outerHeight(true);
                }
            }

            rb = results.offset().top + results.outerHeight(true);
            if (hb > rb) {
                results.scrollTop(results.scrollTop() + (hb - rb));
            }
            y = child.offset().top - results.offset().top;

            // make sure the top of the element is visible
            if (y < 0 && child.css('display') != 'none' ) {
                results.scrollTop(results.scrollTop() + y); // y is negative
            }
        },

        // abstract
        findHighlightableChoices: function() {
            return this.results.find(".select2-result-selectable:not(.select2-selected):not(.select2-disabled)");
        },

        // abstract
        moveHighlight: function (delta) {
            var choices = this.findHighlightableChoices(),
                index = this.highlight();

            while (index > -1 && index < choices.length) {
                index += delta;
                var choice = $(choices[index]);
                if (choice.hasClass("select2-result-selectable") && !choice.hasClass("select2-disabled") && !choice.hasClass("select2-selected")) {
                    this.highlight(index);
                    break;
                }
            }
        },

        // abstract
        highlight: function (index) {
            var choices = this.findHighlightableChoices(),
                choice,
                data;

            if (arguments.length === 0) {
                return indexOf(choices.filter(".select2-highlighted")[0], choices.get());
            }

            if (index >= choices.length) index = choices.length - 1;
            if (index < 0) index = 0;

            this.removeHighlight();

            choice = $(choices[index]);
            choice.addClass("select2-highlighted");

            this.ensureHighlightVisible();

            data = choice.data("select2-data");
            if (data) {
                this.opts.element.trigger({ type: "select2-highlight", val: this.id(data), choice: data });
            }
        },

        removeHighlight: function() {
            this.results.find(".select2-highlighted").removeClass("select2-highlighted");
        },

        // abstract
        countSelectableResults: function() {
            return this.findHighlightableChoices().length;
        },

        // abstract
        highlightUnderEvent: function (event) {
            var el = $(event.target).closest(".select2-result-selectable");
            if (el.length > 0 && !el.is(".select2-highlighted")) {
                var choices = this.findHighlightableChoices();
                this.highlight(choices.index(el));
            } else if (el.length == 0) {
                // if we are over an unselectable item remove all highlights
                this.removeHighlight();
            }
        },

        // abstract
        loadMoreIfNeeded: function () {
            var results = this.results,
                more = results.find("li.select2-more-results"),
                below, // pixels the element is below the scroll fold, below==0 is when the element is starting to be visible
                offset = -1, // index of first element without data
                page = this.resultsPage + 1,
                self=this,
                term=this.search.val(),
                context=this.context;

            if (more.length === 0) return;
            below = more.offset().top - results.offset().top - results.height();

            if (below <= this.opts.loadMorePadding) {
                more.addClass("select2-active");
                this.opts.query({
                        element: this.opts.element,
                        term: term,
                        page: page,
                        context: context,
                        matcher: this.opts.matcher,
                        callback: this.bind(function (data) {

                    // ignore a response if the select2 has been closed before it was received
                    if (!self.opened()) return;


                    self.opts.populateResults.call(this, results, data.results, {term: term, page: page, context:context});
                    self.postprocessResults(data, false, false);

                    if (data.more===true) {
                        more.detach().appendTo(results).text(self.opts.formatLoadMore(page+1));
                        window.setTimeout(function() { self.loadMoreIfNeeded(); }, 10);
                    } else {
                        more.remove();
                    }
                    self.positionDropdown();
                    self.resultsPage = page;
                    self.context = data.context;
                    this.opts.element.trigger({ type: "select2-loaded", items: data });
                })});
            }
        },

        /**
         * Default tokenizer function which does nothing
         */
        tokenize: function() {

        },

        /**
         * @param initial whether or not this is the call to this method right after the dropdown has been opened
         */
        // abstract
        updateResults: function (initial) {
            var search = this.search,
                results = this.results,
                opts = this.opts,
                data,
                self = this,
                input,
                term = search.val(),
                lastTerm = $.data(this.container, "select2-last-term"),
                // sequence number used to drop out-of-order responses
                queryNumber;

            // prevent duplicate queries against the same term
            if (initial !== true && lastTerm && equal(term, lastTerm)) return;

            $.data(this.container, "select2-last-term", term);

            // if the search is currently hidden we do not alter the results
            if (initial !== true && (this.showSearchInput === false || !this.opened())) {
                return;
            }

            function postRender() {
                search.removeClass("select2-active");
                self.positionDropdown();
            }

            function render(html) {
                results.html(html);
                postRender();
            }

            queryNumber = ++this.queryCount;

            var maxSelSize = this.getMaximumSelectionSize();
            if (maxSelSize >=1) {
                data = this.data();
                if ($.isArray(data) && data.length >= maxSelSize && checkFormatter(opts.alertTooBig, "formatSelectionTooBig")) {
                    render("<li class='select2-selection-limit'>" + opts.formatSelectionTooBig(maxSelSize) + "</li>");
                    return;
                }
            }

            if (search.val().length < opts.minimumInputLength) {
                if (checkFormatter(opts.formatInputTooShort, "formatInputTooShort")) {
                    render("<li class='select2-no-results'>" + opts.formatInputTooShort(search.val(), opts.minimumInputLength) + "</li>");
                } else {
                    render("");
                }
                if (initial && this.showSearch) this.showSearch(true);
                return;
            }

            if (opts.maximumInputLength && search.val().length > opts.maximumInputLength) {
                if (checkFormatter(opts.formatInputTooLong, "formatInputTooLong")) {
                    render("<li class='select2-no-results'>" + opts.formatInputTooLong(search.val(), opts.maximumInputLength) + "</li>");
                } else {
                    render("");
                }
                return;
            }

            if (opts.formatSearching && this.findHighlightableChoices().length === 0) {
                render("<li class='select2-searching'>" + opts.formatSearching() + "</li>");
            }

            search.addClass("select2-active");

            this.removeHighlight();

            // give the tokenizer a chance to pre-process the input
            input = this.tokenize();
            if (input != undefined && input != null) {
                search.val(input);
            }

            this.resultsPage = 1;

            opts.query({
                element: opts.element,
                    term: search.val(),
                    page: this.resultsPage,
                    context: null,
                    matcher: opts.matcher,
                    callback: this.bind(function (data) {
                var def; // default choice

                // ignore old responses
                if (queryNumber != this.queryCount) {
                  return;
                }

                // ignore a response if the select2 has been closed before it was received
                if (!this.opened()) {
                    this.search.removeClass("select2-active");
                    return;
                }

                // save context, if any
                this.context = (data.context===undefined) ? null : data.context;
                // create a default choice and prepend it to the list                
                if (this.opts.createSearchChoice && search.val() !== "") {
                    def = this.opts.createSearchChoice.call(self, search.val(), data.results);
                    //alert(def['text']);
                    if (def !== undefined && def !== null && self.id(def) !== undefined && self.id(def) !== null) {
                        if ($(data.results).filter(
                            function () {
                                return equal(self.id(this), self.id(def));
                            }).length === 0) {
                            data.results.unshift(def);
                        }
                    }
                }

                if (data.results.length === 0 && checkFormatter(opts.formatNoMatches, "formatNoMatches")) {
                    render("<li class='select2-no-results'>" + opts.formatNoMatches(search.val()) + "</li>");
                    return;
                }

                results.empty();
                self.opts.populateResults.call(this, results, data.results, {term: search.val(), page: this.resultsPage, context:null});

                if (data.more === true && checkFormatter(opts.formatLoadMore, "formatLoadMore")) {
                    results.append("<li class='select2-more-results'>" + self.opts.escapeMarkup(opts.formatLoadMore(this.resultsPage)) + "</li>");
                    window.setTimeout(function() { self.loadMoreIfNeeded(); }, 10);
                }

                this.postprocessResults(data, initial);

                postRender();

                this.opts.element.trigger({ type: "select2-loaded", items: data });
            })});
        },

        // abstract
        cancel: function () {
            this.close();
        },

        // abstract
        blur: function () {
            // if selectOnBlur == true, select the currently highlighted option
            if (this.opts.selectOnBlur)
                this.selectHighlighted({noFocus: true});

            this.close();
            this.container.removeClass("select2-container-active");
            // synonymous to .is(':focus'), which is available in jquery >= 1.6
            if (this.search[0] === document.activeElement) { this.search.blur(); }
            this.clearSearch();
            this.selection.find(".select2-search-choice-focus").removeClass("select2-search-choice-focus");
        },

        // abstract
        focusSearch: function () {
            focus(this.search);
        },

        // abstract
        selectHighlighted: function (options) {
            var index=this.highlight(),
                highlighted=this.results.find(".select2-highlighted"),
                data = highlighted.closest('.select2-result').data("select2-data");

            if (data) {
                this.highlight(index);
                this.onSelect(data, options);
            } else if (options && options.noFocus) {
                this.close();
            }
        },

        // abstract
        getPlaceholder: function () {
            var placeholderOption;
            return this.opts.element.attr("placeholder") ||
                this.opts.element.attr("data-placeholder") || // jquery 1.4 compat
                this.opts.element.data("placeholder") ||
                this.opts.placeholder ||
                ((placeholderOption = this.getPlaceholderOption()) !== undefined ? placeholderOption.text() : undefined);
        },

        // abstract
        getPlaceholderOption: function() {
            if (this.select) {
                var firstOption = this.select.children().first();
                if (this.opts.placeholderOption !== undefined ) {
                    //Determine the placeholder option based on the specified placeholderOption setting
                    return (this.opts.placeholderOption === "first" && firstOption) ||
                           (typeof this.opts.placeholderOption === "function" && this.opts.placeholderOption(this.select));
                } else if (firstOption.text() === "" && firstOption.val() === "") {
                    //No explicit placeholder option specified, use the first if it's blank
                    return firstOption;
                }
            }
        },

        /**
         * Get the desired width for the container element.  This is
         * derived first from option `width` passed to select2, then
         * the inline 'style' on the original element, and finally
         * falls back to the jQuery calculated element width.
         */
        // abstract
        initContainerWidth: function () {
            function resolveContainerWidth() {
                var style, attrs, matches, i, l;

                if (this.opts.width === "off") {
                    return null;
                } else if (this.opts.width === "element"){
                    return this.opts.element.outerWidth(false) === 0 ? 'auto' : this.opts.element.outerWidth(false) + 'px';
                } else if (this.opts.width === "copy" || this.opts.width === "resolve") {
                    // check if there is inline style on the element that contains width
                    style = this.opts.element.attr('style');
                    if (style !== undefined) {
                        attrs = style.split(';');
                        for (i = 0, l = attrs.length; i < l; i = i + 1) {
                            matches = attrs[i].replace(/\s/g, '')
                                .match(/[^-]width:(([-+]?([0-9]*\.)?[0-9]+)(px|em|ex|%|in|cm|mm|pt|pc))/i);
                            if (matches !== null && matches.length >= 1)
                                return matches[1];
                        }
                    }

                    if (this.opts.width === "resolve") {
                        // next check if css('width') can resolve a width that is percent based, this is sometimes possible
                        // when attached to input type=hidden or elements hidden via css
                        style = this.opts.element.css('width');
                        if (style.indexOf("%") > 0) return style;

                        // finally, fallback on the calculated width of the element
                        return (this.opts.element.outerWidth(false) === 0 ? 'auto' : this.opts.element.outerWidth(false) + 'px');
                    }

                    return null;
                } else if ($.isFunction(this.opts.width)) {
                    return this.opts.width();
                } else {
                    return this.opts.width;
               }
            };

            var width = resolveContainerWidth.call(this);
            if (width !== null) {
                this.container.css("width", width);
            }
        }
    });

    SingleSelect2 = clazz(AbstractSelect2, {

        // single

        createContainer: function () {
            var container = $(document.createElement("div")).attr({
                "class": "select2-container"
            }).html([
                "<a href='javascript:void(0)' onclick='return false;' class='select2-choice' tabindex='-1'>",
                "   <span class='select2-chosen'>&nbsp;</span><abbr class='select2-search-choice-close'></abbr>",
                "   <span class='select2-arrow'><b></b></span>",
                "</a>",
                "<input class='select2-focusser select2-offscreen' type='text'/>",             
                "<div class='select2-drop select2-display-none'>",
                "   <div class='select2-search'>",
                "       <input type='text' autocomplete='off' autocorrect='off' autocapitalize='off' spellcheck='false' class='select2-input'/>",
                "   </div>",
                "   <ul class='select2-results'>",
                "   </ul>",
                "</div>"].join(""));
            return container;
        },

        // single
        enableInterface: function() {
            if (this.parent.enableInterface.apply(this, arguments)) {
                this.focusser.prop("disabled", !this.isInterfaceEnabled());
            }
        },

        // single
        opening: function () {
            var el, range, len;

            if (this.opts.minimumResultsForSearch >= 0) {
                this.showSearch(true);
            }

            this.parent.opening.apply(this, arguments);

            if (this.showSearchInput !== false) {
                // IE appends focusser.val() at the end of field :/ so we manually insert it at the beginning using a range
                // all other browsers handle this just fine

                this.search.val(this.focusser.val());
            }
            this.search.focus();
            // move the cursor to the end after focussing, otherwise it will be at the beginning and
            // new text will appear *before* focusser.val()
            el = this.search.get(0);
            if (el.createTextRange) {
                range = el.createTextRange();
                range.collapse(false);
                range.select();
            } else if (el.setSelectionRange) {
                len = this.search.val().length;
                el.setSelectionRange(len, len);
            }

            // initializes search's value with nextSearchTerm (if defined by user)
            // ignore nextSearchTerm if the dropdown is opened by the user pressing a letter
            if(this.search.val() === "") {
                if(this.nextSearchTerm != undefined){
                    this.search.val(this.nextSearchTerm);
                    this.search.select();
                }
            }

            this.focusser.prop("disabled", true).val("");
            this.updateResults(true);
            this.opts.element.trigger($.Event("select2-open"));
        },

        // single
        close: function (params) {
            if (!this.opened()) return;
            this.parent.close.apply(this, arguments);

            params = params || {focus: true};
            this.focusser.removeAttr("disabled");

            if (params.focus) {
                this.focusser.focus();
            }
        },

        // single
        focus: function () {
            if (this.opened()) {
                this.close();
            } else {
                this.focusser.removeAttr("disabled");
                this.focusser.focus();
            }
        },

        // single
        isFocused: function () {
            return this.container.hasClass("select2-container-active");
        },

        // single
        cancel: function () {
            this.parent.cancel.apply(this, arguments);
            this.focusser.removeAttr("disabled");
            this.focusser.focus();
        },

        // single
        destroy: function() {
            $("label[for='" + this.focusser.attr('id') + "']")
                .attr('for', this.opts.element.attr("id"));
            this.parent.destroy.apply(this, arguments);
        },

        // single
        initContainer: function () {

            var selection,
                container = this.container,
                dropdown = this.dropdown;

            if (this.opts.minimumResultsForSearch < 0) {
                this.showSearch(false);
            } else {
                this.showSearch(true);
            }

            this.selection = selection = container.find(".select2-choice");

            this.focusser = container.find(".select2-focusser");

            // rewrite labels from original element to focusser
            this.focusser.attr("id", "s2id_autogen"+nextUid());

            $("label[for='" + this.opts.element.attr("id") + "']")
                .attr('for', this.focusser.attr('id'));

            this.focusser.attr("tabindex", this.elementTabIndex);

            this.search.on("keydown", this.bind(function (e) {
                if (!this.isInterfaceEnabled()) return;

                if (e.which === KEY.PAGE_UP || e.which === KEY.PAGE_DOWN) {
                    // prevent the page from scrolling
                    killEvent(e);
                    return;
                }

                switch (e.which) {
                    case KEY.UP:
                    case KEY.DOWN:
                        this.moveHighlight((e.which === KEY.UP) ? -1 : 1);
                        killEvent(e);
                        return;
                    case KEY.ENTER:
                        this.selectHighlighted();
                        killEvent(e);
                        return;
                    case KEY.TAB:
                        // if selectOnBlur == true, select the currently highlighted option
                        if (this.opts.selectOnBlur) {
                            this.selectHighlighted({noFocus: true});
                        }
                        return;
                    case KEY.ESC:
                        this.cancel(e);
                        killEvent(e);
                        return;
                }
            }));

            this.search.on("blur", this.bind(function(e) {
                // a workaround for chrome to keep the search field focussed when the scroll bar is used to scroll the dropdown.
                // without this the search field loses focus which is annoying
                if (document.activeElement === this.body().get(0)) {
                    window.setTimeout(this.bind(function() {
                        this.search.focus();
                    }), 0);
                }
            }));

            this.focusser.on("keydown", this.bind(function (e) {
                if (!this.isInterfaceEnabled()) return;

                if (e.which === KEY.TAB || KEY.isControl(e) || KEY.isFunctionKey(e) || e.which === KEY.ESC) {
                    return;
                }

                if (this.opts.openOnEnter === false && e.which === KEY.ENTER) {
                    killEvent(e);
                    return;
                }

                if (e.which == KEY.DOWN || e.which == KEY.UP
                    || (e.which == KEY.ENTER && this.opts.openOnEnter)) {

                    if (e.altKey || e.ctrlKey || e.shiftKey || e.metaKey) return;

                    this.open();
                    killEvent(e);
                    return;
                }

                if (e.which == KEY.DELETE || e.which == KEY.BACKSPACE) {
                    if (this.opts.allowClear) {
                        this.clear();
                    }
                    killEvent(e);
                    return;
                }
            }));


            installKeyUpChangeEvent(this.focusser);
            this.focusser.on("keyup-change input", this.bind(function(e) {
                if (this.opts.minimumResultsForSearch >= 0) {
                    e.stopPropagation();
                    if (this.opened()) return;
                    this.open();
                }
            }));

            selection.on("mousedown", "abbr", this.bind(function (e) {
                if (!this.isInterfaceEnabled()) return;
                this.clear();
                killEventImmediately(e);
                this.close();
                this.selection.focus();
            }));

            selection.on("mousedown", this.bind(function (e) {

                if (!this.container.hasClass("select2-container-active")) {
                    this.opts.element.trigger($.Event("select2-focus"));
                }

                if (this.opened()) {
                    this.close();
                } else if (this.isInterfaceEnabled()) {
                    this.open();
                }

                killEvent(e);
            }));

            dropdown.on("mousedown", this.bind(function() { this.search.focus(); }));

            selection.on("focus", this.bind(function(e) {
                killEvent(e);
            }));

            this.focusser.on("focus", this.bind(function(){
                if (!this.container.hasClass("select2-container-active")) {
                    this.opts.element.trigger($.Event("select2-focus"));
                }
                this.container.addClass("select2-container-active");
            })).on("blur", this.bind(function() {
                if (!this.opened()) {
                    this.container.removeClass("select2-container-active");
                    this.opts.element.trigger($.Event("select2-blur"));
                }
            }));
            this.search.on("focus", this.bind(function(){
                if (!this.container.hasClass("select2-container-active")) {
                    this.opts.element.trigger($.Event("select2-focus"));
                }
                this.container.addClass("select2-container-active");
            }));

            this.initContainerWidth();
            this.opts.element.addClass("select2-offscreen");
            this.setPlaceholder();

        },

        // single
        clear: function(triggerChange) {
            var data=this.selection.data("select2-data");
            if (data) { // guard against queued quick consecutive clicks
                var placeholderOption = this.getPlaceholderOption();
                this.opts.element.val(placeholderOption ? placeholderOption.val() : "");
                this.selection.find(".select2-chosen").empty();
                this.selection.removeData("select2-data");
                this.setPlaceholder();

                if (triggerChange !== false){
                    this.opts.element.trigger({ type: "select2-removed", val: this.id(data), choice: data });
                    this.triggerChange({removed:data});
                }
            }
        },

        /**
         * Sets selection based on source element's value
         */
        // single
        initSelection: function () {
            var selected;
            if (this.isPlaceholderOptionSelected()) {
                this.updateSelection(null);
                this.close();
                this.setPlaceholder();
            } else {
                var self = this;
                this.opts.initSelection.call(null, this.opts.element, function(selected){
                    if (selected !== undefined && selected !== null) {
                        self.updateSelection(selected);
                        self.close();
                        self.setPlaceholder();
                    }
                });
            }
        },

        isPlaceholderOptionSelected: function() {
            var placeholderOption;
            if (!this.opts.placeholder) return false; // no placeholder specified so no option should be considered
            return ((placeholderOption = this.getPlaceholderOption()) !== undefined && placeholderOption.is(':selected'))
                || (this.opts.element.val() === "")
                || (this.opts.element.val() === undefined)
                || (this.opts.element.val() === null);
        },

        // single
        prepareOpts: function () {
            var opts = this.parent.prepareOpts.apply(this, arguments),
                self=this;

            if (opts.element.get(0).tagName.toLowerCase() === "select") {
                // install the selection initializer
                opts.initSelection = function (element, callback) {
                    var selected = element.find(":selected");
                    // a single select box always has a value, no need to null check 'selected'
                    callback(self.optionToData(selected));
                };
            } else if ("data" in opts) {
                // install default initSelection when applied to hidden input and data is local
                opts.initSelection = opts.initSelection || function (element, callback) {
                    var id = element.val();
                    //search in data by id, storing the actual matching item
                    var match = null;
                    opts.query({
                        matcher: function(term, text, el){
                            var is_match = equal(id, opts.id(el));
                            if (is_match) {
                                match = el;
                            }
                            return is_match;
                        },
                        callback: !$.isFunction(callback) ? $.noop : function() {
                            callback(match);
                        }
                    });
                };
            }

            return opts;
        },

        // single
        getPlaceholder: function() {
            // if a placeholder is specified on a single select without a valid placeholder option ignore it
            if (this.select) {
                if (this.getPlaceholderOption() === undefined) {
                    return undefined;
                }
            }

            return this.parent.getPlaceholder.apply(this, arguments);
        },

        // single
        setPlaceholder: function () {
            var placeholder = this.getPlaceholder();

            if (this.isPlaceholderOptionSelected() && placeholder !== undefined) {

                // check for a placeholder option if attached to a select
                if (this.select && this.getPlaceholderOption() === undefined) return;

                this.selection.find(".select2-chosen").html(this.opts.escapeMarkup(placeholder));

                this.selection.addClass("select2-default");

                this.container.removeClass("select2-allowclear");
            }
        },

        // single
        postprocessResults: function (data, initial, noHighlightUpdate) {
            var selected = 0, self = this, showSearchInput = true;

            // find the selected element in the result list

            this.findHighlightableChoices().each2(function (i, elm) {
                if (equal(self.id(elm.data("select2-data")), self.opts.element.val())) {
                    selected = i;
                    return false;
                }
            });

            // and highlight it
            if (noHighlightUpdate !== false) {
                if (initial === true && selected >= 0) {
                    this.highlight(selected);
                } else {
                    this.highlight(0);
                }
            }

            // hide the search box if this is the first we got the results and there are enough of them for search

            if (initial === true) {
                var min = this.opts.minimumResultsForSearch;
                if (min >= 0) {
                    this.showSearch(countResults(data.results) >= min);
                }
            }
        },

        // single
        showSearch: function(showSearchInput) {
            if (this.showSearchInput === showSearchInput) return;

            this.showSearchInput = showSearchInput;

            this.dropdown.find(".select2-search").toggleClass("select2-search-hidden", !showSearchInput);
            this.dropdown.find(".select2-search").toggleClass("select2-offscreen", !showSearchInput);
            //add "select2-with-searchbox" to the container if search box is shown
            $(this.dropdown, this.container).toggleClass("select2-with-searchbox", showSearchInput);
        },

        // single
        onSelect: function (data, options) {

            if (!this.triggerSelect(data)) { return; }

            var old = this.opts.element.val(),
                oldData = this.data();

            this.opts.element.val(this.id(data));
            this.updateSelection(data);

            this.opts.element.trigger({ type: "select2-selected", val: this.id(data), choice: data });

            this.nextSearchTerm = this.opts.nextSearchTerm(data, this.search.val());
            this.close();

            if (!options || !options.noFocus)
                this.selection.focus();

            if (!equal(old, this.id(data))) { this.triggerChange({added:data,removed:oldData}); }
        },

        // single
        updateSelection: function (data) {

            var container=this.selection.find(".select2-chosen"), formatted, cssClass;

            this.selection.data("select2-data", data);

            container.empty();
            if (data !== null) {
                formatted=this.opts.formatSelection(data, container, this.opts.escapeMarkup);
            }
            if (formatted !== undefined) {
                container.append(formatted);
            }
            cssClass=this.opts.formatSelectionCssClass(data, container);
            if (cssClass !== undefined) {
                container.addClass(cssClass);
            }

            this.selection.removeClass("select2-default");

            if (this.opts.allowClear && this.getPlaceholder() !== undefined) {
                this.container.addClass("select2-allowclear");
            }
        },

        // single
        val: function () {
            var val,
                triggerChange = false,
                data = null,
                self = this,
                oldData = this.data();

            if (arguments.length === 0) {
                return this.opts.element.val();
            }

            val = arguments[0];
            
            if (arguments.length > 1) {
                triggerChange = arguments[1];
            }

            if (this.select) {
                this.select
                    .val(val)
                    .find(":selected").each2(function (i, elm) {
                        data = self.optionToData(elm);
                        return false;
                    });
                this.updateSelection(data);
                this.setPlaceholder();
                if (triggerChange) {
                    this.triggerChange({added: data, removed:oldData});
                }
            } else {
                // val is an id. !val is true for [undefined,null,'',0] - 0 is legal
                if (!val && val !== 0) {                   
                    this.clear(triggerChange);
                    return;
                }
                if (this.opts.initSelection === undefined) {
                    throw new Error("cannot call val() if initSelection() is not defined");
                }
                this.opts.element.val(val);
                this.opts.initSelection(this.opts.element, function(data){
                    self.opts.element.val(!data ? "" : self.id(data));
                    self.updateSelection(data);
                    self.setPlaceholder();
                    if (triggerChange) {
                        self.triggerChange({added: data, removed:oldData});
                    }
                });
            }
        },

        // single
        clearSearch: function () {
            this.search.val("");
            this.focusser.val("");
        },

        // single
        data: function(value) {           
            var data,
                triggerChange = false;

            if (arguments.length === 0) {
                data = this.selection.data("select2-data");
                if (data == undefined) data = null;
                return data;
            } else {
                if (arguments.length > 1) {
                    triggerChange = arguments[1];
                }
                if (!value) {
                    this.clear(triggerChange);
                } else {
                    data = this.data();
                    this.opts.element.val(!value ? "" : this.id(value));
                    this.updateSelection(value);
                    if (triggerChange) {
                        this.triggerChange({added: value, removed:data});
                    }
                }
            }
        }
    });

    MultiSelect2 = clazz(AbstractSelect2, {

        // multi
        createContainer: function () {
            var container = $(document.createElement("div")).attr({
                "class": "select2-container select2-container-multi"
            }).html([
                "<ul class='select2-choices'>",
                "  <li class='select2-search-field'>",
                "    <input type='text' autocomplete='off' autocorrect='off' autocapitalize='off' spellcheck='false' class='select2-input'>",
                "  </li>",
                "</ul>",
                "<div class='select2-drop select2-drop-multi select2-display-none'>",
                "   <ul class='select2-results'>",
                "   </ul>",
                "</div>"].join(""));
            return container;
        },

        // multi
        prepareOpts: function () {
            var opts = this.parent.prepareOpts.apply(this, arguments),
                self=this;

            // TODO validate placeholder is a string if specified

            if (opts.element.get(0).tagName.toLowerCase() === "select") {
                // install sthe selection initializer
                opts.initSelection = function (element, callback) {

                    var data = [];

                    element.find(":selected").each2(function (i, elm) {
                        data.push(self.optionToData(elm));
                    });
                    callback(data);
                };
            } else if ("data" in opts) {
                // install default initSelection when applied to hidden input and data is local
                opts.initSelection = opts.initSelection || function (element, callback) {
                    var ids = splitVal(element.val(), opts.separator);
                    //search in data by array of ids, storing matching items in a list
                    var matches = [];
                    opts.query({
                        matcher: function(term, text, el){
                            var is_match = $.grep(ids, function(id) {
                                return equal(id, opts.id(el));
                            }).length;
                            if (is_match) {
                                matches.push(el);
                            }
                            return is_match;
                        },
                        callback: !$.isFunction(callback) ? $.noop : function() {
                            // reorder matches based on the order they appear in the ids array because right now
                            // they are in the order in which they appear in data array
                            var ordered = [];
                            for (var i = 0; i < ids.length; i++) {
                                var id = ids[i];
                                for (var j = 0; j < matches.length; j++) {
                                    var match = matches[j];
                                    if (equal(id, opts.id(match))) {
                                        ordered.push(match);
                                        matches.splice(j, 1);
                                        break;
                                    }
                                }
                            }
                            callback(ordered);
                        }
                    });
                };
            }

            return opts;
        },

        selectChoice: function (choice) {

            var selected = this.container.find(".select2-search-choice-focus");
            if (selected.length && choice && choice[0] == selected[0]) {

            } else {
                if (selected.length) {
                    this.opts.element.trigger("choice-deselected", selected);
                }
                selected.removeClass("select2-search-choice-focus");
                if (choice && choice.length) {
                    this.close();
                    choice.addClass("select2-search-choice-focus");
                    this.opts.element.trigger("choice-selected", choice);
                }
            }
        },

        // multi
        destroy: function() {
            $("label[for='" + this.search.attr('id') + "']")
                .attr('for', this.opts.element.attr("id"));
            this.parent.destroy.apply(this, arguments);
        },

        // multi
        initContainer: function () {

            var selector = ".select2-choices", selection;

            this.searchContainer = this.container.find(".select2-search-field");
            this.selection = selection = this.container.find(selector);

            var _this = this;
            this.selection.on("click", ".select2-search-choice", function (e) {
                //killEvent(e);
                _this.search[0].focus();
                _this.selectChoice($(this));
            });

            // rewrite labels from original element to focusser
            this.search.attr("id", "s2id_autogen"+nextUid());
            $("label[for='" + this.opts.element.attr("id") + "']")
                .attr('for', this.search.attr('id'));

            this.search.on("input paste", this.bind(function() {
                if (!this.isInterfaceEnabled()) return;
                if (!this.opened()) {
                    this.open();
                }
            }));

            this.search.attr("tabindex", this.elementTabIndex);

            this.keydowns = 0;
            this.search.on("keydown", this.bind(function (e) {
                if (!this.isInterfaceEnabled()) return;

                ++this.keydowns;
                var selected = selection.find(".select2-search-choice-focus");
                var prev = selected.prev(".select2-search-choice:not(.select2-locked)");
                var next = selected.next(".select2-search-choice:not(.select2-locked)");
                var pos = getCursorInfo(this.search);

                if (selected.length &&
                    (e.which == KEY.LEFT || e.which == KEY.RIGHT || e.which == KEY.BACKSPACE || e.which == KEY.DELETE || e.which == KEY.ENTER)) {
                    var selectedChoice = selected;
                    if (e.which == KEY.LEFT && prev.length) {
                        selectedChoice = prev;
                    }
                    else if (e.which == KEY.RIGHT) {
                        selectedChoice = next.length ? next : null;
                    }
                    else if (e.which === KEY.BACKSPACE) {
                        this.unselect(selected.first());
                        this.search.width(10);
                        selectedChoice = prev.length ? prev : next;
                    } else if (e.which == KEY.DELETE) {
                        this.unselect(selected.first());
                        this.search.width(10);
                        selectedChoice = next.length ? next : null;
                    } else if (e.which == KEY.ENTER) {
                        selectedChoice = null;
                    }

                    this.selectChoice(selectedChoice);
                    killEvent(e);
                    if (!selectedChoice || !selectedChoice.length) {
                        this.open();
                    }
                    return;
                } else if (((e.which === KEY.BACKSPACE && this.keydowns == 1)
                    || e.which == KEY.LEFT) && (pos.offset == 0 && !pos.length)) {

                    this.selectChoice(selection.find(".select2-search-choice:not(.select2-locked)").last());
                    killEvent(e);
                    return;
                } else {
                    this.selectChoice(null);
                }

                if (this.opened()) {
                    switch (e.which) {
                    case KEY.UP:
                    case KEY.DOWN:
                        this.moveHighlight((e.which === KEY.UP) ? -1 : 1);
                        killEvent(e);
                        return;
                    case KEY.ENTER:
                        this.selectHighlighted();
                        killEvent(e);
                        return;
                    case KEY.TAB:
                        // if selectOnBlur == true, select the currently highlighted option
                        if (this.opts.selectOnBlur) {
                            this.selectHighlighted({noFocus:true});
                        }
                        this.close();
                        return;
                    case KEY.ESC:
                        this.cancel(e);
                        killEvent(e);
                        return;
                    }
                }

                if (e.which === KEY.TAB || KEY.isControl(e) || KEY.isFunctionKey(e)
                 || e.which === KEY.BACKSPACE || e.which === KEY.ESC) {
                    return;
                }

                if (e.which === KEY.ENTER) {
                    if (this.opts.openOnEnter === false) {
                        return;
                    } else if (e.altKey || e.ctrlKey || e.shiftKey || e.metaKey) {
                        return;
                    }
                }

                this.open();

                if (e.which === KEY.PAGE_UP || e.which === KEY.PAGE_DOWN) {
                    // prevent the page from scrolling
                    killEvent(e);
                }

                if (e.which === KEY.ENTER) {
                    // prevent form from being submitted
                    killEvent(e);
                }

            }));

            this.search.on("keyup", this.bind(function (e) {
                this.keydowns = 0;
                this.resizeSearch();
            })
            );

            this.search.on("blur", this.bind(function(e) {
                this.container.removeClass("select2-container-active");
                this.search.removeClass("select2-focused");
                this.selectChoice(null);
                if (!this.opened()) this.clearSearch();
                e.stopImmediatePropagation();
                this.opts.element.trigger($.Event("select2-blur"));
            }));

            this.container.on("click", selector, this.bind(function (e) {
                if (!this.isInterfaceEnabled()) return;
                if ($(e.target).closest(".select2-search-choice").length > 0) {
                    // clicked inside a select2 search choice, do not open
                    return;
                }
                this.selectChoice(null);
                this.clearPlaceholder();
                if (!this.container.hasClass("select2-container-active")) {
                    this.opts.element.trigger($.Event("select2-focus"));
                }
                this.open();
                this.focusSearch();
                e.preventDefault();
            }));

            this.container.on("focus", selector, this.bind(function () {
                if (!this.isInterfaceEnabled()) return;
                if (!this.container.hasClass("select2-container-active")) {
                    this.opts.element.trigger($.Event("select2-focus"));
                }
                this.container.addClass("select2-container-active");
                this.dropdown.addClass("select2-drop-active");
                this.clearPlaceholder();
            }));

            this.initContainerWidth();
            this.opts.element.addClass("select2-offscreen");

            // set the placeholder if necessary
            this.clearSearch();
        },

        // multi
        enableInterface: function() {
            if (this.parent.enableInterface.apply(this, arguments)) {
                this.search.prop("disabled", !this.isInterfaceEnabled());
            }
        },

        // multi
        initSelection: function () {
            var data;
            if (this.opts.element.val() === "" && this.opts.element.text() === "") {
                this.updateSelection([]);
                this.close();
                // set the placeholder if necessary
                this.clearSearch();
            }
            if (this.select || this.opts.element.val() !== "") {
                var self = this;
                this.opts.initSelection.call(null, this.opts.element, function(data){
                    if (data !== undefined && data !== null) {
                        self.updateSelection(data);
                        self.close();
                        // set the placeholder if necessary
                        self.clearSearch();
                    }
                });
            }
        },

        // multi
        clearSearch: function () {            
            var placeholder = this.getPlaceholder(),
                maxWidth = this.getMaxSearchWidth();

            if (placeholder !== undefined  && this.getVal().length === 0 && this.search.hasClass("select2-focused") === false) {
                this.search.val(placeholder).addClass("select2-default");
                // stretch the search box to full width of the container so as much of the placeholder is visible as possible
                // we could call this.resizeSearch(), but we do not because that requires a sizer and we do not want to create one so early because of a firefox bug, see #944
                this.search.width(maxWidth > 0 ? maxWidth : this.container.css("width"));
            } else {
                this.search.val("").width(10);
            }
        },

        // multi
        clearPlaceholder: function () {
            if (this.search.hasClass("select2-default")) {
                this.search.val("").removeClass("select2-default");
            }
        },

        // multi
        opening: function () {
            this.clearPlaceholder(); // should be done before super so placeholder is not used to search
            this.resizeSearch();

            this.parent.opening.apply(this, arguments);

            this.focusSearch();

            this.updateResults(true);
            this.search.focus();
            this.opts.element.trigger($.Event("select2-open"));
        },

        // multi
        close: function () {
            if (!this.opened()) return;
            this.parent.close.apply(this, arguments);
        },

        // multi
        focus: function () {
            this.close();
            this.search.focus();
        },

        // multi
        isFocused: function () {
            return this.search.hasClass("select2-focused");
        },

        // multi
        updateSelection: function (data) {
            var ids = [], filtered = [], self = this;

            // filter out duplicates
            $(data).each(function () {
                if (indexOf(self.id(this), ids) < 0) {
                    ids.push(self.id(this));
                    filtered.push(this);
                }
            });
            data = filtered;

            this.selection.find(".select2-search-choice").remove();
            $(data).each(function () {
                self.addSelectedChoice(this);
            });
            self.postprocessResults();
        },

        // multi
        tokenize: function() {
            var input = this.search.val();
            input = this.opts.tokenizer.call(this, input, this.data(), this.bind(this.onSelect), this.opts);
            if (input != null && input != undefined) {
                this.search.val(input);
                if (input.length > 0) {
                    this.open();
                }
            }

        },

        // multi
        onSelect: function (data, options) {

            if (!this.triggerSelect(data)) { return; }

            this.addSelectedChoice(data);

            this.opts.element.trigger({ type: "selected", val: this.id(data), choice: data });

            if (this.select || !this.opts.closeOnSelect) this.postprocessResults(data, false, this.opts.closeOnSelect===true);

            if (this.opts.closeOnSelect) {
                this.close();
                this.search.width(10);
            } else {
                if (this.countSelectableResults()>0) {
                    this.search.width(10);
                    this.resizeSearch();
                    if (this.getMaximumSelectionSize() > 0 && this.val().length >= this.getMaximumSelectionSize()) {
                        // if we reached max selection size repaint the results so choices
                        // are replaced with the max selection reached message
                        this.updateResults(true);
                    }
                    this.positionDropdown();
                } else {
                    // if nothing left to select close
                    this.close();
                    this.search.width(10);
                }
            }

            // since its not possible to select an element that has already been
            // added we do not need to check if this is a new element before firing change
            this.triggerChange({ added: data });

            if (!options || !options.noFocus)
                this.focusSearch();
        },

        // multi
        cancel: function () {
            this.close();
            this.focusSearch();
        },

        addSelectedChoice: function (data) {
            var enableChoice = !data.locked,
                enabledItem = $(
                    "<li class='select2-search-choice'>" +
                    "    <div></div>" +
                    "    <a href='#' onclick='return false;' class='select2-search-choice-close' tabindex='-1'></a>" +
                    "</li>"),
                disabledItem = $(
                    "<li class='select2-search-choice select2-locked'>" +
                    "<div></div>" +
                    "</li>");
            var choice = enableChoice ? enabledItem : disabledItem,
                id = this.id(data),
                val = this.getVal(),
                formatted,
                cssClass;

            formatted=this.opts.formatSelection(data, choice.find("div"), this.opts.escapeMarkup);
            if (formatted != undefined) {
                choice.find("div").replaceWith("<div>"+formatted+"</div>");
            }
            cssClass=this.opts.formatSelectionCssClass(data, choice.find("div"));
            if (cssClass != undefined) {
                choice.addClass(cssClass);
            }

            if(enableChoice){
              choice.find(".select2-search-choice-close")
                  .on("mousedown", killEvent)
                  .on("click dblclick", this.bind(function (e) {
                  if (!this.isInterfaceEnabled()) return;

                  $(e.target).closest(".select2-search-choice").fadeOut('fast', this.bind(function(){
                      this.unselect($(e.target));
                      this.selection.find(".select2-search-choice-focus").removeClass("select2-search-choice-focus");
                      this.close();
                      this.focusSearch();
                  })).dequeue();
                  killEvent(e);
              })).on("focus", this.bind(function () {
                  if (!this.isInterfaceEnabled()) return;
                  this.container.addClass("select2-container-active");
                  this.dropdown.addClass("select2-drop-active");
              }));
            }

            choice.data("select2-data", data);
            choice.insertBefore(this.searchContainer);

            val.push(id);
            this.setVal(val);
        },

        // multi
        unselect: function (selected) {
            var val = this.getVal(),
                data,
                index;

            selected = selected.closest(".select2-search-choice");

            if (selected.length === 0) {
                throw "Invalid argument: " + selected + ". Must be .select2-search-choice";
            }

            data = selected.data("select2-data");

            if (!data) {
                // prevent a race condition when the 'x' is clicked really fast repeatedly the event can be queued
                // and invoked on an element already removed
                return;
            }

            index = indexOf(this.id(data), val);

            if (index >= 0) {
                val.splice(index, 1);
                this.setVal(val);
                if (this.select) this.postprocessResults();
            }
            selected.remove();

            this.opts.element.trigger({ type: "removed", val: this.id(data), choice: data });
            this.triggerChange({ removed: data });
        },

        // multi
        postprocessResults: function (data, initial, noHighlightUpdate) {
            var val = this.getVal(),
                choices = this.results.find(".select2-result"),
                compound = this.results.find(".select2-result-with-children"),
                self = this;

            choices.each2(function (i, choice) {
                var id = self.id(choice.data("select2-data"));
                if (indexOf(id, val) >= 0) {
                    choice.addClass("select2-selected");
                    // mark all children of the selected parent as selected
                    choice.find(".select2-result-selectable").addClass("select2-selected");
                }
            });

            compound.each2(function(i, choice) {
                // hide an optgroup if it doesnt have any selectable children
                if (!choice.is('.select2-result-selectable')
                    && choice.find(".select2-result-selectable:not(.select2-selected)").length === 0) {
                    choice.addClass("select2-selected");
                }
            });

            if (this.highlight() == -1 && noHighlightUpdate !== false){
                self.highlight(0);
            }

            //If all results are chosen render formatNoMAtches
            if(!this.opts.createSearchChoice && !choices.filter('.select2-result:not(.select2-selected)').length > 0){
                if(!data || data && !data.more && this.results.find(".select2-no-results").length === 0) {
                    if (checkFormatter(self.opts.formatNoMatches, "formatNoMatches")) {
                        this.results.append("<li class='select2-no-results'>" + self.opts.formatNoMatches(self.search.val()) + "</li>");
                    }
                }
            }

        },

        // multi
        getMaxSearchWidth: function() {
            return this.selection.width() - getSideBorderPadding(this.search);
        },

        // multi
        resizeSearch: function () {
            var minimumWidth, left, maxWidth, containerLeft, searchWidth,
                sideBorderPadding = getSideBorderPadding(this.search);

            minimumWidth = measureTextWidth(this.search) + 10;

            left = this.search.offset().left;

            maxWidth = this.selection.width();
            containerLeft = this.selection.offset().left;

            searchWidth = maxWidth - (left - containerLeft) - sideBorderPadding;

            if (searchWidth < minimumWidth) {
                searchWidth = maxWidth - sideBorderPadding;
            }

            if (searchWidth < 40) {
                searchWidth = maxWidth - sideBorderPadding;
            }

            if (searchWidth <= 0) {
              searchWidth = minimumWidth;
            }

            this.search.width(searchWidth);
        },

        // multi
        getVal: function () {
            var val;
            if (this.select) {
                val = this.select.val();
                return val === null ? [] : val;
            } else {
                val = this.opts.element.val();
                return splitVal(val, this.opts.separator);
            }
        },

        // multi
        setVal: function (val) {
            var unique;
            if (this.select) {
                this.select.val(val);
            } else {
                unique = [];
                // filter out duplicates
                $(val).each(function () {
                    if (indexOf(this, unique) < 0) unique.push(this);
                });
                this.opts.element.val(unique.length === 0 ? "" : unique.join(this.opts.separator));
            }
        },

        // multi
        buildChangeDetails: function (old, current) {
            var current = current.slice(0),
                old = old.slice(0);

            // remove intersection from each array
            for (var i = 0; i < current.length; i++) {
                for (var j = 0; j < old.length; j++) {
                    if (equal(this.opts.id(current[i]), this.opts.id(old[j]))) {
                        current.splice(i, 1);
                        i--;
                        old.splice(j, 1);
                        j--;
                    }
                }
            }

            return {added: current, removed: old};
        },


        // multi
        val: function (val, triggerChange) {
            var oldData, self=this, changeDetails;

            if (arguments.length === 0) {
                return this.getVal();
            }

            oldData=this.data();
            if (!oldData.length) oldData=[];

            // val is an id. !val is true for [undefined,null,'',0] - 0 is legal
            if (!val && val !== 0) {
                this.opts.element.val("");
                this.updateSelection([]);
                this.clearSearch();
                if (triggerChange) {
                    this.triggerChange({added: this.data(), removed: oldData});
                }
                return;
            }

            // val is a list of ids
            this.setVal(val);

            if (this.select) {
                this.opts.initSelection(this.select, this.bind(this.updateSelection));
                if (triggerChange) {
                    this.triggerChange(this.buildChangeDetails(oldData, this.data()));
                }
            } else {
                if (this.opts.initSelection === undefined) {
                    throw new Error("val() cannot be called if initSelection() is not defined");
                }

                this.opts.initSelection(this.opts.element, function(data){
                    var ids=$.map(data, self.id);
                    self.setVal(ids);
                    self.updateSelection(data);
                    self.clearSearch();
                    if (triggerChange) {
                        self.triggerChange(self.buildChangeDetails(oldData, this.data()));
                    }
                });
            }
            this.clearSearch();
        },

        // multi
        onSortStart: function() {
            if (this.select) {
                throw new Error("Sorting of elements is not supported when attached to <select>. Attach to <input type='hidden'/> instead.");
            }

            // collapse search field into 0 width so its container can be collapsed as well
            this.search.width(0);
            // hide the container
            this.searchContainer.hide();
        },

        // multi
        onSortEnd:function() {

            var val=[], self=this;

            // show search and move it to the end of the list
            this.searchContainer.show();
            // make sure the search container is the last item in the list
            this.searchContainer.appendTo(this.searchContainer.parent());
            // since we collapsed the width in dragStarted, we resize it here
            this.resizeSearch();

            // update selection
            this.selection.find(".select2-search-choice").each(function() {
                val.push(self.opts.id($(this).data("select2-data")));
            });
            this.setVal(val);
            this.triggerChange();
        },

        // multi
        data: function(values, triggerChange) {
            var self=this, ids, old;
            if (arguments.length === 0) {
                 return this.selection
                     .find(".select2-search-choice")
                     .map(function() { return $(this).data("select2-data"); })
                     .get();
            } else {
                old = this.data();
                if (!values) { values = []; }
                ids = $.map(values, function(e) { return self.opts.id(e); });
                this.setVal(ids);
                this.updateSelection(values);
                this.clearSearch();
                if (triggerChange) {
                    this.triggerChange(this.buildChangeDetails(old, this.data()));
                }
            }
        }
    });

    $.fn.select2 = function () {

        var args = Array.prototype.slice.call(arguments, 0),
            opts,
            select2,
            method, value, multiple,
            allowedMethods = ["val", "destroy", "opened", "open", "close", "focus", "isFocused", "container", "dropdown", "onSortStart", "onSortEnd", "enable", "disable", "readonly", "positionDropdown", "data", "search"],
            valueMethods = ["opened", "isFocused", "container", "dropdown"],
            propertyMethods = ["val", "data"],
            methodsMap = { search: "externalSearch" };

        this.each(function () {
            if (args.length === 0 || typeof(args[0]) === "object") {
                opts = args.length === 0 ? {} : $.extend({}, args[0]);
                opts.element = $(this);

                if (opts.element.get(0).tagName.toLowerCase() === "select") {
                    multiple = opts.element.prop("multiple");
                } else {
                    multiple = opts.multiple || false;
                    if ("tags" in opts) {opts.multiple = multiple = true;}
                }

                select2 = multiple ? new MultiSelect2() : new SingleSelect2();
                select2.init(opts);
            } else if (typeof(args[0]) === "string") {

                if (indexOf(args[0], allowedMethods) < 0) {
                    throw "Unknown method: " + args[0];
                }

                value = undefined;
                select2 = $(this).data("select2");
                if (select2 === undefined) return;

                method=args[0];

                if (method === "container") {
                    value = select2.container;
                } else if (method === "dropdown") {
                    value = select2.dropdown;
                } else {
                    if (methodsMap[method]) method = methodsMap[method];

                    value = select2[method].apply(select2, args.slice(1));
                }
                if (indexOf(args[0], valueMethods) >= 0
                    || (indexOf(args[0], propertyMethods) && args.length == 1)) {
                    return false; // abort the iteration, ready to return first matched value
                }
            } else {
                throw "Invalid arguments to select2 plugin: " + args;
            }
        });
        return (value === undefined) ? this : value;
    };

    // plugin defaults, accessible to users
    $.fn.select2.defaults = {
        width: "copy",
        loadMorePadding: 0,
        closeOnSelect: true,
        openOnEnter: true,
        containerCss: {},
        dropdownCss: {},
        containerCssClass: "",
        dropdownCssClass: "",
        formatResult: function(result, container, query, escapeMarkup) {
            var markup=[];
            markMatch(result.text, query.term, markup, escapeMarkup);
            return markup.join("");
        },
        formatSelection: function (data, container, escapeMarkup) {
            return data ? escapeMarkup(data.text) : undefined;
        },
        sortResults: function (results, container, query) {
            return results;
        },
        formatResultCssClass: function(data) {return undefined;},
        formatSelectionCssClass: function(data, container) {return undefined;},
        formatNoMatches: function () { return "No matches found"; },
        formatInputTooShort: function (input, min) { var n = min - input.length; return "Please enter " + n + " more character" + (n == 1? "" : "s"); },
        formatInputTooLong: function (input, max) { var n = input.length - max; return "Please delete " + n + " character" + (n == 1? "" : "s"); },
        formatSelectionTooBig: function (limit) { return "You can only select " + limit + " item" + (limit == 1 ? "" : "s"); },
        formatLoadMore: function (pageNumber) { return "Loading more results..."; },
        formatSearching: function () { return "Searching..."; },
        minimumResultsForSearch: 0,
        minimumInputLength: 0,
        maximumInputLength: null,
        maximumSelectionSize: 0,
        id: function (e) { return e.id; },
        matcher: function(term, text) {
            return stripDiacritics(''+text).toUpperCase().indexOf(stripDiacritics(''+term).toUpperCase()) >= 0;
        },
        separator: ",",
        tokenSeparators: [],
        tokenizer: defaultTokenizer,
        escapeMarkup: defaultEscapeMarkup,
        blurOnChange: false,
        selectOnBlur: false,
        adaptContainerCssClass: function(c) { return c; },
        adaptDropdownCssClass: function(c) { return null; },
        nextSearchTerm: function(selectedObject, currentSearchTerm) { return undefined; }
    };

    $.fn.select2.ajaxDefaults = {
        transport: $.ajax,
        params: {
            type: "GET",
            cache: false,
            dataType: "json"
        }
    };

    // exports
    window.Select2 = {
        query: {
            ajax: ajax,
            local: local,
            tags: tags
        }, util: {
            debounce: debounce,
            markMatch: markMatch,
            escapeMarkup: defaultEscapeMarkup,
            stripDiacritics: stripDiacritics
        }, "class": {
            "abstract": AbstractSelect2,
            "single": SingleSelect2,
            "multi": MultiSelect2
        }
    };

}(jQuery));

/**
* @license Input Mask plugin for jquery
* http://github.com/RobinHerbots/jquery.inputmask
* Copyright (c) 2010 - 2014 Robin Herbots
* Licensed under the MIT license (http://www.opensource.org/licenses/mit-license.php)
* Version: 2.4.17
*/

(function ($) {
    if ($.fn.inputmask === undefined) {
        //helper functions    
        function isInputEventSupported(eventName) {
            var el = document.createElement('input'),
            eventName = 'on' + eventName,
            isSupported = (eventName in el);
            if (!isSupported) {
                el.setAttribute(eventName, 'return;');
                isSupported = typeof el[eventName] == 'function';
            }
            el = null;
            return isSupported;
        }

        function resolveAlias(aliasStr, options, opts) {
            var aliasDefinition = opts.aliases[aliasStr];
            if (aliasDefinition) {
                if (aliasDefinition.alias) resolveAlias(aliasDefinition.alias, undefined, opts); //alias is another alias
                $.extend(true, opts, aliasDefinition);  //merge alias definition in the options
                $.extend(true, opts, options);  //reapply extra given options
                return true;
            }
            return false;
        }


        function generateMaskSets(opts) {
            var ms = [];
            var genmasks = []; //used to keep track of the masks that where processed, to avoid duplicates
            function getMaskTemplate(mask) {
                if (opts.numericInput) {
                    mask = mask.split('').reverse().join('');
                }
                var escaped = false, outCount = 0, greedy = opts.greedy, repeat = opts.repeat;
                if (repeat == "*") greedy = false;
                //if (greedy == true && opts.placeholder == "") opts.placeholder = " ";
                if (mask.length == 1 && greedy == false && repeat != 0) { opts.placeholder = ""; } //hide placeholder with single non-greedy mask
                var singleMask = $.map(mask.split(""), function (element, index) {
                    var outElem = [];
                    if (element == opts.escapeChar) {
                        escaped = true;
                    }
                    else if ((element != opts.optionalmarker.start && element != opts.optionalmarker.end) || escaped) {
                        var maskdef = opts.definitions[element];
                        if (maskdef && !escaped) {
                            for (var i = 0; i < maskdef.cardinality; i++) {
                                outElem.push(opts.placeholder.charAt((outCount + i) % opts.placeholder.length));
                            }
                        } else {
                            outElem.push(element);
                            escaped = false;
                        }
                        outCount += outElem.length;
                        return outElem;
                    }
                });

                //allocate repetitions
                var repeatedMask = singleMask.slice();
                for (var i = 1; i < repeat && greedy; i++) {
                    repeatedMask = repeatedMask.concat(singleMask.slice());
                }

                return { "mask": repeatedMask, "repeat": repeat, "greedy": greedy };
            }
            //test definition => {fn: RegExp/function, cardinality: int, optionality: bool, newBlockMarker: bool, offset: int, casing: null/upper/lower, def: definitionSymbol}
            function getTestingChain(mask) {
                if (opts.numericInput) {
                    mask = mask.split('').reverse().join('');
                }
                var isOptional = false, escaped = false;
                var newBlockMarker = false; //indicates wheter the begin/ending of a block should be indicated

                return $.map(mask.split(""), function (element, index) {
                    var outElem = [];

                    if (element == opts.escapeChar) {
                        escaped = true;
                    } else if (element == opts.optionalmarker.start && !escaped) {
                        isOptional = true;
                        newBlockMarker = true;
                    }
                    else if (element == opts.optionalmarker.end && !escaped) {
                        isOptional = false;
                        newBlockMarker = true;
                    }
                    else {
                        var maskdef = opts.definitions[element];
                        if (maskdef && !escaped) {
                            var prevalidators = maskdef["prevalidator"], prevalidatorsL = prevalidators ? prevalidators.length : 0;
                            for (var i = 1; i < maskdef.cardinality; i++) {
                                var prevalidator = prevalidatorsL >= i ? prevalidators[i - 1] : [], validator = prevalidator["validator"], cardinality = prevalidator["cardinality"];
                                outElem.push({ fn: validator ? typeof validator == 'string' ? new RegExp(validator) : new function () { this.test = validator; } : new RegExp("."), cardinality: cardinality ? cardinality : 1, optionality: isOptional, newBlockMarker: isOptional == true ? newBlockMarker : false, offset: 0, casing: maskdef["casing"], def: maskdef["definitionSymbol"] || element });
                                if (isOptional == true) //reset newBlockMarker
                                    newBlockMarker = false;
                            }
                            outElem.push({ fn: maskdef.validator ? typeof maskdef.validator == 'string' ? new RegExp(maskdef.validator) : new function () { this.test = maskdef.validator; } : new RegExp("."), cardinality: maskdef.cardinality, optionality: isOptional, newBlockMarker: newBlockMarker, offset: 0, casing: maskdef["casing"], def: maskdef["definitionSymbol"] || element });
                        } else {
                            outElem.push({ fn: null, cardinality: 0, optionality: isOptional, newBlockMarker: newBlockMarker, offset: 0, casing: null, def: element });
                            escaped = false;
                        }
                        //reset newBlockMarker
                        newBlockMarker = false;
                        return outElem;
                    }
                });
            }
            function markOptional(maskPart) { //needed for the clearOptionalTail functionality
                return opts.optionalmarker.start + maskPart + opts.optionalmarker.end;
            }
            function splitFirstOptionalEndPart(maskPart) {
                var optionalStartMarkers = 0, optionalEndMarkers = 0, mpl = maskPart.length;
                for (var i = 0; i < mpl; i++) {
                    if (maskPart.charAt(i) == opts.optionalmarker.start) {
                        optionalStartMarkers++;
                    }
                    if (maskPart.charAt(i) == opts.optionalmarker.end) {
                        optionalEndMarkers++;
                    }
                    if (optionalStartMarkers > 0 && optionalStartMarkers == optionalEndMarkers)
                        break;
                }
                var maskParts = [maskPart.substring(0, i)];
                if (i < mpl) {
                    maskParts.push(maskPart.substring(i + 1, mpl));
                }
                return maskParts;
            }
            function splitFirstOptionalStartPart(maskPart) {
                var mpl = maskPart.length;
                for (var i = 0; i < mpl; i++) {
                    if (maskPart.charAt(i) == opts.optionalmarker.start) {
                        break;
                    }
                }
                var maskParts = [maskPart.substring(0, i)];
                if (i < mpl) {
                    maskParts.push(maskPart.substring(i + 1, mpl));
                }
                return maskParts;
            }
            function generateMask(maskPrefix, maskPart, metadata) {
                var maskParts = splitFirstOptionalEndPart(maskPart);
                var newMask, maskTemplate;

                var masks = splitFirstOptionalStartPart(maskParts[0]);
                if (masks.length > 1) {
                    newMask = maskPrefix + masks[0] + markOptional(masks[1]) + (maskParts.length > 1 ? maskParts[1] : "");
                    if ($.inArray(newMask, genmasks) == -1 && newMask != "") {
                        genmasks.push(newMask);
                        maskTemplate = getMaskTemplate(newMask);
                        ms.push({
                            "mask": newMask,
                            "_buffer": maskTemplate["mask"],
                            "buffer": maskTemplate["mask"].slice(),
                            "tests": getTestingChain(newMask),
                            "lastValidPosition": -1,
                            "greedy": maskTemplate["greedy"],
                            "repeat": maskTemplate["repeat"],
                            "metadata": metadata
                        });
                    }
                    newMask = maskPrefix + masks[0] + (maskParts.length > 1 ? maskParts[1] : "");
                    if ($.inArray(newMask, genmasks) == -1 && newMask != "") {
                        genmasks.push(newMask);
                        maskTemplate = getMaskTemplate(newMask);
                        ms.push({
                            "mask": newMask,
                            "_buffer": maskTemplate["mask"],
                            "buffer": maskTemplate["mask"].slice(),
                            "tests": getTestingChain(newMask),
                            "lastValidPosition": -1,
                            "greedy": maskTemplate["greedy"],
                            "repeat": maskTemplate["repeat"],
                            "metadata": metadata
                        });
                    }
                    if (splitFirstOptionalStartPart(masks[1]).length > 1) { //optional contains another optional
                        generateMask(maskPrefix + masks[0], masks[1] + maskParts[1], metadata);
                    }
                    if (maskParts.length > 1 && splitFirstOptionalStartPart(maskParts[1]).length > 1) {
                        generateMask(maskPrefix + masks[0] + markOptional(masks[1]), maskParts[1], metadata);
                        generateMask(maskPrefix + masks[0], maskParts[1], metadata);
                    }
                }
                else {
                    newMask = maskPrefix + maskParts;
                    if ($.inArray(newMask, genmasks) == -1 && newMask != "") {
                        genmasks.push(newMask);
                        maskTemplate = getMaskTemplate(newMask);
                        ms.push({
                            "mask": newMask,
                            "_buffer": maskTemplate["mask"],
                            "buffer": maskTemplate["mask"].slice(),
                            "tests": getTestingChain(newMask),
                            "lastValidPosition": -1,
                            "greedy": maskTemplate["greedy"],
                            "repeat": maskTemplate["repeat"],
                            "metadata": metadata
                        });
                    }
                }

            }

            if ($.isFunction(opts.mask)) { //allow mask to be a preprocessing fn - should return a valid mask
                opts.mask = opts.mask.call(this, opts);
            }
            if ($.isArray(opts.mask)) {
                $.each(opts.mask, function (ndx, msk) {
                    if (msk["mask"] != undefined) {
                        generateMask("", msk["mask"].toString(), msk);
                    } else
                        generateMask("", msk.toString());
                });
            } else generateMask("", opts.mask.toString());

            return opts.greedy ? ms : ms.sort(function (a, b) { return a["mask"].length - b["mask"].length; });
        }


        var msie10 = navigator.userAgent.match(new RegExp("msie 10", "i")) !== null,
            iphone = navigator.userAgent.match(new RegExp("iphone", "i")) !== null,
            android = navigator.userAgent.match(new RegExp("android.*safari.*", "i")) !== null,
            androidchrome = navigator.userAgent.match(new RegExp("android.*chrome.*", "i")) !== null,
            pasteEvent = isInputEventSupported('paste') ? 'paste' : isInputEventSupported('input') ? 'input' : "propertychange";


        //masking scope

        function maskScope(masksets, activeMasksetIndex, opts) {
            var isRTL = false,
                valueOnFocus = getActiveBuffer().join(''),
                $el, chromeValueOnInput;

            //maskset helperfunctions

            function getActiveMaskSet() {
                return masksets[activeMasksetIndex];
            }

            function getActiveTests() {
                return getActiveMaskSet()['tests'];
            }

            function getActiveBufferTemplate() {
                return getActiveMaskSet()['_buffer'];
            }

            function getActiveBuffer() {
                return getActiveMaskSet()['buffer'];
            }

            function isValid(pos, c, strict) { //strict true ~ no correction or autofill
                strict = strict === true; //always set a value to strict to prevent possible strange behavior in the extensions 

                function _isValid(position, activeMaskset, c, strict) {
                    var testPos = determineTestPosition(position), loopend = c ? 1 : 0, chrs = '', buffer = activeMaskset["buffer"];
                    for (var i = activeMaskset['tests'][testPos].cardinality; i > loopend; i--) {
                        chrs += getBufferElement(buffer, testPos - (i - 1));
                    }

                    if (c) {
                        chrs += c;
                    }

                    //return is false or a json object => { pos: ??, c: ??} or true
                    return activeMaskset['tests'][testPos].fn != null ?
                        activeMaskset['tests'][testPos].fn.test(chrs, buffer, position, strict, opts)
                        : (c == getBufferElement(activeMaskset['_buffer'], position, true) || c == opts.skipOptionalPartCharacter) ?
                            { "refresh": true, c: getBufferElement(activeMaskset['_buffer'], position, true), pos: position }
                            : false;
                }

                function PostProcessResults(maskForwards, results) {
                    var hasValidActual = false;
                    $.each(results, function (ndx, rslt) {
                        hasValidActual = $.inArray(rslt["activeMasksetIndex"], maskForwards) == -1 && rslt["result"] !== false;
                        if (hasValidActual) return false;
                    });
                    if (hasValidActual) { //strip maskforwards
                        results = $.map(results, function (rslt, ndx) {
                            if ($.inArray(rslt["activeMasksetIndex"], maskForwards) == -1) {
                                return rslt;
                            } else {
                                masksets[rslt["activeMasksetIndex"]]["lastValidPosition"] = actualLVP;
                            }
                        });
                    } else { //keep maskforwards with the least forward
                        var lowestPos = -1, lowestIndex = -1, rsltValid;
                        $.each(results, function (ndx, rslt) {
                            if ($.inArray(rslt["activeMasksetIndex"], maskForwards) != -1 && rslt["result"] !== false & (lowestPos == -1 || lowestPos > rslt["result"]["pos"])) {
                                lowestPos = rslt["result"]["pos"];
                                lowestIndex = rslt["activeMasksetIndex"];
                            }
                        });
                        results = $.map(results, function (rslt, ndx) {
                            if ($.inArray(rslt["activeMasksetIndex"], maskForwards) != -1) {
                                if (rslt["result"]["pos"] == lowestPos) {
                                    return rslt;
                                } else if (rslt["result"] !== false) {
                                    for (var i = pos; i < lowestPos; i++) {
                                        rsltValid = _isValid(i, masksets[rslt["activeMasksetIndex"]], masksets[lowestIndex]["buffer"][i], true);
                                        if (rsltValid === false) {
                                            masksets[rslt["activeMasksetIndex"]]["lastValidPosition"] = lowestPos - 1;
                                            break;
                                        } else {
                                            setBufferElement(masksets[rslt["activeMasksetIndex"]]["buffer"], i, masksets[lowestIndex]["buffer"][i], true);
                                            masksets[rslt["activeMasksetIndex"]]["lastValidPosition"] = i;
                                        }
                                    }
                                    //also check check for the lowestpos with the new input
                                    rsltValid = _isValid(lowestPos, masksets[rslt["activeMasksetIndex"]], c, true);
                                    if (rsltValid !== false) {
                                        setBufferElement(masksets[rslt["activeMasksetIndex"]]["buffer"], lowestPos, c, true);
                                        masksets[rslt["activeMasksetIndex"]]["lastValidPosition"] = lowestPos;
                                    }
                                    //console.log("ndx " + rslt["activeMasksetIndex"] + " validate " + masksets[rslt["activeMasksetIndex"]]["buffer"].join('') + " lv " + masksets[rslt["activeMasksetIndex"]]['lastValidPosition']);
                                    return rslt;
                                }
                            }
                        });
                    }
                    return results;
                }

                if (strict) {
                    var result = _isValid(pos, getActiveMaskSet(), c, strict); //only check validity in current mask when validating strict
                    if (result === true) {
                        result = { "pos": pos }; //always take a possible corrected maskposition into account
                    }
                    return result;
                }

                var results = [], result = false, currentActiveMasksetIndex = activeMasksetIndex,
                    actualBuffer = getActiveBuffer().slice(), actualLVP = getActiveMaskSet()["lastValidPosition"],
                    actualPrevious = seekPrevious(pos),
                    maskForwards = [];
                $.each(masksets, function (index, value) {
                    if (typeof (value) == "object") {
                        activeMasksetIndex = index;

                        var maskPos = pos;
                        var lvp = getActiveMaskSet()['lastValidPosition'],
                            rsltValid;
                        if (lvp == actualLVP) {
                            if ((maskPos - actualLVP) > 1) {
                                for (var i = lvp == -1 ? 0 : lvp; i < maskPos; i++) {
                                    rsltValid = _isValid(i, getActiveMaskSet(), actualBuffer[i], true);
                                    if (rsltValid === false) {
                                        break;
                                    } else {
                                        setBufferElement(getActiveBuffer(), i, actualBuffer[i], true);
                                        if (rsltValid === true) {
                                            rsltValid = { "pos": i }; //always take a possible corrected maskposition into account
                                        }
                                        var newValidPosition = rsltValid.pos || i;
                                        if (getActiveMaskSet()['lastValidPosition'] < newValidPosition)
                                            getActiveMaskSet()['lastValidPosition'] = newValidPosition; //set new position from isValid
                                    }
                                }
                            }
                            //does the input match on a further position?
                            if (!isMask(maskPos) && !_isValid(maskPos, getActiveMaskSet(), c, strict)) {
                                var maxForward = seekNext(maskPos) - maskPos;
                                for (var fw = 0; fw < maxForward; fw++) {
                                    if (_isValid(++maskPos, getActiveMaskSet(), c, strict) !== false)
                                        break;
                                }
                                maskForwards.push(activeMasksetIndex);
                                //console.log('maskforward ' + activeMasksetIndex + " pos " + pos + " maskPos " + maskPos);
                            }
                        }

                        if (getActiveMaskSet()['lastValidPosition'] >= actualLVP || activeMasksetIndex == currentActiveMasksetIndex) {
                            if (maskPos >= 0 && maskPos < getMaskLength()) {
                                result = _isValid(maskPos, getActiveMaskSet(), c, strict);
                                if (result !== false) {
                                    if (result === true) {
                                        result = { "pos": maskPos }; //always take a possible corrected maskposition into account
                                    }
                                    var newValidPosition = result.pos || maskPos;
                                    if (getActiveMaskSet()['lastValidPosition'] < newValidPosition)
                                        getActiveMaskSet()['lastValidPosition'] = newValidPosition; //set new position from isValid
                                }
                                //console.log("pos " + pos + " ndx " + activeMasksetIndex + " validate " + getActiveBuffer().join('') + " lv " + getActiveMaskSet()['lastValidPosition']);
                                results.push({ "activeMasksetIndex": index, "result": result });
                            }
                        }
                    }
                });
                activeMasksetIndex = currentActiveMasksetIndex; //reset activeMasksetIndex

                return PostProcessResults(maskForwards, results); //return results of the multiple mask validations
            }

            function determineActiveMasksetIndex() {
                var currentMasksetIndex = activeMasksetIndex,
                    highestValid = { "activeMasksetIndex": 0, "lastValidPosition": -1, "next": -1 };
                $.each(masksets, function (index, value) {
                    if (typeof (value) == "object") {
                        activeMasksetIndex = index;
                        if (getActiveMaskSet()['lastValidPosition'] > highestValid['lastValidPosition']) {
                            highestValid["activeMasksetIndex"] = index;
                            highestValid["lastValidPosition"] = getActiveMaskSet()['lastValidPosition'];
                            highestValid["next"] = seekNext(getActiveMaskSet()['lastValidPosition']);
                        } else if (getActiveMaskSet()['lastValidPosition'] == highestValid['lastValidPosition'] &&
                            (highestValid['next'] == -1 || highestValid['next'] > seekNext(getActiveMaskSet()['lastValidPosition']))) {
                            highestValid["activeMasksetIndex"] = index;
                            highestValid["lastValidPosition"] = getActiveMaskSet()['lastValidPosition'];
                            highestValid["next"] = seekNext(getActiveMaskSet()['lastValidPosition']);
                        }
                    }
                });

                activeMasksetIndex = highestValid["lastValidPosition"] != -1 && masksets[currentMasksetIndex]["lastValidPosition"] == highestValid["lastValidPosition"] ? currentMasksetIndex : highestValid["activeMasksetIndex"];
                if (currentMasksetIndex != activeMasksetIndex) {
                    clearBuffer(getActiveBuffer(), seekNext(highestValid["lastValidPosition"]), getMaskLength());
                    getActiveMaskSet()["writeOutBuffer"] = true;
                }
                $el.data('_inputmask')['activeMasksetIndex'] = activeMasksetIndex; //store the activeMasksetIndex
            }

            function isMask(pos) {
                var testPos = determineTestPosition(pos);
                var test = getActiveTests()[testPos];

                return test != undefined ? test.fn : false;
            }

            function determineTestPosition(pos) {
                return pos % getActiveTests().length;
            }

            function getMaskLength() {
                return opts.getMaskLength(getActiveBufferTemplate(), getActiveMaskSet()['greedy'], getActiveMaskSet()['repeat'], getActiveBuffer(), opts);
            }

            //pos: from position

            function seekNext(pos) {
                var maskL = getMaskLength();
                if (pos >= maskL) return maskL;
                var position = pos;
                while (++position < maskL && !isMask(position)) {
                }
                return position;
            }

            //pos: from position

            function seekPrevious(pos) {
                var position = pos;
                if (position <= 0) return 0;

                while (--position > 0 && !isMask(position)) {
                }
                ;
                return position;
            }

            function setBufferElement(buffer, position, element, autoPrepare) {
                if (autoPrepare) position = prepareBuffer(buffer, position);

                var test = getActiveTests()[determineTestPosition(position)];
                var elem = element;
                if (elem != undefined && test != undefined) {
                    switch (test.casing) {
                        case "upper":
                            elem = element.toUpperCase();
                            break;
                        case "lower":
                            elem = element.toLowerCase();
                            break;
                    }
                }

                buffer[position] = elem;
            }

            function getBufferElement(buffer, position, autoPrepare) {
                if (autoPrepare) position = prepareBuffer(buffer, position);
                return buffer[position];
            }

            //needed to handle the non-greedy mask repetitions

            function prepareBuffer(buffer, position) {
                var j;
                while (buffer[position] == undefined && buffer.length < getMaskLength()) {
                    j = 0;
                    while (getActiveBufferTemplate()[j] !== undefined) { //add a new buffer
                        buffer.push(getActiveBufferTemplate()[j++]);
                    }
                }

                return position;
            }

            function writeBuffer(input, buffer, caretPos) {
                input._valueSet(buffer.join(''));
                if (caretPos != undefined) {
                    caret(input, caretPos);
                }
            }

            function clearBuffer(buffer, start, end, stripNomasks) {
                for (var i = start, maskL = getMaskLength() ; i < end && i < maskL; i++) {
                    if (stripNomasks === true) {
                        if (!isMask(i))
                            setBufferElement(buffer, i, "");
                    } else
                        setBufferElement(buffer, i, getBufferElement(getActiveBufferTemplate().slice(), i, true));
                }
            }

            function setReTargetPlaceHolder(buffer, pos) {
                var testPos = determineTestPosition(pos);
                setBufferElement(buffer, pos, getBufferElement(getActiveBufferTemplate(), testPos));
            }

            function getPlaceHolder(pos) {
                return opts.placeholder.charAt(pos % opts.placeholder.length);
            }

            function checkVal(input, writeOut, strict, nptvl, intelliCheck) {
                var inputValue = nptvl != undefined ? nptvl.slice() : truncateInput(input._valueGet()).split('');

                $.each(masksets, function (ndx, ms) {
                    if (typeof (ms) == "object") {
                        ms["buffer"] = ms["_buffer"].slice();
                        ms["lastValidPosition"] = -1;
                        ms["p"] = -1;
                    }
                });
                if (strict !== true) activeMasksetIndex = 0;
                if (writeOut) input._valueSet(""); //initial clear
                var ml = getMaskLength();
                $.each(inputValue, function (ndx, charCode) {
                    if (intelliCheck === true) {
                        var p = getActiveMaskSet()["p"], lvp = p == -1 ? p : seekPrevious(p),
                            pos = lvp == -1 ? ndx : seekNext(lvp);
                        if ($.inArray(charCode, getActiveBufferTemplate().slice(lvp + 1, pos)) == -1) {
                            $(input).trigger("_keypress", [true, charCode.charCodeAt(0), writeOut, strict, ndx]);
                        }
                    } else {
                        $(input).trigger("_keypress", [true, charCode.charCodeAt(0), writeOut, strict, ndx]);
                    }
                });

                if (strict === true && getActiveMaskSet()["p"] != -1) {
                    getActiveMaskSet()["lastValidPosition"] = seekPrevious(getActiveMaskSet()["p"]);
                }
            }

            function escapeRegex(str) {
                return $.inputmask.escapeRegex.call(this, str);
            }

            function truncateInput(inputValue) {
                return inputValue.replace(new RegExp("(" + escapeRegex(getActiveBufferTemplate().join('')) + ")*$"), "");
            }

            function clearOptionalTail(input) {
                var buffer = getActiveBuffer(), tmpBuffer = buffer.slice(), testPos, pos;
                for (var pos = tmpBuffer.length - 1; pos >= 0; pos--) {
                    var testPos = determineTestPosition(pos);
                    if (getActiveTests()[testPos].optionality) {
                        if (!isMask(pos) || !isValid(pos, buffer[pos], true))
                            tmpBuffer.pop();
                        else break;
                    } else break;
                }
                writeBuffer(input, tmpBuffer);
            }

            function unmaskedvalue($input, skipDatepickerCheck) {
                if (getActiveTests() && (skipDatepickerCheck === true || !$input.hasClass('hasDatepicker'))) {
                    //checkVal(input, false, true);
                    var umValue = $.map(getActiveBuffer(), function (element, index) {
                        return isMask(index) && isValid(index, element, true) ? element : null;
                    });
                    var unmaskedValue = (isRTL ? umValue.reverse() : umValue).join('');
                    return opts.onUnMask != undefined ? opts.onUnMask.call(this, getActiveBuffer().join(''), unmaskedValue) : unmaskedValue;
                } else {
                    return $input[0]._valueGet();
                }
            }

            function TranslatePosition(pos) {
                if (isRTL && typeof pos == 'number' && (!opts.greedy || opts.placeholder != "")) {
                    var bffrLght = getActiveBuffer().length;
                    pos = bffrLght - pos;
                }
                return pos;
            }

            function caret(input, begin, end) {
                var npt = input.jquery && input.length > 0 ? input[0] : input, range;
                if (typeof begin == 'number') {
                    begin = TranslatePosition(begin);
                    end = TranslatePosition(end);
                    if (!$(input).is(':visible')) {
                        return;
                    }
                    end = (typeof end == 'number') ? end : begin;
                    npt.scrollLeft = npt.scrollWidth;
                    if (opts.insertMode == false && begin == end) end++; //set visualization for insert/overwrite mode
                    if (npt.setSelectionRange) {
                        npt.selectionStart = begin;
                        npt.selectionEnd = android ? begin : end;

                    } else if (npt.createTextRange) {
                        range = npt.createTextRange();
                        range.collapse(true);
                        range.moveEnd('character', end);
                        range.moveStart('character', begin);
                        range.select();
                    }
                } else {
                    if (!$(input).is(':visible')) {
                        return { "begin": 0, "end": 0 };
                    }
                    if (npt.setSelectionRange) {
                        begin = npt.selectionStart;
                        end = npt.selectionEnd;
                    } else if (document.selection && document.selection.createRange) {
                        range = document.selection.createRange();
                        begin = 0 - range.duplicate().moveStart('character', -100000);
                        end = begin + range.text.length;
                    }
                    begin = TranslatePosition(begin);
                    end = TranslatePosition(end);
                    return { "begin": begin, "end": end };
                }
            }

            function isComplete(buffer) { //return true / false / undefined (repeat *)
                if (opts.repeat == "*") return undefined;
                var complete = false, highestValidPosition = 0, currentActiveMasksetIndex = activeMasksetIndex;
                $.each(masksets, function (ndx, ms) {
                    if (typeof (ms) == "object") {
                        activeMasksetIndex = ndx;
                        var aml = seekPrevious(getMaskLength());
                        if (ms["lastValidPosition"] >= highestValidPosition && ms["lastValidPosition"] == aml) {
                            var msComplete = true;
                            for (var i = 0; i <= aml; i++) {
                                var mask = isMask(i), testPos = determineTestPosition(i);
                                if ((mask && (buffer[i] == undefined || buffer[i] == getPlaceHolder(i))) || (!mask && buffer[i] != getActiveBufferTemplate()[testPos])) {
                                    msComplete = false;
                                    break;
                                }
                            }
                            complete = complete || msComplete;
                            if (complete) //break loop
                                return false;
                        }
                        highestValidPosition = ms["lastValidPosition"];
                    }
                });
                activeMasksetIndex = currentActiveMasksetIndex; //reset activeMaskset
                return complete;
            }

            function isSelection(begin, end) {
                return isRTL ? (begin - end) > 1 || ((begin - end) == 1 && opts.insertMode) :
                    (end - begin) > 1 || ((end - begin) == 1 && opts.insertMode);
            }

            function mask(el) {
                $el = $(el);
                if (!$el.is(":input")) return;

                //store tests & original buffer in the input element - used to get the unmasked value
                $el.data('_inputmask', {
                    'masksets': masksets,
                    'activeMasksetIndex': activeMasksetIndex,
                    'opts': opts,
                    'isRTL': false
                });

                //show tooltip
                if (opts.showTooltip) {
                    $el.prop("title", getActiveMaskSet()["mask"]);
                }

                //correct greedy setting if needed
                getActiveMaskSet()['greedy'] = getActiveMaskSet()['greedy'] ? getActiveMaskSet()['greedy'] : getActiveMaskSet()['repeat'] == 0;

                //handle maxlength attribute
                if ($el.attr("maxLength") != null) //only when the attribute is set
                {
                    var maxLength = $el.prop('maxLength');
                    if (maxLength > -1) { //handle *-repeat
                        $.each(masksets, function (ndx, ms) {
                            if (typeof (ms) == "object") {
                                if (ms["repeat"] == "*") {
                                    ms["repeat"] = maxLength;
                                }
                            }
                        });
                    }
                    if (getMaskLength() >= maxLength && maxLength > -1) { //FF sets no defined max length to -1 
                        if (maxLength < getActiveBufferTemplate().length) getActiveBufferTemplate().length = maxLength;
                        if (getActiveMaskSet()['greedy'] == false) {
                            getActiveMaskSet()['repeat'] = Math.round(maxLength / getActiveBufferTemplate().length);
                        }
                        $el.prop('maxLength', getMaskLength() * 2);
                    }
                }

                patchValueProperty(el);

                //init vars
                var skipKeyPressEvent = false, //Safari 5.1.x - modal dialog fires keypress twice workaround
                    skipInputEvent = false, //skip when triggered from within inputmask
                    ignorable = false;

                if (opts.numericInput) opts.isNumeric = opts.numericInput;
                if (el.dir == "rtl" || (opts.numericInput && opts.rightAlignNumerics) || (opts.isNumeric && opts.rightAlignNumerics))
                    $el.css("text-align", "right");

                if (el.dir == "rtl" || opts.numericInput) {
                    el.dir = "ltr";
                    $el.removeAttr("dir");
                    var inputData = $el.data('_inputmask');
                    inputData['isRTL'] = true;
                    $el.data('_inputmask', inputData);
                    isRTL = true;
                }

                //unbind all events - to make sure that no other mask will interfere when re-masking
                $el.unbind(".inputmask");
                $el.removeClass('focus.inputmask');
                //bind events
                $el.closest('form').bind("submit", function () { //trigger change on submit if any
                    if (valueOnFocus != getActiveBuffer().join('')) {
                        $el.change();
                    }
                }).bind('reset', function () {
                    setTimeout(function () {
                        $el.trigger("setvalue");
                    }, 0);
                });
                $el.bind("mouseenter.inputmask", function () {
                    var $input = $(this), input = this;
                    if (!$input.hasClass('focus.inputmask') && opts.showMaskOnHover) {
                        if (input._valueGet() != getActiveBuffer().join('')) {
                            writeBuffer(input, getActiveBuffer());
                        }
                    }
                }).bind("blur.inputmask", function () {
                    var $input = $(this), input = this, nptValue = input._valueGet(), buffer = getActiveBuffer();
                    $input.removeClass('focus.inputmask');
                    if (valueOnFocus != getActiveBuffer().join('')) {
                        $input.change();
                    }
                    if (opts.clearMaskOnLostFocus && nptValue != '') {
                        if (nptValue == getActiveBufferTemplate().join(''))
                            input._valueSet('');
                        else { //clearout optional tail of the mask
                            clearOptionalTail(input);
                        }
                    }
                    if (isComplete(buffer) === false) {
                        $input.trigger("incomplete");
                        if (opts.clearIncomplete) {
                            $.each(masksets, function (ndx, ms) {
                                if (typeof (ms) == "object") {
                                    ms["buffer"] = ms["_buffer"].slice();
                                    ms["lastValidPosition"] = -1;
                                }
                            });
                            activeMasksetIndex = 0;
                            if (opts.clearMaskOnLostFocus)
                                input._valueSet('');
                            else {
                                buffer = getActiveBufferTemplate().slice();
                                writeBuffer(input, buffer);
                            }
                        }
                    }
                }).bind("focus.inputmask", function () {
                    var $input = $(this), input = this, nptValue = input._valueGet();
                    if (opts.showMaskOnFocus && !$input.hasClass('focus.inputmask') && (!opts.showMaskOnHover || (opts.showMaskOnHover && nptValue == ''))) {
                        if (input._valueGet() != getActiveBuffer().join('')) {
                            writeBuffer(input, getActiveBuffer(), seekNext(getActiveMaskSet()["lastValidPosition"]));
                        }
                    }
                    $input.addClass('focus.inputmask');
                    valueOnFocus = getActiveBuffer().join('');
                }).bind("mouseleave.inputmask", function () {
                    var $input = $(this), input = this;
                    if (opts.clearMaskOnLostFocus) {
                        if (!$input.hasClass('focus.inputmask') && input._valueGet() != $input.attr("placeholder")) {
                            if (input._valueGet() == getActiveBufferTemplate().join('') || input._valueGet() == '')
                                input._valueSet('');
                            else { //clearout optional tail of the mask
                                clearOptionalTail(input);
                            }
                        }
                    }
                }).bind("click.inputmask", function () {
                    var input = this;
                    setTimeout(function () {
                        var selectedCaret = caret(input), buffer = getActiveBuffer();
                        if (selectedCaret.begin == selectedCaret.end) {
                            var clickPosition = isRTL ? TranslatePosition(selectedCaret.begin) : selectedCaret.begin,
                                lvp = getActiveMaskSet()["lastValidPosition"],
                                lastPosition;
                            if (opts.isNumeric) {
                                lastPosition = opts.skipRadixDance === false && opts.radixPoint != "" && $.inArray(opts.radixPoint, buffer) != -1 ?
                                    (opts.numericInput ? seekNext($.inArray(opts.radixPoint, buffer)) : $.inArray(opts.radixPoint, buffer)) :
                                    seekNext(lvp);
                            } else {
                                lastPosition = seekNext(lvp);
                            }
                            if (clickPosition < lastPosition) {
                                if (isMask(clickPosition))
                                    caret(input, clickPosition);
                                else caret(input, seekNext(clickPosition));
                            } else
                                caret(input, lastPosition);
                        }
                    }, 0);
                }).bind('dblclick.inputmask', function () {
                    var input = this;
                    setTimeout(function () {
                        caret(input, 0, seekNext(getActiveMaskSet()["lastValidPosition"]));
                    }, 0);
                }).bind(pasteEvent + ".inputmask dragdrop.inputmask drop.inputmask", function (e) {
                    if (skipInputEvent === true) {
                        skipInputEvent = false;
                        return true;
                    }
                    var input = this, $input = $(input);

                    //paste event for IE8 and lower I guess ;-)
                    if (e.type == "propertychange" && input._valueGet().length <= getMaskLength()) {
                        return true;
                    }
                    setTimeout(function () {
                        var pasteValue = opts.onBeforePaste != undefined ? opts.onBeforePaste.call(this, input._valueGet()) : input._valueGet();
                        checkVal(input, true, false, pasteValue.split(''), true);
                        if (isComplete(getActiveBuffer()) === true)
                            $input.trigger("complete");
                        $input.click();
                    }, 0);
                }).bind('setvalue.inputmask', function () {
                    var input = this;
                    checkVal(input, true);
                    valueOnFocus = getActiveBuffer().join('');
                    if (input._valueGet() == getActiveBufferTemplate().join(''))
                        input._valueSet('');
                }).bind("_keypress.inputmask", keypressEvent //will be skipped be the eventruler
                ).bind('complete.inputmask', opts.oncomplete
                ).bind('incomplete.inputmask', opts.onincomplete
                ).bind('cleared.inputmask', opts.oncleared
                ).bind("keyup.inputmask", keyupEvent);

                if (androidchrome) {
                    $el.bind("input.inputmask", inputEvent);
                } else {
                    $el.bind("keydown.inputmask", keydownEvent
                    ).bind("keypress.inputmask", keypressEvent);
                }

                if (msie10)
                    $el.bind("input.inputmask", inputEvent);

                //apply mask
                checkVal(el, true, false);
                valueOnFocus = getActiveBuffer().join('');
                // Wrap document.activeElement in a try/catch block since IE9 throw "Unspecified error" if document.activeElement is undefined when we are in an IFrame.
                var activeElement;
                try {
                    activeElement = document.activeElement;
                } catch (e) {
                }
                if (activeElement === el) { //position the caret when in focus
                    $el.addClass('focus.inputmask');
                    caret(el, seekNext(getActiveMaskSet()["lastValidPosition"]));
                } else if (opts.clearMaskOnLostFocus) {
                    if (getActiveBuffer().join('') == getActiveBufferTemplate().join('')) {
                        el._valueSet('');
                    } else {
                        clearOptionalTail(el);
                    }
                } else {
                    writeBuffer(el, getActiveBuffer());
                }

                installEventRuler(el);

                //private functions

                function installEventRuler(npt) {
                    var events = $._data(npt).events;

                    $.each(events, function (eventType, eventHandlers) {
                        $.each(eventHandlers, function (ndx, eventHandler) {
                            if (eventHandler.namespace == "inputmask") {
                                if (eventHandler.type != "setvalue" && eventHandler.type != "_keypress") {
                                    var handler = eventHandler.handler;
                                    eventHandler.handler = function (e) {
                                        if (this.readOnly || this.disabled)
                                            e.preventDefault;
                                        else
                                            return handler.apply(this, arguments);
                                    };
                                }
                            }
                        });
                    });
                }

                function patchValueProperty(npt) {
                    var valueProperty;
                    if (Object.getOwnPropertyDescriptor)
                        valueProperty = Object.getOwnPropertyDescriptor(npt, "value");
                    if (valueProperty && valueProperty.get) {
                        if (!npt._valueGet) {
                            var valueGet = valueProperty.get;
                            var valueSet = valueProperty.set;
                            npt._valueGet = function () {
                                return isRTL ? valueGet.call(this).split('').reverse().join('') : valueGet.call(this);
                            };
                            npt._valueSet = function (value) {
                                valueSet.call(this, isRTL ? value.split('').reverse().join('') : value);
                            };

                            Object.defineProperty(npt, "value", {
                                get: function () {
                                    var $self = $(this), inputData = $(this).data('_inputmask'), masksets = inputData['masksets'],
                                        activeMasksetIndex = inputData['activeMasksetIndex'];
                                    return inputData && inputData['opts'].autoUnmask ? $self.inputmask('unmaskedvalue') : valueGet.call(this) != masksets[activeMasksetIndex]['_buffer'].join('') ? valueGet.call(this) : '';
                                },
                                set: function (value) {
                                    valueSet.call(this, value);
                                    $(this).triggerHandler('setvalue.inputmask');
                                }
                            });
                        }
                    } else if (document.__lookupGetter__ && npt.__lookupGetter__("value")) {
                        if (!npt._valueGet) {
                            var valueGet = npt.__lookupGetter__("value");
                            var valueSet = npt.__lookupSetter__("value");
                            npt._valueGet = function () {
                                return isRTL ? valueGet.call(this).split('').reverse().join('') : valueGet.call(this);
                            };
                            npt._valueSet = function (value) {
                                valueSet.call(this, isRTL ? value.split('').reverse().join('') : value);
                            };

                            npt.__defineGetter__("value", function () {
                                var $self = $(this), inputData = $(this).data('_inputmask'), masksets = inputData['masksets'],
                                    activeMasksetIndex = inputData['activeMasksetIndex'];
                                return inputData && inputData['opts'].autoUnmask ? $self.inputmask('unmaskedvalue') : valueGet.call(this) != masksets[activeMasksetIndex]['_buffer'].join('') ? valueGet.call(this) : '';
                            });
                            npt.__defineSetter__("value", function (value) {
                                valueSet.call(this, value);
                                $(this).triggerHandler('setvalue.inputmask');
                            });
                        }
                    } else {
                        if (!npt._valueGet) {
                            npt._valueGet = function () { return isRTL ? this.value.split('').reverse().join('') : this.value; };
                            npt._valueSet = function (value) { this.value = isRTL ? value.split('').reverse().join('') : value; };
                        }
                        if ($.valHooks.text == undefined || $.valHooks.text.inputmaskpatch != true) {
                            var valueGet = $.valHooks.text && $.valHooks.text.get ? $.valHooks.text.get : function (elem) { return elem.value; };
                            var valueSet = $.valHooks.text && $.valHooks.text.set ? $.valHooks.text.set : function (elem, value) {
                                elem.value = value;
                                return elem;
                            };

                            jQuery.extend($.valHooks, {
                                text: {
                                    get: function (elem) {
                                        var $elem = $(elem);
                                        if ($elem.data('_inputmask')) {
                                            if ($elem.data('_inputmask')['opts'].autoUnmask)
                                                return $elem.inputmask('unmaskedvalue');
                                            else {
                                                var result = valueGet(elem),
                                                    inputData = $elem.data('_inputmask'), masksets = inputData['masksets'],
                                                    activeMasksetIndex = inputData['activeMasksetIndex'];
                                                return result != masksets[activeMasksetIndex]['_buffer'].join('') ? result : '';
                                            }
                                        } else return valueGet(elem);
                                    },
                                    set: function (elem, value) {
                                        var $elem = $(elem);
                                        var result = valueSet(elem, value);
                                        if ($elem.data('_inputmask')) $elem.triggerHandler('setvalue.inputmask');
                                        return result;
                                    },
                                    inputmaskpatch: true
                                }
                            });
                        }
                    }
                }

                //shift chars to left from start to end and put c at end position if defined

                function shiftL(start, end, c, maskJumps) {
                    var buffer = getActiveBuffer();
                    if (maskJumps !== false) //jumping over nonmask position
                        while (!isMask(start) && start - 1 >= 0) start--;
                    for (var i = start; i < end && i < getMaskLength() ; i++) {
                        if (isMask(i)) {
                            setReTargetPlaceHolder(buffer, i);
                            var j = seekNext(i);
                            var p = getBufferElement(buffer, j);
                            if (p != getPlaceHolder(j)) {
                                if (j < getMaskLength() && isValid(i, p, true) !== false && getActiveTests()[determineTestPosition(i)].def == getActiveTests()[determineTestPosition(j)].def) {
                                    setBufferElement(buffer, i, p, true);
                                } else {
                                    if (isMask(i))
                                        break;
                                }
                            }
                        } else {
                            setReTargetPlaceHolder(buffer, i);
                        }
                    }
                    if (c != undefined)
                        setBufferElement(buffer, seekPrevious(end), c);

                    if (getActiveMaskSet()["greedy"] == false) {
                        var trbuffer = truncateInput(buffer.join('')).split('');
                        buffer.length = trbuffer.length;
                        for (var i = 0, bl = buffer.length; i < bl; i++) {
                            buffer[i] = trbuffer[i];
                        }
                        if (buffer.length == 0) getActiveMaskSet()["buffer"] = getActiveBufferTemplate().slice();
                    }
                    return start; //return the used start position
                }

                function shiftR(start, end, c) {
                    var buffer = getActiveBuffer();
                    if (getBufferElement(buffer, start, true) != getPlaceHolder(start)) {
                        for (var i = seekPrevious(end) ; i > start && i >= 0; i--) {
                            if (isMask(i)) {
                                var j = seekPrevious(i);
                                var t = getBufferElement(buffer, j);
                                if (t != getPlaceHolder(j)) {
                                    if (isValid(j, t, true) !== false && getActiveTests()[determineTestPosition(i)].def == getActiveTests()[determineTestPosition(j)].def) {
                                        setBufferElement(buffer, i, t, true);
                                        setReTargetPlaceHolder(buffer, j);
                                    } //else break;
                                }
                            } else
                                setReTargetPlaceHolder(buffer, i);
                        }
                    }
                    if (c != undefined && getBufferElement(buffer, start) == getPlaceHolder(start))
                        setBufferElement(buffer, start, c);
                    var lengthBefore = buffer.length;
                    if (getActiveMaskSet()["greedy"] == false) {
                        var trbuffer = truncateInput(buffer.join('')).split('');
                        buffer.length = trbuffer.length;
                        for (var i = 0, bl = buffer.length; i < bl; i++) {
                            buffer[i] = trbuffer[i];
                        }
                        if (buffer.length == 0) getActiveMaskSet()["buffer"] = getActiveBufferTemplate().slice();
                    }
                    return end - (lengthBefore - buffer.length); //return new start position
                }

                ;


                function HandleRemove(input, k, pos) {
                    if (opts.numericInput || isRTL) {
                        switch (k) {
                            case opts.keyCode.BACKSPACE:
                                k = opts.keyCode.DELETE;
                                break;
                            case opts.keyCode.DELETE:
                                k = opts.keyCode.BACKSPACE;
                                break;
                        }
                        if (isRTL) {
                            var pend = pos.end;
                            pos.end = pos.begin;
                            pos.begin = pend;
                        }
                    }

                    var isSelection = true;
                    if (pos.begin == pos.end) {
                        var posBegin = k == opts.keyCode.BACKSPACE ? pos.begin - 1 : pos.begin;
                        if (opts.isNumeric && opts.radixPoint != "" && getActiveBuffer()[posBegin] == opts.radixPoint) {
                            pos.begin = (getActiveBuffer().length - 1 == posBegin) /* radixPoint is latest? delete it */ ? pos.begin : k == opts.keyCode.BACKSPACE ? posBegin : seekNext(posBegin);
                            pos.end = pos.begin;
                        }
                        isSelection = false;
                        if (k == opts.keyCode.BACKSPACE)
                            pos.begin--;
                        else if (k == opts.keyCode.DELETE)
                            pos.end++;
                    } else if (pos.end - pos.begin == 1 && !opts.insertMode) {
                        isSelection = false;
                        if (k == opts.keyCode.BACKSPACE)
                            pos.begin--;
                    }

                    clearBuffer(getActiveBuffer(), pos.begin, pos.end);

                    var ml = getMaskLength();
                    if (opts.greedy == false) {
                        shiftL(pos.begin, ml, undefined, !isRTL && (k == opts.keyCode.BACKSPACE && !isSelection));
                    } else {
                        var newpos = pos.begin;
                        for (var i = pos.begin; i < pos.end; i++) { //seeknext to skip placeholders at start in selection
                            if (isMask(i) || !isSelection)
                                newpos = shiftL(pos.begin, ml, undefined, !isRTL && (k == opts.keyCode.BACKSPACE && !isSelection));
                        }
                        if (!isSelection) pos.begin = newpos;
                    }
                    var firstMaskPos = seekNext(-1);
                    clearBuffer(getActiveBuffer(), pos.begin, pos.end, true);
                    checkVal(input, false, masksets[1] == undefined || firstMaskPos >= pos.end, getActiveBuffer());
                    if (getActiveMaskSet()['lastValidPosition'] < firstMaskPos) {
                        getActiveMaskSet()["lastValidPosition"] = -1;
                        getActiveMaskSet()["p"] = firstMaskPos;
                    } else {
                        getActiveMaskSet()["p"] = pos.begin;
                    }
                }

                function keydownEvent(e) {
                    //Safari 5.1.x - modal dialog fires keypress twice workaround
                    skipKeyPressEvent = false;
                    var input = this, $input = $(input), k = e.keyCode, pos = caret(input);

                    //backspace, delete, and escape get special treatment
                    if (k == opts.keyCode.BACKSPACE || k == opts.keyCode.DELETE || (iphone && k == 127) || e.ctrlKey && k == 88) { //backspace/delete
                        e.preventDefault(); //stop default action but allow propagation
                        if (k == 88) valueOnFocus = getActiveBuffer().join('');
                        HandleRemove(input, k, pos);
                        determineActiveMasksetIndex();
                        writeBuffer(input, getActiveBuffer(), getActiveMaskSet()["p"]);
                        if (input._valueGet() == getActiveBufferTemplate().join(''))
                            $input.trigger('cleared');

                        if (opts.showTooltip) { //update tooltip
                            $input.prop("title", getActiveMaskSet()["mask"]);
                        }
                    } else if (k == opts.keyCode.END || k == opts.keyCode.PAGE_DOWN) { //when END or PAGE_DOWN pressed set position at lastmatch
                        setTimeout(function () {
                            var caretPos = seekNext(getActiveMaskSet()["lastValidPosition"]);
                            if (!opts.insertMode && caretPos == getMaskLength() && !e.shiftKey) caretPos--;
                            caret(input, e.shiftKey ? pos.begin : caretPos, caretPos);
                        }, 0);
                    } else if ((k == opts.keyCode.HOME && !e.shiftKey) || k == opts.keyCode.PAGE_UP) { //Home or page_up
                        caret(input, 0, e.shiftKey ? pos.begin : 0);
                    } else if (k == opts.keyCode.ESCAPE || (k == 90 && e.ctrlKey)) { //escape && undo
                        checkVal(input, true, false, valueOnFocus.split(''));
                        $input.click();
                    } else if (k == opts.keyCode.INSERT && !(e.shiftKey || e.ctrlKey)) { //insert
                        opts.insertMode = !opts.insertMode;
                        caret(input, !opts.insertMode && pos.begin == getMaskLength() ? pos.begin - 1 : pos.begin);
                    } else if (opts.insertMode == false && !e.shiftKey) {
                        if (k == opts.keyCode.RIGHT) {
                            setTimeout(function () {
                                var caretPos = caret(input);
                                caret(input, caretPos.begin);
                            }, 0);
                        } else if (k == opts.keyCode.LEFT) {
                            setTimeout(function () {
                                var caretPos = caret(input);
                                caret(input, caretPos.begin - 1);
                            }, 0);
                        }
                    }

                    var currentCaretPos = caret(input);
                    if (opts.onKeyDown.call(this, e, getActiveBuffer(), opts) === true) //extra stuff to execute on keydown
                        caret(input, currentCaretPos.begin, currentCaretPos.end);
                    ignorable = $.inArray(k, opts.ignorables) != -1;
                }


                function keypressEvent(e, checkval, k, writeOut, strict, ndx) {
                    //Safari 5.1.x - modal dialog fires keypress twice workaround
                    if (k == undefined && skipKeyPressEvent) return false;
                    skipKeyPressEvent = true;

                    var input = this, $input = $(input);

                    e = e || window.event;
                    var k = k || e.which || e.charCode || e.keyCode;

                    if ((!(e.ctrlKey && e.altKey) && (e.ctrlKey || e.metaKey || ignorable)) && checkval !== true) {
                        return true;
                    } else {
                        if (k) {
                            //special treat the decimal separator
                            if (checkval !== true && k == 46 && e.shiftKey == false && opts.radixPoint == ",") k = 44;

                            var pos, results, result, c = String.fromCharCode(k);
                            if (checkval) {
                                var pcaret = strict ? ndx : getActiveMaskSet()["lastValidPosition"] + 1;
                                pos = { begin: pcaret, end: pcaret };
                            } else {
                                pos = caret(input);
                            }

                            //should we clear a possible selection??
                            var isSlctn = isSelection(pos.begin, pos.end), redetermineLVP = false,
                                initialIndex = activeMasksetIndex;
                            if (isSlctn) {
                                activeMasksetIndex = initialIndex;
                                $.each(masksets, function (ndx, lmnt) { //init undobuffer for recovery when not valid
                                    if (typeof (lmnt) == "object") {
                                        activeMasksetIndex = ndx;
                                        getActiveMaskSet()["undoBuffer"] = getActiveBuffer().join('');
                                    }
                                });
                                HandleRemove(input, opts.keyCode.DELETE, pos);
                                if (!opts.insertMode) { //preserve some space
                                    $.each(masksets, function (ndx, lmnt) {
                                        if (typeof (lmnt) == "object") {
                                            activeMasksetIndex = ndx;
                                            shiftR(pos.begin, getMaskLength());
                                            getActiveMaskSet()["lastValidPosition"] = seekNext(getActiveMaskSet()["lastValidPosition"]);
                                        }
                                    });
                                }
                                activeMasksetIndex = initialIndex; //restore index
                            }

                            var radixPosition = getActiveBuffer().join('').indexOf(opts.radixPoint);
                            if (opts.isNumeric && checkval !== true && radixPosition != -1) {
                                if (opts.greedy && pos.begin <= radixPosition) {
                                    pos.begin = seekPrevious(pos.begin);
                                    pos.end = pos.begin;
                                } else if (c == opts.radixPoint) {
                                    pos.begin = radixPosition;
                                    pos.end = pos.begin;
                                }
                            }


                            var p = pos.begin;
                            results = isValid(p, c, strict);
                            if (strict === true) results = [{ "activeMasksetIndex": activeMasksetIndex, "result": results }];
                            var minimalForwardPosition = -1;
                            $.each(results, function (index, result) {
                                activeMasksetIndex = result["activeMasksetIndex"];
                                getActiveMaskSet()["writeOutBuffer"] = true;
                                var np = result["result"];
                                if (np !== false) {
                                    var refresh = false, buffer = getActiveBuffer();
                                    if (np !== true) {
                                        refresh = np["refresh"]; //only rewrite buffer from isValid
                                        p = np.pos != undefined ? np.pos : p; //set new position from isValid
                                        c = np.c != undefined ? np.c : c; //set new char from isValid
                                    }
                                    if (refresh !== true) {
                                        if (opts.insertMode == true) {
                                            var lastUnmaskedPosition = getMaskLength();
                                            var bfrClone = buffer.slice();
                                            while (getBufferElement(bfrClone, lastUnmaskedPosition, true) != getPlaceHolder(lastUnmaskedPosition) && lastUnmaskedPosition >= p) {
                                                lastUnmaskedPosition = lastUnmaskedPosition == 0 ? -1 : seekPrevious(lastUnmaskedPosition);
                                            }
                                            if (lastUnmaskedPosition >= p) {
                                                shiftR(p, getMaskLength(), c);
                                                //shift the lvp if needed
                                                var lvp = getActiveMaskSet()["lastValidPosition"], nlvp = seekNext(lvp);
                                                if (nlvp != getMaskLength() && lvp >= p && (getBufferElement(getActiveBuffer(), nlvp, true) != getPlaceHolder(nlvp))) {
                                                    getActiveMaskSet()["lastValidPosition"] = nlvp;
                                                }
                                            } else getActiveMaskSet()["writeOutBuffer"] = false;
                                        } else setBufferElement(buffer, p, c, true);
                                        if (minimalForwardPosition == -1 || minimalForwardPosition > seekNext(p)) {
                                            minimalForwardPosition = seekNext(p);
                                        }
                                    } else if (!strict) {
                                        var nextPos = p < getMaskLength() ? p + 1 : p;
                                        if (minimalForwardPosition == -1 || minimalForwardPosition > nextPos) {
                                            minimalForwardPosition = nextPos;
                                        }
                                    }
                                    if (minimalForwardPosition > getActiveMaskSet()["p"])
                                        getActiveMaskSet()["p"] = minimalForwardPosition; //needed for checkval strict 
                                }
                            });

                            if (strict !== true) {
                                activeMasksetIndex = initialIndex;
                                determineActiveMasksetIndex();
                            }
                            if (writeOut !== false) {
                                $.each(results, function (ndx, rslt) {
                                    if (rslt["activeMasksetIndex"] == activeMasksetIndex) {
                                        result = rslt;
                                        return false;
                                    }
                                });
                                if (result != undefined) {
                                    var self = this;
                                    setTimeout(function () { opts.onKeyValidation.call(self, result["result"], opts); }, 0);
                                    if (getActiveMaskSet()["writeOutBuffer"] && result["result"] !== false) {
                                        var buffer = getActiveBuffer();

                                        var newCaretPosition;
                                        if (checkval) {
                                            newCaretPosition = undefined;
                                        } else if (opts.numericInput) {
                                            if (p > radixPosition) {
                                                newCaretPosition = seekPrevious(minimalForwardPosition);
                                            } else if (c == opts.radixPoint) {
                                                newCaretPosition = minimalForwardPosition - 1;
                                            } else newCaretPosition = seekPrevious(minimalForwardPosition - 1);
                                        } else {
                                            newCaretPosition = minimalForwardPosition;
                                        }

                                        writeBuffer(input, buffer, newCaretPosition);
                                        if (checkval !== true) {
                                            setTimeout(function () { //timeout needed for IE
                                                if (isComplete(buffer) === true)
                                                    $input.trigger("complete");
                                                skipInputEvent = true;
                                                $input.trigger("input");
                                            }, 0);
                                        }
                                    } else if (isSlctn) {
                                        getActiveMaskSet()["buffer"] = getActiveMaskSet()["undoBuffer"].split('');
                                    }
                                }
                            }

                            if (opts.showTooltip) { //update tooltip
                                $input.prop("title", getActiveMaskSet()["mask"]);
                            }
                            e.preventDefault();
                        }
                    }
                }

                function keyupEvent(e) {
                    var $input = $(this), input = this, k = e.keyCode, buffer = getActiveBuffer();

                    if (androidchrome && k == opts.keyCode.BACKSPACE) {
                        if (chromeValueOnInput == input._valueGet())
                            keydownEvent.call(this, e);
                    }

                    opts.onKeyUp.call(this, e, buffer, opts); //extra stuff to execute on keyup
                    if (k == opts.keyCode.TAB && opts.showMaskOnFocus) {
                        if ($input.hasClass('focus.inputmask') && input._valueGet().length == 0) {
                            buffer = getActiveBufferTemplate().slice();
                            writeBuffer(input, buffer);
                            caret(input, 0);
                            valueOnFocus = getActiveBuffer().join('');
                        } else {
                            writeBuffer(input, buffer);
                            if (buffer.join('') == getActiveBufferTemplate().join('') && $.inArray(opts.radixPoint, buffer) != -1) {
                                caret(input, TranslatePosition(0));
                                $input.click();
                            } else
                                caret(input, TranslatePosition(0), TranslatePosition(getMaskLength()));
                        }
                    }
                }

                function inputEvent(e) {
                    if (skipInputEvent === true) {
                        skipInputEvent = false;
                        return true;
                    }
                    var input = this, $input = $(input);

                    chromeValueOnInput = getActiveBuffer().join('');
                    checkVal(input, false, false);
                    writeBuffer(input, getActiveBuffer());
                    if (isComplete(getActiveBuffer()) === true)
                        $input.trigger("complete");
                    $input.click();
                }
            }

            return {
                isComplete: function (buffer) {
                    return isComplete(buffer);
                },
                unmaskedvalue: function ($input, skipDatepickerCheck) {
                    isRTL = $input.data('_inputmask')['isRTL'];
                    return unmaskedvalue($input, skipDatepickerCheck);
                },
                mask: function (el) {
                    mask(el);
                }
            };
        };

        $.inputmask = {
            //options default
            defaults: {
                placeholder: "_",
                optionalmarker: { start: "[", end: "]" },
                quantifiermarker: { start: "{", end: "}" },
                groupmarker: { start: "(", end: ")" },
                escapeChar: "\\",
                mask: null,
                oncomplete: $.noop, //executes when the mask is complete
                onincomplete: $.noop, //executes when the mask is incomplete and focus is lost
                oncleared: $.noop, //executes when the mask is cleared
                repeat: 0, //repetitions of the mask: * ~ forever, otherwise specify an integer
                greedy: true, //true: allocated buffer for the mask and repetitions - false: allocate only if needed
                autoUnmask: false, //automatically unmask when retrieving the value with $.fn.val or value if the browser supports __lookupGetter__ or getOwnPropertyDescriptor
                clearMaskOnLostFocus: true,
                insertMode: true, //insert the input or overwrite the input
                clearIncomplete: false, //clear the incomplete input on blur
                aliases: {}, //aliases definitions => see jquery.inputmask.extensions.js
                onKeyUp: $.noop, //override to implement autocomplete on certain keys for example
                onKeyDown: $.noop, //override to implement autocomplete on certain keys for example
                onBeforePaste: undefined, //executes before masking the pasted value to allow preprocessing of the pasted value.  args => pastedValue => return processedValue
                onUnMask: undefined, //executes after unmasking to allow postprocessing of the unmaskedvalue.  args => maskedValue, unmaskedValue
                showMaskOnFocus: true, //show the mask-placeholder when the input has focus
                showMaskOnHover: true, //show the mask-placeholder when hovering the empty input
                onKeyValidation: $.noop, //executes on every key-press with the result of isValid. Params: result, opts
                skipOptionalPartCharacter: " ", //a character which can be used to skip an optional part of a mask
                showTooltip: false, //show the activemask as tooltip
                numericInput: false, //numericInput input direction style (input shifts to the left while holding the caret position)
                //numeric basic properties
                isNumeric: false, //enable numeric features
                radixPoint: "", //".", // | ","
                skipRadixDance: false, //disable radixpoint caret positioning
                rightAlignNumerics: true, //align numerics to the right
                //numeric basic properties
                definitions: {
                    '9': {
                        validator: "[0-9]",
                        cardinality: 1
                    },
                    'a': {
                        validator: "[A-Za-z\u0410-\u044F\u0401\u0451]",
                        cardinality: 1
                    },
                    '*': {
                        validator: "[A-Za-z\u0410-\u044F\u0401\u04510-9]",
                        cardinality: 1
                    }
                },
                keyCode: {
                    ALT: 18, BACKSPACE: 8, CAPS_LOCK: 20, COMMA: 188, COMMAND: 91, COMMAND_LEFT: 91, COMMAND_RIGHT: 93, CONTROL: 17, DELETE: 46, DOWN: 40, END: 35, ENTER: 13, ESCAPE: 27, HOME: 36, INSERT: 45, LEFT: 37, MENU: 93, NUMPAD_ADD: 107, NUMPAD_DECIMAL: 110, NUMPAD_DIVIDE: 111, NUMPAD_ENTER: 108,
                    NUMPAD_MULTIPLY: 106, NUMPAD_SUBTRACT: 109, PAGE_DOWN: 34, PAGE_UP: 33, PERIOD: 190, RIGHT: 39, SHIFT: 16, SPACE: 32, TAB: 9, UP: 38, WINDOWS: 91
                },
                //specify keycodes which should not be considered in the keypress event, otherwise the preventDefault will stop their default behavior especially in FF
                ignorables: [8, 9, 13, 19, 27, 33, 34, 35, 36, 37, 38, 39, 40, 45, 46, 93, 112, 113, 114, 115, 116, 117, 118, 119, 120, 121, 122, 123],
                getMaskLength: function (buffer, greedy, repeat, currentBuffer, opts) {
                    var calculatedLength = buffer.length;
                    if (!greedy) {
                        if (repeat == "*") {
                            calculatedLength = currentBuffer.length + 1;
                        } else if (repeat > 1) {
                            calculatedLength += (buffer.length * (repeat - 1));
                        }
                    }
                    return calculatedLength;
                }
            },
            escapeRegex: function (str) {
                var specials = ['/', '.', '*', '+', '?', '|', '(', ')', '[', ']', '{', '}', '\\'];
                return str.replace(new RegExp('(\\' + specials.join('|\\') + ')', 'gim'), '\\$1');
            },
            format: function (value, opts) {

            }
        };

        $.fn.inputmask = function (fn, options) {
            var opts = $.extend(true, {}, $.inputmask.defaults, options),
            	masksets,
                activeMasksetIndex = 0;

            if (typeof fn === "string") {
                switch (fn) {
                    case "mask":
                        //resolve possible aliases given by options
                        resolveAlias(opts.alias, options, opts);
                        masksets = generateMaskSets(opts);
                        if (masksets.length == 0) { return this; }

                        return this.each(function () {
                            maskScope($.extend(true, {}, masksets), 0, opts).mask(this);
                        });
                    case "unmaskedvalue":
                        var $input = $(this), input = this;
                        if ($input.data('_inputmask')) {
                            masksets = $input.data('_inputmask')['masksets'];
                            activeMasksetIndex = $input.data('_inputmask')['activeMasksetIndex'];
                            opts = $input.data('_inputmask')['opts'];
                            return maskScope(masksets, activeMasksetIndex, opts).unmaskedvalue($input);
                        } else return $input.val();
                    case "remove":
                        return this.each(function () {
                            var $input = $(this), input = this;
                            if ($input.data('_inputmask')) {
                                masksets = $input.data('_inputmask')['masksets'];
                                activeMasksetIndex = $input.data('_inputmask')['activeMasksetIndex'];
                                opts = $input.data('_inputmask')['opts'];
                                //writeout the unmaskedvalue
                                input._valueSet(maskScope(masksets, activeMasksetIndex, opts).unmaskedvalue($input, true));
                                //clear data
                                $input.removeData('_inputmask');
                                //unbind all events
                                $input.unbind(".inputmask");
                                $input.removeClass('focus.inputmask');
                                //restore the value property
                                var valueProperty;
                                if (Object.getOwnPropertyDescriptor)
                                    valueProperty = Object.getOwnPropertyDescriptor(input, "value");
                                if (valueProperty && valueProperty.get) {
                                    if (input._valueGet) {
                                        Object.defineProperty(input, "value", {
                                            get: input._valueGet,
                                            set: input._valueSet
                                        });
                                    }
                                } else if (document.__lookupGetter__ && input.__lookupGetter__("value")) {
                                    if (input._valueGet) {
                                        input.__defineGetter__("value", input._valueGet);
                                        input.__defineSetter__("value", input._valueSet);
                                    }
                                }
                                try { //try catch needed for IE7 as it does not supports deleting fns
                                    delete input._valueGet;
                                    delete input._valueSet;
                                } catch (e) {
                                    input._valueGet = undefined;
                                    input._valueSet = undefined;

                                }
                            }
                        });
                        break;
                    case "getemptymask": //return the default (empty) mask value, usefull for setting the default value in validation
                        if (this.data('_inputmask')) {
                            masksets = this.data('_inputmask')['masksets'];
                            activeMasksetIndex = this.data('_inputmask')['activeMasksetIndex'];
                            return masksets[activeMasksetIndex]['_buffer'].join('');
                        }
                        else return "";
                    case "hasMaskedValue": //check wheter the returned value is masked or not; currently only works reliable when using jquery.val fn to retrieve the value 
                        return this.data('_inputmask') ? !this.data('_inputmask')['opts'].autoUnmask : false;
                    case "isComplete":
                        masksets = this.data('_inputmask')['masksets'];
                        activeMasksetIndex = this.data('_inputmask')['activeMasksetIndex'];
                        opts = this.data('_inputmask')['opts'];
                        return maskScope(masksets, activeMasksetIndex, opts).isComplete(this[0]._valueGet().split(''));
                    case "getmetadata": //return mask metadata if exists
                        if (this.data('_inputmask')) {
                            masksets = this.data('_inputmask')['masksets'];
                            activeMasksetIndex = this.data('_inputmask')['activeMasksetIndex'];
                            return masksets[activeMasksetIndex]['metadata'];
                        }
                        else return undefined;
                    default:
                        //check if the fn is an alias
                        if (!resolveAlias(fn, options, opts)) {
                            //maybe fn is a mask so we try
                            //set mask
                            opts.mask = fn;
                        }
                        masksets = generateMaskSets(opts);
                        if (masksets.length == 0) { return this; }
                        return this.each(function () {
                            maskScope($.extend(true, {}, masksets), activeMasksetIndex, opts).mask(this);
                        });

                        break;
                }
            } else if (typeof fn == "object") {
                opts = $.extend(true, {}, $.inputmask.defaults, fn);

                resolveAlias(opts.alias, fn, opts); //resolve aliases
                masksets = generateMaskSets(opts);
                if (masksets.length == 0) { return this; }
                return this.each(function () {
                    maskScope($.extend(true, {}, masksets), activeMasksetIndex, opts).mask(this);
                });
            } else if (fn == undefined) {
                //look for data-inputmask atribute - the attribute should only contain optipns
                return this.each(function () {
                    var attrOptions = $(this).attr("data-inputmask");
                    if (attrOptions && attrOptions != "") {
                        try {
                            attrOptions = attrOptions.replace(new RegExp("'", "g"), '"');
                            var dataoptions = $.parseJSON("{" + attrOptions + "}");
                            $.extend(true, dataoptions, options);
                            opts = $.extend(true, {}, $.inputmask.defaults, dataoptions);
                            resolveAlias(opts.alias, dataoptions, opts);
                            opts.alias = undefined;
                            $(this).inputmask(opts);
                        } catch (ex) { } //need a more relax parseJSON
                    }
                });
            }
        };
    }
})(jQuery);
/*
Input Mask plugin extensions
http://github.com/RobinHerbots/jquery.inputmask
Copyright (c) 2010 - 2014 Robin Herbots
Licensed under the MIT license (http://www.opensource.org/licenses/mit-license.php)
Version: 2.4.17

Optional extensions on the jquery.inputmask base
*/
(function ($) {
    //extra definitions
    $.extend($.inputmask.defaults.definitions, {
        'A': {
            validator: "[A-Za-z]",
            cardinality: 1,
            casing: "upper" //auto uppercasing
        },
        '#': {
            validator: "[A-Za-z\u0410-\u044F\u0401\u04510-9]",
            cardinality: 1,
            casing: "upper"
        }
    });
    $.extend($.inputmask.defaults.aliases, {
        'url': {
            mask: "ir",
            placeholder: "",
            separator: "",
            defaultPrefix: "http://",
            regex: {
                urlpre1: new RegExp("[fh]"),
                urlpre2: new RegExp("(ft|ht)"),
                urlpre3: new RegExp("(ftp|htt)"),
                urlpre4: new RegExp("(ftp:|http|ftps)"),
                urlpre5: new RegExp("(ftp:/|ftps:|http:|https)"),
                urlpre6: new RegExp("(ftp://|ftps:/|http:/|https:)"),
                urlpre7: new RegExp("(ftp://|ftps://|http://|https:/)"),
                urlpre8: new RegExp("(ftp://|ftps://|http://|https://)")
            },
            definitions: {
                'i': {
                    validator: function (chrs, buffer, pos, strict, opts) {
                        return true;
                    },
                    cardinality: 8,
                    prevalidator: (function () {
                        var result = [], prefixLimit = 8;
                        for (var i = 0; i < prefixLimit; i++) {
                            result[i] = (function () {
                                var j = i;
                                return {
                                    validator: function (chrs, buffer, pos, strict, opts) {
                                        if (opts.regex["urlpre" + (j + 1)]) {
                                            var tmp = chrs, k;
                                            if (((j + 1) - chrs.length) > 0) {
                                                tmp = buffer.join('').substring(0, ((j + 1) - chrs.length)) + "" + tmp;
                                            }
                                            var isValid = opts.regex["urlpre" + (j + 1)].test(tmp);
                                            if (!strict && !isValid) {
                                                pos = pos - j;
                                                for (k = 0; k < opts.defaultPrefix.length; k++) {
                                                    buffer[pos] = opts.defaultPrefix[k]; pos++;
                                                }
                                                for (k = 0; k < tmp.length - 1; k++) {
                                                    buffer[pos] = tmp[k]; pos++;
                                                }
                                                return { "pos": pos };
                                            }
                                            return isValid;
                                        } else {
                                            return false;
                                        }
                                    }, cardinality: j
                                };
                            })();
                        }
                        return result;
                    })()
                },
                "r": {
                    validator: ".",
                    cardinality: 50
                }
            },
            insertMode: false,
            autoUnmask: false
        },
        "ip": { //ip-address mask
            mask: ["[[x]y]z.[[x]y]z.[[x]y]z.x[yz]", "[[x]y]z.[[x]y]z.[[x]y]z.[[x]y][z]"],
            definitions: {
                'x': {
                    validator: "[012]",
                    cardinality: 1,
                    definitionSymbol: "i"
                },
                'y': {
                    validator: function (chrs, buffer, pos, strict, opts) {
                        if (pos - 1 > -1 && buffer[pos - 1] != ".")
                            chrs = buffer[pos - 1] + chrs;
                        else chrs = "0" + chrs;
                        return new RegExp("2[0-5]|[01][0-9]").test(chrs);
                    },
                    cardinality: 1,
                    definitionSymbol: "i"
                },
                'z': {
                    validator: function (chrs, buffer, pos, strict, opts) {
                        if (pos - 1 > -1 && buffer[pos - 1] != ".") {
                            chrs = buffer[pos - 1] + chrs;
                            if (pos - 2 > -1 && buffer[pos - 2] != ".") {
                                chrs = buffer[pos - 2] + chrs;
                            } else chrs = "0" + chrs;
                        } else chrs = "00" + chrs;
                        return new RegExp("25[0-5]|2[0-4][0-9]|[01][0-9][0-9]").test(chrs);
                    },
                    cardinality: 1,
                    definitionSymbol: "i"
                }
            }
        }
    });
})(jQuery);
/*
Input Mask plugin extensions
http://github.com/RobinHerbots/jquery.inputmask
Copyright (c) 2010 - 2014 Robin Herbots
Licensed under the MIT license (http://www.opensource.org/licenses/mit-license.php)
Version: 2.4.17

Optional extensions on the jquery.inputmask base
*/
(function ($) {
    //date & time aliases
    $.extend($.inputmask.defaults.definitions, {
        'h': { //hours
            validator: "[01][0-9]|2[0-3]",
            cardinality: 2,
            prevalidator: [{ validator: "[0-2]", cardinality: 1 }]
        },
        's': { //seconds || minutes
            validator: "[0-5][0-9]",
            cardinality: 2,
            prevalidator: [{ validator: "[0-5]", cardinality: 1 }]
        },
        'd': { //basic day
            validator: "0[1-9]|[12][0-9]|3[01]",
            cardinality: 2,
            prevalidator: [{ validator: "[0-3]", cardinality: 1 }]
        },
        'm': { //basic month
            validator: "0[1-9]|1[012]",
            cardinality: 2,
            prevalidator: [{ validator: "[01]", cardinality: 1 }]
        },
        'y': { //basic year
            validator: "(19|20)\\d{2}",
            cardinality: 4,
            prevalidator: [
                        { validator: "[12]", cardinality: 1 },
                        { validator: "(19|20)", cardinality: 2 },
                        { validator: "(19|20)\\d", cardinality: 3 }
            ]
        }
    });
    $.extend($.inputmask.defaults.aliases, {
        'dd/mm/yyyy': {
            mask: "1/2/y",
            placeholder: "dd/mm/yyyy",
            regex: {
                val1pre: new RegExp("[0-3]"), //daypre
                val1: new RegExp("0[1-9]|[12][0-9]|3[01]"), //day
                val2pre: function (separator) { var escapedSeparator = $.inputmask.escapeRegex.call(this, separator); return new RegExp("((0[1-9]|[12][0-9]|3[01])" + escapedSeparator + "[01])"); }, //monthpre
                val2: function (separator) { var escapedSeparator = $.inputmask.escapeRegex.call(this, separator); return new RegExp("((0[1-9]|[12][0-9])" + escapedSeparator + "(0[1-9]|1[012]))|(30" + escapedSeparator + "(0[13-9]|1[012]))|(31" + escapedSeparator + "(0[13578]|1[02]))"); }//month
            },
            leapday: "29/02/",
            separator: '/',
            yearrange: { minyear: 1900, maxyear: 2099 },
            isInYearRange: function (chrs, minyear, maxyear) {
                var enteredyear = parseInt(chrs.concat(minyear.toString().slice(chrs.length)));
                var enteredyear2 = parseInt(chrs.concat(maxyear.toString().slice(chrs.length)));
                return (enteredyear != NaN ? minyear <= enteredyear && enteredyear <= maxyear : false) ||
            		   (enteredyear2 != NaN ? minyear <= enteredyear2 && enteredyear2 <= maxyear : false);
            },
            determinebaseyear: function (minyear, maxyear, hint) {
                var currentyear = (new Date()).getFullYear();
                if (minyear > currentyear) return minyear;
                if (maxyear < currentyear) {
                    var maxYearPrefix = maxyear.toString().slice(0, 2);
                    var maxYearPostfix = maxyear.toString().slice(2, 4);
                    while (maxyear < maxYearPrefix + hint) {
                        maxYearPrefix--;
                    }
                    var maxxYear = maxYearPrefix + maxYearPostfix;
                    return minyear > maxxYear ? minyear : maxxYear;
                }

                return currentyear;
            },
            onKeyUp: function (e, buffer, opts) {
                var $input = $(this);
                if (e.ctrlKey && e.keyCode == opts.keyCode.RIGHT) {
                    var today = new Date();
                    $input.val(today.getDate().toString() + (today.getMonth() + 1).toString() + today.getFullYear().toString());
                }
            },
            definitions: {
                '1': { //val1 ~ day or month
                    validator: function (chrs, buffer, pos, strict, opts) {
                        var isValid = opts.regex.val1.test(chrs);
                        if (!strict && !isValid) {
                            if (chrs.charAt(1) == opts.separator || "-./".indexOf(chrs.charAt(1)) != -1) {
                                isValid = opts.regex.val1.test("0" + chrs.charAt(0));
                                if (isValid) {
                                    buffer[pos - 1] = "0";
                                    return { "pos": pos, "c": chrs.charAt(0) };
                                }
                            }
                        }
                        return isValid;
                    },
                    cardinality: 2,
                    prevalidator: [{
                        validator: function (chrs, buffer, pos, strict, opts) {
                            var isValid = opts.regex.val1pre.test(chrs);
                            if (!strict && !isValid) {
                                isValid = opts.regex.val1.test("0" + chrs);
                                if (isValid) {
                                    buffer[pos] = "0";
                                    pos++;
                                    return { "pos": pos };
                                }
                            }
                            return isValid;
                        }, cardinality: 1
                    }]
                },
                '2': { //val2 ~ day or month
                    validator: function (chrs, buffer, pos, strict, opts) {
                        var frontValue = buffer.join('').substr(0, 3);
                        if (frontValue.indexOf(opts.placeholder[0]) != -1) frontValue = "01" + opts.separator;
                        var isValid = opts.regex.val2(opts.separator).test(frontValue + chrs);
                        if (!strict && !isValid) {
                            if (chrs.charAt(1) == opts.separator || "-./".indexOf(chrs.charAt(1)) != -1) {
                                isValid = opts.regex.val2(opts.separator).test(frontValue + "0" + chrs.charAt(0));
                                if (isValid) {
                                    buffer[pos - 1] = "0";
                                    return { "pos": pos, "c": chrs.charAt(0) };
                                }
                            }
                        }
                        return isValid;
                    },
                    cardinality: 2,
                    prevalidator: [{
                        validator: function (chrs, buffer, pos, strict, opts) {
                            var frontValue = buffer.join('').substr(0, 3);
                            if (frontValue.indexOf(opts.placeholder[0]) != -1) frontValue = "01" + opts.separator;
                            var isValid = opts.regex.val2pre(opts.separator).test(frontValue + chrs);
                            if (!strict && !isValid) {
                                isValid = opts.regex.val2(opts.separator).test(frontValue + "0" + chrs);
                                if (isValid) {
                                    buffer[pos] = "0";
                                    pos++;
                                    return { "pos": pos };
                                }
                            }
                            return isValid;
                        }, cardinality: 1
                    }]
                },
                'y': { //year
                    validator: function (chrs, buffer, pos, strict, opts) {
                        if (opts.isInYearRange(chrs, opts.yearrange.minyear, opts.yearrange.maxyear)) {
                            var dayMonthValue = buffer.join('').substr(0, 6);
                            if (dayMonthValue != opts.leapday)
                                return true;
                            else {
                                var year = parseInt(chrs, 10);//detect leap year
                                if (year % 4 === 0)
                                    if (year % 100 === 0)
                                        if (year % 400 === 0)
                                            return true;
                                        else return false;
                                    else return true;
                                else return false;
                            }
                        } else return false;
                    },
                    cardinality: 4,
                    prevalidator: [
                {
                    validator: function (chrs, buffer, pos, strict, opts) {
                        var isValid = opts.isInYearRange(chrs, opts.yearrange.minyear, opts.yearrange.maxyear);
                        if (!strict && !isValid) {
                            var yearPrefix = opts.determinebaseyear(opts.yearrange.minyear, opts.yearrange.maxyear, chrs + "0").toString().slice(0, 1);

                            isValid = opts.isInYearRange(yearPrefix + chrs, opts.yearrange.minyear, opts.yearrange.maxyear);
                            if (isValid) {
                                buffer[pos++] = yearPrefix[0];
                                return { "pos": pos };
                            }
                            yearPrefix = opts.determinebaseyear(opts.yearrange.minyear, opts.yearrange.maxyear, chrs + "0").toString().slice(0, 2);

                            isValid = opts.isInYearRange(yearPrefix + chrs, opts.yearrange.minyear, opts.yearrange.maxyear);
                            if (isValid) {
                                buffer[pos++] = yearPrefix[0];
                                buffer[pos++] = yearPrefix[1];
                                return { "pos": pos };
                            }
                        }
                        return isValid;
                    },
                    cardinality: 1
                },
                {
                    validator: function (chrs, buffer, pos, strict, opts) {
                        var isValid = opts.isInYearRange(chrs, opts.yearrange.minyear, opts.yearrange.maxyear);
                        if (!strict && !isValid) {
                            var yearPrefix = opts.determinebaseyear(opts.yearrange.minyear, opts.yearrange.maxyear, chrs).toString().slice(0, 2);

                            isValid = opts.isInYearRange(chrs[0] + yearPrefix[1] + chrs[1], opts.yearrange.minyear, opts.yearrange.maxyear);
                            if (isValid) {
                                buffer[pos++] = yearPrefix[1];
                                return { "pos": pos };
                            }

                            yearPrefix = opts.determinebaseyear(opts.yearrange.minyear, opts.yearrange.maxyear, chrs).toString().slice(0, 2);
                            if (opts.isInYearRange(yearPrefix + chrs, opts.yearrange.minyear, opts.yearrange.maxyear)) {
                                var dayMonthValue = buffer.join('').substr(0, 6);
                                if (dayMonthValue != opts.leapday)
                                    isValid = true;
                                else {
                                    var year = parseInt(chrs, 10);//detect leap year
                                    if (year % 4 === 0)
                                        if (year % 100 === 0)
                                            if (year % 400 === 0)
                                                isValid = true;
                                            else isValid = false;
                                        else isValid = true;
                                    else isValid = false;
                                }
                            } else isValid = false;
                            if (isValid) {
                                buffer[pos - 1] = yearPrefix[0];
                                buffer[pos++] = yearPrefix[1];
                                buffer[pos++] = chrs[0];
                                return { "pos": pos };
                            }
                        }
                        return isValid;
                    }, cardinality: 2
                },
                {
                    validator: function (chrs, buffer, pos, strict, opts) {
                        return opts.isInYearRange(chrs, opts.yearrange.minyear, opts.yearrange.maxyear);
                    }, cardinality: 3
                }
                    ]
                }
            },
            insertMode: false,
            autoUnmask: false
        },
        'mm/dd/yyyy': {
            placeholder: "mm/dd/yyyy",
            alias: "dd/mm/yyyy", //reuse functionality of dd/mm/yyyy alias
            regex: {
                val2pre: function (separator) { var escapedSeparator = $.inputmask.escapeRegex.call(this, separator); return new RegExp("((0[13-9]|1[012])" + escapedSeparator + "[0-3])|(02" + escapedSeparator + "[0-2])"); }, //daypre
                val2: function (separator) { var escapedSeparator = $.inputmask.escapeRegex.call(this, separator); return new RegExp("((0[1-9]|1[012])" + escapedSeparator + "(0[1-9]|[12][0-9]))|((0[13-9]|1[012])" + escapedSeparator + "30)|((0[13578]|1[02])" + escapedSeparator + "31)"); }, //day
                val1pre: new RegExp("[01]"), //monthpre
                val1: new RegExp("0[1-9]|1[012]") //month
            },
            leapday: "02/29/",
            onKeyUp: function (e, buffer, opts) {
                var $input = $(this);
                if (e.ctrlKey && e.keyCode == opts.keyCode.RIGHT) {
                    var today = new Date();
                    $input.val((today.getMonth() + 1).toString() + today.getDate().toString() + today.getFullYear().toString());
                }
            }
        },
        'yyyy/mm/dd': {
            mask: "y/1/2",
            placeholder: "yyyy/mm/dd",
            alias: "mm/dd/yyyy",
            leapday: "/02/29",
            onKeyUp: function (e, buffer, opts) {
                var $input = $(this);
                if (e.ctrlKey && e.keyCode == opts.keyCode.RIGHT) {
                    var today = new Date();
                    $input.val(today.getFullYear().toString() + (today.getMonth() + 1).toString() + today.getDate().toString());
                }
            },
            definitions: {
                '2': { //val2 ~ day or month
                    validator: function (chrs, buffer, pos, strict, opts) {
                        var frontValue = buffer.join('').substr(5, 3);
                        if (frontValue.indexOf(opts.placeholder[5]) != -1) frontValue = "01" + opts.separator;
                        var isValid = opts.regex.val2(opts.separator).test(frontValue + chrs);
                        if (!strict && !isValid) {
                            if (chrs.charAt(1) == opts.separator || "-./".indexOf(chrs.charAt(1)) != -1) {
                                isValid = opts.regex.val2(opts.separator).test(frontValue + "0" + chrs.charAt(0));
                                if (isValid) {
                                    buffer[pos - 1] = "0";
                                    return { "pos": pos, "c": chrs.charAt(0) };
                                }
                            }
                        }

                        //check leap yeap
                        if (isValid) {
                            var dayMonthValue = buffer.join('').substr(4, 4) + chrs;
                            if (dayMonthValue != opts.leapday)
                                return true;
                            else {
                                var year = parseInt(buffer.join('').substr(0, 4), 10);  //detect leap year
                                if (year % 4 === 0)
                                    if (year % 100 === 0)
                                        if (year % 400 === 0)
                                            return true;
                                        else return false;
                                    else return true;
                                else return false;
                            }
                        }

                        return isValid;
                    },
                    cardinality: 2,
                    prevalidator: [{
                        validator: function (chrs, buffer, pos, strict, opts) {
                            var frontValue = buffer.join('').substr(5, 3);
                            if (frontValue.indexOf(opts.placeholder[5]) != -1) frontValue = "01" + opts.separator;
                            var isValid = opts.regex.val2pre(opts.separator).test(frontValue + chrs);
                            if (!strict && !isValid) {
                                isValid = opts.regex.val2(opts.separator).test(frontValue + "0" + chrs);
                                if (isValid) {
                                    buffer[pos] = "0";
                                    pos++;
                                    return { "pos": pos };
                                }
                            }
                            return isValid;
                        }, cardinality: 1
                    }]
                }
            }
        },
        'dd.mm.yyyy': {
            mask: "1.2.y",
            placeholder: "dd.mm.yyyy",
            leapday: "29.02.",
            separator: '.',
            alias: "dd/mm/yyyy"
        },
        'dd-mm-yyyy': {
            mask: "1-2-y",
            placeholder: "dd-mm-yyyy",
            leapday: "29-02-",
            separator: '-',
            alias: "dd/mm/yyyy"
        },
        'mm.dd.yyyy': {
            mask: "1.2.y",
            placeholder: "mm.dd.yyyy",
            leapday: "02.29.",
            separator: '.',
            alias: "mm/dd/yyyy"
        },
        'mm-dd-yyyy': {
            mask: "1-2-y",
            placeholder: "mm-dd-yyyy",
            leapday: "02-29-",
            separator: '-',
            alias: "mm/dd/yyyy"
        },
        'yyyy.mm.dd': {
            mask: "y.1.2",
            placeholder: "yyyy.mm.dd",
            leapday: ".02.29",
            separator: '.',
            alias: "yyyy/mm/dd"
        },
        'yyyy-mm-dd': {
            mask: "y-1-2",
            placeholder: "yyyy-mm-dd",
            leapday: "-02-29",
            separator: '-',
            alias: "yyyy/mm/dd"
        },
        'datetime': {
            mask: "1/2/y h:s",
            placeholder: "dd/mm/yyyy hh:mm",
            alias: "dd/mm/yyyy",
            regex: {
                hrspre: new RegExp("[012]"), //hours pre
                hrs24: new RegExp("2[0-9]|1[3-9]"),
                hrs: new RegExp("[01][0-9]|2[0-3]"), //hours
                ampm: new RegExp("^[a|p|A|P][m|M]")
            },
            timeseparator: ':',
            hourFormat: "24", // or 12
            definitions: {
                'h': { //hours
                    validator: function (chrs, buffer, pos, strict, opts) {
                        var isValid = opts.regex.hrs.test(chrs);
                        if (!strict && !isValid) {
                            if (chrs.charAt(1) == opts.timeseparator || "-.:".indexOf(chrs.charAt(1)) != -1) {
                                isValid = opts.regex.hrs.test("0" + chrs.charAt(0));
                                if (isValid) {
                                    buffer[pos - 1] = "0";
                                    buffer[pos] = chrs.charAt(0);
                                    pos++;
                                    return { "pos": pos };
                                }
                            }
                        }

                        if (isValid && opts.hourFormat !== "24" && opts.regex.hrs24.test(chrs)) {

                            var tmp = parseInt(chrs, 10);

                            if (tmp == 24) {
                                buffer[pos + 5] = "a";
                                buffer[pos + 6] = "m";
                            } else {
                                buffer[pos + 5] = "p";
                                buffer[pos + 6] = "m";
                            }

                            tmp = tmp - 12;

                            if (tmp < 10) {
                                buffer[pos] = tmp.toString();
                                buffer[pos - 1] = "0";
                            } else {
                                buffer[pos] = tmp.toString().charAt(1);
                                buffer[pos - 1] = tmp.toString().charAt(0);
                            }

                            return { "pos": pos, "c": buffer[pos] };
                        }

                        return isValid;
                    },
                    cardinality: 2,
                    prevalidator: [{
                        validator: function (chrs, buffer, pos, strict, opts) {
                            var isValid = opts.regex.hrspre.test(chrs);
                            if (!strict && !isValid) {
                                isValid = opts.regex.hrs.test("0" + chrs);
                                if (isValid) {
                                    buffer[pos] = "0";
                                    pos++;
                                    return { "pos": pos };
                                }
                            }
                            return isValid;
                        }, cardinality: 1
                    }]
                },
                't': { //am/pm
                    validator: function (chrs, buffer, pos, strict, opts) {
                        return opts.regex.ampm.test(chrs + "m");
                    },
                    casing: "lower",
                    cardinality: 1
                }
            },
            insertMode: false,
            autoUnmask: false
        },
        'datetime12': {
            mask: "1/2/y h:s t\\m",
            placeholder: "dd/mm/yyyy hh:mm xm",
            alias: "datetime",
            hourFormat: "12"
        },
        'hh:mm t': {
            mask: "h:s t\\m",
            placeholder: "hh:mm xm",
            alias: "datetime",
            hourFormat: "12"
        },
        'h:s t': {
            mask: "h:s t\\m",
            placeholder: "hh:mm xm",
            alias: "datetime",
            hourFormat: "12"
        },
        'hh:mm:ss': {
            mask: "h:s:s",
            autoUnmask: false
        },
        'hh:mm': {
            mask: "h:s",
            autoUnmask: false
        },
        'date': {
            alias: "dd/mm/yyyy" // "mm/dd/yyyy"
        },
        'mm/yyyy': {
            mask: "1/y",
            placeholder: "mm/yyyy",
            leapday: "donotuse",
            separator: '/',
            alias: "mm/dd/yyyy"
        }
    });
})(jQuery);
/*
Input Mask plugin extensions
http://github.com/RobinHerbots/jquery.inputmask
Copyright (c) 2010 - 2014 Robin Herbots
Licensed under the MIT license (http://www.opensource.org/licenses/mit-license.php)
Version: 2.4.17

Optional extensions on the jquery.inputmask base
*/
(function ($) {
    //number aliases
    $.extend($.inputmask.defaults.aliases, {
        'decimal': {
            mask: "~",
            placeholder: "",
            repeat: "*",
            greedy: false,
            numericInput: false,
            isNumeric: true,
            digits: "*", //number of fractionalDigits
            groupSeparator: "",//",", // | "."
            radixPoint: ".",
            groupSize: 3,
            autoGroup: false,
            allowPlus: true,
            allowMinus: true,
            //todo
            integerDigits: "*", //number of integerDigits
            defaultValue: "",
            prefix: "",
            suffix: "",

            //todo
            getMaskLength: function (buffer, greedy, repeat, currentBuffer, opts) { //custom getMaskLength to take the groupSeparator into account
                var calculatedLength = buffer.length;

                if (!greedy) {
                    if (repeat == "*") {
                        calculatedLength = currentBuffer.length + 1;
                    } else if (repeat > 1) {
                        calculatedLength += (buffer.length * (repeat - 1));
                    }
                }

                var escapedGroupSeparator = $.inputmask.escapeRegex.call(this, opts.groupSeparator);
                var escapedRadixPoint = $.inputmask.escapeRegex.call(this, opts.radixPoint);
                var currentBufferStr = currentBuffer.join(''), strippedBufferStr = currentBufferStr.replace(new RegExp(escapedGroupSeparator, "g"), "").replace(new RegExp(escapedRadixPoint), ""),
                groupOffset = currentBufferStr.length - strippedBufferStr.length;
                return calculatedLength + groupOffset;
            },
            postFormat: function (buffer, pos, reformatOnly, opts) {
                if (opts.groupSeparator == "") return pos;
                var cbuf = buffer.slice(),
                    radixPos = $.inArray(opts.radixPoint, buffer);
                if (!reformatOnly) {
                    cbuf.splice(pos, 0, "?"); //set position indicator
                }
                var bufVal = cbuf.join('');
                if (opts.autoGroup || (reformatOnly && bufVal.indexOf(opts.groupSeparator) != -1)) {
                    var escapedGroupSeparator = $.inputmask.escapeRegex.call(this, opts.groupSeparator);
                    bufVal = bufVal.replace(new RegExp(escapedGroupSeparator, "g"), '');
                    var radixSplit = bufVal.split(opts.radixPoint);
                    bufVal = radixSplit[0];
                    var reg = new RegExp('([-\+]?[\\d\?]+)([\\d\?]{' + opts.groupSize + '})');
                    while (reg.test(bufVal)) {
                        bufVal = bufVal.replace(reg, '$1' + opts.groupSeparator + '$2');
                        bufVal = bufVal.replace(opts.groupSeparator + opts.groupSeparator, opts.groupSeparator);
                    }
                    if (radixSplit.length > 1)
                        bufVal += opts.radixPoint + radixSplit[1];
                }
                buffer.length = bufVal.length; //align the length
                for (var i = 0, l = bufVal.length; i < l; i++) {
                    buffer[i] = bufVal.charAt(i);
                }
                var newPos = $.inArray("?", buffer);
                if (!reformatOnly) buffer.splice(newPos, 1);

                return reformatOnly ? pos : newPos;
            },
            regex: {
                number: function (opts) {
                    var escapedGroupSeparator = $.inputmask.escapeRegex.call(this, opts.groupSeparator);
                    var escapedRadixPoint = $.inputmask.escapeRegex.call(this, opts.radixPoint);
                    var digitExpression = isNaN(opts.digits) ? opts.digits : '{0,' + opts.digits + '}';
                    var signedExpression = opts.allowPlus || opts.allowMinus ? "[" + (opts.allowPlus ? "\+" : "") + (opts.allowMinus ? "-" : "") + "]?" : "";
                    return new RegExp("^" + signedExpression + "(\\d+|\\d{1," + opts.groupSize + "}((" + escapedGroupSeparator + "\\d{" + opts.groupSize + "})?)+)(" + escapedRadixPoint + "\\d" + digitExpression + ")?$");
                }
            },
            onKeyDown: function (e, buffer, opts) {
                var $input = $(this), input = this;
                if (e.keyCode == opts.keyCode.TAB) {
                    var radixPosition = $.inArray(opts.radixPoint, buffer);
                    if (radixPosition != -1) {
                        var masksets = $input.data('_inputmask')['masksets'];
                        var activeMasksetIndex = $input.data('_inputmask')['activeMasksetIndex'];
                        for (var i = 1; i <= opts.digits && i < opts.getMaskLength(masksets[activeMasksetIndex]["_buffer"], masksets[activeMasksetIndex]["greedy"], masksets[activeMasksetIndex]["repeat"], buffer, opts) ; i++) {
                            if (buffer[radixPosition + i] == undefined || buffer[radixPosition + i] == "") buffer[radixPosition + i] = "0";
                        }
                        input._valueSet(buffer.join(''));
                    }
                } else if (e.keyCode == opts.keyCode.DELETE || e.keyCode == opts.keyCode.BACKSPACE) {
                    opts.postFormat(buffer, 0, true, opts);
                    input._valueSet(buffer.join(''));
                    return true;
                }
            },
            definitions: {
                '~': { //real number
                    validator: function (chrs, buffer, pos, strict, opts) {
                        if (chrs == "") return false;
                        if (!strict && pos <= 1 && buffer[0] === '0' && new RegExp("[\\d-]").test(chrs) && buffer.join('').length == 1) { //handle first char
                            buffer[0] = "";
                            return { "pos": 0 };
                        }

                        var cbuf = strict ? buffer.slice(0, pos) : buffer.slice();

                        cbuf.splice(pos, 0, chrs);
                        var bufferStr = cbuf.join('');

                        //strip groupseparator
                        var escapedGroupSeparator = $.inputmask.escapeRegex.call(this, opts.groupSeparator);
                        bufferStr = bufferStr.replace(new RegExp(escapedGroupSeparator, "g"), '');

                        var isValid = opts.regex.number(opts).test(bufferStr);
                        if (!isValid) {
                            //let's help the regex a bit
                            bufferStr += "0";
                            isValid = opts.regex.number(opts).test(bufferStr);
                            if (!isValid) {
                                //make a valid group
                                var lastGroupSeparator = bufferStr.lastIndexOf(opts.groupSeparator);
                                for (var i = bufferStr.length - lastGroupSeparator; i <= 3; i++) {
                                    bufferStr += "0";
                                }

                                isValid = opts.regex.number(opts).test(bufferStr);
                                if (!isValid && !strict) {
                                    if (chrs == opts.radixPoint) {
                                        isValid = opts.regex.number(opts).test("0" + bufferStr + "0");
                                        if (isValid) {
                                            buffer[pos] = "0";
                                            pos++;
                                            return { "pos": pos };
                                        }
                                    }
                                }
                            }
                        }

                        if (isValid != false && !strict && chrs != opts.radixPoint) {
                            var newPos = opts.postFormat(buffer, pos, false, opts);
                            return { "pos": newPos };
                        }

                        return isValid;
                    },
                    cardinality: 1,
                    prevalidator: null
                }
            },
            insertMode: true,
            autoUnmask: false
        },
        'integer': {
            regex: {
                number: function (opts) {
                    var escapedGroupSeparator = $.inputmask.escapeRegex.call(this, opts.groupSeparator);
                    var signedExpression = opts.allowPlus || opts.allowMinus ? "[" + (opts.allowPlus ? "\+" : "") + (opts.allowMinus ? "-" : "") + "]?" : "";
                    return new RegExp("^" + signedExpression + "(\\d+|\\d{1," + opts.groupSize + "}((" + escapedGroupSeparator + "\\d{" + opts.groupSize + "})?)+)$");
                }
            },
            alias: "decimal"
        }
    });
})(jQuery);
/*
Input Mask plugin extensions
http://github.com/RobinHerbots/jquery.inputmask
Copyright (c) 2010 - 2014 Robin Herbots
Licensed under the MIT license (http://www.opensource.org/licenses/mit-license.php)
Version: 2.4.17

Regex extensions on the jquery.inputmask base
Allows for using regular expressions as a mask
*/
(function ($) {
    $.extend($.inputmask.defaults.aliases, { // $(selector).inputmask("Regex", { regex: "[0-9]*"}
        'Regex': {
            mask: "r",
            greedy: false,
            repeat: "*",
            regex: null,
            regexTokens: null,
            //Thx to https://github.com/slevithan/regex-colorizer for the tokenizer regex
            tokenizer: /\[\^?]?(?:[^\\\]]+|\\[\S\s]?)*]?|\\(?:0(?:[0-3][0-7]{0,2}|[4-7][0-7]?)?|[1-9][0-9]*|x[0-9A-Fa-f]{2}|u[0-9A-Fa-f]{4}|c[A-Za-z]|[\S\s]?)|\((?:\?[:=!]?)?|(?:[?*+]|\{[0-9]+(?:,[0-9]*)?\})\??|[^.?*+^${[()|\\]+|./g,
            quantifierFilter: /[0-9]+[^,]/,
            definitions: {
                'r': {
                    validator: function (chrs, buffer, pos, strict, opts) {
                        function regexToken() {
                            this.matches = [];
                            this.isGroup = false;
                            this.isQuantifier = false;
                            this.isLiteral = false;
                        }
                        function analyseRegex() {
                            var currentToken = new regexToken(), match, m, opengroups = [];

                            opts.regexTokens = [];

                            // The tokenizer regex does most of the tokenization grunt work
                            while (match = opts.tokenizer.exec(opts.regex)) {
                                m = match[0];
                                switch (m.charAt(0)) {
                                    case "[": // Character class
                                    case "\\":  // Escape or backreference
                                        if (opengroups.length > 0) {
                                            opengroups[opengroups.length - 1]["matches"].push(m);
                                        } else {
                                            currentToken.matches.push(m);
                                        }
                                        break;
                                    case "(": // Group opening
                                        if (!currentToken.isGroup && currentToken.matches.length > 0)
                                            opts.regexTokens.push(currentToken);
                                        currentToken = new regexToken();
                                        currentToken.isGroup = true;
                                        opengroups.push(currentToken);
                                        break;
                                    case ")": // Group closing
                                        var groupToken = opengroups.pop();
                                        if (opengroups.length > 0) {
                                            opengroups[opengroups.length - 1]["matches"].push(groupToken);
                                        } else {
                                            opts.regexTokens.push(groupToken);
                                            currentToken = new regexToken();
                                        }
                                        break;
                                    case "{": //Quantifier
                                        var quantifier = new regexToken();
                                        quantifier.isQuantifier = true;
                                        quantifier.matches.push(m);
                                        if (opengroups.length > 0) {
                                            opengroups[opengroups.length - 1]["matches"].push(quantifier);
                                        } else {
                                            currentToken.matches.push(quantifier);
                                        }
                                        break;
                                    default:
                                        // Vertical bar (alternator) 
                                        // ^ or $ anchor
                                        // Dot (.)
                                        // Literal character sequence
                                        var literal = new regexToken();
                                        literal.isLiteral = true;
                                        literal.matches.push(m);
                                        if (opengroups.length > 0) {
                                            opengroups[opengroups.length - 1]["matches"].push(literal);
                                        } else {
                                            currentToken.matches.push(literal);
                                        }
                                }
                            }

                            if (currentToken.matches.length > 0)
                                opts.regexTokens.push(currentToken);
                        };

                        function validateRegexToken(token, fromGroup) {
                            var isvalid = false;
                            if (fromGroup) {
                                regexPart += "(";
                                openGroupCount++;
                            }
                            for (var mndx = 0; mndx < token["matches"].length; mndx++) {
                                var matchToken = token["matches"][mndx];
                                if (matchToken["isGroup"] == true) {
                                    isvalid = validateRegexToken(matchToken, true);
                                } else if (matchToken["isQuantifier"] == true) {
                                    matchToken = matchToken["matches"][0];
                                    var quantifierMax = opts.quantifierFilter.exec(matchToken)[0].replace("}", "");
                                    var testExp = regexPart + "{1," + quantifierMax + "}"; //relax quantifier validation
                                    for (var j = 0; j < openGroupCount; j++) {
                                        testExp += ")";
                                    }
                                    var exp = new RegExp("^(" + testExp + ")$");
                                    isvalid = exp.test(bufferStr);
                                    regexPart += matchToken;
                                } else if (matchToken["isLiteral"] == true) {
                                    matchToken = matchToken["matches"][0];
                                    var testExp = regexPart, openGroupCloser = "";
                                    for (var j = 0; j < openGroupCount; j++) {
                                        openGroupCloser += ")";
                                    }
                                    for (var k = 0; k < matchToken.length; k++) { //relax literal validation
                                        testExp = (testExp + matchToken[k]).replace(/\|$/, "");
                                        var exp = new RegExp("^(" + testExp + openGroupCloser + ")$");
                                        isvalid = exp.test(bufferStr);
                                        if (isvalid) break;
                                    }
                                    regexPart += matchToken;
                                    //console.log(bufferStr + " " + exp + " " + isvalid);
                                } else {
                                    regexPart += matchToken;
                                    var testExp = regexPart.replace(/\|$/, "");
                                    for (var j = 0; j < openGroupCount; j++) {
                                        testExp += ")";
                                    }
                                    var exp = new RegExp("^(" + testExp + ")$");
                                    isvalid = exp.test(bufferStr);
                                    //console.log(bufferStr + " " + exp + " " + isvalid);
                                }
                                if (isvalid) break;
                            }

                            if (fromGroup) {
                                regexPart += ")";
                                openGroupCount--;
                            }

                            return isvalid;
                        }


                        if (opts.regexTokens == null) {
                            analyseRegex();
                        }

                        var cbuffer = buffer.slice(), regexPart = "", isValid = false, openGroupCount = 0;
                        cbuffer.splice(pos, 0, chrs);
                        var bufferStr = cbuffer.join('');
                        for (var i = 0; i < opts.regexTokens.length; i++) {
                            var regexToken = opts.regexTokens[i];
                            isValid = validateRegexToken(regexToken, regexToken["isGroup"]);
                            if (isValid) break;
                        }

                        return isValid;
                    },
                    cardinality: 1
                }
            }
        }
    });
})(jQuery);
/*
Input Mask plugin extensions
http://github.com/RobinHerbots/jquery.inputmask
Copyright (c) 2010 - 2014 Robin Herbots
Licensed under the MIT license (http://www.opensource.org/licenses/mit-license.php)
Version: 2.4.17

Phone extension.
When using this extension make sure you specify the correct url to get the masks

 $(selector).inputmask("phone", {
                url: "Scripts/jquery.inputmask/phone-codes/phone-codes.json", 
                onKeyValidation: function () { //show some metadata in the console
                    console.log($(this).inputmask("getmetadata")["name_en"]);
                } 
  });


*/
(function ($) {
    $.extend($.inputmask.defaults.aliases, {
        'phone': {
            url: "phone-codes/phone-codes.json",
            mask: function (opts) {
                opts.definitions = {
                    'p': {
                        validator: function () { return false; },
                        cardinality: 1
                    },
                    '#': {
                        validator: "[0-9]",
                        cardinality: 1
                    }
                };
                var maskList = [];
                $.ajax({
                    url: opts.url,
                    async: false,
                    dataType: 'json',
                    success: function (response) {
                        maskList = response;
                    }
                });
    
                maskList.splice(0, 0, "+p(ppp)ppp-pppp");
                return maskList;
            }
        }
    });
})(jQuery);

/**
 * Handsontable 0.10.3
 * Handsontable is a simple jQuery plugin for editable tables with basic copy-paste compatibility with Excel and Google Docs
 *
 * Copyright 2012, Marcin Warpechowski
 * Licensed under the MIT license.
 * http://handsontable.com/
 *
 * Date: Mon Feb 10 2014 14:15:11 GMT+0100 (CET)
 */
/*jslint white: true, browser: true, plusplus: true, indent: 4, maxerr: 50 */

var Handsontable = { //class namespace
  extension: {}, //extenstion namespace
  helper: {} //helper namespace
};

(function ($, window, Handsontable) {
  "use strict";
//http://stackoverflow.com/questions/3629183/why-doesnt-indexof-work-on-an-array-ie8
if (!Array.prototype.indexOf) {
  Array.prototype.indexOf = function (elt /*, from*/) {
    var len = this.length >>> 0;

    var from = Number(arguments[1]) || 0;
    from = (from < 0)
      ? Math.ceil(from)
      : Math.floor(from);
    if (from < 0)
      from += len;

    for (; from < len; from++) {
      if (from in this &&
        this[from] === elt)
        return from;
    }
    return -1;
  };
}
/**
 * Array.filter() shim by Trevor Menagh (https://github.com/trevmex) with some modifications
 */

if (!Array.prototype.filter) {
  Array.prototype.filter = function (fun, thisp) {
    "use strict";

    if (typeof this === "undefined" || this === null) {
      throw new TypeError();
    }
    if (typeof fun !== "function") {
      throw new TypeError();
    }

    thisp = thisp || this;

    if (isNodeList(thisp)) {
      thisp = convertNodeListToArray(thisp);
    }

    var len = thisp.length,
      res = [],
      i,
      val;

    for (i = 0; i < len; i += 1) {
      if (thisp.hasOwnProperty(i)) {
        val = thisp[i]; // in case fun mutates this
        if (fun.call(thisp, val, i, thisp)) {
          res.push(val);
        }
      }
    }

    return res;

    function isNodeList(object) {
      return /NodeList/i.test(object.item);
    }

    function convertNodeListToArray(nodeList) {
      var array = [];

      for (var i = 0, len = nodeList.length; i < len; i++){
        array[i] = nodeList[i]
      }

      return array;
    }
  };
}

/*
 * Copyright 2012 The Polymer Authors. All rights reserved.
 * Use of this source code is governed by a BSD-style
 * license that can be found in the LICENSE file.
 */

if (typeof WeakMap === 'undefined') {
  (function() {
    var defineProperty = Object.defineProperty;

    try {
      var properDefineProperty = true;
      defineProperty(function(){}, 'foo', {});
    } catch (e) {
      properDefineProperty = false;
    }

    /*
      IE8 does not support Date.now() but IE8 compatibility mode in IE9 and IE10 does.
      M$ deserves a high five for this one :)
     */
    var counter = +(new Date) % 1e9;

    var WeakMap = function() {
      this.name = '__st' + (Math.random() * 1e9 >>> 0) + (counter++ + '__');
      if(!properDefineProperty){
        this._wmCache = [];
      }
    };

    if(properDefineProperty){
      WeakMap.prototype = {
        set: function(key, value) {
          var entry = key[this.name];
          if (entry && entry[0] === key)
            entry[1] = value;
          else
            defineProperty(key, this.name, {value: [key, value], writable: true});

        },
        get: function(key) {
          var entry;
          return (entry = key[this.name]) && entry[0] === key ?
            entry[1] : undefined;
        },
        'delete': function(key) {
          this.set(key, undefined);
        }
      };
    } else {
      WeakMap.prototype = {
        set: function(key, value) {

          if(typeof key == 'undefined' || typeof value == 'undefined') return;

          for(var i = 0, len = this._wmCache.length; i < len; i++){
            if(this._wmCache[i].key == key){
              this._wmCache[i].value = value;
              return;
            }
          }

          this._wmCache.push({key: key, value: value});

        },
        get: function(key) {

          if(typeof key == 'undefined') return;

          for(var i = 0, len = this._wmCache.length; i < len; i++){
            if(this._wmCache[i].key == key){
              return  this._wmCache[i].value;
            }
          }

          return;

        },
        'delete': function(key) {

          if(typeof key == 'undefined') return;

          for(var i = 0, len = this._wmCache.length; i < len; i++){
            if(this._wmCache[i].key == key){
              Array.prototype.slice.call(this._wmCache, i, 1);
            }
          }
        }
      };
    }

    window.WeakMap = WeakMap;
  })();
}

Handsontable.activeGuid = null;

/**
 * Handsontable constructor
 * @param rootElement The jQuery element in which Handsontable DOM will be inserted
 * @param userSettings
 * @constructor
 */
Handsontable.Core = function (rootElement, userSettings) {
  var priv
    , datamap
    , grid
    , selection
    , editorManager
    , autofill
    , instance = this
    , GridSettings = function () {};

  Handsontable.helper.extend(GridSettings.prototype, DefaultSettings.prototype); //create grid settings as a copy of default settings
  Handsontable.helper.extend(GridSettings.prototype, userSettings); //overwrite defaults with user settings
  Handsontable.helper.extend(GridSettings.prototype, expandType(userSettings));

  this.rootElement = rootElement;
  var $document = $(document.documentElement);
  var $body = $(document.body);
  this.guid = 'ht_' + Handsontable.helper.randomString(); //this is the namespace for global events

  if (!this.rootElement[0].id) {
    this.rootElement[0].id = this.guid; //if root element does not have an id, assign a random id
  }

  priv = {
    cellSettings: [],
    columnSettings: [],
    columnsSettingConflicts: ['data', 'width'],
    settings: new GridSettings(), // current settings instance
    settingsFromDOM: {},
    selStart: new Handsontable.SelectionPoint(),
    selEnd: new Handsontable.SelectionPoint(),
    isPopulated: null,
    scrollable: null,
    extensions: {},
    firstRun: true
  };

  grid = {
    /**
     * Inserts or removes rows and columns
     * @param {String} action Possible values: "insert_row", "insert_col", "remove_row", "remove_col"
     * @param {Number} index
     * @param {Number} amount
     * @param {String} [source] Optional. Source of hook runner.
     * @param {Boolean} [keepEmptyRows] Optional. Flag for preventing deletion of empty rows.
     */
    alter: function (action, index, amount, source, keepEmptyRows) {
      var delta;

      amount = amount || 1;

      switch (action) {
        case "insert_row":
          delta = datamap.createRow(index, amount);

          if (delta) {
            if (priv.selStart.exists() && priv.selStart.row() >= index) {
              priv.selStart.row(priv.selStart.row() + delta);
              selection.transformEnd(delta, 0); //will call render() internally
            }
            else {
              selection.refreshBorders(); //it will call render and prepare methods
            }
          }
          break;

        case "insert_col":
          delta = datamap.createCol(index, amount);

          if (delta) {

            if(Handsontable.helper.isArray(instance.getSettings().colHeaders)){
              var spliceArray = [index, 0];
              spliceArray.length += delta; //inserts empty (undefined) elements at the end of an array
              Array.prototype.splice.apply(instance.getSettings().colHeaders, spliceArray); //inserts empty (undefined) elements into the colHeader array
            }

            if (priv.selStart.exists() && priv.selStart.col() >= index) {
              priv.selStart.col(priv.selStart.col() + delta);
              selection.transformEnd(0, delta); //will call render() internally
            }
            else {
              selection.refreshBorders(); //it will call render and prepare methods
            }
          }
          break;

        case "remove_row":
          datamap.removeRow(index, amount);
          priv.cellSettings.splice(index, amount);
          grid.adjustRowsAndCols();
          selection.refreshBorders(); //it will call render and prepare methods
          break;

        case "remove_col":
          datamap.removeCol(index, amount);

          for(var row = 0, len = datamap.getAll().length; row < len; row++){
            if(row in priv.cellSettings){  //if row hasn't been rendered it wouldn't have cellSettings
              priv.cellSettings[row].splice(index, amount);
            }
          }

          if(Handsontable.helper.isArray(instance.getSettings().colHeaders)){
            if(typeof index == 'undefined'){
              index = -1;
            }
            instance.getSettings().colHeaders.splice(index, amount);
          }

          priv.columnSettings.splice(index, amount);

          grid.adjustRowsAndCols();
          selection.refreshBorders(); //it will call render and prepare methods
          break;

        default:
          throw new Error('There is no such action "' + action + '"');
          break;
      }

      if (!keepEmptyRows) {
        grid.adjustRowsAndCols(); //makes sure that we did not add rows that will be removed in next refresh
      }
    },

    /**
     * Makes sure there are empty rows at the bottom of the table
     */
    adjustRowsAndCols: function () {
      var r, rlen, emptyRows = instance.countEmptyRows(true), emptyCols;

      //should I add empty rows to data source to meet minRows?
      rlen = instance.countRows();
      if (rlen < priv.settings.minRows) {
        for (r = 0; r < priv.settings.minRows - rlen; r++) {
          datamap.createRow(instance.countRows(), 1, true);
        }
      }

      //should I add empty rows to meet minSpareRows?
      if (emptyRows < priv.settings.minSpareRows) {
        for (; emptyRows < priv.settings.minSpareRows && instance.countRows() < priv.settings.maxRows; emptyRows++) {
          datamap.createRow(instance.countRows(), 1, true);
        }
      }

      //count currently empty cols
      emptyCols = instance.countEmptyCols(true);

      //should I add empty cols to meet minCols?
      if (!priv.settings.columns && instance.countCols() < priv.settings.minCols) {
        for (; instance.countCols() < priv.settings.minCols; emptyCols++) {
          datamap.createCol(instance.countCols(), 1, true);
        }
      }

      //should I add empty cols to meet minSpareCols?
      if (!priv.settings.columns && instance.dataType === 'array' && emptyCols < priv.settings.minSpareCols) {
        for (; emptyCols < priv.settings.minSpareCols && instance.countCols() < priv.settings.maxCols; emptyCols++) {
          datamap.createCol(instance.countCols(), 1, true);
        }
      }

      if (priv.settings.enterBeginsEditing) {
        for (; (((priv.settings.minRows || priv.settings.minSpareRows) && instance.countRows() > priv.settings.minRows) && (priv.settings.minSpareRows && emptyRows > priv.settings.minSpareRows)); emptyRows--) {
          datamap.removeRow();
        }
      }

      if (priv.settings.enterBeginsEditing && !priv.settings.columns) {
        for (; (((priv.settings.minCols || priv.settings.minSpareCols) && instance.countCols() > priv.settings.minCols) && (priv.settings.minSpareCols && emptyCols > priv.settings.minSpareCols)); emptyCols--) {
          datamap.removeCol();
        }
      }

      var rowCount = instance.countRows();
      var colCount = instance.countCols();

      if (rowCount === 0 || colCount === 0) {
        selection.deselect();
      }

      if (priv.selStart.exists()) {
        var selectionChanged;
        var fromRow = priv.selStart.row();
        var fromCol = priv.selStart.col();
        var toRow = priv.selEnd.row();
        var toCol = priv.selEnd.col();

        //if selection is outside, move selection to last row
        if (fromRow > rowCount - 1) {
          fromRow = rowCount - 1;
          selectionChanged = true;
          if (toRow > fromRow) {
            toRow = fromRow;
          }
        } else if (toRow > rowCount - 1) {
          toRow = rowCount - 1;
          selectionChanged = true;
          if (fromRow > toRow) {
            fromRow = toRow;
          }
        }

        //if selection is outside, move selection to last row
        if (fromCol > colCount - 1) {
          fromCol = colCount - 1;
          selectionChanged = true;
          if (toCol > fromCol) {
            toCol = fromCol;
          }
        } else if (toCol > colCount - 1) {
          toCol = colCount - 1;
          selectionChanged = true;
          if (fromCol > toCol) {
            fromCol = toCol;
          }
        }

        if (selectionChanged) {
          instance.selectCell(fromRow, fromCol, toRow, toCol);
        }
      }
    },

    /**
     * Populate cells at position with 2d array
     * @param {Object} start Start selection position
     * @param {Array} input 2d array
     * @param {Object} [end] End selection position (only for drag-down mode)
     * @param {String} [source="populateFromArray"]
     * @param {String} [method="overwrite"]
     * @return {Object|undefined} ending td in pasted area (only if any cell was changed)
     */
    populateFromArray: function (start, input, end, source, method) {
      var r, rlen, c, clen, setData = [], current = {};
      rlen = input.length;
      if (rlen === 0) {
        return false;
      }

      var repeatCol
        , repeatRow
        , cmax
        , rmax;

      // insert data with specified pasteMode method
      switch (method) {
        case 'shift_down' :
          repeatCol = end ? end.col - start.col + 1 : 0;
          repeatRow = end ? end.row - start.row + 1 : 0;
          input = Handsontable.helper.translateRowsToColumns(input);
          for (c = 0, clen = input.length, cmax = Math.max(clen, repeatCol); c < cmax; c++) {
            if (c < clen) {
              for (r = 0, rlen = input[c].length; r < repeatRow - rlen; r++) {
                input[c].push(input[c][r % rlen]);
              }
              input[c].unshift(start.col + c, start.row, 0);
              instance.spliceCol.apply(instance, input[c]);
            }
            else {
              input[c % clen][0] = start.col + c;
              instance.spliceCol.apply(instance, input[c % clen]);
            }
          }
          break;

        case 'shift_right' :
          repeatCol = end ? end.col - start.col + 1 : 0;
          repeatRow = end ? end.row - start.row + 1 : 0;
          for (r = 0, rlen = input.length, rmax = Math.max(rlen, repeatRow); r < rmax; r++) {
            if (r < rlen) {
              for (c = 0, clen = input[r].length; c < repeatCol - clen; c++) {
                input[r].push(input[r][c % clen]);
              }
              input[r].unshift(start.row + r, start.col, 0);
              instance.spliceRow.apply(instance, input[r]);
            }
            else {
              input[r % rlen][0] = start.row + r;
              instance.spliceRow.apply(instance, input[r % rlen]);
            }
          }
          break;

        case 'overwrite' :
        default:
          // overwrite and other not specified options
          current.row = start.row;
          current.col = start.col;
          for (r = 0; r < rlen; r++) {
            if ((end && current.row > end.row) || (!priv.settings.minSpareRows && current.row > instance.countRows() - 1) || (current.row >= priv.settings.maxRows)) {
              break;
            }
            current.col = start.col;
            clen = input[r] ? input[r].length : 0;
            for (c = 0; c < clen; c++) {
              if ((end && current.col > end.col) || (!priv.settings.minSpareCols && current.col > instance.countCols() - 1) || (current.col >= priv.settings.maxCols)) {
                break;
              }
              if (!instance.getCellMeta(current.row, current.col).readOnly) {
                setData.push([current.row, current.col, input[r][c]]);
              }
              current.col++;
              if (end && c === clen - 1) {
                c = -1;
              }
            }
            current.row++;
            if (end && r === rlen - 1) {
              r = -1;
            }
          }
          instance.setDataAtCell(setData, null, null, source || 'populateFromArray');
          break;
      }
    },

    /**
     * Returns the top left (TL) and bottom right (BR) selection coordinates
     * @param {Object[]} coordsArr
     * @returns {Object}
     */
    getCornerCoords: function (coordsArr) {
      function mapProp(func, array, prop) {
        function getProp(el) {
          return el[prop];
        }

        if (Array.prototype.map) {
          return func.apply(Math, array.map(getProp));
        }
        return func.apply(Math, $.map(array, getProp));
      }

      return {
        TL: {
          row: mapProp(Math.min, coordsArr, "row"),
          col: mapProp(Math.min, coordsArr, "col")
        },
        BR: {
          row: mapProp(Math.max, coordsArr, "row"),
          col: mapProp(Math.max, coordsArr, "col")
        }
      };
    },

    /**
     * Returns array of td objects given start and end coordinates
     */
    getCellsAtCoords: function (start, end) {
      var corners = grid.getCornerCoords([start, end]);
      var r, c, output = [];
      for (r = corners.TL.row; r <= corners.BR.row; r++) {
        for (c = corners.TL.col; c <= corners.BR.col; c++) {
          output.push(instance.view.getCellAtCoords({
            row: r,
            col: c
          }));
        }
      }
      return output;
    }
  };

  this.selection = selection = { //this public assignment is only temporary
    inProgress: false,

    /**
     * Sets inProgress to true. This enables onSelectionEnd and onSelectionEndByProp to function as desired
     */
    begin: function () {
      instance.selection.inProgress = true;
    },

    /**
     * Sets inProgress to false. Triggers onSelectionEnd and onSelectionEndByProp
     */
    finish: function () {
      var sel = instance.getSelected();
      instance.PluginHooks.run("afterSelectionEnd", sel[0], sel[1], sel[2], sel[3]);
      instance.PluginHooks.run("afterSelectionEndByProp", sel[0], instance.colToProp(sel[1]), sel[2], instance.colToProp(sel[3]));
      instance.selection.inProgress = false;
    },

    isInProgress: function () {
      return instance.selection.inProgress;
    },

    /**
     * Starts selection range on given td object
     * @param {Object} coords
     */
    setRangeStart: function (coords) {
      priv.selStart.coords(coords);
      selection.setRangeEnd(coords);
    },

    /**
     * Ends selection range on given td object
     * @param {Object} coords
     * @param {Boolean} [scrollToCell=true] If true, viewport will be scrolled to range end
     */
    setRangeEnd: function (coords, scrollToCell) {
      instance.selection.begin();

      priv.selEnd.coords(coords);
      if (!priv.settings.multiSelect) {
        priv.selStart.coords(coords);
      }

      //set up current selection
      instance.view.wt.selections.current.clear();
      instance.view.wt.selections.current.add(priv.selStart.arr());

      //set up area selection
      instance.view.wt.selections.area.clear();
      if (selection.isMultiple()) {
        instance.view.wt.selections.area.add(priv.selStart.arr());
        instance.view.wt.selections.area.add(priv.selEnd.arr());
      }

      //set up highlight
      if (priv.settings.currentRowClassName || priv.settings.currentColClassName) {
        instance.view.wt.selections.highlight.clear();
        instance.view.wt.selections.highlight.add(priv.selStart.arr());
        instance.view.wt.selections.highlight.add(priv.selEnd.arr());
      }

      //trigger handlers
      instance.PluginHooks.run("afterSelection", priv.selStart.row(), priv.selStart.col(), priv.selEnd.row(), priv.selEnd.col());
      instance.PluginHooks.run("afterSelectionByProp", priv.selStart.row(), datamap.colToProp(priv.selStart.col()), priv.selEnd.row(), datamap.colToProp(priv.selEnd.col()));

      if (scrollToCell !== false) {
        instance.view.scrollViewport(coords);
      }
      selection.refreshBorders();
    },

    /**
     * Destroys editor, redraws borders around cells, prepares editor
     * @param {Boolean} revertOriginal
     * @param {Boolean} keepEditor
     */
    refreshBorders: function (revertOriginal, keepEditor) {
      if (!keepEditor) {
        editorManager.destroyEditor(revertOriginal);
      }
      instance.view.render();
      if (selection.isSelected() && !keepEditor) {
        editorManager.prepareEditor();
      }
    },

    /**
     * Returns information if we have a multiselection
     * @return {Boolean}
     */
    isMultiple: function () {
      return !(priv.selEnd.col() === priv.selStart.col() && priv.selEnd.row() === priv.selStart.row());
    },

    /**
     * Selects cell relative to current cell (if possible)
     */
    transformStart: function (rowDelta, colDelta, force) {
      if (priv.selStart.row() + rowDelta > instance.countRows() - 1) {
        if (force && priv.settings.minSpareRows > 0) {
          instance.alter("insert_row", instance.countRows());
        }
        else if (priv.settings.autoWrapCol) {
          rowDelta = 1 - instance.countRows();
          colDelta = priv.selStart.col() + colDelta == instance.countCols() - 1 ? 1 - instance.countCols() : 1;
        }
      }
      else if (priv.settings.autoWrapCol && priv.selStart.row() + rowDelta < 0 && priv.selStart.col() + colDelta >= 0) {
        rowDelta = instance.countRows() - 1;
        colDelta = priv.selStart.col() + colDelta == 0 ? instance.countCols() - 1 : -1;
      }

      if (priv.selStart.col() + colDelta > instance.countCols() - 1) {
        if (force && priv.settings.minSpareCols > 0) {
          instance.alter("insert_col", instance.countCols());
        }
        else if (priv.settings.autoWrapRow) {
          rowDelta = priv.selStart.row() + rowDelta == instance.countRows() - 1 ? 1 - instance.countRows() : 1;
          colDelta = 1 - instance.countCols();
        }
      }
      else if (priv.settings.autoWrapRow && priv.selStart.col() + colDelta < 0 && priv.selStart.row() + rowDelta >= 0) {
        rowDelta = priv.selStart.row() + rowDelta == 0 ? instance.countRows() - 1 : -1;
        colDelta = instance.countCols() - 1;
      }

      var totalRows = instance.countRows();
      var totalCols = instance.countCols();
      var coords = {
        row: priv.selStart.row() + rowDelta,
        col: priv.selStart.col() + colDelta
      };

      if (coords.row < 0) {
        coords.row = 0;
      }
      else if (coords.row > 0 && coords.row >= totalRows) {
        coords.row = totalRows - 1;
      }

      if (coords.col < 0) {
        coords.col = 0;
      }
      else if (coords.col > 0 && coords.col >= totalCols) {
        coords.col = totalCols - 1;
      }

      selection.setRangeStart(coords);
    },

    /**
     * Sets selection end cell relative to current selection end cell (if possible)
     */
    transformEnd: function (rowDelta, colDelta) {
      if (priv.selEnd.exists()) {
        var totalRows = instance.countRows();
        var totalCols = instance.countCols();
        var coords = {
          row: priv.selEnd.row() + rowDelta,
          col: priv.selEnd.col() + colDelta
        };

        if (coords.row < 0) {
          coords.row = 0;
        }
        else if (coords.row > 0 && coords.row >= totalRows) {
          coords.row = totalRows - 1;
        }

        if (coords.col < 0) {
          coords.col = 0;
        }
        else if (coords.col > 0 && coords.col >= totalCols) {
          coords.col = totalCols - 1;
        }

        selection.setRangeEnd(coords);
      }
    },

    /**
     * Returns true if currently there is a selection on screen, false otherwise
     * @return {Boolean}
     */
    isSelected: function () {
      return priv.selEnd.exists();
    },

    /**
     * Returns true if coords is within current selection coords
     * @return {Boolean}
     */
    inInSelection: function (coords) {
      if (!selection.isSelected()) {
        return false;
      }
      var sel = grid.getCornerCoords([priv.selStart.coords(), priv.selEnd.coords()]);
      return (sel.TL.row <= coords.row && sel.BR.row >= coords.row && sel.TL.col <= coords.col && sel.BR.col >= coords.col);
    },

    /**
     * Deselects all selected cells
     */
    deselect: function () {
      if (!selection.isSelected()) {
        return;
      }
      instance.selection.inProgress = false; //needed by HT inception
      priv.selEnd = new Handsontable.SelectionPoint(); //create new empty point to remove the existing one
      instance.view.wt.selections.current.clear();
      instance.view.wt.selections.area.clear();
      editorManager.destroyEditor();
      selection.refreshBorders();
      instance.PluginHooks.run('afterDeselect');
    },

    /**
     * Select all cells
     */
    selectAll: function () {
      if (!priv.settings.multiSelect) {
        return;
      }
      selection.setRangeStart({
        row: 0,
        col: 0
      });
      selection.setRangeEnd({
        row: instance.countRows() - 1,
        col: instance.countCols() - 1
      }, false);
    },

    /**
     * Deletes data from selected cells
     */
    empty: function () {
      if (!selection.isSelected()) {
        return;
      }
      var corners = grid.getCornerCoords([priv.selStart.coords(), priv.selEnd.coords()]);
      var r, c, changes = [];
      for (r = corners.TL.row; r <= corners.BR.row; r++) {
        for (c = corners.TL.col; c <= corners.BR.col; c++) {
          if (!instance.getCellMeta(r, c).readOnly) {
            changes.push([r, c, '']);
          }
        }
      }
      instance.setDataAtCell(changes);
    }
  };

  this.autofill = autofill = { //this public assignment is only temporary
    handle: null,

    /**
     * Create fill handle and fill border objects
     */
    init: function () {
      if (!autofill.handle) {
        autofill.handle = {};
      }
      else {
        autofill.handle.disabled = false;
      }
    },

    /**
     * Hide fill handle and fill border permanently
     */
    disable: function () {
      autofill.handle.disabled = true;
    },

    /**
     * Selects cells down to the last row in the left column, then fills down to that cell
     */
    selectAdjacent: function () {
      var select, data, r, maxR, c;

      if (selection.isMultiple()) {
        select = instance.view.wt.selections.area.getCorners();
      }
      else {
        select = instance.view.wt.selections.current.getCorners();
      }

      data = datamap.getAll();
      rows : for (r = select[2] + 1; r < instance.countRows(); r++) {
        for (c = select[1]; c <= select[3]; c++) {
          if (data[r][c]) {
            break rows;
          }
        }
        if (!!data[r][select[1] - 1] || !!data[r][select[3] + 1]) {
          maxR = r;
        }
      }
      if (maxR) {
        instance.view.wt.selections.fill.clear();
        instance.view.wt.selections.fill.add([select[0], select[1]]);
        instance.view.wt.selections.fill.add([maxR, select[3]]);
        autofill.apply();
      }
    },

    /**
     * Apply fill values to the area in fill border, omitting the selection border
     */
    apply: function () {
      var drag, select, start, end, _data;

      autofill.handle.isDragged = 0;

      drag = instance.view.wt.selections.fill.getCorners();
      if (!drag) {
        return;
      }

      instance.view.wt.selections.fill.clear();

      if (selection.isMultiple()) {
        select = instance.view.wt.selections.area.getCorners();
      }
      else {
        select = instance.view.wt.selections.current.getCorners();
      }

      if (drag[0] === select[0] && drag[1] < select[1]) {
        start = {
          row: drag[0],
          col: drag[1]
        };
        end = {
          row: drag[2],
          col: select[1] - 1
        };
      }
      else if (drag[0] === select[0] && drag[3] > select[3]) {
        start = {
          row: drag[0],
          col: select[3] + 1
        };
        end = {
          row: drag[2],
          col: drag[3]
        };
      }
      else if (drag[0] < select[0] && drag[1] === select[1]) {
        start = {
          row: drag[0],
          col: drag[1]
        };
        end = {
          row: select[0] - 1,
          col: drag[3]
        };
      }
      else if (drag[2] > select[2] && drag[1] === select[1]) {
        start = {
          row: select[2] + 1,
          col: drag[1]
        };
        end = {
          row: drag[2],
          col: drag[3]
        };
      }

      if (start) {

        _data = SheetClip.parse(datamap.getText(priv.selStart.coords(), priv.selEnd.coords()));
        instance.PluginHooks.run('beforeAutofill', start, end, _data);

        grid.populateFromArray(start, _data, end, 'autofill');

        selection.setRangeStart({row: drag[0], col: drag[1]});
        selection.setRangeEnd({row: drag[2], col: drag[3]});
      }
      /*else {
       //reset to avoid some range bug
       selection.refreshBorders();
       }*/
    },

    /**
     * Show fill border
     */
    showBorder: function (coords) {
      coords.row = coords[0];
      coords.col = coords[1];

      var corners = grid.getCornerCoords([priv.selStart.coords(), priv.selEnd.coords()]);
      if (priv.settings.fillHandle !== 'horizontal' && (corners.BR.row < coords.row || corners.TL.row > coords.row)) {
        coords = [coords.row, corners.BR.col];
      }
      else if (priv.settings.fillHandle !== 'vertical') {
        coords = [corners.BR.row, coords.col];
      }
      else {
        return; //wrong direction
      }

      instance.view.wt.selections.fill.clear();
      instance.view.wt.selections.fill.add([priv.selStart.coords().row, priv.selStart.coords().col]);
      instance.view.wt.selections.fill.add([priv.selEnd.coords().row, priv.selEnd.coords().col]);
      instance.view.wt.selections.fill.add(coords);
      instance.view.render();
    }
  };

  this.init = function () {
    instance.PluginHooks.run('beforeInit');

    this.view = new Handsontable.TableView(this);
    editorManager = new Handsontable.EditorManager(instance, priv, selection, datamap);

    this.updateSettings(priv.settings, true);
    this.parseSettingsFromDOM();


    this.forceFullRender = true; //used when data was changed
    this.view.render();

    if (typeof priv.firstRun === 'object') {
      instance.PluginHooks.run('afterChange', priv.firstRun[0], priv.firstRun[1]);
      priv.firstRun = false;
    }
    instance.PluginHooks.run('afterInit');
  };

  function ValidatorsQueue() { //moved this one level up so it can be used in any function here. Probably this should be moved to a separate file
    var resolved = false;

    return {
      validatorsInQueue: 0,
      addValidatorToQueue: function () {
        this.validatorsInQueue++;
        resolved = false;
      },
      removeValidatorFormQueue: function () {
        this.validatorsInQueue = this.validatorsInQueue - 1 < 0 ? 0 : this.validatorsInQueue - 1;
        this.checkIfQueueIsEmpty();
      },
      onQueueEmpty: function () {
      },
      checkIfQueueIsEmpty: function () {
        if (this.validatorsInQueue == 0 && resolved == false) {
          resolved = true;
          this.onQueueEmpty();
        }
      }
    };
  }

  function validateChanges(changes, source, callback) {
    var waitingForValidator = new ValidatorsQueue();
    waitingForValidator.onQueueEmpty = resolve;

    for (var i = changes.length - 1; i >= 0; i--) {
      if (changes[i] === null) {
        changes.splice(i, 1);
      }
      else {
        var row = changes[i][0];
        var col = datamap.propToCol(changes[i][1]);
        var logicalCol = instance.runHooksAndReturn('modifyCol', col); //column order may have changes, so we need to translate physical col index (stored in datasource) to logical (displayed to user)
        var cellProperties = instance.getCellMeta(row, logicalCol);

        if (cellProperties.type === 'numeric' && typeof changes[i][3] === 'string') {
          if (changes[i][3].length > 0 && /^-?[\d\s]*\.?\d*$/.test(changes[i][3])) {
            changes[i][3] = numeral().unformat(changes[i][3] || '0'); //numeral cannot unformat empty string
          }
        }

        if (instance.getCellValidator(cellProperties)) {
          waitingForValidator.addValidatorToQueue();
          instance.validateCell(changes[i][3], cellProperties, (function (i, cellProperties) {
            return function (result) {
              if (typeof result !== 'boolean') {
                throw new Error("Validation error: result is not boolean");
              }
              if (result === false && cellProperties.allowInvalid === false) {
                changes.splice(i, 1);         // cancel the change
                cellProperties.valid = true;  // we cancelled the change, so cell value is still valid
                --i;
              }
              waitingForValidator.removeValidatorFormQueue();
            }
          })(i, cellProperties)
            , source);
        }
      }
    }
    waitingForValidator.checkIfQueueIsEmpty();

    function resolve() {
      var beforeChangeResult;

      if (changes.length) {
        beforeChangeResult = instance.PluginHooks.execute("beforeChange", changes, source);
        if (typeof beforeChangeResult === 'function') {
          $.when(result).then(function () {
            callback(); //called when async validators and async beforeChange are resolved
          });
        }
        else if (beforeChangeResult === false) {
          changes.splice(0, changes.length); //invalidate all changes (remove everything from array)
        }
      }
      if (typeof beforeChangeResult !== 'function') {
        callback(); //called when async validators are resolved and beforeChange was not async
      }
    }
  }

  /**
   * Internal function to apply changes. Called after validateChanges
   * @param {Array} changes Array in form of [row, prop, oldValue, newValue]
   * @param {String} source String that identifies how this change will be described in changes array (useful in onChange callback)
   */
  function applyChanges(changes, source) {
    var i = changes.length - 1;

    if (i < 0) {
      return;
    }

    for (; 0 <= i; i--) {
      if (changes[i] === null) {
        changes.splice(i, 1);
        continue;
      }

      if (priv.settings.minSpareRows) {
        while (changes[i][0] > instance.countRows() - 1) {
          datamap.createRow();
        }
      }

      if (instance.dataType === 'array' && priv.settings.minSpareCols) {
        while (datamap.propToCol(changes[i][1]) > instance.countCols() - 1) {
          datamap.createCol();
        }
      }

      datamap.set(changes[i][0], changes[i][1], changes[i][3]);
    }

    instance.forceFullRender = true; //used when data was changed
    grid.adjustRowsAndCols();
    selection.refreshBorders(null, true);
    instance.PluginHooks.run('afterChange', changes, source || 'edit');
  }

  this.validateCell = function (value, cellProperties, callback, source) {
    var validator = instance.getCellValidator(cellProperties);

    if (Object.prototype.toString.call(validator) === '[object RegExp]') {
      validator = (function (validator) {
        return function (value, callback) {
          callback(validator.test(value));
        }
      })(validator);
    }

    if (typeof validator == 'function') {

      value = instance.PluginHooks.execute("beforeValidate", value, cellProperties.row, cellProperties.prop, source);

      // To provide consistent behaviour, validation should be always asynchronous
      setTimeout(function () {
        validator.call(cellProperties, value, function (valid) {
          cellProperties.valid = valid;

          valid = instance.PluginHooks.execute("afterValidate", valid, value, cellProperties.row, cellProperties.prop, source);

          callback(valid);
        });
      });

    } else { //resolve callback even if validator function was not found
      cellProperties.valid = true;
      callback(true);
    }



  };

  function setDataInputToArray(row, prop_or_col, value) {
    if (typeof row === "object") { //is it an array of changes
      return row;
    }
    else if ($.isPlainObject(value)) { //backwards compatibility
      return value;
    }
    else {
      return [
        [row, prop_or_col, value]
      ];
    }
  }

  /**
   * Set data at given cell
   * @public
   * @param {Number|Array} row or array of changes in format [[row, col, value], ...]
   * @param {Number|String} col or source String
   * @param {String} value
   * @param {String} source String that identifies how this change will be described in changes array (useful in onChange callback)
   */
  this.setDataAtCell = function (row, col, value, source) {
    var input = setDataInputToArray(row, col, value)
      , i
      , ilen
      , changes = []
      , prop;

    for (i = 0, ilen = input.length; i < ilen; i++) {
      if (typeof input[i] !== 'object') {
        throw new Error('Method `setDataAtCell` accepts row number or changes array of arrays as its first parameter');
      }
      if (typeof input[i][1] !== 'number') {
        throw new Error('Method `setDataAtCell` accepts row and column number as its parameters. If you want to use object property name, use method `setDataAtRowProp`');
      }
      prop = datamap.colToProp(input[i][1]);
      changes.push([
        input[i][0],
        prop,
        datamap.get(input[i][0], prop),
        input[i][2]
      ]);
    }

    if (!source && typeof row === "object") {
      source = col;
    }

    validateChanges(changes, source, function () {
      applyChanges(changes, source);
    });
  };


  /**
   * Set data at given row property
   * @public
   * @param {Number|Array} row or array of changes in format [[row, prop, value], ...]
   * @param {String} prop or source String
   * @param {String} value
   * @param {String} source String that identifies how this change will be described in changes array (useful in onChange callback)
   */
  this.setDataAtRowProp = function (row, prop, value, source) {
    var input = setDataInputToArray(row, prop, value)
      , i
      , ilen
      , changes = [];

    for (i = 0, ilen = input.length; i < ilen; i++) {
      changes.push([
        input[i][0],
        input[i][1],
        datamap.get(input[i][0], input[i][1]),
        input[i][2]
      ]);
    }

    if (!source && typeof row === "object") {
      source = prop;
    }

    validateChanges(changes, source, function () {
      applyChanges(changes, source);
    });
  };

  /**
   * Listen to document body keyboard input
   */
  this.listen = function () {
    Handsontable.activeGuid = instance.guid;

    if (document.activeElement && document.activeElement !== document.body) {
      document.activeElement.blur();
    }
    else if (!document.activeElement) { //IE
      document.body.focus();
    }
  };

  /**
   * Stop listening to document body keyboard input
   */
  this.unlisten = function () {
    Handsontable.activeGuid = null;
  };

  /**
   * Returns true if current Handsontable instance is listening on document body keyboard input
   */
  this.isListening = function () {
    return Handsontable.activeGuid === instance.guid;
  };

  /**
   * Destroys current editor, renders and selects current cell. If revertOriginal != true, edited data is saved
   * @param {Boolean} revertOriginal
   */
  this.destroyEditor = function (revertOriginal) {
    selection.refreshBorders(revertOriginal);
  };

  /**
   * Populate cells at position with 2d array
   * @param {Number} row Start row
   * @param {Number} col Start column
   * @param {Array} input 2d array
   * @param {Number=} endRow End row (use when you want to cut input when certain row is reached)
   * @param {Number=} endCol End column (use when you want to cut input when certain column is reached)
   * @param {String=} [source="populateFromArray"]
   * @param {String=} [method="overwrite"]
   * @return {Object|undefined} ending td in pasted area (only if any cell was changed)
   */
  this.populateFromArray = function (row, col, input, endRow, endCol, source, method) {
    if (!(typeof input === 'object' && typeof input[0] === 'object')) {
      throw new Error("populateFromArray parameter `input` must be an array of arrays"); //API changed in 0.9-beta2, let's check if you use it correctly
    }
    return grid.populateFromArray({row: row, col: col}, input, typeof endRow === 'number' ? {row: endRow, col: endCol} : null, source, method);
  };

  /**
   * Adds/removes data from the column
   * @param {Number} col Index of column in which do you want to do splice.
   * @param {Number} index Index at which to start changing the array. If negative, will begin that many elements from the end
   * @param {Number} amount An integer indicating the number of old array elements to remove. If amount is 0, no elements are removed
   * param {...*} elements Optional. The elements to add to the array. If you don't specify any elements, spliceCol simply removes elements from the array
   */
  this.spliceCol = function (col, index, amount/*, elements... */) {
    return datamap.spliceCol.apply(datamap, arguments);
  };

  /**
   * Adds/removes data from the row
   * @param {Number} row Index of column in which do you want to do splice.
   * @param {Number} index Index at which to start changing the array. If negative, will begin that many elements from the end
   * @param {Number} amount An integer indicating the number of old array elements to remove. If amount is 0, no elements are removed
   * param {...*} elements Optional. The elements to add to the array. If you don't specify any elements, spliceCol simply removes elements from the array
   */
  this.spliceRow = function (row, index, amount/*, elements... */) {
    return datamap.spliceRow.apply(datamap, arguments);
  };

  /**
   * Returns the top left (TL) and bottom right (BR) selection coordinates
   * @param {Object[]} coordsArr
   * @returns {Object}
   */
  this.getCornerCoords = function (coordsArr) {
    return grid.getCornerCoords(coordsArr);
  };

  /**
   * Returns current selection. Returns undefined if there is no selection.
   * @public
   * @return {Array} [`startRow`, `startCol`, `endRow`, `endCol`]
   */
  this.getSelected = function () { //https://github.com/warpech/jquery-handsontable/issues/44  //cjl
    if (selection.isSelected()) {
      return [priv.selStart.row(), priv.selStart.col(), priv.selEnd.row(), priv.selEnd.col()];
    }
  };

  /**
   * Parse settings from DOM and CSS
   * @public
   */
  this.parseSettingsFromDOM = function () {
    var overflow = this.rootElement.css('overflow');
    if (overflow === 'scroll' || overflow === 'auto') {
      this.rootElement[0].style.overflow = 'visible';
      priv.settingsFromDOM.overflow = overflow;
    }
    else if (priv.settings.width === void 0 || priv.settings.height === void 0) {
      priv.settingsFromDOM.overflow = 'auto';
    }

    if (priv.settings.width === void 0) {
      priv.settingsFromDOM.width = this.rootElement.width();
    }
    else {
      priv.settingsFromDOM.width = void 0;
    }

    priv.settingsFromDOM.height = void 0;
    if (priv.settings.height === void 0) {
      if (priv.settingsFromDOM.overflow === 'scroll' || priv.settingsFromDOM.overflow === 'auto') {
        //this needs to read only CSS/inline style and not actual height
        //so we need to call getComputedStyle on cloned container
        var clone = this.rootElement[0].cloneNode(false);
        var parent = this.rootElement[0].parentNode;
        if (parent) {
          clone.removeAttribute('id');
          parent.appendChild(clone);
          var computedHeight = parseInt(window.getComputedStyle(clone, null).getPropertyValue('height'), 10);

          if(isNaN(computedHeight) && clone.currentStyle){
            computedHeight = parseInt(clone.currentStyle.height, 10)
          }

          if (computedHeight > 0) {
            priv.settingsFromDOM.height = computedHeight;
          }
          parent.removeChild(clone);
        }
      }
    }
  };

  /**
   * Render visible data
   * @public
   */
  this.render = function () {
    if (instance.view) {
      instance.forceFullRender = true; //used when data was changed
      instance.parseSettingsFromDOM();
      selection.refreshBorders(null, true);
    }
  };

  /**
   * Load data from array
   * @public
   * @param {Array} data
   */
  this.loadData = function (data) {
    if (typeof data === 'object' && data !== null) {
      if (!(data.push && data.splice)) { //check if data is array. Must use duck-type check so Backbone Collections also pass it
        //when data is not an array, attempt to make a single-row array of it
        data = [data];
      }
    }
    else if(data === null) {
      data = [];
      var row;
      for (var r = 0, rlen = priv.settings.startRows; r < rlen; r++) {
        row = [];
        for (var c = 0, clen = priv.settings.startCols; c < clen; c++) {
          row.push(null);
        }
        data.push(row);
      }
    }
    else {
      throw new Error("loadData only accepts array of objects or array of arrays (" + typeof data + " given)");
    }

    priv.isPopulated = false;
    GridSettings.prototype.data = data;

    if (priv.settings.dataSchema instanceof Array || data[0]  instanceof Array) {
      instance.dataType = 'array';
    }
    else if (typeof priv.settings.dataSchema === 'function') {
      instance.dataType = 'function';
    }
    else {
      instance.dataType = 'object';
    }

    datamap = new Handsontable.DataMap(instance, priv, GridSettings);

    clearCellSettingCache();

    grid.adjustRowsAndCols();
    instance.PluginHooks.run('afterLoadData');

    if (priv.firstRun) {
      priv.firstRun = [null, 'loadData'];
    }
    else {
      instance.PluginHooks.run('afterChange', null, 'loadData');
      instance.render();
    }

    priv.isPopulated = true;



    function clearCellSettingCache() {
      priv.cellSettings.length = 0;
    }
  };

  /**
   * Return the current data object (the same that was passed by `data` configuration option or `loadData` method). Optionally you can provide cell range `r`, `c`, `r2`, `c2` to get only a fragment of grid data
   * @public
   * @param {Number} r (Optional) From row
   * @param {Number} c (Optional) From col
   * @param {Number} r2 (Optional) To row
   * @param {Number} c2 (Optional) To col
   * @return {Array|Object}
   */
  this.getData = function (r, c, r2, c2) {
    if (typeof r === 'undefined') {
      return datamap.getAll();
    }
    else {
      return datamap.getRange({row: r, col: c}, {row: r2, col: c2}, datamap.DESTINATION_RENDERER);
    }
  };

  this.getCopyableData = function (startRow, startCol, endRow, endCol) {
    return datamap.getCopyableText({row: startRow, col: startCol}, {row: endRow, col: endCol});
  }

  /**
   * Update settings
   * @public
   */
  this.updateSettings = function (settings, init) {
    var i, clen;

    if (typeof settings.rows !== "undefined") {
      throw new Error("'rows' setting is no longer supported. do you mean startRows, minRows or maxRows?");
    }
    if (typeof settings.cols !== "undefined") {
      throw new Error("'cols' setting is no longer supported. do you mean startCols, minCols or maxCols?");
    }

    for (i in settings) {
      if (i === 'data') {
        continue; //loadData will be triggered later
      }
      else {
        if (instance.PluginHooks.hooks[i] !== void 0 || instance.PluginHooks.legacy[i] !== void 0) {
          if (typeof settings[i] === 'function' || Handsontable.helper.isArray(settings[i])) {
            instance.PluginHooks.add(i, settings[i]);
          }
        }
        else {
          // Update settings
          if (!init && settings.hasOwnProperty(i)) {
            GridSettings.prototype[i] = settings[i];
          }

          //launch extensions
          if (Handsontable.extension[i]) {
            priv.extensions[i] = new Handsontable.extension[i](instance, settings[i]);
          }
        }
      }
    }

    // Load data or create data map
    if (settings.data === void 0 && priv.settings.data === void 0) {
      instance.loadData(null); //data source created just now
    }
    else if (settings.data !== void 0) {
      instance.loadData(settings.data); //data source given as option
    }
    else if (settings.columns !== void 0) {
      datamap.createMap();
    }

    // Init columns constructors configuration
    clen = instance.countCols();

    //Clear cellSettings cache
    priv.cellSettings.length = 0;

    if (clen > 0) {
      var proto, column;

      for (i = 0; i < clen; i++) {
        priv.columnSettings[i] = Handsontable.helper.columnFactory(GridSettings, priv.columnsSettingConflicts);

        // shortcut for prototype
        proto = priv.columnSettings[i].prototype;

        // Use settings provided by user
        if (GridSettings.prototype.columns) {
          column = GridSettings.prototype.columns[i];
          Handsontable.helper.extend(proto, column);
          Handsontable.helper.extend(proto, expandType(column));
        }
      }
    }

    if (typeof settings.fillHandle !== "undefined") {
      if (autofill.handle && settings.fillHandle === false) {
        autofill.disable();
      }
      else if (!autofill.handle && settings.fillHandle !== false) {
        autofill.init();
      }
    }

    if (typeof settings.className !== "undefined") {
      if (GridSettings.prototype.className) {
        instance.rootElement.removeClass(GridSettings.prototype.className);
      }
      if (settings.className) {
        instance.rootElement.addClass(settings.className);
      }
    }

    if (!init) {
      instance.PluginHooks.run('afterUpdateSettings');
    }

    grid.adjustRowsAndCols();
    if (instance.view && !priv.firstRun) {
      instance.forceFullRender = true; //used when data was changed
      selection.refreshBorders(null, true);
    }
  };

  this.getValue = function () {
    var sel = instance.getSelected();
    if (GridSettings.prototype.getValue) {
      if (typeof GridSettings.prototype.getValue === 'function') {
        return GridSettings.prototype.getValue.call(instance);
      }
      else if (sel) {
        return instance.getData()[sel[0]][GridSettings.prototype.getValue];
      }
    }
    else if (sel) {
      return instance.getDataAtCell(sel[0], sel[1]);
    }
  };

  function expandType(obj) {
    if (!obj.hasOwnProperty('type')) return; //ignore obj.prototype.type


    var type, expandedType = {};

    if (typeof obj.type === 'object') {
      type = obj.type;
    }
    else if (typeof obj.type === 'string') {
      type = Handsontable.cellTypes[obj.type];
      if (type === void 0) {
        throw new Error('You declared cell type "' + obj.type + '" as a string that is not mapped to a known object. Cell type must be an object or a string mapped to an object in Handsontable.cellTypes');
      }
    }


    for (var i in type) {
      if (type.hasOwnProperty(i) && !obj.hasOwnProperty(i)) {
        expandedType[i] = type[i];
      }
    }

    return expandedType;

  }

  /**
   * Returns current settings object
   * @return {Object}
   */
  this.getSettings = function () {
    return priv.settings;
  };

  /**
   * Returns current settingsFromDOM object
   * @return {Object}
   */
  this.getSettingsFromDOM = function () {
    return priv.settingsFromDOM;
  };

  /**
   * Clears grid
   * @public
   */
  this.clear = function () {
    selection.selectAll();
    selection.empty();
  };

  /**
   * Inserts or removes rows and columns
   * @param {String} action See grid.alter for possible values
   * @param {Number} index
   * @param {Number} amount
   * @param {String} [source] Optional. Source of hook runner.
   * @param {Boolean} [keepEmptyRows] Optional. Flag for preventing deletion of empty rows.
   * @public
   */
  this.alter = function (action, index, amount, source, keepEmptyRows) {
    grid.alter(action, index, amount, source, keepEmptyRows);
  };

  /**
   * Returns <td> element corresponding to params row, col
   * @param {Number} row
   * @param {Number} col
   * @public
   * @return {Element}
   */
  this.getCell = function (row, col) {
    return instance.view.getCellAtCoords({row: row, col: col});
  };

  /**
   * Returns property name associated with column number
   * @param {Number} col
   * @public
   * @return {String}
   */
  this.colToProp = function (col) {
    return datamap.colToProp(col);
  };

  /**
   * Returns column number associated with property name
   * @param {String} prop
   * @public
   * @return {Number}
   */
  this.propToCol = function (prop) {
    return datamap.propToCol(prop);
  };

  /**
   * Return value at `row`, `col`
   * @param {Number} row
   * @param {Number} col
   * @public
   * @return value (mixed data type)
   */
  this.getDataAtCell = function (row, col) {
    return datamap.get(row, datamap.colToProp(col));
  };

  /**
   * Return value at `row`, `prop`
   * @param {Number} row
   * @param {String} prop
   * @public
   * @return value (mixed data type)
   */
  this.getDataAtRowProp = function (row, prop) {
    return datamap.get(row, prop);
  };

  /**
   * Return value at `col`
   * @param {Number} col
   * @public
   * @return value (mixed data type)
   */
  this.getDataAtCol = function (col) {
    return [].concat.apply([], datamap.getRange({row: 0, col: col}, {row: priv.settings.data.length - 1, col: col}, datamap.DESTINATION_RENDERER));
  };

  /**
   * Return value at `prop`
   * @param {String} prop
   * @public
   * @return value (mixed data type)
   */
  this.getDataAtProp = function (prop) {
    return [].concat.apply([], datamap.getRange({row: 0, col: datamap.propToCol(prop)}, {row: priv.settings.data.length - 1, col: datamap.propToCol(prop)}, datamap.DESTINATION_RENDERER));
  };

  /**
   * Return value at `row`
   * @param {Number} row
   * @public
   * @return value (mixed data type)
   */
  this.getDataAtRow = function (row) {
    return priv.settings.data[row];
  };

  /**
   * Returns cell meta data object corresponding to params row, col
   * @param {Number} row
   * @param {Number} col
   * @public
   * @return {Object}
   */
  this.getCellMeta = function (row, col) {
    var prop = datamap.colToProp(col)
      , cellProperties;

    row = translateRowIndex(row);
    col = translateColIndex(col);

    if ("undefined" === typeof priv.columnSettings[col]) {
      priv.columnSettings[col] = Handsontable.helper.columnFactory(GridSettings, priv.columnsSettingConflicts);
    }

    if (!priv.cellSettings[row]) {
      priv.cellSettings[row] = [];
    }
    if (!priv.cellSettings[row][col]) {
      priv.cellSettings[row][col] = new priv.columnSettings[col]();
    }

    cellProperties = priv.cellSettings[row][col]; //retrieve cellProperties from cache

    cellProperties.row = row;
    cellProperties.col = col;
    cellProperties.prop = prop;
    cellProperties.instance = instance;

    instance.PluginHooks.run('beforeGetCellMeta', row, col, cellProperties);
    Handsontable.helper.extend(cellProperties, expandType(cellProperties)); //for `type` added in beforeGetCellMeta

    if (cellProperties.cells) {
      var settings = cellProperties.cells.call(cellProperties, row, col, prop);

      if (settings) {
        Handsontable.helper.extend(cellProperties, settings);
        Handsontable.helper.extend(cellProperties, expandType(settings)); //for `type` added in cells
      }
    }

    instance.PluginHooks.run('afterGetCellMeta', row, col, cellProperties);

    return cellProperties;

    /**
     * If displayed rows order is different than the order of rows stored in memory (i.e. sorting is applied)
     * we need to translate logical (stored) row index to physical (displayed) index.
     * @param row - original row index
     * @returns {int} translated row index
     */
    function translateRowIndex(row){
      var getVars  = {row: row};

      instance.PluginHooks.execute('beforeGet', getVars);

      return getVars.row;
    }

    /**
     * If displayed columns order is different than the order of columns stored in memory (i.e. column were moved using manualColumnMove plugin)
     * we need to translate logical (stored) column index to physical (displayed) index.
     * @param col - original column index
     * @returns {int} - translated column index
     */
    function translateColIndex(col){
      return Handsontable.PluginHooks.execute(instance, 'modifyCol', col); // warning: this must be done after datamap.colToProp
    }
  };

  var rendererLookup = Handsontable.helper.cellMethodLookupFactory('renderer');
  this.getCellRenderer = function (row, col) {
    var renderer = rendererLookup.call(this, row, col);
    return Handsontable.renderers.getRenderer(renderer);

  };

  this.getCellEditor = Handsontable.helper.cellMethodLookupFactory('editor');

  this.getCellValidator = Handsontable.helper.cellMethodLookupFactory('validator');


  /**
   * Validates all cells using their validator functions and calls callback when finished. Does not render the view
   * @param callback
   */
  this.validateCells = function (callback) {
    var waitingForValidator = new ValidatorsQueue();
    waitingForValidator.onQueueEmpty = callback;

    var i = instance.countRows() - 1;
    while (i >= 0) {
      var j = instance.countCols() - 1;
      while (j >= 0) {
        waitingForValidator.addValidatorToQueue();
        instance.validateCell(instance.getDataAtCell(i, j), instance.getCellMeta(i, j), function () {
          waitingForValidator.removeValidatorFormQueue();
        }, 'validateCells');
        j--;
      }
      i--;
    }
    waitingForValidator.checkIfQueueIsEmpty();
  };

  /**
   * Return array of row headers (if they are enabled). If param `row` given, return header at given row as string
   * @param {Number} row (Optional)
   * @return {Array|String}
   */
  this.getRowHeader = function (row) {
    if (row === void 0) {
      var out = [];
      for (var i = 0, ilen = instance.countRows(); i < ilen; i++) {
        out.push(instance.getRowHeader(i));
      }
      return out;
    }
    else if (Object.prototype.toString.call(priv.settings.rowHeaders) === '[object Array]' && priv.settings.rowHeaders[row] !== void 0) {
      return priv.settings.rowHeaders[row];
    }
    else if (typeof priv.settings.rowHeaders === 'function') {
      return priv.settings.rowHeaders(row);
    }
    else if (priv.settings.rowHeaders && typeof priv.settings.rowHeaders !== 'string' && typeof priv.settings.rowHeaders !== 'number') {
      return row + 1;
    }
    else {
      return priv.settings.rowHeaders;
    }
  };

  /**
   * Returns information of this table is configured to display row headers
   * @returns {boolean}
   */
  this.hasRowHeaders = function () {
    return !!priv.settings.rowHeaders;
  };

  /**
   * Returns information of this table is configured to display column headers
   * @returns {boolean}
   */
  this.hasColHeaders = function () {
    if (priv.settings.colHeaders !== void 0 && priv.settings.colHeaders !== null) { //Polymer has empty value = null
      return !!priv.settings.colHeaders;
    }
    for (var i = 0, ilen = instance.countCols(); i < ilen; i++) {
      if (instance.getColHeader(i)) {
        return true;
      }
    }
    return false;
  };

  /**
   * Return array of column headers (if they are enabled). If param `col` given, return header at given column as string
   * @param {Number} col (Optional)
   * @return {Array|String}
   */
  this.getColHeader = function (col) {
    if (col === void 0) {
      var out = [];
      for (var i = 0, ilen = instance.countCols(); i < ilen; i++) {
        out.push(instance.getColHeader(i));
      }
      return out;
    }
    else {
      col = Handsontable.PluginHooks.execute(instance, 'modifyCol', col);

      if (priv.settings.columns && priv.settings.columns[col] && priv.settings.columns[col].title) {
        return priv.settings.columns[col].title;
      }
      else if (Object.prototype.toString.call(priv.settings.colHeaders) === '[object Array]' && priv.settings.colHeaders[col] !== void 0) {
        return priv.settings.colHeaders[col];
      }
      else if (typeof priv.settings.colHeaders === 'function') {
        return priv.settings.colHeaders(col);
      }
      else if (priv.settings.colHeaders && typeof priv.settings.colHeaders !== 'string' && typeof priv.settings.colHeaders !== 'number') {
        return Handsontable.helper.spreadsheetColumnLabel(col);
      }
      else {
        return priv.settings.colHeaders;
      }
    }
  };

  /**
   * Return column width from settings (no guessing). Private use intended
   * @param {Number} col
   * @return {Number}
   */
  this._getColWidthFromSettings = function (col) {
    var cellProperties = instance.getCellMeta(0, col);
    var width = cellProperties.width;
    if (width === void 0 || width === priv.settings.width) {
      width = cellProperties.colWidths;
    }
    if (width !== void 0 && width !== null) {
      switch (typeof width) {
        case 'object': //array
          width = width[col];
          break;

        case 'function':
          width = width(col);
          break;
      }
      if (typeof width === 'string') {
        width = parseInt(width, 10);
      }
    }
    return width;
  };

  /**
   * Return column width
   * @param {Number} col
   * @return {Number}
   */
  this.getColWidth = function (col) {
    col = Handsontable.PluginHooks.execute(instance, 'modifyCol', col);
    var response = {
      width: instance._getColWidthFromSettings(col)
    };
    if (!response.width) {
      response.width = 50;
    }
    instance.PluginHooks.run('afterGetColWidth', col, response);
    return response.width;
  };

  /**
   * Return total number of rows in grid
   * @return {Number}
   */
  this.countRows = function () {
    return priv.settings.data.length;
  };

  /**
   * Return total number of columns in grid
   * @return {Number}
   */
  this.countCols = function () {
    if (instance.dataType === 'object' || instance.dataType === 'function') {
      if (priv.settings.columns && priv.settings.columns.length) {
        return priv.settings.columns.length;
      }
      else {
        return datamap.colToPropCache.length;
      }
    }
    else if (instance.dataType === 'array') {
      if (priv.settings.columns && priv.settings.columns.length) {
        return priv.settings.columns.length;
      }
      else if (priv.settings.data && priv.settings.data[0] && priv.settings.data[0].length) {
        return priv.settings.data[0].length;
      }
      else {
        return 0;
      }
    }
  };

  /**
   * Return index of first visible row
   * @return {Number}
   */
  this.rowOffset = function () {
    return instance.view.wt.getSetting('offsetRow');
  };

  /**
   * Return index of first visible column
   * @return {Number}
   */
  this.colOffset = function () {
    return instance.view.wt.getSetting('offsetColumn');
  };

  /**
   * Return number of visible rows. Returns -1 if table is not visible
   * @return {Number}
   */
  this.countVisibleRows = function () {
    return instance.view.wt.drawn ? instance.view.wt.wtTable.rowStrategy.countVisible() : -1;
  };

  /**
   * Return number of visible columns. Returns -1 if table is not visible
   * @return {Number}
   */
  this.countVisibleCols = function () {
    return instance.view.wt.drawn ? instance.view.wt.wtTable.columnStrategy.countVisible() : -1;
  };

  /**
   * Return number of empty rows
   * @return {Boolean} ending If true, will only count empty rows at the end of the data source
   */
  this.countEmptyRows = function (ending) {
    var i = instance.countRows() - 1
      , empty = 0;
    while (i >= 0) {
      datamap.get(i, 0);

      if (instance.isEmptyRow(datamap.getVars.row)) {
        empty++;
      }
      else if (ending) {
        break;
      }
      i--;
    }
    return empty;
  };

  /**
   * Return number of empty columns
   * @return {Boolean} ending If true, will only count empty columns at the end of the data source row
   */
  this.countEmptyCols = function (ending) {
    if (instance.countRows() < 1) {
      return 0;
    }

    var i = instance.countCols() - 1
      , empty = 0;
    while (i >= 0) {
      if (instance.isEmptyCol(i)) {
        empty++;
      }
      else if (ending) {
        break;
      }
      i--;
    }
    return empty;
  };

  /**
   * Return true if the row at the given index is empty, false otherwise
   * @param {Number} r Row index
   * @return {Boolean}
   */
  this.isEmptyRow = function (r) {
    return priv.settings.isEmptyRow.call(instance, r);
  };

  /**
   * Return true if the column at the given index is empty, false otherwise
   * @param {Number} c Column index
   * @return {Boolean}
   */
  this.isEmptyCol = function (c) {
    return priv.settings.isEmptyCol.call(instance, c);
  };

  /**
   * Selects cell on grid. Optionally selects range to another cell
   * @param {Number} row
   * @param {Number} col
   * @param {Number} [endRow]
   * @param {Number} [endCol]
   * @param {Boolean} [scrollToCell=true] If true, viewport will be scrolled to the selection
   * @public
   * @return {Boolean}
   */
  this.selectCell = function (row, col, endRow, endCol, scrollToCell) {
    if (typeof row !== 'number' || row < 0 || row >= instance.countRows()) {
      return false;
    }
    if (typeof col !== 'number' || col < 0 || col >= instance.countCols()) {
      return false;
    }
    if (typeof endRow !== "undefined") {
      if (typeof endRow !== 'number' || endRow < 0 || endRow >= instance.countRows()) {
        return false;
      }
      if (typeof endCol !== 'number' || endCol < 0 || endCol >= instance.countCols()) {
        return false;
      }
    }
    priv.selStart.coords({row: row, col: col});
    if (document.activeElement && document.activeElement !== document.documentElement && document.activeElement !== document.body) {
      document.activeElement.blur(); //needed or otherwise prepare won't focus the cell. selectionSpec tests this (should move focus to selected cell)
    }
    instance.listen();
    if (typeof endRow === "undefined") {
      selection.setRangeEnd({row: row, col: col}, scrollToCell);
    }
    else {
      selection.setRangeEnd({row: endRow, col: endCol}, scrollToCell);
    }

    instance.selection.finish();
    return true;
  };

  this.selectCellByProp = function (row, prop, endRow, endProp, scrollToCell) {
    arguments[1] = datamap.propToCol(arguments[1]);
    if (typeof arguments[3] !== "undefined") {
      arguments[3] = datamap.propToCol(arguments[3]);
    }
    return instance.selectCell.apply(instance, arguments);
  };

  /**
   * Deselects current sell selection on grid
   * @public
   */
  this.deselectCell = function () {
    selection.deselect();
  };

  /**
   * Remove grid from DOM
   * @public
   */
  this.destroy = function () {
    instance.clearTimeouts();
    if (instance.view) { //in case HT is destroyed before initialization has finished
      instance.view.wt.destroy();
    }
    instance.rootElement.empty();
    instance.rootElement.removeData('handsontable');
    instance.rootElement.off('.handsontable');
    $(window).off('.' + instance.guid);
    $document.off('.' + instance.guid);
    $body.off('.' + instance.guid);
    instance.PluginHooks.run('afterDestroy');
  };

  /**
   * Returns active editor object
   * @returns {Object}
   */
  this.getActiveEditor = function(){
    return editorManager.getActiveEditor();
  };

  /**
   * Return Handsontable instance
   * @public
   * @return {Object}
   */
  this.getInstance = function () {
    return instance.rootElement.data("handsontable");
  };

  (function () {
    // Create new instance of plugin hooks
    instance.PluginHooks = new Handsontable.PluginHookClass();

    // Upgrade methods to call of global PluginHooks instance
    var _run = instance.PluginHooks.run
      , _exe = instance.PluginHooks.execute;

    instance.PluginHooks.run = function (key, p1, p2, p3, p4, p5) {
      _run.call(this, instance, key, p1, p2, p3, p4, p5);
      Handsontable.PluginHooks.run(instance, key, p1, p2, p3, p4, p5);
    };

    instance.PluginHooks.execute = function (key, p1, p2, p3, p4, p5) {
      var globalHandlerResult = Handsontable.PluginHooks.execute(instance, key, p1, p2, p3, p4, p5);
      var localHandlerResult = _exe.call(this, instance, key, globalHandlerResult, p2, p3, p4, p5);

      return typeof localHandlerResult == 'undefined' ? globalHandlerResult : localHandlerResult;

    };

    // Map old API with new methods
    instance.addHook = function () {
      instance.PluginHooks.add.apply(instance.PluginHooks, arguments);
    };
    instance.addHookOnce = function () {
      instance.PluginHooks.once.apply(instance.PluginHooks, arguments);
    };

    instance.removeHook = function () {
      instance.PluginHooks.remove.apply(instance.PluginHooks, arguments);
    };

    instance.runHooks = function () {
      instance.PluginHooks.run.apply(instance.PluginHooks, arguments);
    };
    instance.runHooksAndReturn = function () {
      return instance.PluginHooks.execute.apply(instance.PluginHooks, arguments);
    };

  })();

  this.timeouts = {};

  /**
   * Sets timeout. Purpose of this method is to clear all known timeouts when `destroy` method is called
   * @public
   */
  this.registerTimeout = function (key, handle, ms) {
    clearTimeout(this.timeouts[key]);
    this.timeouts[key] = setTimeout(handle, ms || 0);
  };

  /**
   * Clears all known timeouts
   * @public
   */
  this.clearTimeouts = function () {
    for (var key in this.timeouts) {
      if (this.timeouts.hasOwnProperty(key)) {
        clearTimeout(this.timeouts[key]);
      }
    }
  };

  /**
   * Handsontable version
   */
  this.version = '0.10.3'; //inserted by grunt from package.json
};

var DefaultSettings = function () {};

DefaultSettings.prototype = {
  data: void 0,
  width: void 0,
  height: void 0,
  startRows: 5,
  startCols: 5,
  rowHeaders: null,
  colHeaders: null,
  minRows: 0,
  minCols: 0,
  maxRows: Infinity,
  maxCols: Infinity,
  minSpareRows: 0,
  minSpareCols: 0,
  multiSelect: true,
  fillHandle: true,
  fixedRowsTop: 0,
  fixedColumnsLeft: 0,
  outsideClickDeselects: true,
  enterBeginsEditing: true,
  enterMoves: {row: 1, col: 0},
  tabMoves: {row: 0, col: 1},
  autoWrapRow: false,
  autoWrapCol: false,
  copyRowsLimit: 1000,
  copyColsLimit: 1000,
  pasteMode: 'overwrite',
  currentRowClassName: void 0,
  currentColClassName: void 0,
  stretchH: 'hybrid',
  isEmptyRow: function (r) {
    var val;
    for (var c = 0, clen = this.countCols(); c < clen; c++) {
      val = this.getDataAtCell(r, c);
      if (val !== '' && val !== null && typeof val !== 'undefined') {
        return false;
      }
    }
    return true;
  },
  isEmptyCol: function (c) {
    var val;
    for (var r = 0, rlen = this.countRows(); r < rlen; r++) {
      val = this.getDataAtCell(r, c);
      if (val !== '' && val !== null && typeof val !== 'undefined') {
        return false;
      }
    }
    return true;
  },
  observeDOMVisibility: true,
  allowInvalid: true,
  invalidCellClassName: 'htInvalid',
  placeholderCellClassName: 'htPlaceholder',
  readOnlyCellClassName: 'htDimmed',
  fragmentSelection: false,
  readOnly: false,
  nativeScrollbars: false,
  type: 'text',
  copyable: true,
  debug: false //shows debug overlays in Walkontable
};
Handsontable.DefaultSettings = DefaultSettings;

$.fn.handsontable = function (action) {
  var i
    , ilen
    , args
    , output
    , userSettings
    , $this = this.first() // Use only first element from list
    , instance = $this.data('handsontable');

  // Init case
  if (typeof action !== 'string') {
    userSettings = action || {};
    if (instance) {
      instance.updateSettings(userSettings);
    }
    else {
      instance = new Handsontable.Core($this, userSettings);
      $this.data('handsontable', instance);
      instance.init();
    }

    return $this;
  }
  // Action case
  else {
    args = [];
    if (arguments.length > 1) {
      for (i = 1, ilen = arguments.length; i < ilen; i++) {
        args.push(arguments[i]);
      }
    }

    if (instance) {
      if (typeof instance[action] !== 'undefined') {
        output = instance[action].apply(instance, args);
      }
      else {
        throw new Error('Handsontable do not provide action: ' + action);
      }
    }

    return output;
  }
};

/**
 * Handsontable TableView constructor
 * @param {Object} instance
 */
Handsontable.TableView = function (instance) {
  var that = this
    , $window = $(window)
    , $documentElement = $(document.documentElement);

  this.instance = instance;
  this.settings = instance.getSettings();
  this.settingsFromDOM = instance.getSettingsFromDOM();

  instance.rootElement.data('originalStyle', instance.rootElement[0].getAttribute('style')); //needed to retrieve original style in jsFiddle link generator in HT examples. may be removed in future versions
  // in IE7 getAttribute('style') returns an object instead of a string, but we only support IE8+

  instance.rootElement.addClass('handsontable');

  var table = document.createElement('TABLE');
  table.className = 'htCore';
  this.THEAD = document.createElement('THEAD');
  table.appendChild(this.THEAD);
  this.TBODY = document.createElement('TBODY');
  table.appendChild(this.TBODY);

  instance.$table = $(table);
  instance.rootElement.prepend(instance.$table);

  instance.rootElement.on('mousedown.handsontable', function (event) {
    if (!that.isTextSelectionAllowed(event.target)) {
      clearTextSelection();
      event.preventDefault();
      window.focus(); //make sure that window that contains HOT is active. Important when HOT is in iframe.
    }
  });

  $documentElement.on('keyup.' + instance.guid, function (event) {
    if (instance.selection.isInProgress() && !event.shiftKey) {
      instance.selection.finish();
    }
  });

  var isMouseDown;

  $documentElement.on('mouseup.' + instance.guid, function (event) {
    if (instance.selection.isInProgress() && event.which === 1) { //is left mouse button
      instance.selection.finish();
    }

    isMouseDown = false;

    if (instance.autofill.handle && instance.autofill.handle.isDragged) {
      if (instance.autofill.handle.isDragged > 1) {
        instance.autofill.apply();
      }
      instance.autofill.handle.isDragged = 0;
    }

    if (Handsontable.helper.isOutsideInput(document.activeElement)) {
      instance.unlisten();
    }
  });

  $documentElement.on('mousedown.' + instance.guid, function (event) {
    var next = event.target;

    if (next !== that.wt.wtTable.spreader) { //immediate click on "spreader" means click on the right side of vertical scrollbar
      while (next !== document.documentElement) {
        if (next === null) {
          return; //click on something that was a row but now is detached (possibly because your click triggered a rerender)
        }
        if (next === instance.rootElement[0] || next.nodeName === 'HANDSONTABLE-TABLE') {
          return; //click inside container or Web Component (HANDSONTABLE-TABLE is the name of the custom element)
        }
        next = next.parentNode;
      }
    }

    if (that.settings.outsideClickDeselects) {
      instance.deselectCell();
    }
    else {
      instance.destroyEditor();
    }
  });

  instance.rootElement.on('mousedown.handsontable', '.dragdealer', function () {
    instance.destroyEditor();
  });

  instance.$table.on('selectstart', function (event) {
    if (that.settings.fragmentSelection) {
      return;
    }

    //https://github.com/warpech/jquery-handsontable/issues/160
    //selectstart is IE only event. Prevent text from being selected when performing drag down in IE8
    event.preventDefault();
  });

  var clearTextSelection = function () {
    //http://stackoverflow.com/questions/3169786/clear-text-selection-with-javascript
    if (window.getSelection) {
      if (window.getSelection().empty) {  // Chrome
        window.getSelection().empty();
      } else if (window.getSelection().removeAllRanges) {  // Firefox
        window.getSelection().removeAllRanges();
      }
    } else if (document.selection) {  // IE?
      document.selection.empty();
    }
  };

  var walkontableConfig = {
    debug: function () {
      return that.settings.debug;
    },
    table: table,
    stretchH: this.settings.stretchH,
    data: instance.getDataAtCell,
    totalRows: instance.countRows,
    totalColumns: instance.countCols,
    nativeScrollbars: this.settings.nativeScrollbars,
    offsetRow: 0,
    offsetColumn: 0,
    width: this.getWidth(),
    height: this.getHeight(),
    fixedColumnsLeft: function () {
      return that.settings.fixedColumnsLeft;
    },
    fixedRowsTop: function () {
      return that.settings.fixedRowsTop;
    },
    rowHeaders: function () {
      return instance.hasRowHeaders() ? [function (index, TH) {
        that.appendRowHeader(index, TH);
      }] : []
    },
    columnHeaders: function () {
      return instance.hasColHeaders() ? [function (index, TH) {
        that.appendColHeader(index, TH);
      }] : []
    },
    columnWidth: instance.getColWidth,
    cellRenderer: function (row, col, TD) {

      var prop = that.instance.colToProp(col)
        , cellProperties = that.instance.getCellMeta(row, col)
        , renderer = that.instance.getCellRenderer(cellProperties);

      var value = that.instance.getDataAtRowProp(row, prop);

      renderer(that.instance, TD, row, col, prop, value, cellProperties);

      that.instance.PluginHooks.run('afterRenderer', TD, row, col, prop, value, cellProperties);

    },
    selections: {
      current: {
        className: 'current',
        border: {
          width: 2,
          color: '#5292F7',
          style: 'solid',
          cornerVisible: function () {
            return that.settings.fillHandle && !that.isCellEdited() && !instance.selection.isMultiple()
          }
        }
      },
      area: {
        className: 'area',
        border: {
          width: 1,
          color: '#89AFF9',
          style: 'solid',
          cornerVisible: function () {
            return that.settings.fillHandle && !that.isCellEdited() && instance.selection.isMultiple()
          }
        }
      },
      highlight: {
        highlightRowClassName: that.settings.currentRowClassName,
        highlightColumnClassName: that.settings.currentColClassName
      },
      fill: {
        className: 'fill',
        border: {
          width: 1,
          color: 'red',
          style: 'solid'
        }
      }
    },
    hideBorderOnMouseDownOver: function () {
      return that.settings.fragmentSelection;
    },
    onCellMouseDown: function (event, coords, TD) {
      instance.listen();

      isMouseDown = true;
      var coordsObj = {row: coords[0], col: coords[1]};
      if (event.button === 2 && instance.selection.inInSelection(coordsObj)) { //right mouse button
        //do nothing
      }
      else if (event.shiftKey) {
        instance.selection.setRangeEnd(coordsObj);
      }
      else {
        instance.selection.setRangeStart(coordsObj);
      }

      instance.PluginHooks.run('afterOnCellMouseDown', event, coords, TD);
    },
    /*onCellMouseOut: function (/*event, coords, TD* /) {
     if (isMouseDown && that.settings.fragmentSelection === 'single') {
     clearTextSelection(); //otherwise text selection blinks during multiple cells selection
     }
     },*/
    onCellMouseOver: function (event, coords, TD) {
      var coordsObj = {row: coords[0], col: coords[1]};
      if (isMouseDown) {
        /*if (that.settings.fragmentSelection === 'single') {
         clearTextSelection(); //otherwise text selection blinks during multiple cells selection
         }*/
        instance.selection.setRangeEnd(coordsObj);
      }
      else if (instance.autofill.handle && instance.autofill.handle.isDragged) {
        instance.autofill.handle.isDragged++;
        instance.autofill.showBorder(coords);
      }
      instance.PluginHooks.run('afterOnCellMouseOver', event, coords, TD);
    },
    onCellCornerMouseDown: function (event) {
      instance.autofill.handle.isDragged = 1;
      event.preventDefault();
      instance.PluginHooks.run('afterOnCellCornerMouseDown', event);
    },
    onCellCornerDblClick: function () {
      instance.autofill.selectAdjacent();
    },
    beforeDraw: function (force) {
      that.beforeRender(force);
    },
    onDraw: function(force){
      that.onDraw(force);
    },
    onScrollVertically: function () {
      instance.runHooks('afterScrollVertically');
    },
    onScrollHorizontally: function () {
      instance.runHooks('afterScrollHorizontally');
    }
  };

  instance.PluginHooks.run('beforeInitWalkontable', walkontableConfig);

  this.wt = new Walkontable(walkontableConfig);

  $window.on('resize.' + instance.guid, function () {
    instance.registerTimeout('resizeTimeout', function () {
      instance.parseSettingsFromDOM();
      var newWidth = that.getWidth();
      var newHeight = that.getHeight();
      if (walkontableConfig.width !== newWidth || walkontableConfig.height !== newHeight) {
        instance.forceFullRender = true;
        that.render();
        walkontableConfig.width = newWidth;
        walkontableConfig.height = newHeight;
      }
    }, 60);
  });

  $(that.wt.wtTable.spreader).on('mousedown.handsontable, contextmenu.handsontable', function (event) {
    if (event.target === that.wt.wtTable.spreader && event.which === 3) { //right mouse button exactly on spreader means right clickon the right hand side of vertical scrollbar
      event.stopPropagation();
    }
  });

  $documentElement.on('click.' + instance.guid, function () {
    if (that.settings.observeDOMVisibility) {
      if (that.wt.drawInterrupted) {
        that.instance.forceFullRender = true;
        that.render();
      }
    }
  });
};

Handsontable.TableView.prototype.isTextSelectionAllowed = function (el) {
  if ( Handsontable.helper.isInput(el) ) {
    return (true);
  }
  if (this.settings.fragmentSelection && this.wt.wtDom.isChildOf(el, this.TBODY)) {
    return (true);
  }
  return false;
};

Handsontable.TableView.prototype.isCellEdited = function () {
  var activeEditor = this.instance.getActiveEditor();
  return activeEditor && activeEditor.isOpened();
};

Handsontable.TableView.prototype.getWidth = function () {
  var val = this.settings.width !== void 0 ? this.settings.width : this.settingsFromDOM.width;
  return typeof val === 'function' ? val() : val;
};

Handsontable.TableView.prototype.getHeight = function () {
  var val = this.settings.height !== void 0 ? this.settings.height : this.settingsFromDOM.height;
  return typeof val === 'function' ? val() : val;
};

Handsontable.TableView.prototype.beforeRender = function (force) {
  if (force) { //force = did Walkontable decide to do full render
    this.instance.PluginHooks.run('beforeRender', this.instance.forceFullRender); //this.instance.forceFullRender = did Handsontable request full render?
    this.wt.update('width', this.getWidth());
    this.wt.update('height', this.getHeight());
  }
};

Handsontable.TableView.prototype.onDraw = function(force){
  if (force) { //force = did Walkontable decide to do full render
    this.instance.PluginHooks.run('afterRender', this.instance.forceFullRender); //this.instance.forceFullRender = did Handsontable request full render?
  }
};

Handsontable.TableView.prototype.render = function () {
  this.wt.draw(!this.instance.forceFullRender);
  this.instance.forceFullRender = false;
  this.instance.rootElement.triggerHandler('render.handsontable');
};

/**
 * Returns td object given coordinates
 */
Handsontable.TableView.prototype.getCellAtCoords = function (coords) {
  var td = this.wt.wtTable.getCell([coords.row, coords.col]);
  if (td < 0) { //there was an exit code (cell is out of bounds)
    return null;
  }
  else {
    return td;
  }
};

/**
 * Scroll viewport to selection
 * @param coords
 */
Handsontable.TableView.prototype.scrollViewport = function (coords) {
  this.wt.scrollViewport([coords.row, coords.col]);
};

/**
 * Append row header to a TH element
 * @param row
 * @param TH
 */
Handsontable.TableView.prototype.appendRowHeader = function (row, TH) {
  if (row > -1) {
    this.wt.wtDom.fastInnerHTML(TH, this.instance.getRowHeader(row));
  }
  else {
    var DIV = document.createElement('DIV');
    DIV.className = 'relative';
    this.wt.wtDom.fastInnerText(DIV, '\u00A0');
    this.wt.wtDom.empty(TH);
    TH.appendChild(DIV);
  }
};

/**
 * Append column header to a TH element
 * @param col
 * @param TH
 */
Handsontable.TableView.prototype.appendColHeader = function (col, TH) {
  var DIV = document.createElement('DIV')
    , SPAN = document.createElement('SPAN');

  DIV.className = 'relative';
  SPAN.className = 'colHeader';

  this.wt.wtDom.fastInnerHTML(SPAN, this.instance.getColHeader(col));
  DIV.appendChild(SPAN);

  this.wt.wtDom.empty(TH);
  TH.appendChild(DIV);
  this.instance.PluginHooks.run('afterGetColHeader', col, TH);
};

/**
 * Given a element's left position relative to the viewport, returns maximum element width until the right edge of the viewport (before scrollbar)
 * @param {Number} left
 * @return {Number}
 */
Handsontable.TableView.prototype.maximumVisibleElementWidth = function (left) {
  var rootWidth = this.wt.wtViewport.getWorkspaceWidth();
  if(this.settings.nativeScrollbars) {
    return rootWidth;
  }
  return rootWidth - left;
};

/**
 * Given a element's top position relative to the viewport, returns maximum element height until the bottom edge of the viewport (before scrollbar)
 * @param {Number} top
 * @return {Number}
 */
Handsontable.TableView.prototype.maximumVisibleElementHeight = function (top) {
  var rootHeight = this.wt.wtViewport.getWorkspaceHeight();
  if(this.settings.nativeScrollbars) {
    return rootHeight;
  }
  return rootHeight - top;
};

/**
 * Utility to register editors and common namespace for keeping reference to all editor classes
 */
(function (Handsontable) {
  'use strict';

  function RegisteredEditor(editorClass) {
    var clazz, instances;

    instances = {};
    clazz = editorClass;

    this.getInstance = function (hotInstance) {
      if (!(hotInstance.guid in instances)) {
        instances[hotInstance.guid] = new clazz(hotInstance);
      }

      return instances[hotInstance.guid];
    }

  }

  var registeredEditorNames = {};
  var registeredEditorClasses = new WeakMap();

  Handsontable.editors = {

    /**
     * Registers editor under given name
     * @param {String} editorName
     * @param {Function} editorClass
     */
    registerEditor: function (editorName, editorClass) {
      var editor = new RegisteredEditor(editorClass);
      if (typeof editorName === "string") {
        registeredEditorNames[editorName] = editor;
      }
      registeredEditorClasses.set(editorClass, editor);
    },

    /**
     * Returns instance (singleton) of editor class
     * @param {String|Function} editorName/editorClass
     * @returns {Function} editorClass
     */
    getEditor: function (editorName, hotInstance) {
      var editor;
      if (typeof editorName == 'function') {
        if (!(registeredEditorClasses.get(editorName))) {
          this.registerEditor(null, editorName);
        }
        editor = registeredEditorClasses.get(editorName);
      }
      else if (typeof editorName == 'string') {
        editor = registeredEditorNames[editorName];
      }
      else {
        throw Error('Only strings and functions can be passed as "editor" parameter ');
      }

      if (!editor) {
        throw Error('No editor registered under name "' + editorName + '"');
      }

      return editor.getInstance(hotInstance);
    }

  };


})(Handsontable);

(function(Handsontable){
  'use strict';

  Handsontable.EditorManager = function(instance, priv, selection){
    var that = this;
    var $document = $(document);
    var keyCodes = Handsontable.helper.keyCode;

    var activeEditor;

    var init = function () {

      function onKeyDown(event) {

        if (!instance.isListening()) {
          return;
        }

        if (priv.settings.beforeOnKeyDown) { // HOT in HOT Plugin
          priv.settings.beforeOnKeyDown.call(instance, event);
        }

        instance.PluginHooks.run('beforeKeyDown', event);

        if (!event.isImmediatePropagationStopped()) {

          priv.lastKeyCode = event.keyCode;
          if (selection.isSelected()) {
            var ctrlDown = (event.ctrlKey || event.metaKey) && !event.altKey; //catch CTRL but not right ALT (which in some systems triggers ALT+CTRL)

            if (!activeEditor.isWaiting()) {
              if (!Handsontable.helper.isMetaKey(event.keyCode) && !ctrlDown) {
                that.openEditor('');
                event.stopPropagation(); //required by HandsontableEditor
                return;
              }
            }

            var rangeModifier = event.shiftKey ? selection.setRangeEnd : selection.setRangeStart;

              switch (event.keyCode) {

                case keyCodes.A:
                  if (ctrlDown) {
                    selection.selectAll(); //select all cells

                    event.preventDefault();
                    event.stopPropagation();
                    break;
                  }

                case keyCodes.ARROW_UP:

                  if (that.isEditorOpened() && !activeEditor.isWaiting()){
                    that.closeEditorAndSaveChanges(ctrlDown);
                  }

                  moveSelectionUp(event.shiftKey);

                  event.preventDefault();
                  event.stopPropagation(); //required by HandsontableEditor
                  break;

                case keyCodes.ARROW_DOWN:
                  if (that.isEditorOpened() && !activeEditor.isWaiting()){
                    that.closeEditorAndSaveChanges(ctrlDown);
                  }

                  moveSelectionDown(event.shiftKey);

                  event.preventDefault();
                  event.stopPropagation(); //required by HandsontableEditor
                  break;

                case keyCodes.ARROW_RIGHT:
                  if(that.isEditorOpened()  && !activeEditor.isWaiting()){
                    that.closeEditorAndSaveChanges(ctrlDown);
                  }

                  moveSelectionRight(event.shiftKey);

                  event.preventDefault();
                  event.stopPropagation(); //required by HandsontableEditor
                  break;

                case keyCodes.ARROW_LEFT:
                  if(that.isEditorOpened() && !activeEditor.isWaiting()){
                    that.closeEditorAndSaveChanges(ctrlDown);
                  }

                  moveSelectionLeft(event.shiftKey);

                  event.preventDefault();
                  event.stopPropagation(); //required by HandsontableEditor
                  break;

                case keyCodes.TAB:
                  var tabMoves = typeof priv.settings.tabMoves === 'function' ? priv.settings.tabMoves(event) : priv.settings.tabMoves;
                  if (event.shiftKey) {
                    selection.transformStart(-tabMoves.row, -tabMoves.col); //move selection left
                  }
                  else {
                    selection.transformStart(tabMoves.row, tabMoves.col, true); //move selection right (add a new column if needed)
                  }
                  event.preventDefault();
                  event.stopPropagation(); //required by HandsontableEditor
                  break;

                case keyCodes.BACKSPACE:
                case keyCodes.DELETE:
                  selection.empty(event);
                  that.prepareEditor();
                  event.preventDefault();
                  break;

                case keyCodes.F2: /* F2 */
                  that.openEditor();
                  event.preventDefault(); //prevent Opera from opening Go to Page dialog
                  break;

                case keyCodes.ENTER: /* return/enter */
                  if(that.isEditorOpened()){

                    if (activeEditor.state !== Handsontable.EditorState.WAITING){
                      that.closeEditorAndSaveChanges(ctrlDown);
                    }

                    moveSelectionAfterEnter(event.shiftKey);

                  } else {

                    if (instance.getSettings().enterBeginsEditing){
                      that.openEditor();
                    } else {
                      moveSelectionAfterEnter(event.shiftKey);
                    }

                  }

                  event.preventDefault(); //don't add newline to field
                  event.stopImmediatePropagation(); //required by HandsontableEditor
                  break;

                case keyCodes.ESCAPE:
                  if(that.isEditorOpened()){
                    that.closeEditorAndRestoreOriginalValue(ctrlDown);
                  }
                  event.preventDefault();
                  break;

                case keyCodes.HOME:
                  if (event.ctrlKey || event.metaKey) {
                    rangeModifier({row: 0, col: priv.selStart.col()});
                  }
                  else {
                    rangeModifier({row: priv.selStart.row(), col: 0});
                  }
                  event.preventDefault(); //don't scroll the window
                  event.stopPropagation(); //required by HandsontableEditor
                  break;

                case keyCodes.END:
                  if (event.ctrlKey || event.metaKey) {
                    rangeModifier({row: instance.countRows() - 1, col: priv.selStart.col()});
                  }
                  else {
                    rangeModifier({row: priv.selStart.row(), col: instance.countCols() - 1});
                  }
                  event.preventDefault(); //don't scroll the window
                  event.stopPropagation(); //required by HandsontableEditor
                  break;

                case keyCodes.PAGE_UP:
                  selection.transformStart(-instance.countVisibleRows(), 0);
                  instance.view.wt.scrollVertical(-instance.countVisibleRows());
                  instance.view.render();
                  event.preventDefault(); //don't page up the window
                  event.stopPropagation(); //required by HandsontableEditor
                  break;

                case keyCodes.PAGE_DOWN:
                  selection.transformStart(instance.countVisibleRows(), 0);
                  instance.view.wt.scrollVertical(instance.countVisibleRows());
                  instance.view.render();
                  event.preventDefault(); //don't page down the window
                  event.stopPropagation(); //required by HandsontableEditor
                  break;

                default:
                  break;
              }

          }
        }
      }
      $document.on('keydown.handsontable.' + instance.guid, onKeyDown);

      function onDblClick() {
//        that.instance.destroyEditor();
        that.openEditor();
      }

      instance.view.wt.update('onCellDblClick', onDblClick);

      instance.addHook('afterDestroy', function(){
        $document.off('keydown.handsontable.' + instance.guid);
      });

      function moveSelectionAfterEnter(shiftKey){
        var enterMoves = typeof priv.settings.enterMoves === 'function' ? priv.settings.enterMoves(event) : priv.settings.enterMoves;

        if (shiftKey) {
          selection.transformStart(-enterMoves.row, -enterMoves.col); //move selection up
        }
        else {
          selection.transformStart(enterMoves.row, enterMoves.col, true); //move selection down (add a new row if needed)
        }
      }

      function moveSelectionUp(shiftKey){
        if (shiftKey) {
          selection.transformEnd(-1, 0);
        }
        else {
          selection.transformStart(-1, 0);
        }
      }

      function moveSelectionDown(shiftKey){
        if (shiftKey) {
          selection.transformEnd(1, 0); //expanding selection down with shift
        }
        else {
          selection.transformStart(1, 0); //move selection down
        }
      }

      function moveSelectionRight(shiftKey){
        if (shiftKey) {
          selection.transformEnd(0, 1);
        }
        else {
          selection.transformStart(0, 1);
        }
      }

      function moveSelectionLeft(shiftKey){
        if (shiftKey) {
          selection.transformEnd(0, -1);
        }
        else {
          selection.transformStart(0, -1);
        }
      }
    };

    /**
     * Destroy current editor, if exists
     * @param {Boolean} revertOriginal
     */
    this.destroyEditor = function (revertOriginal) {
      this.closeEditor(revertOriginal);
    };

    this.getActiveEditor = function () {
      return activeEditor;
    };

    /**
     * Prepare text input to be displayed at given grid cell
     */
    this.prepareEditor = function () {

      if (activeEditor && activeEditor.isWaiting()){

        this.closeEditor(false, false, function(dataSaved){
          if(dataSaved){
            that.prepareEditor();
          }
        });

        return;
      }

      var row = priv.selStart.row();
      var col = priv.selStart.col();
      var prop = instance.colToProp(col);
      var td = instance.getCell(row, col);
      var originalValue = instance.getDataAtCell(row, col);
      var cellProperties = instance.getCellMeta(row, col);

      var editorClass = instance.getCellEditor(cellProperties);
      activeEditor = Handsontable.editors.getEditor(editorClass, instance);

      activeEditor.prepare(row, col, prop, td, originalValue, cellProperties);

    };

    this.isEditorOpened = function () {
      return activeEditor.isOpened();
    };

    this.openEditor = function (initialValue) {
      activeEditor.beginEditing(initialValue);
    };

    this.closeEditor = function (restoreOriginalValue, ctrlDown, callback) {

      if (!activeEditor){
        if(callback) {
          callback(false);
        }
      }
      else {
        activeEditor.finishEditing(restoreOriginalValue, ctrlDown, callback);
      }
    };

    this.closeEditorAndSaveChanges = function(ctrlDown){
      return this.closeEditor(false, ctrlDown);
    };

    this.closeEditorAndRestoreOriginalValue = function(ctrlDown){
      return this.closeEditor(true, ctrlDown);
    };

    init();
  };

})(Handsontable);

/**
 * Utility to register renderers and common namespace for keeping reference to all renderers classes
 */
(function (Handsontable) {
  'use strict';

  var registeredRenderers = {};

  Handsontable.renderers = {

    /**
     * Registers renderer under given name
     * @param {String} rendererName
     * @param {Function} rendererFunction
     */
    registerRenderer: function (rendererName, rendererFunction) {
      registeredRenderers[rendererName] = rendererFunction
    },

    /**
     * @param {String|Function} rendererName/rendererFunction
     * @returns {Function} rendererFunction
     */
    getRenderer: function (rendererName) {
      if (typeof rendererName == 'function'){
        return rendererName;
      }

      if (typeof rendererName != 'string'){
        throw Error('Only strings and functions can be passed as "renderer" parameter ');
      }

      if (!(rendererName in registeredRenderers)) {
        throw Error('No editor registered under name "' + rendererName + '"');
      }

      return registeredRenderers[rendererName];
    }

  };


})(Handsontable);

/**
 * DOM helper optimized for maximum performance
 * It is recommended for Handsontable plugins and renderers, because it is much faster than jQuery
 * @type {WalkonableDom}
 */
Handsontable.Dom = new WalkontableDom();

/**
 * Returns true if keyCode represents a printable character
 * @param {Number} keyCode
 * @return {Boolean}
 */
Handsontable.helper.isPrintableChar = function (keyCode) {
  return ((keyCode == 32) || //space
    (keyCode >= 48 && keyCode <= 57) || //0-9
    (keyCode >= 96 && keyCode <= 111) || //numpad
    (keyCode >= 186 && keyCode <= 192) || //;=,-./`
    (keyCode >= 219 && keyCode <= 222) || //[]{}\|"'
    keyCode >= 226 || //special chars (229 for Asian chars)
    (keyCode >= 65 && keyCode <= 90)); //a-z
};

Handsontable.helper.isMetaKey = function (keyCode) {
  var keyCodes = Handsontable.helper.keyCode;
  var metaKeys = [
    keyCodes.ARROW_DOWN,
    keyCodes.ARROW_UP,
    keyCodes.ARROW_LEFT,
    keyCodes.ARROW_RIGHT,
    keyCodes.HOME,
    keyCodes.END,
    keyCodes.DELETE,
    keyCodes.BACKSPACE,
    keyCodes.F1,
    keyCodes.F2,
    keyCodes.F3,
    keyCodes.F4,
    keyCodes.F5,
    keyCodes.F6,
    keyCodes.F7,
    keyCodes.F8,
    keyCodes.F9,
    keyCodes.F10,
    keyCodes.F11,
    keyCodes.F12,
    keyCodes.TAB,
    keyCodes.PAGE_DOWN,
    keyCodes.PAGE_UP,
    keyCodes.ENTER,
    keyCodes.ESCAPE,
    keyCodes.SHIFT,
    keyCodes.CAPS_LOCK,
    keyCodes.ALT
  ];

  return metaKeys.indexOf(keyCode) != -1;
};

Handsontable.helper.isCtrlKey = function (keyCode) {

  var keys = Handsontable.helper.keyCode;

  return [keys.CONTROL_LEFT, 224, keys.COMMAND_LEFT, keys.COMMAND_RIGHT].indexOf(keyCode) != -1;
};

/**
 * Converts a value to string
 * @param value
 * @return {String}
 */
Handsontable.helper.stringify = function (value) {
  switch (typeof value) {
    case 'string':
    case 'number':
      return value + '';
      break;

    case 'object':
      if (value === null) {
        return '';
      }
      else {
        return value.toString();
      }
      break;

    case 'undefined':
      return '';
      break;

    default:
      return value.toString();
  }
};

/**
 * Generates spreadsheet-like column names: A, B, C, ..., Z, AA, AB, etc
 * @param index
 * @returns {String}
 */
Handsontable.helper.spreadsheetColumnLabel = function (index) {
  var dividend = index + 1;
  var columnLabel = '';
  var modulo;
  while (dividend > 0) {
    modulo = (dividend - 1) % 26;
    columnLabel = String.fromCharCode(65 + modulo) + columnLabel;
    dividend = parseInt((dividend - modulo) / 26, 10);
  }
  return columnLabel;
};

/**
 * Checks if value of n is a numeric one
 * http://jsperf.com/isnan-vs-isnumeric/4
 * @param n
 * @returns {boolean}
 */
Handsontable.helper.isNumeric = function (n) {
    var t = typeof n;
    return t == 'number' ? !isNaN(n) && isFinite(n) :
           t == 'string' ? !n.length ? false :
           n.length == 1 ? /\d/.test(n) :
           /^\s*[+-]?\s*(?:(?:\d+(?:\.\d+)?(?:e[+-]?\d+)?)|(?:0x[a-f\d]+))\s*$/i.test(n) :
           t == 'object' ? !!n && typeof n.valueOf() == "number" && !(n instanceof Date) : false;
};

Handsontable.helper.isArray = function (obj) {
  return Object.prototype.toString.call(obj).match(/array/i) !== null;
};

/**
 * Checks if child is a descendant of given parent node
 * http://stackoverflow.com/questions/2234979/how-to-check-in-javascript-if-one-element-is-a-child-of-another
 * @param parent
 * @param child
 * @returns {boolean}
 */
Handsontable.helper.isDescendant = function (parent, child) {
  var node = child.parentNode;
  while (node != null) {
    if (node == parent) {
      return true;
    }
    node = node.parentNode;
  }
  return false;
};

/**
 * Generates a random hex string. Used as namespace for Handsontable instance events.
 * @return {String} - 16 character random string: "92b1bfc74ec4"
 */
Handsontable.helper.randomString = function () {
  return walkontableRandomString();
};

/**
 * Inherit without without calling parent constructor, and setting `Child.prototype.constructor` to `Child` instead of `Parent`.
 * Creates temporary dummy function to call it as constructor.
 * Described in ticket: https://github.com/warpech/jquery-handsontable/pull/516
 * @param  {Object} Child  child class
 * @param  {Object} Parent parent class
 * @return {Object}        extended Child
 */
Handsontable.helper.inherit = function (Child, Parent) {
  Parent.prototype.constructor = Parent;
  Child.prototype = new Parent();
  Child.prototype.constructor = Child;
  return Child;
};

/**
 * Perform shallow extend of a target object with extension's own properties
 * @param {Object} target An object that will receive the new properties
 * @param {Object} extension An object containing additional properties to merge into the target
 */
Handsontable.helper.extend = function (target, extension) {
  for (var i in extension) {
    if (extension.hasOwnProperty(i)) {
      target[i] = extension[i];
    }
  }
};

Handsontable.helper.getPrototypeOf = function (obj) {
  var prototype;

  if(typeof obj.__proto__ == "object"){
    prototype = obj.__proto__;
  } else {
    var oldConstructor,
        constructor = obj.constructor;

    if (typeof obj.constructor == "function") {
      oldConstructor = constructor;

      if (delete obj.constructor){
        constructor = obj.constructor; // get real constructor
        obj.constructor = oldConstructor; // restore constructor
      }


    }

    prototype = constructor ? constructor.prototype : null; // needed for IE

  }

  return prototype;
};

/**
 * Factory for columns constructors.
 * @param {Object} GridSettings
 * @param {Array} conflictList
 * @return {Object} ColumnSettings
 */
Handsontable.helper.columnFactory = function (GridSettings, conflictList) {
  function ColumnSettings () {}

  Handsontable.helper.inherit(ColumnSettings, GridSettings);

  // Clear conflict settings
  for (var i = 0, len = conflictList.length; i < len; i++) {
    ColumnSettings.prototype[conflictList[i]] = void 0;
  }

  return ColumnSettings;
};

Handsontable.helper.translateRowsToColumns = function (input) {
  var i
    , ilen
    , j
    , jlen
    , output = []
    , olen = 0;

  for (i = 0, ilen = input.length; i < ilen; i++) {
    for (j = 0, jlen = input[i].length; j < jlen; j++) {
      if (j == olen) {
        output.push([]);
        olen++;
      }
      output[j].push(input[i][j])
    }
  }
  return output;
};

Handsontable.helper.to2dArray = function (arr) {
  var i = 0
    , ilen = arr.length;
  while (i < ilen) {
    arr[i] = [arr[i]];
    i++;
  }
};

Handsontable.helper.extendArray = function (arr, extension) {
  var i = 0
    , ilen = extension.length;
  while (i < ilen) {
    arr.push(extension[i]);
    i++;
  }
};

/**
 * Determines if the given DOM element is an input field.
 * Notice: By 'input' we mean input, textarea and select nodes
 * @param element - DOM element
 * @returns {boolean}
 */
Handsontable.helper.isInput = function (element) {
  var inputs = ['INPUT', 'SELECT', 'TEXTAREA'];

  return inputs.indexOf(element.nodeName) > -1;
}

/**
 * Determines if the given DOM element is an input field placed OUTSIDE of HOT.
 * Notice: By 'input' we mean input, textarea and select nodes
 * @param element - DOM element
 * @returns {boolean}
 */
Handsontable.helper.isOutsideInput = function (element) {
  return Handsontable.helper.isInput(element) && element.className.indexOf('handsontableInput') == -1;
};

Handsontable.helper.keyCode = {
  MOUSE_LEFT: 1,
  MOUSE_RIGHT: 3,
  MOUSE_MIDDLE: 2,
  BACKSPACE: 8,
  COMMA: 188,
  DELETE: 46,
  END: 35,
  ENTER: 13,
  ESCAPE: 27,
  CONTROL_LEFT: 91,
  COMMAND_LEFT: 17,
  COMMAND_RIGHT: 93,
  ALT: 18,
  HOME: 36,
  PAGE_DOWN: 34,
  PAGE_UP: 33,
  PERIOD: 190,
  SPACE: 32,
  SHIFT: 16,
  CAPS_LOCK: 20,
  TAB: 9,
  ARROW_RIGHT: 39,
  ARROW_LEFT: 37,
  ARROW_UP: 38,
  ARROW_DOWN: 40,
  F1: 112,
  F2: 113,
  F3: 114,
  F4: 115,
  F5: 116,
  F6: 117,
  F7: 118,
  F8: 119,
  F9: 120,
  F10: 121,
  F11: 122,
  F12: 123,
  A: 65,
  X: 88,
  C: 67,
  V: 86
};

/**
 * Determines whether given object is a plain Object.
 * Note: String and Array are not plain Objects
 * @param {*} obj
 * @returns {boolean}
 */
Handsontable.helper.isObject = function (obj) {
  return Object.prototype.toString.call(obj) == '[object Object]';
};

/**
 * Determines whether given object is an Array.
 * Note: String is not an Array
 * @param {*} obj
 * @returns {boolean}
 */
Handsontable.helper.isArray = function(obj){
  return Array.isArray ? Array.isArray(obj) : Object.prototype.toString.call(obj) == '[object Array]';
};

Handsontable.helper.pivot = function (arr) {
  var pivotedArr = [];

  if(!arr || arr.length == 0 || !arr[0] || arr[0].length == 0){
    return pivotedArr;
  }

  var rowCount = arr.length;
  var colCount = arr[0].length;

  for(var i = 0; i < rowCount; i++){
    for(var j = 0; j < colCount; j++){
      if(!pivotedArr[j]){
        pivotedArr[j] = [];
      }

      pivotedArr[j][i] = arr[i][j];
    }
  }

  return pivotedArr;

};

Handsontable.helper.proxy = function (fun, context) {
  return function () {
    return fun.apply(context, arguments);
  };
};

/**
 * Factory that produces a function for searching methods (or any properties) which could be defined directly in
 * table configuration or implicitly, within cell type definition.
 *
 * For example: renderer can be defined explicitly using "renderer" property in column configuration or it can be
 * defined implicitly using "type" property.
 *
 * Methods/properties defined explicitly always takes precedence over those defined through "type".
 *
 * If the method/property is not found in an object, searching is continued recursively through prototype chain, until
 * it reaches the Object.prototype.
 *
 *
 * @param methodName {String} name of the method/property to search (i.e. 'renderer', 'validator', 'copyable')
 * @param allowUndefined {Boolean} [optional] if false, the search is continued if methodName has not been found in cell "type"
 * @returns {Function}
 */
Handsontable.helper.cellMethodLookupFactory = function (methodName, allowUndefined) {

  allowUndefined = typeof allowUndefined == 'undefined' ? true : allowUndefined;

  return function cellMethodLookup (row, col) {

    return (function getMethodFromProperties(properties) {

      if (!properties){

        return;                       //method not found

      }
      else if (properties.hasOwnProperty(methodName) && properties[methodName]) { //check if it is own and is not empty

        return properties[methodName];  //method defined directly

      } else if (properties.hasOwnProperty('type') && properties.type) { //check if it is own and is not empty

        var type;

        if(typeof properties.type != 'string' ){
          throw new Error('Cell type must be a string ');
        }

        type = translateTypeNameToObject(properties.type);

        if (type.hasOwnProperty(methodName)) {
          return type[methodName]; //method defined in type.
        } else if (allowUndefined) {
          return; //method does not defined in type (eg. validator), returns undefined
        }

      }

      return getMethodFromProperties(Handsontable.helper.getPrototypeOf(properties));

    })(typeof row == 'number' ? this.getCellMeta(row, col) : row);

  };

  function translateTypeNameToObject(typeName) {
    var type = Handsontable.cellTypes[typeName];

    if(typeof type == 'undefined'){
      throw new Error('You declared cell type "' + typeName + '" as a string that is not mapped to a known object. Cell type must be an object or a string mapped to an object in Handsontable.cellTypes');
    }

    return type;
  }
};
Handsontable.SelectionPoint = function () {
  this._row = null; //private use intended
  this._col = null;
};

Handsontable.SelectionPoint.prototype.exists = function () {
  return (this._row !== null);
};

Handsontable.SelectionPoint.prototype.row = function (val) {
  if (val !== void 0) {
    this._row = val;
  }
  return this._row;
};

Handsontable.SelectionPoint.prototype.col = function (val) {
  if (val !== void 0) {
    this._col = val;
  }
  return this._col;
};

Handsontable.SelectionPoint.prototype.coords = function (coords) {
  if (coords !== void 0) {
    this._row = coords.row;
    this._col = coords.col;
  }
  return {
    row: this._row,
    col: this._col
  }
};

Handsontable.SelectionPoint.prototype.arr = function (arr) {
  if (arr !== void 0) {
    this._row = arr[0];
    this._col = arr[1];
  }
  return [this._row, this._col]
};
(function (Handsontable) {
  'use strict';

  /**
   * Utility class that gets and saves data from/to the data source using mapping of columns numbers to object property names
   * TODO refactor arguments of methods getRange, getText to be numbers (not objects)
   * TODO remove priv, GridSettings from object constructor
   *
   * @param instance
   * @param priv
   * @param GridSettings
   * @constructor
   */
  Handsontable.DataMap = function (instance, priv, GridSettings) {
    this.instance = instance;
    this.priv = priv;
    this.GridSettings = GridSettings;
    this.dataSource = this.instance.getSettings().data;

    if (this.dataSource[0]) {
      this.duckSchema = this.recursiveDuckSchema(this.dataSource[0]);
    }
    else {
      this.duckSchema = {};
    }
    this.createMap();

    this.getVars = {}; //used by modifier
    this.setVars = {}; //used by modifier
  };

  Handsontable.DataMap.prototype.DESTINATION_RENDERER = 1;
  Handsontable.DataMap.prototype.DESTINATION_CLIPBOARD_GENERATOR = 2;

  Handsontable.DataMap.prototype.recursiveDuckSchema = function (obj) {
    var schema;
    if ($.isPlainObject(obj)) {
      schema = {};
      for (var i in obj) {
        if (obj.hasOwnProperty(i)) {
          if ($.isPlainObject(obj[i])) {
            schema[i] = this.recursiveDuckSchema(obj[i]);
          }
          else {
            schema[i] = null;
          }
        }
      }
    }
    else {
      schema = [];
    }
    return schema;
  };

  Handsontable.DataMap.prototype.recursiveDuckColumns = function (schema, lastCol, parent) {
    var prop, i;
    if (typeof lastCol === 'undefined') {
      lastCol = 0;
      parent = '';
    }
    if ($.isPlainObject(schema)) {
      for (i in schema) {
        if (schema.hasOwnProperty(i)) {
          if (schema[i] === null) {
            prop = parent + i;
            this.colToPropCache.push(prop);
            this.propToColCache[prop] = lastCol;
            lastCol++;
          }
          else {
            lastCol = this.recursiveDuckColumns(schema[i], lastCol, i + '.');
          }
        }
      }
    }
    return lastCol;
  };

  Handsontable.DataMap.prototype.createMap = function () {
    if (typeof this.getSchema() === "undefined") {
      throw new Error("trying to create `columns` definition but you didnt' provide `schema` nor `data`");
    }
    var i, ilen, schema = this.getSchema();
    this.colToPropCache = [];
    this.propToColCache = {};
    var columns = this.instance.getSettings().columns;
    if (columns) {
      for (i = 0, ilen = columns.length; i < ilen; i++) {
        this.colToPropCache[i] = columns[i].data;
        this.propToColCache[columns[i].data] = i;
      }
    }
    else {
      this.recursiveDuckColumns(schema);
    }
  };

  Handsontable.DataMap.prototype.colToProp = function (col) {
    col = Handsontable.PluginHooks.execute(this.instance, 'modifyCol', col);
    if (this.colToPropCache && typeof this.colToPropCache[col] !== 'undefined') {
      return this.colToPropCache[col];
    }
    else {
      return col;
    }
  };

  Handsontable.DataMap.prototype.propToCol = function (prop) {
    var col;
    if (typeof this.propToColCache[prop] !== 'undefined') {
      col = this.propToColCache[prop];
    }
    else {
      col = prop;
    }
    col = Handsontable.PluginHooks.execute(this.instance, 'modifyCol', col);
    return col;
  };

  Handsontable.DataMap.prototype.getSchema = function () {
    var schema = this.instance.getSettings().dataSchema;
    if (schema) {
      if (typeof schema === 'function') {
        return schema();
      }
      return schema;
    }
    return this.duckSchema;
  };

  /**
   * Creates row at the bottom of the data array
   * @param {Number} [index] Optional. Index of the row before which the new row will be inserted
   */
  Handsontable.DataMap.prototype.createRow = function (index, amount, createdAutomatically) {
    var row
      , colCount = this.instance.countCols()
      , numberOfCreatedRows = 0
      , currentIndex;

    if (!amount) {
      amount = 1;
    }

    if (typeof index !== 'number' || index >= this.instance.countRows()) {
      index = this.instance.countRows();
    }

    currentIndex = index;
    var maxRows = this.instance.getSettings().maxRows;
    while (numberOfCreatedRows < amount && this.instance.countRows() < maxRows) {

      if (this.instance.dataType === 'array') {
        row = [];
        for (var c = 0; c < colCount; c++) {
          row.push(null);
        }
      }
      else if (this.instance.dataType === 'function') {
        row = this.instance.getSettings().dataSchema(index);
      }
      else {
        row = $.extend(true, {}, this.getSchema());
      }

      if (index === this.instance.countRows()) {
        this.dataSource.push(row);
      }
      else {
        this.dataSource.splice(index, 0, row);
      }

      numberOfCreatedRows++;
      currentIndex++;
    }


    this.instance.PluginHooks.run('afterCreateRow', index, numberOfCreatedRows, createdAutomatically);
    this.instance.forceFullRender = true; //used when data was changed

    return numberOfCreatedRows;
  };

  /**
   * Creates col at the right of the data array
   * @param {Number} [index] Optional. Index of the column before which the new column will be inserted
   *   * @param {Number} [amount] Optional.
   */
  Handsontable.DataMap.prototype.createCol = function (index, amount, createdAutomatically) {
    if (this.instance.dataType === 'object' || this.instance.getSettings().columns) {
      throw new Error("Cannot create new column. When data source in an object, " +
        "you can only have as much columns as defined in first data row, data schema or in the 'columns' setting." +
        "If you want to be able to add new columns, you have to use array datasource.");
    }
    var rlen = this.instance.countRows()
      , data = this.dataSource
      , constructor
      , numberOfCreatedCols = 0
      , currentIndex;

    if (!amount) {
      amount = 1;
    }

    currentIndex = index;

    var maxCols = this.instance.getSettings().maxCols;
    while (numberOfCreatedCols < amount && this.instance.countCols() < maxCols) {
      constructor = Handsontable.helper.columnFactory(this.GridSettings, this.priv.columnsSettingConflicts);
      if (typeof index !== 'number' || index >= this.instance.countCols()) {
        for (var r = 0; r < rlen; r++) {
          if (typeof data[r] === 'undefined') {
            data[r] = [];
          }
          data[r].push(null);
        }
        // Add new column constructor
        this.priv.columnSettings.push(constructor);
      }
      else {
        for (var r = 0; r < rlen; r++) {
          data[r].splice(currentIndex, 0, null);
        }
        // Add new column constructor at given index
        this.priv.columnSettings.splice(currentIndex, 0, constructor);
      }

      numberOfCreatedCols++;
      currentIndex++;
    }

    this.instance.PluginHooks.run('afterCreateCol', index, numberOfCreatedCols, createdAutomatically);
    this.instance.forceFullRender = true; //used when data was changed

    return numberOfCreatedCols;
  };

  /**
   * Removes row from the data array
   * @param {Number} [index] Optional. Index of the row to be removed. If not provided, the last row will be removed
   * @param {Number} [amount] Optional. Amount of the rows to be removed. If not provided, one row will be removed
   */
  Handsontable.DataMap.prototype.removeRow = function (index, amount) {
    if (!amount) {
      amount = 1;
    }
    if (typeof index !== 'number') {
      index = -amount;
    }

    index = (this.instance.countRows() + index) % this.instance.countRows();

    // We have to map the physical row ids to logical and than perform removing with (possibly) new row id
    var logicRows = this.physicalRowsToLogical(index, amount);

    var actionWasNotCancelled = this.instance.PluginHooks.execute('beforeRemoveRow', index, amount);

    if (actionWasNotCancelled === false) {
      return;
    }

    var data = this.dataSource;
    var newData = data.filter(function (row, index) {
      return logicRows.indexOf(index) == -1;
    });

    data.length = 0;
    Array.prototype.push.apply(data, newData);

    this.instance.PluginHooks.run('afterRemoveRow', index, amount);

    this.instance.forceFullRender = true; //used when data was changed
  };

  /**
   * Removes column from the data array
   * @param {Number} [index] Optional. Index of the column to be removed. If not provided, the last column will be removed
   * @param {Number} [amount] Optional. Amount of the columns to be removed. If not provided, one column will be removed
   */
  Handsontable.DataMap.prototype.removeCol = function (index, amount) {
    if (this.instance.dataType === 'object' || this.instance.getSettings().columns) {
      throw new Error("cannot remove column with object data source or columns option specified");
    }
    if (!amount) {
      amount = 1;
    }
    if (typeof index !== 'number') {
      index = -amount;
    }

    index = (this.instance.countCols() + index) % this.instance.countCols();

    var actionWasNotCancelled = this.instance.PluginHooks.execute('beforeRemoveCol', index, amount);

    if (actionWasNotCancelled === false) {
      return;
    }

    var data = this.dataSource;
    for (var r = 0, rlen = this.instance.countRows(); r < rlen; r++) {
      data[r].splice(index, amount);
    }
    this.priv.columnSettings.splice(index, amount);

    this.instance.PluginHooks.run('afterRemoveCol', index, amount);
    this.instance.forceFullRender = true; //used when data was changed
  };

  /**
   * Add / removes data from the column
   * @param {Number} col Index of column in which do you want to do splice.
   * @param {Number} index Index at which to start changing the array. If negative, will begin that many elements from the end
   * @param {Number} amount An integer indicating the number of old array elements to remove. If amount is 0, no elements are removed
   * param {...*} elements Optional. The elements to add to the array. If you don't specify any elements, spliceCol simply removes elements from the array
   */
  Handsontable.DataMap.prototype.spliceCol = function (col, index, amount/*, elements...*/) {
    var elements = 4 <= arguments.length ? [].slice.call(arguments, 3) : [];

    var colData = this.instance.getDataAtCol(col);
    var removed = colData.slice(index, index + amount);
    var after = colData.slice(index + amount);

    Handsontable.helper.extendArray(elements, after);
    var i = 0;
    while (i < amount) {
      elements.push(null); //add null in place of removed elements
      i++;
    }
    Handsontable.helper.to2dArray(elements);
    this.instance.populateFromArray(index, col, elements, null, null, 'spliceCol');

    return removed;
  };

  /**
   * Add / removes data from the row
   * @param {Number} row Index of row in which do you want to do splice.
   * @param {Number} index Index at which to start changing the array. If negative, will begin that many elements from the end
   * @param {Number} amount An integer indicating the number of old array elements to remove. If amount is 0, no elements are removed
   * param {...*} elements Optional. The elements to add to the array. If you don't specify any elements, spliceCol simply removes elements from the array
   */
  Handsontable.DataMap.prototype.spliceRow = function (row, index, amount/*, elements...*/) {
    var elements = 4 <= arguments.length ? [].slice.call(arguments, 3) : [];

    var rowData = this.instance.getDataAtRow(row);
    var removed = rowData.slice(index, index + amount);
    var after = rowData.slice(index + amount);

    Handsontable.helper.extendArray(elements, after);
    var i = 0;
    while (i < amount) {
      elements.push(null); //add null in place of removed elements
      i++;
    }
    this.instance.populateFromArray(row, index, [elements], null, null, 'spliceRow');

    return removed;
  };

  /**
   * Returns single value from the data array
   * @param {Number} row
   * @param {Number} prop
   */
  Handsontable.DataMap.prototype.get = function (row, prop) {
    this.getVars.row = row;
    this.getVars.prop = prop;
    this.instance.PluginHooks.run('beforeGet', this.getVars);
    if (typeof this.getVars.prop === 'string' && this.getVars.prop.indexOf('.') > -1) {
      var sliced = this.getVars.prop.split(".");
      var out = this.dataSource[this.getVars.row];
      if (!out) {
        return null;
      }
      for (var i = 0, ilen = sliced.length; i < ilen; i++) {
        out = out[sliced[i]];
        if (typeof out === 'undefined') {
          return null;
        }
      }
      return out;
    }
    else if (typeof this.getVars.prop === 'function') {
      /**
       *  allows for interacting with complex structures, for example
       *  d3/jQuery getter/setter properties:
       *
       *    {columns: [{
         *      data: function(row, value){
         *        if(arguments.length === 1){
         *          return row.property();
         *        }
         *        row.property(value);
         *      }
         *    }]}
       */
      return this.getVars.prop(this.dataSource.slice(
        this.getVars.row,
        this.getVars.row + 1
      )[0]);
    }
    else {
      return this.dataSource[this.getVars.row] ? this.dataSource[this.getVars.row][this.getVars.prop] : null;
    }
  };

  var copyableLookup = Handsontable.helper.cellMethodLookupFactory('copyable', false);

  /**
   * Returns single value from the data array (intended for clipboard copy to an external application)
   * @param {Number} row
   * @param {Number} prop
   * @return {String}
   */
  Handsontable.DataMap.prototype.getCopyable = function (row, prop) {
    if (copyableLookup.call(this.instance, row, this.propToCol(prop))) {
      return this.get(row, prop);
    }
    return '';
  };

  /**
   * Saves single value to the data array
   * @param {Number} row
   * @param {Number} prop
   * @param {String} value
   * @param {String} [source] Optional. Source of hook runner.
   */
  Handsontable.DataMap.prototype.set = function (row, prop, value, source) {
    this.setVars.row = row;
    this.setVars.prop = prop;
    this.setVars.value = value;
    this.instance.PluginHooks.run('beforeSet', this.setVars, source || "datamapGet");
    if (typeof this.setVars.prop === 'string' && this.setVars.prop.indexOf('.') > -1) {
      var sliced = this.setVars.prop.split(".");
      var out = this.dataSource[this.setVars.row];
      for (var i = 0, ilen = sliced.length - 1; i < ilen; i++) {
        out = out[sliced[i]];
      }
      out[sliced[i]] = this.setVars.value;
    }
    else if (typeof this.setVars.prop === 'function') {
      /* see the `function` handler in `get` */
      this.setVars.prop(this.dataSource.slice(
        this.setVars.row,
        this.setVars.row + 1
      )[0], this.setVars.value);
    }
    else {
      this.dataSource[this.setVars.row][this.setVars.prop] = this.setVars.value;
    }
  };

  /**
   * This ridiculous piece of code maps rows Id that are present in table data to those displayed for user.
   * The trick is, the physical row id (stored in settings.data) is not necessary the same
   * as the logical (displayed) row id (e.g. when sorting is applied).
   */
  Handsontable.DataMap.prototype.physicalRowsToLogical = function (index, amount) {
    var totalRows = this.instance.countRows();
    var physicRow = (totalRows + index) % totalRows;
    var logicRows = [];
    var rowsToRemove = amount;

    while (physicRow < totalRows && rowsToRemove) {
      this.get(physicRow, 0); //this performs an actual mapping and saves the result to getVars
      logicRows.push(this.getVars.row);

      rowsToRemove--;
      physicRow++;
    }

    return logicRows;
  };

  /**
   * Clears the data array
   */
  Handsontable.DataMap.prototype.clear = function () {
    for (var r = 0; r < this.instance.countRows(); r++) {
      for (var c = 0; c < this.instance.countCols(); c++) {
        this.set(r, this.colToProp(c), '');
      }
    }
  };

  /**
   * Returns the data array
   * @return {Array}
   */
  Handsontable.DataMap.prototype.getAll = function () {
    return this.dataSource;
  };

  /**
   * Returns data range as array
   * @param {Object} start Start selection position
   * @param {Object} end End selection position
   * @param {Number} destination Destination of datamap.get
   * @return {Array}
   */
  Handsontable.DataMap.prototype.getRange = function (start, end, destination) {
    var r, rlen, c, clen, output = [], row;
    var getFn = destination === this.DESTINATION_CLIPBOARD_GENERATOR ? this.getCopyable : this.get;
    rlen = Math.max(start.row, end.row);
    clen = Math.max(start.col, end.col);
    for (r = Math.min(start.row, end.row); r <= rlen; r++) {
      row = [];
      for (c = Math.min(start.col, end.col); c <= clen; c++) {
        row.push(getFn.call(this, r, this.colToProp(c)));
      }
      output.push(row);
    }
    return output;
  };

  /**
   * Return data as text (tab separated columns)
   * @param {Object} start (Optional) Start selection position
   * @param {Object} end (Optional) End selection position
   * @return {String}
   */
  Handsontable.DataMap.prototype.getText = function (start, end) {
    return SheetClip.stringify(this.getRange(start, end, this.DESTINATION_RENDERER));
  };

  /**
   * Return data as copyable text (tab separated columns intended for clipboard copy to an external application)
   * @param {Object} start (Optional) Start selection position
   * @param {Object} end (Optional) End selection position
   * @return {String}
   */
  Handsontable.DataMap.prototype.getCopyableText = function (start, end) {
    return SheetClip.stringify(this.getRange(start, end, this.DESTINATION_CLIPBOARD_GENERATOR));
  };

})(Handsontable);

(function (Handsontable) {
  'use strict';

  /*
    Adds appropriate CSS class to table cell, based on cellProperties
   */
  Handsontable.renderers.cellDecorator = function (instance, TD, row, col, prop, value, cellProperties) {
    if (cellProperties.readOnly) {
      instance.view.wt.wtDom.addClass(TD, cellProperties.readOnlyCellClassName);
    }

    if (cellProperties.valid === false && cellProperties.invalidCellClassName) {
      instance.view.wt.wtDom.addClass(TD, cellProperties.invalidCellClassName);
    }

    if (!value && cellProperties.placeholder) {
      instance.view.wt.wtDom.addClass(TD, cellProperties.placeholderCellClassName);
    }
  }

})(Handsontable);
/**
 * Default text renderer
 * @param {Object} instance Handsontable instance
 * @param {Element} TD Table cell where to render
 * @param {Number} row
 * @param {Number} col
 * @param {String|Number} prop Row object property name
 * @param value Value to render (remember to escape unsafe HTML before inserting to DOM!)
 * @param {Object} cellProperties Cell properites (shared by cell renderer and editor)
 */
(function (Handsontable) {
  'use strict';

  var TextRenderer = function (instance, TD, row, col, prop, value, cellProperties) {

    Handsontable.renderers.cellDecorator.apply(this, arguments);

    if (!value && cellProperties.placeholder) {
      value = cellProperties.placeholder;
    }

    var escaped = Handsontable.helper.stringify(value);

    if (cellProperties.rendererTemplate) {
      instance.view.wt.wtDom.empty(TD);
      var TEMPLATE = document.createElement('TEMPLATE');
      TEMPLATE.setAttribute('bind', '{{}}');
      TEMPLATE.innerHTML = cellProperties.rendererTemplate;
      HTMLTemplateElement.decorate(TEMPLATE);
      TEMPLATE.model = instance.getDataAtRow(row);
      TD.appendChild(TEMPLATE);
    }
    else {
      instance.view.wt.wtDom.fastInnerText(TD, escaped); //this is faster than innerHTML. See: https://github.com/warpech/jquery-handsontable/wiki/JavaScript-&-DOM-performance-tips
    }

  };

  //Handsontable.TextRenderer = TextRenderer; //Left for backward compatibility
  Handsontable.renderers.TextRenderer = TextRenderer;
  Handsontable.renderers.registerRenderer('text', TextRenderer);

})(Handsontable);
(function (Handsontable) {

  var clonableWRAPPER = document.createElement('DIV');
  clonableWRAPPER.className = 'htAutocompleteWrapper';

  var clonableARROW = document.createElement('DIV');
  clonableARROW.className = 'htAutocompleteArrow';
  clonableARROW.appendChild(document.createTextNode('\u25BC'));
//this is faster than innerHTML. See: https://github.com/warpech/jquery-handsontable/wiki/JavaScript-&-DOM-performance-tips

  var wrapTdContentWithWrapper = function(TD, WRAPPER){
    WRAPPER.innerHTML = TD.innerHTML;
    Handsontable.Dom.empty(TD);
    TD.appendChild(WRAPPER);
  };

  /**
   * Autocomplete renderer
   * @param {Object} instance Handsontable instance
   * @param {Element} TD Table cell where to render
   * @param {Number} row
   * @param {Number} col
   * @param {String|Number} prop Row object property name
   * @param value Value to render (remember to escape unsafe HTML before inserting to DOM!)
   * @param {Object} cellProperties Cell properites (shared by cell renderer and editor)
   */
  var AutocompleteRenderer = function (instance, TD, row, col, prop, value, cellProperties) {

    var WRAPPER = clonableWRAPPER.cloneNode(true); //this is faster than createElement
    var ARROW = clonableARROW.cloneNode(true); //this is faster than createElement

    Handsontable.renderers.TextRenderer(instance, TD, row, col, prop, value, cellProperties);

    TD.appendChild(ARROW);
    Handsontable.Dom.addClass(TD, 'htAutocomplete');


    if (!TD.firstChild) { //http://jsperf.com/empty-node-if-needed
      //otherwise empty fields appear borderless in demo/renderers.html (IE)
      TD.appendChild(document.createTextNode('\u00A0')); //\u00A0 equals &nbsp; for a text node
      //this is faster than innerHTML. See: https://github.com/warpech/jquery-handsontable/wiki/JavaScript-&-DOM-performance-tips
    }

    if (!instance.acArrowListener) {
      //not very elegant but easy and fast
      instance.acArrowListener = function () {
        instance.view.wt.getSetting('onCellDblClick');
      };

      instance.rootElement.on('mousedown', '.htAutocompleteArrow', instance.acArrowListener); //this way we don't bind event listener to each arrow. We rely on propagation instead

    }
  };

  Handsontable.AutocompleteRenderer = AutocompleteRenderer;
  Handsontable.renderers.AutocompleteRenderer = AutocompleteRenderer;
  Handsontable.renderers.registerRenderer('autocomplete', AutocompleteRenderer);
})(Handsontable);
/**
 * Checkbox renderer
 * @param {Object} instance Handsontable instance
 * @param {Element} TD Table cell where to render
 * @param {Number} row
 * @param {Number} col
 * @param {String|Number} prop Row object property name
 * @param value Value to render (remember to escape unsafe HTML before inserting to DOM!)
 * @param {Object} cellProperties Cell properites (shared by cell renderer and editor)
 */
(function (Handsontable) {

  'use strict';

  var clonableINPUT = document.createElement('INPUT');
  clonableINPUT.className = 'htCheckboxRendererInput';
  clonableINPUT.type = 'checkbox';
  clonableINPUT.setAttribute('autocomplete', 'off');

  var CheckboxRenderer = function (instance, TD, row, col, prop, value, cellProperties) {

    if (typeof cellProperties.checkedTemplate === "undefined") {
      cellProperties.checkedTemplate = true;
    }
    if (typeof cellProperties.uncheckedTemplate === "undefined") {
      cellProperties.uncheckedTemplate = false;
    }

    instance.view.wt.wtDom.empty(TD); //TODO identify under what circumstances this line can be removed

    var INPUT = clonableINPUT.cloneNode(false); //this is faster than createElement

    if (value === cellProperties.checkedTemplate || value === Handsontable.helper.stringify(cellProperties.checkedTemplate)) {
      INPUT.checked = true;
      TD.appendChild(INPUT);
    }
    else if (value === cellProperties.uncheckedTemplate || value === Handsontable.helper.stringify(cellProperties.uncheckedTemplate)) {
      TD.appendChild(INPUT);
    }
    else if (value === null) { //default value
      INPUT.className += ' noValue';
      TD.appendChild(INPUT);
    }
    else {
      instance.view.wt.wtDom.fastInnerText(TD, '#bad value#'); //this is faster than innerHTML. See: https://github.com/warpech/jquery-handsontable/wiki/JavaScript-&-DOM-performance-tips
    }

    var $input = $(INPUT);

    if (cellProperties.readOnly) {
      $input.on('click', function (event) {
        event.preventDefault();
      });
    }
    else {
      $input.on('mousedown', function (event) {
        event.stopPropagation(); //otherwise can confuse cell mousedown handler
      });

      $input.on('mouseup', function (event) {
        event.stopPropagation(); //otherwise can confuse cell dblclick handler
      });

      $input.on('change', function(){
        if (this.checked) {
          instance.setDataAtRowProp(row, prop, cellProperties.checkedTemplate);
        }
        else {
          instance.setDataAtRowProp(row, prop, cellProperties.uncheckedTemplate);
        }
      });
    }

    if(!instance.CheckboxRenderer || !instance.CheckboxRenderer.beforeKeyDownHookBound){
      instance.CheckboxRenderer = {
        beforeKeyDownHookBound : true
      };

      instance.addHook('beforeKeyDown', function(event){
        if(event.keyCode == Handsontable.helper.keyCode.SPACE){

          var selection = instance.getSelected();
          var cell, checkbox, cellProperties;

          var selStart = {
            row: Math.min(selection[0], selection[2]),
            col: Math.min(selection[1], selection[3])
          };

          var selEnd = {
            row: Math.max(selection[0], selection[2]),
            col: Math.max(selection[1], selection[3])
          };

          for(var row = selStart.row; row <= selEnd.row; row++ ){
            for(var col = selEnd.col; col <= selEnd.col; col++){
              cell = instance.getCell(row, col);
              cellProperties = instance.getCellMeta(row, col);

              checkbox = cell.querySelectorAll('input[type=checkbox]');

              if(checkbox.length > 0 && !cellProperties.readOnly){

                if(!event.isImmediatePropagationStopped()){
                  event.stopImmediatePropagation();
                  event.preventDefault();
                }

                for(var i = 0, len = checkbox.length; i < len; i++){
                  checkbox[i].checked = !checkbox[i].checked;
                  $(checkbox[i]).trigger('change');
                }

              }

            }
          }
        }
      });
    }

  };

  Handsontable.CheckboxRenderer = CheckboxRenderer;
  Handsontable.renderers.CheckboxRenderer = CheckboxRenderer;
  Handsontable.renderers.registerRenderer('checkbox', CheckboxRenderer);

})(Handsontable);
/**
 * Numeric cell renderer
 * @param {Object} instance Handsontable instance
 * @param {Element} TD Table cell where to render
 * @param {Number} row
 * @param {Number} col
 * @param {String|Number} prop Row object property name
 * @param value Value to render (remember to escape unsafe HTML before inserting to DOM!)
 * @param {Object} cellProperties Cell properites (shared by cell renderer and editor)
 */
(function (Handsontable) {

  'use strict';

  var NumericRenderer = function (instance, TD, row, col, prop, value, cellProperties) {
    if (Handsontable.helper.isNumeric(value)) {
      if (typeof cellProperties.language !== 'undefined') {
        numeral.language(cellProperties.language)
      }
      value = numeral(value).format(cellProperties.format || '0'); //docs: http://numeraljs.com/
      instance.view.wt.wtDom.addClass(TD, 'htNumeric');
    }
    Handsontable.renderers.TextRenderer(instance, TD, row, col, prop, value, cellProperties);
  };

  Handsontable.NumericRenderer = NumericRenderer; //Left for backward compatibility with versions prior 0.10.0
  Handsontable.renderers.NumericRenderer = NumericRenderer;
  Handsontable.renderers.registerRenderer('numeric', NumericRenderer);

})(Handsontable);
(function(Handosntable){

  'use strict';

  var PasswordRenderer = function (instance, TD, row, col, prop, value, cellProperties) {
    Handsontable.renderers.TextRenderer.apply(this, arguments);

    value = TD.innerHTML;

    var hash;
    var hashLength = cellProperties.hashLength || value.length;
    var hashSymbol = cellProperties.hashSymbol || '*';

    for( hash = ''; hash.split(hashSymbol).length - 1 < hashLength; hash += hashSymbol);

    instance.view.wt.wtDom.fastInnerHTML(TD, hash);

  };

  Handosntable.PasswordRenderer = PasswordRenderer;
  Handosntable.renderers.PasswordRenderer = PasswordRenderer;
  Handosntable.renderers.registerRenderer('password', PasswordRenderer);

})(Handsontable);
(function (Handsontable) {

  function HtmlRenderer(instance, TD, row, col, prop, value, cellProperties){

    Handsontable.renderers.cellDecorator.apply(this, arguments);

    Handsontable.Dom.fastInnerHTML(TD, value);
  }

  Handsontable.renderers.registerRenderer('html', HtmlRenderer);
  Handsontable.renderers.HtmlRenderer = HtmlRenderer;

})(Handsontable);

(function (Handsontable) {
  'use strict';

  Handsontable.EditorState = {
    VIRGIN: 'STATE_VIRGIN', //before editing
    EDITING: 'STATE_EDITING',
    WAITING: 'STATE_WAITING', //waiting for async validation
    FINISHED: 'STATE_FINISHED'
  };

  function BaseEditor(instance) {
    this.instance = instance;
    this.state = Handsontable.EditorState.VIRGIN;

    this._opened = false;
    this._closeCallback = null;

    this.init();
  }

  BaseEditor.prototype._fireCallbacks = function(result) {
    if(this._closeCallback){
      this._closeCallback(result);
      this._closeCallback = null;
    }

  }

  BaseEditor.prototype.init = function(){};

  BaseEditor.prototype.getValue = function(){
    throw Error('Editor getValue() method unimplemented');
  };

  BaseEditor.prototype.setValue = function(newValue){
    throw Error('Editor setValue() method unimplemented');
  };

  BaseEditor.prototype.open = function(){
    throw Error('Editor open() method unimplemented');
  };

  BaseEditor.prototype.close = function(){
    throw Error('Editor close() method unimplemented');
  };

  BaseEditor.prototype.prepare = function(row, col, prop, td, originalValue, cellProperties){
    this.TD = td;
    this.row = row;
    this.col = col;
    this.prop = prop;
    this.originalValue = originalValue;
    this.cellProperties = cellProperties;

    this.state = Handsontable.EditorState.VIRGIN;
  };

  BaseEditor.prototype.extend = function(){
    var baseClass = this.constructor;
    function Editor(){
      baseClass.apply(this, arguments);
    }

    function inherit(Child, Parent){
      function Bridge() {
      }

      Bridge.prototype = Parent.prototype;
      Child.prototype = new Bridge();
      Child.prototype.constructor = Child;
      return Child;
    }

    return inherit(Editor, baseClass);
  };

  BaseEditor.prototype.saveValue = function (val, ctrlDown) {
    if (ctrlDown) { //if ctrl+enter and multiple cells selected, behave like Excel (finish editing and apply to all cells)
      var sel = this.instance.getSelected();
      this.instance.populateFromArray(sel[0], sel[1], val, sel[2], sel[3], 'edit');
    }
    else {
      this.instance.populateFromArray(this.row, this.col, val, null, null, 'edit');
    }
  };

  BaseEditor.prototype.beginEditing = function(initialValue){
    if (this.state != Handsontable.EditorState.VIRGIN) {
      return;
    }

    if (this.cellProperties.readOnly) {
      return;
    }

    this.instance.view.scrollViewport({row: this.row, col: this.col});
    this.instance.view.render();

    this.state = Handsontable.EditorState.EDITING;

    initialValue = typeof initialValue == 'string' ? initialValue : this.originalValue;

    this.setValue(Handsontable.helper.stringify(initialValue));

    this.open();
    this._opened = true;
    this.focus();

    this.instance.view.render(); //only rerender the selections (FillHandle should disappear when beginediting is triggered)
  };

  BaseEditor.prototype.finishEditing = function (restoreOriginalValue, ctrlDown, callback) {

    if (callback) {
      var previousCloseCallback = this._closeCallback;
      this._closeCallback = function (result) {
        if(previousCloseCallback){
          previousCloseCallback(result);
        }

        callback(result);
      };
    }

    if (this.isWaiting()) {
      return;
    }

    if (this.state == Handsontable.EditorState.VIRGIN) {
      var that = this;
      setTimeout(function () {
        that._fireCallbacks(true);
      });
      return;
    }

    if (this.state == Handsontable.EditorState.EDITING) {

      if (restoreOriginalValue) {

        this.cancelChanges();
        return;

      }


      var val = [
        [String.prototype.trim.call(this.getValue())] //String.prototype.trim is defined in Walkontable polyfill.js
      ];

      this.state = Handsontable.EditorState.WAITING;

      this.saveValue(val, ctrlDown);

      if(this.instance.getCellValidator(this.cellProperties)){
        var that = this;
        this.instance.addHookOnce('afterValidate', function (result) {
          that.state = Handsontable.EditorState.FINISHED;
          that.discardEditor(result);
        });
      } else {
        this.state = Handsontable.EditorState.FINISHED;
        this.discardEditor(true);
      }

    }
  };

  BaseEditor.prototype.cancelChanges = function () {
    this.state = Handsontable.EditorState.FINISHED;
    this.discardEditor();
  };

  BaseEditor.prototype.discardEditor = function (result) {
    if (this.state !== Handsontable.EditorState.FINISHED) {
      return;
    }

    if (result === false && this.cellProperties.allowInvalid !== true) { //validator was defined and failed

      this.instance.selectCell(this.row, this.col);
      this.focus();

      this.state = Handsontable.EditorState.EDITING;

      this._fireCallbacks(false);
    }
    else {
      this.close();
      this._opened = false;

      this.state = Handsontable.EditorState.VIRGIN;

      this._fireCallbacks(true);
    }

  };

  BaseEditor.prototype.isOpened = function(){
    return this._opened;
  };

  BaseEditor.prototype.isWaiting = function () {
    return this.state === Handsontable.EditorState.WAITING;
  };

  Handsontable.editors.BaseEditor = BaseEditor;

})(Handsontable);

(function(Handsontable){
  var TextEditor = Handsontable.editors.BaseEditor.prototype.extend();

  TextEditor.prototype.init = function(){
    this.createElements();
    this.bindEvents();
  };

  TextEditor.prototype.getValue = function(){
    return this.TEXTAREA.value
  };

  TextEditor.prototype.setValue = function(newValue){
    this.TEXTAREA.value = newValue;
  };

  var onBeforeKeyDown =  function onBeforeKeyDown(event){

    var instance = this;
    var that = instance.getActiveEditor();

    var keyCodes = Handsontable.helper.keyCode;
    var ctrlDown = (event.ctrlKey || event.metaKey) && !event.altKey; //catch CTRL but not right ALT (which in some systems triggers ALT+CTRL)


    //Process only events that have been fired in the editor
    if (event.target !== that.TEXTAREA || event.isImmediatePropagationStopped()){
      return;
    }

    if (event.keyCode === 17 || event.keyCode === 224 || event.keyCode === 91 || event.keyCode === 93) {
      //when CTRL or its equivalent is pressed and cell is edited, don't prepare selectable text in textarea
      event.stopImmediatePropagation();
      return;
    }

    switch (event.keyCode) {
      case keyCodes.ARROW_RIGHT:
        if (that.wtDom.getCaretPosition(that.TEXTAREA) !== that.TEXTAREA.value.length) {
          event.stopImmediatePropagation();
        }
        break;

      case keyCodes.ARROW_LEFT: /* arrow left */
        if (that.wtDom.getCaretPosition(that.TEXTAREA) !== 0) {
          event.stopImmediatePropagation();
        }
        break;

      case keyCodes.ENTER:
        var selected = that.instance.getSelected();
        var isMultipleSelection = !(selected[0] === selected[2] && selected[1] === selected[3]);
        if ((ctrlDown && !isMultipleSelection) || event.altKey) { //if ctrl+enter or alt+enter, add new line
          if(that.isOpened()){
            that.setValue(that.getValue() + '\n');
            that.focus();
          } else {
            that.beginEditing(that.originalValue + '\n')
          }
          event.stopImmediatePropagation();
        }
        event.preventDefault(); //don't add newline to field
        break;

      case keyCodes.A:
      case keyCodes.X:
      case keyCodes.C:
      case keyCodes.V:
        if(ctrlDown){
          event.stopImmediatePropagation(); //CTRL+A, CTRL+C, CTRL+V, CTRL+X should only work locally when cell is edited (not in table context)
          break;
        }
      case keyCodes.BACKSPACE:
      case keyCodes.DELETE:
      case keyCodes.HOME:
      case keyCodes.END:
        event.stopImmediatePropagation(); //backspace, delete, home, end should only work locally when cell is edited (not in table context)
        break;
    }

  };

  TextEditor.prototype.open = function(){
    this.refreshDimensions(); //need it instantly, to prevent https://github.com/warpech/jquery-handsontable/issues/348

    this.instance.addHook('beforeKeyDown', onBeforeKeyDown);
  };

  TextEditor.prototype.close = function(){
    this.textareaParentStyle.display = 'none';

    if (document.activeElement === this.TEXTAREA) {
      this.instance.listen(); //don't refocus the table if user focused some cell outside of HT on purpose
    }

    this.instance.removeHook('beforeKeyDown', onBeforeKeyDown);
  };

  TextEditor.prototype.focus = function(){
    this.TEXTAREA.focus();
    this.wtDom.setCaretPosition(this.TEXTAREA, this.TEXTAREA.value.length);
  };

  TextEditor.prototype.createElements = function () {
    this.$body = $(document.body);
    this.wtDom = new WalkontableDom();

    this.TEXTAREA = document.createElement('TEXTAREA');
    this.$textarea = $(this.TEXTAREA);

    this.wtDom.addClass(this.TEXTAREA, 'handsontableInput');

    this.textareaStyle = this.TEXTAREA.style;
    this.textareaStyle.width = 0;
    this.textareaStyle.height = 0;

    this.TEXTAREA_PARENT = document.createElement('DIV');
    this.wtDom.addClass(this.TEXTAREA_PARENT, 'handsontableInputHolder');

    this.textareaParentStyle = this.TEXTAREA_PARENT.style;
    this.textareaParentStyle.top = 0;
    this.textareaParentStyle.left = 0;
    this.textareaParentStyle.display = 'none';

    this.TEXTAREA_PARENT.appendChild(this.TEXTAREA);

    this.instance.rootElement[0].appendChild(this.TEXTAREA_PARENT);

    var that = this;
    Handsontable.PluginHooks.add('afterRender', function () {
      that.instance.registerTimeout('refresh_editor_dimensions', function () {
        that.refreshDimensions();
      }, 0);
    });
  };

  TextEditor.prototype.refreshDimensions = function () {
    if (this.state !== Handsontable.EditorState.EDITING) {
      return;
    }

    ///start prepare textarea position
    this.TD = this.instance.getCell(this.row, this.col);
    if (!this.TD) {
      //TD is outside of the viewport. Otherwise throws exception when scrolling the table while a cell is edited
      return;
    }
    var $td = $(this.TD); //because old td may have been scrolled out with scrollViewport
    var currentOffset = this.wtDom.offset(this.TD);
    var containerOffset = this.wtDom.offset(this.instance.rootElement[0]);
    var scrollTop = this.instance.rootElement.scrollTop();
    var scrollLeft = this.instance.rootElement.scrollLeft();
    var editTop = currentOffset.top - containerOffset.top + scrollTop - 1;
    var editLeft = currentOffset.left - containerOffset.left + scrollLeft - 1;

    var settings = this.instance.getSettings();
    var rowHeadersCount = settings.rowHeaders === false ? 0 : 1;
    var colHeadersCount = settings.colHeaders === false ? 0 : 1;

    if (editTop < 0) {
      editTop = 0;
    }
    if (editLeft < 0) {
      editLeft = 0;
    }

    if (rowHeadersCount > 0 && parseInt($td.css('border-top-width'), 10) > 0) {
      editTop += 1;
    }
    if (colHeadersCount > 0 && parseInt($td.css('border-left-width'), 10) > 0) {
      editLeft += 1;
    }

    this.textareaParentStyle.top = editTop + 'px';
    this.textareaParentStyle.left = editLeft + 'px';
    ///end prepare textarea position

    var width = $td.width()
      , maxWidth = this.instance.view.maximumVisibleElementWidth(editLeft) - 10 //10 is TEXTAREAs border and padding
      , height = $td.outerHeight() - 4
      , maxHeight = this.instance.view.maximumVisibleElementHeight(editTop) - 5; //10 is TEXTAREAs border and padding

    if (parseInt($td.css('border-top-width'), 10) > 0) {
      height -= 1;
    }
    if (parseInt($td.css('border-left-width'), 10) > 0) {
      if (rowHeadersCount > 0) {
        width -= 1;
      }
    }

    //in future may change to pure JS http://stackoverflow.com/questions/454202/creating-a-textarea-with-auto-resize
    this.$textarea.autoResize({
      minHeight: Math.min(height, maxHeight),
      maxHeight: maxHeight, //TEXTAREA should never be wider than visible part of the viewport (should not cover the scrollbar)
      minWidth: Math.min(width, maxWidth),
      maxWidth: maxWidth, //TEXTAREA should never be wider than visible part of the viewport (should not cover the scrollbar)
      animate: false,
      extraSpace: 0
    });

    this.textareaParentStyle.display = 'block';
  };

  TextEditor.prototype.bindEvents = function () {
    this.$textarea.on('cut.editor', function (event) {
      event.stopPropagation();
    });

    this.$textarea.on('paste.editor', function (event) {
      event.stopPropagation();
    });
  };

  Handsontable.editors.TextEditor = TextEditor;
  Handsontable.editors.registerEditor('text', Handsontable.editors.TextEditor);

})(Handsontable);

(function(Handsontable){

  //Blank editor, because all the work is done by renderer
  var CheckboxEditor = Handsontable.editors.BaseEditor.prototype.extend();

  CheckboxEditor.prototype.beginEditing = function () {
    this.saveValue([
      [!this.originalValue]
    ]);
  };
  CheckboxEditor.prototype.finishEditing = function () {};

  CheckboxEditor.prototype.init = function () {};
  CheckboxEditor.prototype.open = function () {};
  CheckboxEditor.prototype.close = function () {};
  CheckboxEditor.prototype.getValue = function () {};
  CheckboxEditor.prototype.setValue = function () {};
  CheckboxEditor.prototype.focus = function () {};

  Handsontable.editors.CheckboxEditor = CheckboxEditor;
  Handsontable.editors.registerEditor('checkbox', CheckboxEditor);

})(Handsontable);


(function (Handsontable) {
  var DateEditor = Handsontable.editors.TextEditor.prototype.extend();

  DateEditor.prototype.init = function () {
    if (!$.datepicker) {
      throw new Error("jQuery UI Datepicker dependency not found. Did you forget to include jquery-ui.custom.js or its substitute?");
    }

    Handsontable.editors.TextEditor.prototype.init.apply(this, arguments);

    this.isCellEdited = false;
    var that = this;

    this.instance.addHook('afterDestroy', function () {
      that.destroyElements();
    })

  };

  DateEditor.prototype.createElements = function () {
    Handsontable.editors.TextEditor.prototype.createElements.apply(this, arguments);

    this.datePicker = document.createElement('DIV');
    this.instance.view.wt.wtDom.addClass(this.datePicker, 'htDatepickerHolder');
    this.datePickerStyle = this.datePicker.style;
    this.datePickerStyle.position = 'absolute';
    this.datePickerStyle.top = 0;
    this.datePickerStyle.left = 0;
    this.datePickerStyle.zIndex = 99;
    document.body.appendChild(this.datePicker);
    this.$datePicker = $(this.datePicker);

    var that = this;
    var defaultOptions = {
      dateFormat: "yy-mm-dd",
      showButtonPanel: true,
      changeMonth: true,
      changeYear: true,
      onSelect: function (dateStr) {
        that.setValue(dateStr);
        that.finishEditing(false);
      }
    };
    this.$datePicker.datepicker(defaultOptions);

    /**
     * Prevent recognizing clicking on jQuery Datepicker as clicking outside of table
     */
    this.$datePicker.on('mousedown', function (event) {
      event.stopPropagation();
    });

    this.hideDatepicker();
  };

  DateEditor.prototype.destroyElements = function () {
    this.$datePicker.datepicker('destroy');
    this.$datePicker.remove();
  };

  DateEditor.prototype.beginEditing = function (row, col, prop, useOriginalValue, suffix) {
    Handsontable.editors.TextEditor.prototype.beginEditing.apply(this, arguments);
    this.showDatepicker();
  };

  DateEditor.prototype.finishEditing = function (isCancelled, ctrlDown) {
    this.hideDatepicker();
    Handsontable.editors.TextEditor.prototype.finishEditing.apply(this, arguments);
  };

  DateEditor.prototype.showDatepicker = function () {
    var $td = $(this.TD);
    var offset = $td.offset();
    this.datePickerStyle.top = (offset.top + $td.height()) + 'px';
    this.datePickerStyle.left = offset.left + 'px';

    var dateOptions = {
      defaultDate: this.originalValue || void 0
    };
    $.extend(dateOptions, this.cellProperties);
    this.$datePicker.datepicker("option", dateOptions);
    if (this.originalValue) {
      this.$datePicker.datepicker("setDate", this.originalValue);
    }
    this.datePickerStyle.display = 'block';
  };

  DateEditor.prototype.hideDatepicker = function () {
    this.datePickerStyle.display = 'none';
  };


  Handsontable.editors.DateEditor = DateEditor;
  Handsontable.editors.registerEditor('date', DateEditor);
})(Handsontable);
/**
 * This is inception. Using Handsontable as Handsontable editor
 */
(function (Handsontable) {
  "use strict";

  var HandsontableEditor = Handsontable.editors.TextEditor.prototype.extend();

  HandsontableEditor.prototype.createElements = function () {
    Handsontable.editors.TextEditor.prototype.createElements.apply(this, arguments);

    var DIV = document.createElement('DIV');
    DIV.className = 'handsontableEditor';
    this.TEXTAREA_PARENT.appendChild(DIV);

    this.$htContainer = $(DIV);
    this.$htContainer.handsontable();
  };

  HandsontableEditor.prototype.prepare = function (td, row, col, prop, value, cellProperties) {

    Handsontable.editors.TextEditor.prototype.prepare.apply(this, arguments);

    var parent = this;

    var options = {
      startRows: 0,
      startCols: 0,
      minRows: 0,
      minCols: 0,
      className: 'listbox',
      copyPaste: false,
      cells: function () {
        return {
          readOnly: true
        }
      },
      fillHandle: false,
      afterOnCellMouseDown: function () {
        var value = this.getValue();
        if (value !== void 0) { //if the value is undefined then it means we don't want to set the value
          parent.setValue(value);
        }
        parent.instance.destroyEditor();
      },
      beforeOnKeyDown: function (event) {
        var instance = this;

        switch (event.keyCode) {
          case Handsontable.helper.keyCode.ESCAPE:
            parent.instance.destroyEditor(true);
            event.stopImmediatePropagation();
            event.preventDefault();
            break;

          case Handsontable.helper.keyCode.ENTER: //enter
            var sel = instance.getSelected();
            var value = this.getDataAtCell(sel[0], sel[1]);
            if (value !== void 0) { //if the value is undefined then it means we don't want to set the value
              parent.setValue(value);
            }
            parent.instance.destroyEditor();
            break;

          case Handsontable.helper.keyCode.ARROW_UP:
            if (instance.getSelected() && instance.getSelected()[0] == 0 && !parent.cellProperties.strict){
              instance.deselectCell();
              parent.instance.listen();
              parent.focus();
              event.preventDefault();
              event.stopImmediatePropagation();
            }
            break;
        }
      }
    };

    if (this.cellProperties.handsontable) {
      options = $.extend(options, cellProperties.handsontable);
    }
    this.$htContainer.handsontable('destroy');
    this.$htContainer.handsontable(options);
  };

  var onBeforeKeyDown = function (event) {

    if (event.isImmediatePropagationStopped()) {
      return;
    }

    var editor = this.getActiveEditor();
    var innerHOT = editor.$htContainer.handsontable('getInstance');

    if (event.keyCode == Handsontable.helper.keyCode.ARROW_DOWN) {

      if (!innerHOT.getSelected()){
        innerHOT.selectCell(0, 0);
      } else {
        var selectedRow = innerHOT.getSelected()[0];
        var rowToSelect = selectedRow < innerHOT.countRows() - 1 ? selectedRow + 1 : selectedRow;

        innerHOT.selectCell(rowToSelect, 0);
      }

      event.preventDefault();
      event.stopImmediatePropagation();
    }

  };

  HandsontableEditor.prototype.open = function () {

    this.instance.addHook('beforeKeyDown', onBeforeKeyDown);

    Handsontable.editors.TextEditor.prototype.open.apply(this, arguments);

    this.$htContainer.handsontable('render');

    if (this.cellProperties.strict) {
      this.$htContainer.handsontable('selectCell', 0, 0);
      this.$textarea[0].style.visibility = 'hidden';
    } else {
      this.$htContainer.handsontable('deselectCell');
      this.$textarea[0].style.visibility = 'visible';
    }

    this.wtDom.setCaretPosition(this.$textarea[0], 0, this.$textarea[0].value.length);

  };

  HandsontableEditor.prototype.close = function () {

    this.instance.removeHook('beforeKeyDown', onBeforeKeyDown);
    this.instance.listen();

    Handsontable.editors.TextEditor.prototype.close.apply(this, arguments);
  };

  HandsontableEditor.prototype.focus = function () {

    this.instance.listen();

    Handsontable.editors.TextEditor.prototype.focus.apply(this, arguments);
  };

  HandsontableEditor.prototype.beginEditing = function (initialValue) {
    var onBeginEditing = this.instance.getSettings().onBeginEditing;
    if (onBeginEditing && onBeginEditing() === false) {
      return;
    }

    Handsontable.editors.TextEditor.prototype.beginEditing.apply(this, arguments);

  };

  HandsontableEditor.prototype.finishEditing = function (isCancelled, ctrlDown) {
    if (this.$htContainer.handsontable('isListening')) { //if focus is still in the HOT editor
      this.instance.listen(); //return the focus to the parent HOT instance
    }

    if (this.$htContainer.handsontable('getSelected')) {
      var value = this.$htContainer.handsontable('getInstance').getValue();
      if (value !== void 0) { //if the value is undefined then it means we don't want to set the value
        this.setValue(value);
      }
    }

    return Handsontable.editors.TextEditor.prototype.finishEditing.apply(this, arguments);
  };

  Handsontable.editors.HandsontableEditor = HandsontableEditor;
  Handsontable.editors.registerEditor('handsontable', HandsontableEditor);

})(Handsontable);






(function (Handsontable) {
  var AutocompleteEditor = Handsontable.editors.HandsontableEditor.prototype.extend();

  AutocompleteEditor.prototype.init = function () {
    Handsontable.editors.HandsontableEditor.prototype.init.apply(this, arguments);

    this.query = null;
  };

  AutocompleteEditor.prototype.createElements = function(){
    Handsontable.editors.HandsontableEditor.prototype.createElements.apply(this, arguments);

    this.$htContainer.addClass('autocompleteEditor');

  };

  AutocompleteEditor.prototype.bindEvents = function(){

    var that = this;
    this.$textarea.on('keydown.autocompleteEditor', function(event){
      if(!Handsontable.helper.isMetaKey(event.keyCode) || [Handsontable.helper.keyCode.BACKSPACE, Handsontable.helper.keyCode.DELETE].indexOf(event.keyCode) != -1){
        setTimeout(function () {
          that.queryChoices(that.$textarea.val());
        });
      } else if (event.keyCode == Handsontable.helper.keyCode.ENTER && that.cellProperties.strict !== true){
        that.$htContainer.handsontable('deselectCell');
      }

    });

    this.$htContainer.on('mouseenter', function () {
      that.$htContainer.handsontable('deselectCell');
    });

    this.$htContainer.on('mouseleave', function () {
      that.queryChoices(that.query);
    });

    Handsontable.editors.HandsontableEditor.prototype.bindEvents.apply(this, arguments);

  };

  AutocompleteEditor.prototype.beginEditing = function () {
    Handsontable.editors.HandsontableEditor.prototype.beginEditing.apply(this, arguments);

    var that = this;
    setTimeout(function () {
      that.queryChoices(that.TEXTAREA.value);
    });

    var hot =  this.$htContainer.handsontable('getInstance');

    hot.updateSettings({
      'colWidths': [this.wtDom.outerWidth(this.TEXTAREA) - 2],
      afterRenderer: function (TD, row, col, prop, value) {
        var caseSensitive = this.getCellMeta(row, col).filteringCaseSensitive === true;
        var indexOfMatch =  caseSensitive ? value.indexOf(that.query) : value.toLowerCase().indexOf(that.query.toLowerCase());

        if(indexOfMatch != -1){
          var match = value.substr(indexOfMatch, that.query.length);
          TD.innerHTML = value.replace(match, '<strong>' + match + '</strong>');
        }
      }
    });


  };

  var onBeforeKeyDownInner;

  AutocompleteEditor.prototype.open = function () {

    var parent = this;

    onBeforeKeyDownInner = function (event) {
      var instance = this;

      if (event.keyCode == Handsontable.helper.keyCode.ARROW_UP){
        if (instance.getSelected() && instance.getSelected()[0] == 0){

          if(!parent.cellProperties.strict){
            instance.deselectCell();
          }

          parent.instance.listen();
          parent.focus();
          event.preventDefault();
          event.stopImmediatePropagation();
        }
      }

    };

    this.$htContainer.handsontable('getInstance').addHook('beforeKeyDown', onBeforeKeyDownInner);

    Handsontable.editors.HandsontableEditor.prototype.open.apply(this, arguments);

    this.$textarea[0].style.visibility = 'visible';
    parent.focus();
  };

  AutocompleteEditor.prototype.close = function () {

    this.$htContainer.handsontable('getInstance').removeHook('beforeKeyDown', onBeforeKeyDownInner);

    Handsontable.editors.HandsontableEditor.prototype.close.apply(this, arguments);
  };

  AutocompleteEditor.prototype.queryChoices = function(query){

    this.query = query;

    if (typeof this.cellProperties.source == 'function'){
      var that = this;

      this.cellProperties.source(query, function(choices){
        that.updateChoicesList(choices)
      });

    } else if (Handsontable.helper.isArray(this.cellProperties.source)) {

      var choices;

      if(!query || this.cellProperties.filter === false){
        choices = this.cellProperties.source;
      } else {

        var filteringCaseSensitive = this.cellProperties.filteringCaseSensitive === true;
        var lowerCaseQuery = query.toLowerCase();

        choices = this.cellProperties.source.filter(function(choice){

          if (filteringCaseSensitive) {
            return choice.indexOf(query) != -1;
          } else {
            return choice.toLowerCase().indexOf(lowerCaseQuery) != -1;
          }

        });
      }

      this.updateChoicesList(choices)

    } else {
      this.updateChoicesList([]);
    }

  };

  function findItemIndexToHighlight(items, value){
    var bestMatch = {};
    var valueLength = value.length;
    var currentItem;
    var indexOfValue;
    var charsLeft;


    for(var i = 0, len = items.length; i < len; i++){
      currentItem = items[i];

      if(valueLength > 0){
        indexOfValue = currentItem.indexOf(value)
      } else {
        indexOfValue = currentItem === value ? 0 : -1;
      }

      if(indexOfValue == -1) continue;

      charsLeft =  currentItem.length - indexOfValue - valueLength;

      if( typeof bestMatch.indexOfValue == 'undefined'
        || bestMatch.indexOfValue > indexOfValue
        || ( bestMatch.indexOfValue == indexOfValue && bestMatch.charsLeft > charsLeft ) ){

        bestMatch.indexOfValue = indexOfValue;
        bestMatch.charsLeft = charsLeft;
        bestMatch.index = i;

      }

    }


    return bestMatch.index;
  }

  AutocompleteEditor.prototype.updateChoicesList = function (choices) {
    this.$htContainer.handsontable('loadData', Handsontable.helper.pivot([choices]));

    var value = this.getValue();
    var rowToHighlight;

    if(this.cellProperties.strict === true){

      rowToHighlight = findItemIndexToHighlight(choices, value);

      if ( typeof rowToHighlight == 'undefined' && this.cellProperties.allowInvalid === false){
        rowToHighlight = 0;
      }

    }

    if(typeof rowToHighlight == 'undefined'){
      this.$htContainer.handsontable('deselectCell');
    } else {
      this.$htContainer.handsontable('selectCell', rowToHighlight, 0);
    }

    this.focus();
  };

  Handsontable.editors.AutocompleteEditor = AutocompleteEditor;
  Handsontable.editors.registerEditor('autocomplete', AutocompleteEditor);

})(Handsontable);

(function(Handsontable){

  var PasswordEditor = Handsontable.editors.TextEditor.prototype.extend();
  var wtDom = new WalkontableDom();

  PasswordEditor.prototype.createElements = function () {
    Handsontable.editors.TextEditor.prototype.createElements.apply(this, arguments);

    this.TEXTAREA = document.createElement('input');
    this.TEXTAREA.setAttribute('type', 'password');
    this.TEXTAREA.className = 'handsontableInput';
    this.textareaStyle = this.TEXTAREA.style;
    this.textareaStyle.width = 0;
    this.textareaStyle.height = 0;
    this.$textarea = $(this.TEXTAREA);

    wtDom.empty(this.TEXTAREA_PARENT);
    this.TEXTAREA_PARENT.appendChild(this.TEXTAREA);

  };

  Handsontable.editors.PasswordEditor = PasswordEditor;
  Handsontable.editors.registerEditor('password', PasswordEditor);

})(Handsontable);

(function (Handsontable) {

  var SelectEditor = Handsontable.editors.BaseEditor.prototype.extend();

  SelectEditor.prototype.init = function(){
    this.select = $('<select />')
      .addClass('htSelectEditor')
      .hide();
    this.instance.rootElement.append(this.select);
  };

  SelectEditor.prototype.prepare = function(){
    Handsontable.editors.BaseEditor.prototype.prepare.apply(this, arguments);


    var selectOptions = this.cellProperties.selectOptions;
    var options;

    if (typeof selectOptions == 'function'){
      options =  this.prepareOptions(selectOptions(this.row, this.col, this.prop))
    } else {
      options =  this.prepareOptions(selectOptions);
    }

    var optionElements = [];

    for (var option in options){
      if (options.hasOwnProperty(option)){
        var optionElement = $('<option />');
        optionElement.val(option);
        optionElement.html(options[option]);

        optionElements.push(optionElement);
      }
    }

    this.select.empty();
    this.select.append(optionElements);

  };

  SelectEditor.prototype.prepareOptions = function(optionsToPrepare){

    var preparedOptions = {};

    if (Handsontable.helper.isArray(optionsToPrepare)){
      for(var i = 0, len = optionsToPrepare.length; i < len; i++){
        preparedOptions[optionsToPrepare[i]] = optionsToPrepare[i];
      }
    }
    else if (typeof optionsToPrepare == 'object') {
      preparedOptions = optionsToPrepare;
    }

    return preparedOptions;

  };

  SelectEditor.prototype.getValue = function () {
    return this.select.val();
  };

  SelectEditor.prototype.setValue = function (value) {
    this.select.val(value);
  };

  var onBeforeKeyDown = function (event) {
    var instance = this;
    var editor = instance.getActiveEditor();

    switch (event.keyCode){
      case Handsontable.helper.keyCode.ARROW_UP:

        var previousOption = editor.select.find('option:selected').prev();

        if (previousOption.length == 1){
          previousOption.prop('selected', true);
        }

        event.stopImmediatePropagation();
        event.preventDefault();
        break;

      case Handsontable.helper.keyCode.ARROW_DOWN:

        var nextOption = editor.select.find('option:selected').next();

        if (nextOption.length == 1){
          nextOption.prop('selected', true);
        }

        event.stopImmediatePropagation();
        event.preventDefault();
        break;
    }
  };

  SelectEditor.prototype.open = function () {
    this.select.css({
      height: $(this.TD).height(),
      'min-width' : $(this.TD).outerWidth()
    });

    this.select.show();
    this.select.offset($(this.TD).offset());

    this.instance.addHook('beforeKeyDown', onBeforeKeyDown);
  };

  SelectEditor.prototype.close = function () {
    this.select.hide();
    this.instance.removeHook('beforeKeyDown', onBeforeKeyDown);
  };

  SelectEditor.prototype.focus = function () {
    this.select.focus();
  };

  Handsontable.editors.SelectEditor = SelectEditor;
  Handsontable.editors.registerEditor('select', SelectEditor);

})(Handsontable);

(function (Handsontable) {

  var DropdownEditor = Handsontable.editors.AutocompleteEditor.prototype.extend();

  DropdownEditor.prototype.prepare = function () {
    Handsontable.editors.AutocompleteEditor.prototype.prepare.apply(this, arguments);

    this.cellProperties.filter = false;
    this.cellProperties.strict = true;

  };


  Handsontable.editors.DropdownEditor = DropdownEditor;
  Handsontable.editors.registerEditor('dropdown', DropdownEditor);


})(Handsontable);
/**
 * Numeric cell validator
 * @param {*} value - Value of edited cell
 * @param {*} callback - Callback called with validation result
 */
Handsontable.NumericValidator = function (value, callback) {
  if (value === null) {
    value = '';
  }
  callback(/^-?\d*\.?\d*$/.test(value));
};
/**
 * Function responsible for validation of autocomplete value
 * @param {*} value - Value of edited cell
 * @param {*} calback - Callback called with validation result
 */
var process = function (value, callback) {

  var originalVal  = value;
  var lowercaseVal = typeof originalVal === 'string' ? originalVal.toLowerCase() : null;

  return function (source) {
    var found = false;
    for (var s = 0, slen = source.length; s < slen; s++) {
      if (originalVal === source[s]) {
        found = true; //perfect match
        break;
      }
      else if (lowercaseVal === source[s].toLowerCase()) {
        // changes[i][3] = source[s]; //good match, fix the case << TODO?
        found = true;
        break;
      }
    }

    callback(found);
  }
};

/**
 * Autocomplete cell validator
 * @param {*} value - Value of edited cell
 * @param {*} calback - Callback called with validation result
 */
Handsontable.AutocompleteValidator = function (value, callback) {
  if (this.strict && this.source) {
    typeof this.source === 'function' ? this.source(value, process(value, callback)) : process(value, callback)(this.source);
  } else {
    callback(true);
  }
};

/**
 * Cell type is just a shortcut for setting bunch of cellProperties (used in getCellMeta)
 */

Handsontable.AutocompleteCell = {
  editor: Handsontable.editors.AutocompleteEditor,
  renderer: Handsontable.renderers.AutocompleteRenderer,
  validator: Handsontable.AutocompleteValidator
};

Handsontable.CheckboxCell = {
  editor: Handsontable.editors.CheckboxEditor,
  renderer: Handsontable.renderers.CheckboxRenderer
};

Handsontable.TextCell = {
  editor: Handsontable.editors.TextEditor,
  renderer: Handsontable.renderers.TextRenderer
};

Handsontable.NumericCell = {
  editor: Handsontable.editors.TextEditor,
  renderer: Handsontable.renderers.NumericRenderer,
  validator: Handsontable.NumericValidator,
  dataType: 'number'
};

Handsontable.DateCell = {
  editor: Handsontable.editors.DateEditor,
  renderer: Handsontable.renderers.AutocompleteRenderer //displays small gray arrow on right side of the cell
};

Handsontable.HandsontableCell = {
  editor: Handsontable.editors.HandsontableEditor,
  renderer: Handsontable.renderers.AutocompleteRenderer //displays small gray arrow on right side of the cell
};

Handsontable.PasswordCell = {
  editor: Handsontable.editors.PasswordEditor,
  renderer: Handsontable.renderers.PasswordRenderer,
  copyable: false
};

Handsontable.DropdownCell = {
  editor: Handsontable.editors.DropdownEditor,
  renderer: Handsontable.renderers.AutocompleteRenderer, //displays small gray arrow on right side of the cell
  validator: Handsontable.AutocompleteValidator
};

//here setup the friendly aliases that are used by cellProperties.type
Handsontable.cellTypes = {
  text: Handsontable.TextCell,
  date: Handsontable.DateCell,
  numeric: Handsontable.NumericCell,
  checkbox: Handsontable.CheckboxCell,
  autocomplete: Handsontable.AutocompleteCell,
  handsontable: Handsontable.HandsontableCell,
  password: Handsontable.PasswordCell,
  dropdown: Handsontable.DropdownCell
};

//here setup the friendly aliases that are used by cellProperties.renderer and cellProperties.editor
Handsontable.cellLookup = {
  validator: {
    numeric: Handsontable.NumericValidator,
    autocomplete: Handsontable.AutocompleteValidator
  }
};
/*
 * jQuery.fn.autoResize 1.1+
 * --
 * https://github.com/warpech/jQuery.fn.autoResize
 *
 * This fork differs from others in a way that it autoresizes textarea in 2-dimensions (horizontally and vertically).
 * It was originally forked from alexbardas's repo but maybe should be merged with dpashkevich's repo in future.
 *
 * originally forked from:
 * https://github.com/jamespadolsey/jQuery.fn.autoResize
 * which is now located here:
 * https://github.com/alexbardas/jQuery.fn.autoResize
 * though the mostly maintained for is here:
 * https://github.com/dpashkevich/jQuery.fn.autoResize/network
 *
 * --
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://sam.zoy.org/wtfpl/COPYING for more details. */

(function($){

  autoResize.defaults = {
    onResize: function(){},
    animate: {
      duration: 200,
      complete: function(){}
    },
    extraSpace: 50,
    minHeight: 'original',
    maxHeight: 500,
    minWidth: 'original',
    maxWidth: 500
  };

  autoResize.cloneCSSProperties = [
    'lineHeight', 'textDecoration', 'letterSpacing',
    'fontSize', 'fontFamily', 'fontStyle', 'fontWeight',
    'textTransform', 'textAlign', 'direction', 'wordSpacing', 'fontSizeAdjust',
    'padding'
  ];

  autoResize.cloneCSSValues = {
    position: 'absolute',
    top: -9999,
    left: -9999,
    opacity: 0,
    overflow: 'hidden',
    overflowX: 'hidden',
    overflowY: 'hidden',
    border: '1px solid black',
    padding: '0.49em' //this must be about the width of caps W character
  };

  autoResize.resizableFilterSelector = 'textarea,input:not(input[type]),input[type=text],input[type=password]';

  autoResize.AutoResizer = AutoResizer;

  $.fn.autoResize = autoResize;

  function autoResize(config) {
    this.filter(autoResize.resizableFilterSelector).each(function(){
      new AutoResizer( $(this), config );
    });
    return this;
  }

  function AutoResizer(el, config) {

    if(this.clones) return;

    this.config = $.extend({}, autoResize.defaults, config);

    this.el = el;

    this.nodeName = el[0].nodeName.toLowerCase();

    this.previousScrollTop = null;

    if (config.maxWidth === 'original') config.maxWidth = el.width();
    if (config.minWidth === 'original') config.minWidth = el.width();
    if (config.maxHeight === 'original') config.maxHeight = el.height();
    if (config.minHeight === 'original') config.minHeight = el.height();

    if (this.nodeName === 'textarea') {
      el.css({
        resize: 'none',
        overflowY: 'none'
      });
    }

    el.data('AutoResizer', this);

    this.createClone();
    this.injectClone();
    this.bind();

  }

  AutoResizer.prototype = {

    bind: function() {

      var check = $.proxy(function(){
        this.check();
        return true;
      }, this);

      this.unbind();

      this.el
        .bind('keyup.autoResize', check)
        //.bind('keydown.autoResize', check)
        .bind('change.autoResize', check);

      this.check(null, true);

    },

    unbind: function() {
      this.el.unbind('.autoResize');
    },

    createClone: function() {

      var el = this.el,
        self = this,
        config = this.config;

      this.clones = $();

      if (config.minHeight !== 'original' || config.maxHeight !== 'original') {
        this.hClone = el.clone().height('auto');
        this.clones = this.clones.add(this.hClone);
      }
      if (config.minWidth !== 'original' || config.maxWidth !== 'original') {
        this.wClone = $('<div/>').width('auto').css({
          whiteSpace: 'nowrap',
          'float': 'left'
        });
        this.clones = this.clones.add(this.wClone);
      }

      $.each(autoResize.cloneCSSProperties, function(i, p){
        self.clones.css(p, el.css(p));
      });

      this.clones
        .removeAttr('name')
        .removeAttr('id')
        .attr('tabIndex', -1)
        .css(autoResize.cloneCSSValues)
        .css('overflowY', 'scroll');

    },

    check: function(e, immediate) {

      var config = this.config,
        wClone = this.wClone,
        hClone = this.hClone,
        el = this.el,
        value = el.val();

      if (wClone) {

        wClone.text(value);

        // Calculate new width + whether to change
        var cloneWidth = wClone.outerWidth(),
          newWidth = (cloneWidth + config.extraSpace) >= config.minWidth ?
            cloneWidth + config.extraSpace : config.minWidth,
          currentWidth = el.width();

        newWidth = Math.min(newWidth, config.maxWidth);

        if (
          (newWidth < currentWidth && newWidth >= config.minWidth) ||
            (newWidth >= config.minWidth && newWidth <= config.maxWidth)
          ) {

          config.onResize.call(el);

          el.scrollLeft(0);

          config.animate && !immediate ?
            el.stop(1,1).animate({
              width: newWidth
            }, config.animate)
            : el.width(newWidth);

        }

      }

      if (hClone) {

        if (newWidth) {
          hClone.width(newWidth);
        }

        hClone.height(0).val(value).scrollTop(10000);

        var scrollTop = hClone[0].scrollTop + config.extraSpace;

        // Don't do anything if scrollTop hasen't changed:
        if (this.previousScrollTop === scrollTop) {
          return;
        }

        this.previousScrollTop = scrollTop;

        if (scrollTop >= config.maxHeight) {
          scrollTop = config.maxHeight;
        }

        if (scrollTop < config.minHeight) {
          scrollTop = config.minHeight;
        }

        if(scrollTop == config.maxHeight && newWidth == config.maxWidth) {
          el.css('overflowY', 'scroll');
        }
        else {
          el.css('overflowY', 'hidden');
        }

        config.onResize.call(el);

        // Either animate or directly apply height:
        config.animate && !immediate ?
          el.stop(1,1).animate({
            height: scrollTop
          }, config.animate)
          : el.height(scrollTop);
      }
    },

    destroy: function() {
      this.unbind();
      this.el.removeData('AutoResizer');
      this.clones.remove();
      delete this.el;
      delete this.hClone;
      delete this.wClone;
      delete this.clones;
    },

    injectClone: function() {
      (
        autoResize.cloneContainer ||
          (autoResize.cloneContainer = $('<arclones/>').appendTo('body'))
        ).empty().append(this.clones); //this should be refactored so that a node is never cloned more than once
    }

  };

})(jQuery);
/**
 * SheetClip - Spreadsheet Clipboard Parser
 * version 0.2
 *
 * This tiny library transforms JavaScript arrays to strings that are pasteable by LibreOffice, OpenOffice,
 * Google Docs and Microsoft Excel.
 *
 * Copyright 2012, Marcin Warpechowski
 * Licensed under the MIT license.
 * http://github.com/warpech/sheetclip/
 */
/*jslint white: true*/
(function (global) {
  "use strict";

  function countQuotes(str) {
    return str.split('"').length - 1;
  }

  global.SheetClip = {
    parse: function (str) {
      var r, rlen, rows, arr = [], a = 0, c, clen, multiline, last;
      rows = str.split('\n');
      if (rows.length > 1 && rows[rows.length - 1] === '') {
        rows.pop();
      }
      for (r = 0, rlen = rows.length; r < rlen; r += 1) {
        rows[r] = rows[r].split('\t');
        for (c = 0, clen = rows[r].length; c < clen; c += 1) {
          if (!arr[a]) {
            arr[a] = [];
          }
          if (multiline && c === 0) {
            last = arr[a].length - 1;
            arr[a][last] = arr[a][last] + '\n' + rows[r][0];
            if (multiline && (countQuotes(rows[r][0]) & 1)) { //& 1 is a bitwise way of performing mod 2
              multiline = false;
              arr[a][last] = arr[a][last].substring(0, arr[a][last].length - 1).replace(/""/g, '"');
            }
          }
          else {
            if (c === clen - 1 && rows[r][c].indexOf('"') === 0) {
              arr[a].push(rows[r][c].substring(1).replace(/""/g, '"'));
              multiline = true;
            }
            else {
              arr[a].push(rows[r][c].replace(/""/g, '"'));
              multiline = false;
            }
          }
        }
        if (!multiline) {
          a += 1;
        }
      }
      return arr;
    },

    stringify: function (arr) {
      var r, rlen, c, clen, str = '', val;
      for (r = 0, rlen = arr.length; r < rlen; r += 1) {
        for (c = 0, clen = arr[r].length; c < clen; c += 1) {
          if (c > 0) {
            str += '\t';
          }
          val = arr[r][c];
          if (typeof val === 'string') {
            if (val.indexOf('\n') > -1) {
              str += '"' + val.replace(/"/g, '""') + '"';
            }
            else {
              str += val;
            }
          }
          else if (val === null || val === void 0) { //void 0 resolves to undefined
            str += '';
          }
          else {
            str += val;
          }
        }
        str += '\n';
      }
      return str;
    }
  };
}(window));
/**
 * CopyPaste.js
 * Creates a textarea that stays hidden on the page and gets focused when user presses CTRL while not having a form input focused
 * In future we may implement a better driver when better APIs are available
 * @constructor
 */
var CopyPaste = (function () {
  var instance;
  return {
    getInstance: function () {
      if (!instance) {
        instance = new CopyPasteClass();
      } else if (instance.hasBeenDestroyed()){
        instance.init();
      }

      instance.refCounter++;

      return instance;
    }
  };
})();

function CopyPasteClass() {
  this.refCounter = 0;
  this.init();
}

CopyPasteClass.prototype.init = function () {
  var that = this
    , style
    , parent;

  this.copyCallbacks = [];
  this.cutCallbacks = [];
  this.pasteCallbacks = [];

  this.listenerElement = document.documentElement;
  parent = document.body;

  if (document.getElementById('CopyPasteDiv')) {
    this.elDiv = document.getElementById('CopyPasteDiv');
    this.elTextarea = this.elDiv.firstChild;
  }
  else {
    this.elDiv = document.createElement('DIV');
    this.elDiv.id = 'CopyPasteDiv';
    style = this.elDiv.style;
    style.position = 'fixed';
    style.top = 0;
    style.left = 0;
    parent.appendChild(this.elDiv);

    this.elTextarea = document.createElement('TEXTAREA');
    this.elTextarea.className = 'copyPaste';
    style = this.elTextarea.style;
    style.width = '1px';
    style.height = '1px';
    this.elDiv.appendChild(this.elTextarea);

    if (typeof style.opacity !== 'undefined') {
      style.opacity = 0;
    }
    else {
      /*@cc_on @if (@_jscript)
       if(typeof style.filter === 'string') {
       style.filter = 'alpha(opacity=0)';
       }
       @end @*/
    }
  }

  this.keydownListener = function (event) {
    var isCtrlDown = false;
    if (event.metaKey) { //mac
      isCtrlDown = true;
    }
    else if (event.ctrlKey && navigator.userAgent.indexOf('Mac') === -1) { //pc
      isCtrlDown = true;
    }

    if (isCtrlDown) {
      if (document.activeElement !== that.elTextarea && (that.getSelectionText() != '' || ['INPUT', 'SELECT', 'TEXTAREA'].indexOf(document.activeElement.nodeName) != -1)) {
        return; //this is needed by fragmentSelection in Handsontable. Ignore copypaste.js behavior if fragment of cell text is selected
      }

      that.selectNodeText(that.elTextarea);
      setTimeout(function () {
        that.selectNodeText(that.elTextarea);
      }, 0);
    }

    /* 67 = c
     * 86 = v
     * 88 = x
     */
    if (isCtrlDown && (event.keyCode === 67 || event.keyCode === 86 || event.keyCode === 88)) {
      // that.selectNodeText(that.elTextarea);

      if (event.keyCode === 88) { //works in all browsers, incl. Opera < 12.12
        setTimeout(function () {
          that.triggerCut(event);
        }, 0);
      }
      else if (event.keyCode === 86) {
        setTimeout(function () {
          that.triggerPaste(event);
        }, 0);
      }
    }
  }

  this._bindEvent(this.listenerElement, 'keydown', this.keydownListener);
};

//http://jsperf.com/textara-selection
//http://stackoverflow.com/questions/1502385/how-can-i-make-this-code-work-in-ie
CopyPasteClass.prototype.selectNodeText = function (el) {
  el.select();
};

//http://stackoverflow.com/questions/5379120/get-the-highlighted-selected-text
CopyPasteClass.prototype.getSelectionText = function () {
  var text = "";
  if (window.getSelection) {
    text = window.getSelection().toString();
  } else if (document.selection && document.selection.type != "Control") {
    text = document.selection.createRange().text;
  }
  return text;
};

CopyPasteClass.prototype.copyable = function (str) {
  if (typeof str !== 'string' && str.toString === void 0) {
    throw new Error('copyable requires string parameter');
  }
  this.elTextarea.value = str;
};

/*CopyPasteClass.prototype.onCopy = function (fn) {
  this.copyCallbacks.push(fn);
};*/

CopyPasteClass.prototype.onCut = function (fn) {
  this.cutCallbacks.push(fn);
};

CopyPasteClass.prototype.onPaste = function (fn) {
  this.pasteCallbacks.push(fn);
};

CopyPasteClass.prototype.removeCallback = function (fn) {
  var i, ilen;
  for (i = 0, ilen = this.copyCallbacks.length; i < ilen; i++) {
    if (this.copyCallbacks[i] === fn) {
      this.copyCallbacks.splice(i, 1);
      return true;
    }
  }
  for (i = 0, ilen = this.cutCallbacks.length; i < ilen; i++) {
    if (this.cutCallbacks[i] === fn) {
      this.cutCallbacks.splice(i, 1);
      return true;
    }
  }
  for (i = 0, ilen = this.pasteCallbacks.length; i < ilen; i++) {
    if (this.pasteCallbacks[i] === fn) {
      this.pasteCallbacks.splice(i, 1);
      return true;
    }
  }
  return false;
};

CopyPasteClass.prototype.triggerCut = function (event) {
  var that = this;
  if (that.cutCallbacks) {
    setTimeout(function () {
      for (var i = 0, ilen = that.cutCallbacks.length; i < ilen; i++) {
        that.cutCallbacks[i](event);
      }
    }, 50);
  }
};

CopyPasteClass.prototype.triggerPaste = function (event, str) {
  var that = this;
  if (that.pasteCallbacks) {
    setTimeout(function () {
      var val = (str || that.elTextarea.value).replace(/\n$/, ''); //remove trailing newline
      for (var i = 0, ilen = that.pasteCallbacks.length; i < ilen; i++) {
        that.pasteCallbacks[i](val, event);
      }
    }, 50);
  }
};

CopyPasteClass.prototype.destroy = function () {

  if(!this.hasBeenDestroyed() && --this.refCounter == 0){
    if (this.elDiv && this.elDiv.parentNode) {
      this.elDiv.parentNode.removeChild(this.elDiv);
    }

    this._unbindEvent(this.listenerElement, 'keydown', this.keydownListener);

  }

};

CopyPasteClass.prototype.hasBeenDestroyed = function () {
  return !this.refCounter;
};

//old version used this:
// - http://net.tutsplus.com/tutorials/javascript-ajax/javascript-from-null-cross-browser-event-binding/
// - http://stackoverflow.com/questions/4643249/cross-browser-event-object-normalization
//but that cannot work with jQuery.trigger
CopyPasteClass.prototype._bindEvent = (function () {
  if (window.jQuery) { //if jQuery exists, use jQuery event (for compatibility with $.trigger and $.triggerHandler, which can only trigger jQuery events - and we use that in tests)
    return function (elem, type, cb) {
      $(elem).on(type + '.copypaste', cb);
    };
  }
  else {
    return function (elem, type, cb) {
      elem.addEventListener(type, cb, false); //sorry, IE8 will only work with jQuery
    };
  }
})();

CopyPasteClass.prototype._unbindEvent = (function () {
  if (window.jQuery) { //if jQuery exists, use jQuery event (for compatibility with $.trigger and $.triggerHandler, which can only trigger jQuery events - and we use that in tests)
    return function (elem, type, cb) {
      $(elem).off(type + '.copypaste', cb);
    };
  }
  else {
    return function (elem, type, cb) {
      elem.removeEventListener(type, cb, false); //sorry, IE8 will only work with jQuery
    };
  }
})();
// json-patch-duplex.js 0.3.6
// (c) 2013 Joachim Wester
// MIT license
var jsonpatch;
(function (jsonpatch) {
    var objOps = {
        add: function (obj, key) {
            obj[key] = this.value;
            return true;
        },
        remove: function (obj, key) {
            delete obj[key];
            return true;
        },
        replace: function (obj, key) {
            obj[key] = this.value;
            return true;
        },
        move: function (obj, key, tree) {
            var temp = { op: "_get", path: this.from };
            apply(tree, [temp]);
            apply(tree, [
                { op: "remove", path: this.from }
            ]);
            apply(tree, [
                { op: "add", path: this.path, value: temp.value }
            ]);
            return true;
        },
        copy: function (obj, key, tree) {
            var temp = { op: "_get", path: this.from };
            apply(tree, [temp]);
            apply(tree, [
                { op: "add", path: this.path, value: temp.value }
            ]);
            return true;
        },
        test: function (obj, key) {
            return (JSON.stringify(obj[key]) === JSON.stringify(this.value));
        },
        _get: function (obj, key) {
            this.value = obj[key];
        }
    };

    var arrOps = {
        add: function (arr, i) {
            arr.splice(i, 0, this.value);
            return true;
        },
        remove: function (arr, i) {
            arr.splice(i, 1);
            return true;
        },
        replace: function (arr, i) {
            arr[i] = this.value;
            return true;
        },
        move: objOps.move,
        copy: objOps.copy,
        test: objOps.test,
        _get: objOps._get
    };

    var observeOps = {
        add: function (patches, path) {
            var patch = {
                op: "add",
                path: path + escapePathComponent(this.name),
                value: this.object[this.name]
            };
            patches.push(patch);
        },
        'delete': function (patches, path) {
            var patch = {
                op: "remove",
                path: path + escapePathComponent(this.name)
            };
            patches.push(patch);
        },
        update: function (patches, path) {
            var patch = {
                op: "replace",
                path: path + escapePathComponent(this.name),
                value: this.object[this.name]
            };
            patches.push(patch);
        }
    };

    function escapePathComponent(str) {
        if (str.indexOf('/') === -1 && str.indexOf('~') === -1)
            return str;
        return str.replace(/~/g, '~0').replace(/\//g, '~1');
    }

    function _getPathRecursive(root, obj) {
        var found;
        for (var key in root) {
            if (root.hasOwnProperty(key)) {
                if (root[key] === obj) {
                    return escapePathComponent(key) + '/';
                } else if (typeof root[key] === 'object') {
                    found = _getPathRecursive(root[key], obj);
                    if (found != '') {
                        return escapePathComponent(key) + '/' + found;
                    }
                }
            }
        }
        return '';
    }

    function getPath(root, obj) {
        if (root === obj) {
            return '/';
        }
        var path = _getPathRecursive(root, obj);
        if (path === '') {
            throw new Error("Object not found in root");
        }
        return '/' + path;
    }

    var beforeDict = [];

    jsonpatch.intervals;

    var Mirror = (function () {
        function Mirror(obj) {
            this.observers = [];
            this.obj = obj;
        }
        return Mirror;
    })();

    var ObserverInfo = (function () {
        function ObserverInfo(callback, observer) {
            this.callback = callback;
            this.observer = observer;
        }
        return ObserverInfo;
    })();

    function getMirror(obj) {
        for (var i = 0, ilen = beforeDict.length; i < ilen; i++) {
            if (beforeDict[i].obj === obj) {
                return beforeDict[i];
            }
        }
    }

    function getObserverFromMirror(mirror, callback) {
        for (var j = 0, jlen = mirror.observers.length; j < jlen; j++) {
            if (mirror.observers[j].callback === callback) {
                return mirror.observers[j].observer;
            }
        }
    }

    function removeObserverFromMirror(mirror, observer) {
        for (var j = 0, jlen = mirror.observers.length; j < jlen; j++) {
            if (mirror.observers[j].observer === observer) {
                mirror.observers.splice(j, 1);
                return;
            }
        }
    }

    function unobserve(root, observer) {
        generate(observer);
        if (Object.observe) {
            _unobserve(observer, root);
        } else {
            clearTimeout(observer.next);
        }

        var mirror = getMirror(root);
        removeObserverFromMirror(mirror, observer);
    }
    jsonpatch.unobserve = unobserve;

    function observe(obj, callback) {
        var patches = [];
        var root = obj;
        var observer;
        var mirror = getMirror(obj);

        if (!mirror) {
            mirror = new Mirror(obj);
            beforeDict.push(mirror);
        } else {
            observer = getObserverFromMirror(mirror, callback);
        }

        if (observer) {
            return observer;
        }

        if (Object.observe) {
            observer = function (arr) {
                //This "refresh" is needed to begin observing new object properties
                _unobserve(observer, obj);
                _observe(observer, obj);

                var a = 0, alen = arr.length;
                while (a < alen) {
                    if (!(arr[a].name === 'length' && _isArray(arr[a].object)) && !(arr[a].name === '__Jasmine_been_here_before__')) {
                        var type = arr[a].type;

                        switch (type) {
                            case 'new':
                                type = 'add';
                                break;

                            case 'deleted':
                                type = 'delete';
                                break;

                            case 'updated':
                                type = 'update';
                                break;
                        }

                        observeOps[type].call(arr[a], patches, getPath(root, arr[a].object));
                    }
                    a++;
                }

                if (patches) {
                    if (callback) {
                        callback(patches);
                    }
                }
                observer.patches = patches;
                patches = [];
            };
        } else {
            observer = {};

            mirror.value = JSON.parse(JSON.stringify(obj));

            if (callback) {
                //callbacks.push(callback); this has no purpose
                observer.callback = callback;
                observer.next = null;
                var intervals = this.intervals || [100, 1000, 10000, 60000];
                var currentInterval = 0;

                var dirtyCheck = function () {
                    generate(observer);
                };
                var fastCheck = function () {
                    clearTimeout(observer.next);
                    observer.next = setTimeout(function () {
                        dirtyCheck();
                        currentInterval = 0;
                        observer.next = setTimeout(slowCheck, intervals[currentInterval++]);
                    }, 0);
                };
                var slowCheck = function () {
                    dirtyCheck();
                    if (currentInterval == intervals.length)
                        currentInterval = intervals.length - 1;
                    observer.next = setTimeout(slowCheck, intervals[currentInterval++]);
                };
                if (typeof window !== 'undefined') {
                    if (window.addEventListener) {
                        window.addEventListener('mousedown', fastCheck);
                        window.addEventListener('mouseup', fastCheck);
                        window.addEventListener('keydown', fastCheck);
                    } else {
                        window.attachEvent('onmousedown', fastCheck);
                        window.attachEvent('onmouseup', fastCheck);
                        window.attachEvent('onkeydown', fastCheck);
                    }
                }
                observer.next = setTimeout(slowCheck, intervals[currentInterval++]);
            }
        }
        observer.patches = patches;
        observer.object = obj;

        mirror.observers.push(new ObserverInfo(callback, observer));

        return _observe(observer, obj);
    }
    jsonpatch.observe = observe;

    /// Listen to changes on an object tree, accumulate patches
    function _observe(observer, obj) {
        if (Object.observe) {
            Object.observe(obj, observer);
            for (var key in obj) {
                if (obj.hasOwnProperty(key)) {
                    var v = obj[key];
                    if (v && typeof (v) === "object") {
                        _observe(observer, v);
                    }
                }
            }
        }
        return observer;
    }

    function _unobserve(observer, obj) {
        if (Object.observe) {
            Object.unobserve(obj, observer);
            for (var key in obj) {
                if (obj.hasOwnProperty(key)) {
                    var v = obj[key];
                    if (v && typeof (v) === "object") {
                        _unobserve(observer, v);
                    }
                }
            }
        }
        return observer;
    }

    function generate(observer) {
        if (Object.observe) {
            Object.deliverChangeRecords(observer);
        } else {
            var mirror;
            for (var i = 0, ilen = beforeDict.length; i < ilen; i++) {
                if (beforeDict[i].obj === observer.object) {
                    mirror = beforeDict[i];
                    break;
                }
            }
            _generate(mirror.value, observer.object, observer.patches, "");
        }
        var temp = observer.patches;
        if (temp.length > 0) {
            observer.patches = [];
            if (observer.callback) {
                observer.callback(temp);
            }
        }
        return temp;
    }
    jsonpatch.generate = generate;

    var _objectKeys;
    if (Object.keys) {
        _objectKeys = Object.keys;
    } else {
        _objectKeys = function (obj) {
            var keys = [];
            for (var o in obj) {
                if (obj.hasOwnProperty(o)) {
                    keys.push(o);
                }
            }
            return keys;
        };
    }

    // Dirty check if obj is different from mirror, generate patches and update mirror
    function _generate(mirror, obj, patches, path) {
        var newKeys = _objectKeys(obj);
        var oldKeys = _objectKeys(mirror);
        var changed = false;
        var deleted = false;

        for (var t = oldKeys.length - 1; t >= 0; t--) {
            var key = oldKeys[t];
            var oldVal = mirror[key];
            if (obj.hasOwnProperty(key)) {
                var newVal = obj[key];
                if (oldVal instanceof Object) {
                    _generate(oldVal, newVal, patches, path + "/" + escapePathComponent(key));
                } else {
                    if (oldVal != newVal) {
                        changed = true;
                        patches.push({ op: "replace", path: path + "/" + escapePathComponent(key), value: newVal });
                        mirror[key] = newVal;
                    }
                }
            } else {
                patches.push({ op: "remove", path: path + "/" + escapePathComponent(key) });
                delete mirror[key];
                deleted = true;
            }
        }

        if (!deleted && newKeys.length == oldKeys.length) {
            return;
        }

        for (var t = 0; t < newKeys.length; t++) {
            var key = newKeys[t];
            if (!mirror.hasOwnProperty(key)) {
                patches.push({ op: "add", path: path + "/" + escapePathComponent(key), value: obj[key] });
                mirror[key] = JSON.parse(JSON.stringify(obj[key]));
            }
        }
    }

    var _isArray;
    if (Array.isArray) {
        _isArray = Array.isArray;
    } else {
        _isArray = function (obj) {
            return obj.push && typeof obj.length === 'number';
        };
    }

    /// Apply a json-patch operation on an object tree
    function apply(tree, patches) {
        var result = false, p = 0, plen = patches.length, patch;
        while (p < plen) {
            patch = patches[p];

            // Find the object
            var keys = patch.path.split('/');
            var obj = tree;
            var t = 1;
            var len = keys.length;
            while (true) {
                if (_isArray(obj)) {
                    var index = parseInt(keys[t], 10);
                    t++;
                    if (t >= len) {
                        result = arrOps[patch.op].call(patch, obj, index, tree);
                        break;
                    }
                    obj = obj[index];
                } else {
                    var key = keys[t];
                    if (key.indexOf('~') != -1)
                        key = key.replace(/~1/g, '/').replace(/~0/g, '~');
                    t++;
                    if (t >= len) {
                        result = objOps[patch.op].call(patch, obj, key, tree);
                        break;
                    }
                    obj = obj[key];
                }
            }
            p++;
        }
        return result;
    }
    jsonpatch.apply = apply;
})(jsonpatch || (jsonpatch = {}));

if (typeof exports !== "undefined") {
    exports.apply = jsonpatch.apply;
    exports.observe = jsonpatch.observe;
    exports.unobserve = jsonpatch.unobserve;
    exports.generate = jsonpatch.generate;
}

Handsontable.PluginHookClass = (function () {

  var Hooks = function () {
    return {
      // Hooks
      beforeInitWalkontable: [],

      beforeInit: [],
      beforeRender: [],
      beforeChange: [],
      beforeRemoveCol: [],
      beforeRemoveRow: [],
      beforeValidate: [],
      beforeGet: [],
      beforeSet: [],
      beforeGetCellMeta: [],
      beforeAutofill: [],
      beforeKeyDown: [],
      beforeColumnSort: [],

      afterInit : [],
      afterLoadData : [],
      afterUpdateSettings: [],
      afterRender : [],
      afterRenderer : [],
      afterChange : [],
      afterValidate: [],
      afterGetCellMeta: [],
      afterGetColHeader: [],
      afterGetColWidth: [],
      afterDestroy: [],
      afterRemoveRow: [],
      afterCreateRow: [],
      afterRemoveCol: [],
      afterCreateCol: [],
      afterColumnResize: [],
      afterColumnMove: [],
      afterColumnSort: [],
      afterDeselect: [],
      afterSelection: [],
      afterSelectionByProp: [],
      afterSelectionEnd: [],
      afterSelectionEndByProp: [],
      afterCopyLimit: [],
      afterOnCellMouseDown: [],
      afterOnCellMouseOver: [],
      afterOnCellCornerMouseDown: [],
      afterScrollVertically: [],
      afterScrollHorizontally: [],

      // Modifiers
      modifyCol: []
    }
  };

  var legacy = {
    onBeforeChange: "beforeChange",
    onChange: "afterChange",
    onCreateRow: "afterCreateRow",
    onCreateCol: "afterCreateCol",
    onSelection: "afterSelection",
    onCopyLimit: "afterCopyLimit",
    onSelectionEnd: "afterSelectionEnd",
    onSelectionByProp: "afterSelectionByProp",
    onSelectionEndByProp: "afterSelectionEndByProp"
  };

  function PluginHookClass() {

    this.hooks = Hooks();

    this.legacy = legacy;

  }

  PluginHookClass.prototype.add = function (key, fn) {
    //if fn is array, run this for all the array items
    if (Handsontable.helper.isArray(fn)) {
      for (var i = 0, len = fn.length; i < len; i++) {
        this.add(key, fn[i]);
      }
    }
    else {
      // provide support for old versions of HOT
      if (key in legacy) {
        key = legacy[key];
      }

      if (typeof this.hooks[key] === "undefined") {
        this.hooks[key] = [];
      }

      if (this.hooks[key].indexOf(fn) == -1) {
        this.hooks[key].push(fn); //only add a hook if it has not already be added (adding the same hook twice is now silently ignored)
      }
    }
    return this;
  };

  PluginHookClass.prototype.once = function(key, fn){

    if(Handsontable.helper.isArray(fn)){

      for(var i = 0, len = fn.length; i < len; i++){
        fn[i].runOnce = true;
        this.add(key, fn[i]);
      }

    } else {
      fn.runOnce = true;
      this.add(key, fn);

    }

  };

  PluginHookClass.prototype.remove = function (key, fn) {
    var status = false;

    // provide support for old versions of HOT
    if (key in legacy) {
      key = legacy[key];
    }

    if (typeof this.hooks[key] !== 'undefined') {

      for (var i = 0, leni = this.hooks[key].length; i < leni; i++) {

        if (this.hooks[key][i] == fn) {
          delete this.hooks[key][i].runOnce;
          this.hooks[key].splice(i, 1);
          status = true;
          break;
        }

      }

    }

    return status;
  };

  PluginHookClass.prototype.run = function (instance, key, p1, p2, p3, p4, p5) {

    // provide support for old versions of HOT
    if (key in legacy) {
      key = legacy[key];
    }

    //performance considerations - http://jsperf.com/call-vs-apply-for-a-plugin-architecture
    if (typeof this.hooks[key] !== 'undefined') {

      //Make a copy of handler array
      var handlers = Array.prototype.slice.call(this.hooks[key]);

      for (var i = 0, leni = handlers.length; i < leni; i++) {
        handlers[i].call(instance, p1, p2, p3, p4, p5);

        if(handlers[i].runOnce){
          this.remove(key, handlers[i]);
        }
      }

    }

  };

  PluginHookClass.prototype.execute = function (instance, key, p1, p2, p3, p4, p5) {
    var res, handlers;

    // provide support for old versions of HOT
    if (key in legacy) {
      key = legacy[key];
    }

    //performance considerations - http://jsperf.com/call-vs-apply-for-a-plugin-architecture
      if (typeof this.hooks[key] !== 'undefined') {

        handlers = Array.prototype.slice.call(this.hooks[key]);

        for (var i = 0, leni = handlers.length; i < leni; i++) {

          res = handlers[i].call(instance, p1, p2, p3, p4, p5);
          if (res !== void 0) {
            p1 = res;
          }

          if(handlers[i].runOnce){
            this.remove(key, handlers[i]);
          }

          if(res === false){ //if any handler returned false
            return false; //event has been cancelled and further execution of handler queue is being aborted
          }

        }

      }

    return p1;
  };

  return PluginHookClass;

})();

Handsontable.PluginHooks = new Handsontable.PluginHookClass();

(function (Handsontable) {

  function HandsontableAutoColumnSize() {
    var plugin = this
      , sampleCount = 5; //number of samples to take of each value length

    this.beforeInit = function () {
      var instance = this;
      instance.autoColumnWidths = [];

      if (instance.getSettings().autoColumnSize !== false) {
        if (!instance.autoColumnSizeTmp) {
          instance.autoColumnSizeTmp = {
            table: null,
            tableStyle: null,
            theadTh: null,
            tbody: null,
            container: null,
            containerStyle: null,
            determineBeforeNextRender: true
          };

          instance.addHook('beforeRender', htAutoColumnSize.determineIfChanged);
          instance.addHook('afterGetColWidth', htAutoColumnSize.getColWidth);
          instance.addHook('afterDestroy', htAutoColumnSize.afterDestroy);

          instance.determineColumnWidth = plugin.determineColumnWidth;
        }
      } else {
        if (instance.autoColumnSizeTmp) {
          instance.removeHook('beforeRender', htAutoColumnSize.determineIfChanged);
          instance.removeHook('afterGetColWidth', htAutoColumnSize.getColWidth);
          instance.removeHook('afterDestroy', htAutoColumnSize.afterDestroy);

          delete instance.determineColumnWidth;

          plugin.afterDestroy.call(instance);
        }
      }
    };

    this.determineIfChanged = function (force) {
      if (force) {
        htAutoColumnSize.determineColumnsWidth.apply(this, arguments);
      }
    };

    this.determineColumnWidth = function (col) {
      var instance = this
        , tmp = instance.autoColumnSizeTmp;

      if (!tmp.container) {
        createTmpContainer.call(tmp, instance);
      }

      tmp.container.className = instance.rootElement[0].className + ' htAutoColumnSize';
      tmp.table.className = instance.$table[0].className;

      var rows = instance.countRows();
      var samples = {};
      var maxLen = 0;
      for (var r = 0; r < rows; r++) {
        var value = Handsontable.helper.stringify(instance.getDataAtCell(r, col));
        var len = value.length;
        if (len > maxLen) {
          maxLen = len;
        }
        if (!samples[len]) {
          samples[len] = {
            needed: sampleCount,
            strings: []
          };
        }
        if (samples[len].needed) {
          samples[len].strings.push({value: value, row: r});
          samples[len].needed--;
        }
      }

      var settings = instance.getSettings();
      if (settings.colHeaders) {
        instance.view.appendColHeader(col, tmp.theadTh); //TH innerHTML
      }

      instance.view.wt.wtDom.empty(tmp.tbody);

      for (var i in samples) {
        if (samples.hasOwnProperty(i)) {
          for (var j = 0, jlen = samples[i].strings.length; j < jlen; j++) {
            var row = samples[i].strings[j].row;

            var cellProperties = instance.getCellMeta(row, col);
            cellProperties.col = col;
            cellProperties.row = row;

            var renderer = instance.getCellRenderer(cellProperties);

            var tr = document.createElement('tr');
            var td = document.createElement('td');

            renderer(instance, td, row, col, instance.colToProp(col), samples[i].strings[j].value, cellProperties);
            r++;
            tr.appendChild(td);
            tmp.tbody.appendChild(tr);
          }
        }
      }

      var parent = instance.rootElement[0].parentNode;
      parent.appendChild(tmp.container);
      var width = instance.view.wt.wtDom.outerWidth(tmp.table);
      parent.removeChild(tmp.container);

      if (!settings.nativeScrollbars) { //with native scrollbars a cell size can safely exceed the width of the viewport
        var maxWidth = instance.view.wt.wtViewport.getViewportWidth() - 2; //2 is some overhead for cell border
        if (width > maxWidth) {
          width = maxWidth;
        }
      }

      return width;
    };

    this.determineColumnsWidth = function () {
      var instance = this;
      var settings = this.getSettings();
      if (settings.autoColumnSize || !settings.colWidths) {
        var cols = this.countCols();
        for (var c = 0; c < cols; c++) {
          if (!instance._getColWidthFromSettings(c)) {
            this.autoColumnWidths[c] = plugin.determineColumnWidth.call(instance, c);
          }
        }
      }
    };

    this.getColWidth = function (col, response) {
      if (this.autoColumnWidths[col] && this.autoColumnWidths[col] > response.width) {
        response.width = this.autoColumnWidths[col];
      }
    };

    this.afterDestroy = function () {
      var instance = this;
      if (instance.autoColumnSizeTmp && instance.autoColumnSizeTmp.container && instance.autoColumnSizeTmp.container.parentNode) {
        instance.autoColumnSizeTmp.container.parentNode.removeChild(instance.autoColumnSizeTmp.container);
      }
      instance.autoColumnSizeTmp = null;
    };

    function createTmpContainer(instance) {
      var d = document
        , tmp = this;

      tmp.table = d.createElement('table');
      tmp.theadTh = d.createElement('th');
      tmp.table.appendChild(d.createElement('thead')).appendChild(d.createElement('tr')).appendChild(tmp.theadTh);

      tmp.tableStyle = tmp.table.style;
      tmp.tableStyle.tableLayout = 'auto';
      tmp.tableStyle.width = 'auto';

      tmp.tbody = d.createElement('tbody');
      tmp.table.appendChild(tmp.tbody);

      tmp.container = d.createElement('div');
      tmp.container.className = instance.rootElement[0].className + ' hidden';
      tmp.containerStyle = tmp.container.style;

      tmp.container.appendChild(tmp.table);
    }
  }

  var htAutoColumnSize = new HandsontableAutoColumnSize();

  Handsontable.PluginHooks.add('beforeInit', htAutoColumnSize.beforeInit);
  Handsontable.PluginHooks.add('afterUpdateSettings', htAutoColumnSize.beforeInit);

})(Handsontable);

/**
 * This plugin sorts the view by a column (but does not sort the data source!)
 * @constructor
 */
function HandsontableColumnSorting() {
  var plugin = this;

  this.init = function (source) {
    var instance = this;
    var sortingSettings = instance.getSettings().columnSorting;
    var sortingColumn, sortingOrder;

    instance.sortingEnabled = !!(sortingSettings);

    if (instance.sortingEnabled) {
      instance.sortIndex = [];

      var loadedSortingState = loadSortingState.call(instance);

      if (typeof loadedSortingState != 'undefined') {
        sortingColumn = loadedSortingState.sortColumn;
        sortingOrder = loadedSortingState.sortOrder;
      } else {
        sortingColumn = sortingSettings.column;
        sortingOrder = sortingSettings.sortOrder;
      }
      plugin.sortByColumn.call(instance, sortingColumn, sortingOrder);

      instance.sort = function(){
        var args = Array.prototype.slice.call(arguments);

        return plugin.sortByColumn.apply(instance, args)
      };

      if (typeof instance.getSettings().observeChanges == 'undefined'){
        enableObserveChangesPlugin.call(instance);
      }

      if (source == 'afterInit') {
        bindColumnSortingAfterClick.call(instance);

        instance.addHook('afterCreateRow', plugin.afterCreateRow);
        instance.addHook('afterRemoveRow', plugin.afterRemoveRow);
        instance.addHook('afterLoadData', plugin.init);
      }
    } else {
      delete instance.sort;

      instance.removeHook('afterCreateRow', plugin.afterCreateRow);
      instance.removeHook('afterRemoveRow', plugin.afterRemoveRow);
      instance.removeHook('afterLoadData', plugin.init);
    }
  };

  this.setSortingColumn = function (col, order) {
    var instance = this;

    if (typeof col == 'undefined') {
      delete instance.sortColumn;
      delete instance.sortOrder;

      return;
    } else if (instance.sortColumn === col && typeof order == 'undefined') {
      instance.sortOrder = !instance.sortOrder;
    } else {
      instance.sortOrder = typeof order != 'undefined' ? order : true;
    }

    instance.sortColumn = col;

  };

  this.sortByColumn = function (col, order) {
    var instance = this;

    plugin.setSortingColumn.call(instance, col, order);

    if(typeof instance.sortColumn == 'undefined'){
      return;
    }

    instance.PluginHooks.run('beforeColumnSort', instance.sortColumn, instance.sortOrder);

    plugin.sort.call(instance);
    instance.render();

    saveSortingState.call(instance);

    instance.PluginHooks.run('afterColumnSort', instance.sortColumn, instance.sortOrder);
  };

  var saveSortingState = function () {
    var instance = this;

    var sortingState = {};

    if (typeof instance.sortColumn != 'undefined') {
      sortingState.sortColumn = instance.sortColumn;
    }

    if (typeof instance.sortOrder != 'undefined') {
      sortingState.sortOrder = instance.sortOrder;
    }

    if (sortingState.hasOwnProperty('sortColumn') || sortingState.hasOwnProperty('sortOrder')) {
      instance.PluginHooks.run('persistentStateSave', 'columnSorting', sortingState);
    }

  };

  var loadSortingState = function () {
    var instance = this;
    var storedState = {};
    instance.PluginHooks.run('persistentStateLoad', 'columnSorting', storedState);

    return storedState.value;
  };

  var bindColumnSortingAfterClick = function () {
    var instance = this;

    instance.rootElement.on('click.handsontable', '.columnSorting', function (e) {
      if (instance.view.wt.wtDom.hasClass(e.target, 'columnSorting')) {
        var col = getColumn(e.target);
        plugin.sortByColumn.call(instance, col);
      }
    });

    function countRowHeaders() {
      var THs = instance.view.TBODY.querySelector('tr').querySelectorAll('th');
      return THs.length;
    }

    function getColumn(target) {
      var TH = instance.view.wt.wtDom.closest(target, 'TH');
      return instance.view.wt.wtDom.index(TH) - countRowHeaders();
    }
  };

  function enableObserveChangesPlugin () {
    var instance = this;
    instance.registerTimeout('enableObserveChanges', function(){
      instance.updateSettings({
        observeChanges: true
      });
    }, 0);
  }

  function defaultSort(sortOrder) {
    return function (a, b) {
      if (a[1] === b[1]) {
        return 0;
      }
      if (a[1] === null) {
        return 1;
      }
      if (b[1] === null) {
        return -1;
      }
      if (a[1] < b[1]) return sortOrder ? -1 : 1;
      if (a[1] > b[1]) return sortOrder ? 1 : -1;
      return 0;
    }
  }

  function dateSort(sortOrder) {
    return function (a, b) {
      if (a[1] === b[1]) {
        return 0;
      }
      if (a[1] === null) {
        return 1;
      }
      if (b[1] === null) {
        return -1;
      }

      var aDate = new Date(a[1]);
      var bDate = new Date(b[1]);

      if (aDate < bDate) return sortOrder ? -1 : 1;
      if (aDate > bDate) return sortOrder ? 1 : -1;

      return 0;
    }
  }

  this.sort = function () {
    var instance = this;

    if (typeof instance.sortOrder == 'undefined') {
      return;
    }

    instance.sortingEnabled = false; //this is required by translateRow plugin hook
    instance.sortIndex.length = 0;

    var colOffset = this.colOffset();
    for (var i = 0, ilen = this.countRows() - instance.getSettings()['minSpareRows']; i < ilen; i++) {
      this.sortIndex.push([i, instance.getDataAtCell(i, this.sortColumn + colOffset)]);
    }

    var colMeta = instance.getCellMeta(0, instance.sortColumn);
    var sortFunction;
    switch (colMeta.type) {
      case 'date':
        sortFunction = dateSort;
        break;
      default:
        sortFunction = defaultSort;
    }

    this.sortIndex.sort(sortFunction(instance.sortOrder));

    //Append spareRows
    for(var i = this.sortIndex.length; i < instance.countRows(); i++){
      this.sortIndex.push([i, instance.getDataAtCell(i, this.sortColumn + colOffset)]);
    }

    instance.sortingEnabled = true; //this is required by translateRow plugin hook
  };

  this.translateRow = function (row) {
    var instance = this;

    if (instance.sortingEnabled && instance.sortIndex && instance.sortIndex.length && instance.sortIndex[row]) {
      return instance.sortIndex[row][0];
    }

    return row;
  };

  this.onBeforeGetSet = function (getVars) {
    var instance = this;
    getVars.row = plugin.translateRow.call(instance, getVars.row);
  };

  this.untranslateRow = function (row) {
    var instance = this;
    if (instance.sortingEnabled && instance.sortIndex && instance.sortIndex.length) {
      for (var i = 0; i < instance.sortIndex.length; i++) {
        if (instance.sortIndex[i][0] == row) {
          return i;
        }
      }
    }
  };

  this.getColHeader = function (col, TH) {
    if (this.getSettings().columnSorting) {
      this.view.wt.wtDom.addClass(TH.querySelector('.colHeader'), 'columnSorting');
    }
  };

  function isSorted(instance){
    return typeof instance.sortColumn != 'undefined';
  }

  this.afterCreateRow = function(index, amount){
    var instance = this;

    if(!isSorted(instance)){
      return;
    }


    for(var i = 0; i < instance.sortIndex.length; i++){
      if (instance.sortIndex[i][0] >= index){
        instance.sortIndex[i][0] += amount;
      }
    }

    for(var i=0; i < amount; i++){
      instance.sortIndex.splice(index+i, 0, [index+i, instance.getData()[index+i][instance.sortColumn + instance.colOffset()]]);
    }



    saveSortingState.call(instance);

  };

  this.afterRemoveRow = function(index, amount){
    var instance = this;

    if(!isSorted(instance)){
      return;
    }

    var physicalRemovedIndex = plugin.translateRow.call(instance, index);

    instance.sortIndex.splice(index, amount);

    for(var i = 0; i < instance.sortIndex.length; i++){

      if (instance.sortIndex[i][0] > physicalRemovedIndex){
        instance.sortIndex[i][0] -= amount;
      }
    }

    saveSortingState.call(instance);

  };

  this.afterChangeSort = function (changes/*, source*/) {
    var instance = this;
    var sortColumnChanged = false;
    var selection = {};
    if (!changes) {
      return;
    }

    for (var i = 0; i < changes.length; i++) {
      if (changes[i][1] == instance.sortColumn) {
        sortColumnChanged = true;
        selection.row = plugin.translateRow.call(instance, changes[i][0]);
        selection.col = changes[i][1];
        break;
      }
    }

    if (sortColumnChanged) {
      setTimeout(function () {
        plugin.sort.call(instance);
        instance.render();
        instance.selectCell(plugin.untranslateRow.call(instance, selection.row), selection.col);
      }, 0);
    }
  };
}
var htSortColumn = new HandsontableColumnSorting();

Handsontable.PluginHooks.add('afterInit', function () {
  htSortColumn.init.call(this, 'afterInit')
});
Handsontable.PluginHooks.add('afterUpdateSettings', function () {
  htSortColumn.init.call(this, 'afterUpdateSettings')
});
Handsontable.PluginHooks.add('beforeGet', htSortColumn.onBeforeGetSet);
Handsontable.PluginHooks.add('beforeSet', htSortColumn.onBeforeGetSet);
Handsontable.PluginHooks.add('afterGetColHeader', htSortColumn.getColHeader);


(function (Handsontable) {
  'use strict';

  function ContextMenu(instance, customOptions){
    this.instance = instance;
    var contextMenu = this;

    this.menu = createMenu();
    this.enabled = true;

    this.bindMouseEvents();
    this.bindTableEvents();

    this.instance.addHook('afterDestroy', function () {
       contextMenu.destroy();
    });

    this.defaultOptions = {
      items: {
        'row_above': {
          name: 'Insert row above',
          callback: function(key, selection){
            this.alter("insert_row", selection.start.row());
          },
          disabled: function () {
            return this.countRows() >= this.getSettings().maxRows;
          }
        },
        'row_below': {
          name: 'Insert row below',
          callback: function(key, selection){
            this.alter("insert_row", selection.end.row() + 1);
          },
          disabled: function () {
            return this.countRows() >= this.getSettings().maxRows;
          }
        },
        "hsep1": ContextMenu.SEPARATOR,
        'col_left': {
          name: 'Insert column on the left',
          callback: function(key, selection){
            this.alter("insert_col", selection.start.col());
          },
          disabled: function () {
            return this.countCols() >= this.getSettings().maxCols;
          }
        },
        'col_right': {
          name: 'Insert column on the right',
          callback: function(key, selection){
            this.alter("insert_col", selection.end.col() + 1);
          },
          disabled: function () {
            return this.countCols() >= this.getSettings().maxCols;
          }
        },
        "hsep2": ContextMenu.SEPARATOR,
        'remove_row': {
          name: 'Remove row',
          callback: function(key, selection){
            var amount = selection.end.row() - selection.start.row() + 1;
            this.alter("remove_row", selection.start.row(), amount);
          }
        },
        'remove_col': {
          name: 'Remove column',
          callback: function(key, selection){
            var amount = selection.end.col() - selection.start.col() + 1;
            this.alter("remove_col", selection.start.col(), amount);
          }
        },
        "hsep3": ContextMenu.SEPARATOR,
        'undo': {
          name: 'Undo',
          callback: function(){
            this.undo();
          },
          disabled: function () {
            return this.undoRedo && !this.undoRedo.isUndoAvailable();
          }
        },
        'redo': {
          name: 'Redo',
          callback: function(){
            this.redo();
          },
          disabled: function () {
            return this.undoRedo && !this.undoRedo.isRedoAvailable();
          }
        }

      }
    };

    this.options = {};
    Handsontable.helper.extend(this.options, this.defaultOptions);

    this.updateOptions(customOptions);

    function createMenu(){

      var menu = $('body > .htContextMenu')[0];

      if(!menu){
        menu = document.createElement('DIV');
        Handsontable.Dom.addClass(menu, 'htContextMenu');
        document.getElementsByTagName('body')[0].appendChild(menu);
      }

      return menu;
    }
  }

  ContextMenu.prototype.bindMouseEvents = function (){

    function contextMenuOpenListener(event){

      event.preventDefault();

      if(event.target.nodeName != 'TD' && !(Handsontable.Dom.hasClass(event.target, 'current') && Handsontable.Dom.hasClass(event.target, 'wtBorder'))){
        return;
      }

      this.show(event.pageY, event.pageX);

      $(document).on('mousedown.htContextMenu', Handsontable.helper.proxy(ContextMenu.prototype.close, this));
    }

    this.instance.rootElement.on('contextmenu.htContextMenu', Handsontable.helper.proxy(contextMenuOpenListener, this));

  };

  ContextMenu.prototype.bindTableEvents = function () {
    var that = this;

    this._afterScrollCallback = function () {
      that.close();
    };

    this.instance.addHook('afterScrollVertically', this._afterScrollCallback);
    this.instance.addHook('afterScrollHorizontally', this._afterScrollCallback);
  };

  ContextMenu.prototype.unbindTableEvents = function () {
    var that = this;

    if(this._afterScrollCallback){
      this.instance.removeHook('afterScrollVertically', this._afterScrollCallback);
      this.instance.removeHook('afterScrollHorizontally', this._afterScrollCallback);
      this._afterScrollCallback = null;
    }


  };

  ContextMenu.prototype.performAction = function (){

    var hot = $(this.menu).handsontable('getInstance');
    var selectedItemIndex = hot.getSelected()[0];
    var selectedItem = hot.getData()[selectedItemIndex];

    if (selectedItem.disabled === true || (typeof selectedItem.disabled == 'function' && selectedItem.disabled.call(this.instance) === true)){
      return;
    }

    if(typeof selectedItem.callback != 'function'){
      return;
    }

    var corners = this.instance.getSelected();
    var normalizedSelection = ContextMenu.utils.normalizeSelection(corners);

    selectedItem.callback.call(this.instance, selectedItem.key, normalizedSelection);

  };

  ContextMenu.prototype.unbindMouseEvents = function () {
    this.instance.rootElement.off('contextmenu.htContextMenu');
    $(document).off('mousedown.htContextMenu');
  };

  ContextMenu.prototype.show = function(top, left){

    this.menu.style.display = 'block';

    $(this.menu)
      .off('mousedown.htContextMenu')
      .on('mousedown.htContextMenu', Handsontable.helper.proxy(this.performAction, this));

    $(this.menu).handsontable({
      data: ContextMenu.utils.convertItemsToArray(this.getItems()),
      colHeaders: false,
      colWidths: [160],
      readOnly: true,
      copyPaste: false,
      columns: [
        {
          data: 'name',
          renderer: Handsontable.helper.proxy(this.renderer, this)
        }
      ],
      beforeKeyDown: Handsontable.helper.proxy(this.onBeforeKeyDown, this)
    });
    this.bindTableEvents();

    this.setMenuPosition(top, left);

    $(this.menu).handsontable('listen');

  };

  ContextMenu.prototype.close = function () {
    this.hide();
    $(document).off('mousedown.htContextMenu');
    this.unbindTableEvents();
    this.instance.listen();
  };

  ContextMenu.prototype.hide = function(){
    this.menu.style.display = 'none';
    $(this.menu).handsontable('destroy');
  };

  ContextMenu.prototype.renderer = function(instance, TD, row, col, prop, value, cellProperties){
    var contextMenu = this;
    var item = instance.getData()[row];
    var wrapper = document.createElement('DIV');

    Handsontable.Dom.empty(TD);
    TD.appendChild(wrapper);

    if(itemIsSeparator(item)){
      Handsontable.Dom.addClass(TD, 'htSeparator');
    } else {
      Handsontable.Dom.fastInnerText(wrapper, value);
    }

    if (itemIsDisabled(item, contextMenu.instance)){
      Handsontable.Dom.addClass(TD, 'htDisabled');

      $(wrapper).on('mouseenter', function () {
        instance.deselectCell();
      });

    } else {
      Handsontable.Dom.removeClass(TD, 'htDisabled');

      $(wrapper).on('mouseenter', function () {
        instance.selectCell(row, col);
      });

    }

    function itemIsSeparator(item){
      return new RegExp(ContextMenu.SEPARATOR, 'i').test(item.name);
    }

    function itemIsDisabled(item, instance){
      return item.disabled === true || (typeof item.disabled == 'function' && item.disabled.call(contextMenu.instance) === true);
    }
  };

  ContextMenu.prototype.onBeforeKeyDown = function (event) {
    var contextMenu = this;
    var instance = $(contextMenu.menu).handsontable('getInstance');
    var selection = instance.getSelected();

    switch(event.keyCode){

      case Handsontable.helper.keyCode.ESCAPE:
        contextMenu.close();
        event.preventDefault();
        event.stopImmediatePropagation();
        break;

      case Handsontable.helper.keyCode.ENTER:
        if(instance.getSelected()){
          contextMenu.performAction();
          contextMenu.close();
        }
        break;

      case Handsontable.helper.keyCode.ARROW_DOWN:
        if(!selection){

          selectFirstCell(instance);

        } else {

          selectNextCell(selection[0], selection[1], instance);

        }

        event.preventDefault();
        event.stopImmediatePropagation();

        break;

      case Handsontable.helper.keyCode.ARROW_UP:
        if(!selection){

          selectLastCell(instance);

        }  else {

          selectPrevCell(selection[0], selection[1], instance);

        }

        event.preventDefault();
        event.stopImmediatePropagation();

        break;

    }

    function selectFirstCell(instance) {

      var firstCell = instance.getCell(0, 0);

      if(ContextMenu.utils.isSeparator(firstCell) || ContextMenu.utils.isDisabled(firstCell)){
        selectNextCell(0, 0, instance);
      } else {
        instance.selectCell(0, 0);
      }

    }


    function selectLastCell(instance) {

      var lastRow = instance.countRows() - 1;
      var lastCell = instance.getCell(lastRow, 0);

      if(ContextMenu.utils.isSeparator(lastCell) || ContextMenu.utils.isDisabled(lastCell)){
        selectPrevCell(lastRow, 0, instance);
      } else {
        instance.selectCell(lastRow, 0);
      }

    }

    function selectNextCell(row, col, instance){
      var nextRow = row + 1;
      var nextCell =  nextRow < instance.countRows() ? instance.getCell(nextRow, col) : null;

      if(!nextCell){
        return;
      }

      if(ContextMenu.utils.isSeparator(nextCell) || ContextMenu.utils.isDisabled(nextCell)){
        selectNextCell(nextRow, col, instance);
      } else {
        instance.selectCell(nextRow, col);
      }
    }

    function selectPrevCell(row, col, instance) {

      var prevRow = row - 1;
      var prevCell = prevRow >= 0 ? instance.getCell(prevRow, col) : null;

      if (!prevCell) {
        return;
      }

      if(ContextMenu.utils.isSeparator(prevCell) || ContextMenu.utils.isDisabled(prevCell)){
        selectPrevCell(prevRow, col, instance);
      } else {
        instance.selectCell(prevRow, col);
      }

    }

  };

  ContextMenu.prototype.getItems = function () {
    var items = {};
    function Item(rawItem){
      if(typeof rawItem == 'string'){
        this.name = rawItem;
      } else {
        Handsontable.helper.extend(this, rawItem);
      }
    }
    Item.prototype = this.options;

    for(var itemName in this.options.items){
      if(this.options.items.hasOwnProperty(itemName) && (!this.itemsFilter || this.itemsFilter.indexOf(itemName) != -1)){
        items[itemName] = new Item(this.options.items[itemName]);
      }
    }

    return items;

  };

  ContextMenu.prototype.updateOptions = function(newOptions){
    newOptions = newOptions || {};

    if(newOptions.items){
      for(var itemName in newOptions.items){
        var item = {};

        if(newOptions.items.hasOwnProperty(itemName)) {
          if(this.defaultOptions.items.hasOwnProperty(itemName)
            && Handsontable.helper.isObject(newOptions.items[itemName])){
            Handsontable.helper.extend(item, this.defaultOptions.items[itemName]);
            Handsontable.helper.extend(item, newOptions.items[itemName]);
            newOptions.items[itemName] = item;
          }
        }

      }
    }

    Handsontable.helper.extend(this.options, newOptions);
  };

  ContextMenu.prototype.setMenuPosition = function (cursorY, cursorX) {

    var cursor = {
      top:  cursorY,
      topRelative: cursorY - document.documentElement.scrollTop,
      left: cursorX,
      leftRelative:cursorX - document.documentElement.scrollLeft
    };

    if(this.menuFitsBelowCursor(cursor)){
      this.positionMenuBelowCursor(cursor);
    } else {
      this.positionMenuAboveCursor(cursor);
    }

    if(this.menuFitsOnRightOfCursor(cursor)){
      this.positionMenuOnRightOfCursor(cursor);
    } else {
      this.positionMenuOnLeftOfCursor(cursor);
    }

  };

  ContextMenu.prototype.menuFitsBelowCursor = function (cursor) {
    return cursor.topRelative + this.menu.offsetHeight <= document.documentElement.scrollTop + document.documentElement.clientHeight;
  };

  ContextMenu.prototype.menuFitsOnRightOfCursor = function (cursor) {
    return cursor.leftRelative + this.menu.offsetWidth <= document.documentElement.scrollLeft + document.documentElement.clientWidth;
  };

  ContextMenu.prototype.positionMenuBelowCursor = function (cursor) {
    this.menu.style.top = cursor.top + 'px';
  };

  ContextMenu.prototype.positionMenuAboveCursor = function (cursor) {
    this.menu.style.top = (cursor.top - this.menu.offsetHeight) + 'px';
  };

  ContextMenu.prototype.positionMenuOnRightOfCursor = function (cursor) {
    this.menu.style.left = cursor.left + 'px';
  };

  ContextMenu.prototype.positionMenuOnLeftOfCursor = function (cursor) {
    this.menu.style.left = (cursor.left - this.menu.offsetWidth) + 'px';
  };

  ContextMenu.utils = {};
  ContextMenu.utils.convertItemsToArray = function (items) {
    var itemArray = [];
    var item;
    for(var itemName in items){
      if(items.hasOwnProperty(itemName)){
        if(typeof items[itemName] == 'string'){
          item = {name: items[itemName]};
        } else if (items[itemName].visible !== false) {
          item = items[itemName];
        } else {
          continue;
        }

        item.key = itemName;
        itemArray.push(item);
      }
    }

    return itemArray;
  };

  ContextMenu.utils.normalizeSelection = function(corners){
    var selection = {
      start: new Handsontable.SelectionPoint(),
      end: new Handsontable.SelectionPoint()
    };

    selection.start.row(Math.min(corners[0], corners[2]));
    selection.start.col(Math.min(corners[1], corners[3]));

    selection.end.row(Math.max(corners[0], corners[2]));
    selection.end.col(Math.max(corners[1], corners[3]));

    return selection;
  };

  ContextMenu.utils.isSeparator = function (cell) {
    return Handsontable.Dom.hasClass(cell, 'htSeparator');
  };

  ContextMenu.utils.isDisabled = function (cell) {
    return Handsontable.Dom.hasClass(cell, 'htDisabled');
  };

  ContextMenu.prototype.enable = function () {
    if(!this.enabled){
      this.enabled = true;
      this.bindMouseEvents();
    }
  };

  ContextMenu.prototype.disable = function () {
    if(this.enabled){
      this.enabled = false;
      this.close();
      this.unbindMouseEvents();
      this.unbindTableEvents();
    }
  };

  ContextMenu.prototype.destroy = function () {
    this.close();
    this.unbindMouseEvents();
    this.unbindTableEvents();

    if(!this.isMenuEnabledByOtherHotInstance()){
      this.removeMenu();
    }

  };

  ContextMenu.prototype.isMenuEnabledByOtherHotInstance = function () {
    var hotContainers = $('.handsontable');
    var menuEnabled = false;

    for(var i = 0, len = hotContainers.length; i < len; i++){
      var instance = $(hotContainers[i]).handsontable('getInstance');
      if(instance && instance.getSettings().contextMenu){
        menuEnabled = true;
        break;
      }
    }

    return menuEnabled;
  };

  ContextMenu.prototype.removeMenu = function () {
    if(this.menu.parentNode){
      this.menu.parentNode.removeChild(this.menu);
    }
  }

  ContextMenu.prototype.filterItems = function(itemsToLeave){
    this.itemsFilter = itemsToLeave;
  };

  ContextMenu.SEPARATOR = "---------";

  function init(){
    var instance = this;
    var contextMenuSetting = instance.getSettings().contextMenu;
    var customOptions = Handsontable.helper.isObject(contextMenuSetting) ? contextMenuSetting : {};

    if(contextMenuSetting){
      if(!instance.contextMenu){
        instance.contextMenu = new ContextMenu(instance, customOptions);
      }

      instance.contextMenu.enable();

      if(Handsontable.helper.isArray(contextMenuSetting)){
        instance.contextMenu.filterItems(contextMenuSetting);
      }

    }  else if(instance.contextMenu){
      instance.contextMenu.destroy();
      delete instance.contextMenu;
    }



  }

  Handsontable.PluginHooks.add('afterInit', init);
  Handsontable.PluginHooks.add('afterUpdateSettings', init);

  Handsontable.ContextMenu = ContextMenu;

})(Handsontable);

/**
 * This plugin adds support for legacy features, deprecated APIs, etc.
 */

/**
 * Support for old autocomplete syntax
 * For old syntax, see: https://github.com/warpech/jquery-handsontable/blob/8c9e701d090ea4620fe08b6a1a048672fadf6c7e/README.md#defining-autocomplete
 */
Handsontable.PluginHooks.add('beforeGetCellMeta', function (row, col, cellProperties) {
  //isWritable - deprecated since 0.8.0
  cellProperties.isWritable = !cellProperties.readOnly;

  //autocomplete - deprecated since 0.7.1 (see CHANGELOG.md)
  if (cellProperties.autoComplete) {
    throw new Error("Support for legacy autocomplete syntax was removed in Handsontable 0.10.0. Please remove the property named 'autoComplete' from your config. For replacement instructions, see wiki page https://github.com/warpech/jquery-handsontable/wiki/Migration-guide-to-0.10.x");
  }
});
function HandsontableManualColumnMove() {
  var pressed
    , startCol
    , endCol
    , startX
    , startOffset;

  var ghost = document.createElement('DIV')
    , ghostStyle = ghost.style;

  ghost.className = 'ghost';
  ghostStyle.position = 'absolute';
  ghostStyle.top = '25px';
  ghostStyle.left = 0;
  ghostStyle.width = '10px';
  ghostStyle.height = '10px';
  ghostStyle.backgroundColor = '#CCC';
  ghostStyle.opacity = 0.7;

  var saveManualColumnPositions = function () {
    var instance = this;

    instance.PluginHooks.run('persistentStateSave', 'manualColumnPositions', instance.manualColumnPositions);
  };

  var loadManualColumnPositions = function () {
    var instance = this;
    var storedState = {};
    instance.PluginHooks.run('persistentStateLoad', 'manualColumnPositions', storedState);

    return storedState.value;
  };


  var bindMoveColEvents = function () {
    var instance = this;

    instance.rootElement.on('mousemove.manualColumnMove', function (e) {
      if (pressed) {
        ghostStyle.left = startOffset + e.pageX - startX + 6 + 'px';
        if (ghostStyle.display === 'none') {
          ghostStyle.display = 'block';
        }
      }
    });

    instance.rootElement.on('mouseup.manualColumnMove', function () {
      if (pressed) {
        if (startCol < endCol) {
          endCol--;
        }
        if (instance.getSettings().rowHeaders) {
          startCol--;
          endCol--;
        }
        instance.manualColumnPositions.splice(endCol, 0, instance.manualColumnPositions.splice(startCol, 1)[0]);
        $('.manualColumnMover.active').removeClass('active');
        pressed = false;
        instance.forceFullRender = true;
        instance.view.render(); //updates all
        ghostStyle.display = 'none';

        saveManualColumnPositions.call(instance);

        instance.PluginHooks.run('afterColumnMove', startCol, endCol);
      }
    });

    instance.rootElement.on('mousedown.manualColumnMove', '.manualColumnMover', function (e) {

      var mover = e.currentTarget;
      var TH = instance.view.wt.wtDom.closest(mover, 'TH');
      startCol = instance.view.wt.wtDom.index(TH) + instance.colOffset();
      endCol = startCol;
      pressed = true;
      startX = e.pageX;

      var TABLE = instance.$table[0];
      TABLE.parentNode.appendChild(ghost);
      ghostStyle.width = instance.view.wt.wtDom.outerWidth(TH) + 'px';
      ghostStyle.height = instance.view.wt.wtDom.outerHeight(TABLE) + 'px';
      startOffset = parseInt(instance.view.wt.wtDom.offset(TH).left - instance.view.wt.wtDom.offset(TABLE).left, 10);
      ghostStyle.left = startOffset + 6 + 'px';
    });

    instance.rootElement.on('mouseenter.manualColumnMove', 'td, th', function () {
      if (pressed) {
        var active = instance.view.THEAD.querySelector('.manualColumnMover.active');
        if (active) {
          instance.view.wt.wtDom.removeClass(active, 'active');
        }
        endCol = instance.view.wt.wtDom.index(this) + instance.colOffset();
        var THs = instance.view.THEAD.querySelectorAll('th');
        var mover = THs[endCol].querySelector('.manualColumnMover');
        instance.view.wt.wtDom.addClass(mover, 'active');
      }
    });

    instance.addHook('afterDestroy', unbindMoveColEvents);
  };

  var unbindMoveColEvents = function(){
    var instance = this;
    instance.rootElement.off('mouseup.manualColumnMove');
    instance.rootElement.off('mousemove.manualColumnMove');
    instance.rootElement.off('mousedown.manualColumnMove');
    instance.rootElement.off('mouseenter.manualColumnMove');
  };

  this.beforeInit = function () {
    this.manualColumnPositions = [];
  };

  this.init = function (source) {
    var instance = this;

    var manualColMoveEnabled = !!(this.getSettings().manualColumnMove);

    if (manualColMoveEnabled) {
      var initialManualColumnPositions = this.getSettings().manualColumnMove;

      var loadedManualColumnPositions = loadManualColumnPositions.call(instance);

      if (typeof loadedManualColumnPositions != 'undefined') {
        this.manualColumnPositions = loadedManualColumnPositions;
      } else if (initialManualColumnPositions instanceof Array) {
        this.manualColumnPositions = initialManualColumnPositions;
      } else {
        this.manualColumnPositions = [];
      }


      instance.forceFullRender = true;

      if (source == 'afterInit') {
        bindMoveColEvents.call(this);
        if (this.manualColumnPositions.length > 0) {
          this.forceFullRender = true;
          this.render();
        }

      }

    } else {
      unbindMoveColEvents.call(this);
      this.manualColumnPositions = [];
    }
  };

  this.modifyCol = function (col) {
    //TODO test performance: http://jsperf.com/object-wrapper-vs-primitive/2
    if (this.getSettings().manualColumnMove) {
      if (typeof this.manualColumnPositions[col] === 'undefined') {
        this.manualColumnPositions[col] = col;
      }
      return this.manualColumnPositions[col];
    }
    return col;
  };

  this.getColHeader = function (col, TH) {
    if (this.getSettings().manualColumnMove) {
      var DIV = document.createElement('DIV');
      DIV.className = 'manualColumnMover';
      TH.firstChild.appendChild(DIV);
    }
  };
}
var htManualColumnMove = new HandsontableManualColumnMove();

Handsontable.PluginHooks.add('beforeInit', htManualColumnMove.beforeInit);
Handsontable.PluginHooks.add('afterInit', function () {
  htManualColumnMove.init.call(this, 'afterInit')
});

Handsontable.PluginHooks.add('afterUpdateSettings', function () {
  htManualColumnMove.init.call(this, 'afterUpdateSettings')
});
Handsontable.PluginHooks.add('afterGetColHeader', htManualColumnMove.getColHeader);
Handsontable.PluginHooks.add('modifyCol', htManualColumnMove.modifyCol);

function HandsontableManualColumnResize() {
  var pressed
    , currentTH
    , currentCol
    , currentWidth
    , instance
    , newSize
    , startX
    , startWidth
    , startOffset
    , resizer = document.createElement('DIV')
    , handle = document.createElement('DIV')
    , line = document.createElement('DIV')
    , lineStyle = line.style;

  resizer.className = 'manualColumnResizer';

  handle.className = 'manualColumnResizerHandle';
  resizer.appendChild(handle);

  line.className = 'manualColumnResizerLine';
  resizer.appendChild(line);

  var $document = $(document);

  $document.mousemove(function (e) {
    if (pressed) {
      currentWidth = startWidth + (e.pageX - startX);
      newSize = setManualSize(currentCol, currentWidth); //save col width
      resizer.style.left = startOffset + currentWidth + 'px';
    }
  });

  $document.mouseup(function () {
    if (pressed) {
      instance.view.wt.wtDom.removeClass(resizer, 'active');
      pressed = false;

      if(newSize != startWidth){
        instance.forceFullRender = true;
        instance.view.render(); //updates all

        saveManualColumnWidths.call(instance);

        instance.PluginHooks.run('afterColumnResize', currentCol, newSize);
      }

      refreshResizerPosition.call(instance, currentTH);
    }
  });

  var saveManualColumnWidths = function () {
    var instance = this;

    instance.PluginHooks.run('persistentStateSave', 'manualColumnWidths', instance.manualColumnWidths);
  };

  var loadManualColumnWidths = function () {
    var instance = this;
    var storedState = {};
    instance.PluginHooks.run('persistentStateLoad', 'manualColumnWidths', storedState);

    return storedState.value;
  };

  function refreshResizerPosition(TH) {
    instance = this;
    currentTH = TH;

    var col = this.view.wt.wtTable.getCoords(TH)[1]; //getCoords returns array [row, col]
    if (col >= 0) { //if not row header
      currentCol = col;
      var rootOffset = this.view.wt.wtDom.offset(this.rootElement[0]).left;
      var thOffset = this.view.wt.wtDom.offset(TH).left;
      startOffset = (thOffset - rootOffset) - 6;
      resizer.style.left = startOffset + parseInt(this.view.wt.wtDom.outerWidth(TH), 10) + 'px';

      this.rootElement[0].appendChild(resizer);
    }
  }

  function refreshLinePosition() {
    var instance = this;
    startWidth = parseInt(this.view.wt.wtDom.outerWidth(currentTH), 10);
    instance.view.wt.wtDom.addClass(resizer, 'active');
    lineStyle.height = instance.view.wt.wtDom.outerHeight(instance.$table[0]) + 'px';
    pressed = instance;
  }

  var bindManualColumnWidthEvents = function () {
    var instance = this;
    var dblclick = 0;
    var autoresizeTimeout = null;

    this.rootElement.on('mouseenter.handsontable', 'th', function (e) {
      if (!pressed) {
        refreshResizerPosition.call(instance, e.currentTarget);
      }
    });

    this.rootElement.on('mousedown.handsontable', '.manualColumnResizer', function () {
      if (autoresizeTimeout == null) {
        autoresizeTimeout = setTimeout(function () {
          if (dblclick >= 2) {
            newSize = instance.determineColumnWidth.call(instance, currentCol);
            setManualSize(currentCol, newSize);
            instance.forceFullRender = true;
            instance.view.render(); //updates all
            instance.PluginHooks.run('afterColumnResize', currentCol, newSize);
          }
          dblclick = 0;
          autoresizeTimeout = null;
        }, 500);
      }
      dblclick++;
    });

    this.rootElement.on('mousedown.handsontable', '.manualColumnResizer', function (e) {
      startX = e.pageX;
      refreshLinePosition.call(instance);
      newSize = startWidth;
    });
  };

  this.beforeInit = function () {
    this.manualColumnWidths = [];
  };

  this.init = function (source) {
    var instance = this;
    var manualColumnWidthEnabled = !!(this.getSettings().manualColumnResize);

    if (manualColumnWidthEnabled) {
      var initialColumnWidths = this.getSettings().manualColumnResize;

      var loadedManualColumnWidths = loadManualColumnWidths.call(instance);

      if (typeof loadedManualColumnWidths != 'undefined') {
        this.manualColumnWidths = loadedManualColumnWidths;
      } else if (initialColumnWidths instanceof Array) {
        this.manualColumnWidths = initialColumnWidths;
      } else {
        this.manualColumnWidths = [];
      }

      if (source == 'afterInit') {
        bindManualColumnWidthEvents.call(this);
        instance.forceFullRender = true;
        instance.render();
      }
    }
  };


  var setManualSize = function (col, width) {
    width = Math.max(width, 20);

    /**
     *  We need to run col through modifyCol hook, in case the order of displayed columns is different than the order
     *  in data source. For instance, this order can be modified by manualColumnMove plugin.
     */
    col = instance.PluginHooks.execute('modifyCol', col);

    instance.manualColumnWidths[col] = width;
    return width;
  };

  this.getColWidth = function (col, response) {
    if (this.getSettings().manualColumnResize && this.manualColumnWidths[col]) {
      response.width = this.manualColumnWidths[col];
    }
  };
}
var htManualColumnResize = new HandsontableManualColumnResize();

Handsontable.PluginHooks.add('beforeInit', htManualColumnResize.beforeInit);
Handsontable.PluginHooks.add('afterInit', function () {
  htManualColumnResize.init.call(this, 'afterInit')
});
Handsontable.PluginHooks.add('afterUpdateSettings', function () {
  htManualColumnResize.init.call(this, 'afterUpdateSettings')
});
Handsontable.PluginHooks.add('afterGetColWidth', htManualColumnResize.getColWidth);

(function HandsontableObserveChanges() {

  Handsontable.PluginHooks.add('afterLoadData', init);
  Handsontable.PluginHooks.add('afterUpdateSettings', init);

  function init() {
    var instance = this;
    var pluginEnabled = instance.getSettings().observeChanges;

    if (pluginEnabled) {
      if(instance.observer) {
        destroy.call(instance); //destroy observer for old data object
      }
      createObserver.call(instance);
      bindEvents.call(instance);

    } else if (!pluginEnabled){
      destroy.call(instance);
    }
  }

  function createObserver(){
    var instance = this;

    instance.observeChangesActive = true;

    instance.pauseObservingChanges = function(){
      instance.observeChangesActive = false;
    };

    instance.resumeObservingChanges = function(){
      instance.observeChangesActive = true;
    };

    instance.observedData = instance.getData();
    instance.observer = jsonpatch.observe(instance.observedData, function (patches) {
      if(instance.observeChangesActive){
        runHookForOperation.call(instance, patches);
        instance.render();
      }

      instance.runHooks('afterChangesObserved');
    });
  }

  function runHookForOperation(rawPatches){
    var instance = this;
    var patches = cleanPatches(rawPatches);

    for(var i = 0, len = patches.length; i < len; i++){
      var patch = patches[i];
      var parsedPath = parsePath(patch.path);


      switch(patch.op){
        case 'add':
          if(isNaN(parsedPath.col)){
            instance.runHooks('afterCreateRow', parsedPath.row);
          } else {
            instance.runHooks('afterCreateCol', parsedPath.col);
          }
          break;

        case 'remove':
          if(isNaN(parsedPath.col)){
            instance.runHooks('afterRemoveRow', parsedPath.row, 1);
          } else {
            instance.runHooks('afterRemoveCol', parsedPath.col, 1);
          }
          break;

        case 'replace':
          instance.runHooks('afterChange', [parsedPath.row, parsedPath.col, null, patch.value], 'external');
          break;
      }
    }

    function cleanPatches(rawPatches){
      var patches;

      patches = removeLengthRelatedPatches(rawPatches);
      patches = removeMultipleAddOrRemoveColPatches(patches);

      return patches;
    }

    /**
     * Removing or adding column will produce one patch for each table row.
     * This function leaves only one patch for each column add/remove operation
     */
    function removeMultipleAddOrRemoveColPatches(rawPatches){
      var newOrRemovedColumns = [];

      return rawPatches.filter(function(patch){
        var parsedPath = parsePath(patch.path);

        if(['add', 'remove'].indexOf(patch.op) != -1 && !isNaN(parsedPath.col)){
          if(newOrRemovedColumns.indexOf(parsedPath.col) != -1){
            return false;
          } else {
            newOrRemovedColumns.push(parsedPath.col);
          }
        }

        return true;
      });

    }

    /**
     * If observeChanges uses native Object.observe method, then it produces patches for length property.
     * This function removes them.
     */
    function removeLengthRelatedPatches(rawPatches){
      return rawPatches.filter(function(patch){
        return !/[/]length/ig.test(patch.path);
      })
    }

    function parsePath(path){
      var match = path.match(/^\/(\d+)\/?(.*)?$/);
      return {
        row: parseInt(match[1], 10),
        col: /^\d*$/.test(match[2]) ? parseInt(match[2], 10) : match[2]
      }
    }
  }

  function destroy(){
    var instance = this;

    if (instance.observer){
      destroyObserver.call(instance);
      unbindEvents.call(instance);
    }
  }

  function destroyObserver(){
    var instance = this;

    jsonpatch.unobserve(instance.observedData, instance.observer);
    delete instance.observeChangesActive;
    delete instance.pauseObservingChanges;
    delete instance.resumeObservingChanges;
  }

  function bindEvents(){
    var instance = this;
    instance.addHook('afterDestroy', destroy);

    instance.addHook('afterCreateRow', afterTableAlter);
    instance.addHook('afterRemoveRow', afterTableAlter);

    instance.addHook('afterCreateCol', afterTableAlter);
    instance.addHook('afterRemoveCol', afterTableAlter);

    instance.addHook('afterChange', function(changes, source){
      if(source != 'loadData'){
        afterTableAlter.call(this);
      }
    });
  }

  function unbindEvents(){
    var instance = this;
    instance.removeHook('afterDestroy', destroy);

    instance.removeHook('afterCreateRow', afterTableAlter);
    instance.removeHook('afterRemoveRow', afterTableAlter);

    instance.removeHook('afterCreateCol', afterTableAlter);
    instance.removeHook('afterRemoveCol', afterTableAlter);

    instance.removeHook('afterChange', afterTableAlter);
  }

  function afterTableAlter(){
    var instance = this;

    instance.pauseObservingChanges();

    instance.addHookOnce('afterChangesObserved', function(){
      instance.resumeObservingChanges();
    });

  }
})();


/*
 *
 * Plugin enables saving table state
 *
 * */


function Storage(prefix) {

  var savedKeys;

  var saveSavedKeys = function () {
    window.localStorage[prefix + '__' + 'persistentStateKeys'] = JSON.stringify(savedKeys);
  };

  var loadSavedKeys = function () {
    var keysJSON = window.localStorage[prefix + '__' + 'persistentStateKeys'];
    var keys = typeof keysJSON == 'string' ? JSON.parse(keysJSON) : void 0;
    savedKeys = keys ? keys : [];
  };

  var clearSavedKeys = function () {
    savedKeys = [];
    saveSavedKeys();
  };

  loadSavedKeys();

  this.saveValue = function (key, value) {
    window.localStorage[prefix + '_' + key] = JSON.stringify(value);
    if (savedKeys.indexOf(key) == -1) {
      savedKeys.push(key);
      saveSavedKeys();
    }

  };

  this.loadValue = function (key, defaultValue) {

    key = typeof key != 'undefined' ? key : defaultValue;

    var value = window.localStorage[prefix + '_' + key];

    return typeof value == "undefined" ? void 0 : JSON.parse(value);

  };

  this.reset = function (key) {
    window.localStorage.removeItem(prefix + '_' + key);
  };

  this.resetAll = function () {
    for (var index = 0; index < savedKeys.length; index++) {
      window.localStorage.removeItem(prefix + '_' + savedKeys[index]);
    }

    clearSavedKeys();
  };

}


(function (StorageClass) {
  function HandsontablePersistentState() {
    var plugin = this;


    this.init = function () {
      var instance = this,
        pluginSettings = instance.getSettings()['persistentState'];

      plugin.enabled = !!(pluginSettings);

      if (!plugin.enabled) {
        removeHooks.call(instance);
        return;
      }

      if (!instance.storage) {
        instance.storage = new StorageClass(instance.rootElement[0].id);
      }

      instance.resetState = plugin.resetValue;

      addHooks.call(instance);

    };

    this.saveValue = function (key, value) {
      var instance = this;

      instance.storage.saveValue(key, value);
    };

    this.loadValue = function (key, saveTo) {
      var instance = this;

      saveTo.value = instance.storage.loadValue(key);
    };

    this.resetValue = function (key) {
      var instance = this;

      if (typeof  key != 'undefined') {
        instance.storage.reset(key);
      } else {
        instance.storage.resetAll();
      }

    };

    var hooks = {
      'persistentStateSave': plugin.saveValue,
      'persistentStateLoad': plugin.loadValue,
      'persistentStateReset': plugin.resetValue
    };

    function addHooks() {
      var instance = this;

      for (var hookName in hooks) {
        if (hooks.hasOwnProperty(hookName) && !hookExists.call(instance, hookName)) {
          instance.PluginHooks.add(hookName, hooks[hookName]);
        }
      }
    }

    function removeHooks() {
      var instance = this;

      for (var hookName in hooks) {
        if (hooks.hasOwnProperty(hookName) && hookExists.call(instance, hookName)) {
          instance.PluginHooks.remove(hookName, hooks[hookName]);
        }
      }
    }

    function hookExists(hookName) {
      var instance = this;
      return instance.PluginHooks.hooks.hasOwnProperty(hookName);
    }
  }

  var htPersistentState = new HandsontablePersistentState();
  Handsontable.PluginHooks.add('beforeInit', htPersistentState.init);
  Handsontable.PluginHooks.add('afterUpdateSettings', htPersistentState.init);
})(Storage);

/**
 * Handsontable UndoRedo class
 */
(function(Handsontable){
  Handsontable.UndoRedo = function (instance) {
    var plugin = this;
    this.instance = instance;
    this.doneActions = [];
    this.undoneActions = [];
    this.ignoreNewActions = false;
    instance.addHook("afterChange", function (changes, origin) {
      if(changes){
        var action = new Handsontable.UndoRedo.ChangeAction(changes);
        plugin.done(action);
      }
    });

    instance.addHook("afterCreateRow", function (index, amount, createdAutomatically) {

      if (createdAutomatically) {
        return;
      }

      var action = new Handsontable.UndoRedo.CreateRowAction(index, amount);
      plugin.done(action);
    });

    instance.addHook("beforeRemoveRow", function (index, amount) {
      var originalData = plugin.instance.getData();
      index = ( originalData.length + index ) % originalData.length;
      var removedData = originalData.slice(index, index + amount);
      var action = new Handsontable.UndoRedo.RemoveRowAction(index, removedData);
      plugin.done(action);
    });

    instance.addHook("afterCreateCol", function (index, amount, createdAutomatically) {

      if (createdAutomatically) {
        return;
      }

      var action = new Handsontable.UndoRedo.CreateColumnAction(index, amount);
      plugin.done(action);
    });

    instance.addHook("beforeRemoveCol", function (index, amount) {
      var originalData = plugin.instance.getData();
      index = ( plugin.instance.countCols() + index ) % plugin.instance.countCols();
      var removedData = [];

      for (var i = 0, len = originalData.length; i < len; i++) {
        removedData[i] = originalData[i].slice(index, index + amount);
      }

      var headers;
      if(Handsontable.helper.isArray(instance.getSettings().colHeaders)){
        headers = instance.getSettings().colHeaders.slice(index, index + removedData.length);
      }

      var action = new Handsontable.UndoRedo.RemoveColumnAction(index, removedData, headers);
      plugin.done(action);
    });
  };

  Handsontable.UndoRedo.prototype.done = function (action) {
    if (!this.ignoreNewActions) {
      this.doneActions.push(action);
      this.undoneActions.length = 0;
    }
  };

  /**
   * Undo operation from current revision
   */
  Handsontable.UndoRedo.prototype.undo = function () {
    if (this.isUndoAvailable()) {
      var action = this.doneActions.pop();

      this.ignoreNewActions = true;
      action.undo(this.instance);
      this.ignoreNewActions = false;

      this.undoneActions.push(action);
    }
  };

  /**
   * Redo operation from current revision
   */
  Handsontable.UndoRedo.prototype.redo = function () {
    if (this.isRedoAvailable()) {
      var action = this.undoneActions.pop();

      this.ignoreNewActions = true;
      action.redo(this.instance);
      this.ignoreNewActions = true;

      this.doneActions.push(action);
    }
  };

  /**
   * Returns true if undo point is available
   * @return {Boolean}
   */
  Handsontable.UndoRedo.prototype.isUndoAvailable = function () {
    return this.doneActions.length > 0;
  };

  /**
   * Returns true if redo point is available
   * @return {Boolean}
   */
  Handsontable.UndoRedo.prototype.isRedoAvailable = function () {
    return this.undoneActions.length > 0;
  };

  /**
   * Clears undo history
   */
  Handsontable.UndoRedo.prototype.clear = function () {
    this.doneActions.length = 0;
    this.undoneActions.length = 0;
  };

  Handsontable.UndoRedo.Action = function () {
  };
  Handsontable.UndoRedo.Action.prototype.undo = function () {
  };
  Handsontable.UndoRedo.Action.prototype.redo = function () {
  };

  Handsontable.UndoRedo.ChangeAction = function (changes) {
    this.changes = changes;
  };
  Handsontable.helper.inherit(Handsontable.UndoRedo.ChangeAction, Handsontable.UndoRedo.Action);
  Handsontable.UndoRedo.ChangeAction.prototype.undo = function (instance) {
    var data = $.extend(true, [], this.changes);
    for (var i = 0, len = data.length; i < len; i++) {
      data[i].splice(3, 1);
    }
    instance.setDataAtRowProp(data, null, null, 'undo');

  };
  Handsontable.UndoRedo.ChangeAction.prototype.redo = function (instance) {
    var data = $.extend(true, [], this.changes);
    for (var i = 0, len = data.length; i < len; i++) {
      data[i].splice(2, 1);
    }
    instance.setDataAtRowProp(data, null, null, 'redo');

  };

  Handsontable.UndoRedo.CreateRowAction = function (index, amount) {
    this.index = index;
    this.amount = amount;
  };
  Handsontable.helper.inherit(Handsontable.UndoRedo.CreateRowAction, Handsontable.UndoRedo.Action);
  Handsontable.UndoRedo.CreateRowAction.prototype.undo = function (instance) {
    instance.alter('remove_row', this.index, this.amount);
  };
  Handsontable.UndoRedo.CreateRowAction.prototype.redo = function (instance) {
    instance.alter('insert_row', this.index + 1, this.amount);
  };

  Handsontable.UndoRedo.RemoveRowAction = function (index, data) {
    this.index = index;
    this.data = data;
  };
  Handsontable.helper.inherit(Handsontable.UndoRedo.RemoveRowAction, Handsontable.UndoRedo.Action);
  Handsontable.UndoRedo.RemoveRowAction.prototype.undo = function (instance) {
    var spliceArgs = [this.index, 0];
    Array.prototype.push.apply(spliceArgs, this.data);

    Array.prototype.splice.apply(instance.getData(), spliceArgs);

    instance.render();
  };
  Handsontable.UndoRedo.RemoveRowAction.prototype.redo = function (instance) {
    instance.alter('remove_row', this.index, this.data.length);
  };

  Handsontable.UndoRedo.CreateColumnAction = function (index, amount) {
    this.index = index;
    this.amount = amount;
  };
  Handsontable.helper.inherit(Handsontable.UndoRedo.CreateColumnAction, Handsontable.UndoRedo.Action);
  Handsontable.UndoRedo.CreateColumnAction.prototype.undo = function (instance) {
    instance.alter('remove_col', this.index, this.amount);
  };
  Handsontable.UndoRedo.CreateColumnAction.prototype.redo = function (instance) {
    instance.alter('insert_col', this.index + 1, this.amount);
  };

  Handsontable.UndoRedo.RemoveColumnAction = function (index, data, headers) {
    this.index = index;
    this.data = data;
    this.amount = this.data[0].length;
    this.headers = headers;
  };
  Handsontable.helper.inherit(Handsontable.UndoRedo.RemoveColumnAction, Handsontable.UndoRedo.Action);
  Handsontable.UndoRedo.RemoveColumnAction.prototype.undo = function (instance) {
    var row, spliceArgs;
    for (var i = 0, len = instance.getData().length; i < len; i++) {
      row = instance.getDataAtRow(i);

      spliceArgs = [this.index, 0];
      Array.prototype.push.apply(spliceArgs, this.data[i]);

      Array.prototype.splice.apply(row, spliceArgs);

    }

    if(typeof this.headers != 'undefined'){
      spliceArgs = [this.index, 0];
      Array.prototype.push.apply(spliceArgs, this.headers);
      Array.prototype.splice.apply(instance.getSettings().colHeaders, spliceArgs);
    }

    instance.render();
  };
  Handsontable.UndoRedo.RemoveColumnAction.prototype.redo = function (instance) {
    instance.alter('remove_col', this.index, this.amount);
  };
})(Handsontable);

(function(Handsontable){

  function init(){
    var instance = this;
    var pluginEnabled = typeof instance.getSettings().undo == 'undefined' || instance.getSettings().undo;

    if(pluginEnabled){
      if(!instance.undoRedo){
        instance.undoRedo = new Handsontable.UndoRedo(instance);

        exposeUndoRedoMethods(instance);

        instance.addHook('beforeKeyDown', onBeforeKeyDown);
        instance.addHook('afterChange', onAfterChange);
      }
    } else {
      if(instance.undoRedo){
        delete instance.undoRedo;

        removeExposedUndoRedoMethods(instance);

        instance.removeHook('beforeKeyDown', onBeforeKeyDown);
        instance.removeHook('afterChange', onAfterChange);
      }
    }
  }

  function onBeforeKeyDown(event){
    var instance = this;

    var ctrlDown = (event.ctrlKey || event.metaKey) && !event.altKey;

    if(ctrlDown){
      if (event.keyCode === 89 || (event.shiftKey && event.keyCode === 90)) { //CTRL + Y or CTRL + SHIFT + Z
        instance.undoRedo.redo();
        event.stopImmediatePropagation();
      }
      else if (event.keyCode === 90) { //CTRL + Z
        instance.undoRedo.undo();
        event.stopImmediatePropagation();
      }
    }
  }

  function onAfterChange(changes, source){
    var instance = this;
    if (source == 'loadData'){
      return instance.undoRedo.clear();
    }
  }

  function exposeUndoRedoMethods(instance){
    instance.undo = function(){
      return instance.undoRedo.undo();
    };

    instance.redo = function(){
      return instance.undoRedo.redo();
    };

    instance.isUndoAvailable = function(){
      return instance.undoRedo.isUndoAvailable();
    };

    instance.isRedoAvailable = function(){
      return instance.undoRedo.isRedoAvailable();
    };

    instance.clearUndo = function(){
      return instance.undoRedo.clear();
    };
  }

  function removeExposedUndoRedoMethods(instance){
    delete instance.undo;
    delete instance.redo;
    delete instance.isUndoAvailable;
    delete instance.isRedoAvailable;
    delete instance.clearUndo;
  }

  Handsontable.PluginHooks.add('afterInit', init);
  Handsontable.PluginHooks.add('afterUpdateSettings', init);

})(Handsontable);
/**
 * Plugin used to scroll Handsontable by selecting a cell and dragging outside of visible viewport
 * @constructor
 */
function DragToScroll() {
  this.boundaries = null;
  this.callback = null;
}

/**
 * @param boundaries {Object} compatible with getBoundingClientRect
 */
DragToScroll.prototype.setBoundaries = function (boundaries) {
  this.boundaries = boundaries;
};

/**
 * @param callback {Function}
 */
DragToScroll.prototype.setCallback = function (callback) {
  this.callback = callback;
};

/**
 * Check if mouse position (x, y) is outside of the viewport
 * @param x
 * @param y
 */
DragToScroll.prototype.check = function (x, y) {
  var diffX = 0;
  var diffY = 0;

  if (y < this.boundaries.top) {
    //y is less than top
    diffY = y - this.boundaries.top;
  }
  else if (y > this.boundaries.bottom) {
    //y is more than bottom
    diffY = y - this.boundaries.bottom;
  }

  if (x < this.boundaries.left) {
    //x is less than left
    diffX = x - this.boundaries.left;
  }
  else if (x > this.boundaries.right) {
    //x is more than right
    diffX = x - this.boundaries.right;
  }

  this.callback(diffX, diffY);
};

var listening = false;
var dragToScroll;
var instance;

if (typeof Handsontable !== 'undefined') {
  var setupListening = function (instance) {
    var scrollHandler = instance.view.wt.wtScrollbars.vertical.scrollHandler; //native scroll
    dragToScroll = new DragToScroll();
    if (scrollHandler === window) {
      //not much we can do currently
      return;
    }
    else if (scrollHandler) {
      dragToScroll.setBoundaries(scrollHandler.getBoundingClientRect());
    }
    else {
      dragToScroll.setBoundaries(instance.$table[0].getBoundingClientRect());
    }

    dragToScroll.setCallback(function (scrollX, scrollY) {
      if (scrollX < 0) {
        if (scrollHandler) {
          scrollHandler.scrollLeft -= 50;
        }
        else {
          instance.view.wt.scrollHorizontal(-1).draw();
        }
      }
      else if (scrollX > 0) {
        if (scrollHandler) {
          scrollHandler.scrollLeft += 50;
        }
        else {
          instance.view.wt.scrollHorizontal(1).draw();
        }
      }

      if (scrollY < 0) {
        if (scrollHandler) {
          scrollHandler.scrollTop -= 20;
        }
        else {
          instance.view.wt.scrollVertical(-1).draw();
        }
      }
      else if (scrollY > 0) {
        if (scrollHandler) {
          scrollHandler.scrollTop += 20;
        }
        else {
          instance.view.wt.scrollVertical(1).draw();
        }
      }
    });

    listening = true;
  };

  Handsontable.PluginHooks.add('afterInit', function () {
    $(document).on('mouseup.' + this.guid, function () {
      listening = false;
    });

    $(document).on('mousemove.' + this.guid, function (event) {
      if (listening) {
        dragToScroll.check(event.clientX, event.clientY);
      }
    });
  });

  Handsontable.PluginHooks.add('destroy', function () {
    $(document).off('.' + this.guid);
  });

  Handsontable.PluginHooks.add('afterOnCellMouseDown', function () {
    setupListening(this);
  });

  Handsontable.PluginHooks.add('afterOnCellCornerMouseDown', function () {
    setupListening(this);
  });
}

(function (Handsontable, CopyPaste, SheetClip) {

  function CopyPastePlugin(instance) {
    this.copyPasteInstance = CopyPaste.getInstance();

    this.copyPasteInstance.onCut(onCut);
    this.copyPasteInstance.onPaste(onPaste);
    var plugin = this;

    instance.addHook('beforeKeyDown', onBeforeKeyDown);

    function onCut() {
      if (!instance.isListening()) {
        return;
      }

      instance.selection.empty();
    }

    function onPaste(str) {
      if (!instance.isListening() || !instance.selection.isSelected()) {
        return;
      }

      var input = str.replace(/^[\r\n]*/g, '').replace(/[\r\n]*$/g, '') //remove newline from the start and the end of the input
        , inputArray = SheetClip.parse(input)
        , selected = instance.getSelected()
        , coords = instance.getCornerCoords([{row: selected[0], col: selected[1]}, {row: selected[2], col: selected[3]}])
        , areaStart = coords.TL
        , areaEnd = {
          row: Math.max(coords.BR.row, inputArray.length - 1 + coords.TL.row),
          col: Math.max(coords.BR.col, inputArray[0].length - 1 + coords.TL.col)
        };

      instance.PluginHooks.once('afterChange', function (changes, source) {
        if (changes && changes.length) {
          this.selectCell(areaStart.row, areaStart.col, areaEnd.row, areaEnd.col);
        }
      });

      instance.populateFromArray(areaStart.row, areaStart.col, inputArray, areaEnd.row, areaEnd.col, 'paste', instance.getSettings().pasteMode);
    };

    function onBeforeKeyDown (event) {
      if (Handsontable.helper.isCtrlKey(event.keyCode) && instance.getSelected()) {
        //when CTRL is pressed, prepare selectable text in textarea
        //http://stackoverflow.com/questions/3902635/how-does-one-capture-a-macs-command-key-via-javascript
        plugin.setCopyableText();
        event.stopImmediatePropagation();
        return;
      }

      var ctrlDown = (event.ctrlKey || event.metaKey) && !event.altKey; //catch CTRL but not right ALT (which in some systems triggers ALT+CTRL)

      if (event.keyCode == Handsontable.helper.keyCode.A && ctrlDown) {
        setTimeout(Handsontable.helper.proxy(plugin.setCopyableText, plugin));
      }

    }

    this.destroy = function () {
      this.copyPasteInstance.removeCallback(onCut);
      this.copyPasteInstance.removeCallback(onPaste);
      this.copyPasteInstance.destroy();
      instance.removeHook('beforeKeyDown', onBeforeKeyDown);
    };

    instance.addHook('afterDestroy', Handsontable.helper.proxy(this.destroy, this));

    this.triggerPaste = Handsontable.helper.proxy(this.copyPasteInstance.triggerPaste, this.copyPasteInstance);
    this.triggerCut = Handsontable.helper.proxy(this.copyPasteInstance.triggerCut, this.copyPasteInstance);

    /**
     * Prepares copyable text in the invisible textarea
     */
    this.setCopyableText = function () {

      var selection = instance.getSelected();
      var settings = instance.getSettings();
      var copyRowsLimit = settings.copyRowsLimit;
      var copyColsLimit = settings.copyColsLimit;

      var startRow = Math.min(selection[0], selection[2]);
      var startCol = Math.min(selection[1], selection[3]);
      var endRow = Math.max(selection[0], selection[2]);
      var endCol = Math.max(selection[1], selection[3]);
      var finalEndRow = Math.min(endRow, startRow + copyRowsLimit - 1);
      var finalEndCol = Math.min(endCol, startCol + copyColsLimit - 1);

      instance.copyPaste.copyPasteInstance.copyable(instance.getCopyableData(startRow, startCol, finalEndRow, finalEndCol));

      if (endRow !== finalEndRow || endCol !== finalEndCol) {
        instance.PluginHooks.run("afterCopyLimit", endRow - startRow + 1, endCol - startCol + 1, copyRowsLimit, copyColsLimit);
      }
    };

  }



  function init() {
    var instance  = this;
    var pluginEnabled = instance.getSettings().copyPaste !== false;

    if(pluginEnabled && !instance.copyPaste){

      instance.copyPaste = new CopyPastePlugin(instance);

    } else if (!pluginEnabled && instance.copyPaste) {

      instance.copyPaste.destroy();
      delete instance.copyPaste;

    }

  }

  Handsontable.PluginHooks.add('afterInit', init);
  Handsontable.PluginHooks.add('afterUpdateSettings', init);

})(Handsontable, CopyPaste, SheetClip);
/**
 * Creates an overlay over the original Walkontable instance. The overlay renders the clone of the original Walkontable
 * and (optionally) implements behavior needed for native horizontal and vertical scrolling
 */
function WalkontableOverlay() {
  this.maxOuts = 10; //max outs in one direction (before and after table)
}

/*
 Possible optimizations:
 [x] don't rerender if scroll delta is smaller than the fragment outside of the viewport
 [ ] move .style.top change before .draw()
 [ ] put .draw() in requestAnimationFrame
 [ ] don't rerender rows that remain visible after the scroll
 */

WalkontableOverlay.prototype.init = function () {
  this.TABLE = this.instance.wtTable.TABLE;
  this.fixed = this.instance.wtTable.hider;
  this.fixedContainer = this.instance.wtTable.holder;
  this.fixed.style.position = 'absolute';
  this.fixed.style.left = '0';
  this.scrollHandler = this.getScrollableElement(this.TABLE);
  this.$scrollHandler = $(this.scrollHandler); //in future remove jQuery from here
};

WalkontableOverlay.prototype.makeClone = function (direction) {
  var clone = document.createElement('DIV');
  clone.className = 'ht_clone_' + direction + ' handsontable';
  clone.style.position = 'fixed';
  clone.style.overflow = 'hidden';

  var table2 = document.createElement('TABLE');
  table2.className = this.instance.wtTable.TABLE.className;
  clone.appendChild(table2);

  this.instance.wtTable.holder.parentNode.appendChild(clone);

  return new Walkontable({
    cloneSource: this.instance,
    cloneOverlay: this,
    table: table2
  });
};

WalkontableOverlay.prototype.getScrollableElement = function (TABLE) {
  var el = TABLE.parentNode;
  while (el && el.style) {
    if (el.style.overflow !== 'visible' && el.style.overflow !== '') {
      return el;
    }
    if (this instanceof WalkontableHorizontalScrollbarNative && el.style.overflowX !== 'visible' && el.style.overflowX !== '') {
      return el;
    }
    el = el.parentNode;
  }
  return window;
};

WalkontableOverlay.prototype.prepare = function () {
};

WalkontableOverlay.prototype.onScroll = function (forcePosition) {

  this.windowScrollPosition = this.getScrollPosition();
  this.readSettings(); //read window scroll position

  if (forcePosition) {
    this.windowScrollPosition = forcePosition;
  }

  this.resetFixedPosition(); //may be redundant
};

WalkontableOverlay.prototype.availableSize = function () {
  var availableSize;

  if (this.windowScrollPosition > this.tableParentOffset /*&& last > -1*/) { //last -1 means that viewport is scrolled behind the table
    if (this.instance.wtTable.getLastVisibleRow() === this.total - 1) {
      availableSize = this.instance.wtDom.outerHeight(this.TABLE);
    }
    else {
      availableSize = this.windowSize;
    }
  }
  else {
    availableSize = this.windowSize - (this.tableParentOffset);
  }

  return availableSize;
};

WalkontableOverlay.prototype.refresh = function (selectionsOnly) {
  var last = this.getLastCell();
  this.measureBefore = this.sumCellSizes(0, this.offset);
  if (last === -1) { //last -1 means that viewport is scrolled behind the table
    this.measureAfter = 0;
  }
  else {
    this.measureAfter = this.sumCellSizes(last, this.total - last);
  }
  this.applyToDOM();
  this.clone && this.clone.draw(selectionsOnly);
};

WalkontableOverlay.prototype.destroy = function () {
  this.$scrollHandler.off('.' + this.instance.guid);
  $(window).off('.' + this.instance.guid);
  $(document).off('.' + this.instance.guid);
};
function WalkontableBorder(instance, settings) {
  var style;

  //reference to instance
  this.instance = instance;
  this.settings = settings;
  this.wtDom = this.instance.wtDom;

  this.main = document.createElement("div");
  style = this.main.style;
  style.position = 'absolute';
  style.top = 0;
  style.left = 0;
//  style.visibility = 'hidden';

  for (var i = 0; i < 5; i++) {
    var DIV = document.createElement('DIV');
    DIV.className = 'wtBorder ' + (settings.className || '');
    style = DIV.style;
    style.backgroundColor = settings.border.color;
    style.height = settings.border.width + 'px';
    style.width = settings.border.width + 'px';
    this.main.appendChild(DIV);
  }

  this.top = this.main.childNodes[0];
  this.left = this.main.childNodes[1];
  this.bottom = this.main.childNodes[2];
  this.right = this.main.childNodes[3];


  /*$(this.top).on(sss, function(event) {
   event.preventDefault();
   event.stopImmediatePropagation();
   $(this).hide();
   });
   $(this.left).on(sss, function(event) {
   event.preventDefault();
   event.stopImmediatePropagation();
   $(this).hide();
   });
   $(this.bottom).on(sss, function(event) {
   event.preventDefault();
   event.stopImmediatePropagation();
   $(this).hide();
   });
   $(this.right).on(sss, function(event) {
   event.preventDefault();
   event.stopImmediatePropagation();
   $(this).hide();
   });*/

  this.topStyle = this.top.style;
  this.leftStyle = this.left.style;
  this.bottomStyle = this.bottom.style;
  this.rightStyle = this.right.style;

  this.corner = this.main.childNodes[4];
  this.corner.className += ' corner';
  this.cornerStyle = this.corner.style;
  this.cornerStyle.width = '5px';
  this.cornerStyle.height = '5px';
  this.cornerStyle.border = '2px solid #FFF';

  this.disappear();
  if (!instance.wtTable.bordersHolder) {
    instance.wtTable.bordersHolder = document.createElement('div');
    instance.wtTable.bordersHolder.className = 'htBorders';
    instance.wtTable.hider.appendChild(instance.wtTable.bordersHolder);

  }
  instance.wtTable.bordersHolder.appendChild(this.main);

  var down = false;
  var $body = $(document.body);

  $body.on('mousedown.walkontable.' + instance.guid, function () {
    down = true;
  });

  $body.on('mouseup.walkontable.' + instance.guid, function () {
    down = false
  });

  $(this.main.childNodes).on('mouseenter', function (event) {
    if (!down || !instance.getSetting('hideBorderOnMouseDownOver')) {
      return;
    }
    event.preventDefault();
    event.stopImmediatePropagation();

    var bounds = this.getBoundingClientRect();

    var $this = $(this);
    $this.hide();

    var isOutside = function (event) {
      if (event.clientY < Math.floor(bounds.top)) {
        return true;
      }
      if (event.clientY > Math.ceil(bounds.top + bounds.height)) {
        return true;
      }
      if (event.clientX < Math.floor(bounds.left)) {
        return true;
      }
      if (event.clientX > Math.ceil(bounds.left + bounds.width)) {
        return true;
      }
    };

    $body.on('mousemove.border.' + instance.guid, function (event) {
      if (isOutside(event)) {
        $body.off('mousemove.border.' + instance.guid);
        $this.show();
      }
    });
  });
}

/**
 * Show border around one or many cells
 * @param {Array} corners
 */
WalkontableBorder.prototype.appear = function (corners) {
  var isMultiple, fromTD, toTD, fromOffset, toOffset, containerOffset, top, minTop, left, minLeft, height, width;
  if (this.disabled) {
    return;
  }

  var instance = this.instance
    , fromRow
    , fromColumn
    , toRow
    , toColumn
    , hideTop = false
    , hideLeft = false
    , hideBottom = false
    , hideRight = false
    , i
    , ilen
    , s;

  if (!instance.wtTable.isRowInViewport(corners[0])) {
    hideTop = true;
  }

  if (!instance.wtTable.isRowInViewport(corners[2])) {
    hideBottom = true;
  }

  ilen = instance.wtTable.rowStrategy.countVisible();

  for (i = 0; i < ilen; i++) {
    s = instance.wtTable.rowFilter.visibleToSource(i);
    if (s >= corners[0] && s <= corners[2]) {
      fromRow = s;
      break;
    }
  }

  for (i = ilen - 1; i >= 0; i--) {
    s = instance.wtTable.rowFilter.visibleToSource(i);
    if (s >= corners[0] && s <= corners[2]) {
      toRow = s;
      break;
    }
  }

  if (hideTop && hideBottom) {
    hideLeft = true;
    hideRight = true;
  }
  else {
    if (!instance.wtTable.isColumnInViewport(corners[1])) {
      hideLeft = true;
    }

    if (!instance.wtTable.isColumnInViewport(corners[3])) {
      hideRight = true;
    }

    ilen = instance.wtTable.columnStrategy.countVisible();

    for (i = 0; i < ilen; i++) {
      s = instance.wtTable.columnFilter.visibleToSource(i);
      if (s >= corners[1] && s <= corners[3]) {
        fromColumn = s;
        break;
      }
    }

    for (i = ilen - 1; i >= 0; i--) {
      s = instance.wtTable.columnFilter.visibleToSource(i);
      if (s >= corners[1] && s <= corners[3]) {
        toColumn = s;
        break;
      }
    }
  }

  if (fromRow !== void 0 && fromColumn !== void 0) {
    isMultiple = (fromRow !== toRow || fromColumn !== toColumn);
    fromTD = instance.wtTable.getCell([fromRow, fromColumn]);
    toTD = isMultiple ? instance.wtTable.getCell([toRow, toColumn]) : fromTD;
    fromOffset = this.wtDom.offset(fromTD);
    toOffset = isMultiple ? this.wtDom.offset(toTD) : fromOffset;
    containerOffset = this.wtDom.offset(instance.wtTable.TABLE);

    minTop = fromOffset.top;
    height = toOffset.top + this.wtDom.outerHeight(toTD) - minTop;
    minLeft = fromOffset.left;
    width = toOffset.left + this.wtDom.outerWidth(toTD) - minLeft;

    top = minTop - containerOffset.top - 1;
    left = minLeft - containerOffset.left - 1;

    var style = this.wtDom.getComputedStyle(fromTD);
    if (parseInt(style['borderTopWidth'], 10) > 0) {
      top += 1;
      height = height > 0 ? height - 1 : 0;
    }
    if (parseInt(style['borderLeftWidth'], 10) > 0) {
      left += 1;
      width = width > 0 ? width - 1 : 0;
    }
  }
  else {
    this.disappear();
    return;
  }

  if (hideTop) {
    this.topStyle.display = 'none';
  }
  else {
    this.topStyle.top = top + 'px';
    this.topStyle.left = left + 'px';
    this.topStyle.width = width + 'px';
    this.topStyle.display = 'block';
  }

  if (hideLeft) {
    this.leftStyle.display = 'none';
  }
  else {
    this.leftStyle.top = top + 'px';
    this.leftStyle.left = left + 'px';
    this.leftStyle.height = height + 'px';
    this.leftStyle.display = 'block';
  }

  var delta = Math.floor(this.settings.border.width / 2);

  if (hideBottom) {
    this.bottomStyle.display = 'none';
  }
  else {
    this.bottomStyle.top = top + height - delta + 'px';
    this.bottomStyle.left = left + 'px';
    this.bottomStyle.width = width + 'px';
    this.bottomStyle.display = 'block';
  }

  if (hideRight) {
    this.rightStyle.display = 'none';
  }
  else {
    this.rightStyle.top = top + 'px';
    this.rightStyle.left = left + width - delta + 'px';
    this.rightStyle.height = height + 1 + 'px';
    this.rightStyle.display = 'block';
  }

  if (hideBottom || hideRight || !this.hasSetting(this.settings.border.cornerVisible)) {
    this.cornerStyle.display = 'none';
  }
  else {
    this.cornerStyle.top = top + height - 4 + 'px';
    this.cornerStyle.left = left + width - 4 + 'px';
    this.cornerStyle.display = 'block';
  }
};

/**
 * Hide border
 */
WalkontableBorder.prototype.disappear = function () {
  this.topStyle.display = 'none';
  this.leftStyle.display = 'none';
  this.bottomStyle.display = 'none';
  this.rightStyle.display = 'none';
  this.cornerStyle.display = 'none';
};

WalkontableBorder.prototype.hasSetting = function (setting) {
  if (typeof setting === 'function') {
    return setting();
  }
  return !!setting;
};
/**
 * WalkontableCellFilter
 * @constructor
 */
function WalkontableCellFilter() {
  this.offset = 0;
  this.total = 0;
  this.fixedCount = 0;
}

WalkontableCellFilter.prototype.source = function (n) {
  return n;
};

WalkontableCellFilter.prototype.offsetted = function (n) {
  return n + this.offset;
};

WalkontableCellFilter.prototype.unOffsetted = function (n) {
  return n - this.offset;
};

WalkontableCellFilter.prototype.fixed = function (n) {
  if (n < this.fixedCount) {
    return n - this.offset;
  }
  else {
    return n;
  }
};

WalkontableCellFilter.prototype.unFixed = function (n) {
  if (n < this.fixedCount) {
    return n + this.offset;
  }
  else {
    return n;
  }
};

WalkontableCellFilter.prototype.visibleToSource = function (n) {
  return this.source(this.offsetted(this.fixed(n)));
};

WalkontableCellFilter.prototype.sourceToVisible = function (n) {
  return this.source(this.unOffsetted(this.unFixed(n)));
};
/**
 * WalkontableCellStrategy
 * @constructor
 */
function WalkontableCellStrategy(instance) {
  this.instance = instance;
}

WalkontableCellStrategy.prototype.getSize = function (index) {
  return this.cellSizes[index];
};

WalkontableCellStrategy.prototype.getContainerSize = function (proposedSize) {
  return typeof this.containerSizeFn === 'function' ? this.containerSizeFn(proposedSize) : this.containerSizeFn;
};

WalkontableCellStrategy.prototype.countVisible = function () {
  return this.cellCount;
};

WalkontableCellStrategy.prototype.isLastIncomplete = function () {

  if(this.instance.getSetting('nativeScrollbars')){

    var nativeScrollbar = this.instance.cloneFrom ? this.instance.cloneFrom.wtScrollbars.vertical : this.instance.wtScrollbars.vertical;
    return this.remainingSize > nativeScrollbar.sumCellSizes(nativeScrollbar.offset, nativeScrollbar.offset + nativeScrollbar.curOuts + 1);

  } else {
    return this.remainingSize > 0;
  }

};
/**
 * WalkontableClassNameList
 * @constructor
 */
function WalkontableClassNameCache() {
  this.cache = [];
}

WalkontableClassNameCache.prototype.add = function (r, c, cls) {
  if (!this.cache[r]) {
    this.cache[r] = [];
  }
  if (!this.cache[r][c]) {
    this.cache[r][c] = [];
  }
  this.cache[r][c][cls] = true;
};

WalkontableClassNameCache.prototype.test = function (r, c, cls) {
  return (this.cache[r] && this.cache[r][c] && this.cache[r][c][cls]);
};
/**
 * WalkontableColumnFilter
 * @constructor
 */
function WalkontableColumnFilter() {
  this.countTH = 0;
}

WalkontableColumnFilter.prototype = new WalkontableCellFilter();

WalkontableColumnFilter.prototype.readSettings = function (instance) {
  this.offset = instance.wtSettings.settings.offsetColumn;
  this.total = instance.getSetting('totalColumns');
  this.fixedCount = instance.getSetting('fixedColumnsLeft');
  this.countTH = instance.getSetting('rowHeaders').length;
};

WalkontableColumnFilter.prototype.offsettedTH = function (n) {
  return n - this.countTH;
};

WalkontableColumnFilter.prototype.unOffsettedTH = function (n) {
  return n + this.countTH;
};

WalkontableColumnFilter.prototype.visibleRowHeadedColumnToSourceColumn = function (n) {
  return this.visibleToSource(this.offsettedTH(n));
};

WalkontableColumnFilter.prototype.sourceColumnToVisibleRowHeadedColumn = function (n) {
  return this.unOffsettedTH(this.sourceToVisible(n));
};
/**
 * WalkontableColumnStrategy
 * @param containerSizeFn
 * @param sizeAtIndex
 * @param strategy - all, last, none
 * @constructor
 */
function WalkontableColumnStrategy(instance, containerSizeFn, sizeAtIndex, strategy) {
  var size
    , i = 0;

  WalkontableCellStrategy.apply(this, arguments);

  this.containerSizeFn = containerSizeFn;
  this.cellSizesSum = 0;
  this.cellSizes = [];
  this.cellStretch = [];
  this.cellCount = 0;
  this.remainingSize = 0;
  this.strategy = strategy;

  //step 1 - determine cells that fit containerSize and cache their widths
  while (true) {
    size = sizeAtIndex(i);
    if (size === void 0) {
      break; //total columns exceeded
    }
    if (this.cellSizesSum >= this.getContainerSize(this.cellSizesSum + size)) {
      break; //total width exceeded
    }
    this.cellSizes.push(size);
    this.cellSizesSum += size;
    this.cellCount++;

    i++;
  }

  var containerSize = this.getContainerSize(this.cellSizesSum);
  this.remainingSize = this.cellSizesSum - containerSize;
  //negative value means the last cell is fully visible and there is some space left for stretching
  //positive value means the last cell is not fully visible
}

WalkontableColumnStrategy.prototype = new WalkontableCellStrategy();

WalkontableColumnStrategy.prototype.getSize = function (index) {
  return this.cellSizes[index] + (this.cellStretch[index] || 0);
};

WalkontableColumnStrategy.prototype.stretch = function () {
  //step 2 - apply stretching strategy
  var containerSize = this.getContainerSize(this.cellSizesSum)
    , i = 0;
  this.remainingSize = this.cellSizesSum - containerSize;

  this.cellStretch.length = 0; //clear previous stretch

  if (this.strategy === 'all') {
    if (this.remainingSize < 0) {
      var ratio = containerSize / this.cellSizesSum;
      var newSize;

      while (i < this.cellCount - 1) { //"i < this.cellCount - 1" is needed because last cellSize is adjusted after the loop
        newSize = Math.floor(ratio * this.cellSizes[i]);
        this.remainingSize += newSize - this.cellSizes[i];
        this.cellStretch[i] = newSize - this.cellSizes[i];
        i++;
      }
      this.cellStretch[this.cellCount - 1] = -this.remainingSize;
      this.remainingSize = 0;
    }
  }
  else if (this.strategy === 'last') {
    if (this.remainingSize < 0 && containerSize !== Infinity) { //Infinity is with native scroll when the table is wider than the viewport (TODO: test)
      this.cellStretch[this.cellCount - 1] = -this.remainingSize;
      this.remainingSize = 0;
    }
  }
};
function Walkontable(settings) {
  var that = this,
    originalHeaders = [];

  this.guid = 'wt_' + walkontableRandomString(); //this is the namespace for global events

  //bootstrap from settings
  this.wtDom = new WalkontableDom();
  if (settings.cloneSource) {
    this.cloneSource = settings.cloneSource;
    this.cloneOverlay = settings.cloneOverlay;
    this.wtSettings = settings.cloneSource.wtSettings;
    this.wtTable = new WalkontableTable(this, settings.table);
    this.wtScroll = new WalkontableScroll(this);
    this.wtViewport = settings.cloneSource.wtViewport;
  }
  else {
    this.wtSettings = new WalkontableSettings(this, settings);
    this.wtTable = new WalkontableTable(this, settings.table);
    this.wtScroll = new WalkontableScroll(this);
    this.wtViewport = new WalkontableViewport(this);
    this.wtScrollbars = new WalkontableScrollbars(this);
    this.wtWheel = new WalkontableWheel(this);
    this.wtEvent = new WalkontableEvent(this);
  }

  //find original headers
  if (this.wtTable.THEAD.childNodes.length && this.wtTable.THEAD.childNodes[0].childNodes.length) {
    for (var c = 0, clen = this.wtTable.THEAD.childNodes[0].childNodes.length; c < clen; c++) {
      originalHeaders.push(this.wtTable.THEAD.childNodes[0].childNodes[c].innerHTML);
    }
    if (!this.getSetting('columnHeaders').length) {
      this.update('columnHeaders', [function (column, TH) {
        that.wtDom.fastInnerText(TH, originalHeaders[column]);
      }]);
    }
  }

  //initialize selections
  this.selections = {};
  var selectionsSettings = this.getSetting('selections');
  if (selectionsSettings) {
    for (var i in selectionsSettings) {
      if (selectionsSettings.hasOwnProperty(i)) {
        this.selections[i] = new WalkontableSelection(this, selectionsSettings[i]);
      }
    }
  }

  this.drawn = false;
  this.drawInterrupted = false;

  //at this point the cached row heights may be invalid, but it is better not to reset the cache, which could cause scrollbar jumping when there are multiline cells outside of the rendered part of the table
  /*if (window.Handsontable) {
    Handsontable.PluginHooks.add('beforeChange', function () {
      if (that.rowHeightCache) {
        that.rowHeightCache.length = 0;
      }
    });

  }*/
}

Walkontable.prototype.draw = function (selectionsOnly) {
  this.drawInterrupted = false;
  if (!selectionsOnly && !this.wtDom.isVisible(this.wtTable.TABLE)) {
    this.drawInterrupted = true; //draw interrupted because TABLE is not visible
    return;
  }

  this.getSetting('beforeDraw', !selectionsOnly);
  selectionsOnly = selectionsOnly && this.getSetting('offsetRow') === this.lastOffsetRow && this.getSetting('offsetColumn') === this.lastOffsetColumn;
  if (this.drawn) { //fix offsets that might have changed
    this.scrollVertical(0);
    this.scrollHorizontal(0);
  }
  this.lastOffsetRow = this.getSetting('offsetRow');
  this.lastOffsetColumn = this.getSetting('offsetColumn');
  this.wtTable.draw(selectionsOnly);
  if (!this.cloneSource) {
    this.getSetting('onDraw',  !selectionsOnly);
  }
  return this;
};

Walkontable.prototype.update = function (settings, value) {
  return this.wtSettings.update(settings, value);
};

Walkontable.prototype.scrollVertical = function (delta) {
  var result = this.wtScroll.scrollVertical(delta);

  this.getSetting('onScrollVertically');

  return result;
};

Walkontable.prototype.scrollHorizontal = function (delta) {
  var result = this.wtScroll.scrollHorizontal(delta);

  this.getSetting('onScrollHorizontally');

  return result;
};

Walkontable.prototype.scrollViewport = function (coords) {
  this.wtScroll.scrollViewport(coords);
  return this;
};

Walkontable.prototype.getViewport = function () {
  return [
    this.wtTable.rowFilter.visibleToSource(0),
    this.wtTable.columnFilter.visibleToSource(0),
    this.wtTable.getLastVisibleRow(),
    this.wtTable.getLastVisibleColumn()
  ];
};

Walkontable.prototype.getSetting = function (key, param1, param2, param3) {
  return this.wtSettings.getSetting(key, param1, param2, param3);
};

Walkontable.prototype.hasSetting = function (key) {
  return this.wtSettings.has(key);
};

Walkontable.prototype.destroy = function () {
  $(document.body).off('.' + this.guid);
  this.wtScrollbars.destroy();
  clearTimeout(this.wheelTimeout);
  this.wtEvent && this.wtEvent.destroy();
};
/**
 * A overlay that renders ALL available rows & columns positioned on top of the original Walkontable instance and all other overlays.
 * Used for debugging purposes to see if the other overlays (that render only part of the rows & columns) are positioned correctly
 * @param instance
 * @constructor
 */
function WalkontableDebugOverlay(instance) {
  this.instance = instance;
  this.init();
  this.clone = this.makeClone('debug');
  this.clone.wtTable.holder.style.opacity = 0.4;
  this.clone.wtTable.holder.style.textShadow = '0 0 2px #ff0000';

  var that = this;
  var lastTimeout;
  var lastX = 0;
  var lastY = 0;
  var overlayContainer = that.clone.wtTable.holder.parentNode;

  $(document.body).on('mousemove.' + this.instance.guid, function (event) {
    if (!that.instance.wtTable.holder.parentNode) {
      return; //removed from DOM
    }
    if ((event.clientX - lastX > -5 && event.clientX - lastX < 5) && (event.clientY - lastY > -5 && event.clientY - lastY < 5)) {
      return; //ignore minor mouse movement
    }
    lastX = event.clientX;
    lastY = event.clientY;
    WalkontableDom.prototype.addClass(overlayContainer, 'wtDebugHidden');
    WalkontableDom.prototype.removeClass(overlayContainer, 'wtDebugVisible');
    clearTimeout(lastTimeout);
    lastTimeout = setTimeout(function () {
      WalkontableDom.prototype.removeClass(overlayContainer, 'wtDebugHidden');
      WalkontableDom.prototype.addClass(overlayContainer, 'wtDebugVisible');
    }, 1000);
  });
}

WalkontableDebugOverlay.prototype = new WalkontableOverlay();

WalkontableDebugOverlay.prototype.resetFixedPosition = function () {
  if (!this.instance.wtTable.holder.parentNode) {
    return; //removed from DOM
  }
  var elem = this.clone.wtTable.holder.parentNode;
  var box = this.instance.wtTable.holder.getBoundingClientRect();
  elem.style.top = Math.ceil(box.top, 10) + 'px';
  elem.style.left = Math.ceil(box.left, 10) + 'px';
};

WalkontableDebugOverlay.prototype.prepare = function () {
};

WalkontableDebugOverlay.prototype.refresh = function (selectionsOnly) {
  this.clone && this.clone.draw(selectionsOnly);
};

WalkontableDebugOverlay.prototype.getScrollPosition = function () {
};

WalkontableDebugOverlay.prototype.getLastCell = function () {
};

WalkontableDebugOverlay.prototype.applyToDOM = function () {
};

WalkontableDebugOverlay.prototype.scrollTo = function () {
};

WalkontableDebugOverlay.prototype.readWindowSize = function () {
};

WalkontableDebugOverlay.prototype.readSettings = function () {
};
function WalkontableDom() {
}

//goes up the DOM tree (including given element) until it finds an element that matches the nodeName
WalkontableDom.prototype.closest = function (elem, nodeNames, until) {
  while (elem != null && elem !== until) {
    if (elem.nodeType === 1 && nodeNames.indexOf(elem.nodeName) > -1) {
      return elem;
    }
    elem = elem.parentNode;
  }
  return null;
};

//goes up the DOM tree and checks if element is child of another element
WalkontableDom.prototype.isChildOf = function (child, parent) {
  var node = child.parentNode;
  while (node != null) {
    if (node == parent) {
      return true;
    }
    node = node.parentNode;
  }
  return false;
};

/**
 * Counts index of element within its parent
 * WARNING: for performance reasons, assumes there are only element nodes (no text nodes). This is true for Walkotnable
 * Otherwise would need to check for nodeType or use previousElementSibling
 * @see http://jsperf.com/sibling-index/10
 * @param {Element} elem
 * @return {Number}
 */
WalkontableDom.prototype.index = function (elem) {
  var i = 0;
  while (elem = elem.previousSibling) {
    ++i
  }
  return i;
};

if (document.documentElement.classList) {
  // HTML5 classList API
  WalkontableDom.prototype.hasClass = function (ele, cls) {
    return ele.classList.contains(cls);
  };

  WalkontableDom.prototype.addClass = function (ele, cls) {
    ele.classList.add(cls);
  };

  WalkontableDom.prototype.removeClass = function (ele, cls) {
    ele.classList.remove(cls);
  };
}
else {
  //http://snipplr.com/view/3561/addclass-removeclass-hasclass/
  WalkontableDom.prototype.hasClass = function (ele, cls) {
    return ele.className.match(new RegExp('(\\s|^)' + cls + '(\\s|$)'));
  };

  WalkontableDom.prototype.addClass = function (ele, cls) {
    if (!this.hasClass(ele, cls)) ele.className += " " + cls;
  };

  WalkontableDom.prototype.removeClass = function (ele, cls) {
    if (this.hasClass(ele, cls)) { //is this really needed?
      var reg = new RegExp('(\\s|^)' + cls + '(\\s|$)');
      ele.className = ele.className.replace(reg, ' ').trim(); //String.prototype.trim is defined in polyfill.js
    }
  };
}

/*//http://net.tutsplus.com/tutorials/javascript-ajax/javascript-from-null-cross-browser-event-binding/
 WalkontableDom.prototype.addEvent = (function () {
 var that = this;
 if (document.addEventListener) {
 return function (elem, type, cb) {
 if ((elem && !elem.length) || elem === window) {
 elem.addEventListener(type, cb, false);
 }
 else if (elem && elem.length) {
 var len = elem.length;
 for (var i = 0; i < len; i++) {
 that.addEvent(elem[i], type, cb);
 }
 }
 };
 }
 else {
 return function (elem, type, cb) {
 if ((elem && !elem.length) || elem === window) {
 elem.attachEvent('on' + type, function () {

 //normalize
 //http://stackoverflow.com/questions/4643249/cross-browser-event-object-normalization
 var e = window['event'];
 e.target = e.srcElement;
 //e.offsetX = e.layerX;
 //e.offsetY = e.layerY;
 e.relatedTarget = e.relatedTarget || e.type == 'mouseover' ? e.fromElement : e.toElement;
 if (e.target.nodeType === 3) e.target = e.target.parentNode; //Safari bug

 return cb.call(elem, e)
 });
 }
 else if (elem.length) {
 var len = elem.length;
 for (var i = 0; i < len; i++) {
 that.addEvent(elem[i], type, cb);
 }
 }
 };
 }
 })();

 WalkontableDom.prototype.triggerEvent = function (element, eventName, target) {
 var event;
 if (document.createEvent) {
 event = document.createEvent("MouseEvents");
 event.initEvent(eventName, true, true);
 } else {
 event = document.createEventObject();
 event.eventType = eventName;
 }

 event.eventName = eventName;
 event.target = target;

 if (document.createEvent) {
 target.dispatchEvent(event);
 } else {
 target.fireEvent("on" + event.eventType, event);
 }
 };*/

WalkontableDom.prototype.removeTextNodes = function (elem, parent) {
  if (elem.nodeType === 3) {
    parent.removeChild(elem); //bye text nodes!
  }
  else if (['TABLE', 'THEAD', 'TBODY', 'TFOOT', 'TR'].indexOf(elem.nodeName) > -1) {
    var childs = elem.childNodes;
    for (var i = childs.length - 1; i >= 0; i--) {
      this.removeTextNodes(childs[i], elem);
    }
  }
};

/**
 * Remove childs function
 * WARNING - this doesn't unload events and data attached by jQuery
 * http://jsperf.com/jquery-html-vs-empty-vs-innerhtml/9
 * http://jsperf.com/jquery-html-vs-empty-vs-innerhtml/11 - no siginificant improvement with Chrome remove() method
 * @param element
 * @returns {void}
 */
//
WalkontableDom.prototype.empty = function (element) {
  var child;
  while (child = element.lastChild) {
    element.removeChild(child);
  }
};

WalkontableDom.prototype.HTML_CHARACTERS = /(<(.*)>|&(.*);)/;

/**
 * Insert content into element trying avoid innerHTML method.
 * @return {void}
 */
WalkontableDom.prototype.fastInnerHTML = function (element, content) {
  if (this.HTML_CHARACTERS.test(content)) {
    element.innerHTML = content;
  }
  else {
    this.fastInnerText(element, content);
  }
};

/**
 * Insert text content into element
 * @return {void}
 */
if (document.createTextNode('test').textContent) { //STANDARDS
  WalkontableDom.prototype.fastInnerText = function (element, content) {
    var child = element.firstChild;
    if (child && child.nodeType === 3 && child.nextSibling === null) {
      //fast lane - replace existing text node
      //http://jsperf.com/replace-text-vs-reuse
      child.textContent = content;
    }
    else {
      //slow lane - empty element and insert a text node
      this.empty(element);
      element.appendChild(document.createTextNode(content));
    }
  };
}
else { //IE8
  WalkontableDom.prototype.fastInnerText = function (element, content) {
    var child = element.firstChild;
    if (child && child.nodeType === 3 && child.nextSibling === null) {
      //fast lane - replace existing text node
      //http://jsperf.com/replace-text-vs-reuse
      child.data = content;
    }
    else {
      //slow lane - empty element and insert a text node
      this.empty(element);
      element.appendChild(document.createTextNode(content));
    }
  };
}

/**
 * Returns true/false depending if element has offset parent
 * @param elem
 * @returns {boolean}
 */
/*if (document.createTextNode('test').textContent) { //STANDARDS
  WalkontableDom.prototype.hasOffsetParent = function (elem) {
    return !!elem.offsetParent;
  }
}
else {
  WalkontableDom.prototype.hasOffsetParent = function (elem) {
    try {
      if (!elem.offsetParent) {
        return false;
      }
    }
    catch (e) {
      return false; //IE8 throws "Unspecified error" when offsetParent is not found - we catch it here
    }
    return true;
  }
}*/

/**
 * Returns true if element is attached to the DOM and visible, false otherwise
 * @param elem
 * @returns {boolean}
 */
WalkontableDom.prototype.isVisible = function (elem) {
  //fast method according to benchmarks, but requires layout so slow in our case
  /*
  if (!WalkontableDom.prototype.hasOffsetParent(elem)) {
    return false; //fixes problem with UI Bootstrap <tabs> directive
  }

//  if (elem.offsetWidth > 0 || (elem.parentNode && elem.parentNode.offsetWidth > 0)) { //IE10 was mistaken here
  if (elem.offsetWidth > 0) {
    return true;
  }
  */

  //slow method
  var next = elem;
  while (next !== document.documentElement) { //until <html> reached
    if (next === null) { //parent detached from DOM
      return false;
    }
    else if (next.nodeType === 11) {  //nodeType == 1 -> DOCUMENT_FRAGMENT_NODE
      if (next.host) { //this is Web Components Shadow DOM
        //see: http://w3c.github.io/webcomponents/spec/shadow/#encapsulation
        //according to spec, should be if (next.ownerDocument !== window.document), but that doesn't work yet
        if (next.host.impl) { //Chrome 33.0.1723.0 canary (2013-11-29) Web Platform features disabled
          return WalkontableDom.prototype.isVisible(next.host.impl);
        }
        else if (next.host) { //Chrome 33.0.1723.0 canary (2013-11-29) Web Platform features enabled
          return WalkontableDom.prototype.isVisible(next.host);
        }
        else {
          throw new Error("Lost in Web Components world");
        }
      }
      else {
        return false; //this is a node detached from document in IE8
      }
    }
    else if (next.style.display === 'none') {
      return false;
    }
    next = next.parentNode;
  }
  return true;
};

/**
 * Returns elements top and left offset relative to the document. In our usage case compatible with jQuery but 2x faster
 * @param {HTMLElement} elem
 * @return {Object}
 */
WalkontableDom.prototype.offset = function (elem) {
  if (this.hasCaptionProblem() && elem.firstChild && elem.firstChild.nodeName === 'CAPTION') {
    //fixes problem with Firefox ignoring <caption> in TABLE offset (see also WalkontableDom.prototype.outerHeight)
    //http://jsperf.com/offset-vs-getboundingclientrect/8
    var box = elem.getBoundingClientRect();
    return {
      top: box.top + (window.pageYOffset || document.documentElement.scrollTop) - (document.documentElement.clientTop || 0),
      left: box.left + (window.pageXOffset || document.documentElement.scrollLeft) - (document.documentElement.clientLeft || 0)
    };
  }

  var offsetLeft = elem.offsetLeft
    , offsetTop = elem.offsetTop
    , lastElem = elem;

  while (elem = elem.offsetParent) {
    if (elem === document.body) { //from my observation, document.body always has scrollLeft/scrollTop == 0
      break;
    }
    offsetLeft += elem.offsetLeft;
    offsetTop += elem.offsetTop;
    lastElem = elem;
  }

  if (lastElem && lastElem.style.position === 'fixed') { //slow - http://jsperf.com/offset-vs-getboundingclientrect/6
    //if(lastElem !== document.body) { //faster but does gives false positive in Firefox
    offsetLeft += window.pageXOffset || document.documentElement.scrollLeft;
    offsetTop += window.pageYOffset || document.documentElement.scrollTop;
  }

  return {
    left: offsetLeft,
    top: offsetTop
  };
};

WalkontableDom.prototype.getComputedStyle = function (elem) {
  return elem.currentStyle || document.defaultView.getComputedStyle(elem);
};

WalkontableDom.prototype.outerWidth = function (elem) {
  return elem.offsetWidth;
};

WalkontableDom.prototype.outerHeight = function (elem) {
  if (this.hasCaptionProblem() && elem.firstChild && elem.firstChild.nodeName === 'CAPTION') {
    //fixes problem with Firefox ignoring <caption> in TABLE.offsetHeight
    //jQuery (1.10.1) still has this unsolved
    //may be better to just switch to getBoundingClientRect
    //http://bililite.com/blog/2009/03/27/finding-the-size-of-a-table/
    //http://lists.w3.org/Archives/Public/www-style/2009Oct/0089.html
    //http://bugs.jquery.com/ticket/2196
    //http://lists.w3.org/Archives/Public/www-style/2009Oct/0140.html#start140
    return elem.offsetHeight + elem.firstChild.offsetHeight;
  }
  else {
    return elem.offsetHeight;
  }
};

(function () {
  var hasCaptionProblem;

  function detectCaptionProblem() {
    var TABLE = document.createElement('TABLE');
    TABLE.style.borderSpacing = 0;
    TABLE.style.borderWidth = 0;
    TABLE.style.padding = 0;
    var TBODY = document.createElement('TBODY');
    TABLE.appendChild(TBODY);
    TBODY.appendChild(document.createElement('TR'));
    TBODY.firstChild.appendChild(document.createElement('TD'));
    TBODY.firstChild.firstChild.innerHTML = '<tr><td>t<br>t</td></tr>';

    var CAPTION = document.createElement('CAPTION');
    CAPTION.innerHTML = 'c<br>c<br>c<br>c';
    CAPTION.style.padding = 0;
    CAPTION.style.margin = 0;
    TABLE.insertBefore(CAPTION, TBODY);

    document.body.appendChild(TABLE);
    hasCaptionProblem = (TABLE.offsetHeight < 2 * TABLE.lastChild.offsetHeight); //boolean
    document.body.removeChild(TABLE);
  }

  WalkontableDom.prototype.hasCaptionProblem = function () {
    if (hasCaptionProblem === void 0) {
      detectCaptionProblem();
    }
    return hasCaptionProblem;
  };

  /**
   * Returns caret position in text input
   * @author http://stackoverflow.com/questions/263743/how-to-get-caret-position-in-textarea
   * @return {Number}
   */
  WalkontableDom.prototype.getCaretPosition = function (el) {
    if (el.selectionStart) {
      return el.selectionStart;
    }
    else if (document.selection) { //IE8
      el.focus();
      var r = document.selection.createRange();
      if (r == null) {
        return 0;
      }
      var re = el.createTextRange(),
        rc = re.duplicate();
      re.moveToBookmark(r.getBookmark());
      rc.setEndPoint('EndToStart', re);
      return rc.text.length;
    }
    return 0;
  };

  /**
   * Sets caret position in text input
   * @author http://blog.vishalon.net/index.php/javascript-getting-and-setting-caret-position-in-textarea/
   * @param {Element} el
   * @param {Number} pos
   * @param {Number} endPos
   */
  WalkontableDom.prototype.setCaretPosition = function (el, pos, endPos) {
    if (endPos === void 0) {
      endPos = pos;
    }
    if (el.setSelectionRange) {
      el.focus();
      el.setSelectionRange(pos, endPos);
    }
    else if (el.createTextRange) { //IE8
      var range = el.createTextRange();
      range.collapse(true);
      range.moveEnd('character', endPos);
      range.moveStart('character', pos);
      range.select();
    }
  };

  var cachedScrollbarWidth;
  //http://stackoverflow.com/questions/986937/how-can-i-get-the-browsers-scrollbar-sizes
  function walkontableCalculateScrollbarWidth() {
    var inner = document.createElement('p');
    inner.style.width = "100%";
    inner.style.height = "200px";

    var outer = document.createElement('div');
    outer.style.position = "absolute";
    outer.style.top = "0px";
    outer.style.left = "0px";
    outer.style.visibility = "hidden";
    outer.style.width = "200px";
    outer.style.height = "150px";
    outer.style.overflow = "hidden";
    outer.appendChild(inner);

    (document.body || document.documentElement).appendChild(outer);
    var w1 = inner.offsetWidth;
    outer.style.overflow = 'scroll';
    var w2 = inner.offsetWidth;
    if (w1 == w2) w2 = outer.clientWidth;

    (document.body || document.documentElement).removeChild(outer);

    return (w1 - w2);
  }

  /**
   * Returns the computed width of the native browser scroll bar
   * @return {Number} width
   */
  WalkontableDom.prototype.getScrollbarWidth = function () {
    if (cachedScrollbarWidth === void 0) {
      cachedScrollbarWidth = walkontableCalculateScrollbarWidth();
    }
    return cachedScrollbarWidth;
  }
})();

function WalkontableEvent(instance) {
  var that = this;

  //reference to instance
  this.instance = instance;

  this.wtDom = this.instance.wtDom;

  var dblClickOrigin = [null, null];
  var dblClickTimeout = [null, null];

  var onMouseDown = function (event) {
    var cell = that.parentCell(event.target);

    if (that.wtDom.hasClass(event.target, 'corner')) {
      that.instance.getSetting('onCellCornerMouseDown', event, event.target);
    }
    else if (cell.TD && cell.TD.nodeName === 'TD') {
      if (that.instance.hasSetting('onCellMouseDown')) {
        that.instance.getSetting('onCellMouseDown', event, cell.coords, cell.TD);
      }
    }

    if (event.button !== 2) { //if not right mouse button
      if (cell.TD && cell.TD.nodeName === 'TD') {
        dblClickOrigin[0] = cell.TD;
        clearTimeout(dblClickTimeout[0]);
        dblClickTimeout[0] = setTimeout(function () {
          dblClickOrigin[0] = null;
        }, 1000);
      }
    }
  };

  var lastMouseOver;
  var onMouseOver = function (event) {
    if (that.instance.hasSetting('onCellMouseOver')) {
      var TABLE = that.instance.wtTable.TABLE;
      var TD = that.wtDom.closest(event.target, ['TD', 'TH'], TABLE);
      if (TD && TD !== lastMouseOver && that.wtDom.isChildOf(TD, TABLE)) {
        lastMouseOver = TD;
        if (TD.nodeName === 'TD') {
          that.instance.getSetting('onCellMouseOver', event, that.instance.wtTable.getCoords(TD), TD);
        }
      }
    }
  };

/*  var lastMouseOut;
  var onMouseOut = function (event) {
    if (that.instance.hasSetting('onCellMouseOut')) {
      var TABLE = that.instance.wtTable.TABLE;
      var TD = that.wtDom.closest(event.target, ['TD', 'TH'], TABLE);
      if (TD && TD !== lastMouseOut && that.wtDom.isChildOf(TD, TABLE)) {
        lastMouseOut = TD;
        if (TD.nodeName === 'TD') {
          that.instance.getSetting('onCellMouseOut', event, that.instance.wtTable.getCoords(TD), TD);
        }
      }
    }
  };*/

  var onMouseUp = function (event) {
    if (event.button !== 2) { //if not right mouse button
      var cell = that.parentCell(event.target);

      if (cell.TD === dblClickOrigin[0] && cell.TD === dblClickOrigin[1]) {
        if (that.wtDom.hasClass(event.target, 'corner')) {
          that.instance.getSetting('onCellCornerDblClick', event, cell.coords, cell.TD);
        }
        else if (cell.TD) {
          that.instance.getSetting('onCellDblClick', event, cell.coords, cell.TD);
        }

        dblClickOrigin[0] = null;
        dblClickOrigin[1] = null;
      }
      else if (cell.TD === dblClickOrigin[0]) {
        dblClickOrigin[1] = cell.TD;
        clearTimeout(dblClickTimeout[1]);
        dblClickTimeout[1] = setTimeout(function () {
          dblClickOrigin[1] = null;
        }, 500);
      }
    }
  };

  $(this.instance.wtTable.holder).on('mousedown', onMouseDown);
  $(this.instance.wtTable.TABLE).on('mouseover', onMouseOver);
//  $(this.instance.wtTable.TABLE).on('mouseout', onMouseOut);
  $(this.instance.wtTable.holder).on('mouseup', onMouseUp);
}

WalkontableEvent.prototype.parentCell = function (elem) {
  var cell = {};
  var TABLE = this.instance.wtTable.TABLE;
  var TD = this.wtDom.closest(elem, ['TD', 'TH'], TABLE);
  if (TD && this.wtDom.isChildOf(TD, TABLE)) {
    cell.coords = this.instance.wtTable.getCoords(TD);
    cell.TD = TD;
  }
  else if (this.wtDom.hasClass(elem, 'wtBorder') && this.wtDom.hasClass(elem, 'current')) {
    cell.coords = this.instance.selections.current.selected[0];
    cell.TD = this.instance.wtTable.getCell(cell.coords);
  }
  return cell;
};

WalkontableEvent.prototype.destroy = function () {
  clearTimeout(this.dblClickTimeout0);
  clearTimeout(this.dblClickTimeout1);
};
function walkontableRangesIntersect() {
  var from = arguments[0];
  var to = arguments[1];
  for (var i = 1, ilen = arguments.length / 2; i < ilen; i++) {
    if (from <= arguments[2 * i + 1] && to >= arguments[2 * i]) {
      return true;
    }
  }
  return false;
}

/**
 * Generates a random hex string. Used as namespace for Walkontable instance events.
 * @return {String} - 16 character random string: "92b1bfc74ec4"
 */
function walkontableRandomString() {
  function s4() {
    return Math.floor((1 + Math.random()) * 0x10000)
      .toString(16)
      .substring(1);
  }

  return s4() + s4() + s4() + s4();
}
/**
 * http://notes.jetienne.com/2011/05/18/cancelRequestAnimFrame-for-paul-irish-requestAnimFrame.html
 */
window.requestAnimFrame = (function () {
  return  window.requestAnimationFrame ||
    window.webkitRequestAnimationFrame ||
    window.mozRequestAnimationFrame ||
    window.oRequestAnimationFrame ||
    window.msRequestAnimationFrame ||
    function (/* function */ callback, /* DOMElement */ element) {
      return window.setTimeout(callback, 1000 / 60);
    };
})();

window.cancelRequestAnimFrame = (function () {
  return window.cancelAnimationFrame ||
    window.webkitCancelRequestAnimationFrame ||
    window.mozCancelRequestAnimationFrame ||
    window.oCancelRequestAnimationFrame ||
    window.msCancelRequestAnimationFrame ||
    clearTimeout
})();

//http://snipplr.com/view/13523/
//modified for speed
//http://jsperf.com/getcomputedstyle-vs-style-vs-css/8
if (!window.getComputedStyle) {
  (function () {
    var elem;

    var styleObj = {
      getPropertyValue: function getPropertyValue(prop) {
        if (prop == 'float') prop = 'styleFloat';
        return elem.currentStyle[prop.toUpperCase()] || null;
      }
    };

    window.getComputedStyle = function (el) {
      elem = el;
      return styleObj;
    }
  })();
}

/**
 * https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/String/Trim
 */
if (!String.prototype.trim) {
  var trimRegex = /^\s+|\s+$/g;
  String.prototype.trim = function () {
    return this.replace(trimRegex, '');
  };
}
/**
 * WalkontableRowFilter
 * @constructor
 */
function WalkontableRowFilter() {
}

WalkontableRowFilter.prototype = new WalkontableCellFilter();

WalkontableRowFilter.prototype.readSettings = function (instance) {
  if (instance.cloneOverlay instanceof WalkontableDebugOverlay) {
    this.offset = 0;
  }
  else {
    this.offset = instance.wtSettings.settings.offsetRow;
  }
  this.total = instance.getSetting('totalRows');
  this.fixedCount = instance.getSetting('fixedRowsTop');
};
/**
 * WalkontableRowStrategy
 * @param containerSizeFn
 * @param sizeAtIndex
 * @constructor
 */
function WalkontableRowStrategy(instance, containerSizeFn, sizeAtIndex) {

  WalkontableCellStrategy.apply(this, arguments);

  this.containerSizeFn = containerSizeFn;
  this.sizeAtIndex = sizeAtIndex;
  this.cellSizesSum = 0;
  this.cellSizes = [];
  this.cellCount = 0;
  this.remainingSize = -Infinity;
}

WalkontableRowStrategy.prototype = new WalkontableCellStrategy();

WalkontableRowStrategy.prototype.add = function (i, TD, reverse) {
  if (!this.isLastIncomplete() && this.remainingSize != 0) {
    var size = this.sizeAtIndex(i, TD);
    if (size === void 0) {
      return false; //total rows exceeded
    }
    var containerSize = this.getContainerSize(this.cellSizesSum + size);
    if (reverse) {
      this.cellSizes.unshift(size);
    }
    else {
      this.cellSizes.push(size);
    }
    this.cellSizesSum += size;
    this.cellCount++;
    this.remainingSize = this.cellSizesSum - containerSize;

    if (reverse && this.isLastIncomplete()) { //something is outside of the screen, maybe even some full rows?
      return false;
    }
    return true;
  }
  return false;
};

WalkontableRowStrategy.prototype.remove = function () {
  var size = this.cellSizes.pop();
  this.cellSizesSum -= size;
  this.cellCount--;
  this.remainingSize -= size;
};

WalkontableRowStrategy.prototype.removeOutstanding = function () {
  while (this.cellCount > 0 && this.cellSizes[this.cellCount - 1] < this.remainingSize) { //this row is completely off screen!
    this.remove();
  }
};
function WalkontableScroll(instance) {
  this.instance = instance;
}

WalkontableScroll.prototype.scrollVertical = function (delta) {
  if (!this.instance.drawn) {
    throw new Error('scrollVertical can only be called after table was drawn to DOM');
  }

  var instance = this.instance
    , newOffset
    , offset = instance.getSetting('offsetRow')
    , fixedCount = instance.getSetting('fixedRowsTop')
    , total = instance.getSetting('totalRows')
    , maxSize = instance.wtViewport.getViewportHeight();

  if (total > 0) {
    newOffset = this.scrollLogicVertical(delta, offset, total, fixedCount, maxSize, function (row) {
      if (row - offset < fixedCount && row - offset >= 0) {
        return instance.getSetting('rowHeight', row - offset);
      }
      else {
        return instance.getSetting('rowHeight', row);
      }
    }, function (isReverse) {
      instance.wtTable.verticalRenderReverse = isReverse;
    });
  }
  else {
    newOffset = 0;
  }

  if (newOffset !== offset) {
    this.instance.wtScrollbars.vertical.scrollTo(newOffset);
  }
  return instance;
};

WalkontableScroll.prototype.scrollHorizontal = function (delta) {
  if (!this.instance.drawn) {
    throw new Error('scrollHorizontal can only be called after table was drawn to DOM');
  }

  var instance = this.instance
    , newOffset
    , offset = instance.getSetting('offsetColumn')
    , fixedCount = instance.getSetting('fixedColumnsLeft')
    , total = instance.getSetting('totalColumns')
    , maxSize = instance.wtViewport.getViewportWidth();

  if (total > 0) {
    newOffset = this.scrollLogicHorizontal(delta, offset, total, fixedCount, maxSize, function (col) {
      if (col - offset < fixedCount && col - offset >= 0) {
        return instance.getSetting('columnWidth', col - offset);
      }
      else {
        return instance.getSetting('columnWidth', col);
      }
    });
  }
  else {
    newOffset = 0;
  }

  if (newOffset !== offset) {
    this.instance.wtScrollbars.horizontal.scrollTo(newOffset);
  }
  return instance;
};

WalkontableScroll.prototype.scrollLogicVertical = function (delta, offset, total, fixedCount, maxSize, cellSizeFn, setReverseRenderFn) {
  var newOffset = offset + delta;

  if (newOffset >= total - fixedCount) {
    newOffset = total - fixedCount - 1;
    setReverseRenderFn(true);
  }

  if (newOffset < 0) {
    newOffset = 0;
  }

  return newOffset;
};

WalkontableScroll.prototype.scrollLogicHorizontal = function (delta, offset, total, fixedCount, maxSize, cellSizeFn) {
  var newOffset = offset + delta
    , sum = 0
    , col;

  if (newOffset > fixedCount) {
    if (newOffset >= total - fixedCount) {
      newOffset = total - fixedCount - 1;
    }

    col = newOffset;
    while (sum < maxSize && col < total) {
      sum += cellSizeFn(col);
      col++;
    }

    if (sum < maxSize) {
      while (newOffset > 0) {
        //if sum still less than available width, we cannot scroll that far (must move offset to the left)
        sum += cellSizeFn(newOffset - 1);
        if (sum < maxSize) {
          newOffset--;
        }
        else {
          break;
        }
      }
    }
  }
  else if (newOffset < 0) {
    newOffset = 0;
  }

  return newOffset;
};

/**
 * Scrolls viewport to a cell by minimum number of cells
 */
WalkontableScroll.prototype.scrollViewport = function (coords) {
  if (!this.instance.drawn) {
    return;
  }

  var offsetRow = this.instance.getSetting('offsetRow')
    , offsetColumn = this.instance.getSetting('offsetColumn')
    , lastVisibleRow = this.instance.wtTable.getLastVisibleRow()
    , totalRows = this.instance.getSetting('totalRows')
    , totalColumns = this.instance.getSetting('totalColumns')
    , fixedRowsTop = this.instance.getSetting('fixedRowsTop')
    , fixedColumnsLeft = this.instance.getSetting('fixedColumnsLeft');

  if (this.instance.getSetting('nativeScrollbars')) {
    var TD = this.instance.wtTable.getCell(coords);
    if (typeof TD === 'object') {
      var offset = WalkontableDom.prototype.offset(TD);
      var outerWidth = WalkontableDom.prototype.outerWidth(TD);
      var outerHeight = WalkontableDom.prototype.outerHeight(TD);
      var scrollX = this.instance.wtScrollbars.horizontal.getScrollPosition();
      var scrollY = this.instance.wtScrollbars.vertical.getScrollPosition();
      var clientWidth = WalkontableDom.prototype.outerWidth(this.instance.wtScrollbars.horizontal.scrollHandler);
      var clientHeight = WalkontableDom.prototype.outerHeight(this.instance.wtScrollbars.vertical.scrollHandler);
      if (this.instance.wtScrollbars.horizontal.scrollHandler !== window) {
        offset.left = offset.left - WalkontableDom.prototype.offset(this.instance.wtScrollbars.horizontal.scrollHandler).left;
      }
      if (this.instance.wtScrollbars.vertical.scrollHandler !== window) {
        offset.top = offset.top - WalkontableDom.prototype.offset(this.instance.wtScrollbars.vertical.scrollHandler).top;
      }

      clientWidth -= 20;
      clientHeight -= 20;

      if (outerWidth < clientWidth) {
        if (offset.left < scrollX) {
          this.instance.wtScrollbars.horizontal.setScrollPosition(offset.left);
        }
        else if (offset.left + outerWidth > scrollX + clientWidth) {
          this.instance.wtScrollbars.horizontal.setScrollPosition(offset.left - clientWidth + outerWidth);
        }
      }
      if (outerHeight < clientHeight) {
        if (offset.top < scrollY) {
          this.instance.wtScrollbars.vertical.setScrollPosition(offset.top);
        }
        else if (offset.top + outerHeight > scrollY + clientHeight) {
          this.instance.wtScrollbars.vertical.setScrollPosition(offset.top - clientHeight + outerHeight);
        }
      }
      return;
    }
  }

  if (coords[0] < 0 || coords[0] > totalRows - 1) {
    throw new Error('row ' + coords[0] + ' does not exist');
  }
  else if (coords[1] < 0 || coords[1] > totalColumns - 1) {
    throw new Error('column ' + coords[1] + ' does not exist');
  }

  if (coords[0] > lastVisibleRow) {
//    this.scrollVertical(coords[0] - lastVisibleRow + 1);
    this.scrollVertical(coords[0] - fixedRowsTop - offsetRow);
    this.instance.wtTable.verticalRenderReverse = true;
  }
  else if (coords[0] === lastVisibleRow && this.instance.wtTable.rowStrategy.isLastIncomplete()) {
//    this.scrollVertical(coords[0] - lastVisibleRow + 1);
    this.scrollVertical(coords[0] - fixedRowsTop - offsetRow);
    this.instance.wtTable.verticalRenderReverse = true;
  }
  else if (coords[0] - fixedRowsTop < offsetRow) {
    this.scrollVertical(coords[0] - fixedRowsTop - offsetRow);
  }
  else {
    this.scrollVertical(0); //Craig's issue: remove row from the last scroll page should scroll viewport a row up if needed
  }

  if (this.instance.wtTable.isColumnBeforeViewport(coords[1])) {
    //scroll left
    this.instance.wtScrollbars.horizontal.scrollTo(coords[1] - fixedColumnsLeft);
  }
  else if (this.instance.wtTable.isColumnAfterViewport(coords[1]) || (this.instance.wtTable.getLastVisibleColumn() === coords[1] && !this.instance.wtTable.isLastColumnFullyVisible())) {
    //scroll right
    var sum = 0;
    for (var i = 0; i < fixedColumnsLeft; i++) {
      sum += this.instance.getSetting('columnWidth', i);
    }
    var scrollTo = coords[1];
    sum += this.instance.getSetting('columnWidth', scrollTo);
    var available = this.instance.wtViewport.getViewportWidth();
    if (sum < available) {
      var next = this.instance.getSetting('columnWidth', scrollTo - 1);
      while (sum + next <= available && scrollTo >= fixedColumnsLeft) {
        scrollTo--;
        sum += next;
        next = this.instance.getSetting('columnWidth', scrollTo - 1);
      }
    }

    this.instance.wtScrollbars.horizontal.scrollTo(scrollTo - fixedColumnsLeft);
  }
  /*else {
   //no scroll
   }*/

  return this.instance;
};

function WalkontableScrollbar() {
}

WalkontableScrollbar.prototype.init = function () {
  var that = this;

  //reference to instance
  this.$table = $(this.instance.wtTable.TABLE);

  //create elements
  this.slider = document.createElement('DIV');
  this.sliderStyle = this.slider.style;
  this.sliderStyle.position = 'absolute';
  this.sliderStyle.top = '0';
  this.sliderStyle.left = '0';
  this.sliderStyle.display = 'none';
  this.slider.className = 'dragdealer ' + this.type;

  this.handle = document.createElement('DIV');
  this.handleStyle = this.handle.style;
  this.handle.className = 'handle';

  this.slider.appendChild(this.handle);
  this.container = this.instance.wtTable.holder;
  this.container.appendChild(this.slider);

  var firstRun = true;
  this.dragTimeout = null;
  var dragDelta;
  var dragRender = function () {
    that.onScroll(dragDelta);
  };

  this.dragdealer = new Dragdealer(this.slider, {
    vertical: (this.type === 'vertical'),
    horizontal: (this.type === 'horizontal'),
    slide: false,
    speed: 100,
    animationCallback: function (x, y) {
      if (firstRun) {
        firstRun = false;
        return;
      }
      that.skipRefresh = true;
      dragDelta = that.type === 'vertical' ? y : x;
      if (that.dragTimeout === null) {
        that.dragTimeout = setInterval(dragRender, 100);
        dragRender();
      }
    },
    callback: function (x, y) {
      that.skipRefresh = false;
      clearInterval(that.dragTimeout);
      that.dragTimeout = null;
      dragDelta = that.type === 'vertical' ? y : x;
      that.onScroll(dragDelta);
    }
  });
  this.skipRefresh = false;
};

WalkontableScrollbar.prototype.onScroll = function (delta) {
  if (this.instance.drawn) {
    this.readSettings();
    if (this.total > this.visibleCount) {
      var newOffset = Math.round(this.handlePosition * this.total / this.sliderSize);

      if (delta === 1) {
        if (this.type === 'vertical') {
          this.instance.scrollVertical(Infinity).draw();
        }
        else {
          this.instance.scrollHorizontal(Infinity).draw();
        }
      }
      else if (newOffset !== this.offset) { //is new offset different than old offset
        if (this.type === 'vertical') {
          this.instance.scrollVertical(newOffset - this.offset).draw();
        }
        else {
          this.instance.scrollHorizontal(newOffset - this.offset).draw();
        }
      }
      else {
        this.refresh();
      }
    }
  }
};

/**
 * Returns what part of the scroller should the handle take
 * @param viewportCount {Number} number of visible rows or columns
 * @param totalCount {Number} total number of rows or columns
 * @return {Number} 0..1
 */
WalkontableScrollbar.prototype.getHandleSizeRatio = function (viewportCount, totalCount) {
  if (!totalCount || viewportCount > totalCount || viewportCount == totalCount) {
    return 1;
  }
  return 1 / totalCount;
};

WalkontableScrollbar.prototype.prepare = function () {
  if (this.skipRefresh) {
    return;
  }
  var ratio = this.getHandleSizeRatio(this.visibleCount, this.total);
  if (((ratio === 1 || isNaN(ratio)) && this.scrollMode === 'auto') || this.scrollMode === 'none') {
    //isNaN is needed because ratio equals NaN when totalRows/totalColumns equals 0
    this.visible = false;
  }
  else {
    this.visible = true;
  }
};

WalkontableScrollbar.prototype.refresh = function () {
  if (this.skipRefresh) {
    return;
  }
  else if (!this.visible) {
    this.sliderStyle.display = 'none';
    return;
  }

  var ratio
    , sliderSize
    , handleSize
    , handlePosition
    , visibleCount = this.visibleCount
    , tableWidth = this.instance.wtViewport.getWorkspaceWidth()
    , tableHeight = this.instance.wtViewport.getWorkspaceHeight();

  if (tableWidth === Infinity) {
    tableWidth = this.instance.wtViewport.getWorkspaceActualWidth();
  }

  if (tableHeight === Infinity) {
    tableHeight = this.instance.wtViewport.getWorkspaceActualHeight();
  }

  if (this.type === 'vertical') {
    if (this.instance.wtTable.rowStrategy.isLastIncomplete()) {
      visibleCount--;
    }

    sliderSize = tableHeight - 2; //2 is sliders border-width

    this.sliderStyle.top = this.instance.wtDom.offset(this.$table[0]).top - this.instance.wtDom.offset(this.container).top + 'px';
    this.sliderStyle.left = tableWidth - 1 + 'px'; //1 is sliders border-width
    this.sliderStyle.height = Math.max(sliderSize, 0) + 'px';
  }
  else { //horizontal
    sliderSize = tableWidth - 2; //2 is sliders border-width

    this.sliderStyle.left = this.instance.wtDom.offset(this.$table[0]).left - this.instance.wtDom.offset(this.container).left + 'px';
    this.sliderStyle.top = tableHeight - 1 + 'px'; //1 is sliders border-width
    this.sliderStyle.width = Math.max(sliderSize, 0) + 'px';
  }

  ratio = this.getHandleSizeRatio(visibleCount, this.total);
  handleSize = Math.round(sliderSize * ratio);
  if (handleSize < 10) {
    handleSize = 15;
  }

  handlePosition = Math.floor(sliderSize * (this.offset / this.total));
  if (handleSize + handlePosition > sliderSize) {
    handlePosition = sliderSize - handleSize;
  }

  if (this.type === 'vertical') {
    this.handleStyle.height = handleSize + 'px';
    this.handleStyle.top = handlePosition + 'px';

  }
  else { //horizontal
    this.handleStyle.width = handleSize + 'px';
    this.handleStyle.left = handlePosition + 'px';
  }

  this.sliderStyle.display = 'block';
};

WalkontableScrollbar.prototype.destroy = function () {
  clearInterval(this.dragdealer.interval);
};

///

var WalkontableVerticalScrollbar = function (instance) {
  this.instance = instance;
  this.type = 'vertical';
  this.init();
};

WalkontableVerticalScrollbar.prototype = new WalkontableScrollbar();

WalkontableVerticalScrollbar.prototype.scrollTo = function (cell) {
  this.instance.update('offsetRow', cell);
};

WalkontableVerticalScrollbar.prototype.readSettings = function () {
  this.scrollMode = this.instance.getSetting('scrollV');
  this.offset = this.instance.getSetting('offsetRow');
  this.total = this.instance.getSetting('totalRows');
  this.visibleCount = this.instance.wtTable.rowStrategy.countVisible();
  if(this.visibleCount > 1 && this.instance.wtTable.rowStrategy.isLastIncomplete()) {
    this.visibleCount--;
  }
  this.handlePosition = parseInt(this.handleStyle.top, 10);
  this.sliderSize = parseInt(this.sliderStyle.height, 10);
  this.fixedCount = this.instance.getSetting('fixedRowsTop');
};

///

var WalkontableHorizontalScrollbar = function (instance) {
  this.instance = instance;
  this.type = 'horizontal';
  this.init();
};

WalkontableHorizontalScrollbar.prototype = new WalkontableScrollbar();

WalkontableHorizontalScrollbar.prototype.scrollTo = function (cell) {
  this.instance.update('offsetColumn', cell);
};

WalkontableHorizontalScrollbar.prototype.readSettings = function () {
  this.scrollMode = this.instance.getSetting('scrollH');
  this.offset = this.instance.getSetting('offsetColumn');
  this.total = this.instance.getSetting('totalColumns');
  this.visibleCount = this.instance.wtTable.columnStrategy.countVisible();
  if(this.visibleCount > 1 && this.instance.wtTable.columnStrategy.isLastIncomplete()) {
    this.visibleCount--;
  }
  this.handlePosition = parseInt(this.handleStyle.left, 10);
  this.sliderSize = parseInt(this.sliderStyle.width, 10);
  this.fixedCount = this.instance.getSetting('fixedColumnsLeft');
};

WalkontableHorizontalScrollbar.prototype.getHandleSizeRatio = function (viewportCount, totalCount) {
  if (!totalCount || viewportCount > totalCount || viewportCount == totalCount) {
    return 1;
  }
  return viewportCount / totalCount;
};
function WalkontableCornerScrollbarNative(instance) {
  this.instance = instance;
  this.init();
  this.clone = this.makeClone('corner');
}

WalkontableCornerScrollbarNative.prototype = new WalkontableOverlay();

WalkontableCornerScrollbarNative.prototype.resetFixedPosition = function () {
  if (!this.instance.wtTable.holder.parentNode) {
    return; //removed from DOM
  }
  var elem = this.clone.wtTable.holder.parentNode;

  var box;
  if (this.scrollHandler === window) {
    box = this.instance.wtTable.hider.getBoundingClientRect();
    var top = Math.ceil(box.top, 10);
    var bottom = Math.ceil(box.bottom, 10);

    if (top < 0 && bottom > 0) {
      elem.style.top = '0';
    }
    else {
      elem.style.top = top + 'px';
    }

    var left = Math.ceil(box.left, 10);
    var right = Math.ceil(box.right, 10);

    if (left < 0 && right > 0) {
      elem.style.left = '0';
    }
    else {
      elem.style.left = left + 'px';
    }
  }
  else {
    box = this.scrollHandler.getBoundingClientRect();
    elem.style.top = Math.ceil(box.top, 10) + 'px';
    elem.style.left = Math.ceil(box.left, 10) + 'px';
  }

  elem.style.width = WalkontableDom.prototype.outerWidth(this.clone.wtTable.TABLE) + 4 + 'px';
  elem.style.height = WalkontableDom.prototype.outerHeight(this.clone.wtTable.TABLE) + 4 + 'px';
};

WalkontableCornerScrollbarNative.prototype.prepare = function () {
};

WalkontableCornerScrollbarNative.prototype.refresh = function (selectionsOnly) {
  this.measureBefore = 0;
  this.measureAfter = 0;
  this.clone && this.clone.draw(selectionsOnly);
};

WalkontableCornerScrollbarNative.prototype.getScrollPosition = function () {
};

WalkontableCornerScrollbarNative.prototype.getLastCell = function () {
};

WalkontableCornerScrollbarNative.prototype.applyToDOM = function () {
};

WalkontableCornerScrollbarNative.prototype.scrollTo = function () {
};

WalkontableCornerScrollbarNative.prototype.readWindowSize = function () {
};

WalkontableCornerScrollbarNative.prototype.readSettings = function () {
};
function WalkontableHorizontalScrollbarNative(instance) {
  this.instance = instance;
  this.type = 'horizontal';
  this.cellSize = 50;
  this.init();
  this.clone = this.makeClone('left');
}

WalkontableHorizontalScrollbarNative.prototype = new WalkontableOverlay();

//resetFixedPosition (in future merge it with this.refresh?)
WalkontableHorizontalScrollbarNative.prototype.resetFixedPosition = function () {
  if (!this.instance.wtTable.holder.parentNode) {
    return; //removed from DOM
  }
  var elem = this.clone.wtTable.holder.parentNode;

  var box;
  if (this.scrollHandler === window) {
    box = this.instance.wtTable.hider.getBoundingClientRect();
    var left = Math.ceil(box.left, 10);
    var right = Math.ceil(box.right, 10);

    if (left < 0 && right > 0) {
      elem.style.left = '0';
    }
    else {
      elem.style.left = left + 'px';
    }
  }
  else {
    box = this.scrollHandler.getBoundingClientRect();
    elem.style.top = Math.ceil(box.top, 10) + 'px';
    elem.style.left = Math.ceil(box.left, 10) + 'px';
  }
};

//react on movement of the other dimension scrollbar (in future merge it with this.refresh?)
WalkontableHorizontalScrollbarNative.prototype.react = function () {
  if (!this.instance.wtTable.holder.parentNode) {
    return; //removed from DOM
  }
  var overlayContainer = this.clone.wtTable.holder.parentNode;
  if (this.instance.wtScrollbars.vertical.scrollHandler === window) {
    var box = this.instance.wtTable.hider.getBoundingClientRect();
    overlayContainer.style.top = Math.ceil(box.top, 10) + 'px';
    overlayContainer.style.height = WalkontableDom.prototype.outerHeight(this.clone.wtTable.TABLE) + 'px';
  }
  else {
    this.clone.wtTable.holder.style.top = -(this.instance.wtScrollbars.vertical.windowScrollPosition - this.instance.wtScrollbars.vertical.measureBefore) + 'px';
    overlayContainer.style.height = this.instance.wtViewport.getWorkspaceHeight() + 'px'
  }
  overlayContainer.style.width = WalkontableDom.prototype.outerWidth(this.clone.wtTable.TABLE) + 4 + 'px'; //4 is for the box shadow
};

WalkontableHorizontalScrollbarNative.prototype.prepare = function () {
};

WalkontableHorizontalScrollbarNative.prototype.refresh = function (selectionsOnly) {
  this.measureBefore = 0;
  this.measureAfter = 0;
  this.clone && this.clone.draw(selectionsOnly);
};

WalkontableHorizontalScrollbarNative.prototype.getScrollPosition = function () {
  if (this.scrollHandler === window) {
    return this.scrollHandler.scrollX;
  }
  else {
    return this.scrollHandler.scrollLeft;
  }
};

WalkontableHorizontalScrollbarNative.prototype.setScrollPosition = function (pos) {
    this.scrollHandler.scrollLeft = pos;
};

WalkontableHorizontalScrollbarNative.prototype.onScroll = function () {
  WalkontableOverlay.prototype.onScroll.apply(this, arguments);

  this.instance.getSetting('onScrollHorizontally');
};

WalkontableHorizontalScrollbarNative.prototype.getLastCell = function () {
  return this.instance.wtTable.getLastVisibleColumn();
};

//applyToDOM (in future merge it with this.refresh?)
WalkontableHorizontalScrollbarNative.prototype.applyToDOM = function () {
  this.fixedContainer.style.paddingLeft = this.measureBefore + 'px';
  this.fixedContainer.style.paddingRight = this.measureAfter + 'px';
};

WalkontableHorizontalScrollbarNative.prototype.scrollTo = function (cell) {
  this.$scrollHandler.scrollLeft(this.tableParentOffset + cell * this.cellSize);
};

//readWindowSize (in future merge it with this.prepare?)
WalkontableHorizontalScrollbarNative.prototype.readWindowSize = function () {
  if (this.scrollHandler === window) {
    this.windowSize = document.documentElement.clientWidth;
    this.tableParentOffset = this.instance.wtTable.holderOffset.left;
  }
  else {
    this.windowSize = WalkontableDom.prototype.outerWidth(this.scrollHandler);
    this.tableParentOffset = 0;
  }
  this.windowScrollPosition = this.getScrollPosition();
};

//readSettings (in future merge it with this.prepare?)
WalkontableHorizontalScrollbarNative.prototype.readSettings = function () {
  this.offset = this.instance.getSetting('offsetColumn');
  this.total = this.instance.getSetting('totalColumns');
};
function WalkontableVerticalScrollbarNative(instance) {
  this.instance = instance;
  this.type = 'vertical';
  this.cellSize = 23;
  this.init();
  this.clone = this.makeClone('top');
}

WalkontableVerticalScrollbarNative.prototype = new WalkontableOverlay();

//resetFixedPosition (in future merge it with this.refresh?)
WalkontableVerticalScrollbarNative.prototype.resetFixedPosition = function () {
  if (!this.instance.wtTable.holder.parentNode) {
    return; //removed from DOM
  }
  var elem = this.clone.wtTable.holder.parentNode;

  var box;
  if (this.scrollHandler === window) {
    box = this.instance.wtTable.hider.getBoundingClientRect();
    var top = Math.ceil(box.top, 10);
    var bottom = Math.ceil(box.bottom, 10);

    if (top < 0 && bottom > 0) {
      elem.style.top = '0';
    }
    else {
      elem.style.top = top + 'px';
    }
  }
  else {
    box = this.instance.wtScrollbars.horizontal.scrollHandler.getBoundingClientRect();
    elem.style.top = Math.ceil(box.top, 10) + 'px';
    elem.style.left = Math.ceil(box.left, 10) + 'px';
  }

  if (this.instance.wtScrollbars.horizontal.scrollHandler === window) {
    elem.style.width = this.instance.wtViewport.getWorkspaceActualWidth() + 'px';
  }
  else {
    elem.style.width = WalkontableDom.prototype.outerWidth(this.instance.wtTable.holder.parentNode) + 'px';
  }

  elem.style.height = WalkontableDom.prototype.outerHeight(this.clone.wtTable.TABLE) + 4 + 'px';
};

//react on movement of the other dimension scrollbar (in future merge it with this.refresh?)
WalkontableVerticalScrollbarNative.prototype.react = function () {
  if (!this.instance.wtTable.holder.parentNode) {
    return; //removed from DOM
  }
  if (this.instance.wtScrollbars.horizontal.scrollHandler !== window) {
    var elem = this.clone.wtTable.holder.parentNode;
    elem.firstChild.style.left = -this.instance.wtScrollbars.horizontal.windowScrollPosition + 'px';
  }
};

WalkontableVerticalScrollbarNative.prototype.getScrollPosition = function () {
  if (this.scrollHandler === window) {
    return this.scrollHandler.scrollY;
  }
  else {
    return this.scrollHandler.scrollTop;
  }
};

WalkontableVerticalScrollbarNative.prototype.setScrollPosition = function (pos) {
  this.scrollHandler.scrollTop = pos;
};

WalkontableVerticalScrollbarNative.prototype.onScroll = function (forcePosition) {
  WalkontableOverlay.prototype.onScroll.apply(this, arguments);

  var scrollDelta;
  var newOffset = 0;

  if (1 == 1 || this.windowScrollPosition > this.tableParentOffset) {
    scrollDelta = this.windowScrollPosition - this.tableParentOffset;

    partialOffset = 0;
    if (scrollDelta > 0) {
      var sum = 0;
      var last;
      for (var i = 0; i < this.total; i++) {
        last = this.instance.getSetting('rowHeight', i);
        sum += last;
        if (sum > scrollDelta) {
          break;
        }
      }

      if (this.offset > 0) {
        partialOffset = (sum - scrollDelta);
      }
      newOffset = i;
      newOffset = Math.min(newOffset, this.total);
    }
  }

  this.curOuts = newOffset > this.maxOuts ? this.maxOuts : newOffset;
  newOffset -= this.curOuts;

  this.instance.update('offsetRow', newOffset);
  this.readSettings(); //read new offset
  this.instance.draw();

  this.instance.getSetting('onScrollVertically');
};

WalkontableVerticalScrollbarNative.prototype.getLastCell = function () {
  return this.instance.getSetting('offsetRow') + this.instance.wtTable.tbodyChildrenLength - 1;
};

var partialOffset = 0;

WalkontableVerticalScrollbarNative.prototype.sumCellSizes = function (from, length) {
  var sum = 0;
  while (from < length) {
    sum += this.instance.getSetting('rowHeight', from);
    from++;
  }
  return sum;
};

//applyToDOM (in future merge it with this.refresh?)
WalkontableVerticalScrollbarNative.prototype.applyToDOM = function () {
  var headerSize = this.instance.wtViewport.getColumnHeaderHeight();
  this.fixedContainer.style.height = headerSize + this.sumCellSizes(0, this.total) + 4 + 'px'; //+4 is needed, otherwise vertical scroll appears in Chrome (window scroll mode) - maybe because of fill handle in last row or because of box shadow
  this.fixed.style.top = this.measureBefore + 'px';
  this.fixed.style.bottom = '';
};

WalkontableVerticalScrollbarNative.prototype.scrollTo = function (cell) {
  var newY = this.tableParentOffset + cell * this.cellSize;
  this.$scrollHandler.scrollTop(newY);
  this.onScroll(newY);
};

//readWindowSize (in future merge it with this.prepare?)
WalkontableVerticalScrollbarNative.prototype.readWindowSize = function () {
  if (this.scrollHandler === window) {
    this.windowSize = document.documentElement.clientHeight;
    this.tableParentOffset = this.instance.wtTable.holderOffset.top;
  }
  else {
    //this.windowSize = WalkontableDom.prototype.outerHeight(this.scrollHandler);
    this.windowSize = this.scrollHandler.clientHeight; //returns height without DIV scrollbar
    this.tableParentOffset = 0;
  }
  this.windowScrollPosition = this.getScrollPosition();
};

//readSettings (in future merge it with this.prepare?)
WalkontableVerticalScrollbarNative.prototype.readSettings = function () {
  this.offset = this.instance.getSetting('offsetRow');
  this.total = this.instance.getSetting('totalRows');
};
function WalkontableScrollbars(instance) {
  this.instance = instance;
  if (instance.getSetting('nativeScrollbars')) {
    instance.update('scrollbarWidth', instance.wtDom.getScrollbarWidth());
    instance.update('scrollbarHeight', instance.wtDom.getScrollbarWidth());
    this.vertical = new WalkontableVerticalScrollbarNative(instance);
    this.horizontal = new WalkontableHorizontalScrollbarNative(instance);
    this.corner = new WalkontableCornerScrollbarNative(instance);
    if (instance.getSetting('debug')) {
      this.debug = new WalkontableDebugOverlay(instance);
    }
    this.registerListeners();
  }
  else {
    this.vertical = new WalkontableVerticalScrollbar(instance);
    this.horizontal = new WalkontableHorizontalScrollbar(instance);
  }
}

WalkontableScrollbars.prototype.registerListeners = function () {
  var that = this;

  var oldVerticalScrollPosition
    , oldHorizontalScrollPosition
    , oldBoxTop
    , oldBoxLeft
    , oldBoxWidth
    , oldBoxHeight;

  function refreshAll() {
    if (!that.instance.wtTable.holder.parentNode) {
      //Walkontable was detached from DOM, but this handler was not removed
      that.destroy();
      return;
    }

    that.vertical.windowScrollPosition = that.vertical.getScrollPosition();
    that.horizontal.windowScrollPosition = that.horizontal.getScrollPosition();
    that.box = that.instance.wtTable.hider.getBoundingClientRect();

    if((that.box.width !== oldBoxWidth || that.box.height !== oldBoxHeight) && that.instance.rowHeightCache) {
      //that.instance.rowHeightCache.length = 0; //at this point the cached row heights may be invalid, but it is better not to reset the cache, which could cause scrollbar jumping when there are multiline cells outside of the rendered part of the table
      oldBoxWidth = that.box.width;
      oldBoxHeight = that.box.height;
      that.instance.draw();
    }

    if (that.vertical.windowScrollPosition !== oldVerticalScrollPosition || that.horizontal.windowScrollPosition !== oldHorizontalScrollPosition || that.box.top !== oldBoxTop || that.box.left !== oldBoxLeft) {
      that.vertical.onScroll();
      that.horizontal.onScroll(); //it's done here to make sure that all onScroll's are executed before changing styles

      that.vertical.react();
      that.horizontal.react(); //it's done here to make sure that all onScroll's are executed before changing styles

      oldVerticalScrollPosition = that.vertical.windowScrollPosition;
      oldHorizontalScrollPosition = that.horizontal.windowScrollPosition;
      oldBoxTop = that.box.top;
      oldBoxLeft = that.box.left;
    }
  }

  var $window = $(window);
  this.vertical.$scrollHandler.on('scroll.' + this.instance.guid, refreshAll);
  if (this.vertical.scrollHandler !== this.horizontal.scrollHandler) {
    this.horizontal.$scrollHandler.on('scroll.' + this.instance.guid, refreshAll);
  }

  if (this.vertical.scrollHandler !== window && this.horizontal.scrollHandler !== window) {
    $window.on('scroll.' + this.instance.guid, refreshAll);
  }
  $window.on('load.' + this.instance.guid, refreshAll);
  $window.on('resize.' + this.instance.guid, refreshAll);
  $(document).on('ready.' + this.instance.guid, refreshAll);
  setInterval(refreshAll, 100); //Marcin - only idea I have to reposition scrollbars on CSS change of the container (container was moved using some styles after page was loaded)
};

WalkontableScrollbars.prototype.destroy = function () {
  this.vertical && this.vertical.destroy();
  this.horizontal && this.horizontal.destroy();
};

WalkontableScrollbars.prototype.refresh = function (selectionsOnly) {
  this.horizontal && this.horizontal.readSettings();
  this.vertical && this.vertical.readSettings();
  this.horizontal && this.horizontal.prepare();
  this.vertical && this.vertical.prepare();
  this.horizontal && this.horizontal.refresh(selectionsOnly);
  this.vertical && this.vertical.refresh(selectionsOnly);
  this.corner && this.corner.refresh(selectionsOnly);
  this.debug && this.debug.refresh(selectionsOnly);
};
function WalkontableSelection(instance, settings) {
  this.instance = instance;
  this.settings = settings;
  this.selected = [];
  if (settings.border) {
    this.border = new WalkontableBorder(instance, settings);
  }
}

WalkontableSelection.prototype.add = function (coords) {
  this.selected.push(coords);
};

WalkontableSelection.prototype.clear = function () {
  this.selected.length = 0; //http://jsperf.com/clear-arrayxxx
};

/**
 * Returns the top left (TL) and bottom right (BR) selection coordinates
 * @returns {Object}
 */
WalkontableSelection.prototype.getCorners = function () {
  var minRow
    , minColumn
    , maxRow
    , maxColumn
    , i
    , ilen = this.selected.length;

  if (ilen > 0) {
    minRow = maxRow = this.selected[0][0];
    minColumn = maxColumn = this.selected[0][1];

    if (ilen > 1) {
      for (i = 1; i < ilen; i++) {
        if (this.selected[i][0] < minRow) {
          minRow = this.selected[i][0];
        }
        else if (this.selected[i][0] > maxRow) {
          maxRow = this.selected[i][0];
        }

        if (this.selected[i][1] < minColumn) {
          minColumn = this.selected[i][1];
        }
        else if (this.selected[i][1] > maxColumn) {
          maxColumn = this.selected[i][1];
        }
      }
    }
  }

  return [minRow, minColumn, maxRow, maxColumn];
};

WalkontableSelection.prototype.draw = function () {
  var corners, r, c, source_r, source_c;

  var visibleRows = this.instance.wtTable.rowStrategy.countVisible()
    , visibleColumns = this.instance.wtTable.columnStrategy.countVisible();

  if (this.selected.length) {
    corners = this.getCorners();

    for (r = 0; r < visibleRows; r++) {
      for (c = 0; c < visibleColumns; c++) {
        source_r = this.instance.wtTable.rowFilter.visibleToSource(r);
        source_c = this.instance.wtTable.columnFilter.visibleToSource(c);

        if (source_r >= corners[0] && source_r <= corners[2] && source_c >= corners[1] && source_c <= corners[3]) {
          //selected cell
          this.instance.wtTable.currentCellCache.add(r, c, this.settings.className);
        }
        else if (source_r >= corners[0] && source_r <= corners[2]) {
          //selection is in this row
          this.instance.wtTable.currentCellCache.add(r, c, this.settings.highlightRowClassName);
        }
        else if (source_c >= corners[1] && source_c <= corners[3]) {
          //selection is in this column
          this.instance.wtTable.currentCellCache.add(r, c, this.settings.highlightColumnClassName);
        }
      }
    }

    this.border && this.border.appear(corners); //warning! border.appear modifies corners!
  }
  else {
    this.border && this.border.disappear();
  }
};

function WalkontableSettings(instance, settings) {
  var that = this;
  this.instance = instance;

  //default settings. void 0 means it is required, null means it can be empty
  this.defaults = {
    table: void 0,
    debug: false, //shows WalkontableDebugOverlay

    //presentation mode
    scrollH: 'auto', //values: scroll (always show scrollbar), auto (show scrollbar if table does not fit in the container), none (never show scrollbar)
    scrollV: 'auto', //values: see above
    nativeScrollbars: false, //values: false (dragdealer), true (native)
    stretchH: 'hybrid', //values: hybrid, all, last, none
    currentRowClassName: null,
    currentColumnClassName: null,

    //data source
    data: void 0,
    offsetRow: 0,
    offsetColumn: 0,
    fixedColumnsLeft: 0,
    fixedRowsTop: 0,
    rowHeaders: function () {
      return []
    }, //this must be array of functions: [function (row, TH) {}]
    columnHeaders: function () {
      return []
    }, //this must be array of functions: [function (column, TH) {}]
    totalRows: void 0,
    totalColumns: void 0,
    width: null,
    height: null,
    cellRenderer: function (row, column, TD) {
      var cellData = that.getSetting('data', row, column);
      that.instance.wtDom.fastInnerText(TD, cellData === void 0 || cellData === null ? '' : cellData);
    },
    columnWidth: 50,
    selections: null,
    hideBorderOnMouseDownOver: false,

    //callbacks
    onCellMouseDown: null,
    onCellMouseOver: null,
//    onCellMouseOut: null,
    onCellDblClick: null,
    onCellCornerMouseDown: null,
    onCellCornerDblClick: null,
    beforeDraw: null,
    onDraw: null,
    onScrollVertically: null,
    onScrollHorizontally: null,

    //constants
    scrollbarWidth: 10,
    scrollbarHeight: 10
  };

  //reference to settings
  this.settings = {};
  for (var i in this.defaults) {
    if (this.defaults.hasOwnProperty(i)) {
      if (settings[i] !== void 0) {
        this.settings[i] = settings[i];
      }
      else if (this.defaults[i] === void 0) {
        throw new Error('A required setting "' + i + '" was not provided');
      }
      else {
        this.settings[i] = this.defaults[i];
      }
    }
  }
}

/**
 * generic methods
 */

WalkontableSettings.prototype.update = function (settings, value) {
  if (value === void 0) { //settings is object
    for (var i in settings) {
      if (settings.hasOwnProperty(i)) {
        this.settings[i] = settings[i];
      }
    }
  }
  else { //if value is defined then settings is the key
    this.settings[settings] = value;
  }
  return this.instance;
};

WalkontableSettings.prototype.getSetting = function (key, param1, param2, param3) {
  if (this[key]) {
    return this[key](param1, param2, param3);
  }
  else {
    return this._getSetting(key, param1, param2, param3);
  }
};

WalkontableSettings.prototype._getSetting = function (key, param1, param2, param3) {
  if (typeof this.settings[key] === 'function') {
    return this.settings[key](param1, param2, param3);
  }
  else if (param1 !== void 0 && Object.prototype.toString.call(this.settings[key]) === '[object Array]') {
    return this.settings[key][param1];
  }
  else {
    return this.settings[key];
  }
};

WalkontableSettings.prototype.has = function (key) {
  return !!this.settings[key]
};

/**
 * specific methods
 */
WalkontableSettings.prototype.rowHeight = function (row, TD) {
  if (!this.instance.rowHeightCache) {
    this.instance.rowHeightCache = []; //hack. This cache is being invalidated in WOT core.js
  }
  if (this.instance.rowHeightCache[row] === void 0) {
    var size = 23; //guess
    if (TD) {
      size = this.instance.wtDom.outerHeight(TD); //measure
      this.instance.rowHeightCache[row] = size; //cache only something we measured
    }
    return size;
  }
  else {
    return this.instance.rowHeightCache[row];
  }
};
function WalkontableTable(instance, table) {
  //reference to instance
  this.instance = instance;
  this.TABLE = table;
  this.wtDom = this.instance.wtDom;
  this.wtDom.removeTextNodes(this.TABLE);

  //wtSpreader
  var parent = this.TABLE.parentNode;
  if (!parent || parent.nodeType !== 1 || !this.wtDom.hasClass(parent, 'wtHolder')) {
    var spreader = document.createElement('DIV');
    spreader.className = 'wtSpreader';
    if (parent) {
      parent.insertBefore(spreader, this.TABLE); //if TABLE is detached (e.g. in Jasmine test), it has no parentNode so we cannot attach holder to it
    }
    spreader.appendChild(this.TABLE);
  }
  this.spreader = this.TABLE.parentNode;

  //wtHider
  parent = this.spreader.parentNode;
  if (!parent || parent.nodeType !== 1 || !this.wtDom.hasClass(parent, 'wtHolder')) {
    var hider = document.createElement('DIV');
    hider.className = 'wtHider';
    if (parent) {
      parent.insertBefore(hider, this.spreader); //if TABLE is detached (e.g. in Jasmine test), it has no parentNode so we cannot attach holder to it
    }
    hider.appendChild(this.spreader);
  }
  this.hider = this.spreader.parentNode;
  this.hiderStyle = this.hider.style;
  this.hiderStyle.position = 'relative';

  //wtHolder
  parent = this.hider.parentNode;
  if (!parent || parent.nodeType !== 1 || !this.wtDom.hasClass(parent, 'wtHolder')) {
    var holder = document.createElement('DIV');
    holder.style.position = 'relative';
    holder.className = 'wtHolder';
    if (parent) {
      parent.insertBefore(holder, this.hider); //if TABLE is detached (e.g. in Jasmine test), it has no parentNode so we cannot attach holder to it
    }
    holder.appendChild(this.hider);
  }
  this.holder = this.hider.parentNode;

  //bootstrap from settings
  this.TBODY = this.TABLE.getElementsByTagName('TBODY')[0];
  if (!this.TBODY) {
    this.TBODY = document.createElement('TBODY');
    this.TABLE.appendChild(this.TBODY);
  }
  this.THEAD = this.TABLE.getElementsByTagName('THEAD')[0];
  if (!this.THEAD) {
    this.THEAD = document.createElement('THEAD');
    this.TABLE.insertBefore(this.THEAD, this.TBODY);
  }
  this.COLGROUP = this.TABLE.getElementsByTagName('COLGROUP')[0];
  if (!this.COLGROUP) {
    this.COLGROUP = document.createElement('COLGROUP');
    this.TABLE.insertBefore(this.COLGROUP, this.THEAD);
  }

  if (this.instance.getSetting('columnHeaders').length) {
    if (!this.THEAD.childNodes.length) {
      var TR = document.createElement('TR');
      this.THEAD.appendChild(TR);
    }
  }

  this.colgroupChildrenLength = this.COLGROUP.childNodes.length;
  this.theadChildrenLength = this.THEAD.firstChild ? this.THEAD.firstChild.childNodes.length : 0;
  this.tbodyChildrenLength = this.TBODY.childNodes.length;

  this.oldCellCache = new WalkontableClassNameCache();
  this.currentCellCache = new WalkontableClassNameCache();

  this.rowFilter = new WalkontableRowFilter();
  this.columnFilter = new WalkontableColumnFilter();

  this.verticalRenderReverse = false;
}

WalkontableTable.prototype.refreshHiderDimensions = function () {
  var height = this.instance.wtViewport.getWorkspaceHeight();
  var width = this.instance.wtViewport.getWorkspaceWidth();

  var spreaderStyle = this.spreader.style;

  if ((height !== Infinity || width !== Infinity) && !this.instance.getSetting('nativeScrollbars')) {
    if (height === Infinity) {
      height = this.instance.wtViewport.getWorkspaceActualHeight();
    }
    if (width === Infinity) {
      width = this.instance.wtViewport.getWorkspaceActualWidth();
    }

    this.hiderStyle.overflow = 'hidden';

    spreaderStyle.position = 'absolute';
    spreaderStyle.top = '0';
    spreaderStyle.left = '0';

    if (!this.instance.getSetting('nativeScrollbars')) {
      spreaderStyle.height = '4000px';
      spreaderStyle.width = '4000px';
    }

    if (height < 0) { //this happens with WalkontableOverlay and causes "Invalid argument" error in IE8
      height = 0;
    }

    this.hiderStyle.height = height + 'px';
    this.hiderStyle.width = width + 'px';
  }
  else {
    spreaderStyle.position = 'relative';
    spreaderStyle.width = 'auto';
    spreaderStyle.height = 'auto';
  }
};

WalkontableTable.prototype.refreshStretching = function () {
  if (this.instance.cloneSource) {
    return;
  }

  var instance = this.instance
    , stretchH = instance.getSetting('stretchH')
    , totalRows = instance.getSetting('totalRows')
    , totalColumns = instance.getSetting('totalColumns')
    , offsetColumn = instance.getSetting('offsetColumn');

  var containerWidthFn = function (cacheWidth) {
    var viewportWidth = that.instance.wtViewport.getViewportWidth(cacheWidth);
    if (viewportWidth < cacheWidth && that.instance.getSetting('nativeScrollbars')) {
      return Infinity; //disable stretching when viewport is bigger than sum of the cell widths
    }
    return viewportWidth;
  };

  var that = this;

  var columnWidthFn = function (i) {
    var source_c = that.columnFilter.visibleToSource(i);
    if (source_c < totalColumns) {
      return instance.getSetting('columnWidth', source_c);
    }
  };

  if (stretchH === 'hybrid') {
    if (offsetColumn > 0) {
      stretchH = 'last';
    }
    else {
      stretchH = 'none';
    }
  }

  var containerHeightFn = function (cacheHeight) {
    if (that.instance.getSetting('nativeScrollbars')) {
      if (that.instance.cloneOverlay instanceof WalkontableDebugOverlay) {
        return Infinity;
      }
      else {
        return 2 * that.instance.wtViewport.getViewportHeight(cacheHeight);
      }
    }
    return that.instance.wtViewport.getViewportHeight(cacheHeight);
  };

  var rowHeightFn = function (i, TD) {
    if (that.instance.getSetting('nativeScrollbars')) {
      return 20;
    }
    var source_r = that.rowFilter.visibleToSource(i);
    if (source_r < totalRows) {
      if (that.verticalRenderReverse && i === 0) {
        return that.instance.getSetting('rowHeight', source_r, TD) - 1;
      }
      else {
        return that.instance.getSetting('rowHeight', source_r, TD);
      }
    }
  };

  this.columnStrategy = new WalkontableColumnStrategy(instance, containerWidthFn, columnWidthFn, stretchH);
  this.rowStrategy = new WalkontableRowStrategy(instance, containerHeightFn, rowHeightFn);
};

WalkontableTable.prototype.adjustAvailableNodes = function () {
  var displayTds
    , rowHeaders = this.instance.getSetting('rowHeaders')
    , displayThs = rowHeaders.length
    , columnHeaders = this.instance.getSetting('columnHeaders')
    , TR
    , TD
    , c;

  //adjust COLGROUP
  while (this.colgroupChildrenLength < displayThs) {
    this.COLGROUP.appendChild(document.createElement('COL'));
    this.colgroupChildrenLength++;
  }

  this.refreshStretching(); //actually it is wrong position because it assumes rowHeader would be always 50px wide (because we measure before it is filled with text). TODO: debug
  if (this.instance.cloneSource && (this.instance.cloneOverlay instanceof WalkontableHorizontalScrollbarNative || this.instance.cloneOverlay instanceof WalkontableCornerScrollbarNative)) {
    displayTds = this.instance.getSetting('fixedColumnsLeft');
  }
  else {
    displayTds = this.columnStrategy.cellCount;
  }

  //adjust COLGROUP
  while (this.colgroupChildrenLength < displayTds + displayThs) {
    this.COLGROUP.appendChild(document.createElement('COL'));
    this.colgroupChildrenLength++;
  }
  while (this.colgroupChildrenLength > displayTds + displayThs) {
    this.COLGROUP.removeChild(this.COLGROUP.lastChild);
    this.colgroupChildrenLength--;
  }

  //adjust THEAD
  TR = this.THEAD.firstChild;
  if (columnHeaders.length) {
    if (!TR) {
      TR = document.createElement('TR');
      this.THEAD.appendChild(TR);
    }

    this.theadChildrenLength = TR.childNodes.length;
    while (this.theadChildrenLength < displayTds + displayThs) {
      TR.appendChild(document.createElement('TH'));
      this.theadChildrenLength++;
    }
    while (this.theadChildrenLength > displayTds + displayThs) {
      TR.removeChild(TR.lastChild);
      this.theadChildrenLength--;
    }
  }
  else if (TR) {
    this.wtDom.empty(TR);
  }

  //draw COLGROUP
  for (c = 0; c < this.colgroupChildrenLength; c++) {
    if (c < displayThs) {
      this.wtDom.addClass(this.COLGROUP.childNodes[c], 'rowHeader');
    }
    else {
      this.wtDom.removeClass(this.COLGROUP.childNodes[c], 'rowHeader');
    }
  }

  //draw THEAD
  if (columnHeaders.length) {
    TR = this.THEAD.firstChild;
    if (displayThs) {
      TD = TR.firstChild; //actually it is TH but let's reuse single variable
      for (c = 0; c < displayThs; c++) {
        rowHeaders[c](-displayThs + c, TD);
        TD = TD.nextSibling;
      }
    }
  }

  for (c = 0; c < displayTds; c++) {
    if (columnHeaders.length) {
      columnHeaders[0](this.columnFilter.visibleToSource(c), TR.childNodes[displayThs + c]);
    }
  }
};

WalkontableTable.prototype.adjustColumns = function (TR, desiredCount) {
  var count = TR.childNodes.length;
  while (count < desiredCount) {
    var TD = document.createElement('TD');
    TR.appendChild(TD);
    count++;
  }
  while (count > desiredCount) {
    TR.removeChild(TR.lastChild);
    count--;
  }
};

WalkontableTable.prototype.draw = function (selectionsOnly) {
  if (this.instance.getSetting('nativeScrollbars')) {
    this.verticalRenderReverse = false; //this is only supported in dragdealer mode, not in native
  }

  this.rowFilter.readSettings(this.instance);
  this.columnFilter.readSettings(this.instance);

  if (!selectionsOnly) {
    if (this.instance.getSetting('nativeScrollbars')) {
      if (this.instance.cloneSource) {
        this.tableOffset = this.instance.cloneSource.wtTable.tableOffset;
      }
      else {
        this.holderOffset = this.wtDom.offset(this.holder);
        this.tableOffset = this.wtDom.offset(this.TABLE);
        this.instance.wtScrollbars.vertical.readWindowSize();
        this.instance.wtScrollbars.horizontal.readWindowSize();
        this.instance.wtViewport.resetSettings();
      }
    }
    else {
      this.tableOffset = this.wtDom.offset(this.TABLE);
      this.instance.wtViewport.resetSettings();
    }
    this._doDraw();
  }
  else {
    this.instance.wtScrollbars && this.instance.wtScrollbars.refresh(true);
  }

  this.refreshPositions(selectionsOnly);

  if (!selectionsOnly) {
    if (this.instance.getSetting('nativeScrollbars')) {
      if (!this.instance.cloneSource) {
        this.instance.wtScrollbars.vertical.resetFixedPosition();
        this.instance.wtScrollbars.horizontal.resetFixedPosition();
        this.instance.wtScrollbars.corner.resetFixedPosition();
        this.instance.wtScrollbars.debug && this.instance.wtScrollbars.debug.resetFixedPosition();
      }
    }
  }

  this.instance.drawn = true;
  return this;
};

WalkontableTable.prototype._doDraw = function () {
  var r = 0
    , source_r
    , c
    , source_c
    , offsetRow = this.instance.getSetting('offsetRow')
    , totalRows = this.instance.getSetting('totalRows')
    , totalColumns = this.instance.getSetting('totalColumns')
    , displayTds
    , rowHeaders = this.instance.getSetting('rowHeaders')
    , displayThs = rowHeaders.length
    , TR
    , TD
    , TH
    , adjusted = false
    , workspaceWidth
    , mustBeInViewport
    , res;

  if (this.verticalRenderReverse) {
    mustBeInViewport = offsetRow;
  }

  var noPartial = false;
  if (this.verticalRenderReverse) {
    if (offsetRow === totalRows - this.rowFilter.fixedCount - 1) {
      noPartial = true;
    }
    else {
      this.instance.update('offsetRow', offsetRow + 1); //if we are scrolling reverse
      this.rowFilter.readSettings(this.instance);
    }
  }

  if (this.instance.cloneSource) {
    this.columnStrategy = this.instance.cloneSource.wtTable.columnStrategy;
    this.rowStrategy = this.instance.cloneSource.wtTable.rowStrategy;
  }

  //draw TBODY
  if (totalColumns > 0) {
    source_r = this.rowFilter.visibleToSource(r);

    var fixedRowsTop = this.instance.getSetting('fixedRowsTop');
    var cloneLimit;
    if (this.instance.cloneSource) { //must be run after adjustAvailableNodes because otherwise this.rowStrategy is not yet defined
      if (this.instance.cloneOverlay instanceof WalkontableVerticalScrollbarNative || this.instance.cloneOverlay instanceof WalkontableCornerScrollbarNative) {
        cloneLimit = fixedRowsTop;
      }
      else if (this.instance.cloneOverlay instanceof WalkontableHorizontalScrollbarNative) {
        cloneLimit = this.rowStrategy.countVisible();
      }
      //else if WalkontableDebugOverlay do nothing. No cloneLimit means render ALL rows
    }

    this.adjustAvailableNodes();
    adjusted = true;

    if (this.instance.cloneSource && (this.instance.cloneOverlay instanceof WalkontableHorizontalScrollbarNative || this.instance.cloneOverlay instanceof WalkontableCornerScrollbarNative)) {
      displayTds = this.instance.getSetting('fixedColumnsLeft');
    }
    else {
      displayTds = this.columnStrategy.cellCount;
    }

    if (!this.instance.cloneSource) {
      workspaceWidth = this.instance.wtViewport.getWorkspaceWidth();
      this.columnStrategy.stretch();
    }

    for (c = 0; c < displayTds; c++) {
      this.COLGROUP.childNodes[c + displayThs].style.width = this.columnStrategy.getSize(c) + 'px';
    }

    while (source_r < totalRows && source_r >= 0) {
      if (r > 1000) {
        throw new Error('Security brake: Too much TRs. Please define height for your table, which will enforce scrollbars.');
      }

      if (cloneLimit !== void 0 && r === cloneLimit) {
        break; //we have as much rows as needed for this clone
      }

      if (r >= this.tbodyChildrenLength || (this.verticalRenderReverse && r >= this.rowFilter.fixedCount)) {
        TR = document.createElement('TR');
        for (c = 0; c < displayThs; c++) {
          TR.appendChild(document.createElement('TH'));
        }
        if (this.verticalRenderReverse && r >= this.rowFilter.fixedCount) {
          this.TBODY.insertBefore(TR, this.TBODY.childNodes[this.rowFilter.fixedCount] || this.TBODY.firstChild);
        }
        else {
          this.TBODY.appendChild(TR);
        }
        this.tbodyChildrenLength++;
      }
      else if (r === 0) {
        TR = this.TBODY.firstChild;
      }
      else {
        TR = TR.nextSibling; //http://jsperf.com/nextsibling-vs-indexed-childnodes
      }

      //TH
      TH = TR.firstChild;
      for (c = 0; c < displayThs; c++) {

        //If the number of row headers increased we need to replace TD with TH
        if (TH.nodeName == 'TD') {
          TD = TH;
          TH = document.createElement('TH');
          TR.insertBefore(TH, TD);
          TR.removeChild(TD);
        }

        rowHeaders[c](source_r, TH); //actually TH
        TH = TH.nextSibling; //http://jsperf.com/nextsibling-vs-indexed-childnodes
      }

      this.adjustColumns(TR, displayTds + displayThs);

      for (c = 0; c < displayTds; c++) {
        source_c = this.columnFilter.visibleToSource(c);
        if (c === 0) {
          TD = TR.childNodes[this.columnFilter.sourceColumnToVisibleRowHeadedColumn(source_c)];
        }
        else {
          TD = TD.nextSibling; //http://jsperf.com/nextsibling-vs-indexed-childnodes
        }

        //If the number of headers has been reduced, we need to replace excess TH with TD
        if (TD.nodeName == 'TH') {
          TH = TD;
          TD = document.createElement('TD');
          TR.insertBefore(TD, TH);
          TR.removeChild(TH);
        }

        TD.className = '';
        TD.removeAttribute('style');
        this.instance.getSetting('cellRenderer', source_r, source_c, TD);
      }

      offsetRow = this.instance.getSetting('offsetRow'); //refresh the value

      //after last column is rendered, check if last cell is fully displayed
      if (this.verticalRenderReverse && noPartial) {
        if (-this.wtDom.outerHeight(TR.firstChild) < this.rowStrategy.remainingSize) {
          this.TBODY.removeChild(TR);
          this.instance.update('offsetRow', offsetRow + 1);
          this.tbodyChildrenLength--;
          this.rowFilter.readSettings(this.instance);
          break;

        }
        else if (!this.instance.cloneSource) {
          res = this.rowStrategy.add(r, TD, this.verticalRenderReverse);
          if (res === false) {
            this.rowStrategy.removeOutstanding();
          }
        }
      }
      else if (!this.instance.cloneSource) {
        res = this.rowStrategy.add(r, TD, this.verticalRenderReverse);

        if (res === false) {
          if (!this.instance.getSetting('nativeScrollbars')) {
            this.rowStrategy.removeOutstanding();
          }
        }

        if (this.rowStrategy.isLastIncomplete()) {
          if (this.verticalRenderReverse && !this.isRowInViewport(mustBeInViewport)) {
            //we failed because one of the cells was by far too large. Recover by rendering from top
            this.verticalRenderReverse = false;
            this.instance.update('offsetRow', mustBeInViewport);
            this.draw();
            return;
          }
          break;
        }
      }

      if (this.instance.getSetting('nativeScrollbars')) {
        if (this.instance.cloneSource) {
          TR.style.height = this.instance.getSetting('rowHeight', source_r) + 'px'; //if I have 2 fixed columns with one-line content and the 3rd column has a multiline content, this is the way to make sure that the overlay will has same row height
        }
        else {
          this.instance.getSetting('rowHeight', source_r, TD); //this trick saves rowHeight in rowHeightCache. It is then read in WalkontableVerticalScrollbarNative.prototype.sumCellSizes and reset in Walkontable constructor
        }
      }

      if (this.verticalRenderReverse && r >= this.rowFilter.fixedCount) {
        if (offsetRow === 0) {
          break;
        }
        this.instance.update('offsetRow', offsetRow - 1);
        this.rowFilter.readSettings(this.instance);
      }
      else {
        r++;
      }

      source_r = this.rowFilter.visibleToSource(r);
    }
  }

  if (!adjusted) {
    this.adjustAvailableNodes();
  }

  if (!(this.instance.cloneOverlay instanceof WalkontableDebugOverlay)) {
    r = this.rowStrategy.countVisible();
    while (this.tbodyChildrenLength > r) {
      this.TBODY.removeChild(this.TBODY.lastChild);
      this.tbodyChildrenLength--;
    }
  }

  this.instance.wtScrollbars && this.instance.wtScrollbars.refresh(false);

  if (!this.instance.cloneSource) {
    if (workspaceWidth !== this.instance.wtViewport.getWorkspaceWidth()) {
      //workspace width changed though to shown/hidden vertical scrollbar. Let's reapply stretching
      this.columnStrategy.stretch();
      for (c = 0; c < this.columnStrategy.cellCount; c++) {
        this.COLGROUP.childNodes[c + displayThs].style.width = this.columnStrategy.getSize(c) + 'px';
      }
    }
  }

  this.verticalRenderReverse = false;
};

WalkontableTable.prototype.refreshPositions = function (selectionsOnly) {
  this.refreshHiderDimensions();
  this.refreshSelections(selectionsOnly);
};

WalkontableTable.prototype.refreshSelections = function (selectionsOnly) {
  var vr
    , r
    , vc
    , c
    , s
    , slen
    , classNames = []
    , visibleRows = this.rowStrategy.countVisible()
    , visibleColumns = this.columnStrategy.countVisible();

  this.oldCellCache = this.currentCellCache;
  this.currentCellCache = new WalkontableClassNameCache();

  if (this.instance.selections) {
    for (r in this.instance.selections) {
      if (this.instance.selections.hasOwnProperty(r)) {
        this.instance.selections[r].draw();
        if (this.instance.selections[r].settings.className) {
          classNames.push(this.instance.selections[r].settings.className);
        }
        if (this.instance.selections[r].settings.highlightRowClassName) {
          classNames.push(this.instance.selections[r].settings.highlightRowClassName);
        }
        if (this.instance.selections[r].settings.highlightColumnClassName) {
          classNames.push(this.instance.selections[r].settings.highlightColumnClassName);
        }
      }
    }
  }

  slen = classNames.length;

  for (vr = 0; vr < visibleRows; vr++) {
    for (vc = 0; vc < visibleColumns; vc++) {
      r = this.rowFilter.visibleToSource(vr);
      c = this.columnFilter.visibleToSource(vc);
      for (s = 0; s < slen; s++) {
        if (this.currentCellCache.test(vr, vc, classNames[s])) {
          this.wtDom.addClass(this.getCell([r, c]), classNames[s]);
        }
        else if (selectionsOnly && this.oldCellCache.test(vr, vc, classNames[s])) {
          this.wtDom.removeClass(this.getCell([r, c]), classNames[s]);
        }
      }
    }
  }
};

/**
 * getCell
 * @param {Array} coords
 * @return {Object} HTMLElement on success or {Number} one of the exit codes on error:
 *  -1 row before viewport
 *  -2 row after viewport
 *  -3 column before viewport
 *  -4 column after viewport
 *
 */
WalkontableTable.prototype.getCell = function (coords) {
  if (this.isRowBeforeViewport(coords[0])) {
    return -1; //row before viewport
  }
  else if (this.isRowAfterViewport(coords[0])) {
    return -2; //row after viewport
  }
  else {
    if (this.isColumnBeforeViewport(coords[1])) {
      return -3; //column before viewport
    }
    else if (this.isColumnAfterViewport(coords[1])) {
      return -4; //column after viewport
    }
    else {
      return this.TBODY.childNodes[this.rowFilter.sourceToVisible(coords[0])].childNodes[this.columnFilter.sourceColumnToVisibleRowHeadedColumn(coords[1])];
    }
  }
};

WalkontableTable.prototype.getCoords = function (TD) {
  return [
    this.rowFilter.visibleToSource(this.wtDom.index(TD.parentNode)),
    this.columnFilter.visibleRowHeadedColumnToSourceColumn(TD.cellIndex)
  ];
};

//returns -1 if no row is visible
WalkontableTable.prototype.getLastVisibleRow = function () {
  return this.rowFilter.visibleToSource(this.rowStrategy.cellCount - 1);
};

//returns -1 if no column is visible
WalkontableTable.prototype.getLastVisibleColumn = function () {
  return this.columnFilter.visibleToSource(this.columnStrategy.cellCount - 1);
};

WalkontableTable.prototype.isRowBeforeViewport = function (r) {
  return (this.rowFilter.sourceToVisible(r) < this.rowFilter.fixedCount && r >= this.rowFilter.fixedCount);
};

WalkontableTable.prototype.isRowAfterViewport = function (r) {
  return (r > this.getLastVisibleRow());
};

WalkontableTable.prototype.isColumnBeforeViewport = function (c) {
  return (this.columnFilter.sourceToVisible(c) < this.columnFilter.fixedCount && c >= this.columnFilter.fixedCount);
};

WalkontableTable.prototype.isColumnAfterViewport = function (c) {
  return (c > this.getLastVisibleColumn());
};

WalkontableTable.prototype.isRowInViewport = function (r) {
  return (!this.isRowBeforeViewport(r) && !this.isRowAfterViewport(r));
};

WalkontableTable.prototype.isColumnInViewport = function (c) {
  return (!this.isColumnBeforeViewport(c) && !this.isColumnAfterViewport(c));
};

WalkontableTable.prototype.isLastRowFullyVisible = function () {
  return (this.getLastVisibleRow() === this.instance.getSetting('totalRows') - 1 && !this.rowStrategy.isLastIncomplete());
};

WalkontableTable.prototype.isLastColumnFullyVisible = function () {
  return (this.getLastVisibleColumn() === this.instance.getSetting('totalColumns') - 1 && !this.columnStrategy.isLastIncomplete());
};

function WalkontableViewport(instance) {
  this.instance = instance;
  this.resetSettings();

  if (this.instance.getSetting('nativeScrollbars')) {
    var that = this;
    $(window).on('resize', function () {
      that.clientHeight = that.getWorkspaceHeight();
    });
  }
}

/*WalkontableViewport.prototype.isInSightVertical = function () {
  //is table outside viewport bottom edge
  if (tableTop > windowHeight + scrollTop) {
    return -1;
  }

  //is table outside viewport top edge
  else if (scrollTop > tableTop + tableFakeHeight) {
    return -2;
  }

  //table is in viewport but how much exactly?
  else {

  }
};*/

//used by scrollbar
WalkontableViewport.prototype.getWorkspaceHeight = function (proposedHeight) {
  if (this.instance.getSetting('nativeScrollbars')) {
    return this.instance.wtScrollbars.vertical.windowSize;
  }

  var height = this.instance.getSetting('height');

  if (height === Infinity || height === void 0 || height === null || height < 1) {
    if (this.instance.wtScrollbars.vertical instanceof WalkontableOverlay) {
      height = this.instance.wtScrollbars.vertical.availableSize();
    }
    else {
      height = Infinity;
    }
  }

  if (height !== Infinity) {
    if (proposedHeight >= height) {
      height -= this.instance.getSetting('scrollbarHeight');
    }
    else if (this.instance.wtScrollbars.horizontal.visible) {
      height -= this.instance.getSetting('scrollbarHeight');
    }
  }

  return height;
};

WalkontableViewport.prototype.getWorkspaceWidth = function (proposedWidth) {
  var width = this.instance.getSetting('width');

  if (width === Infinity || width === void 0 || width === null || width < 1) {
    if (this.instance.wtScrollbars.horizontal instanceof WalkontableOverlay) {
      width = this.instance.wtScrollbars.horizontal.availableSize();
    }
    else {
      width = Infinity;
    }
  }

  if (width !== Infinity) {
    if (proposedWidth >= width) {
      width -= this.instance.getSetting('scrollbarWidth');
    }
    else if (this.instance.wtScrollbars.vertical.visible) {
      width -= this.instance.getSetting('scrollbarWidth');
    }
  }
  return width;
};

WalkontableViewport.prototype.getWorkspaceActualHeight = function () {
  return this.instance.wtDom.outerHeight(this.instance.wtTable.TABLE);
};

WalkontableViewport.prototype.getWorkspaceActualWidth = function () {
  return this.instance.wtDom.outerWidth(this.instance.wtTable.TABLE) || this.instance.wtDom.outerWidth(this.instance.wtTable.TBODY) || this.instance.wtDom.outerWidth(this.instance.wtTable.THEAD); //IE8 reports 0 as <table> offsetWidth;
};

WalkontableViewport.prototype.getColumnHeaderHeight = function () {
  if (isNaN(this.columnHeaderHeight)) {
    var cellOffset = this.instance.wtDom.offset(this.instance.wtTable.TBODY)
      , tableOffset = this.instance.wtTable.tableOffset;
    this.columnHeaderHeight = cellOffset.top - tableOffset.top;
  }
  return this.columnHeaderHeight;
};

WalkontableViewport.prototype.getViewportHeight = function (proposedHeight) {
  var containerHeight = this.getWorkspaceHeight(proposedHeight);

  if (containerHeight === Infinity) {
    return containerHeight;
  }

  var columnHeaderHeight = this.getColumnHeaderHeight();
  if (columnHeaderHeight > 0) {
    return containerHeight - columnHeaderHeight;
  }
  else {
    return containerHeight;
  }
};

WalkontableViewport.prototype.getRowHeaderWidth = function () {
  if (this.instance.cloneSource) {
    return this.instance.cloneSource.wtViewport.getRowHeaderWidth();
  }
  if (isNaN(this.rowHeaderWidth)) {
    var rowHeaders = this.instance.getSetting('rowHeaders');
    if (rowHeaders.length) {
      var TH = this.instance.wtTable.TABLE.querySelector('TH');
      this.rowHeaderWidth = 0;
      for (var i = 0, ilen = rowHeaders.length; i < ilen; i++) {
        if (TH) {
          this.rowHeaderWidth += this.instance.wtDom.outerWidth(TH);
          TH = TH.nextSibling;
        }
        else {
          this.rowHeaderWidth += 50; //yes this is a cheat but it worked like that before, just taking assumption from CSS instead of measuring. TODO: proper fix
        }
      }
    }
    else {
      this.rowHeaderWidth = 0;
    }
  }
  return this.rowHeaderWidth;
};

WalkontableViewport.prototype.getViewportWidth = function (proposedWidth) {
  var containerWidth = this.getWorkspaceWidth(proposedWidth);

  if (containerWidth === Infinity) {
    return containerWidth;
  }

  var rowHeaderWidth = this.getRowHeaderWidth();
  if (rowHeaderWidth > 0) {
    return containerWidth - rowHeaderWidth;
  }
  else {
    return containerWidth;
  }
};

WalkontableViewport.prototype.resetSettings = function () {
  this.rowHeaderWidth = NaN;
  this.columnHeaderHeight = NaN;
};
function WalkontableWheel(instance) {
  if (instance.getSetting('nativeScrollbars')) {
    return;
  }

  //spreader === instance.wtTable.TABLE.parentNode
  $(instance.wtTable.spreader).on('mousewheel', function (event, delta, deltaX, deltaY) {
    if (!deltaX && !deltaY && delta) { //we are in IE8, see https://github.com/brandonaaron/jquery-mousewheel/issues/53
      deltaY = delta;
    }

    if (!deltaX && !deltaY) { //this happens in IE8 test case
      return;
    }

    if (deltaY > 0 && instance.getSetting('offsetRow') === 0) {
      return; //attempt to scroll up when it's already showing first row
    }
    else if (deltaY < 0 && instance.wtTable.isLastRowFullyVisible()) {
      return; //attempt to scroll down when it's already showing last row
    }
    else if (deltaX < 0 && instance.getSetting('offsetColumn') === 0) {
      return; //attempt to scroll left when it's already showing first column
    }
    else if (deltaX > 0 && instance.wtTable.isLastColumnFullyVisible()) {
      return; //attempt to scroll right when it's already showing last column
    }

    //now we are sure we really want to scroll
    clearTimeout(instance.wheelTimeout);
    instance.wheelTimeout = setTimeout(function () { //timeout is needed because with fast-wheel scrolling mousewheel event comes dozen times per second
      if (deltaY) {
        //ceil is needed because jquery-mousewheel reports fractional mousewheel deltas on touchpad scroll
        //see http://stackoverflow.com/questions/5527601/normalizing-mousewheel-speed-across-browsers
        if (instance.wtScrollbars.vertical.visible) { // if we see scrollbar
          instance.scrollVertical(-Math.ceil(deltaY)).draw();
        }
      }
      else if (deltaX) {
        if (instance.wtScrollbars.horizontal.visible) { // if we see scrollbar
          instance.scrollHorizontal(Math.ceil(deltaX)).draw();
        }
      }
    }, 0);

    event.preventDefault();
  });
}
/**
 * Dragdealer JS v0.9.5 - patched by Walkontable at lines 66, 309-310, 339-340
 * http://code.ovidiu.ch/dragdealer-js
 *
 * Copyright (c) 2010, Ovidiu Chereches
 * MIT License
 * http://legal.ovidiu.ch/licenses/MIT
 */

/* Cursor */

var Cursor =
{
	x: 0, y: 0,
	init: function()
	{
		this.setEvent('mouse');
		this.setEvent('touch');
	},
	setEvent: function(type)
	{
		var moveHandler = document['on' + type + 'move'] || function(){};
		document['on' + type + 'move'] = function(e)
		{
			moveHandler(e);
			Cursor.refresh(e);
		}
	},
	refresh: function(e)
	{
		if(!e)
		{
			e = window.event;
		}
		if(e.type == 'mousemove')
		{
			this.set(e);
		}
		else if(e.touches)
		{
			this.set(e.touches[0]);
		}
	},
	set: function(e)
	{
		if(e.pageX || e.pageY)
		{
			this.x = e.pageX;
			this.y = e.pageY;
		}
		else if(document.body && (e.clientX || e.clientY)) //need to check whether body exists, because of IE8 issue (#1084)
		{
			this.x = e.clientX + document.body.scrollLeft + document.documentElement.scrollLeft;
			this.y = e.clientY + document.body.scrollTop + document.documentElement.scrollTop;
		}
	}
};
Cursor.init();

/* Position */

var Position =
{
	get: function(obj)
	{
		var curtop = 0, curleft = 0; //Walkontable patch. Original (var curleft = curtop = 0;) created curtop in global scope
		if(obj.offsetParent)
		{
			do
			{
				curleft += obj.offsetLeft;
				curtop += obj.offsetTop;
			}
			while((obj = obj.offsetParent));
		}
		return [curleft, curtop];
	}
};

/* Dragdealer */

var Dragdealer = function(wrapper, options)
{
	if(typeof(wrapper) == 'string')
	{
		wrapper = document.getElementById(wrapper);
	}
	if(!wrapper)
	{
		return;
	}
	var handle = wrapper.getElementsByTagName('div')[0];
	if(!handle || handle.className.search(/(^|\s)handle(\s|$)/) == -1)
	{
		return;
	}
	this.init(wrapper, handle, options || {});
	this.setup();
};
Dragdealer.prototype =
{
	init: function(wrapper, handle, options)
	{
		this.wrapper = wrapper;
		this.handle = handle;
		this.options = options;
		
		this.disabled = this.getOption('disabled', false);
		this.horizontal = this.getOption('horizontal', true);
		this.vertical = this.getOption('vertical', false);
		this.slide = this.getOption('slide', true);
		this.steps = this.getOption('steps', 0);
		this.snap = this.getOption('snap', false);
		this.loose = this.getOption('loose', false);
		this.speed = this.getOption('speed', 10) / 100;
		this.xPrecision = this.getOption('xPrecision', 0);
		this.yPrecision = this.getOption('yPrecision', 0);
		
		this.callback = options.callback || null;
		this.animationCallback = options.animationCallback || null;
		
		this.bounds = {
			left: options.left || 0, right: -(options.right || 0),
			top: options.top || 0, bottom: -(options.bottom || 0),
			x0: 0, x1: 0, xRange: 0,
			y0: 0, y1: 0, yRange: 0
		};
		this.value = {
			prev: [-1, -1],
			current: [options.x || 0, options.y || 0],
			target: [options.x || 0, options.y || 0]
		};
		this.offset = {
			wrapper: [0, 0],
			mouse: [0, 0],
			prev: [-999999, -999999],
			current: [0, 0],
			target: [0, 0]
		};
		this.change = [0, 0];
		
		this.activity = false;
		this.dragging = false;
		this.tapping = false;
	},
	getOption: function(name, defaultValue)
	{
		return this.options[name] !== undefined ? this.options[name] : defaultValue;
	},
	setup: function()
	{
		this.setWrapperOffset();
		this.setBoundsPadding();
		this.setBounds();
		this.setSteps();
		
		this.addListeners();
	},
	setWrapperOffset: function()
	{
		this.offset.wrapper = Position.get(this.wrapper);
	},
	setBoundsPadding: function()
	{
		if(!this.bounds.left && !this.bounds.right)
		{
			this.bounds.left = Position.get(this.handle)[0] - this.offset.wrapper[0];
			this.bounds.right = -this.bounds.left;
		}
		if(!this.bounds.top && !this.bounds.bottom)
		{
			this.bounds.top = Position.get(this.handle)[1] - this.offset.wrapper[1];
			this.bounds.bottom = -this.bounds.top;
		}
	},
	setBounds: function()
	{
		this.bounds.x0 = this.bounds.left;
		this.bounds.x1 = this.wrapper.offsetWidth + this.bounds.right;
		this.bounds.xRange = (this.bounds.x1 - this.bounds.x0) - this.handle.offsetWidth;
		
		this.bounds.y0 = this.bounds.top;
		this.bounds.y1 = this.wrapper.offsetHeight + this.bounds.bottom;
		this.bounds.yRange = (this.bounds.y1 - this.bounds.y0) - this.handle.offsetHeight;
		
		this.bounds.xStep = 1 / (this.xPrecision || Math.max(this.wrapper.offsetWidth, this.handle.offsetWidth));
		this.bounds.yStep = 1 / (this.yPrecision || Math.max(this.wrapper.offsetHeight, this.handle.offsetHeight));
	},
	setSteps: function()
	{
		if(this.steps > 1)
		{
			this.stepRatios = [];
			for(var i = 0; i <= this.steps - 1; i++)
			{
				this.stepRatios[i] = i / (this.steps - 1);
			}
		}
	},
	addListeners: function()
	{
		var self = this;
		
		this.wrapper.onselectstart = function()
		{
			return false;
		}
		this.handle.onmousedown = this.handle.ontouchstart = function(e)
		{
			self.handleDownHandler(e);
		};
		this.wrapper.onmousedown = this.wrapper.ontouchstart = function(e)
		{
			self.wrapperDownHandler(e);
		};
		var mouseUpHandler = document.onmouseup || function(){};
		document.onmouseup = function(e)
		{
			mouseUpHandler(e);
			self.documentUpHandler(e);
		};
		var touchEndHandler = document.ontouchend || function(){};
		document.ontouchend = function(e)
		{
			touchEndHandler(e);
			self.documentUpHandler(e);
		};
		var resizeHandler = window.onresize || function(){};
		window.onresize = function(e)
		{
			resizeHandler(e);
			self.documentResizeHandler(e);
		};
		this.wrapper.onmousemove = function(e)
		{
			self.activity = true;
		}
		this.wrapper.onclick = function(e)
		{
			return !self.activity;
		}
		
		this.interval = setInterval(function(){ self.animate() }, 25);
		self.animate(false, true);
	},
	handleDownHandler: function(e)
	{
		this.activity = false;
		Cursor.refresh(e);
		
		this.preventDefaults(e, true);
		this.startDrag();
	},
	wrapperDownHandler: function(e)
	{
		Cursor.refresh(e);
		
		this.preventDefaults(e, true);
		this.startTap();
	},
	documentUpHandler: function(e)
	{
		this.stopDrag();
		this.stopTap();
	},
	documentResizeHandler: function(e)
	{
		this.setWrapperOffset();
		this.setBounds();
		
		this.update();
	},
	enable: function()
	{
		this.disabled = false;
		this.handle.className = this.handle.className.replace(/\s?disabled/g, '');
	},
	disable: function()
	{
		this.disabled = true;
		this.handle.className += ' disabled';
	},
	setStep: function(x, y, snap)
	{
		this.setValue(
			this.steps && x > 1 ? (x - 1) / (this.steps - 1) : 0,
			this.steps && y > 1 ? (y - 1) / (this.steps - 1) : 0,
			snap
		);
	},
	setValue: function(x, y, snap)
	{
		this.setTargetValue([x, y || 0]);
		if(snap)
		{
			this.groupCopy(this.value.current, this.value.target);
		}
	},
	startTap: function(target)
	{
		if(this.disabled)
		{
			return;
		}
		this.tapping = true;

		this.setWrapperOffset();
		this.setBounds();

		if(target === undefined)
		{
			target = [
				Cursor.x - this.offset.wrapper[0] - (this.handle.offsetWidth / 2),
				Cursor.y - this.offset.wrapper[1] - (this.handle.offsetHeight / 2)
			];
		}
		this.setTargetOffset(target);
	},
	stopTap: function()
	{
		if(this.disabled || !this.tapping)
		{
			return;
		}
		this.tapping = false;
		
		this.setTargetValue(this.value.current);
		this.result();
	},
	startDrag: function()
	{
		if(this.disabled)
		{
			return;
		}

		this.setWrapperOffset();
		this.setBounds();

		this.offset.mouse = [
			Cursor.x - Position.get(this.handle)[0],
			Cursor.y - Position.get(this.handle)[1]
		];
		
		this.dragging = true;
	},
	stopDrag: function()
	{
		if(this.disabled || !this.dragging)
		{
			return;
		}
		this.dragging = false;
		
		var target = this.groupClone(this.value.current);
		if(this.slide)
		{
			var ratioChange = this.change;
			target[0] += ratioChange[0] * 4;
			target[1] += ratioChange[1] * 4;
		}
		this.setTargetValue(target);
		this.result();
	},
	feedback: function()
	{
		var value = this.value.current;
		if(this.snap && this.steps > 1)
		{
			value = this.getClosestSteps(value);
		}
		if(!this.groupCompare(value, this.value.prev))
		{
			if(typeof(this.animationCallback) == 'function')
			{
				this.animationCallback(value[0], value[1]);
			}
			this.groupCopy(this.value.prev, value);
		}
	},
	result: function()
	{
		if(typeof(this.callback) == 'function')
		{
			this.callback(this.value.target[0], this.value.target[1]);
		}
	},
	animate: function(direct, first)
	{
		if(direct && !this.dragging)
		{
			return;
		}
		if(this.dragging)
		{
			var prevTarget = this.groupClone(this.value.target);
			
			var offset = [
				Cursor.x - this.offset.wrapper[0] - this.offset.mouse[0],
				Cursor.y - this.offset.wrapper[1] - this.offset.mouse[1]
			];
			this.setTargetOffset(offset, this.loose);
			
			this.change = [
				this.value.target[0] - prevTarget[0],
				this.value.target[1] - prevTarget[1]
			];
		}
		if(this.dragging || first)
		{
			this.groupCopy(this.value.current, this.value.target);
		}
		if(this.dragging || this.glide() || first)
		{
			this.update();
			this.feedback();
		}
	},
	glide: function()
	{
		var diff = [
			this.value.target[0] - this.value.current[0],
			this.value.target[1] - this.value.current[1]
		];
		if(!diff[0] && !diff[1])
		{
			return false;
		}
		if(Math.abs(diff[0]) > this.bounds.xStep || Math.abs(diff[1]) > this.bounds.yStep)
		{
			this.value.current[0] += diff[0] * this.speed;
			this.value.current[1] += diff[1] * this.speed;
		}
		else
		{
			this.groupCopy(this.value.current, this.value.target);
		}
		return true;
	},
	update: function()
	{
		if(!this.snap)
		{
			this.offset.current = this.getOffsetsByRatios(this.value.current);
		}
		else
		{
			this.offset.current = this.getOffsetsByRatios(
				this.getClosestSteps(this.value.current)
			);
		}
		this.show();
	},
	show: function()
	{
		if(!this.groupCompare(this.offset.current, this.offset.prev))
		{
			if(this.horizontal)
			{
				this.handle.style.left = String(this.offset.current[0]) + 'px';
			}
			if(this.vertical)
			{
				this.handle.style.top = String(this.offset.current[1]) + 'px';
			}
			this.groupCopy(this.offset.prev, this.offset.current);
		}
	},
	setTargetValue: function(value, loose)
	{
		var target = loose ? this.getLooseValue(value) : this.getProperValue(value);
		
		this.groupCopy(this.value.target, target);
		this.offset.target = this.getOffsetsByRatios(target);
	},
	setTargetOffset: function(offset, loose)
	{
		var value = this.getRatiosByOffsets(offset);
		var target = loose ? this.getLooseValue(value) : this.getProperValue(value);
		
		this.groupCopy(this.value.target, target);
		this.offset.target = this.getOffsetsByRatios(target);
	},
	getLooseValue: function(value)
	{
		var proper = this.getProperValue(value);
		return [
			proper[0] + ((value[0] - proper[0]) / 4),
			proper[1] + ((value[1] - proper[1]) / 4)
		];
	},
	getProperValue: function(value)
	{
		var proper = this.groupClone(value);

		proper[0] = Math.max(proper[0], 0);
		proper[1] = Math.max(proper[1], 0);
		proper[0] = Math.min(proper[0], 1);
		proper[1] = Math.min(proper[1], 1);

		if((!this.dragging && !this.tapping) || this.snap)
		{
			if(this.steps > 1)
			{
				proper = this.getClosestSteps(proper);
			}
		}
		return proper;
	},
	getRatiosByOffsets: function(group)
	{
		return [
			this.getRatioByOffset(group[0], this.bounds.xRange, this.bounds.x0),
			this.getRatioByOffset(group[1], this.bounds.yRange, this.bounds.y0)
		];
	},
	getRatioByOffset: function(offset, range, padding)
	{
		return range ? (offset - padding) / range : 0;
	},
	getOffsetsByRatios: function(group)
	{
		return [
			this.getOffsetByRatio(group[0], this.bounds.xRange, this.bounds.x0),
			this.getOffsetByRatio(group[1], this.bounds.yRange, this.bounds.y0)
		];
	},
	getOffsetByRatio: function(ratio, range, padding)
	{
		return Math.round(ratio * range) + padding;
	},
	getClosestSteps: function(group)
	{
		return [
			this.getClosestStep(group[0]),
			this.getClosestStep(group[1])
		];
	},
	getClosestStep: function(value)
	{
		var k = 0;
		var min = 1;
		for(var i = 0; i <= this.steps - 1; i++)
		{
			if(Math.abs(this.stepRatios[i] - value) < min)
			{
				min = Math.abs(this.stepRatios[i] - value);
				k = i;
			}
		}
		return this.stepRatios[k];
	},
	groupCompare: function(a, b)
	{
		return a[0] == b[0] && a[1] == b[1];
	},
	groupCopy: function(a, b)
	{
		a[0] = b[0];
		a[1] = b[1];
	},
	groupClone: function(a)
	{
		return [a[0], a[1]];
	},
	preventDefaults: function(e, selection)
	{
		if(!e)
		{
			e = window.event;
		}
		if(e.preventDefault)
		{
			e.preventDefault();
		}
		e.returnValue = false;
		
		if(selection && document.selection)
		{
			document.selection.empty();
		}
	},
	cancelEvent: function(e)
	{
		if(!e)
		{
			e = window.event;
		}
		if(e.stopPropagation)
		{
			e.stopPropagation();
		}
		e.cancelBubble = true;
	}
};

/*! Copyright (c) 2013 Brandon Aaron (http://brandonaaron.net)
 * Licensed under the MIT License (LICENSE.txt).
 *
 * Thanks to: http://adomas.org/javascript-mouse-wheel/ for some pointers.
 * Thanks to: Mathias Bank(http://www.mathias-bank.de) for a scope bug fix.
 * Thanks to: Seamus Leahy for adding deltaX and deltaY
 *
 * Version: 3.1.3
 *
 * Requires: 1.2.2+
 */

(function (factory) {
    if ( typeof define === 'function' && define.amd ) {
        // AMD. Register as an anonymous module.
        define(['jquery'], factory);
    } else if (typeof exports === 'object') {
        // Node/CommonJS style for Browserify
        module.exports = factory;
    } else {
        // Browser globals
        factory(jQuery);
    }
}(function ($) {

    var toFix = ['wheel', 'mousewheel', 'DOMMouseScroll', 'MozMousePixelScroll'];
    var toBind = 'onwheel' in document || document.documentMode >= 9 ? ['wheel'] : ['mousewheel', 'DomMouseScroll', 'MozMousePixelScroll'];
    var lowestDelta, lowestDeltaXY;

    if ( $.event.fixHooks ) {
        for ( var i = toFix.length; i; ) {
            $.event.fixHooks[ toFix[--i] ] = $.event.mouseHooks;
        }
    }

    $.event.special.mousewheel = {
        setup: function() {
            if ( this.addEventListener ) {
                for ( var i = toBind.length; i; ) {
                    this.addEventListener( toBind[--i], handler, false );
                }
            } else {
                this.onmousewheel = handler;
            }
        },

        teardown: function() {
            if ( this.removeEventListener ) {
                for ( var i = toBind.length; i; ) {
                    this.removeEventListener( toBind[--i], handler, false );
                }
            } else {
                this.onmousewheel = null;
            }
        }
    };

    $.fn.extend({
        mousewheel: function(fn) {
            return fn ? this.bind("mousewheel", fn) : this.trigger("mousewheel");
        },

        unmousewheel: function(fn) {
            return this.unbind("mousewheel", fn);
        }
    });


    function handler(event) {
        var orgEvent = event || window.event,
            args = [].slice.call(arguments, 1),
            delta = 0,
            deltaX = 0,
            deltaY = 0,
            absDelta = 0,
            absDeltaXY = 0,
            fn;
        event = $.event.fix(orgEvent);
        event.type = "mousewheel";

        // Old school scrollwheel delta
        if ( orgEvent.wheelDelta ) { delta = orgEvent.wheelDelta; }
        if ( orgEvent.detail )     { delta = orgEvent.detail * -1; }

        // New school wheel delta (wheel event)
        if ( orgEvent.deltaY ) {
            deltaY = orgEvent.deltaY * -1;
            delta  = deltaY;
        }
        if ( orgEvent.deltaX ) {
            deltaX = orgEvent.deltaX;
            delta  = deltaX * -1;
        }

        // Webkit
        if ( orgEvent.wheelDeltaY !== undefined ) { deltaY = orgEvent.wheelDeltaY; }
        if ( orgEvent.wheelDeltaX !== undefined ) { deltaX = orgEvent.wheelDeltaX * -1; }

        // Look for lowest delta to normalize the delta values
        absDelta = Math.abs(delta);
        if ( !lowestDelta || absDelta < lowestDelta ) { lowestDelta = absDelta; }
        absDeltaXY = Math.max(Math.abs(deltaY), Math.abs(deltaX));
        if ( !lowestDeltaXY || absDeltaXY < lowestDeltaXY ) { lowestDeltaXY = absDeltaXY; }

        // Get a whole value for the deltas
        fn = delta > 0 ? 'floor' : 'ceil';
        delta  = Math[fn](delta / lowestDelta);
        deltaX = Math[fn](deltaX / lowestDeltaXY);
        deltaY = Math[fn](deltaY / lowestDeltaXY);

        // Add event and delta to the front of the arguments
        args.unshift(event, delta, deltaX, deltaY);

        return ($.event.dispatch || $.event.handle).apply(this, args);
    }

}));

})(jQuery, window, Handsontable);
// numeral.js
// version : 1.4.7
// author : Adam Draper
// license : MIT
// http://adamwdraper.github.com/Numeral-js/

(function () {

    /************************************
        Constants
    ************************************/

    var numeral,
        VERSION = '1.4.7',
        // internal storage for language config files
        languages = {},
        currentLanguage = 'en',
        zeroFormat = null,
        // check for nodeJS
        hasModule = (typeof module !== 'undefined' && module.exports);


    /************************************
        Constructors
    ************************************/


    // Numeral prototype object
    function Numeral (number) {
        this._n = number;
    }

    /**
     * Implementation of toFixed() that treats floats more like decimals
     *
     * Fixes binary rounding issues (eg. (0.615).toFixed(2) === '0.61') that present
     * problems for accounting- and finance-related software.
     */
    function toFixed (value, precision, optionals) {
        var power = Math.pow(10, precision),
            output;

        // Multiply up by precision, round accurately, then divide and use native toFixed():
        output = (Math.round(value * power) / power).toFixed(precision);

        if (optionals) {
            var optionalsRegExp = new RegExp('0{1,' + optionals + '}$');
            output = output.replace(optionalsRegExp, '');
        }

        return output;
    }

    /************************************
        Formatting
    ************************************/

    // determine what type of formatting we need to do
    function formatNumeral (n, format) {
        var output;

        // figure out what kind of format we are dealing with
        if (format.indexOf('$') > -1) { // currency!!!!!
            output = formatCurrency(n, format);
        } else if (format.indexOf('%') > -1) { // percentage
            output = formatPercentage(n, format);
        } else if (format.indexOf(':') > -1) { // time
            output = formatTime(n, format);
        } else { // plain ol' numbers or bytes
            output = formatNumber(n, format);
        }

        // return string
        return output;
    }

    // revert to number
    function unformatNumeral (n, string) {
        if (string.indexOf(':') > -1) {
            n._n = unformatTime(string);
        } else {
            if (string === zeroFormat) {
                n._n = 0;
            } else {
                var stringOriginal = string;
                if (languages[currentLanguage].delimiters.decimal !== '.') {
                    string = string.replace(/\./g,'').replace(languages[currentLanguage].delimiters.decimal, '.');
                }

                // see if abbreviations are there so that we can multiply to the correct number
                var thousandRegExp = new RegExp(languages[currentLanguage].abbreviations.thousand + '(?:\\)|(\\' + languages[currentLanguage].currency.symbol + ')?(?:\\))?)?$'),
                    millionRegExp = new RegExp(languages[currentLanguage].abbreviations.million + '(?:\\)|(\\' + languages[currentLanguage].currency.symbol + ')?(?:\\))?)?$'),
                    billionRegExp = new RegExp(languages[currentLanguage].abbreviations.billion + '(?:\\)|(\\' + languages[currentLanguage].currency.symbol + ')?(?:\\))?)?$'),
                    trillionRegExp = new RegExp(languages[currentLanguage].abbreviations.trillion + '(?:\\)|(\\' + languages[currentLanguage].currency.symbol + ')?(?:\\))?)?$');

                // see if bytes are there so that we can multiply to the correct number
                var prefixes = ['KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'],
                    bytesMultiplier = false;

                for (var power = 0; power <= prefixes.length; power++) {
                    bytesMultiplier = (string.indexOf(prefixes[power]) > -1) ? Math.pow(1024, power + 1) : false;

                    if (bytesMultiplier) {
                        break;
                    }
                }

                // do some math to create our number
                n._n = ((bytesMultiplier) ? bytesMultiplier : 1) * ((stringOriginal.match(thousandRegExp)) ? Math.pow(10, 3) : 1) * ((stringOriginal.match(millionRegExp)) ? Math.pow(10, 6) : 1) * ((stringOriginal.match(billionRegExp)) ? Math.pow(10, 9) : 1) * ((stringOriginal.match(trillionRegExp)) ? Math.pow(10, 12) : 1) * ((string.indexOf('%') > -1) ? 0.01 : 1) * Number(((string.indexOf('(') > -1) ? '-' : '') + string.replace(/[^0-9\.-]+/g, ''));

                // round if we are talking about bytes
                n._n = (bytesMultiplier) ? Math.ceil(n._n) : n._n;
            }
        }
        return n._n;
    }

    function formatCurrency (n, format) {
        var prependSymbol = (format.indexOf('$') <= 1) ? true : false;

        // remove $ for the moment
        var space = '';

        // check for space before or after currency
        if (format.indexOf(' $') > -1) {
            space = ' ';
            format = format.replace(' $', '');
        } else if (format.indexOf('$ ') > -1) {
            space = ' ';
            format = format.replace('$ ', '');
        } else {
            format = format.replace('$', '');
        }

        // format the number
        var output = formatNumeral(n, format);

        // position the symbol
        if (prependSymbol) {
            if (output.indexOf('(') > -1 || output.indexOf('-') > -1) {
                output = output.split('');
                output.splice(1, 0, languages[currentLanguage].currency.symbol + space);
                output = output.join('');
            } else {
                output = languages[currentLanguage].currency.symbol + space + output;
            }
        } else {
            if (output.indexOf(')') > -1) {
                output = output.split('');
                output.splice(-1, 0, space + languages[currentLanguage].currency.symbol);
                output = output.join('');
            } else {
                output = output + space + languages[currentLanguage].currency.symbol;
            }
        }

        return output;
    }

    function formatPercentage (n, format) {
        var space = '';
        // check for space before %
        if (format.indexOf(' %') > -1) {
            space = ' ';
            format = format.replace(' %', '');
        } else {
            format = format.replace('%', '');
        }

        n._n = n._n * 100;
        var output = formatNumeral(n, format);
        if (output.indexOf(')') > -1 ) {
            output = output.split('');
            output.splice(-1, 0, space + '%');
            output = output.join('');
        } else {
            output = output + space + '%';
        }
        return output;
    }

    function formatTime (n, format) {
        var hours = Math.floor(n._n/60/60),
            minutes = Math.floor((n._n - (hours * 60 * 60))/60),
            seconds = Math.round(n._n - (hours * 60 * 60) - (minutes * 60));
        return hours + ':' + ((minutes < 10) ? '0' + minutes : minutes) + ':' + ((seconds < 10) ? '0' + seconds : seconds);
    }

    function unformatTime (string) {
        var timeArray = string.split(':'),
            seconds = 0;
        // turn hours and minutes into seconds and add them all up
        if (timeArray.length === 3) {
            // hours
            seconds = seconds + (Number(timeArray[0]) * 60 * 60);
            // minutes
            seconds = seconds + (Number(timeArray[1]) * 60);
            // seconds
            seconds = seconds + Number(timeArray[2]);
        } else if (timeArray.lenght === 2) {
            // minutes
            seconds = seconds + (Number(timeArray[0]) * 60);
            // seconds
            seconds = seconds + Number(timeArray[1]);
        }
        return Number(seconds);
    }

    function formatNumber (n, format) {
        var negP = false,
            optDec = false,
            abbr = '',
            bytes = '',
            ord = '',
            abs = Math.abs(n._n);

        // check if number is zero and a custom zero format has been set
        if (n._n === 0 && zeroFormat !== null) {
            return zeroFormat;
        } else {
            // see if we should use parentheses for negative number
            if (format.indexOf('(') > -1) {
                negP = true;
                format = format.slice(1, -1);
            }

            // see if abbreviation is wanted
            if (format.indexOf('a') > -1) {
                // check for space before abbreviation
                if (format.indexOf(' a') > -1) {
                    abbr = ' ';
                    format = format.replace(' a', '');
                } else {
                    format = format.replace('a', '');
                }

                if (abs >= Math.pow(10, 12)) {
                    // trillion
                    abbr = abbr + languages[currentLanguage].abbreviations.trillion;
                    n._n = n._n / Math.pow(10, 12);
                } else if (abs < Math.pow(10, 12) && abs >= Math.pow(10, 9)) {
                    // billion
                    abbr = abbr + languages[currentLanguage].abbreviations.billion;
                    n._n = n._n / Math.pow(10, 9);
                } else if (abs < Math.pow(10, 9) && abs >= Math.pow(10, 6)) {
                    // million
                    abbr = abbr + languages[currentLanguage].abbreviations.million;
                    n._n = n._n / Math.pow(10, 6);
                } else if (abs < Math.pow(10, 6) && abs >= Math.pow(10, 3)) {
                    // thousand
                    abbr = abbr + languages[currentLanguage].abbreviations.thousand;
                    n._n = n._n / Math.pow(10, 3);
                }
            }

            // see if we are formatting bytes
            if (format.indexOf('b') > -1) {
                // check for space before
                if (format.indexOf(' b') > -1) {
                    bytes = ' ';
                    format = format.replace(' b', '');
                } else {
                    format = format.replace('b', '');
                }

                var prefixes = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'],
                    min,
                    max;

                for (var power = 0; power <= prefixes.length; power++) {
                    min = Math.pow(1024, power);
                    max = Math.pow(1024, power+1);

                    if (n._n >= min && n._n < max) {
                        bytes = bytes + prefixes[power];
                        if (min > 0) {
                            n._n = n._n / min;
                        }
                        break;
                    }
                }
            }

            // see if ordinal is wanted
            if (format.indexOf('o') > -1) {
                // check for space before
                if (format.indexOf(' o') > -1) {
                    ord = ' ';
                    format = format.replace(' o', '');
                } else {
                    format = format.replace('o', '');
                }

                ord = ord + languages[currentLanguage].ordinal(n._n);
            }

            if (format.indexOf('[.]') > -1) {
                optDec = true;
                format = format.replace('[.]', '.');
            }

            var w = n._n.toString().split('.')[0],
                precision = format.split('.')[1],
                thousands = format.indexOf(','),
                d = '',
                neg = false;

            if (precision) {
                if (precision.indexOf('[') > -1) {
                    precision = precision.replace(']', '');
                    precision = precision.split('[');
                    d = toFixed(n._n, (precision[0].length + precision[1].length), precision[1].length);
                } else {
                    d = toFixed(n._n, precision.length);
                }

                w = d.split('.')[0];

                if (d.split('.')[1].length) {
                    d = languages[currentLanguage].delimiters.decimal + d.split('.')[1];
                } else {
                    d = '';
                }

                if (optDec && Number(d) === 0) {
                    d = '';
                }
            } else {
                w = toFixed(n._n, null);
            }

            // format number
            if (w.indexOf('-') > -1) {
                w = w.slice(1);
                neg = true;
            }

            if (thousands > -1) {
                w = w.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1' + languages[currentLanguage].delimiters.thousands);
            }

            if (format.indexOf('.') === 0) {
                w = '';
            }

            return ((negP && neg) ? '(' : '') + ((!negP && neg) ? '-' : '') + w + d + ((ord) ? ord : '') + ((abbr) ? abbr : '') + ((bytes) ? bytes : '') + ((negP && neg) ? ')' : '');
        }
    }

    /************************************
        Top Level Functions
    ************************************/

    numeral = function (input) {
        if (numeral.isNumeral(input)) {
            input = input.value();
        } else if (!Number(input)) {
            input = 0;
        }

        return new Numeral(Number(input));
    };

    // version number
    numeral.version = VERSION;

    // compare numeral object
    numeral.isNumeral = function (obj) {
        return obj instanceof Numeral;
    };

    // This function will load languages and then set the global language.  If
    // no arguments are passed in, it will simply return the current global
    // language key.
    numeral.language = function (key, values) {
        if (!key) {
            return currentLanguage;
        }

        if (key && !values) {
            currentLanguage = key;
        }

        if (values || !languages[key]) {
            loadLanguage(key, values);
        }

        return numeral;
    };

    numeral.language('en', {
        delimiters: {
            thousands: ',',
            decimal: '.'
        },
        abbreviations: {
            thousand: 'k',
            million: 'm',
            billion: 'b',
            trillion: 't'
        },
        ordinal: function (number) {
            var b = number % 10;
            return (~~ (number % 100 / 10) === 1) ? 'th' :
                (b === 1) ? 'st' :
                (b === 2) ? 'nd' :
                (b === 3) ? 'rd' : 'th';
        },
        currency: {
            symbol: '$'
        }
    });

    numeral.zeroFormat = function (format) {
        if (typeof(format) === 'string') {
            zeroFormat = format;
        } else {
            zeroFormat = null;
        }
    };

    /************************************
        Helpers
    ************************************/

    function loadLanguage(key, values) {
        languages[key] = values;
    }


    /************************************
        Numeral Prototype
    ************************************/


    numeral.fn = Numeral.prototype = {

        clone : function () {
            return numeral(this);
        },

        format : function (inputString) {
            return formatNumeral(this, inputString ? inputString : numeral.defaultFormat);
        },

        unformat : function (inputString) {
            return unformatNumeral(this, inputString ? inputString : numeral.defaultFormat);
        },

        value : function () {
            return this._n;
        },

        valueOf : function () {
            return this._n;
        },

        set : function (value) {
            this._n = Number(value);
            return this;
        },

        add : function (value) {
            this._n = this._n + Number(value);
            return this;
        },

        subtract : function (value) {
            this._n = this._n - Number(value);
            return this;
        },

        multiply : function (value) {
            this._n = this._n * Number(value);
            return this;
        },

        divide : function (value) {
            this._n = this._n / Number(value);
            return this;
        },

        difference : function (value) {
            var difference = this._n - Number(value);

            if (difference < 0) {
                difference = -difference;
            }

            return difference;
        }

    };

    /************************************
        Exposing Numeral
    ************************************/

    // CommonJS module is defined
    if (hasModule) {
        module.exports = numeral;
    }

    /*global ender:false */
    if (typeof ender === 'undefined') {
        // here, `this` means `window` in the browser, or `global` on the server
        // add `numeral` as a global object via a string identifier,
        // for Closure Compiler 'advanced' mode
        this['numeral'] = numeral;
    }

    /*global define:false */
    if (typeof define === 'function' && define.amd) {
        define([], function () {
            return numeral;
        });
    }
}).call(this);

/**
 * Created with JetBrains PhpStorm.
 * User: oli2002
 * Date: 9/18/13
 * Time: 2:05 PM
 * To change this template use File | Settings | File Templates.
 */

var _max_tab_height = 0;

$(document).ready(function() {
//    $('a, button').click(function() {
//        $(this).toggleClass('active');
//    });

//    $('.has-spinner').click(function() {
//        console.log('button pressed');
//        $(this).toggleClass('active');
//    });

    // Bind normal buttons
    Ladda.bind( '.btntest', { timeout: 2000 } );

});

function checkButtonPromise( fieldsArr, count, limit ) {

    var ajaxOK, promise;

    promise = new Promise();

    ajaxOK = waitWhenReady(fieldsArr, count, limit);

    if( ajaxOK ) {
        promise.resolve(ajaxOK);
    } else {
        promise.reject(ajaxOK);
    }

    return promise;
}

//inputField - input field element which is tested for value is being set
function waitWhenReady( fieldsArr, count, limit ) {

    var inputId = fieldsArr[count];

    var inputField = $(inputId).find('.keyfield').not("*[id^='s2id_']");

    if( inputField.hasClass('combobox')  ) {
        var testValue = inputField.select2('data').text;
    } else {
        var testValue = inputField.val();
    }

    var checkButton = $(inputId).find('#check_btn');
    //var isCheckBtn = checkButton.find("i").hasClass('checkbtn');

    if( limit == 0 ) {
        //printF(checkButton,"click button:");
        //console.log("check single form: count="+count);
        //checkButton.trigger("click");   //click only once
        var checkres = checkForm( checkButton, true );
    }

    //console.log("error length="+$('.maskerror-added').length);
    if( $('.maskerror-added').length > 0 || !checkres ) {
        //fieldsArr = null;
        return false;
    }

    return true;
}

function finalStepCheck() {
    $('#part-single').css( "width", "20%" );
    $('#block-single').css( "width", "20%" );
    $('#maincinglebtn').show();
    //console.log("asseccionKeyGlobal="+asseccionKeyGlobal+", asseccionKeytypeGlobal="+asseccionKeytypeGlobal+", partKeyGlobal="+partKeyGlobal+", blockKeyGlobal="+blockKeyGlobal);
}

//var _lbtn = Ladda.create( document.querySelector( '.singleform-optional-button' ) );

function addAsync(a, b) {
    console.log("addAsync");
    var deferred = Q.defer();
    deferred.reject(Error("Network Error"));
    // Wait 2 seconds and then add a + b
    setTimeout(function() {
        console.log("deferred.resolve");
        deferred.resolve(a + b);
    }, 2000);
    console.log("deferred.promise");
    return deferred.promise;
}

//Check form single
function checkFormSingle( elem ) {

//    (function () {
//        "use strict";
//
//        var deferredAnimate = Q.async(function* (element) {
//            for (var i = 0; i < 100; ++i) {
//                element.style.marginLeft = i + "px";
//                yield Q.delay(20);
//            }
//        });
//
//        Q.spawn(function* () {
//            yield deferredAnimate(document.getElementById("box"));
//            alert("Done!");
//        });
//    }());
//    return false;

    Q.all([
            addAsync(1, 1),
            addAsync(2, 2),
            addAsync(3, 3)
        ]).then(
            function(result1, result2, result3) {
                console.log(result1, result2, result3);
            },
            function(error) {
                console.log("error="+error);
            }
        );

//    addAsync(3, 4).then(
//        function(result) {
//            console.log(result);
//        },
//        function(error) {
//            console.log("error="+error);
//        }
//    );

    console.log("chaining");
    var promise = Q.promise(function(resolve, reject) {
        resolve(1);
    });

    promise.then(function(val) {
        console.log(val); // 1
        return val + 2;
    }).then(function(val) {
            console.log(val); // 3
        });

    console.log("finished");
    return false;

    if( validateMaskFields() > 0 ) {
        //console.log("errors > 0 => return");
        return false;
    }      

    if( $('#maincinglebtn').is(":visible") ) {
        //console.log("maincinglebtn is visible => return");
        return false;
    }

//    console.log("cancheck="+cancheck);
//    if( !cancheck ) {         
//        return;
//    }

    //$('#optional_param').collapse('toggle');    //open. Need to open to populate patient (if existed) linked to accession

    console.log("start ajax");
    //var lbtn = Ladda.create( document.querySelector( '.singleform-optional-button' ) );
    //_lbtn.start();
    //Ladda.bind(elem);

    //Ladda.bind( '.singleform-optional-button', { timeout: 2000 } );
    //return false;
    //btn.button('loading');
    //$("elem").toggleClass('active');
    var _lbtn = Ladda.create(elem);
    _lbtn.start();

    //$('.singleform-optional-button').append('<img class="spinner-image" src="http://collage.med.cornell.edu/order/bundles/olegorderform/form/img/select2-spinner.gif"/></div>');

    //alert('pause');

    //window.setTimeout( callWaitStack, 300 );
    //console.log('before');
    //console.log('after');

    var ajaxOK = callWaitStack();


    console.log("stop ajax");
//    //btn.button('reset');
    _lbtn.stop();

    if( !ajaxOK ) {
        return false;
    }

    if( $('.maskerror-added').length == 0 ) {
        collapseElementFix($('#optional_param'));
        finalStepCheck();
    } else {
        return false;
    }

    initOptionalParam();

    //console.log("ready!!!");

    return true;
}


function callWaitStack() {
    var fieldsArr = new Array();
    fieldsArr[0] = '#accession-single';
    fieldsArr[1] = '#part-single';
    fieldsArr[2] = '#block-single';

    var ajaxOK = true;

    Q.spread([
        waitWhenReady( fieldsArr, 0, 0 ),
        waitWhenReady( fieldsArr, 1, 0 ),
        waitWhenReady( fieldsArr, 2, 0 )
    ],
        function( acc, part, block ){
            if( !acc ) ajaxOK = false;
            if( !part ) ajaxOK = false;
            if( !block ) ajaxOK = false;
        }
    );

//    if( !waitWhenReady( fieldsArr, 0, 0 ) ) {
//        ajaxOK = false;
//    }

//    if( !waitWhenReady( fieldsArr, 1, 0 ) ) {
//        ajaxOK = false;
//    }
//
//    if( !waitWhenReady( fieldsArr, 2, 0 ) ) {
//        ajaxOK = false;
//    }

    return ajaxOK;
}


//Remove form single
function removeFormSingle( elem ) {

    //console.log("asseccionKeyGlobal="+asseccionKeyGlobal+", asseccionKeytypeGlobal="+asseccionKeytypeGlobal+", partKeyGlobal="+partKeyGlobal+", blockKeyGlobal="+blockKeyGlobal);    

    $("#remove_single_btn").button('loading');
    console.log("start remove: trigger blockbtn: class="+$('.blockbtn').attr("class"));

    $('.blockbtn').trigger("click");

    $('.partbtn').trigger("click");

    $('.accessionbtn').trigger("click");
    
    if( $('.patientmrn').find('i').hasClass('removebtn') ) {
        $('.patientmrn').trigger("click");
    }  

    $('#part-single').css( "width", "25%" );
    $('#block-single').css( "width", "25%" );
    $('#maincinglebtn').hide();
    collapseElementFix($('#optional_param'));   //close optional info

    console.log("end of remove");
    $("#remove_single_btn").button('reset');

}

function checkSingleFormOnNext( elem ) {
    //data-target="#orderinfo_param"
    if( validateMaskFields() > 0 ) {
        return false;
    } else {
        //console.log("no masking errors");
        $("#next_button").hide();

        var accTypeText = $('.accessiontype-combobox').first().select2('data').text;
        if( accTypeText != 'TMA Slide' ) {
            $("#optional_button").show();
        }

        collapseElementFix($('#orderinfo_param'));
    }
    return true;
}

function collapseElementFix(elem) {
    $(document).ready(function() {
        elem.collapse('toggle');
    });
}


function getAccessionInfoDebug(text) {
    var accTypeText = $('.accessiontype-combobox').select2('data').text;
    var accTypeVal = $('.accessiontype-combobox').select2('val');
    var accNum = $('.accession-mask').val();
    console.log("############ "+text+": Accession #="+accNum+", type text="+accTypeText+", type id="+accTypeVal);
}

//open a tab with max height
function initOptionalParam() {

    $('#optional_param_tab a').click(function (e) {

        e.preventDefault();

        var elem = $(this);

        elem.tab('show');

    });

}





/**
 * JS for slide coollections
 * Created with JetBrains PhpStorm.
 * User: oli2002
 * Date: 9/10/13
 * Time: 9:11 AM
 * To change this template use File | Settings | File Templates.
 */

////////////////// uses as generic collection field with + and - buttons ////////////////////////

var findCollectionStr = 'input[type=text],.ajax-combobox-staintype';
var findCollectionSpecialStr = 'input[type=text],.ajax-combobox-staintype';
var findCollectionEnabledStr = 'input[type=text]:enabled:not([readonly])';  //,.ajax-combobox-staintype:enabled:not([readonly])';

//get input field only
function getCollField( ident, patient, procedure, accession, part, block, slide, coll, prefix ) {

    //console.log("coll field input:"+ident+"_"+patient+"_"+procedure+"_"+accession+"_"+part+"_"+block+"_"+slide+"_"+coll+"_"+prefix);
    //diffDisident_field_0_0_0_0_0_0_0_0_diffdiag_0
    var dataholder = "#form-prototype-data"; //fixed data holder

    if( !prefix ) {
        prefix = "";
    }

    var identLowerCase = ident.toLowerCase();
    //console.log(dataholder+", ident="+ident+", identLowerCase="+identLowerCase);
    var collectionHolder =  $(dataholder);
    var prototype = collectionHolder.data('prototype-'+prefix+identLowerCase);
    //console.log("prototype="+prototype);

    var newForm = prototype.replace(/__patient__/g, patient);
    newForm = newForm.replace(/__procedure__/g, procedure);
    newForm = newForm.replace(/__accession__/g, accession);
    newForm = newForm.replace(/__part__/g, part);
    newForm = newForm.replace(/__block__/g, block);
    newForm = newForm.replace(/__slide__/g, slide);

    var regex = new RegExp( '__' + identLowerCase + '__', 'g' );
    newForm = newForm.replace(regex, coll);

    //console.log("newForm="+newForm);
    return newForm;
}

//called by pressing + button attached to the field
//btnpos - position of the button element (used for text area when + button is placed at the bottom of the text area)
function addCollectionField( elem, btnpos ) {

    var element = $(elem);
    //console.log("element.class="+element.attr('class'));

    var parent = element.parent().parent().parent();
    //console.log("parent id="+parent.attr('id')+", class="+parent.attr('class'));

    //make sure to get only one element with correct id containing patient, procedure ... indexes
    var elementInputFind = parent.find('.fieldInputColl').find(findCollectionStr).not("*[id^='s2id_']");    //.find('[id^=_patient_]');    //has("[id^=_patient_]");  //.not("*[id^='s2id_']");
    //var elementInputFind = parent.find('.slidespecialstains').find(findCollectionStr).not("*[id^='s2id_']");

    var elementInput =  elementInputFind.first();
    for( var i = 0; i < elementInputFind.length; i++ ) {
        //console.log("id="+elementInputFind.eq(i).attr('id')+", class="+elementInputFind.eq(i).attr('class'));
        if( elementInputFind.eq(i).attr('id') && elementInputFind.eq(i).attr('id').indexOf("_patient_") != -1 ) {
            elementInput = elementInputFind.eq(i);
            break;
        }
    }

    //console.log("elementInput.class="+elementInput.attr('class')+", id="+elementInput.attr('id'));
    var inputId = elementInput.attr('id');
    //console.log("inputId="+inputId+", elementInput.length="+elementInput.length);

    var idsArr = inputId.split("_");

    var name = idsArr[idsArr.length-holderIndex];   //i.e. "patient"
    var fieldName = idsArr[idsArr.length-fieldIndex];

    //console.log("name="+name+",fieldName="+fieldName);

    var patient = idsArr[4];
    var procedure = idsArr[6];
    var accession = idsArr[8];
    var part = idsArr[10];
    var block = idsArr[12];
    var slide = idsArr[14];

    if( inputId && inputId.indexOf("_slide_") != -1 ) {
        var block = idsArr[12];
        var slide = idsArr[14];
    } else {
        var block = 0;
        var slide = 0;
    }

    if( btnpos && btnpos == "bottom" ) {
        var elementHolder = element.parent().parent().parent();
    } else {
        var elementHolder = element.parent().parent().parent().parent().parent().parent();
    }

    //console.log("elementHolder id="+elementHolder.attr("id")+",class="+elementHolder.attr("class"));

    var collHolders = elementHolder.find('.row');
    var collHoldersCount = collHolders.length;  //TODO: this will be used as id of this element, however, the input field with this id might already exist (solution: get the max id for this element for all existing fields)
    var maxId = getMaxIdFromRows(collHolders,fieldName);
    //console.log("maxId="+maxId);

    var ident = name+fieldName;
    //console.log("ident=" + ident + ", collHoldersCount="+collHoldersCount + ", patient="+patient );

    var prefix = "add"; //indicate that this field is added by + button, so we can get different data prototype

    //var newForm = getDiffdiagField( fieldName, type, patient, procedure, accession, part, block, slide, collInputsCount, false );
    var newForm = getCollField( ident, patient, procedure, accession, part, block, slide, maxId+1, prefix );
    //console.log("newForm="+newForm);

    //var collInputs = elementHolder.find('input[type=text],textarea');
    var collInputs = elementHolder.find(findCollectionSpecialStr);
    var lastInput = collInputs.eq(collHoldersCount-1);
    //console.log("attach to el id="+lastInput.attr("id")+",class="+lastInput.attr("class"));

    //add to last input field
    var lastcollHolder = collHolders.last();    //eq(collHoldersCount-1);

    var btnDel = getDelBtn(btnpos);

    if( btnpos && btnpos == "bottom" ) {    //TEXTAREA with button at the bottom

        lastcollHolder.after(newForm);

        //add - button to + button if does not exists
        var delLen = element.closest('.addDelBtnColl').find('.delbtnCollField').length;
        //console.log("delLen="+delLen);
        if( delLen == 0 ) {
            //console.log("add - button to textarea + button");
            element.after(btnDel);
        }

        expandTextarea();

    } else {    //INPUT with attached button

        lastcollHolder.after(newForm);

        //continue only for fields where button is attached to it as a component to each field

        //switch + for - : remove + and add -
        //remove + from the last element
        var delBtnEl = lastInput.parent().find('.addbtnCollField');
        //console.log("delBtnEl id="+delBtnEl.attr("id")+",class="+delBtnEl.attr("class"));
        delBtnEl.remove();
        //add - to the last element if not exists
        //console.log("count="+lastInput.parent().find('.delbtnCollField').length);
        if( lastInput.parent().find('.delbtnCollField').length == 0 ) {
            lastInput.after(btnDel);
        }
        //add - to created element (this element has + button)
        var addBtnElement = elementHolder.find('.addbtnCollField');   //find + button
        addBtnElement.before(btnDel);

    }

    //populate the combobox by Ajax
    if( btnpos && btnpos == "bottom" ) {
        getComboboxSpecialStain(urlCommon,new Array(patient,procedure,accession,part,block,slide,maxId+1),true);
    }
}

//elements is a row element
function getMaxIdFromRows( elements, field ) {
    var maxId = 0;
    //console.log("elements.length="+elements.length + ", field="+field);
    for( var i = 0; i < elements.length; i++ ) {

        var element = elements.eq(i);
        var inputField = element.find(findCollectionStr);
        var fieldId = inputField.attr("id");
        //console.log("get Max: inputField id="+fieldId+",class="+inputField.attr("class"));
        var idArr = fieldId.split("_"+field+"_");
        var idValueStr = idArr[1].split("_")[0];
        var idValue = parseInt(idValueStr);
        //console.log( "idValue=" + idValue );
        //console.log( idValue+"=?"+maxId );
        if( idValue > maxId ) {
            maxId = idValue;
        }
    }
    return maxId;
}

//get input field only
function getDiffdiagField( name, type, patient, procedure, accession, part, block, slide, diffdiag, noDelBtn ) {

    //inputGroupId_patient_0_procedure_0_accession_0_part_0_diffDisident_0_diffDisident
    var ending = "_" + name + "_" + diffdiag + "_" + name;

    var dataholder = "#form-prototype-data"; //fixed data holder
    //console.log(dataholder);
    var collectionHolder =  $(dataholder);

    if( name == "diffDisident" ) {
        var prototype = collectionHolder.data('prototype-diffdisident');
        //console.log("diffDisident prototype="+prototype);
        var newForm = prototype.replace(/__patient__/g, patient);
        newForm = newForm.replace(/__procedure__/g, procedure);
        newForm = newForm.replace(/__accession__/g, accession);
        newForm = newForm.replace(/__part__/g, part);
        newForm = newForm.replace(/__partdiffdisident__/g, diffdiag);
    }

    if( name == "relevantScans" ) {
        var prototype = collectionHolder.data('prototype-relevantscans');
        //console.log("prototype="+prototype);
        var newForm = prototype.replace(/__patient__/g, patient);
        newForm = newForm.replace(/__procedure__/g, procedure);
        newForm = newForm.replace(/__accession__/g, accession);
        newForm = newForm.replace(/__part__/g, part);
        newForm = newForm.replace(/__block__/g, block);
        newForm = newForm.replace(/__slide__/g, slide);
        newForm = newForm.replace(/__relevantScans__/g, diffdiag);
    }

    var inputGroupId = 'inputGroupId_patient_'+patient+'_procedure_'+procedure+'_accession_'+accession+'_part_'+part+'_block_'+block+'_slide_'+slide+ending;
    //console.log("inputGroupId="+inputGroupId);

    var header = '<div class="input-group" id="'+inputGroupId+'">';

    var btnAdd = getAddBtn();

    var btnDel = "";
    if( noDelBtn != true ) {
        btnDel = getDelBtn();
    }

    var footer = '</div>';

    newForm = header + newForm + btnDel + btnAdd + footer;

    //console.log("newForm="+newForm);
    return newForm;
}

//delete input field and modify +/- buttons accordingly:
function delCollectionField( elem, btnpos ) {

    var element = $(elem);

    if( btnpos && btnpos == "bottom" ) {
        var parent = element.parent().parent().parent(); //field parent i.e. slidespecialstains
        var elementHolder = parent.find(".row").last();
        var holder = parent;
    } else {
        var parent = element.parent();
        var elementHolder = element.parent().parent().parent().parent().parent();
        var holder = elementHolder.parent();
    }

    //console.log("holder id="+holder.attr("id")+", class="+holder.attr("class"));
    //console.log("elementHolder id="+elementHolder.attr("id")+", class="+elementHolder.attr("class"));

    elementHolder.remove();

    if( btnpos && btnpos == "bottom" ) {
        var elementsEnabledCount = parent.find(".row").length;
    } else {
        //var elementsEnabled = holder.find('input[type=text]:enabled:not([readonly]),textarea:enabled:not([readonly])');
        var elementsEnabled = holder.find(findCollectionEnabledStr);
        var elementsEnabledCount = elementsEnabled.length;
    }

    //console.log("elementsEnabledCount="+elementsEnabledCount);

    if( !btnpos || btnpos != "bottom" ) { //only for attached field-button
        //add '+' button if the last visible field doesn't have it
        var lastEnabledEl = elementsEnabled.last();
        //console.log("lastEnabledEl id="+lastEnabledEl.attr("id")+", class="+lastEnabledEl.attr("class"));
        if( lastEnabledEl.parent().find('.addbtnCollField').length == 0 ) {
            //console.log("add + btn");
            var delBtnEl = lastEnabledEl.parent().find('.delbtnCollField');
            delBtnEl.after(getAddBtn());
        }
    }

    //remove '-' button if only one visible field left
    if( elementsEnabledCount == 1 ) {
        //console.log('remove - btn, count='+elementsEnabledCount);
        if( btnpos && btnpos == "bottom" ) {
            //console.log('button at the bottom => text area');
            element.remove();
        } else {
            elementsEnabled.first().parent().find('.delbtnCollField').remove();
        }
    }

    return false;
}

function getAddBtn() {
    //var addbtnId = 'addbtn_patient_'+patient+'_procedure_'+procedure+'_accession_'+accession+'_part_'+part+'_block_'+block+'_slide_'+slide+'_'+ident+'_'+collInt+'_'+ident;
    var btn = '<span onClick="addCollectionField(this)"'+
        'class="input-group-addon btn addbtnCollField" data-toggle="datepicker" type="button"><i class="glyphicon glyphicon-plus-sign"></i></span>';
    return btn;
}

function getDelBtn(btnpos) {
    if( btnpos && btnpos == "bottom" ) {
        var btn = '&nbsp;<button onClick="delCollectionField(this,\''+btnpos+'\')" class="btn btn-sm btn-danger delbtnCollField" type="button">-</button>';
    } else {
        var btn = '<span onClick="delCollectionField(this)"'+
            'class="input-group-addon btn delbtnCollField" data-toggle="datepicker" type="button"><i class="glyphicon glyphicon-minus-sign"></i></span>';
    }

    return btn;
}

/**
 * Created with JetBrains PhpStorm.
 * User: oli2002
 * Date: 9/11/13
 * Time: 5:17 PM
 * To change this template use File | Settings | File Templates.
 * JS for diseaseType group
 */

function diseaseTypeListener() {

    //add listener for all visible radio for diseaseType
    $(".diseaseType").find('input:radio').on('change', function(){
        //access value of changed radio group with $(this).val()
        var checkedValue = $(this).val();
        //console.log("checkedValue="+checkedValue);

        var parent = $(this).closest('.partdiseasetype');
        var originradio = parent.find('.originradio');
        var primaryorganradio = parent.find('.primaryorganradio');
        //console.log("originradio id="+originradio.attr("id")+", class="+originradio.attr("class"));

        if( checkedValue == "Neoplastic" ) {

            originradio.collapse('show');

            //add listener for child radio, because it is visible now
            originradio.find('input:radio').on('change', function(){
                var checkedValueOrigin = $(this).val();
                //console.log("checkedValueOrigin="+checkedValueOrigin);
                if( checkedValueOrigin == "Metastatic" ) {
                    primaryorganradio.collapse('show');
                } else {
                    if( primaryorganradio.is(':visible') ) {
                        primaryorganradio.collapse('hide');
                        clearPrimaryOrgan($(this));
                    }
                }
            });

        } else {

            hideDiseaseTypeChildren($(this));

        }


    });

}

function diseaseTypeRender() {

    function checkDiseaseType() {
        var radioElement = $(this);
        var radioElementValue = radioElement.val();

        if( radioElement.is(':checked') && radioElementValue == "Neoplastic" ) {
            //console.log("checked id="+radioElement.attr("id"));

            var parent = radioElement.closest('.partdiseasetype');
            var originradio = parent.find('.originradio');

            originradio.collapse('show');

            //console.log("originradio id="+originradio.attr("id")+",class="+originradio.attr("id"));

            originradio.find('input:radio').each(function() {

                var originElement = $(this);
                //console.log("originElement id="+originElement.attr("id")+", value="+originElement.val());

                if( originElement.is(':checked') && originElement.val() == "Metastatic" ) {

                    var primaryorganradio = parent.find('.primaryorganradio');
                    primaryorganradio.collapse('show');
                }

            });

        }

    }

    $(".diseaseType").find('input:radio').each(checkDiseaseType);
}

//render diseaseType by AJAX from checkForm
function diseaseTypeRenderCheckForm( element, origin, primaryorgan ) {

    if( !element.attr("class") || element.attr("class").indexOf('diseaseType') == -1 ) {
        return;
    }

    function checkDiseaseType() {
        var radioElement = $(this);
        var radioElementValue = radioElement.val();

        if( radioElement.is(':checked') && radioElementValue == "Neoplastic" ) {
            //console.log("checked id="+radioElement.attr("id"));

            var parent = radioElement.closest('.partdiseasetype');
            var originradio = parent.find('.originradio');

            originradio.collapse('show');

            //console.log("originradio id="+originradio.attr("id")+",class="+originradio.attr("id"));

            originradio.find('input:radio').each(function() {

                var originElement = $(this);
                var originElementValue = originElement.val();
                //console.log("originElement id="+originElement.attr("id")+", value="+originElementValue);

                if( origin == "Metastatic" && originElementValue == "Metastatic" ) {
                    //console.log("Check "+origin);
                    originElement.prop('checked',true);

                    var primaryorganradio = parent.find('.primaryorganradio');
                    primaryorganradio.collapse('show');

                    //set ajax-combobox-organ
                    var primaryOrgan = primaryorganradio.find(".ajax-combobox-organ");
                    primaryOrgan.select2('data', {id: primaryorgan, text: primaryorgan});
                } else {
                    originElement.attr("disabled", true);
                }

            });

        }

    }

    if( element ) {
        element.find('input:radio').each(checkDiseaseType);
    } else {
        $(".diseaseType").find('input:radio').each(checkDiseaseType);
    }
}

//hide children of DiseaseType
function hideDiseaseTypeChildren( element ) {

    //console.log("hide disease children, id="+element.attr("id")+",class="+element.attr("class"));

    //if( !element.attr("class") || element.attr("class").indexOf('diseaseType') == -1 ) {
    //    return;
    //}

    //console.log("hide Disease Type Children");
    var originradio = element.closest('.partdiseasetype').find(".originradio");
    if( originradio.is(':visible') ) {
        originradio.collapse('hide');
        clearOrigin(element);
    }

    var primaryorganradio = element.closest('.partdiseasetype').find(".primaryorganradio");
    //console.log("primaryorganradio id="+primaryorganradio.attr("id")+",primaryorganradio="+primaryorganradio.attr("class"));
    if( primaryorganradio.is(':visible') ) {
        primaryorganradio.collapse('hide');
        clearPrimaryOrgan(element);
    }

    //remove disable attr
    //console.log("disable and uncheck all in 'Origin'");
    originradio.find("input").removeAttr("disabled");
    originradio.find("input").prop('checked',false);

}

function clearOrigin( elem ) {
    //console.log("clear orig, id="+elem.attr("id")+",class="+elem.attr("class"));
    elem.closest('.partdiseasetype').find('.originradio').find('.radio_inline').each(function() {
        //console.log("clear radio_inline, id="+$(this).attr("id")+",class="+$(this).attr("id"));
        $(this).find("input").prop('checked',false);
    });
}

function clearPrimaryOrgan( elem ) {
    //console.log("clear orig, id="+elem.attr("id")+",class="+elem.attr("class"));
    elem.closest('.partdiseasetype').find('.ajax-combobox-organ').select2('data', null);
}
/**
 * Created with JetBrains PhpStorm.
 * User: oli2002
 * Date: 9/13/13
 * Time: 5:50 PM
 * To change this template use File | Settings | File Templates.
 */

var combobox_width = '100%'; //'element'
//var urlCommon = "http://collage.med.cornell.edu/order/scanorder/Scanorders2/web/app_dev.php/util/";
//var urlCommon = "http://collage.med.cornell.edu/order/util/";
var urlBase = $("#baseurl").val();
var urlCommon = "http://"+urlBase+"/util/";
//var type = $("#formtype").val();
var cicle = $("#formcicle").val();
var user_name = $("#user_name").val();
var user_id = $("#user_id").val();
var proxyuser_name = $("#proxyuser_name").val();
var proxyuser_id = $("#proxyuser_id").val();
//console.log("urlCommon="+urlCommon);
var orderinfoid = $(".orderinfo-id").val();

var accessiontype = new Array();
var partname = new Array();
var blockname = new Array();
var stain = new Array();
var scanregion = new Array();
var procedure = new Array();
var organ = new Array();
var delivery = new Array();
var returnslide = new Array();
var pathservice = new Array();
var userpathservice = new Array();
var userpathserviceflag = false;

var asyncflag = true;

function regularCombobox() {
    //resolve
    $("select.combobox").select2({
        width: combobox_width,
        dropdownAutoWidth: true,
        placeholder: "Choose an option",
        allowClear: true
        //selectOnBlur: true
        //readonly: true
        //selectOnBlur: true,
        //containerCssClass: 'combobox-width'
    });

    //set amd make provider read only
    $("#s2id_oleg_orderformbundle_orderinfotype_provider").select2("readonly", true);
    $("#s2id_oleg_orderformbundle_orderinfotype_provider").select2('data', {id: user_id, text: user_name});

    //preselect with current user
    if( proxyuser_id ) {
//        proxyuser_id = user_id;
//        proxyuser_name = user_name;
        $("#s2id_oleg_orderformbundle_orderinfotype_proxyuser").select2('data', {id: proxyuser_id, text: proxyuser_name});
    }

}

function customCombobox() {

    //console.log("cicle="+cicle);

    if( cicle && cicle != 'edit_user' && urlBase ) {
        getComboboxAccessionType(urlCommon,new Array("0","0","0","0","0","0"));
        getComboboxPartname(urlCommon,new Array("0","0","0","0","0","0"));
        getComboboxBlockname(urlCommon,new Array("0","0","0","0","0","0"));
        getComboboxScanregion(urlCommon,new Array("0","0","0","0","0","0"));
        getComboboxStain(urlCommon,new Array("0","0","0","0","0","0"));
        getComboboxSpecialStain(urlCommon,new Array("0","0","0","0","0","0","0"),false);
        getComboboxProcedure(urlCommon,new Array("0","0","0","0","0","0"));
        getComboboxOrgan(urlCommon,new Array("0","0","0","0","0","0"));
        getComboboxDelivery(urlCommon,new Array("0","0","0","0","0","0"));
        getComboboxReturn(urlCommon,new Array("0","0","0","0","0","0"));
        getComboboxPathService(urlCommon,new Array("0","0","0","0","0","0"));
        slideType(new Array("0","0","0","0","0","0"));
    }

    if( cicle && urlBase && ( cicle == 'edit_user' || cicle == 'accountreq')  ) {
        getComboboxPathService(urlCommon,new Array("0","0","0","0","0","0"));
    }
}

function populateSelectCombobox( targetid, data, placeholder, multiple ) {

    if( placeholder ) {
        var allowClear = true;
    } else {
        var allowClear = false;
    }

    if( multiple ) {
        var multiple = true;
    } else {
        var multiple = false;
    }

    $(targetid).select2({
        placeholder: placeholder,
        allowClear: allowClear,
        width: combobox_width,
        dropdownAutoWidth: true,
        selectOnBlur: true,
        dataType: 'json',
        quietMillis: 100,
        multiple: multiple,
        data: data,
        createSearchChoice:function(term, data) {
            //if( term.match(/^[0-9]+$/) != null ) {
            //    console.log("term is digit");
            //}
            return {id:term, text:term};
        }
    });
}


//#############  stains  ##############//
function getComboboxStain(urlCommon, ids) {

    var url = urlCommon+"stain";

    if( cicle == "new" || cicle == "create" ) {
        url = url + "?opt=default";
    }

    //console.log("stain.length="+stain.length);
    if( stain.length == 0 ) {
        //console.log("stain 0");
        $.ajax({
            url: url,
            async: asyncflag
        }).success(function(data) {
            stain = data;
            populateSelectCombobox( ".ajax-combobox-stain", stain, null );
            populateSelectCombobox( ".ajax-combobox-staintype", stain, null );
        });
    } else {
        //console.log("stain exists");
        populateSelectCombobox( ".ajax-combobox-stain", stain, null );
        populateSelectCombobox( ".ajax-combobox-staintype", stain, null );
    }

    if( cicle == "new"  ) {
        var uid = 'patient_'+ids[0]+'_procedure_'+ids[1]+'_accession_'+ids[2]+'_part_'+ids[3]+'_block_'+ids[4]+'_slide_'+ids[5];
        var id= "#oleg_orderformbundle_orderinfotype_"+uid+"_";
        var targetid = id+"stain_0_field";
        $(targetid).select2('val', '1');
    }

}

function getComboboxSpecialStain(urlCommon, ids, preset) {

    var url = urlCommon+"stain";

    if( cicle == "new" || cicle == "create" ) {
        url = url + "?opt=default";
    }

    var targetid = "";
    if( cicle == "new" || (cicle == "amend" && preset) || (cicle == "edit" && preset) ) {
        var uid = 'patient_'+ids[0]+'_procedure_'+ids[1]+'_accession_'+ids[2]+'_part_'+ids[3]+'_block_'+ids[4]+'_slide_'+ids[5];
        var id= "#oleg_orderformbundle_orderinfotype_"+uid+"_";
        targetid = id+"specialStains_"+ids[6]+"_staintype";
        //console.log("targetid="+targetid);
    }

    if( stain.length == 0 ) {
        $.ajax({
            url: url,
            async: asyncflag
        }).success(function(data) {
                stain = data;
                populateSelectCombobox( ".ajax-combobox-staintype", stain, null );
            });
    } else {
        populateSelectCombobox( targetid, stain, null );
    }

    //console.log("special stain preset="+preset);
    if( targetid != "" ) {
        $(targetid).select2('val', '1');
    }
}

//#############  scan regions  ##############//
function getComboboxScanregion(urlCommon,ids) {

    var url = urlCommon+"scanregion";
    //console.log("scanregion.length="+scanregion.length);

    if( cicle == "edit" || cicle == "show" || cicle == "amend" ) {
        url = url + "?opt="+orderinfoid;
    }

    if( scanregion.length == 0 ) {
        $.ajax({
            url: url,
            async: asyncflag
        }).success(function(data) {
            scanregion = data;
            populateSelectCombobox( ".ajax-combobox-scanregion", scanregion, null );
        });
    } else {
        populateSelectCombobox( ".ajax-combobox-scanregion", scanregion, null );
    }

    if( cicle == "new"  ) {
        var uid = 'patient_'+ids[0]+'_procedure_'+ids[1]+'_accession_'+ids[2]+'_part_'+ids[3]+'_block_'+ids[4]+'_slide_'+ids[5];
        var id= "#oleg_orderformbundle_orderinfotype_"+uid+"_";
        var targetid = id+"scan_0_scanregion";
        $(targetid).select2('data', {id: 'Entire Slide', text: 'Entire Slide'});
    }
}

//#############  source organs  ##############//
function getComboboxOrgan(urlCommon,ids) {
//    var uid = 'patient_'+ids[0]+'_procedure_'+ids[1]+'_accession_'+ids[2]+'_part_'+ids[3];   //+'_block_'+ids[4]+'_slide_'+ids[5];
//    var id= "#oleg_orderformbundle_orderinfotype_"+uid+"_";
    var url = urlCommon+"organ";

    if( cicle == "new" || cicle == "create" ) {
        url = url + "?opt=default";
    }

    if( organ.length == 0 ) {
        $.ajax({
            url: url,
            async: asyncflag
        }).success(function(data) {
            organ = data;
            populateSelectCombobox( ".ajax-combobox-organ", organ, "Source Organ" );
        });
    } else {
        populateSelectCombobox( ".ajax-combobox-organ", organ, "Source Organ" );
    }

}


//#############  procedure types  ##############//
function getComboboxProcedure(urlCommon,ids) {
//    var uid = 'patient_'+ids[0]+'_procedure_'+ids[1];    //+'_accession_'+ids[2]+'_part_'+ids[3]+'_block_'+ids[4]+'_slide_'+ids[5];
//    var id= "#oleg_orderformbundle_orderinfotype_"+uid+"_";
    var url = urlCommon+"procedure";
//    var targetid = id+"name_0_field";

    if( cicle == "new" || cicle == "create" ) {
        url = url + "?opt=default";
    }

    if( procedure.length == 0 ) {
        $.ajax({
            url: url,
            async: asyncflag
        }).success(function(data) {
            procedure = data;
            populateSelectCombobox( ".ajax-combobox-procedure", procedure, "Procedure Type" );
        });
    } else {
        populateSelectCombobox( ".ajax-combobox-procedure", procedure, "Procedure Type" );
    }

}

//#############  Accession Type  ##############//
function getComboboxAccessionType(urlCommon,ids) {

    var url = urlCommon+"accessiontype";

    //console.log("orderformtype="+orderformtype);

    if( cicle == "new" || cicle == "create" ) {
        url = url + "?opt=default&type="+orderformtype;
    }

    if( accessiontype.length == 0 ) {
        $.ajax({
            url: url,
            async: asyncflag
        }).success(function(data) {
                accessiontype = data;
                populateSelectCombobox( ".accessiontype-combobox", accessiontype, null );
                setAccessionMask();
            });
    } else {
        populateSelectCombobox( ".accessiontype-combobox", accessiontype, null );
    }

    if( cicle == "new"  ) {
        var uid = 'patient_'+ids[0]+'_procedure_'+ids[1]+'_accession_'+ids[2];
        var id= "#oleg_orderformbundle_orderinfotype_"+uid+"_";
        var targetid = id+"accession_0_accessiontype";
        //console.log("targetid="+targetid);
        $(targetid).select2('val', 1);
    }
}

//#############  partname types  ##############//
function getComboboxPartname(urlCommon,ids) {

    var url = urlCommon+"partname";

//    if( cicle == "new" || cicle == "create" ) {
//        url = url + "?opt=default";
//    }

    if( cicle == "edit" || cicle == "show" || cicle == "amend" ) {
        url = url + "?opt="+orderinfoid;
    }

    if( partname.length == 0 ) {
        $.ajax({
            url: url,
            async: asyncflag
        }).success(function(data) {
            partname = data;
            populateSelectCombobox( ".ajax-combobox-partname", partname, "Part Name" );
        });
    } else {
        populateSelectCombobox( ".ajax-combobox-partname", partname, "Part Name" );
    }

}

//#############  blockname types  ##############//
function getComboboxBlockname(urlCommon,ids) {

    var url = urlCommon+"blockname";

//    if( cicle == "new" || cicle == "create" ) {
//        url = url + "?opt=default";
//    }
    if( cicle == "edit" || cicle == "show" || cicle == "amend" ) {
        url = url + "?opt="+orderinfoid;
    }

    if( blockname.length == 0 ) {
        $.ajax({
            url: url,
            async: asyncflag
        }).success(function(data) {
            blockname = data;
            populateSelectCombobox( ".ajax-combobox-blockname", blockname, "Block Name" );
        });
    } else {
        populateSelectCombobox( ".ajax-combobox-blockname", blockname, "Block Name" );
    }

}




//#############  slide delivery  ##############//
function getComboboxDelivery(urlCommon,ids) {
    //var uid = "";   //'patient_'+ids[0]+'_procedure_'+ids[1]+'_accession_'+ids[2]+'_part_'+ids[3]+'_block_'+ids[4]+'_slide_'+ids[5];
//    var id= "#oleg_orderformbundle_orderinfotype_";
    var url = urlCommon+"delivery";
//    var targetid = id+"slideDelivery";

    if( cicle == "edit" || cicle == "show" || cicle == "amend" ) {
        url = url + "?opt="+orderinfoid;
    }

    //console.log("scanregion.length="+organ.length);
    if( delivery.length == 0 ) {
        $.ajax({
            url: url,
            async: asyncflag
        }).success(function(data) {
            delivery = data;
            populateSelectCombobox( ".ajax-combobox-delivery", delivery, null );
            if( cicle == "new"  ) {
                $(".ajax-combobox-delivery").select2('data', {id: "I'll give slides to Noah - ST1015E (212) 746-2993", text: "I'll give slides to Noah - ST1015E (212) 746-2993"});
            }
        });
    } else {
        populateSelectCombobox( ".ajax-combobox-delivery", delivery, null );
        if( cicle == "new"  ) {
            $(".ajax-combobox-delivery").select2('data', {id: "I'll give slides to Noah - ST1015E (212) 746-2993", text: "I'll give slides to Noah - ST1015E (212) 746-2993"});
        }
    }

}

//#############  return slides to  ##############//
function getComboboxReturn(urlCommon,ids) {
    //var uid = 'patient_'+ids[0]+'_procedure_'+ids[1]+'_accession_'+ids[2]+'_part_'+ids[3]+'_block_'+ids[4]+'_slide_'+ids[5];
    //var id= "#oleg_orderformbundle_orderinfotype_";
    var url = urlCommon+"return";
    //var targetid = id+"returnSlide";

    if( cicle == "edit" || cicle == "show" || cicle == "amend" ) {
        url = url + "?opt="+orderinfoid;
    }

    //console.log("scanregion.length="+organ.length);
    if( returnslide.length == 0 ) {
        $.ajax({
            url: url,
            async: asyncflag
        }).success(function(data) {
            returnslide = data;
            populateSelectCombobox( ".ajax-combobox-return", returnslide, null );
            if( cicle == "new"  ) {
                $(".ajax-combobox-return").select2('data', {id: "Filing Room", text: "Filing Room"});
            }
        });
    } else {
        populateSelectCombobox( ".ajax-combobox-return", returnslide, null );
        if( cicle == "new"  ) {
            $(".ajax-combobox-return").select2('data', {id: "Filing Room", text: "Filing Room"});
        }
    }

}

//#############  pathology service  ##############//
function getComboboxPathService(urlCommon,ids) {

    //******************* order pathology service *************************//
    //var uid = 'patient_'+ids[0]+'_procedure_'+ids[1]+'_accession_'+ids[2]+'_part_'+ids[3]+'_block_'+ids[4]+'_slide_'+ids[5];
    //var id= "#oleg_orderformbundle_orderinfotype_";
    var targetid = ".ajax-combobox-pathservice";
    var url = urlCommon+"pathservice";

    if( cicle == "new" || cicle == "create" || cicle == "accountreq" ) {
        url = url + "?opt=default";
    }

    //console.log("cicle="+cicle+", url="+url+", targetid="+targetid);
    if( cicle == 'accountreq' ) {
        var multiple = true;
    } else {
        var multiple = false;
    }

    if( pathservice.length == 0 ) {
        $.ajax({
            url: url,
            async: asyncflag
        }).success(function(data) {
            pathservice = data;
            populateSelectCombobox( targetid, pathservice, "Pathology Service", multiple );
        });
    } else {
        populateSelectCombobox( targetid, pathservice, "Pathology Service", multiple );
    }

//    //******************* user pathology service *************************//
//    //var targetid = ".ajax-combobox-pathservice";    //"#oleg_orderformbundle_user_pathologyServices";
//    populateSelectCombobox( targetid, pathservice, "Pathology Service" );
//
//    //console.log("userpathservice.length="+userpathservice.length);
//    if( userpathservice.length == 0 && !userpathserviceflag ) {
//        $.ajax({
//            url: urlCommon+"userpathservice",
//            type: 'POST',
//            data: {username: user_name},
//            dataType: 'json',
//            async: asyncflag,
//            success: function(data) {
//                userpathserviceflag = true;
//                userpathservice = data;
//            }
//        });
//    }
//
//    if( cicle == "new" ) {
//        $(targetid).select2('data', userpathservice);
//    }

}


function initComboboxJs(ids) {

    if( urlBase ) {

        cicle = 'new';

        getComboboxAccessionType(urlCommon,ids);
        getComboboxPartname(urlCommon,ids);
        getComboboxBlockname(urlCommon,ids);
        getComboboxStain(urlCommon,ids);
        getComboboxSpecialStain(urlCommon,ids,false);
        getComboboxScanregion(urlCommon,ids);
        getComboboxProcedure(urlCommon,ids);
        getComboboxOrgan(urlCommon,ids);
        getComboboxPathService(urlCommon,ids);
        slideType(ids);
    }
}


function slideType(ids) {    
    
    //oleg_orderformbundle_orderinfotype_patient_0_procedure_0_accession_0_part_0_block_1_slide_0_slidetype
    var uid = 'patient_'+ids[0]+'_procedure_'+ids[1]+'_accession_'+ids[2]+'_part_'+ids[3]+'_block_'+ids[4]+'_slide_'+ids[5];
    var id= "#oleg_orderformbundle_orderinfotype_"+uid+"_slidetype";

    $(id).change(function(e) {   //.slidetype-combobox
        //console.log("slidetype-combobox changed: this id="+$(this).attr('id')+",class="+$(this).attr('class'));
        //e.preventDefault();
        var parent = $(this).parent().parent().parent().parent().parent().parent().parent().parent();
        //console.log("parent: id="+parent.attr('id')+",class="+parent.attr('class'));
        var blockValue = parent.find('.element-title').first();
        //console.log("slidetype-combobox: id="+parent.find('.slidetype-combobox').first().attr('id')+",class="+parent.find('.slidetype-combobox').first().attr('class'));
        var slideType = parent.find('.slidetype-combobox').first().select2('val');
        //console.log("blockValue: id="+blockValue.attr('id')+",class="+blockValue.attr('class')+",slideType="+slideType);
        var keyfield = parent.find('#check_btn');
        if( slideType == 3 ) {   //'Cytopathology'
            //console.log("Cytopathology is chosen = "+slideType);
            keyfield.attr('disabled','disabled'); 
            disableInElementBlock(parent.find('#check_btn').first(), true, "all", null, null);
            var htmlDiv = '<div class="element-skipped">Block is not used for cytopathology slide</div>';
            parent.find('.element-skipped').first().remove();
            blockValue.after(htmlDiv);
            blockValue.hide();
            parent.find('.form-btn-options').first().hide();
            //parent.find('.panel-body').first().css("border-color", "#C0C0C0");
        } else {    
            //disableInElementBlock(parent.find('#check_btn').first(), false, "all", null, null);
            disableInElementBlock(parent.find('#check_btn').first(), true, null, "notkey", null);
            parent.find('.element-skipped').first().remove();
            blockValue.show();
            keyfield.removeAttr('disabled'); 
            parent.find('.form-btn-options').first().show();
            //parent.find('.panel-body').first().css("border-color", "#1268B3");
        }
        
    });   
}

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


var urlBase = $("#baseurl").val();
var urlCheck = "http://"+urlBase+"/check/";

var keys = new Array("mrn", "accession", "partname", "blockname");
var arrayFieldShow = new Array("clinicalHistory","age","diffDisident"); //,"disident"); //display as array fields "sex"
var selectStr = 'input[type=file],input.form-control,div.patientsexclass,div.diseaseType,div.select2-container,[class^="ajax-combobox-"],[class^="combobox"],textarea,select';  //div.select2-container, select.combobox, div.horizontal_type

var orderformtype = $("#orderformtype").val();

var dataquality_message1 = new Array();
var dataquality_message2 = new Array();

//var _autogenAcc = 8;
//var _autogenMrn = 13;

//add disident to a single form array field
$(document).ready(function() {

    if( orderformtype == "single") {
        arrayFieldShow.push("disident")
    }

    $("#save_order_onidletimeout_btn").click(function() {
        $(this).attr("clicked", "true");
    });

    //validation on form submit
    $("#scanorderform").on("submit", function () {
        return validateForm();
    });

    addKeyListener();

});

//  0         1              2           3   4  5  6   7
//oleg_orderformbundle_orderinfotype_patient_0_mrn_0_field
var fieldIndex = 3;     //get 'key'
var holderIndex = 5;    //get 'patient'
//console.log("urlCheck="+urlCheck);

//needed by a single slide form
var asseccionKeyGlobal = "";
var asseccionKeytypeGlobal = "";
var partKeyGlobal = "";
var blockKeyGlobal = "";
var mrnKeyGlobal = "";
var mrnKeytypeGlobal = "";

//remove errors from inputs
function addKeyListener() {
    //remove has-error class from mrn and accession inputs
    $('.accessionaccession').find('.keyfield').parent().keypress(function() {
        $(this).removeClass('has-error');
    });
    $('.patientmrn').find('.keyfield').parent().keypress(function() {
        //console.log("remove has-error on keypress");
        $(this).removeClass('has-error');
    });

    $('.ajax-combobox-partname').on("change", function(e) {
        //console.log("remove maskerror-added on change");
        $(this).siblings('.maskerror-added').remove();
    });
    $('.ajax-combobox-blockname').on("change", function(e) {
        //console.log("remove maskerror-added on change");
        $(this).siblings('.maskerror-added').remove();
    });
}

//object contains: input value, type, parent (btn element), name, fieldname
function btnObject( btn ){

    if( !btn ) {
        return null;
    }

   this.btn = btn;
   this.element = null;
   this.key = "";
   this.type = null;
   this.typename = null;
   this.parentbtn = null;
   this.name = null;
   this.fieldname = null;
   this.remove = false;

   var inputEl = btn.closest('.row').find('input.keyfield'); 
   this.element = inputEl;
   if( inputEl.attr('class').indexOf("ajax-combobox") != -1 ) {    //select2
       if( inputEl.select2('data') ) {
           this.key = inputEl.select2('data').text;
       }           
   } else {
       this.key = inputEl.val(); 
   }
   
    //get type
    var typeEl = inputEl.prev();
    if( typeEl.hasClass('combobox') ) {    //type exists
        this.type = typeEl.select2('val');
        this.typename = typeEl.select2('data').text;
    }
    
    //get name
    var idsArr = inputEl.attr('id').split("_");
    this.name = idsArr[idsArr.length-holderIndex];       //i.e. "patient"
    this.fieldname = idsArr[idsArr.length-fieldIndex];   //i.e. "mrn"
    
    //get parent
    if( this.name == 'part' || this.name == 'block' ) {      
        this.parentbtn = getParentBtn(btn);             
    } 
    
    //if remove
    if( btn.hasClass('removebtn') ) {
        this.remove = true;
    }
    
    console.log(this);
}

function getParentBtn(btn) {
    
    var parentBtn = null;
    
    if( orderformtype == "single" ) {
        if( name == 'part' ) {
            parentBtn = $('#accessionbtn');
        } 
        if( name == 'block' ) {
            parentBtn = $('#partbtn');
        }    
    } else {
        var parentEl1 = btn.closest('.panel');      
        //console.log(parentEl1);
        var parentEl2 =  parentEl1.siblings().first();    //.find('#check_btn');
        //console.log(parentEl2);
        parentBtn = parentEl2.find('#check_btn');     
    }
    
    if( parentBtn.length == 0 ) {
        parentBtn = null;
    }
    
    return parentBtn;
}

//called by button click
function checkForm( btnel ) {
    
    var btn = $(btnel);         
    var btnObj = new btnObject(btn);      
    var parentBtnObj = null;
    var grandparentBtnObj = null;
      
    console.log('input='+btnObj.key+', type='+btnObj.type);        
    
    //if delete button?
    if( btnObj && btnObj.remove ) {
        console.log('execute click this');
        executeClick( btnObj );
        return;
    }
    
    //patient 
    if( btnObj.name == 'patient' ) {
        console.log('execute click this');
        executeClick( btnObj );
        return;
    }
    
    //accession 
    if( btnObj.name == 'accession' ) {
        console.log('execute click this');
        executeClick( btnObj );
        return;
    }
    
    //part
    if( btnObj.name == 'part' ) {
        console.log('execute click parent then this');
        
        parentBtnObj = new btnObject(btnObj.parentbtn);
        
        //working!
        executeClick( parentBtnObj ).
        then( 
            function(response) {
                console.log("Success!", response);
                return executeClick( btnObj );
            } 
        ).
        then(
            function(response) {
                console.log("Yey JSON!", response);
            }
        ); 
        return;
    }
    
    //block
    if( btnObj.name == 'block' ) {
        console.log('execute click grandparent then parent then this');
        
        parentBtnObj = new btnObject(btnObj.parentbtn);
        grandparentBtnObj = new btnObject(parentBtnObj.parentbtn);
        
        //working!
        executeClick( grandparentBtnObj ).
        then( 
            function(response) {
                console.log("Success!", response);
                return executeClick( parentBtnObj );
            } 
        ).
        then( 
            function(response) {
                console.log("Success!", response);
                return executeClick( btnObj );
            } 
        ).            
        then(
            function(response) {
                console.log("Yey JSON!", response);
            }
        );
        return;    
    }     
         
    return; 
}

function executeClick( btnObj ) {
       
    return Q.promise(function(resolve, reject) {
    
        var casetype = 'check';       
        var btn = btnObj.btn;
        var urlcasename = null;
        var ajaxType = 'GET';
        var key = btnObj.key;
        var type = btnObj.type;
        var parentKey = null;
        var parentType = null;
        var grandparentKey = null;
        var grandparentType = null;
        var single = false; //temp

        console.log('executeClick: key='+key+', parentKey='+parentKey+', parentType='+parentType);
               
        if( btnObj && btnObj.key == '' && !btnObj.remove ) {
            console.log('Case 1: key not exists => generate');
            casetype = 'generate';      
        } else if( btnObj && btnObj.key != '' && !btnObj.remove ) {
            console.log('Case 2: key exists => check');
            casetype = 'check';
        } else if( btnObj && btnObj.remove ) {
            console.log('Case 3: key exists and button delete => delete');
            casetype = 'delete';
        } else {
            console.log('Logical error: invalid key');
        }
        
        console.log('executeClick: casetype='+casetype);
        
        urlcasename = btnObj.name+'/'+casetype;
        
        if( casetype == 'delete' ) {
            ajaxType = 'DELETE'; 
           
            var extraStr = "";
            if( type ) {
                extraStr = "?extra="+type;
            }           
           
           urlcasename = urlcasename + '/' + key + extraStr;
        } 
              
        //get parent
        var parentBtnObj = new btnObject(btnObj.parentbtn);
        
        if( parentBtnObj ) {
            parentKey = parentBtnObj.key;
            parentType = parentBtnObj.type;
        }
        
        if( parentBtnObj && parentType && parentKey == '' ) {
            console.log('parent key is empty');
            reject(Error("parent key is empty"));          
            //return;
        }
        
        //get grand parent
        var grandparentBtnObj = new btnObject(parentBtnObj.parentbtn);
        if( grandparentBtnObj ) {
            grandparentKey = grandparentBtnObj.key;
            grandparentType = grandparentBtnObj.type;
        }
                    
        if( parentBtnObj && parentType && parentKey == '' ) {
            console.log('parent key is empty');
            reject(Error("parent key is empty"));          
            //return;
        }
        
        //trim values
        key = trimWithCheck(key);
        type = trimWithCheck(type);
        parentKey = trimWithCheck(parentKey);
        parentType = trimWithCheck(parentType);
        grandparentKey = trimWithCheck(grandparentKey);
        grandparentType = trimWithCheck(grandparentType);
        
        //temp
        if( orderformtype == "single" ) {
            single = true;
        }
        
        btn.button('loading');
        
        $.ajax({
            url: urlCheck+urlcasename,
            type: ajaxType,
            contentType: 'application/json',
            dataType: 'json',
            async: true,    //use synchronous call
            data: {key: key, extra: type, parentkey: parentKey, parentextra: parentType, grandparentkey: grandparentKey, grandparentextra: grandparentType },
            success: function (data) {
                btn.button('reset');
                
                if( data == -2 ) {
                    //Existing Auto-generated object does not exist in DB
                    createErrorWell(btnObj.element,btnObj.name);
                    reject(Error("Existing Auto-generated object does not exist in DB"));                  
                } else if( data && data.id ) {
                    console.debug("ajax key value data is found");                                    
                    
                    if( casetype == 'generate' ) {
                        setElementBlock(btn, data, null, "key");
                        disableInElementBlock(btn, false, null, "notkey", null);
                    }
                    
                    if( casetype == 'check' ) {
                        setElementBlock(btn, data);
                        //second: disable or enable element. Make sure this function runs after set Element Block
                        disableInElementBlock(btn, true, "all", null, "notarrayfield");
                    }
                    
                    if( casetype == 'delete' ) {
                        if( data != '-1' || single ) {
                            //console.debug("Delete Success");
                            deleteSuccess(btn,single);
                        } else {
                            //console.debug("Delete ok with Error");
                            deleteError(btn,single);
                        }
                    }                  
                    
                    //if( casetype != 'check' ) {
                        invertButton(btn);
                    //}    
                    
                    //resolve("ajax key value data is found");
                } else {
                    
                    if( casetype == 'check' ) {
                        invertButton(btn);
                    }
                    
                    if( casetype == 'delete' ) {
                        deleteError(btn,single);
                    }
                    
                    console.debug('set key data is null');
                } 
                
                resolve("ajax key value data is found");
            },
            error: function () {
                btn.button('reset');
                reject(Error("set key ajax error"));
                //resolve("set key ajax error");
                console.debug("set key ajax error");
            }
        }); //ajax               
        
    }); //promise
     
}


/**
 * Created with JetBrains PhpStorm.
 * User: oli2002
 * Date: 1/6/14
 * Time: 4:17 PM
 * To change this template use File | Settings | File Templates.
 */

///////////////////// DEFAULT MASKS //////////////////////////
var _mrnplaceholder = "NOMRNPROVIDED-";
var _accplaceholder = "NOACCESSIONPROVIDED-";
var _maskErrorClass = "has-warning"; //"maskerror"
var _repeatBig = 25;
var _repeatSmall = 13;

function getMrnDefaultMask() {
    var mrns = [
        //{ "mask": "9[999999999999]" },
//        { "mask": "9" },
        //{ "mask": "m" },
//        {"mask": "*[m][m][m][m][m][m][m][m][m][m][m][*]"} //13 total: alfa-numeric leading and ending, plus 11 alfa-numeric and dash in the middle
        { "mask": getRepeatMask(_repeatSmall,"m") }
    ];

    var mask = {
        "mask": mrns
//        "repeat": _repeatSmall,
//        "greedy": false
    };

    return mask;
}

function getAgeDefaultMask() {
    return "f[9][9]";
}

function getAccessionDefaultMask() {
    //console.log('get default accession mask');
    var accessions = [
//        { "mask": "AA99-f[99999]" },
//        { "mask": "A99-f[99999]" }
        { "mask": "AA99-f[9][9][9][9][9]" },
        { "mask": "A99-f[9][9][9][9][9]" }
    ];
    return accessions;
}
///////////////////// END OF DEFAULT MASKS //////////////////////

function fieldInputMask() {

    $.extend($.inputmask.defaults.definitions, {
        'f': {  //masksymbol
            "validator": "[1-9]",
            "cardinality": 1,
            'prevalidator': null
        }
    });

    //any alfa-numeric without leading or ending '-'
    $.extend($.inputmask.defaults.definitions, {
        "m": {
            "validator": "[A-Za-z0-9-]",
            "cardinality": 1,
            'prevalidator': null
        }
    });

    $.extend($.inputmask.defaults, {
        "onincomplete": function(result){
            makeErrorField($(this),false);
        },
        "oncomplete": function(){ clearErrorField($(this)); },
        "oncleared": function(){ clearErrorField($(this)); },
        "onKeyValidation": function(result) {
            makeErrorField($(this),false);
        },
        "onKeyDown": function(result) {
            makeErrorField($(this),false);
        },
        placeholder: " ",
        clearMaskOnLostFocus: true
    });

    $(":input").inputmask();

    if( cicle == "new" || cicle == "create" ) {

        $(".accession-mask").inputmask( { "mask": getAccessionDefaultMask() } );
        $(".patientmrn-mask").inputmask( getMrnDefaultMask() );
        //$(".patientmrn-mask").inputmask( { "mask": "*", "repeat": 13, "greedy": false } );
        //$(".patientmrn-mask").inputmask( { "mask": "***" } );

    } else {
        //set mrn for amend
        var mrnkeytypeField = $('.mrntype-combobox').not("*[id^='s2id_']");
        mrnkeytypeField.each( function() {
            setMrntypeMask($(this),false);
        });

        //set accession for amend: do this in selectAjax.js when accession is loaded by Ajax
    }

    $(".patientage-mask").inputmask( { "mask": getAgeDefaultMask() });

    $(".datepicker").inputmask( "mm/dd/yyyy" );

    $('.phone-mask').inputmask("mask", {"mask": "+9 (999) 999-9999"});

//    $('.email-mask').inputmask('Regex', { regex: "[a-zA-Z0-9._%-]+@[a-zA-Z0-9-]+\\.[a-zA-Z]{2,4}" });

    accessionTypeListener();
    mrnTypeListener();

}

//element is check button
function setDefaultMask( element ) {

    //printF(element,"Set default mask1:");
    var maskField = getKeyGroupParent(element).find("*[class$='-mask']");
    //printF(maskField,"Set default mask2:");

    clearErrorField(element);

    maskField.each(function() {
        if( $(this).hasClass('patientmrn-mask') ) {
            //console.log("Set default mask for MRN");
            $(this).inputmask( getMrnDefaultMask() );
        }

        if( $(this).hasClass('accession-mask') ) {
            //console.log("Set default mask for Accession");
            $(this).inputmask( { "mask": getAccessionDefaultMask() } );
        }
    });  

}


function mrnTypeListener() {
    $('.mrntype-combobox').on("change", function(e) {
        //console.log("mrn type change listener!!!");
        setMrntypeMask($(this),true);
    });
}

function getMrnAutoGenMask() {
    var placeholderStr = getCleanMaskStr( _mrnplaceholder );
    var mask = {"mask": placeholderStr+"9999999999" };
    return mask;
}

//elem is a keytype element (select box)
function setMrntypeMask( elem, clean ) {
    //console.log("mrn type changed = " + elem.attr("id") + ", class=" + elem.attr("class") );

    var mrnField = getKeyGroupParent(elem).find('.patientmrn-mask');
    var value = elem.select2("val");
    var text = elem.select2("data").text;
    //console.log("text=" + text + ", value=" + value);

    //clear input field
    if( clean ) {
        mrnField.val('');
        clearErrorField(mrnField);
    }

    //mrnField.inputmask('remove');

    switch( text )
    {
        case "Auto-generated MRN":
            mrnField.inputmask( getMrnAutoGenMask() );
            var parent = elem.closest('.patientmrn');
            if( parent.find('#check_btn').find('i').hasClass('checkbtn') ) {
                parent.find('#check_btn').trigger("click");
            }
            //console.log('Auto-generated MRN !!!');
            break;
        case "Existing Auto-generated MRN":
            mrnField.inputmask( getMrnAutoGenMask() );
            break;
        case "New York Hospital MRN":
        case "Epic Ambulatory Enterprise ID Number":
        case "Weill Medical College IDX System MRN":
        case "Uptown Hospital ID":
        case "NYH Health Quest Corporate Person Index":
        case "New York Downtown Hospital":
            mrnField.inputmask( getMrnDefaultMask() );
            break;
        case "California Tumor Registry Patient ID":
        case "Specify Another Patient ID Issuer":
        case "De-Identified NYH Tissue Bank Research Patient ID":
            var repeatStr = getRepeatMask(_repeatBig,"m");
            mrnField.inputmask( { "mask": repeatStr } );
            break;
        case "De-Identified Personal Educational Slide Set Patient ID":
            var placeholderStr = user_name+"-EMRN-";
            var repeatmrn = getRepeatNum( placeholderStr, _repeatBig );
            var placeholderStr = getCleanMaskStr( placeholderStr );
            var repeatStr = getRepeatMask(repeatmrn,"m");
            mrnField.inputmask( { "mask": placeholderStr+repeatStr } );
            break;
        case "De-Identified Personal Research Project Patient ID":
            var placeholderStr = user_name+"-RMRN-";
            var repeatmrn = getRepeatNum( placeholderStr, _repeatBig );
            var placeholderStr = getCleanMaskStr( placeholderStr );
            var repeatStr = getRepeatMask(repeatmrn,"m");
            mrnField.inputmask( { "mask": placeholderStr+repeatStr } );
            break;
        case "Enterprise Master Patient Index":
            mrnField.inputmask('remove');
        default:
            mrnField.inputmask('remove');
    }
}

//this function is called by getComboboxAccessionType() in selectAjax.js when accession type is initially populated by ajax
function setAccessionMask() {
    var acckeytypeField = $('.accessiontype-combobox').not("*[id^='s2id_']");
    acckeytypeField.each( function() {
        setAccessiontypeMask($(this),false);
    });
}

function accessionTypeListener() {
    $('.accessiontype-combobox').on("change", function(e) {
        //console.log("accession type listener!!!");
        setAccessiontypeMask($(this),true);

        //enable optional_button for single form
        if( orderformtype == "single" ) {
            var accTypeText = $(this).select2('data').text;
            if( accTypeText == 'TMA Slide' ) {
                $("#optional_button").hide();
            } else {
                $("#optional_button").show();
            }
            
            if( accTypeText == 'Auto-generated Accession Number' ) {
                //console.log("click on order info");
                checkFormSingle($('#optional_button'));
            }
        }

    });
}

function getAccessionAutoGenMask() {
    var placeholderStr = getCleanMaskStr( _accplaceholder );
    var mask = {"mask": placeholderStr+"9999999999" };
    return mask;
}

//elem is a keytype element (select box)
function setAccessiontypeMask(elem,clean) {
    //console.log("accession type changed = " + elem.attr("id") + ", class=" + elem.attr("class") );

    var accField = getKeyGroupParent(elem).find('.accession-mask');
    //printF(accField,"Set Accession Mask:")

    var value = elem.select2("val");
    var text = elem.select2("data").text;
    //console.log("text=" + text + ", value=" + value);

    //clear input field
    if( clean ) {
        accField.val('');
        clearErrorField(accField);
    }

    switch( text )
    {
        case "Auto-generated Accession Number":
            accField.inputmask( getAccessionAutoGenMask() );
            var btn = elem.closest('.accessionaccession').find('#check_btn');
            if( btn.find('i').hasClass('checkbtn') ) {
                btn.trigger("click");
            }
            //console.log('Auto-generated Accession !!!');
            //printF(btn,"btn to click:");
            break;
        case "Existing Auto-generated Accession Number":
            accField.inputmask( getAccessionAutoGenMask() );
            break;
        case "NYH CoPath Anatomic Pathology Accession Number":
            accField.inputmask( {"mask": getAccessionDefaultMask() } );
            break;
        case "De-Identified Personal Educational Slide Set Specimen ID":
            var placeholderStr = user_name+"-E-";
            var repeatnum = getRepeatNum( placeholderStr, _repeatBig );
            var placeholderStr = getCleanMaskStr( placeholderStr );
            var repeatStr = getRepeatMask(repeatnum,"m");
            accField.inputmask( { "mask": placeholderStr+repeatStr } );
            break;
        case "De-Identified Personal Research Project Specimen ID":
            var placeholderStr = user_name+"-R-";
            var repeatnum = getRepeatNum( placeholderStr, _repeatBig );
            var placeholderStr = getCleanMaskStr( placeholderStr );
            var repeatStr = getRepeatMask(repeatnum,"m");
            accField.inputmask( { "mask": placeholderStr+repeatStr } );
            break;
        case "De-Identified NYH Tissue Bank Research Specimen ID":
        case "California Tumor Registry Specimen ID":
        case "Specify Another Specimen ID Issuer":
        case "TMA Slide":
            var repeatStr = getRepeatMask(_repeatBig,"m");
            accField.inputmask( { "mask": repeatStr } );
            break;
        default:
            accField.inputmask('remove');
    }
}

function noMaskError( element ) {
    //console.log( "complete="+ element.inputmask("isComplete")+", !allZeros="+!allZeros(element) );

    var keytypeText = getKeyGroupParent(element).find('.accessiontype-combobox').select2('data').text;

//    if( ( keytypeText == "NYH CoPath Anatomic Pathology Accession Number" && element.hasClass('accession-mask') ) ||
//        element.hasClass('patientage-mask') ||
//        element.hasClass('datepicker') )
//    {   //regular mask

    //console.log( "no mask error: keytypeText="+ keytypeText );

     if( keytypeText == "NYH CoPath Anatomic Pathology Accession Number" && element.hasClass('accession-mask') ||
         element.hasClass('datepicker') ||
         element.hasClass('patientage-mask') ||
         element.hasClass('phone-mask') ||
         element.hasClass('email-mask')
     ) {  //regular mask + non zero mask

         if( !allZeros(element) && element.inputmask("isComplete") ) {
             return true;
         } else {
             return false;
         }

    } else {   //non zero mask only

         if( !allZeros(element) ) {
             return true;
         } else {
             return false;
         }

    }
}

function makeErrorField(element, appendWell) {
    //console.log("make red field id="+element.attr("id")+", class="+element.attr("class"));

    if( noMaskError(element) ) {
        clearErrorField(element);
        return;
    }

    var value =  trimWithCheck(element.val());
    //console.log("error: value="+value);
    if( value != "" ) {
        element.parent().addClass(_maskErrorClass);
        createErrorMessage( element, null, appendWell );
    }

}

function clearErrorField( element ) {

    //check if not all zeros
    if( allZeros(element) ) {
        //console.log("all zeros!");
        return;
    }

    //console.log("make ok field id="+element.attr("id")+", class="+element.attr("class"));
    element.parent().removeClass(_maskErrorClass);
    $('.maskerror-added').remove();
}

function allZeros(element) {

    if( !element.inputmask("hasMaskedValue") ) {
        return false;
    }

    //console.log("element.val()="+element.val());
    //printF(element,"all zeros? :")
    var res = trimWithCheck(element.val());
    var res = res.match(/^[0]+$/);
    //console.log("res="+res);
    if( res ) {
        //console.log("all zeros!");
        return true;
    }
    return false;
}

function validateMaskFields( element, fieldName ) {

    var errors = 0;
    $('.maskerror-added').remove();

    //console.log("validate mask fields: fieldName="+fieldName);

    if( element ) {

        //console.log("validate mask fields: element id=" + element.attr("id") + ", class=" + element.attr("class") );

        var parent = getKeyGroupParent(element);
        var errorFields = parent.find("."+_maskErrorClass);

        if( fieldName == "partname" ) { //if element is provided, then validate only element's input field. Check parent => accession

            var parent = element.closest('.panel-procedure').find('.accessionaccession');
            //console.log("parent id=" + parent.attr("id") + ", class=" + parent.attr("class") );
            var errorFields = parent.find("."+_maskErrorClass);
            //console.log("count errorFields=" + errorFields.length );

            if( errorFields.length > 0 ) {
                var partname = getKeyGroupParent(element).find("*[class$='-mask']");   //find("input").not("*[id^='s2id_']");
                createErrorMessage( partname, "Accession Number above", true );   //create warning well under partname
            }
        }

    } else {
        var errorFields = $("."+_maskErrorClass);
    }

    errorFields.each(function() {
        var elem = $(this).find("*[class$='-mask']");
        //console.log("error id=" + elem.attr("id") + ", class=" + elem.attr("class") );

        //Please correct the invalid accession number
        var errorHtml = createErrorMessage( elem, null, true );

        $('#validationerror').append(errorHtml);

        errors++;
    });


    //console.log("number of errors =" + errors );
    return errors;
}

function createErrorMessage( element, fieldName, appendWell ) {

    if( noMaskError(element) ) {
        return;
    }

    if( !fieldName ) {
        var fieldName = "field marked in red above";
        if( element.hasClass("accession-mask") ) {
            fieldName = "Accession Number";
        }
        if( element.hasClass("patientmrn-mask") ) {
            fieldName = "MRN";
        }
    }

    var errorHtml =
        '<div class="maskerror-added alert alert-danger">' +
            'Please correct the invalid ' + fieldName + '.' +
            '</div>';

    //console.log("append to element id="+element.attr("id")+", class="+element.attr("class") + ", appendWell="+appendWell);

    //always append error well for datepicker
    if( element.hasClass('datepicker') ) {
        appendWell = true;
        element = element.closest('.input-group.date');
        var len = element.closest('.row').find('.maskerror-added').length;
        //console.log('length='+len );
        if( len > 0 ) {
            appendWell = false;
        }
    }

    if( appendWell ) {
        element.after(errorHtml);
    }

    return errorHtml;
}

function changeMaskToNoProvided( combobox, fieldName ) {
    if( fieldName == "mrn" ) {
        var mrnField = getKeyGroupParent(combobox).find('.patientmrn-mask');
        mrnField.inputmask( getMrnAutoGenMask() );
    }
    if( fieldName == "accession" ) {
        var accField = getKeyGroupParent(combobox).find('.accession-mask');
        //printF(accField,"change to noprovided: ");
        accField.inputmask( getAccessionAutoGenMask() );
    }
}

function getCleanMaskStr( str) {
    //console.log("str="+str);

    var defarr = $.inputmask.defaults.definitions;

    for( var index in defarr ) {
        index = trimWithCheck(index);
        if( index != "*" ) {
            //console.log( "index="+index);
            var replaceValue = "\\\\"+index;
            var regex = new RegExp( index, 'g' );
            str = str.replace(regex, replaceValue);
        }

    }

    //console.log( "str="+str);
    return str;
}

function getRepeatNum( placeholderStr, rnum ) {
    var origLength = placeholderStr.length;
    //console.log("origLength=" + origLength);
    var res = rnum - origLength;
    //console.log("res=" + res);
    return res;
}

//allsame - if true: use * as the first and last masking characters (no leading and ending dashes)
function getRepeatMask( repeat, char, allsame ) {
    if( allsame ) {
        var repeatStr = char;
    } else {
        var repeatStr = "*";
        repeat = repeat - 1;
    }

    for (var i=1; i<repeat; i++ ) {
        repeatStr = repeatStr + char;
    }

    if( allsame ) {
        //
    } else {
        repeatStr = repeatStr + "*";
    }

    return repeatStr;
}

//elem: button, combobox (keytype) or input field
function getKeyGroupParent(elem) {
    //printF(elem, "@@@@@@@@@@@@@ Get parent for element:");
    if( orderformtype == "single" && elem.attr('class').indexOf("mrn") == -1) {
        var parent = $('.singleorderinfo');

    } else {
        var parent = elem.closest('.row');
    }

    return parent;
}

//elem is a keytype (combobox)
function getButtonParent(elem) {

    var parent = elem.closest('.row');

    if( orderformtype == "single") {
        if( elem.hasClass('mrntype-combobox') ) {
            var parent = $('#patient_0');
        }
        if( elem.hasClass('accessiontype-combobox') ) {
            var parent = $('#accession-single');
        }
        if( elem.hasClass('partbtn') ) {
            var parent = $('#part-single');
        }
        if( elem.hasClass('blockbtn') ) {
            var parent = $('#block-single');
        }
    }

    return parent;
}

/**
 * User: oli2002
 * Date: 8/14/13
 * Time: 12:36 PM
 */

//add all element to listeners again, the same as in ready
function initAdd() {

    expandTextarea();

//    $(".combobox").combobox();
    regularCombobox();

    initDatepicker();

    //clean validation elements
    //console.log("clean initAdd");
    cleanValidationAlert();

    fieldInputMask();

}

//confirm delete
function deleteItem(id) {
    //check if this is not the last element in the parent tree
    var thisId = "formpanel_"+id;
    //console.log("replace thisId="+thisId);
    var thisParent = $("#"+thisId).parent();
    //console.log("replace thisParent="+thisParent.attr('id'));
    var elements = thisParent.children( ".panel" );
    //console.log("replace elements.length="+elements.length);

    if( confirm("This auction will affect this element and all its children. Are you sure?") ) {

        if( elements.length > 1 ) {

            $('#formpanel_'+id).remove();

            //check if it is only one element left
            if( (elements.length-1) == 1 ) {
                //change "delete" to "clear"
                var element = thisParent.children( ".panel" );
                //console.log("rename element="+element.attr('id'));
                var delBtnToReplace = element.children(".panel-heading").children(".form-btn-options").children(".delete_form_btn");
                //console.log("rename delBtnToReplace="+delBtnToReplace.attr('id'));
                delBtnToReplace.html('Clear');
            }

        } else {
            //clear the form and all children
            var ids = id.split("_");
            //alert("You can't delete only one left " + ids[0]);

            //console.log("id="+id);
            //console.log("rename elements.length="+elements.length);
            addSameForm(ids[0], ids[1], ids[2], ids[3], ids[4], ids[5], ids[6], ids[7], ids[7], ids[8]);

            $('#formpanel_'+id).remove();

            //make sure to rename delete button to "Clear" if it is only one element left
            if( elements.length == 1 ) {
                //change "delete" to "clear"
                var element = thisParent.children( ".panel" );
                var delBtnToReplace = element.children(".panel-heading").children(".form-btn-options").children(".delete_form_btn");
                delBtnToReplace.html('Clear');
            }
        }
    }

    //clean validation elements
    cleanValidationAlert();

    return false;
}

//main input form from html button: add parent form (always type='multi')
//TODO: remove add parent and use only addChildForms in loop including adding parent
function addSameForm( name, patientid, procedureid, accessionid, partid, blockid, slideid, scanid, stainid ) {

    var uid = patientid+"_"+procedureid+"_"+accessionid+"_"+partid+"_"+blockid+"_"+slideid+"_"+scanid+"_"+stainid;  //+"_"+diffdiag+"_"+specstain+"_"+image;

    //console.log("addSameForm="+name+"_"+uid);

    //prepare form ids and pass it as array
    //increment by 1 current object id
    var btnids = getIds(name, patientid, procedureid, accessionid, partid, blockid, slideid, scanid, stainid);
    var id = btnids['id'];
    var idsorig = btnids['orig'];
    var ids = btnids['ids'];
    var idsm = btnids['idsm'];
    var idsp = btnids['idsp'];

    //place the form in the html page after the last similar element: use parent
    //    var holder = "#formpanel_"+name+"_"+uid;
    //formpanel_slide_0_0_0_0_0_0_0_0
    var partialId = "formpanel_"+name+"_"+btnids['partialId'];
    var elements = $('[id^='+partialId+']');
    var holder = elements.eq(elements.length-1);
    //console.log( "id="+holder.attr('id') );

    //console.log("holder="+holder);

    //attach form
    $(holder).after( getForm( name, id, idsorig, ids, idsm ) );

    if( name == "slide" ) {
        //addCollFieldFirstTime( "relevantScans", ids );
        //addDiffdiagFieldFirstTime( name, ids );
        //addCollFieldFirstTime( "specialStains", ids );
    }

    //bind listener to the toggle button
    bindToggleBtn( name + '_' + ids.join("_") );
    bindDeleteBtn( name + '_' + ids.join("_") );
    //originOptionMulti(ids);
    diseaseTypeListener();
    initComboboxJs(ids);
    initAdd();
    addKeyListener();

    //create children nested forms
    //var nameArray = ['patient', 'procedure', 'accession', 'part', 'block', 'slide', 'stain_scan' ];
    var nameArray = ['patient', 'procedure', 'part', 'block', 'slide' ];
    var length = nameArray.length
    var index = nameArray.indexOf(name);
    //console.log("index="+index+" len="+length);
    var parentName = name;
    for (var i = index+1; i < length; i++) {
        //console.log("=> name="+nameArray[i]);

        if( nameArray[i] == 'stain_scan' ) {
            addChildForms( parentName, 'stain', nameArray[i-1], patientid, procedureid, accessionid, partid, blockid, slideid, scanid, stainid );
            addChildForms( parentName, 'scan', nameArray[i-1], patientid, procedureid, accessionid, partid, blockid, slideid, scanid, stainid );
        } else {
            addChildForms( parentName, nameArray[i], nameArray[i-1], patientid, procedureid, accessionid, partid, blockid, slideid, scanid, stainid );
        }

    }

    //replace all "add" buttons of this branch with "add" buttons for the next element. use parent and children
    var thisId = "formpanel_"+name+"_"+ids.join("_");
    //console.log("thisId="+thisId);
    var thisParent = $("#"+thisId).parent();
    var childrens = thisParent.children( ".panel" );

    //var addbtn = '<button id="form_add_btn_' + name + '_' + ids.join("_") + '" type="button" class="testjs add_form_btn btn btn-xs btn_margin" onclick="addSameForm(\'' + name + '\''+ ',' + ids.join(",") + ')">Add</button>';
    var addbtn =  getHeaderAddBtn( name, ids );

    for (var i = 0; i < childrens.length; i++) {
        var addBtnToReplace = childrens.eq(i).children(".panel-heading").children(".form-btn-options").children(".add_form_btn");
        addBtnToReplace.replaceWith( addbtn );

        //rename "clear" to "Delete"
        if( childrens.length > 1 ) {
            //console.log("childrens.length="+childrens.length);
            var delBtnToRename = childrens.eq(i).children(".panel-heading").children(".form-btn-options").children(".delete_form_btn");
            delBtnToRename.html('Delete');
        }
    }

    initAllElements();
}

//add children forms triggered by parent form
function addChildForms( parentName, name, prevName, patientid, procedureid, accessionid, partid, blockid, slideid, scanid, stainid ) {

    //idsu: +1 for the parent object (parentName)
    //attach to previous object (prevName)
    var btnids = getIds( parentName, patientid, procedureid, accessionid, partid, blockid, slideid, scanid, stainid );
    var idsorig = btnids['orig'];
    var ids = btnids['ids'];
    var idsm = btnids['idsm'];
    var id = btnids['id'];
    id = id - 1;
    var idsu = ids.join("_");

    var uid = prevName+"_"+idsu;
    var holder = "#form_body_"+uid;
    //console.debug(name+": ADD CHILDS to="+holder+" uid="+idsu);

    //attach children form
    $(holder).append( getForm( name, id, idsorig, ids, idsm  ) );

    if( name == "slide" ) {
        //addCollFieldFirstTime( "relevantScans", ids );
        //addDiffdiagFieldFirstTime( name, ids );
        //addCollFieldFirstTime( "specialStains", ids );
    }
    
    bindToggleBtn( name + '_' + ids.join("_") );
    bindDeleteBtn( name + '_' + ids.join("_") );
    //originOptionMulti(ids);
    diseaseTypeListener();
    initComboboxJs(ids);
    initAdd();
    addKeyListener();
}

//input: current form ids
function getForm( name, id, idsorig, ids, idsm ) {

    var deleteStr = "Clear";
    var idsu = ids.join("_");
    var idsc = ids.join(",");
    //increment by 1 current object id
    var formbody = getFormBody( name, ids[0], ids[1], ids[2], ids[3], ids[4], ids[5], ids[6], ids[7] );

    //console.log("getForm: "+name+"_"+", id="+id+", ids="+ids+', idsm='+idsm);

    if( name == "scan" || name == "stain" ) {
        var addbtn = "";
        var deletebtn = "";
        //var itemCount = (id+2);
    } else {
        var addbtn = getHeaderAddBtn( name, ids );
        var deletebtn = getHeaderDeleteBtn( name, ids, deleteStr );
    }

    //get itemCount from partialId
    var itemCount = getIdByName(name,ids) + 1;
    //console.log('itemCount='+itemCount);

    var title = name;
    if( name == "procedure" ) {
        title = "accession";
    }

    var formhtml =
        '<div id="formpanel_' +name + '_' + idsu + '" class="panel panel-'+name+' panel-multi-form">' +
            '<div class="panel-heading">' +

                '<button id="form_body_toggle_'+ name + '_' + idsu +'" type="button"' +
                    'class="btn btn-default btn-xs form_body_toggle_btn glyphicon glyphicon-folder-open pull-left"' +
                    'data-toggle="collapse" data-target="#form_body_'+name+'_'+idsu+'">'+
                '</button>'+

//            '<button id="form_body_toggle_'+ name + '_' + idsu +'" type="button" class="btn btn-default btn-xs form_body_toggle_btn glyphicon glyphicon-folder-open black" data-toggle="collapse" data-target="#form_body_'+name+'_'+idsu+'"></button>' +
//            '&nbsp;' +
//            '<div class="element-title">' + capitaliseFirstLetter(title) + ' ' + itemCount + '</div>' +
                '<h4 class="panel-title element-title">' + capitaliseFirstLetter(title) + ' ' + itemCount + '</h4>' +
                '<div class="form-btn-options">' +
                    addbtn +
                    deletebtn +
                '</div>' +
                '<div class="clearfix"></div>' +
            '</div>' +  //panel-heading
            '<div id="form_body_' + name + '_' + idsu + '" class="panel-body collapse in">' +
                formbody +
            '</div>' +
            '</div>';

    return formhtml;
}

function getFormBody( name, patientid, procedureid, accessionid, partid, blockid, slideid, scanid, stainid ) {

    //console.log("name="+name+",patient="+patientid+ ",procedure="+procedureid+",accession="+accessionid+",part="+partid+",block="+blockid+",slide="+slideid);

    var collectionHolder =  $('#form-prototype-data');

    // Get the data-prototype explained earlier
    var prototype = collectionHolder.data('prototype-'+name);
    //console.log("prototype="+prototype);

    //console.log("before replace patient...");
    var newForm = prototype.replace(/__patient__/g, patientid);
    //console.log("before replace procedure... NewForm="+newForm);
    newForm = newForm.replace(/__procedure__/g, procedureid);
    newForm = newForm.replace(/__accession__/g, accessionid);
    newForm = newForm.replace(/__part__/g, partid);
    newForm = newForm.replace(/__block__/g, blockid);
    newForm = newForm.replace(/__slide__/g, slideid);
    newForm = newForm.replace(/__scan__/g, scanid);
    newForm = newForm.replace(/__stain__/g, stainid);

    //newForm = newForm.replace(/__clinicalHistory__/g, 0);   //add only one clinical history to the new form
    newForm = newForm.replace(/__paper__/g, 0);   //add only one clinical history to the new form

    //replace origin_option_multi_patient_0_procedure_0_accession_0_part_0_origintag with correct ids
    //origin_option_multi_patient_0_procedure_0_accession_0_part_0_origintag
    var newOriginId = "origin_option_multi_patient_"+patientid+"_procedure_"+procedureid+"_accession_"+accessionid+"_part_"+partid+"_origintag";
    newForm = newForm.replace(/origin_option_multi_patient_0_procedure_0_accession_0_part_0_origintag/g, newOriginId);

    newForm = newForm.replace(/__[a-zA-Z0-9]+__/g, 0); //replace everything what is left __*__ by 0 => replace all array fields by 0

    //remove required
    newForm = newForm.replace(/required="required"/g, "");

    //console.log("newForm="+newForm);

    return newForm;
}

function getHeaderAddBtn( name, ids ) {
    var addbtn = '<button id="form_add_btn_' + name + '_' + ids.join("_") + '" type="button"'+
                    'class="btn btn-default btn-xs add_form_btn pull-right"' +
                    'onclick="addSameForm(\'' + name + '\''+ ',' + ids.join(",") + ')">Add'+
                '</button>';
    return addbtn;
}

function getHeaderDeleteBtn( name, ids, deleteStr ) {
    var deletebtn = '<button id="delete_form_btn_' + name + '_' + ids.join("_") + '" type="button"'+ ' style="margin-right:1%;" ' +
                        'class="btn btn-danger btn-xs delete_form_btn pull-right">' + deleteStr +
                    '</button>';
    return deletebtn;
}

//Helpers
function capitaliseFirstLetter(string)
{
    return string.charAt(0).toUpperCase() + string.slice(1);
}

function getIds( name, patientid, procedureid, accessionid, partid, blockid, slideid, scanid, stainid ) {
    var id = 0;
    var nextName = "";

    var patientidm = patientid;
    var procedureidm = procedureid;
    var accessionidm = accessionid;
    var partidm = partid;
    var blockidm = blockid;
    var slideidm = slideid;
    var scaniddm = scanid;
    var stainidm = stainid;

    var patientidp = patientid;
    var procedureidp = procedureid;
    var accessionidp = accessionid;
    var partidp = partid;
    var blockidp = blockid;
    var slideidp = slideid;
    var scaniddp = scanid;
    var stainidp = stainid;

    var partialId = "";

    var orig = [patientid, procedureid, accessionid, partid, blockid, slideid, scanid, stainid];

    switch(name)
    {
        case "patient":
            patientidm = patientid-1;
            patientid++;
            patientidp = patientid+1;
            id = patientid;
            nextName = "procedure";
            partialId = "";
            break;
        case "procedure":
            procedureidm = procedureid-1;
            procedureid++;
            procedureidp = procedureid+1;
            id = procedureid;
            nextName = "accession";
            partialId = patientid;
            break;
        case "accession":
            accessionidm = accessionid-1;
            accessionid++;
            accessionidp = accessionid+1;
            id = accessionid;
            nextName = "part";
            partialId = patientid+"_"+procedureid;
            break;
        case "part":
            partidm = partid-1;
            partid++;
            partidp = partid+1;
            id = partid;
            nextName = "block";
            partialId = patientid+"_"+procedureid+"_"+accessionid;
            break;
        case "block":
            blockidm = blockid-1;
            blockid++;
            blockidp = blockid+1;
            id = blockid;
            nextName = "slide";
            partialId = patientid+"_"+procedureid+"_"+accessionid+"_"+partid;
            break;
        case "slide":
            slideidm = slideid-1;
            slideid++;
            slideidp = slideid+1;
            id = slideid;
            nextName = "";
            partialId = patientid+"_"+procedureid+"_"+accessionid+"_"+partid+"_"+blockid;
            break;
        case "scan":
            scaniddm = scanid-1;
            scanid++;
            scaniddp = scanid+1;
            id = scanid;
            nextName = "";
            partialId = patientid+"_"+procedureid+"_"+accessionid+"_"+partid+"_"+blockid;
            break;
        case "stain":
            stainidm = stainid-1;
            stainid++;
            stainidp = stainid+1;
            id = stainid;
            nextName = "";
            partialId = patientid+"_"+procedureid+"_"+accessionid+"_"+partid+"_"+blockid;
            break;
        default:
            id = 0;
    }

    var idsArray = [patientid, procedureid, accessionid, partid, blockid, slideid, scanid, stainid];
    var idsArrayM = [patientidm, procedureidm, accessionidm, partidm, blockidm, slideidm, scaniddm, stainidm];
    var idsArrayP = [patientidp, procedureidp, accessionidp, partidp, blockidp, slideidp, scaniddp, stainidp];

    var res_array = {
        'id' : id,
        'orig' : orig,
        'ids' : idsArray,
        'idsm' : idsArrayM,
        'idsp' : idsArrayP,
        'nextName' : nextName,
        'partialId' : partialId
    };

    return res_array;
}

function getIdByName( name, ids ) {
    var id = -1;

    switch(name)
    {
        case "patient":
            id = ids[0];
            break;
        case "procedure":
            id = ids[1];
            break;
        case "accession":
            id = ids[2];
            break;
        case "part":
            id = ids[3];
            break;
        case "block":
            id = ids[4];
            break;
        case "slide":
            id = ids[5];
            break;
        default:
            id = 0;
    }

    return id;
}

//bind listener to the toggle button
function bindToggleBtn( uid ) {
    //console.log("toggle uid="+uid);
    $('#form_body_toggle_'+ uid).on('click', function(e) {
        // prevent the link from creating a "#" on the URL
        //e.preventDefault();
        var className = $(this).attr("class");
        var id = this.id;
        //alert(className);

//        if( className == 'form_body_toggle_btn glyphicon glyphicon-folder-open') {
        if( $(this).hasClass('glyphicon-folder-open') ) {
            $("#"+id).removeClass('glyphicon-folder-open');
            $("#"+id).addClass('glyphicon-folder-close');
        } else {
            $("#"+id).removeClass('glyphicon-folder-close');
            $("#"+id).addClass('glyphicon-folder-open');
        }
    });
}
//multy form delete
function bindDeleteBtn( uid ) {
    //console.log('delete uid='+uid);
    $('#delete_form_btn_'+uid).on('click', function(e) {
        var id = uid;
        //alert("clicked delete!");
        deleteItem(id);
    });
}

function setNavBar() {

    $('ul.li').removeClass('active');

    var full = window.location.pathname;

    var id = 'single';

    if( full.indexOf("/index") !== -1 || full.indexOf("/multi/") !== -1 ) {
        id = 'index';
    }

    if( full.indexOf("multi/clinical") !== -1 ) {
        id = 'clinical';
    }

    if ( full.indexOf("multi/educational") !== -1 ) {
        id = 'edu';
    }

    if( full.indexOf("multi/research") !== -1 ) {
        id = 'res';
    }

    if( full.indexOf("multi/table") !== -1 ) {
        id = 'table';
    }

    if( full.indexOf("/history/") !== -1 ) {
        id = "history";
    }

    if( full.indexOf("/user/listusers") !== -1 ) {
        id = 'admin';
    }
    
    if( full.indexOf("/admin/") !== -1 ) {
        id = 'admin';
    }
    
    if( full.indexOf("/user/") !== -1 ) {
        if( $('#nav-bar-admin').length > 0 ) {
           id = 'admin';
        } else {
           id = 'user';
        }
    }

    //console.log("id="+id);
    //console.info("full="+window.location.pathname+", id="+id + " ?="+full.indexOf("multi/clinical"));

    $('#nav-bar-'+id).addClass('active');
}


function priorityOption() {

    if( $('#priority_option').is(':visible') ) {
        $('#priority_option').collapse('hide');
    }

    $('#oleg_orderformbundle_orderinfotype_priority').change(function(e) {
        e.preventDefault();
        $('#priority_option').collapse('toggle');
    });

    var checked = $('#oleg_orderformbundle_orderinfotype_priority').find('input[type=radio]:checked').val();
    //console.log("checked="+checked);
    if( checked == 'Stat' ) {
        $('#priority_option').collapse('show');
    }
}

//function onCwid(){
//    window.open("http://weill.cornell.edu/its/identity-security/identity/cwid/")
//}

function expandTextarea() {
//    var $element = $('.textarea').get(0);
    var elements = document.getElementsByClassName('textarea');
    for (var i = 0; i < elements.length; ++i) {
        var element = elements[i];
        element.addEventListener('keyup', function() {
            this.style.overflow = 'hidden';
            this.style.height = 0;
            var newH = this.scrollHeight + 10;
            //console.log("cur h="+this.style.height+", newH="+newH);
            this.style.height = newH + 'px';
        }, false);
    }

}


function initDatepicker() {

    var datepickers = $('.input-group.date');

    if( cicle != "show" ) {
        initSingleDatepicker( datepickers );
    
        //make sure the masking is clear when input is cleared by datepicker
        datepickers.datepicker().on("clearDate", function(e){
                var inputField = $(this).find('input');
                //printF(inputField,"Clear input:");
                clearErrorField( inputField );
        });
    }

}

//use "eternicode/bootstrap-datepicker": "dev-master"
//process Datepicker: add or remove click event to the field and its siblings calendar button
//element: null or jquery object. If null, all element with class datepicker will be assign to calendar click event
//remove null or "remove"
function processDatepicker( element, remove ) {

    if( cicle != "show" ) {

        //replace element (input field) by a parent with class .input-group .date
        if( !element ) {
            element = $('.input-group.date');
        } else {
            element = element.closest('.input-group.date');
        }

        //var btn = element.parent().find('.input-group-addon');
        //printF(btn,"Datepicker Btn:");

        if( remove == "remove" ) {
            //printF(element,"Remove datepicker:");
            element.datepicker("remove");

            //make sure the masking is clear when input is cleared by datepicker
            var inputField = element.find('input');
            clearErrorField( inputField );

            //btn.attr( "disabled", true );
        } else {
            initSingleDatepicker(element);
            //btn.attr( "disabled", false );
        }

    }
}

function initSingleDatepicker( datepickerElement ) {
    datepickerElement.datepicker({
        autoclose: true,
        clearBtn: true,
        //todayBtn: "linked",
        todayHighlight: true
    });
}


function printF(element,text) {
    var str = "id="+element.attr("id") + ", class=" + element.attr("class")
    if( text ) {
        str = text + " : " + str;
    }
    console.log(str);
}





/**
 * Created with JetBrains PhpStorm.
 * User: oli2002
 * Date: 2/27/14
 * Time: 12:55 PM
 * To change this template use File | Settings | File Templates.
 */

$(document).ready(function() {

    //idle timeout
    idleTimeout();

    setNavBar();

    initAdd();

    initAllElements(); //init disable all fields

    customCombobox();

    //add diseaseType radio listener for new form
    diseaseTypeListener();
    //render diseaseType radio result for show form
    diseaseTypeRender();

    //take care of buttons for single form
    $("#orderinfo").hide();
    $("#optional_button").hide();

    $('#next_button_multi').on('click', function(event) {
        $("#next_button_multi").hide();
        $("#optional_button").show();
    });

    //priority and disease type options
    priorityOption();
    //originOption();
    //primaryOrganOption();

    //tabs for single form
    //initOptionalParam();

    //multy form toggle button
    $('.form_body_toggle_btn').on('click', function(e) {
        var className = $(this).attr("class");
        var id = this.id;
//        if( className == 'form_body_toggle_btn glyphicon glyphicon-folder-open') {
        if( $(this).hasClass('glyphicon-folder-open') ) {
            $("#"+id).removeClass('glyphicon-folder-open');
            $("#"+id).addClass('glyphicon-folder-close');
        } else {
            $("#"+id).removeClass('glyphicon-folder-close');
            $("#"+id).addClass('glyphicon-folder-open');
        }
    });

    //multy form delete button
    $('.delete_form_btn').on('click', function(e) {
        //alert("on click");
        // prevent the link from creating a "#" on the URL
        //e.preventDefault();
        //alert( this.id );
        var id = this.id;
        //$('#formpanel_'+id).remove();
        deleteItem(id);
    });

    //tooltip
    $(".element-with-tooltip").tooltip();

});
/**
 * Created with JetBrains PhpStorm.
 * User: oli2002
 * Date: 2/5/14
 * Time: 12:29 PM
 * To change this template use File | Settings | File Templates.
 */


var _reload_page_after_modal = 0;

$(document).ready(function() {

   $('.comment_modal_close').on( "click", function(){
       cleanModal();
   });

    _reload_page_after_modal = $('#reload_page_after_modal').val();

    //admin: copy combobox to textfield
    $('.modal').find('.combobox').on("change", function(e) {

        var data = $(this).select2('data');
        //console.log('data='+data);
        if( data ) {
            var text = $(this).closest(".modal-body").find('textarea').val();
            //console.log('text='+text);
            if( text != '' ) {
                text = text + '\n';
            }
            text = text + data.text+'.';
            $(this).closest(".modal-body").find('textarea').val(text);
        } else {
            //var text = '';
        }

    });


//    $('.comments-nav').on( "click", function(){
//        $('.order-status-filter').select2('val','With Comments');
//        $('.order-filter-btn').trigger("click");
//    });

});

function submitNewComment(id) {

    var urlBase = $("#baseurl").val();
    var urlCommentSubmit = "http://"+urlBase+"/history/order/create/";

    var text = $('#addComment_'+id).find('.textarea').val();

    if( $('#modal-processor-comment').select2('data') ) {
        var selectednote = $('#modal-processor-comment').select2('data').text;
    } else {
        var selectednote = "";
    }

    //console.log("urlCommentSubmit="+urlCommentSubmit+", text="+text + ", selectednote="+selectednote);

    var comment_modal = $('#addComment_'+id);

    $.ajax({
        url: urlCommentSubmit,
        type: 'POST',
        data: {id: id, selectednote: selectednote, text: text},
        success: function (data) {
            //console.log("OK submit a new comment");
            comment_modal.modal('hide');
            cleanModal();
            if( _reload_page_after_modal == '1' ) {
                window.parent.location.reload();
            }
        },
        error: function () {
            //console.log("Error submit a new comment");
            var errormsg = '<div class="alert alert-danger">Error submitting a new comment</div>';
            $('#modal_error_'+id).html(errormsg);
            return false;
            //comment_modal.modal('hide');
        }
    });

}

function cleanModal() {
    $(".modal_error_div").html('');
    $(".modal-body").find('textarea').val('');
    $('#modal-processor-comment').select2('data',null);
    //console.log("close: clean modal");
    //$(this).closest('.modal').find('.modal_error_div').html('');
}
/*
 * jQuery Idle Timeout 1.2
 * Copyright (c) 2011 Eric Hynds
 *
 * http://www.erichynds.com/jquery/a-new-and-improved-jquery-idle-timeout-plugin/
 *
 * Depends:
 *  - jQuery 1.4.2+
 *  - jQuery Idle Timer (by Paul Irish, http://paulirish.com/2009/jquery-idletimer-plugin/)
 *
 * Dual licensed under the MIT and GPL licenses:
 *   http://www.opensource.org/licenses/mit-license.php
 *   http://www.gnu.org/licenses/gpl.html
 *
*/

(function($, win){
	
	var idleTimeout = {
		init: function( element, resume, options ){
			var self = this, elem;

			this.warning = elem = $(element);
			this.resume = $(resume);
			this.options = options;
			this.countdownOpen = false;
			this.failedRequests = options.failedRequests;
			this._startTimer();
      		this.title = document.title;
			
			// expose obj to data cache so peeps can call internal methods
			$.data( elem[0], 'idletimeout', this );
			
			// start the idle timer
			$.idleTimer(options.idleAfter * 1000);
			
			// once the user becomes idle
			$(document).bind("idle.idleTimer", function(){
				
				// if the user is idle and a countdown isn't already running
				if( $.data(document, 'idleTimer') === 'idle' && !self.countdownOpen ){
					self._stopTimer();
					self.countdownOpen = true;
					self._idle();
				}
			});
			
			// bind continue link
			this.resume.bind("click", function(e){
				e.preventDefault();
				
				win.clearInterval(self.countdown); // stop the countdown
				self.countdownOpen = false; // stop countdown
				self._startTimer(); // start up the timer again
				self._keepAlive( false ); // ping server
				options.onResume.call( self.warning ); // call the resume callback
			});
		},
		
		_idle: function(){
			var self = this,
				options = this.options,
				warning = this.warning[0],
				counter = options.warningLength;
				
			// fire the onIdle function
			options.onIdle.call(warning);
			
			// set inital value in the countdown placeholder
			options.onCountdown.call(warning, counter);
			
			// create a timer that runs every second
			this.countdown = win.setInterval(function(){
				if(--counter === 0){
					window.clearInterval(self.countdown);
					options.onTimeout.call(warning);
				} else {
					options.onCountdown.call(warning, counter);
          document.title = options.titleMessage.replace('%s', counter) + self.title;
				}
			}, 1000);
		},
		
		_startTimer: function(){
			var self = this;

			this.timer = win.setTimeout(function(){
				self._keepAlive();
			}, this.options.pollingInterval * 1000);
		},
		
		_stopTimer: function(){
			// reset the failed requests counter
			this.failedRequests = this.options.failedRequests;
			win.clearTimeout(this.timer);
		},
		
		_keepAlive: function( recurse ){
			var self = this,
				options = this.options;
				
			//Reset the title to what it was.
			document.title = self.title;
			
			// assume a startTimer/keepAlive loop unless told otherwise
			if( typeof recurse === "undefined" ){
				recurse = true;
			}
			
			// if too many requests failed, abort
			if( !this.failedRequests ){
				this._stopTimer();
				options.onAbort.call( this.warning[0] );
				return;
			}
			
			$.ajax({
				timeout: options.AJAXTimeout,
				url: options.keepAliveURL,
				error: function(){
					self.failedRequests--;
				},
				success: function(response){
					if($.trim(response) !== options.serverResponseEquals){
						self.failedRequests--;
					}
				},
				complete: function(){
					if( recurse ){
						self._startTimer();
					}
				}
			});
		}
	};
	
	// expose
	$.idleTimeout = function(element, resume, options){
		idleTimeout.init( element, resume, $.extend($.idleTimeout.options, options) );
		return this;
	};
	
	// options
	$.idleTimeout.options = {
		// number of seconds after user is idle to show the warning
		warningLength: 30,
		
		// url to call to keep the session alive while the user is active
		keepAliveURL: "",
		
		// the response from keepAliveURL must equal this text:
		serverResponseEquals: "OK",
		
		// user is considered idle after this many seconds.  10 minutes default
		idleAfter: 600,
		
		// a polling request will be sent to the server every X seconds
		pollingInterval: 60,
		
		// number of failed polling requests until we abort this script
		failedRequests: 5,
		
		// the $.ajax timeout in MILLISECONDS!
		AJAXTimeout: 250,
		
		// %s will be replaced by the counter value
    	titleMessage: 'Warning: %s seconds until log out | ',
		
		/*
			Callbacks
			"this" refers to the element found by the first selector passed to $.idleTimeout.
		*/
		// callback to fire when the session times out
		onTimeout: $.noop,
		
		// fires when the user becomes idle
		onIdle: $.noop,
		
		// fires during each second of warningLength
		onCountdown: $.noop,
		
		// fires when the user resumes the session
		onResume: $.noop,
		
		// callback to fire when the script is aborted due to too many failed requests
		onAbort: $.noop
	};
	
})(jQuery, window);
/*
 * jQuery idleTimer plugin
 * version 0.8.092209
 * by Paul Irish. 
 *   http://github.com/paulirish/yui-misc/tree/
 * MIT license
 
 * adapted from YUI idle timer by nzakas:
 *   http://github.com/nzakas/yui-misc/
 
 
 * Copyright (c) 2009 Nicholas C. Zakas
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

(function($){

$.idleTimer = function f(newTimeout){

    //$.idleTimer.tId = -1     //timeout ID

    var idle    = false,        //indicates if the user is idle
        enabled = true,        //indicates if the idle timer is enabled
        timeout = 30000,        //the amount of time (ms) before the user is considered idle
        events  = 'mousemove keydown DOMMouseScroll mousewheel mousedown', // activity is one of these events
      //f.olddate = undefined, // olddate used for getElapsedTime. stored on the function
        
    /* (intentionally not documented)
     * Toggles the idle state and fires an appropriate event.
     * @return {void}
     */
    toggleIdleState = function(){
    
        //toggle the state
        idle = !idle;
        
        // reset timeout counter
        f.olddate = +new Date;
        
        //fire appropriate event
        $(document).trigger(  $.data(document,'idleTimer', idle ? "idle" : "active" )  + '.idleTimer');            
    },

    /**
     * Stops the idle timer. This removes appropriate event handlers
     * and cancels any pending timeouts.
     * @return {void}
     * @method stop
     * @static
     */         
    stop = function(){
    
        //set to disabled
        enabled = false;
        
        //clear any pending timeouts
        clearTimeout($.idleTimer.tId);
        
        //detach the event handlers
        $(document).unbind('.idleTimer');
    },
    
    
    /* (intentionally not documented)
     * Handles a user event indicating that the user isn't idle.
     * @param {Event} event A DOM2-normalized event object.
     * @return {void}
     */
    handleUserEvent = function(){
    
        //clear any existing timeout
        clearTimeout($.idleTimer.tId);
        
        
        
        //if the idle timer is enabled
        if (enabled){
        
          
            //if it's idle, that means the user is no longer idle
            if (idle){
                toggleIdleState();           
            } 
        
            //set a new timeout
            $.idleTimer.tId = setTimeout(toggleIdleState, timeout);
            
        }    
     };
    
      
    /**
     * Starts the idle timer. This adds appropriate event handlers
     * and starts the first timeout.
     * @param {int} newTimeout (Optional) A new value for the timeout period in ms.
     * @return {void}
     * @method $.idleTimer
     * @static
     */ 
    
    
    f.olddate = f.olddate || +new Date;
    
    //assign a new timeout if necessary
    if (typeof newTimeout == "number"){
        timeout = newTimeout;
    } else if (newTimeout === 'destroy') {
        stop();
        return this;  
    } else if (newTimeout === 'getElapsedTime'){
        return (+new Date) - f.olddate;
    }
    
    //assign appropriate event handlers
    $(document).bind($.trim((events+' ').split(' ').join('.idleTimer ')),handleUserEvent);
    
    
    //set a timeout to toggle state
    $.idleTimer.tId = setTimeout(toggleIdleState, timeout);
    
    // assume the user is active for the first x seconds.
    $.data(document,'idleTimer',"active");
      
    

    
}; // end of $.idleTimer()

    

})(jQuery);

/**
 * Created with JetBrains PhpStorm.
 * User: oli2002
 * Date: 2/10/14
 * Time: 10:42 AM
 * To change this template use File | Settings | File Templates.
 */

var _idleAfter = 0;

//https://github.com/ehynds/jquery-idle-timeout
function idleTimeout() {

    //get max idle time from server by ajax
    $.ajax({
        url: "http://"+urlBase+"/getmaxidletime/",
        type: 'GET',
        //contentType: 'application/json',
        //dataType: 'json',
        async: false,
        success: function (data) {
            //console.debug("data="+data);
            _idleAfter = data;
        },
        error: function () {
            console.debug("error data="+data);
            _idleAfter = 0;
        }
    });

    // cache a reference to the countdown element so we don't have to query the DOM for it on each ping.
    var $countdown = $("#dialog-countdown");

    var urlCommonIdleTimeout = "http://"+urlBase+"/keepalive/";
    //var urlIdleTimeoutLogout = "http://"+urlBase+"/idlelogout";

    //pollingInterval: 7200 sec, //how often to call keepalive. If set to some big number (i.e. 2 hours) then we will not notify kernel to update session getLastUsed()
    //idleAfter: 1800 sec => 30min*60sec =
    //failedRequests: 1     //if return will no equal 'OK', then failed requests counter will increment to one and compare to failedRequests. If more, then page will forward to urlIdleTimeoutLogout

    // start the idle timer plugin
    $.idleTimeout('#idle-timeout', '#idle-timeout-keepworking', {
        AJAXTimeout: null,
        failedRequests: 1,
        idleAfter: _idleAfter,
        warningLength: 30,
        pollingInterval: _idleAfter-50,
        keepAliveURL: urlCommonIdleTimeout,
        serverResponseEquals: 'OK',
        onTimeout: function(){
            //$('#next_button_multi').trigger('click');
            keepWorking();
            $('#save_order_onidletimeout_btn').show();
            //console.log("on timeout. len="+$('#save_order_onidletimeout_btn').length);

            if( $('#save_order_onidletimeout_btn').length > 0 &&
                ( cicle == "new" || cicle == "amend" || cicle == "edit" )
            ) {
                //console.log("save!!!!!!!!!!!");
                //save if all fields are not empty; don't validate
                $('#save_order_onidletimeout_btn').trigger('click');
            } else {
                //console.log("logout");
                idlelogout();
            }
        },
        onIdle: function(){
            //console.log("on idle");
            $('#idle-timeout').modal('show');
        },
        onCountdown: function(counter){
            //console.log("on Countdown");
            $countdown.html(counter); // update the counter
        },
        onAbort: function(){
            idlelogout();
        }
    });
}

function keepWorking() {
    //console.log("keep working");
    $('#idle-timeout').modal('hide');
}

function logoff() {
    //console.log("logoff");
    var urlRegularLogout = "http://"+urlBase+"/logout";
    window.location = urlRegularLogout;
}

function idlelogout() {
    //console.log("logoff");
//    var str = "";
//    if( saveorder && saveorder == 'saveorder'  ) {
//        str = "?opt=saveorder";
//    }
    var urlIdleTimeoutLogout = "http://"+urlBase+"/idlelogout"; //+str;
    //console.log("urlIdleTimeoutLogout="+urlIdleTimeoutLogout);
    window.location = urlIdleTimeoutLogout;
}

/**
 * Created with JetBrains PhpStorm.
 * User: oli2002
 * Date: 2/12/14
 * Time: 3:36 PM
 * To change this template use File | Settings | File Templates.
 */


$(document).ready(function() {

    var fields = new Array();
    for( var i=0; i<500; i++ ) {
        var row = [i,10, 11, 12, 13];
        fields.push(row);
    }

    var data = [
        ["", "Accession Type", "Accession Number", "Part Name", "Block Name"],
        //fields,
        ["2008", 10, 11, 12, 13],
        ["2009", 20, 11, 14, 13],
        ["2010", 30, 15, 12, 13]
    ];

//    $("#multi-dataTable").handsontable({
//        data: data,
//        startRows: 6,
//        startCols: 8
//    });

});
/* ============================================================
 * Bootstrap: rowlink.js v3.1.0
 * http://jasny.github.io/bootstrap/javascript/#rowlink
 * ============================================================
 * Copyright 2012-2014 Arnold Daniels
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ============================================================ */

+function ($) { "use strict";

  var Rowlink = function (element, options) {
    this.$element = $(element)
    this.options = $.extend({}, Rowlink.DEFAULTS, options)
    
    this.$element.on('click.bs.rowlink', 'td:not(.rowlink-skip)', $.proxy(this.click, this))
  }

  Rowlink.DEFAULTS = {
    target: "a"
  }

  Rowlink.prototype.click = function(e) {
    var target = $(e.currentTarget).closest('tr').find(this.options.target)[0]
    if ($(e.target)[0] === target) return
    
    e.preventDefault();
    
    if (target.click) {
      target.click()
    } else if (document.createEvent) {
      var evt = document.createEvent("MouseEvents"); 
      evt.initMouseEvent("click", true, true, window, 0, 0, 0, 0, 0, false, false, false, false, 0, null); 
      target.dispatchEvent(evt);
    }
  }

  
  // ROWLINK PLUGIN DEFINITION
  // ===========================

  var old = $.fn.rowlink

  $.fn.rowlink = function (options) {
    return this.each(function () {
      var $this = $(this)
      var data = $this.data('rowlink')
      if (!data) $this.data('rowlink', (data = new Rowlink(this, options)))
    })
  }

  $.fn.rowlink.Constructor = Rowlink


  // ROWLINK NO CONFLICT
  // ====================

  $.fn.rowlink.noConflict = function () {
    $.fn.rowlink = old
    return this
  }


  // ROWLINK DATA-API
  // ==================

  $(document).on('click.bs.rowlink.data-api', '[data-link="row"]', function (e) {
    var $this = $(this)
    if ($this.data('rowlink')) return
    $this.rowlink($this.data())
    $(e.target).trigger('click.bs.rowlink')
  })
  
}(window.jQuery);
