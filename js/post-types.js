(()=>{var Ct=Object.create;var Ae=Object.defineProperty;var St=Object.getOwnPropertyDescriptor;var It=Object.getOwnPropertyNames;var Rt=Object.getPrototypeOf,Et=Object.prototype.hasOwnProperty;var h=(e,t)=>()=>(t||e((t={exports:{}}).exports,t),t.exports);var At=(e,t,r,o)=>{if(t&&typeof t=="object"||typeof t=="function")for(let n of It(t))!Et.call(e,n)&&n!==r&&Ae(e,n,{get:()=>t[n],enumerable:!(o=St(t,n))||o.enumerable});return e};var b=(e,t,r)=>(r=e!=null?Ct(Rt(e)):{},At(t||!e||!e.__esModule?Ae(r,"default",{value:e,enumerable:!0}):r,e));var B=h((Zr,De)=>{De.exports=wp.element});var z=h((eo,Le)=>{Le.exports=wp.i18n});var O=h((to,Fe)=>{Fe.exports=React});var He=h((Vo,we)=>{function We(e){var t,r,o="";if(typeof e=="string"||typeof e=="number")o+=e;else if(typeof e=="object")if(Array.isArray(e))for(t=0;t<e.length;t++)e[t]&&(r=We(e[t]))&&(o&&(o+=" "),o+=r);else for(t in e)e[t]&&(o&&(o+=" "),o+=t);return o}function Qe(){for(var e,t,r=0,o="";r<arguments.length;)(e=arguments[r++])&&(t=We(e))&&(o&&(o+=" "),o+=t);return o}we.exports=Qe,we.exports.clsx=Qe});var ze=h(ae=>{"use strict";var er=O(),tr=Symbol.for("react.element"),rr=Symbol.for("react.fragment"),or=Object.prototype.hasOwnProperty,nr=er.__SECRET_INTERNALS_DO_NOT_USE_OR_YOU_WILL_BE_FIRED.ReactCurrentOwner,ar={key:!0,ref:!0,__self:!0,__source:!0};function Be(e,t,r){var o,n={},a=null,s=null;r!==void 0&&(a=""+r),t.key!==void 0&&(a=""+t.key),t.ref!==void 0&&(s=t.ref);for(o in t)or.call(t,o)&&!ar.hasOwnProperty(o)&&(n[o]=t[o]);if(e&&e.defaultProps)for(o in t=e.defaultProps,t)n[o]===void 0&&(n[o]=t[o]);return{$$typeof:tr,type:e,key:a,ref:s,props:n,_owner:nr.current}}ae.Fragment=rr;ae.jsx=Be;ae.jsxs=Be});var M=h((Wo,Ge)=>{"use strict";Ge.exports=ze()});var ie=h(se=>{"use strict";Object.defineProperty(se,"__esModule",{value:!0});se.default=void 0;var sr=ir(He()),L=M();function ir(e){return e&&e.__esModule?e:{default:e}}var lr=function(t){var r=t.label,o=r===void 0?"":r,n=t.required,a=n===void 0?!1:n,s=t.tooltip,i=s===void 0?"":s,l=t.description,p=l===void 0?"":l,u=t.id,f=u===void 0?"":u,d=t.className,c=d===void 0?"":d,m=t.children;return(0,L.jsxs)("div",{className:(0,sr.default)("ef-control",c),children:[o&&(0,L.jsxs)("label",{className:"ef-control__label",htmlFor:f,children:[o.includes("<")?(0,L.jsx)("div",{dangerouslySetInnerHTML:{__html:o}}):o,a&&(0,L.jsx)("span",{className:"ef-control__required",children:"*"})]}),(0,L.jsxs)("div",{className:"ef-control__input",children:[m,p&&(0,L.jsx)("div",{className:"ef-control__description",dangerouslySetInnerHTML:{__html:p}})]})]})},Ho=se.default=lr});var Ce=h(le=>{"use strict";function K(e){return K=typeof Symbol=="function"&&typeof Symbol.iterator=="symbol"?function(t){return typeof t}:function(t){return t&&typeof Symbol=="function"&&t.constructor===Symbol&&t!==Symbol.prototype?"symbol":typeof t},K(e)}Object.defineProperty(le,"__esModule",{value:!0});le.default=void 0;var pr=cr(ie()),Je=M(),ur=["type","id","name","value","placeholder","onChange"];function cr(e){return e&&e.__esModule?e:{default:e}}function Ye(e,t){var r=Object.keys(e);if(Object.getOwnPropertySymbols){var o=Object.getOwnPropertySymbols(e);t&&(o=o.filter(function(n){return Object.getOwnPropertyDescriptor(e,n).enumerable})),r.push.apply(r,o)}return r}function Xe(e){for(var t=1;t<arguments.length;t++){var r=arguments[t]!=null?arguments[t]:{};t%2?Ye(Object(r),!0).forEach(function(o){fr(e,o,r[o])}):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(r)):Ye(Object(r)).forEach(function(o){Object.defineProperty(e,o,Object.getOwnPropertyDescriptor(r,o))})}return e}function fr(e,t,r){return t=dr(t),t in e?Object.defineProperty(e,t,{value:r,enumerable:!0,configurable:!0,writable:!0}):e[t]=r,e}function dr(e){var t=mr(e,"string");return K(t)=="symbol"?t:String(t)}function mr(e,t){if(K(e)!="object"||!e)return e;var r=e[Symbol.toPrimitive];if(r!==void 0){var o=r.call(e,t||"default");if(K(o)!="object")return o;throw new TypeError("@@toPrimitive must return a primitive value.")}return(t==="string"?String:Number)(e)}function br(e,t){if(e==null)return{};var r=yr(e,t),o,n;if(Object.getOwnPropertySymbols){var a=Object.getOwnPropertySymbols(e);for(n=0;n<a.length;n++)o=a[n],!(t.indexOf(o)>=0)&&(!Object.prototype.propertyIsEnumerable.call(e,o)||(r[o]=e[o]))}return r}function yr(e,t){if(e==null)return{};var r={},o=Object.keys(e),n,a;for(a=0;a<o.length;a++)n=o[a],!(t.indexOf(n)>=0)&&(r[n]=e[n]);return r}var vr=function(t){var r=t.type,o=r===void 0?"text":r,n=t.id,a=n===void 0?"":n,s=t.name,i=s===void 0?"":s,l=t.value,p=l===void 0?"":l,u=t.placeholder,f=u===void 0?"":u,d=t.onChange,c=br(t,ur);return(0,Je.jsx)(pr.default,Xe(Xe({id:a},c),{},{children:(0,Je.jsx)("input",{type:o,id:a,name:i,defaultValue:p,placeholder:f,onChange:d})}))},zo=le.default=vr});var ot=h(pe=>{"use strict";function V(e){return V=typeof Symbol=="function"&&typeof Symbol.iterator=="symbol"?function(t){return typeof t}:function(t){return t&&typeof Symbol=="function"&&t.constructor===Symbol&&t!==Symbol.prototype?"symbol":typeof t},V(e)}Object.defineProperty(pe,"__esModule",{value:!0});pe.default=void 0;var hr=Tr(ie()),C=M(),gr=["id","name","value","placeholder","options","onChange"];function Tr(e){return e&&e.__esModule?e:{default:e}}function Ze(e,t){var r=Object.keys(e);if(Object.getOwnPropertySymbols){var o=Object.getOwnPropertySymbols(e);t&&(o=o.filter(function(n){return Object.getOwnPropertyDescriptor(e,n).enumerable})),r.push.apply(r,o)}return r}function et(e){for(var t=1;t<arguments.length;t++){var r=arguments[t]!=null?arguments[t]:{};t%2?Ze(Object(r),!0).forEach(function(o){Pr(e,o,r[o])}):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(r)):Ze(Object(r)).forEach(function(o){Object.defineProperty(e,o,Object.getOwnPropertyDescriptor(r,o))})}return e}function Pr(e,t,r){return t=_r(t),t in e?Object.defineProperty(e,t,{value:r,enumerable:!0,configurable:!0,writable:!0}):e[t]=r,e}function _r(e){var t=xr(e,"string");return V(t)=="symbol"?t:String(t)}function xr(e,t){if(V(e)!="object"||!e)return e;var r=e[Symbol.toPrimitive];if(r!==void 0){var o=r.call(e,t||"default");if(V(o)!="object")return o;throw new TypeError("@@toPrimitive must return a primitive value.")}return(t==="string"?String:Number)(e)}function tt(e,t){return wr(e)||jr(e,t)||Nr(e,t)||Or()}function Or(){throw new TypeError(`Invalid attempt to destructure non-iterable instance.
In order to be iterable, non-array objects must have a [Symbol.iterator]() method.`)}function Nr(e,t){if(!!e){if(typeof e=="string")return rt(e,t);var r=Object.prototype.toString.call(e).slice(8,-1);if(r==="Object"&&e.constructor&&(r=e.constructor.name),r==="Map"||r==="Set")return Array.from(e);if(r==="Arguments"||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(r))return rt(e,t)}}function rt(e,t){(t==null||t>e.length)&&(t=e.length);for(var r=0,o=new Array(t);r<t;r++)o[r]=e[r];return o}function jr(e,t){var r=e==null?null:typeof Symbol<"u"&&e[Symbol.iterator]||e["@@iterator"];if(r!=null){var o,n,a,s,i=[],l=!0,p=!1;try{if(a=(r=r.call(e)).next,t===0){if(Object(r)!==r)return;l=!1}else for(;!(l=(o=a.call(r)).done)&&(i.push(o.value),i.length!==t);l=!0);}catch(u){p=!0,n=u}finally{try{if(!l&&r.return!=null&&(s=r.return(),Object(s)!==s))return}finally{if(p)throw n}}return i}}function wr(e){if(Array.isArray(e))return e}function Cr(e,t){if(e==null)return{};var r=Sr(e,t),o,n;if(Object.getOwnPropertySymbols){var a=Object.getOwnPropertySymbols(e);for(n=0;n<a.length;n++)o=a[n],!(t.indexOf(o)>=0)&&(!Object.prototype.propertyIsEnumerable.call(e,o)||(r[o]=e[o]))}return r}function Sr(e,t){if(e==null)return{};var r={},o=Object.keys(e),n,a;for(a=0;a<o.length;a++)n=o[a],!(t.indexOf(n)>=0)&&(r[n]=e[n]);return r}var Ir=function(t){var r=t.id,o=r===void 0?"":r,n=t.name,a=n===void 0?"":n,s=t.value,i=s===void 0?"":s,l=t.placeholder,p=l===void 0?"-":l,u=t.options,f=t.onChange,d=Cr(t,gr);return(0,C.jsx)(hr.default,et(et({id:o},d),{},{children:(0,C.jsxs)("select",{id:o,name:a,defaultValue:i,onChange:f,children:[(0,C.jsx)("option",{value:"",children:p}),Array.isArray(u)?u.map(function(c){return c.options?(0,C.jsx)("optgroup",{label:c.label,children:Array.isArray(c.options)?c.options.map(function(m){return(0,C.jsx)("option",{value:m.value,children:m.label},m.value)}):Object.entries(c.options).map(function(m){var g=tt(m,2),v=g[0],T=g[1];return(0,C.jsx)("option",{value:v,children:T},v)})},c.label):(0,C.jsx)("option",{value:c.value,children:c.label},c.value)}):Object.entries(u).map(function(c){var m=tt(c,2),g=m[0],v=m[1];return(0,C.jsx)("option",{value:g,children:v},g)})]})}))},Jo=pe.default=Ir});var at=h(ue=>{"use strict";Object.defineProperty(ue,"__esModule",{value:!0});ue.default=void 0;var Rr=Ar(Ce()),Er=M();function Ar(e){return e&&e.__esModule?e:{default:e}}function Q(e){return Q=typeof Symbol=="function"&&typeof Symbol.iterator=="symbol"?function(t){return typeof t}:function(t){return t&&typeof Symbol=="function"&&t.constructor===Symbol&&t!==Symbol.prototype?"symbol":typeof t},Q(e)}function nt(e,t){var r=Object.keys(e);if(Object.getOwnPropertySymbols){var o=Object.getOwnPropertySymbols(e);t&&(o=o.filter(function(n){return Object.getOwnPropertyDescriptor(e,n).enumerable})),r.push.apply(r,o)}return r}function Dr(e){for(var t=1;t<arguments.length;t++){var r=arguments[t]!=null?arguments[t]:{};t%2?nt(Object(r),!0).forEach(function(o){Lr(e,o,r[o])}):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(r)):nt(Object(r)).forEach(function(o){Object.defineProperty(e,o,Object.getOwnPropertyDescriptor(r,o))})}return e}function Lr(e,t,r){return t=Fr(t),t in e?Object.defineProperty(e,t,{value:r,enumerable:!0,configurable:!0,writable:!0}):e[t]=r,e}function Fr(e){var t=$r(e,"string");return Q(t)=="symbol"?t:String(t)}function $r(e,t){if(Q(e)!="object"||!e)return e;var r=e[Symbol.toPrimitive];if(r!==void 0){var o=r.call(e,t||"default");if(Q(o)!="object")return o;throw new TypeError("@@toPrimitive must return a primitive value.")}return(t==="string"?String:Number)(e)}var kr=function(t){return(0,Er.jsx)(Rr.default,Dr({type:"text"},t))},Xo=ue.default=kr});var W=h(F=>{"use strict";Object.defineProperty(F,"__esModule",{value:!0});Object.defineProperty(F,"Control",{enumerable:!0,get:function(){return qr.default}});Object.defineProperty(F,"Input",{enumerable:!0,get:function(){return Ur.default}});Object.defineProperty(F,"Select",{enumerable:!0,get:function(){return Mr.default}});Object.defineProperty(F,"Text",{enumerable:!0,get:function(){return Kr.default}});var qr=ce(ie()),Ur=ce(Ce()),Mr=ce(ot()),Kr=ce(at());function ce(e){return e&&e.__esModule?e:{default:e}}});var pt=h((rn,lt)=>{lt.exports=wp.components});var gt=h((dn,ht)=>{ht.exports=wp.apiFetch});var $=b(B()),jt=b(z());var ee=b(O());var q=b(O());function me(e){return function(t){return!!t.type&&t.type.tabsRole===e}}var N=me("Tab"),I=me("TabList"),j=me("TabPanel");function be(){return be=Object.assign||function(e){for(var t=1;t<arguments.length;t++){var r=arguments[t];for(var o in r)Object.prototype.hasOwnProperty.call(r,o)&&(e[o]=r[o])}return e},be.apply(this,arguments)}function Dt(e){return N(e)||I(e)||j(e)}function G(e,t){return q.Children.map(e,function(r){return r===null?null:Dt(r)?t(r):r.props&&r.props.children&&typeof r.props.children=="object"?(0,q.cloneElement)(r,be({},r.props,{children:G(r.props.children,t)})):r})}function U(e,t){return q.Children.forEach(e,function(r){r!==null&&(N(r)||j(r)?t(r):r.props&&r.props.children&&typeof r.props.children=="object"&&(I(r)&&t(r),U(r.props.children,t)))})}var x=b(O());function $e(e){var t,r,o="";if(typeof e=="string"||typeof e=="number")o+=e;else if(typeof e=="object")if(Array.isArray(e))for(t=0;t<e.length;t++)e[t]&&(r=$e(e[t]))&&(o&&(o+=" "),o+=r);else for(t in e)e[t]&&(o&&(o+=" "),o+=t);return o}function Lt(){for(var e,t,r=0,o="";r<arguments.length;)(e=arguments[r++])&&(t=$e(e))&&(o&&(o+=" "),o+=t);return o}var w=Lt;var Ft=0;function J(){return"react-tabs-"+Ft++}function Y(e){var t=0;return U(e,function(r){N(r)&&t++}),t}function ke(e){var t=0;return U(e,function(r){j(r)&&t++}),t}var $t=["children","className","disabledTabClassName","domRef","focus","forceRenderTabPanel","onSelect","selectedIndex","selectedTabClassName","selectedTabPanelClassName","environment","disableUpDownKeys"];function ye(){return ye=Object.assign||function(e){for(var t=1;t<arguments.length;t++){var r=arguments[t];for(var o in r)Object.prototype.hasOwnProperty.call(r,o)&&(e[o]=r[o])}return e},ye.apply(this,arguments)}function kt(e,t){if(e==null)return{};var r={},o=Object.keys(e),n,a;for(a=0;a<o.length;a++)n=o[a],!(t.indexOf(n)>=0)&&(r[n]=e[n]);return r}function qt(e,t){e.prototype=Object.create(t.prototype),e.prototype.constructor=e,ve(e,t)}function ve(e,t){return ve=Object.setPrototypeOf||function(o,n){return o.__proto__=n,o},ve(e,t)}function Ue(e){return e&&"getAttribute"in e}function qe(e){return Ue(e)&&e.getAttribute("data-rttab")}function S(e){return Ue(e)&&e.getAttribute("aria-disabled")==="true"}var X;function Ut(e){var t=e||(typeof window<"u"?window:void 0);try{X=!!(typeof t<"u"&&t.document&&t.document.activeElement)}catch{X=!1}}var Z=function(e){qt(t,e);function t(){for(var o,n=arguments.length,a=new Array(n),s=0;s<n;s++)a[s]=arguments[s];return o=e.call.apply(e,[this].concat(a))||this,o.tabNodes=[],o.handleKeyDown=function(i){var l=o.props,p=l.direction,u=l.disableUpDownKeys;if(o.isTabFromContainer(i.target)){var f=o.props.selectedIndex,d=!1,c=!1;(i.keyCode===32||i.keyCode===13)&&(d=!0,c=!1,o.handleClick(i)),i.keyCode===37||!u&&i.keyCode===38?(p==="rtl"?f=o.getNextTab(f):f=o.getPrevTab(f),d=!0,c=!0):i.keyCode===39||!u&&i.keyCode===40?(p==="rtl"?f=o.getPrevTab(f):f=o.getNextTab(f),d=!0,c=!0):i.keyCode===35?(f=o.getLastTab(),d=!0,c=!0):i.keyCode===36&&(f=o.getFirstTab(),d=!0,c=!0),d&&i.preventDefault(),c&&o.setSelected(f,i)}},o.handleClick=function(i){var l=i.target;do if(o.isTabFromContainer(l)){if(S(l))return;var p=[].slice.call(l.parentNode.children).filter(qe).indexOf(l);o.setSelected(p,i);return}while((l=l.parentNode)!=null)},o}var r=t.prototype;return r.setSelected=function(n,a){if(!(n<0||n>=this.getTabsCount())){var s=this.props,i=s.onSelect,l=s.selectedIndex;i(n,l,a)}},r.getNextTab=function(n){for(var a=this.getTabsCount(),s=n+1;s<a;s++)if(!S(this.getTab(s)))return s;for(var i=0;i<n;i++)if(!S(this.getTab(i)))return i;return n},r.getPrevTab=function(n){for(var a=n;a--;)if(!S(this.getTab(a)))return a;for(a=this.getTabsCount();a-- >n;)if(!S(this.getTab(a)))return a;return n},r.getFirstTab=function(){for(var n=this.getTabsCount(),a=0;a<n;a++)if(!S(this.getTab(a)))return a;return null},r.getLastTab=function(){for(var n=this.getTabsCount();n--;)if(!S(this.getTab(n)))return n;return null},r.getTabsCount=function(){var n=this.props.children;return Y(n)},r.getPanelsCount=function(){var n=this.props.children;return ke(n)},r.getTab=function(n){return this.tabNodes["tabs-"+n]},r.getChildren=function(){var n=this,a=0,s=this.props,i=s.children,l=s.disabledTabClassName,p=s.focus,u=s.forceRenderTabPanel,f=s.selectedIndex,d=s.selectedTabClassName,c=s.selectedTabPanelClassName,m=s.environment;this.tabIds=this.tabIds||[],this.panelIds=this.panelIds||[];for(var g=this.tabIds.length-this.getTabsCount();g++<0;)this.tabIds.push(J()),this.panelIds.push(J());return G(i,function(v){var T=v;if(I(v)){var P=0,H=!1;X==null&&Ut(m),X&&(H=x.default.Children.toArray(v.props.children).filter(N).some(function(Ee,fe){var k=m||(typeof window<"u"?window:void 0);return k&&k.document.activeElement===n.getTab(fe)})),T=(0,x.cloneElement)(v,{children:G(v.props.children,function(Ee){var fe="tabs-"+P,k=f===P,de={tabRef:function(wt){n.tabNodes[fe]=wt},id:n.tabIds[P],panelId:n.panelIds[P],selected:k,focus:k&&(p||H)};return d&&(de.selectedClassName=d),l&&(de.disabledClassName=l),P++,(0,x.cloneElement)(Ee,de)})})}else if(j(v)){var _={id:n.panelIds[a],tabId:n.tabIds[a],selected:f===a};u&&(_.forceRender=u),c&&(_.selectedClassName=c),a++,T=(0,x.cloneElement)(v,_)}return T})},r.isTabFromContainer=function(n){if(!qe(n))return!1;var a=n.parentElement;do{if(a===this.node)return!0;if(a.getAttribute("data-rttabs"))break;a=a.parentElement}while(a);return!1},r.render=function(){var n=this,a=this.props,s=a.children,i=a.className,l=a.disabledTabClassName,p=a.domRef,u=a.focus,f=a.forceRenderTabPanel,d=a.onSelect,c=a.selectedIndex,m=a.selectedTabClassName,g=a.selectedTabPanelClassName,v=a.environment,T=a.disableUpDownKeys,P=kt(a,$t);return x.default.createElement("div",ye({},P,{className:w(i),onClick:this.handleClick,onKeyDown:this.handleKeyDown,ref:function(_){n.node=_,p&&p(_)},"data-rttabs":!0}),this.getChildren())},t}(x.Component);Z.defaultProps={className:"react-tabs",focus:!1};Z.propTypes={};var Mt=["children","defaultIndex","defaultFocus"];function Kt(e,t){if(e==null)return{};var r={},o=Object.keys(e),n,a;for(a=0;a<o.length;a++)n=o[a],!(t.indexOf(n)>=0)&&(r[n]=e[n]);return r}function Vt(e,t){e.prototype=Object.create(t.prototype),e.prototype.constructor=e,ge(e,t)}function ge(e,t){return ge=Object.setPrototypeOf||function(o,n){return o.__proto__=n,o},ge(e,t)}var Qt=0,he=1,R=function(e){Vt(t,e);function t(o){var n;return n=e.call(this,o)||this,n.handleSelected=function(a,s,i){var l=n.props.onSelect,p=n.state.mode;if(!(typeof l=="function"&&l(a,s,i)===!1)){var u={focus:i.type==="keydown"};p===he&&(u.selectedIndex=a),n.setState(u)}},n.state=t.copyPropsToState(n.props,{},o.defaultFocus),n}t.getDerivedStateFromProps=function(n,a){return t.copyPropsToState(n,a)},t.getModeFromProps=function(n){return n.selectedIndex===null?he:Qt},t.copyPropsToState=function(n,a,s){s===void 0&&(s=!1);var i={focus:s,mode:t.getModeFromProps(n)};if(i.mode===he){var l=Math.max(0,Y(n.children)-1),p=null;a.selectedIndex!=null?p=Math.min(a.selectedIndex,l):p=n.defaultIndex||0,i.selectedIndex=p}return i};var r=t.prototype;return r.render=function(){var n=this.props,a=n.children,s=n.defaultIndex,i=n.defaultFocus,l=Kt(n,Mt),p=this.state,u=p.focus,f=p.selectedIndex;return l.focus=u,l.onSelect=this.handleSelected,f!=null&&(l.selectedIndex=f),ee.default.createElement(Z,l,a)},t}(ee.Component);R.defaultProps={defaultFocus:!1,forceRenderTabPanel:!1,selectedIndex:null,defaultIndex:null,environment:null,disableUpDownKeys:!1};R.propTypes={};R.tabsRole="Tabs";var te=b(O());var Wt=["children","className"];function Te(){return Te=Object.assign||function(e){for(var t=1;t<arguments.length;t++){var r=arguments[t];for(var o in r)Object.prototype.hasOwnProperty.call(r,o)&&(e[o]=r[o])}return e},Te.apply(this,arguments)}function Ht(e,t){if(e==null)return{};var r={},o=Object.keys(e),n,a;for(a=0;a<o.length;a++)n=o[a],!(t.indexOf(n)>=0)&&(r[n]=e[n]);return r}function Bt(e,t){e.prototype=Object.create(t.prototype),e.prototype.constructor=e,Pe(e,t)}function Pe(e,t){return Pe=Object.setPrototypeOf||function(o,n){return o.__proto__=n,o},Pe(e,t)}var E=function(e){Bt(t,e);function t(){return e.apply(this,arguments)||this}var r=t.prototype;return r.render=function(){var n=this.props,a=n.children,s=n.className,i=Ht(n,Wt);return te.default.createElement("ul",Te({},i,{className:w(s),role:"tablist"}),a)},t}(te.Component);E.defaultProps={className:"react-tabs__tab-list"};E.propTypes={};E.tabsRole="TabList";var re=b(O());var zt=["children","className","disabled","disabledClassName","focus","id","panelId","selected","selectedClassName","tabIndex","tabRef"];function xe(){return xe=Object.assign||function(e){for(var t=1;t<arguments.length;t++){var r=arguments[t];for(var o in r)Object.prototype.hasOwnProperty.call(r,o)&&(e[o]=r[o])}return e},xe.apply(this,arguments)}function Gt(e,t){if(e==null)return{};var r={},o=Object.keys(e),n,a;for(a=0;a<o.length;a++)n=o[a],!(t.indexOf(n)>=0)&&(r[n]=e[n]);return r}function Jt(e,t){e.prototype=Object.create(t.prototype),e.prototype.constructor=e,Oe(e,t)}function Oe(e,t){return Oe=Object.setPrototypeOf||function(o,n){return o.__proto__=n,o},Oe(e,t)}var _e="react-tabs__tab",A=function(e){Jt(t,e);function t(){return e.apply(this,arguments)||this}var r=t.prototype;return r.componentDidMount=function(){this.checkFocus()},r.componentDidUpdate=function(){this.checkFocus()},r.checkFocus=function(){var n=this.props,a=n.selected,s=n.focus;a&&s&&this.node.focus()},r.render=function(){var n,a=this,s=this.props,i=s.children,l=s.className,p=s.disabled,u=s.disabledClassName,f=s.focus,d=s.id,c=s.panelId,m=s.selected,g=s.selectedClassName,v=s.tabIndex,T=s.tabRef,P=Gt(s,zt);return re.default.createElement("li",xe({},P,{className:w(l,(n={},n[g]=m,n[u]=p,n)),ref:function(_){a.node=_,T&&T(_)},role:"tab",id:d,"aria-selected":m?"true":"false","aria-disabled":p?"true":"false","aria-controls":c,tabIndex:v||(m?"0":null),"data-rttab":!0}),i)},t}(re.Component);A.defaultProps={className:_e,disabledClassName:_e+"--disabled",focus:!1,id:null,panelId:null,selected:!1,selectedClassName:_e+"--selected"};A.propTypes={};A.tabsRole="Tab";var oe=b(O());var Yt=["children","className","forceRender","id","selected","selectedClassName","tabId"];function Ne(){return Ne=Object.assign||function(e){for(var t=1;t<arguments.length;t++){var r=arguments[t];for(var o in r)Object.prototype.hasOwnProperty.call(r,o)&&(e[o]=r[o])}return e},Ne.apply(this,arguments)}function Xt(e,t){if(e==null)return{};var r={},o=Object.keys(e),n,a;for(a=0;a<o.length;a++)n=o[a],!(t.indexOf(n)>=0)&&(r[n]=e[n]);return r}function Zt(e,t){e.prototype=Object.create(t.prototype),e.prototype.constructor=e,je(e,t)}function je(e,t){return je=Object.setPrototypeOf||function(o,n){return o.__proto__=n,o},je(e,t)}var Me="react-tabs__tab-panel",D=function(e){Zt(t,e);function t(){return e.apply(this,arguments)||this}var r=t.prototype;return r.render=function(){var n,a=this.props,s=a.children,i=a.className,l=a.forceRender,p=a.id,u=a.selected,f=a.selectedClassName,d=a.tabId,c=Xt(a,Yt);return oe.default.createElement("div",Ne({},c,{className:w(i,(n={},n[f]=u,n)),role:"tabpanel",id:p,"aria-labelledby":d}),l||u?s:null)},t}(oe.Component);D.defaultProps={className:Me,forceRender:!1,selectedClassName:Me+"--selected"};D.propTypes={};D.tabsRole="TabPanel";var y=b(z());var Ke=b(B()),ne=b(z()),Ve=PostTypeWithArchivePage=({id:e,label:t,postType:r})=>{let{link:o,title:n,edit:a}=r;return React.createElement(React.Fragment,null,React.createElement("h3",null,t),React.createElement(Ke.RawHTML,null,(0,ne.sprintf)((0,ne.__)('<p>You have a page <a href="%s">%s</a> that has the same slug as the post type archive slug. So WordPress will set it as the archive page for the <code>%s</code> post type.</p><p>To set the meta tags for the page, please <a href="%s">set on the edit page</a>.</p>',"slim-seo"),o,n,e,a)))};var st=b(W()),Vr=e=>{let{id:t,label:r,std:o,className:n="",...a}=e;return React.createElement(st.Control,{className:n,label:r,id:t,...a},React.createElement("div",{className:"ss-input-wrapper"},React.createElement("label",{className:"ss-toggle"},React.createElement("input",{type:"checkbox",id:t,name:t,defaultChecked:o,value:!0}),React.createElement("div",{className:"ss-toggle__switch"}))))},it=Vr;var ut=b(W()),ct=b(pt()),ft=b(B()),Qr=e=>{let t=(0,ft.useRef)(),{id:r,label:o,std:n,className:a="",mediaPopupTitle:s,...i}=e,l=p=>{p.preventDefault();let u=wp.media({multiple:!1,title:s});u.open(),u.off("select"),u.on("select",()=>{let f=u.state().get("selection").first().toJSON().url;t.current.value=f})};return React.createElement(ut.Control,{className:a,label:o,id:r,...i},React.createElement("div",{className:"ss-input-wrapper"},React.createElement("input",{type:"text",id:r,name:r,defaultValue:n,ref:t}),React.createElement(ct.Button,{icon:"format-image",onClick:l,className:"ss-insert-image"})))},Se=Qr;var dt=b(W()),Wr=e=>{let{id:t,label:r,std:o,className:n="",...a}=e;return React.createElement(dt.Control,{className:n,label:r,id:t,...a},React.createElement("div",{className:"ss-input-wrapper"},React.createElement("input",{type:"text",id:t,name:t,defaultValue:o})))},mt=Wr;var bt=b(W()),Hr=e=>{let{id:t,label:r,std:o,className:n="",rows:a=2,...s}=e;return React.createElement(bt.Control,{className:n,label:r,id:t,...s},React.createElement("div",{className:"ss-input-wrapper"},React.createElement("textarea",{defaultValue:o,id:t,name:t,rows:a})))},yt=Hr;var Br=({baseName:e,option:t,label:r})=>React.createElement(React.Fragment,null,React.createElement("h3",null,r),React.createElement(mt,{id:`${e}[title]`,label:(0,y.__)("Meta title","slim-seo"),std:t.title,description:(0,y.__)("Recommended length: \u2264 60 characters.","slim-seo")}),React.createElement(yt,{id:`${e}[description]`,label:(0,y.__)("Meta description","slim-seo"),std:t.description,description:(0,y.__)("Recommended length: 50-160 characters.","slim-seo")}),React.createElement(Se,{id:`${e}[facebook_image]`,label:(0,y.__)("Facebook image","slim-seo"),std:t.facebook_image,mediaPopupTitle:ssPostTypes.mediaPopupTitle,description:(0,y.__)("Recommended size: 1200x630 px.","slim-seo")}),React.createElement(Se,{id:`${e}[twitter_image]`,label:(0,y.__)("Twitter image","slim-seo"),std:t.twitter_image,mediaPopupTitle:ssPostTypes.mediaPopupTitle,description:(0,y.__)("Recommended size: 1200x600 px. Should have aspect ratio 2:1 with minimum width of 300 px and maximum width of 4096 px.","slim-seo")})),zr=({id:e,postType:t,option:r,optionArchive:o})=>{let n=`slim_seo[${e}]`,a=`slim_seo[${e}_archive]`;return React.createElement(React.Fragment,null,React.createElement(it,{id:`${n}[noindex]`,std:r.noindex,label:(0,y.__)("Hide from search results","slim-seo"),tooltip:(0,y.__)("This setting will apply noindex robots tag to all posts of this post type and exclude the post type from the sitemap.","slim-seo")}),ssPostTypes.postTypesWithArchivePage.hasOwnProperty(e)?React.createElement(Ve,{id:e,postType:ssPostTypes.postTypesWithArchivePage[e],label:(0,y.sprintf)((0,y.__)("%s archive page","slim-seo"),t.labels.singular_name)}):t.has_archive&&React.createElement(Br,{baseName:a,option:o,label:(0,y.sprintf)((0,y.__)("%s archive page","slim-seo"),t.labels.singular_name),archive:!0}))},vt=zr;var Ot=b(gt());function Tt(e){let t;try{t=new URL(e,"http://example.com").search.substring(1)}catch{}if(t)return t}function Pt(e){let t="",r=Object.entries(e),o;for(;o=r.shift();){let[n,a]=o;if(Array.isArray(a)||a&&a.constructor===Object){let i=Object.entries(a).reverse();for(let[l,p]of i)r.unshift([`${n}[${l}]`,p])}else a!==void 0&&(a===null&&(a=""),t+="&"+[n,a].map(encodeURIComponent).join("="))}return t.substr(1)}function _t(e){try{return decodeURIComponent(e)}catch{return e}}function Gr(e,t,r){let o=t.length,n=o-1;for(let a=0;a<o;a++){let s=t[a];!s&&Array.isArray(e)&&(s=e.length.toString()),s=["__proto__","constructor","prototype"].includes(s)?s.toUpperCase():s;let i=!isNaN(Number(t[a+1]));e[s]=a===n?r:e[s]||(i?[]:{}),Array.isArray(e[s])&&!i&&(e[s]={...e[s]}),e=e[s]}}function xt(e){return(Tt(e)||"").replace(/\+/g,"%20").split("&").reduce((t,r)=>{let[o,n=""]=r.split("=").filter(Boolean).map(_t);if(o){let a=o.replace(/\]/g,"").split("[");Gr(t,a,n)}return t},Object.create(null))}function Ie(e="",t){if(!t||!Object.keys(t).length)return e;let r=e,o=e.indexOf("?");return o!==-1&&(t=Object.assign(xt(e),t),r=r.substr(0,o)),r+"?"+Pt(t)}var Re={},Nt=async(e,t={},r="GET",o=!0)=>{let n=JSON.stringify({apiName:e,data:t,method:r});if(o&&Re[n])return Re[n];let a;r==="GET"?a={path:Ie(`/slim-seo/${e}`,t)}:a={path:`/slim-seo/${e}`,method:r,data:t};let s=await(0,Ot.default)(a);return Re[n]=s,s};var Jr=()=>{let[e,t]=(0,$.useState)([]);return(0,$.useEffect)(()=>{Nt("post-types-option").then(t)},[]),React.createElement(React.Fragment,null,React.createElement(R,{forceRenderTabPanel:!0,className:"ss-vertical-tabs"},React.createElement(E,null,Object.values(ssPostTypes.postTypes).map(r=>React.createElement(A,null,r.label))),Object.entries(ssPostTypes.postTypes).map(([r,o])=>React.createElement(D,null,React.createElement(vt,{key:r,id:r,postType:o,option:e[r]||[],optionArchive:e[`${r}_archive`]||[]})))),React.createElement("input",{type:"submit",name:"submit",className:"button button-primary",value:(0,jt.__)("Save Changes","slim-seo")}))};(0,$.render)(React.createElement(Jr,null),document.getElementById("ss-post-types"));})();
/**
 * @license React
 * react-jsx-runtime.production.min.js
 *
 * Copyright (c) Facebook, Inc. and its affiliates.
 *
 * This source code is licensed under the MIT license found in the
 * LICENSE file in the root directory of this source tree.
 */
