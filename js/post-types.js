(()=>{var xt=Object.create;var Ae=Object.defineProperty;var jt=Object.getOwnPropertyDescriptor;var wt=Object.getOwnPropertyNames;var Nt=Object.getPrototypeOf,Ct=Object.prototype.hasOwnProperty;var v=(e,t)=>()=>(t||e((t={exports:{}}).exports,t),t.exports);var St=(e,t,r,o)=>{if(t&&typeof t=="object"||typeof t=="function")for(let n of wt(t))!Ct.call(e,n)&&n!==r&&Ae(e,n,{get:()=>t[n],enumerable:!(o=jt(t,n))||o.enumerable});return e};var y=(e,t,r)=>(r=e!=null?xt(Nt(e)):{},St(t||!e||!e.__esModule?Ae(r,"default",{value:e,enumerable:!0}):r,e));var be=v((Qr,qe)=>{qe.exports=wp.element});var ye=v((eo,Fe)=>{Fe.exports=wp.i18n});var j=v((to,Me)=>{Me.exports=React});var ze=v((Wo,Re)=>{function He(e){var t,r,o="";if(typeof e=="string"||typeof e=="number")o+=e;else if(typeof e=="object")if(Array.isArray(e))for(t=0;t<e.length;t++)e[t]&&(r=He(e[t]))&&(o&&(o+=" "),o+=r);else for(t in e)e[t]&&(o&&(o+=" "),o+=t);return o}function Be(){for(var e,t,r=0,o="";r<arguments.length;)(e=arguments[r++])&&(t=He(e))&&(o&&(o+=" "),o+=t);return o}Re.exports=Be,Re.exports.clsx=Be});var re=v((Vo,Je)=>{Je.exports=wp.components});var Ge=v(oe=>{"use strict";var Yt=j(),Gt=Symbol.for("react.element"),Xt=Symbol.for("react.fragment"),Qt=Object.prototype.hasOwnProperty,er=Yt.__SECRET_INTERNALS_DO_NOT_USE_OR_YOU_WILL_BE_FIRED.ReactCurrentOwner,tr={key:!0,ref:!0,__self:!0,__source:!0};function Ye(e,t,r){var o,n={},a=null,s=null;r!==void 0&&(a=""+r),t.key!==void 0&&(a=""+t.key),t.ref!==void 0&&(s=t.ref);for(o in t)Qt.call(t,o)&&!tr.hasOwnProperty(o)&&(n[o]=t[o]);if(e&&e.defaultProps)for(o in t=e.defaultProps,t)n[o]===void 0&&(n[o]=t[o]);return{$$typeof:Gt,type:e,key:a,ref:s,props:n,_owner:er.current}}oe.Fragment=Xt;oe.jsx=Ye;oe.jsxs=Ye});var A=v((Bo,Xe)=>{"use strict";Xe.exports=Ge()});var Qe=v(ae=>{"use strict";Object.defineProperty(ae,"__esModule",{value:!0});ae.default=void 0;var rr=re(),ne=A(),or=function(t){var r=t.content;return(0,ne.jsx)(rr.Tooltip,{text:r,delay:0,children:(0,ne.jsx)("span",{className:"ef-control__tooltip",children:(0,ne.jsx)("svg",{viewBox:"0 0 512 512",xmlns:"http://www.w3.org/2000/svg",children:(0,ne.jsx)("path",{d:"M256,64C150,64,64,150,64,256s86,192,192,192,192-86,192-192S362,64,256,64Zm-6,304a20,20,0,1,1,20-20A20,20,0,0,1,250,368Zm33.44-102C267.23,276.88,265,286.85,265,296a14,14,0,0,1-28,0c0-21.91,10.08-39.33,30.82-53.26C287.1,229.8,298,221.6,298,203.57c0-12.26-7-21.57-21.49-28.46-3.41-1.62-11-3.2-20.34-3.09-11.72.15-20.82,2.95-27.83,8.59C215.12,191.25,214,202.83,214,203a14,14,0,1,1-28-1.35c.11-2.43,1.8-24.32,24.77-42.8,11.91-9.58,27.06-14.56,45-14.78,12.7-.15,24.63,2,32.72,5.82C312.7,161.34,326,180.43,326,203.57,326,237.4,303.39,252.59,283.44,266Z"})})})})},Ho=ae.default=or});var ie=v(se=>{"use strict";Object.defineProperty(se,"__esModule",{value:!0});se.default=void 0;var nr=et(ze()),ar=et(Qe()),h=A();function et(e){return e&&e.__esModule?e:{default:e}}var sr=function(){return(0,h.jsx)("span",{className:"ef-control__tooltip tooltip111222333",children:(0,h.jsx)("svg",{viewBox:"0 0 512 512",xmlns:"http://www.w3.org/2000/svg",children:(0,h.jsx)("path",{d:"M256,64C150,64,64,150,64,256s86,192,192,192,192-86,192-192S362,64,256,64Zm-6,304a20,20,0,1,1,20-20A20,20,0,0,1,250,368Zm33.44-102C267.23,276.88,265,286.85,265,296a14,14,0,0,1-28,0c0-21.91,10.08-39.33,30.82-53.26C287.1,229.8,298,221.6,298,203.57c0-12.26-7-21.57-21.49-28.46-3.41-1.62-11-3.2-20.34-3.09-11.72.15-20.82,2.95-27.83,8.59C215.12,191.25,214,202.83,214,203a14,14,0,1,1-28-1.35c.11-2.43,1.8-24.32,24.77-42.8,11.91-9.58,27.06-14.56,45-14.78,12.7-.15,24.63,2,32.72,5.82C312.7,161.34,326,180.43,326,203.57,326,237.4,303.39,252.59,283.44,266Z"})})})},ir=function(t){var r=t.label,o=r===void 0?"":r,n=t.required,a=n===void 0?!1:n,s=t.tooltip,i=s===void 0?"":s,l=t.description,p=l===void 0?"":l,c=t.id,f=c===void 0?"":c,d=t.className,u=d===void 0?"":d,m=t.children;return(0,h.jsxs)("div",{className:(0,nr.default)("ef-control",u),children:[o&&(0,h.jsxs)("label",{className:"heretest ef-control__label 111222333",htmlFor:f,children:[o.includes("<")?(0,h.jsx)("div",{dangerouslySetInnerHTML:{__html:o}}):o,a&&(0,h.jsx)("span",{className:"ef-control__required",children:"*"}),i&&(0,h.jsx)(ar.default,{content:i}),(0,h.jsx)(sr,{})]}),(0,h.jsxs)("div",{className:"ef-control__input control111222",children:[m,p&&(0,h.jsx)("div",{className:"ef-control__description",dangerouslySetInnerHTML:{__html:p}})]})]})},Jo=se.default=ir});var Ee=v(le=>{"use strict";function W(e){return W=typeof Symbol=="function"&&typeof Symbol.iterator=="symbol"?function(t){return typeof t}:function(t){return t&&typeof Symbol=="function"&&t.constructor===Symbol&&t!==Symbol.prototype?"symbol":typeof t},W(e)}Object.defineProperty(le,"__esModule",{value:!0});le.default=void 0;var lr=ur(ie()),tt=A(),pr=["type","id","name","value","placeholder","onChange"];function ur(e){return e&&e.__esModule?e:{default:e}}function rt(e,t){var r=Object.keys(e);if(Object.getOwnPropertySymbols){var o=Object.getOwnPropertySymbols(e);t&&(o=o.filter(function(n){return Object.getOwnPropertyDescriptor(e,n).enumerable})),r.push.apply(r,o)}return r}function ot(e){for(var t=1;t<arguments.length;t++){var r=arguments[t]!=null?arguments[t]:{};t%2?rt(Object(r),!0).forEach(function(o){cr(e,o,r[o])}):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(r)):rt(Object(r)).forEach(function(o){Object.defineProperty(e,o,Object.getOwnPropertyDescriptor(r,o))})}return e}function cr(e,t,r){return t=fr(t),t in e?Object.defineProperty(e,t,{value:r,enumerable:!0,configurable:!0,writable:!0}):e[t]=r,e}function fr(e){var t=dr(e,"string");return W(t)=="symbol"?t:String(t)}function dr(e,t){if(W(e)!="object"||!e)return e;var r=e[Symbol.toPrimitive];if(r!==void 0){var o=r.call(e,t||"default");if(W(o)!="object")return o;throw new TypeError("@@toPrimitive must return a primitive value.")}return(t==="string"?String:Number)(e)}function mr(e,t){if(e==null)return{};var r=br(e,t),o,n;if(Object.getOwnPropertySymbols){var a=Object.getOwnPropertySymbols(e);for(n=0;n<a.length;n++)o=a[n],!(t.indexOf(o)>=0)&&(!Object.prototype.propertyIsEnumerable.call(e,o)||(r[o]=e[o]))}return r}function br(e,t){if(e==null)return{};var r={},o=Object.keys(e),n,a;for(a=0;a<o.length;a++)n=o[a],!(t.indexOf(n)>=0)&&(r[n]=e[n]);return r}var yr=function(t){var r=t.type,o=r===void 0?"text":r,n=t.id,a=n===void 0?"":n,s=t.name,i=s===void 0?"":s,l=t.value,p=l===void 0?"":l,c=t.placeholder,f=c===void 0?"":c,d=t.onChange,u=mr(t,pr);return(0,tt.jsx)(lr.default,ot(ot({id:a},u),{},{children:(0,tt.jsx)("input",{type:o,id:a,name:i,defaultValue:p,placeholder:f,onChange:d})}))},Go=le.default=yr});var lt=v(pe=>{"use strict";function V(e){return V=typeof Symbol=="function"&&typeof Symbol.iterator=="symbol"?function(t){return typeof t}:function(t){return t&&typeof Symbol=="function"&&t.constructor===Symbol&&t!==Symbol.prototype?"symbol":typeof t},V(e)}Object.defineProperty(pe,"__esModule",{value:!0});pe.default=void 0;var vr=Tr(ie()),S=A(),hr=["id","name","value","placeholder","options","onChange"];function Tr(e){return e&&e.__esModule?e:{default:e}}function nt(e,t){var r=Object.keys(e);if(Object.getOwnPropertySymbols){var o=Object.getOwnPropertySymbols(e);t&&(o=o.filter(function(n){return Object.getOwnPropertyDescriptor(e,n).enumerable})),r.push.apply(r,o)}return r}function at(e){for(var t=1;t<arguments.length;t++){var r=arguments[t]!=null?arguments[t]:{};t%2?nt(Object(r),!0).forEach(function(o){gr(e,o,r[o])}):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(r)):nt(Object(r)).forEach(function(o){Object.defineProperty(e,o,Object.getOwnPropertyDescriptor(r,o))})}return e}function gr(e,t,r){return t=Pr(t),t in e?Object.defineProperty(e,t,{value:r,enumerable:!0,configurable:!0,writable:!0}):e[t]=r,e}function Pr(e){var t=_r(e,"string");return V(t)=="symbol"?t:String(t)}function _r(e,t){if(V(e)!="object"||!e)return e;var r=e[Symbol.toPrimitive];if(r!==void 0){var o=r.call(e,t||"default");if(V(o)!="object")return o;throw new TypeError("@@toPrimitive must return a primitive value.")}return(t==="string"?String:Number)(e)}function st(e,t){return wr(e)||jr(e,t)||xr(e,t)||Or()}function Or(){throw new TypeError(`Invalid attempt to destructure non-iterable instance.
In order to be iterable, non-array objects must have a [Symbol.iterator]() method.`)}function xr(e,t){if(!!e){if(typeof e=="string")return it(e,t);var r=Object.prototype.toString.call(e).slice(8,-1);if(r==="Object"&&e.constructor&&(r=e.constructor.name),r==="Map"||r==="Set")return Array.from(e);if(r==="Arguments"||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(r))return it(e,t)}}function it(e,t){(t==null||t>e.length)&&(t=e.length);for(var r=0,o=new Array(t);r<t;r++)o[r]=e[r];return o}function jr(e,t){var r=e==null?null:typeof Symbol<"u"&&e[Symbol.iterator]||e["@@iterator"];if(r!=null){var o,n,a,s,i=[],l=!0,p=!1;try{if(a=(r=r.call(e)).next,t===0){if(Object(r)!==r)return;l=!1}else for(;!(l=(o=a.call(r)).done)&&(i.push(o.value),i.length!==t);l=!0);}catch(c){p=!0,n=c}finally{try{if(!l&&r.return!=null&&(s=r.return(),Object(s)!==s))return}finally{if(p)throw n}}return i}}function wr(e){if(Array.isArray(e))return e}function Nr(e,t){if(e==null)return{};var r=Cr(e,t),o,n;if(Object.getOwnPropertySymbols){var a=Object.getOwnPropertySymbols(e);for(n=0;n<a.length;n++)o=a[n],!(t.indexOf(o)>=0)&&(!Object.prototype.propertyIsEnumerable.call(e,o)||(r[o]=e[o]))}return r}function Cr(e,t){if(e==null)return{};var r={},o=Object.keys(e),n,a;for(a=0;a<o.length;a++)n=o[a],!(t.indexOf(n)>=0)&&(r[n]=e[n]);return r}var Sr=function(t){var r=t.id,o=r===void 0?"":r,n=t.name,a=n===void 0?"":n,s=t.value,i=s===void 0?"":s,l=t.placeholder,p=l===void 0?"-":l,c=t.options,f=t.onChange,d=Nr(t,hr);return(0,S.jsx)(vr.default,at(at({id:o},d),{},{children:(0,S.jsxs)("select",{id:o,name:a,defaultValue:i,onChange:f,children:[(0,S.jsx)("option",{value:"",children:p}),Array.isArray(c)?c.map(function(u){return u.options?(0,S.jsx)("optgroup",{label:u.label,children:Array.isArray(u.options)?u.options.map(function(m){return(0,S.jsx)("option",{value:m.value,children:m.label},m.value)}):Object.entries(u.options).map(function(m){var g=st(m,2),b=g[0],P=g[1];return(0,S.jsx)("option",{value:b,children:P},b)})},u.label):(0,S.jsx)("option",{value:u.value,children:u.label},u.value)}):Object.entries(c).map(function(u){var m=st(u,2),g=m[0],b=m[1];return(0,S.jsx)("option",{value:g,children:b},g)})]})}))},Qo=pe.default=Sr});var ut=v(ue=>{"use strict";Object.defineProperty(ue,"__esModule",{value:!0});ue.default=void 0;var Ir=Er(Ee()),Rr=A();function Er(e){return e&&e.__esModule?e:{default:e}}function Z(e){return Z=typeof Symbol=="function"&&typeof Symbol.iterator=="symbol"?function(t){return typeof t}:function(t){return t&&typeof Symbol=="function"&&t.constructor===Symbol&&t!==Symbol.prototype?"symbol":typeof t},Z(e)}function pt(e,t){var r=Object.keys(e);if(Object.getOwnPropertySymbols){var o=Object.getOwnPropertySymbols(e);t&&(o=o.filter(function(n){return Object.getOwnPropertyDescriptor(e,n).enumerable})),r.push.apply(r,o)}return r}function Dr(e){for(var t=1;t<arguments.length;t++){var r=arguments[t]!=null?arguments[t]:{};t%2?pt(Object(r),!0).forEach(function(o){Lr(e,o,r[o])}):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(r)):pt(Object(r)).forEach(function(o){Object.defineProperty(e,o,Object.getOwnPropertyDescriptor(r,o))})}return e}function Lr(e,t,r){return t=$r(t),t in e?Object.defineProperty(e,t,{value:r,enumerable:!0,configurable:!0,writable:!0}):e[t]=r,e}function $r(e){var t=Ar(e,"string");return Z(t)=="symbol"?t:String(t)}function Ar(e,t){if(Z(e)!="object"||!e)return e;var r=e[Symbol.toPrimitive];if(r!==void 0){var o=r.call(e,t||"default");if(Z(o)!="object")return o;throw new TypeError("@@toPrimitive must return a primitive value.")}return(t==="string"?String:Number)(e)}var qr=function(t){return(0,Rr.jsx)(Ir.default,Dr({type:"text"},t))},tn=ue.default=qr});var fe=v(q=>{"use strict";Object.defineProperty(q,"__esModule",{value:!0});Object.defineProperty(q,"Control",{enumerable:!0,get:function(){return Fr.default}});Object.defineProperty(q,"Input",{enumerable:!0,get:function(){return Mr.default}});Object.defineProperty(q,"Select",{enumerable:!0,get:function(){return kr.default}});Object.defineProperty(q,"Text",{enumerable:!0,get:function(){return Kr.default}});var Fr=ce(ie()),Mr=ce(Ee()),kr=ce(lt()),Kr=ce(ut());function ce(e){return e&&e.__esModule?e:{default:e}}});var M=y(be()),F=y(ye());var X=y(j());var K=y(j());function ve(e){return function(t){return!!t.type&&t.type.tabsRole===e}}var w=ve("Tab"),R=ve("TabList"),N=ve("TabPanel");function he(){return he=Object.assign||function(e){for(var t=1;t<arguments.length;t++){var r=arguments[t];for(var o in r)Object.prototype.hasOwnProperty.call(r,o)&&(e[o]=r[o])}return e},he.apply(this,arguments)}function It(e){return w(e)||R(e)||N(e)}function H(e,t){return K.Children.map(e,function(r){return r===null?null:It(r)?t(r):r.props&&r.props.children&&typeof r.props.children=="object"?(0,K.cloneElement)(r,he({},r.props,{children:H(r.props.children,t)})):r})}function U(e,t){return K.Children.forEach(e,function(r){r!==null&&(w(r)||N(r)?t(r):r.props&&r.props.children&&typeof r.props.children=="object"&&(R(r)&&t(r),U(r.props.children,t)))})}var x=y(j());function ke(e){var t,r,o="";if(typeof e=="string"||typeof e=="number")o+=e;else if(typeof e=="object")if(Array.isArray(e))for(t=0;t<e.length;t++)e[t]&&(r=ke(e[t]))&&(o&&(o+=" "),o+=r);else for(t in e)e[t]&&(o&&(o+=" "),o+=t);return o}function Rt(){for(var e,t,r=0,o="";r<arguments.length;)(e=arguments[r++])&&(t=ke(e))&&(o&&(o+=" "),o+=t);return o}var C=Rt;var Et=0;function z(){return"react-tabs-"+Et++}function J(e){var t=0;return U(e,function(r){w(r)&&t++}),t}function Ke(e){var t=0;return U(e,function(r){N(r)&&t++}),t}var Dt=["children","className","disabledTabClassName","domRef","focus","forceRenderTabPanel","onSelect","selectedIndex","selectedTabClassName","selectedTabPanelClassName","environment","disableUpDownKeys"];function Te(){return Te=Object.assign||function(e){for(var t=1;t<arguments.length;t++){var r=arguments[t];for(var o in r)Object.prototype.hasOwnProperty.call(r,o)&&(e[o]=r[o])}return e},Te.apply(this,arguments)}function Lt(e,t){if(e==null)return{};var r={},o=Object.keys(e),n,a;for(a=0;a<o.length;a++)n=o[a],!(t.indexOf(n)>=0)&&(r[n]=e[n]);return r}function $t(e,t){e.prototype=Object.create(t.prototype),e.prototype.constructor=e,ge(e,t)}function ge(e,t){return ge=Object.setPrototypeOf||function(o,n){return o.__proto__=n,o},ge(e,t)}function We(e){return e&&"getAttribute"in e}function Ue(e){return We(e)&&e.getAttribute("data-rttab")}function I(e){return We(e)&&e.getAttribute("aria-disabled")==="true"}var Y;function At(e){var t=e||(typeof window<"u"?window:void 0);try{Y=!!(typeof t<"u"&&t.document&&t.document.activeElement)}catch{Y=!1}}var G=function(e){$t(t,e);function t(){for(var o,n=arguments.length,a=new Array(n),s=0;s<n;s++)a[s]=arguments[s];return o=e.call.apply(e,[this].concat(a))||this,o.tabNodes=[],o.handleKeyDown=function(i){var l=o.props,p=l.direction,c=l.disableUpDownKeys;if(o.isTabFromContainer(i.target)){var f=o.props.selectedIndex,d=!1,u=!1;(i.keyCode===32||i.keyCode===13)&&(d=!0,u=!1,o.handleClick(i)),i.keyCode===37||!c&&i.keyCode===38?(p==="rtl"?f=o.getNextTab(f):f=o.getPrevTab(f),d=!0,u=!0):i.keyCode===39||!c&&i.keyCode===40?(p==="rtl"?f=o.getPrevTab(f):f=o.getNextTab(f),d=!0,u=!0):i.keyCode===35?(f=o.getLastTab(),d=!0,u=!0):i.keyCode===36&&(f=o.getFirstTab(),d=!0,u=!0),d&&i.preventDefault(),u&&o.setSelected(f,i)}},o.handleClick=function(i){var l=i.target;do if(o.isTabFromContainer(l)){if(I(l))return;var p=[].slice.call(l.parentNode.children).filter(Ue).indexOf(l);o.setSelected(p,i);return}while((l=l.parentNode)!=null)},o}var r=t.prototype;return r.setSelected=function(n,a){if(!(n<0||n>=this.getTabsCount())){var s=this.props,i=s.onSelect,l=s.selectedIndex;i(n,l,a)}},r.getNextTab=function(n){for(var a=this.getTabsCount(),s=n+1;s<a;s++)if(!I(this.getTab(s)))return s;for(var i=0;i<n;i++)if(!I(this.getTab(i)))return i;return n},r.getPrevTab=function(n){for(var a=n;a--;)if(!I(this.getTab(a)))return a;for(a=this.getTabsCount();a-- >n;)if(!I(this.getTab(a)))return a;return n},r.getFirstTab=function(){for(var n=this.getTabsCount(),a=0;a<n;a++)if(!I(this.getTab(a)))return a;return null},r.getLastTab=function(){for(var n=this.getTabsCount();n--;)if(!I(this.getTab(n)))return n;return null},r.getTabsCount=function(){var n=this.props.children;return J(n)},r.getPanelsCount=function(){var n=this.props.children;return Ke(n)},r.getTab=function(n){return this.tabNodes["tabs-"+n]},r.getChildren=function(){var n=this,a=0,s=this.props,i=s.children,l=s.disabledTabClassName,p=s.focus,c=s.forceRenderTabPanel,f=s.selectedIndex,d=s.selectedTabClassName,u=s.selectedTabPanelClassName,m=s.environment;this.tabIds=this.tabIds||[],this.panelIds=this.panelIds||[];for(var g=this.tabIds.length-this.getTabsCount();g++<0;)this.tabIds.push(z()),this.panelIds.push(z());return H(i,function(b){var P=b;if(R(b)){var _=0,B=!1;Y==null&&At(m),Y&&(B=x.default.Children.toArray(b.props.children).filter(w).some(function($e,de){var k=m||(typeof window<"u"?window:void 0);return k&&k.document.activeElement===n.getTab(de)})),P=(0,x.cloneElement)(b,{children:H(b.props.children,function($e){var de="tabs-"+_,k=f===_,me={tabRef:function(Ot){n.tabNodes[de]=Ot},id:n.tabIds[_],panelId:n.panelIds[_],selected:k,focus:k&&(p||B)};return d&&(me.selectedClassName=d),l&&(me.disabledClassName=l),_++,(0,x.cloneElement)($e,me)})})}else if(N(b)){var O={id:n.panelIds[a],tabId:n.tabIds[a],selected:f===a};c&&(O.forceRender=c),u&&(O.selectedClassName=u),a++,P=(0,x.cloneElement)(b,O)}return P})},r.isTabFromContainer=function(n){if(!Ue(n))return!1;var a=n.parentElement;do{if(a===this.node)return!0;if(a.getAttribute("data-rttabs"))break;a=a.parentElement}while(a);return!1},r.render=function(){var n=this,a=this.props,s=a.children,i=a.className,l=a.disabledTabClassName,p=a.domRef,c=a.focus,f=a.forceRenderTabPanel,d=a.onSelect,u=a.selectedIndex,m=a.selectedTabClassName,g=a.selectedTabPanelClassName,b=a.environment,P=a.disableUpDownKeys,_=Lt(a,Dt);return x.default.createElement("div",Te({},_,{className:C(i),onClick:this.handleClick,onKeyDown:this.handleKeyDown,ref:function(O){n.node=O,p&&p(O)},"data-rttabs":!0}),this.getChildren())},t}(x.Component);G.defaultProps={className:"react-tabs",focus:!1};G.propTypes={};var qt=["children","defaultIndex","defaultFocus"];function Ft(e,t){if(e==null)return{};var r={},o=Object.keys(e),n,a;for(a=0;a<o.length;a++)n=o[a],!(t.indexOf(n)>=0)&&(r[n]=e[n]);return r}function Mt(e,t){e.prototype=Object.create(t.prototype),e.prototype.constructor=e,_e(e,t)}function _e(e,t){return _e=Object.setPrototypeOf||function(o,n){return o.__proto__=n,o},_e(e,t)}var kt=0,Pe=1,E=function(e){Mt(t,e);function t(o){var n;return n=e.call(this,o)||this,n.handleSelected=function(a,s,i){var l=n.props.onSelect,p=n.state.mode;if(!(typeof l=="function"&&l(a,s,i)===!1)){var c={focus:i.type==="keydown"};p===Pe&&(c.selectedIndex=a),n.setState(c)}},n.state=t.copyPropsToState(n.props,{},o.defaultFocus),n}t.getDerivedStateFromProps=function(n,a){return t.copyPropsToState(n,a)},t.getModeFromProps=function(n){return n.selectedIndex===null?Pe:kt},t.copyPropsToState=function(n,a,s){s===void 0&&(s=!1);var i={focus:s,mode:t.getModeFromProps(n)};if(i.mode===Pe){var l=Math.max(0,J(n.children)-1),p=null;a.selectedIndex!=null?p=Math.min(a.selectedIndex,l):p=n.defaultIndex||0,i.selectedIndex=p}return i};var r=t.prototype;return r.render=function(){var n=this.props,a=n.children,s=n.defaultIndex,i=n.defaultFocus,l=Ft(n,qt),p=this.state,c=p.focus,f=p.selectedIndex;return l.focus=c,l.onSelect=this.handleSelected,f!=null&&(l.selectedIndex=f),X.default.createElement(G,l,a)},t}(X.Component);E.defaultProps={defaultFocus:!1,forceRenderTabPanel:!1,selectedIndex:null,defaultIndex:null,environment:null,disableUpDownKeys:!1};E.propTypes={};E.tabsRole="Tabs";var Q=y(j());var Kt=["children","className"];function Oe(){return Oe=Object.assign||function(e){for(var t=1;t<arguments.length;t++){var r=arguments[t];for(var o in r)Object.prototype.hasOwnProperty.call(r,o)&&(e[o]=r[o])}return e},Oe.apply(this,arguments)}function Ut(e,t){if(e==null)return{};var r={},o=Object.keys(e),n,a;for(a=0;a<o.length;a++)n=o[a],!(t.indexOf(n)>=0)&&(r[n]=e[n]);return r}function Wt(e,t){e.prototype=Object.create(t.prototype),e.prototype.constructor=e,xe(e,t)}function xe(e,t){return xe=Object.setPrototypeOf||function(o,n){return o.__proto__=n,o},xe(e,t)}var D=function(e){Wt(t,e);function t(){return e.apply(this,arguments)||this}var r=t.prototype;return r.render=function(){var n=this.props,a=n.children,s=n.className,i=Ut(n,Kt);return Q.default.createElement("ul",Oe({},i,{className:C(s),role:"tablist"}),a)},t}(Q.Component);D.defaultProps={className:"react-tabs__tab-list"};D.propTypes={};D.tabsRole="TabList";var ee=y(j());var Vt=["children","className","disabled","disabledClassName","focus","id","panelId","selected","selectedClassName","tabIndex","tabRef"];function we(){return we=Object.assign||function(e){for(var t=1;t<arguments.length;t++){var r=arguments[t];for(var o in r)Object.prototype.hasOwnProperty.call(r,o)&&(e[o]=r[o])}return e},we.apply(this,arguments)}function Zt(e,t){if(e==null)return{};var r={},o=Object.keys(e),n,a;for(a=0;a<o.length;a++)n=o[a],!(t.indexOf(n)>=0)&&(r[n]=e[n]);return r}function Bt(e,t){e.prototype=Object.create(t.prototype),e.prototype.constructor=e,Ne(e,t)}function Ne(e,t){return Ne=Object.setPrototypeOf||function(o,n){return o.__proto__=n,o},Ne(e,t)}var je="react-tabs__tab",L=function(e){Bt(t,e);function t(){return e.apply(this,arguments)||this}var r=t.prototype;return r.componentDidMount=function(){this.checkFocus()},r.componentDidUpdate=function(){this.checkFocus()},r.checkFocus=function(){var n=this.props,a=n.selected,s=n.focus;a&&s&&this.node.focus()},r.render=function(){var n,a=this,s=this.props,i=s.children,l=s.className,p=s.disabled,c=s.disabledClassName,f=s.focus,d=s.id,u=s.panelId,m=s.selected,g=s.selectedClassName,b=s.tabIndex,P=s.tabRef,_=Zt(s,Vt);return ee.default.createElement("li",we({},_,{className:C(l,(n={},n[g]=m,n[c]=p,n)),ref:function(O){a.node=O,P&&P(O)},role:"tab",id:d,"aria-selected":m?"true":"false","aria-disabled":p?"true":"false","aria-controls":u,tabIndex:b||(m?"0":null),"data-rttab":!0}),i)},t}(ee.Component);L.defaultProps={className:je,disabledClassName:je+"--disabled",focus:!1,id:null,panelId:null,selected:!1,selectedClassName:je+"--selected"};L.propTypes={};L.tabsRole="Tab";var te=y(j());var Ht=["children","className","forceRender","id","selected","selectedClassName","tabId"];function Ce(){return Ce=Object.assign||function(e){for(var t=1;t<arguments.length;t++){var r=arguments[t];for(var o in r)Object.prototype.hasOwnProperty.call(r,o)&&(e[o]=r[o])}return e},Ce.apply(this,arguments)}function zt(e,t){if(e==null)return{};var r={},o=Object.keys(e),n,a;for(a=0;a<o.length;a++)n=o[a],!(t.indexOf(n)>=0)&&(r[n]=e[n]);return r}function Jt(e,t){e.prototype=Object.create(t.prototype),e.prototype.constructor=e,Se(e,t)}function Se(e,t){return Se=Object.setPrototypeOf||function(o,n){return o.__proto__=n,o},Se(e,t)}var Ve="react-tabs__tab-panel",$=function(e){Jt(t,e);function t(){return e.apply(this,arguments)||this}var r=t.prototype;return r.render=function(){var n,a=this.props,s=a.children,i=a.className,l=a.forceRender,p=a.id,c=a.selected,f=a.selectedClassName,d=a.tabId,u=zt(a,Ht);return te.default.createElement("div",Ce({},u,{className:C(i,(n={},n[f]=c,n)),role:"tabpanel",id:p,"aria-labelledby":d}),l||c?s:null)},t}(te.Component);$.defaultProps={className:Ve,forceRender:!1,selectedClassName:Ve+"--selected"};$.propTypes={};$.tabsRole="TabPanel";var Ie={},Ze=async(e,t={},r="GET",o=!0)=>{let n=JSON.stringify({apiName:e,params:t,method:r});if(o&&Ie[n])return Ie[n];let a={method:r,headers:{"X-WP-Nonce":ssPostTypes.nonce,"Content-Type":"application/json"}},s=`${ssPostTypes.rest}/slim-seo-post-types/${e}`,i=new URLSearchParams(t).toString();r==="POST"?a.body=JSON.stringify(t):i&&(s+=ssPostTypes.rest.includes("?")?`&${i}`:`?${i}`);let l=await fetch(s,a).then(p=>p.json());return Ie[n]=l,l};var T=y(ye());var ct=y(fe()),Ur=e=>{let{id:t,label:r,std:o,className:n="",...a}=e;return React.createElement(ct.Control,{className:n,label:r,id:t,...a},React.createElement("div",{className:"ss-input-wrapper"},React.createElement("input",{type:"text",id:t,name:t,defaultValue:o})))},ft=Ur;var dt=y(fe()),mt=y(re()),Wr=e=>{let{id:t,label:r,std:o,className:n="",mediaPopupTitle:a,...s}=e;return React.createElement(dt.Control,{className:n,label:r,id:t,...s},React.createElement("div",{className:"ss-input-wrapper"},React.createElement("input",{type:"text",id:t,name:t,defaultValue:o}),React.createElement(mt.Button,{icon:"format-image",className:"ss-insert-image"})))},De=Wr;var bt=y(fe()),Vr=e=>{let{id:t,label:r,std:o,className:n="",rows:a=2,...s}=e;return React.createElement(bt.Control,{className:n,label:r,id:t,...s},React.createElement("div",{className:"ss-input-wrapper"},React.createElement("textarea",{defaultValue:o,id:t,name:t,rows:a})))},yt=Vr;var Le=y(be());var vt=y(re()),Zr=({content:e})=>React.createElement(vt.Tooltip,{text:e},React.createElement("span",{className:"ef-control__tooltip tooltip"},React.createElement("svg",{viewBox:"0 0 512 512",xmlns:"http://www.w3.org/2000/svg"},React.createElement("path",{d:"M256,64C150,64,64,150,64,256s86,192,192,192,192-86,192-192S362,64,256,64Zm-6,304a20,20,0,1,1,20-20A20,20,0,0,1,250,368Zm33.44-102C267.23,276.88,265,286.85,265,296a14,14,0,0,1-28,0c0-21.91,10.08-39.33,30.82-53.26C287.1,229.8,298,221.6,298,203.57c0-12.26-7-21.57-21.49-28.46-3.41-1.62-11-3.2-20.34-3.09-11.72.15-20.82,2.95-27.83,8.59C215.12,191.25,214,202.83,214,203a14,14,0,1,1-28-1.35c.11-2.43,1.8-24.32,24.77-42.8,11.91-9.58,27.06-14.56,45-14.78,12.7-.15,24.63,2,32.72,5.82C312.7,161.34,326,180.43,326,203.57,326,237.4,303.39,252.59,283.44,266Z"})))),ht=Zr;var Br=({children:e,label:t,description:r,tooltip:o,className:n="",htmlFor:a="",keyValue:s="",required:i=!1,error:l})=>React.createElement("div",{className:`ss-field ${n}`,key:s},t&&React.createElement("label",{className:"ss-label",htmlFor:a},React.createElement(Le.RawHTML,null,t),o&&React.createElement(ht,{id:a,content:o})),React.createElement("div",{className:"ss-input"},e,r&&React.createElement(Le.RawHTML,{className:"ss-description"},r),l&&React.createElement("p",{className:"og-error"},l))),Tt=Br;var Hr=e=>{let{id:t,label:r,std:o,className:n="",...a}=e;return React.createElement(Tt,{label:r,className:`ss-field--checkbox ${n}`,htmlFor:t,...a},React.createElement("label",{className:"ss-toggle"},React.createElement("input",{type:"checkbox",id:t,name:t,defaultChecked:o,value:!0}),React.createElement("div",{className:"ss-toggle__switch"})))},gt=Hr;var Pt=({baseName:e,option:t,label:r,archive:o=!1})=>React.createElement(React.Fragment,null,React.createElement("h3",{className:"archive-title"},r," ",o?"archive page":"pages"),React.createElement(ft,{id:`${e}[title]`,label:(0,T.__)("Meta title","slim-seo"),std:t.title,tooltip:(0,T.__)("Recommended length: \u2264 60 characters.","slim-seo")}),React.createElement(yt,{id:`${e}[description]`,label:(0,T.__)("Meta description","slim-seo"),std:t.description,tooltip:(0,T.__)("Recommended length: 50-160 characters.","slim-seo")}),React.createElement(De,{id:`${e}[facebook_image]`,label:(0,T.__)("Facebook image","slim-seo"),std:t.facebook_image,mediaPopupTitle:ssPostTypes.mediaPopupTitle,tooltip:(0,T.__)("Recommended size: 1200x600 px. Should have aspect ratio 2:1 with minimum width of 300 px and maximum width of 4096 px.","slim-seo")}),React.createElement(De,{id:`${e}[twitter_image]`,label:(0,T.__)("Twitter image","slim-seo"),std:t.twitter_image,mediaPopupTitle:ssPostTypes.mediaPopupTitle,tooltip:(0,T.__)("Recommended size: 1200x600 px. Should have aspect ratio 2:1 with minimum width of 300 px and maximum width of 4096 px.","slim-seo")})),zr=({id:e,postType:t,option:r,optionArchive:o})=>{let n=`slim_seo[${e}]`,a=`slim_seo[${e}_archive]`;return React.createElement(React.Fragment,null,React.createElement(gt,{id:`${n}[noindex]`,std:r.noindex,label:(0,T.__)("Hide from search results.","slim-seo"),tooltip:(0,T.__)("This setting will apply noindex robots tag to all posts of this post type and exclude the post type from the sitemap.","slim-seo")}),React.createElement(Pt,{baseName:n,option:r,label:t.labels.singular_name}),t.has_archive&&React.createElement(Pt,{baseName:a,option:o,label:t.labels.singular_name,archive:!0}))},_t=zr;var Jr=()=>{let[e,t]=(0,M.useState)([]);return(0,M.useEffect)(()=>{Ze("option").then(t)},[]),Object.entries(ssPostTypes.postTypes).length===0?React.createElement("div",{className:"ss-none"},(0,F.__)("There are no custom post type.","slim-seo")):React.createElement(React.Fragment,null,React.createElement(E,{forceRenderTabPanel:!0,className:"ss-vertical-tabs"},React.createElement(D,null,Object.entries(ssPostTypes.postTypes).map(([r,o])=>React.createElement(L,null,o.label))),Object.entries(ssPostTypes.postTypes).map(([r,o])=>React.createElement($,null,ssPostTypes.unablePostTypes.hasOwnProperty(r)?React.createElement(Yr,{id:r,postType:ssPostTypes.unablePostTypes[r]}):React.createElement(_t,{key:r,id:r,postType:o,option:e[r]||[],optionArchive:e[`${r}_archive`]||[]})))),React.createElement("input",{type:"submit",name:"submit",id:"submit",className:"button button-primary",value:(0,F.__)("Save Changes","slim-seo")}))},Yr=({id:e,postType:t})=>{let{link:r,title:o}=t;return React.createElement(React.Fragment,null,React.createElement("span",null,(0,F.__)("You have a page ","slim-seo")),React.createElement("a",{href:r,target:"_blank",rel:"noopener noreferrer"},o," "),React.createElement("span",null,(0,F.__)(" that has the same slug as the post type archive slug. So WordPress will set it as the archive page for the ","slim-seo")),React.createElement("code",null,e),React.createElement("span",null,(0,F.__)(" post type.","slim-seo")))};(0,M.render)(React.createElement(Jr,null),document.getElementById("ss-post-types"));})();
/**
 * @license React
 * react-jsx-runtime.production.min.js
 *
 * Copyright (c) Facebook, Inc. and its affiliates.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */
