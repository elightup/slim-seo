(()=>{var Et=Object.create;var Fe=Object.defineProperty;var At=Object.getOwnPropertyDescriptor;var Dt=Object.getOwnPropertyNames;var Lt=Object.getPrototypeOf,Ft=Object.prototype.hasOwnProperty;var h=(e,t)=>()=>(t||e((t={exports:{}}).exports,t),t.exports);var $t=(e,t,r,o)=>{if(t&&typeof t=="object"||typeof t=="function")for(let n of Dt(t))!Ft.call(e,n)&&n!==r&&Fe(e,n,{get:()=>t[n],enumerable:!(o=At(t,n))||o.enumerable});return e};var b=(e,t,r)=>(r=e!=null?Et(Lt(e)):{},$t(t||!e||!e.__esModule?Fe(r,"default",{value:e,enumerable:!0}):r,e));var B=h((ao,$e)=>{$e.exports=wp.element});var z=h((so,qe)=>{qe.exports=wp.i18n});var O=h((io,ke)=>{ke.exports=React});var Ze=h((Zo,Se)=>{function ze(e){var t,r,o="";if(typeof e=="string"||typeof e=="number")o+=e;else if(typeof e=="object")if(Array.isArray(e))for(t=0;t<e.length;t++)e[t]&&(r=ze(e[t]))&&(o&&(o+=" "),o+=r);else for(t in e)e[t]&&(o&&(o+=" "),o+=t);return o}function Be(){for(var e,t,r=0,o="";r<arguments.length;)(e=arguments[r++])&&(t=ze(e))&&(o&&(o+=" "),o+=t);return o}Se.exports=Be,Se.exports.clsx=Be});var Ie=h((Go,Ge)=>{Ge.exports=wp.components});var Ye=h(ae=>{"use strict";var nr=O(),ar=Symbol.for("react.element"),sr=Symbol.for("react.fragment"),ir=Object.prototype.hasOwnProperty,lr=nr.__SECRET_INTERNALS_DO_NOT_USE_OR_YOU_WILL_BE_FIRED.ReactCurrentOwner,pr={key:!0,ref:!0,__self:!0,__source:!0};function Je(e,t,r){var o,n={},a=null,s=null;r!==void 0&&(a=""+r),t.key!==void 0&&(a=""+t.key),t.ref!==void 0&&(s=t.ref);for(o in t)ir.call(t,o)&&!pr.hasOwnProperty(o)&&(n[o]=t[o]);if(e&&e.defaultProps)for(o in t=e.defaultProps,t)n[o]===void 0&&(n[o]=t[o]);return{$$typeof:ar,type:e,key:a,ref:s,props:n,_owner:lr.current}}ae.Fragment=sr;ae.jsx=Je;ae.jsxs=Je});var F=h((Yo,Xe)=>{"use strict";Xe.exports=Ye()});var et=h(ie=>{"use strict";Object.defineProperty(ie,"__esModule",{value:!0});ie.default=void 0;var ur=Ie(),se=F(),cr=function(t){var r=t.content;return(0,se.jsx)(ur.Tooltip,{text:r,delay:0,children:(0,se.jsx)("span",{className:"ef-control__tooltip",children:(0,se.jsx)("svg",{viewBox:"0 0 512 512",xmlns:"http://www.w3.org/2000/svg",children:(0,se.jsx)("path",{d:"M256,64C150,64,64,150,64,256s86,192,192,192,192-86,192-192S362,64,256,64Zm-6,304a20,20,0,1,1,20-20A20,20,0,0,1,250,368Zm33.44-102C267.23,276.88,265,286.85,265,296a14,14,0,0,1-28,0c0-21.91,10.08-39.33,30.82-53.26C287.1,229.8,298,221.6,298,203.57c0-12.26-7-21.57-21.49-28.46-3.41-1.62-11-3.2-20.34-3.09-11.72.15-20.82,2.95-27.83,8.59C215.12,191.25,214,202.83,214,203a14,14,0,1,1-28-1.35c.11-2.43,1.8-24.32,24.77-42.8,11.91-9.58,27.06-14.56,45-14.78,12.7-.15,24.63,2,32.72,5.82C312.7,161.34,326,180.43,326,203.57,326,237.4,303.39,252.59,283.44,266Z"})})})})},Xo=ie.default=cr});var pe=h(le=>{"use strict";Object.defineProperty(le,"__esModule",{value:!0});le.default=void 0;var fr=tt(Ze()),dr=tt(et()),I=F();function tt(e){return e&&e.__esModule?e:{default:e}}var mr=function(t){var r=t.label,o=r===void 0?"":r,n=t.required,a=n===void 0?!1:n,s=t.tooltip,i=s===void 0?"":s,l=t.description,p=l===void 0?"":l,u=t.id,f=u===void 0?"":u,d=t.className,c=d===void 0?"":d,m=t.children;return(0,I.jsxs)("div",{className:(0,fr.default)("ef-control",c),children:[o&&(0,I.jsxs)("label",{className:"ef-control__label",htmlFor:f,children:[o.includes("<")?(0,I.jsx)("div",{dangerouslySetInnerHTML:{__html:o}}):o,a&&(0,I.jsx)("span",{className:"ef-control__required",children:"*"}),i&&(0,I.jsx)(dr.default,{content:i})]}),(0,I.jsxs)("div",{className:"ef-control__input",children:[m,p&&(0,I.jsx)("div",{className:"ef-control__description",dangerouslySetInnerHTML:{__html:p}})]})]})},tn=le.default=mr});var Re=h(ue=>{"use strict";function K(e){return K=typeof Symbol=="function"&&typeof Symbol.iterator=="symbol"?function(t){return typeof t}:function(t){return t&&typeof Symbol=="function"&&t.constructor===Symbol&&t!==Symbol.prototype?"symbol":typeof t},K(e)}Object.defineProperty(ue,"__esModule",{value:!0});ue.default=void 0;var br=vr(pe()),rt=F(),yr=["type","id","name","value","placeholder","onChange"];function vr(e){return e&&e.__esModule?e:{default:e}}function ot(e,t){var r=Object.keys(e);if(Object.getOwnPropertySymbols){var o=Object.getOwnPropertySymbols(e);t&&(o=o.filter(function(n){return Object.getOwnPropertyDescriptor(e,n).enumerable})),r.push.apply(r,o)}return r}function nt(e){for(var t=1;t<arguments.length;t++){var r=arguments[t]!=null?arguments[t]:{};t%2?ot(Object(r),!0).forEach(function(o){hr(e,o,r[o])}):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(r)):ot(Object(r)).forEach(function(o){Object.defineProperty(e,o,Object.getOwnPropertyDescriptor(r,o))})}return e}function hr(e,t,r){return t=gr(t),t in e?Object.defineProperty(e,t,{value:r,enumerable:!0,configurable:!0,writable:!0}):e[t]=r,e}function gr(e){var t=Tr(e,"string");return K(t)=="symbol"?t:String(t)}function Tr(e,t){if(K(e)!="object"||!e)return e;var r=e[Symbol.toPrimitive];if(r!==void 0){var o=r.call(e,t||"default");if(K(o)!="object")return o;throw new TypeError("@@toPrimitive must return a primitive value.")}return(t==="string"?String:Number)(e)}function Pr(e,t){if(e==null)return{};var r=_r(e,t),o,n;if(Object.getOwnPropertySymbols){var a=Object.getOwnPropertySymbols(e);for(n=0;n<a.length;n++)o=a[n],!(t.indexOf(o)>=0)&&(!Object.prototype.propertyIsEnumerable.call(e,o)||(r[o]=e[o]))}return r}function _r(e,t){if(e==null)return{};var r={},o=Object.keys(e),n,a;for(a=0;a<o.length;a++)n=o[a],!(t.indexOf(n)>=0)&&(r[n]=e[n]);return r}var xr=function(t){var r=t.type,o=r===void 0?"text":r,n=t.id,a=n===void 0?"":n,s=t.name,i=s===void 0?"":s,l=t.value,p=l===void 0?"":l,u=t.placeholder,f=u===void 0?"":u,d=t.onChange,c=Pr(t,yr);return(0,rt.jsx)(br.default,nt(nt({id:a},c),{},{children:(0,rt.jsx)("input",{type:o,id:a,name:i,defaultValue:p,placeholder:f,onChange:d})}))},on=ue.default=xr});var pt=h(ce=>{"use strict";function V(e){return V=typeof Symbol=="function"&&typeof Symbol.iterator=="symbol"?function(t){return typeof t}:function(t){return t&&typeof Symbol=="function"&&t.constructor===Symbol&&t!==Symbol.prototype?"symbol":typeof t},V(e)}Object.defineProperty(ce,"__esModule",{value:!0});ce.default=void 0;var Or=Nr(pe()),C=F(),jr=["id","name","value","placeholder","options","onChange"];function Nr(e){return e&&e.__esModule?e:{default:e}}function at(e,t){var r=Object.keys(e);if(Object.getOwnPropertySymbols){var o=Object.getOwnPropertySymbols(e);t&&(o=o.filter(function(n){return Object.getOwnPropertyDescriptor(e,n).enumerable})),r.push.apply(r,o)}return r}function st(e){for(var t=1;t<arguments.length;t++){var r=arguments[t]!=null?arguments[t]:{};t%2?at(Object(r),!0).forEach(function(o){wr(e,o,r[o])}):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(r)):at(Object(r)).forEach(function(o){Object.defineProperty(e,o,Object.getOwnPropertyDescriptor(r,o))})}return e}function wr(e,t,r){return t=Cr(t),t in e?Object.defineProperty(e,t,{value:r,enumerable:!0,configurable:!0,writable:!0}):e[t]=r,e}function Cr(e){var t=Sr(e,"string");return V(t)=="symbol"?t:String(t)}function Sr(e,t){if(V(e)!="object"||!e)return e;var r=e[Symbol.toPrimitive];if(r!==void 0){var o=r.call(e,t||"default");if(V(o)!="object")return o;throw new TypeError("@@toPrimitive must return a primitive value.")}return(t==="string"?String:Number)(e)}function it(e,t){return Ar(e)||Er(e,t)||Rr(e,t)||Ir()}function Ir(){throw new TypeError(`Invalid attempt to destructure non-iterable instance.
In order to be iterable, non-array objects must have a [Symbol.iterator]() method.`)}function Rr(e,t){if(!!e){if(typeof e=="string")return lt(e,t);var r=Object.prototype.toString.call(e).slice(8,-1);if(r==="Object"&&e.constructor&&(r=e.constructor.name),r==="Map"||r==="Set")return Array.from(e);if(r==="Arguments"||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(r))return lt(e,t)}}function lt(e,t){(t==null||t>e.length)&&(t=e.length);for(var r=0,o=new Array(t);r<t;r++)o[r]=e[r];return o}function Er(e,t){var r=e==null?null:typeof Symbol<"u"&&e[Symbol.iterator]||e["@@iterator"];if(r!=null){var o,n,a,s,i=[],l=!0,p=!1;try{if(a=(r=r.call(e)).next,t===0){if(Object(r)!==r)return;l=!1}else for(;!(l=(o=a.call(r)).done)&&(i.push(o.value),i.length!==t);l=!0);}catch(u){p=!0,n=u}finally{try{if(!l&&r.return!=null&&(s=r.return(),Object(s)!==s))return}finally{if(p)throw n}}return i}}function Ar(e){if(Array.isArray(e))return e}function Dr(e,t){if(e==null)return{};var r=Lr(e,t),o,n;if(Object.getOwnPropertySymbols){var a=Object.getOwnPropertySymbols(e);for(n=0;n<a.length;n++)o=a[n],!(t.indexOf(o)>=0)&&(!Object.prototype.propertyIsEnumerable.call(e,o)||(r[o]=e[o]))}return r}function Lr(e,t){if(e==null)return{};var r={},o=Object.keys(e),n,a;for(a=0;a<o.length;a++)n=o[a],!(t.indexOf(n)>=0)&&(r[n]=e[n]);return r}var Fr=function(t){var r=t.id,o=r===void 0?"":r,n=t.name,a=n===void 0?"":n,s=t.value,i=s===void 0?"":s,l=t.placeholder,p=l===void 0?"-":l,u=t.options,f=t.onChange,d=Dr(t,jr);return(0,C.jsx)(Or.default,st(st({id:o},d),{},{children:(0,C.jsxs)("select",{id:o,name:a,defaultValue:i,onChange:f,children:[(0,C.jsx)("option",{value:"",children:p}),Array.isArray(u)?u.map(function(c){return c.options?(0,C.jsx)("optgroup",{label:c.label,children:Array.isArray(c.options)?c.options.map(function(m){return(0,C.jsx)("option",{value:m.value,children:m.label},m.value)}):Object.entries(c.options).map(function(m){var g=it(m,2),v=g[0],T=g[1];return(0,C.jsx)("option",{value:v,children:T},v)})},c.label):(0,C.jsx)("option",{value:c.value,children:c.label},c.value)}):Object.entries(u).map(function(c){var m=it(c,2),g=m[0],v=m[1];return(0,C.jsx)("option",{value:g,children:v},g)})]})}))},an=ce.default=Fr});var ct=h(fe=>{"use strict";Object.defineProperty(fe,"__esModule",{value:!0});fe.default=void 0;var $r=kr(Re()),qr=F();function kr(e){return e&&e.__esModule?e:{default:e}}function Q(e){return Q=typeof Symbol=="function"&&typeof Symbol.iterator=="symbol"?function(t){return typeof t}:function(t){return t&&typeof Symbol=="function"&&t.constructor===Symbol&&t!==Symbol.prototype?"symbol":typeof t},Q(e)}function ut(e,t){var r=Object.keys(e);if(Object.getOwnPropertySymbols){var o=Object.getOwnPropertySymbols(e);t&&(o=o.filter(function(n){return Object.getOwnPropertyDescriptor(e,n).enumerable})),r.push.apply(r,o)}return r}function Mr(e){for(var t=1;t<arguments.length;t++){var r=arguments[t]!=null?arguments[t]:{};t%2?ut(Object(r),!0).forEach(function(o){Ur(e,o,r[o])}):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(r)):ut(Object(r)).forEach(function(o){Object.defineProperty(e,o,Object.getOwnPropertyDescriptor(r,o))})}return e}function Ur(e,t,r){return t=Kr(t),t in e?Object.defineProperty(e,t,{value:r,enumerable:!0,configurable:!0,writable:!0}):e[t]=r,e}function Kr(e){var t=Vr(e,"string");return Q(t)=="symbol"?t:String(t)}function Vr(e,t){if(Q(e)!="object"||!e)return e;var r=e[Symbol.toPrimitive];if(r!==void 0){var o=r.call(e,t||"default");if(Q(o)!="object")return o;throw new TypeError("@@toPrimitive must return a primitive value.")}return(t==="string"?String:Number)(e)}var Qr=function(t){return(0,qr.jsx)($r.default,Mr({type:"text"},t))},ln=fe.default=Qr});var W=h($=>{"use strict";Object.defineProperty($,"__esModule",{value:!0});Object.defineProperty($,"Control",{enumerable:!0,get:function(){return Wr.default}});Object.defineProperty($,"Input",{enumerable:!0,get:function(){return Hr.default}});Object.defineProperty($,"Select",{enumerable:!0,get:function(){return Br.default}});Object.defineProperty($,"Text",{enumerable:!0,get:function(){return zr.default}});var Wr=de(pe()),Hr=de(Re()),Br=de(pt()),zr=de(ct());function de(e){return e&&e.__esModule?e:{default:e}}});var xt=h((Pn,_t)=>{_t.exports=wp.apiFetch});var q=b(B()),It=b(z());var ee=b(O());var M=b(O());function ye(e){return function(t){return!!t.type&&t.type.tabsRole===e}}var j=ye("Tab"),R=ye("TabList"),N=ye("TabPanel");function ve(){return ve=Object.assign||function(e){for(var t=1;t<arguments.length;t++){var r=arguments[t];for(var o in r)Object.prototype.hasOwnProperty.call(r,o)&&(e[o]=r[o])}return e},ve.apply(this,arguments)}function qt(e){return j(e)||R(e)||N(e)}function Z(e,t){return M.Children.map(e,function(r){return r===null?null:qt(r)?t(r):r.props&&r.props.children&&typeof r.props.children=="object"?(0,M.cloneElement)(r,ve({},r.props,{children:Z(r.props.children,t)})):r})}function U(e,t){return M.Children.forEach(e,function(r){r!==null&&(j(r)||N(r)?t(r):r.props&&r.props.children&&typeof r.props.children=="object"&&(R(r)&&t(r),U(r.props.children,t)))})}var x=b(O());function Me(e){var t,r,o="";if(typeof e=="string"||typeof e=="number")o+=e;else if(typeof e=="object")if(Array.isArray(e))for(t=0;t<e.length;t++)e[t]&&(r=Me(e[t]))&&(o&&(o+=" "),o+=r);else for(t in e)e[t]&&(o&&(o+=" "),o+=t);return o}function kt(){for(var e,t,r=0,o="";r<arguments.length;)(e=arguments[r++])&&(t=Me(e))&&(o&&(o+=" "),o+=t);return o}var w=kt;var Mt=0;function G(){return"react-tabs-"+Mt++}function J(e){var t=0;return U(e,function(r){j(r)&&t++}),t}function Ue(e){var t=0;return U(e,function(r){N(r)&&t++}),t}var Ut=["children","className","disabledTabClassName","domRef","focus","forceRenderTabPanel","onSelect","selectedIndex","selectedTabClassName","selectedTabPanelClassName","environment","disableUpDownKeys"];function he(){return he=Object.assign||function(e){for(var t=1;t<arguments.length;t++){var r=arguments[t];for(var o in r)Object.prototype.hasOwnProperty.call(r,o)&&(e[o]=r[o])}return e},he.apply(this,arguments)}function Kt(e,t){if(e==null)return{};var r={},o=Object.keys(e),n,a;for(a=0;a<o.length;a++)n=o[a],!(t.indexOf(n)>=0)&&(r[n]=e[n]);return r}function Vt(e,t){e.prototype=Object.create(t.prototype),e.prototype.constructor=e,ge(e,t)}function ge(e,t){return ge=Object.setPrototypeOf||function(o,n){return o.__proto__=n,o},ge(e,t)}function Ve(e){return e&&"getAttribute"in e}function Ke(e){return Ve(e)&&e.getAttribute("data-rttab")}function S(e){return Ve(e)&&e.getAttribute("aria-disabled")==="true"}var Y;function Qt(e){var t=e||(typeof window<"u"?window:void 0);try{Y=!!(typeof t<"u"&&t.document&&t.document.activeElement)}catch{Y=!1}}var X=function(e){Vt(t,e);function t(){for(var o,n=arguments.length,a=new Array(n),s=0;s<n;s++)a[s]=arguments[s];return o=e.call.apply(e,[this].concat(a))||this,o.tabNodes=[],o.handleKeyDown=function(i){var l=o.props,p=l.direction,u=l.disableUpDownKeys;if(o.isTabFromContainer(i.target)){var f=o.props.selectedIndex,d=!1,c=!1;(i.keyCode===32||i.keyCode===13)&&(d=!0,c=!1,o.handleClick(i)),i.keyCode===37||!u&&i.keyCode===38?(p==="rtl"?f=o.getNextTab(f):f=o.getPrevTab(f),d=!0,c=!0):i.keyCode===39||!u&&i.keyCode===40?(p==="rtl"?f=o.getPrevTab(f):f=o.getNextTab(f),d=!0,c=!0):i.keyCode===35?(f=o.getLastTab(),d=!0,c=!0):i.keyCode===36&&(f=o.getFirstTab(),d=!0,c=!0),d&&i.preventDefault(),c&&o.setSelected(f,i)}},o.handleClick=function(i){var l=i.target;do if(o.isTabFromContainer(l)){if(S(l))return;var p=[].slice.call(l.parentNode.children).filter(Ke).indexOf(l);o.setSelected(p,i);return}while((l=l.parentNode)!=null)},o}var r=t.prototype;return r.setSelected=function(n,a){if(!(n<0||n>=this.getTabsCount())){var s=this.props,i=s.onSelect,l=s.selectedIndex;i(n,l,a)}},r.getNextTab=function(n){for(var a=this.getTabsCount(),s=n+1;s<a;s++)if(!S(this.getTab(s)))return s;for(var i=0;i<n;i++)if(!S(this.getTab(i)))return i;return n},r.getPrevTab=function(n){for(var a=n;a--;)if(!S(this.getTab(a)))return a;for(a=this.getTabsCount();a-- >n;)if(!S(this.getTab(a)))return a;return n},r.getFirstTab=function(){for(var n=this.getTabsCount(),a=0;a<n;a++)if(!S(this.getTab(a)))return a;return null},r.getLastTab=function(){for(var n=this.getTabsCount();n--;)if(!S(this.getTab(n)))return n;return null},r.getTabsCount=function(){var n=this.props.children;return J(n)},r.getPanelsCount=function(){var n=this.props.children;return Ue(n)},r.getTab=function(n){return this.tabNodes["tabs-"+n]},r.getChildren=function(){var n=this,a=0,s=this.props,i=s.children,l=s.disabledTabClassName,p=s.focus,u=s.forceRenderTabPanel,f=s.selectedIndex,d=s.selectedTabClassName,c=s.selectedTabPanelClassName,m=s.environment;this.tabIds=this.tabIds||[],this.panelIds=this.panelIds||[];for(var g=this.tabIds.length-this.getTabsCount();g++<0;)this.tabIds.push(G()),this.panelIds.push(G());return Z(i,function(v){var T=v;if(R(v)){var P=0,H=!1;Y==null&&Qt(m),Y&&(H=x.default.Children.toArray(v.props.children).filter(j).some(function(Le,me){var k=m||(typeof window<"u"?window:void 0);return k&&k.document.activeElement===n.getTab(me)})),T=(0,x.cloneElement)(v,{children:Z(v.props.children,function(Le){var me="tabs-"+P,k=f===P,be={tabRef:function(Rt){n.tabNodes[me]=Rt},id:n.tabIds[P],panelId:n.panelIds[P],selected:k,focus:k&&(p||H)};return d&&(be.selectedClassName=d),l&&(be.disabledClassName=l),P++,(0,x.cloneElement)(Le,be)})})}else if(N(v)){var _={id:n.panelIds[a],tabId:n.tabIds[a],selected:f===a};u&&(_.forceRender=u),c&&(_.selectedClassName=c),a++,T=(0,x.cloneElement)(v,_)}return T})},r.isTabFromContainer=function(n){if(!Ke(n))return!1;var a=n.parentElement;do{if(a===this.node)return!0;if(a.getAttribute("data-rttabs"))break;a=a.parentElement}while(a);return!1},r.render=function(){var n=this,a=this.props,s=a.children,i=a.className,l=a.disabledTabClassName,p=a.domRef,u=a.focus,f=a.forceRenderTabPanel,d=a.onSelect,c=a.selectedIndex,m=a.selectedTabClassName,g=a.selectedTabPanelClassName,v=a.environment,T=a.disableUpDownKeys,P=Kt(a,Ut);return x.default.createElement("div",he({},P,{className:w(i),onClick:this.handleClick,onKeyDown:this.handleKeyDown,ref:function(_){n.node=_,p&&p(_)},"data-rttabs":!0}),this.getChildren())},t}(x.Component);X.defaultProps={className:"react-tabs",focus:!1};X.propTypes={};var Wt=["children","defaultIndex","defaultFocus"];function Ht(e,t){if(e==null)return{};var r={},o=Object.keys(e),n,a;for(a=0;a<o.length;a++)n=o[a],!(t.indexOf(n)>=0)&&(r[n]=e[n]);return r}function Bt(e,t){e.prototype=Object.create(t.prototype),e.prototype.constructor=e,Pe(e,t)}function Pe(e,t){return Pe=Object.setPrototypeOf||function(o,n){return o.__proto__=n,o},Pe(e,t)}var zt=0,Te=1,E=function(e){Bt(t,e);function t(o){var n;return n=e.call(this,o)||this,n.handleSelected=function(a,s,i){var l=n.props.onSelect,p=n.state.mode;if(!(typeof l=="function"&&l(a,s,i)===!1)){var u={focus:i.type==="keydown"};p===Te&&(u.selectedIndex=a),n.setState(u)}},n.state=t.copyPropsToState(n.props,{},o.defaultFocus),n}t.getDerivedStateFromProps=function(n,a){return t.copyPropsToState(n,a)},t.getModeFromProps=function(n){return n.selectedIndex===null?Te:zt},t.copyPropsToState=function(n,a,s){s===void 0&&(s=!1);var i={focus:s,mode:t.getModeFromProps(n)};if(i.mode===Te){var l=Math.max(0,J(n.children)-1),p=null;a.selectedIndex!=null?p=Math.min(a.selectedIndex,l):p=n.defaultIndex||0,i.selectedIndex=p}return i};var r=t.prototype;return r.render=function(){var n=this.props,a=n.children,s=n.defaultIndex,i=n.defaultFocus,l=Ht(n,Wt),p=this.state,u=p.focus,f=p.selectedIndex;return l.focus=u,l.onSelect=this.handleSelected,f!=null&&(l.selectedIndex=f),ee.default.createElement(X,l,a)},t}(ee.Component);E.defaultProps={defaultFocus:!1,forceRenderTabPanel:!1,selectedIndex:null,defaultIndex:null,environment:null,disableUpDownKeys:!1};E.propTypes={};E.tabsRole="Tabs";var te=b(O());var Zt=["children","className"];function _e(){return _e=Object.assign||function(e){for(var t=1;t<arguments.length;t++){var r=arguments[t];for(var o in r)Object.prototype.hasOwnProperty.call(r,o)&&(e[o]=r[o])}return e},_e.apply(this,arguments)}function Gt(e,t){if(e==null)return{};var r={},o=Object.keys(e),n,a;for(a=0;a<o.length;a++)n=o[a],!(t.indexOf(n)>=0)&&(r[n]=e[n]);return r}function Jt(e,t){e.prototype=Object.create(t.prototype),e.prototype.constructor=e,xe(e,t)}function xe(e,t){return xe=Object.setPrototypeOf||function(o,n){return o.__proto__=n,o},xe(e,t)}var A=function(e){Jt(t,e);function t(){return e.apply(this,arguments)||this}var r=t.prototype;return r.render=function(){var n=this.props,a=n.children,s=n.className,i=Gt(n,Zt);return te.default.createElement("ul",_e({},i,{className:w(s),role:"tablist"}),a)},t}(te.Component);A.defaultProps={className:"react-tabs__tab-list"};A.propTypes={};A.tabsRole="TabList";var re=b(O());var Yt=["children","className","disabled","disabledClassName","focus","id","panelId","selected","selectedClassName","tabIndex","tabRef"];function je(){return je=Object.assign||function(e){for(var t=1;t<arguments.length;t++){var r=arguments[t];for(var o in r)Object.prototype.hasOwnProperty.call(r,o)&&(e[o]=r[o])}return e},je.apply(this,arguments)}function Xt(e,t){if(e==null)return{};var r={},o=Object.keys(e),n,a;for(a=0;a<o.length;a++)n=o[a],!(t.indexOf(n)>=0)&&(r[n]=e[n]);return r}function er(e,t){e.prototype=Object.create(t.prototype),e.prototype.constructor=e,Ne(e,t)}function Ne(e,t){return Ne=Object.setPrototypeOf||function(o,n){return o.__proto__=n,o},Ne(e,t)}var Oe="react-tabs__tab",D=function(e){er(t,e);function t(){return e.apply(this,arguments)||this}var r=t.prototype;return r.componentDidMount=function(){this.checkFocus()},r.componentDidUpdate=function(){this.checkFocus()},r.checkFocus=function(){var n=this.props,a=n.selected,s=n.focus;a&&s&&this.node.focus()},r.render=function(){var n,a=this,s=this.props,i=s.children,l=s.className,p=s.disabled,u=s.disabledClassName,f=s.focus,d=s.id,c=s.panelId,m=s.selected,g=s.selectedClassName,v=s.tabIndex,T=s.tabRef,P=Xt(s,Yt);return re.default.createElement("li",je({},P,{className:w(l,(n={},n[g]=m,n[u]=p,n)),ref:function(_){a.node=_,T&&T(_)},role:"tab",id:d,"aria-selected":m?"true":"false","aria-disabled":p?"true":"false","aria-controls":c,tabIndex:v||(m?"0":null),"data-rttab":!0}),i)},t}(re.Component);D.defaultProps={className:Oe,disabledClassName:Oe+"--disabled",focus:!1,id:null,panelId:null,selected:!1,selectedClassName:Oe+"--selected"};D.propTypes={};D.tabsRole="Tab";var oe=b(O());var tr=["children","className","forceRender","id","selected","selectedClassName","tabId"];function we(){return we=Object.assign||function(e){for(var t=1;t<arguments.length;t++){var r=arguments[t];for(var o in r)Object.prototype.hasOwnProperty.call(r,o)&&(e[o]=r[o])}return e},we.apply(this,arguments)}function rr(e,t){if(e==null)return{};var r={},o=Object.keys(e),n,a;for(a=0;a<o.length;a++)n=o[a],!(t.indexOf(n)>=0)&&(r[n]=e[n]);return r}function or(e,t){e.prototype=Object.create(t.prototype),e.prototype.constructor=e,Ce(e,t)}function Ce(e,t){return Ce=Object.setPrototypeOf||function(o,n){return o.__proto__=n,o},Ce(e,t)}var Qe="react-tabs__tab-panel",L=function(e){or(t,e);function t(){return e.apply(this,arguments)||this}var r=t.prototype;return r.render=function(){var n,a=this.props,s=a.children,i=a.className,l=a.forceRender,p=a.id,u=a.selected,f=a.selectedClassName,d=a.tabId,c=rr(a,tr);return oe.default.createElement("div",we({},c,{className:w(i,(n={},n[f]=u,n)),role:"tabpanel",id:p,"aria-labelledby":d}),l||u?s:null)},t}(oe.Component);L.defaultProps={className:Qe,forceRender:!1,selectedClassName:Qe+"--selected"};L.propTypes={};L.tabsRole="TabPanel";var y=b(z());var We=b(B()),ne=b(z()),He=PostTypeWithArchivePage=({id:e,label:t,postType:r})=>{let{link:o,title:n,edit:a}=r;return React.createElement(React.Fragment,null,React.createElement("h3",null,t),React.createElement(We.RawHTML,null,(0,ne.sprintf)((0,ne.__)('<p>You have a page <a href="%s">%s</a> that has the same slug as the post type archive slug. So WordPress will set it as the archive page for the <code>%s</code> post type.</p><p>To set the meta tags for the page, please <a href="%s">set on the edit page</a>.</p>',"slim-seo"),o,n,e,a)))};var ft=b(W()),Zr=e=>{let{id:t,label:r,std:o,className:n="",...a}=e;return React.createElement(ft.Control,{className:n,label:r,id:t,...a},React.createElement("div",{className:"ss-input-wrapper"},React.createElement("label",{className:"ss-toggle"},React.createElement("input",{type:"checkbox",id:t,name:t,defaultChecked:o,value:!0}),React.createElement("div",{className:"ss-toggle__switch"}))))},dt=Zr;var mt=b(W()),bt=b(Ie()),yt=b(B()),Gr=e=>{let t=(0,yt.useRef)(),{id:r,label:o,std:n,className:a="",mediaPopupTitle:s,...i}=e,l=p=>{p.preventDefault();let u=wp.media({multiple:!1,title:s});u.open(),u.off("select"),u.on("select",()=>{let f=u.state().get("selection").first().toJSON().url;t.current.value=f})};return React.createElement(mt.Control,{className:a,label:o,id:r,...i},React.createElement("div",{className:"ss-input-wrapper"},React.createElement("input",{type:"text",id:r,name:r,defaultValue:n,ref:t}),React.createElement(bt.Button,{icon:"format-image",onClick:l,className:"ss-insert-image"})))},Ee=Gr;var vt=b(W()),Jr=e=>{let{id:t,label:r,std:o,className:n="",...a}=e;return React.createElement(vt.Control,{className:n,label:r,id:t,...a},React.createElement("div",{className:"ss-input-wrapper"},React.createElement("input",{type:"text",id:t,name:t,defaultValue:o})))},ht=Jr;var gt=b(W()),Yr=e=>{let{id:t,label:r,std:o,className:n="",rows:a=2,...s}=e;return React.createElement(gt.Control,{className:n,label:r,id:t,...s},React.createElement("div",{className:"ss-input-wrapper"},React.createElement("textarea",{defaultValue:o,id:t,name:t,rows:a})))},Tt=Yr;var Xr=({baseName:e,option:t,label:r})=>React.createElement(React.Fragment,null,React.createElement("h3",null,r),React.createElement(ht,{id:`${e}[title]`,label:(0,y.__)("Meta title","slim-seo"),std:t.title,description:(0,y.__)("Recommended length: \u2264 60 characters.","slim-seo")}),React.createElement(Tt,{id:`${e}[description]`,label:(0,y.__)("Meta description","slim-seo"),std:t.description,description:(0,y.__)("Recommended length: 50-160 characters.","slim-seo")}),React.createElement(Ee,{id:`${e}[facebook_image]`,label:(0,y.__)("Facebook image","slim-seo"),std:t.facebook_image,mediaPopupTitle:ssPostTypes.mediaPopupTitle,description:(0,y.__)("Recommended size: 1200x630 px.","slim-seo")}),React.createElement(Ee,{id:`${e}[twitter_image]`,label:(0,y.__)("Twitter image","slim-seo"),std:t.twitter_image,mediaPopupTitle:ssPostTypes.mediaPopupTitle,description:(0,y.__)("Recommended size: 1200x600 px. Should have aspect ratio 2:1 with minimum width of 300 px and maximum width of 4096 px.","slim-seo")})),eo=({id:e,postType:t,option:r,optionArchive:o})=>{let n=`slim_seo[${e}]`,a=`slim_seo[${e}_archive]`;return React.createElement(React.Fragment,null,React.createElement(dt,{id:`${n}[noindex]`,std:r.noindex,label:(0,y.__)("Hide from search results","slim-seo"),tooltip:(0,y.__)("This setting will apply noindex robots tag to all posts of this post type and exclude the post type from the sitemap.","slim-seo")}),ssPostTypes.postTypesWithArchivePage.hasOwnProperty(e)?React.createElement(He,{id:e,postType:ssPostTypes.postTypesWithArchivePage[e],label:(0,y.sprintf)((0,y.__)("%s archive page","slim-seo"),t.labels.singular_name)}):t.has_archive&&React.createElement(Xr,{baseName:a,option:o,label:(0,y.sprintf)((0,y.__)("%s archive page","slim-seo"),t.labels.singular_name),archive:!0}))},Pt=eo;var Ct=b(xt());function Ot(e){let t;try{t=new URL(e,"http://example.com").search.substring(1)}catch{}if(t)return t}function jt(e){let t="",r=Object.entries(e),o;for(;o=r.shift();){let[n,a]=o;if(Array.isArray(a)||a&&a.constructor===Object){let i=Object.entries(a).reverse();for(let[l,p]of i)r.unshift([`${n}[${l}]`,p])}else a!==void 0&&(a===null&&(a=""),t+="&"+[n,a].map(encodeURIComponent).join("="))}return t.substr(1)}function Nt(e){try{return decodeURIComponent(e)}catch{return e}}function to(e,t,r){let o=t.length,n=o-1;for(let a=0;a<o;a++){let s=t[a];!s&&Array.isArray(e)&&(s=e.length.toString()),s=["__proto__","constructor","prototype"].includes(s)?s.toUpperCase():s;let i=!isNaN(Number(t[a+1]));e[s]=a===n?r:e[s]||(i?[]:{}),Array.isArray(e[s])&&!i&&(e[s]={...e[s]}),e=e[s]}}function wt(e){return(Ot(e)||"").replace(/\+/g,"%20").split("&").reduce((t,r)=>{let[o,n=""]=r.split("=").filter(Boolean).map(Nt);if(o){let a=o.replace(/\]/g,"").split("[");to(t,a,n)}return t},Object.create(null))}function Ae(e="",t){if(!t||!Object.keys(t).length)return e;let r=e,o=e.indexOf("?");return o!==-1&&(t=Object.assign(wt(e),t),r=r.substr(0,o)),r+"?"+jt(t)}var De={},St=async(e,t={},r="GET",o=!0)=>{let n=JSON.stringify({apiName:e,data:t,method:r});if(o&&De[n])return De[n];let a;r==="GET"?a={path:Ae(`/slim-seo/${e}`,t)}:a={path:`/slim-seo/${e}`,method:r,data:t};let s=await(0,Ct.default)(a);return De[n]=s,s};var ro=()=>{let[e,t]=(0,q.useState)([]);return(0,q.useEffect)(()=>{St("post-types-option").then(t)},[]),React.createElement(React.Fragment,null,React.createElement(E,{forceRenderTabPanel:!0,className:"ss-vertical-tabs"},React.createElement(A,null,Object.values(ssPostTypes.postTypes).map(r=>React.createElement(D,null,r.label))),Object.entries(ssPostTypes.postTypes).map(([r,o])=>React.createElement(L,null,React.createElement(Pt,{key:r,id:r,postType:o,option:e[r]||[],optionArchive:e[`${r}_archive`]||[]})))),React.createElement("input",{type:"submit",name:"submit",className:"button button-primary",value:(0,It.__)("Save Changes","slim-seo")}))};(0,q.render)(React.createElement(ro,null),document.getElementById("ss-post-types"));})();
/**
 * @license React
 * react-jsx-runtime.production.min.js
 *
 * Copyright (c) Facebook, Inc. and its affiliates.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */
