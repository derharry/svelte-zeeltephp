var Sn=Array.isArray,Vt=Array.prototype.indexOf,In=Array.from,Nn=Object.defineProperty,_t=Object.getOwnPropertyDescriptor,Gt=Object.getOwnPropertyDescriptors,Pn=Object.prototype,bn=Array.prototype,Kt=Object.getPrototypeOf;const Fn=()=>{};function Cn(t){return t()}function wt(t){for(var n=0;n<t.length;n++)t[n]()}const g=2,Et=4,at=8,st=16,k=32,q=64,U=128,E=256,H=512,v=1024,D=2048,N=4096,S=8192,z=16384,Zt=32768,yt=65536,qn=1<<17,$t=1<<19,gt=1<<20,ct=Symbol("$state"),Yn=Symbol("legacy props");function mt(t){return t===this.v}function Wt(t,n){return t!=t?n==n:t!==n||t!==null&&typeof t=="object"||typeof t=="function"}function Tt(t){return!Wt(t,this.v)}function zt(t){throw new Error("https://svelte.dev/e/effect_in_teardown")}function Jt(){throw new Error("https://svelte.dev/e/effect_in_unowned_derived")}function Qt(t){throw new Error("https://svelte.dev/e/effect_orphan")}function Xt(){throw new Error("https://svelte.dev/e/effect_update_depth_exceeded")}function Ln(){throw new Error("https://svelte.dev/e/hydration_failed")}function jn(t){throw new Error("https://svelte.dev/e/props_invalid_value")}function Mn(){throw new Error("https://svelte.dev/e/state_descriptors_fixed")}function Bn(){throw new Error("https://svelte.dev/e/state_prototype_fixed")}function tn(){throw new Error("https://svelte.dev/e/state_unsafe_local_read")}function nn(){throw new Error("https://svelte.dev/e/state_unsafe_mutation")}let J=!1;function Un(){J=!0}const Hn=1,Vn=2,Gn=4,Kn=8,Zn=16,$n=1,Wn=2,rn="[",en="[!",ln="]",xt={},zn=Symbol();function At(t){console.warn("https://svelte.dev/e/hydration_mismatch")}let f=null;function vt(t){f=t}function Jn(t,n=!1,r){f={p:f,c:null,e:null,m:!1,s:t,x:null,l:null},J&&!n&&(f.l={s:null,u:null,r1:[],r2:ut(!1)})}function Qn(t){const n=f;if(n!==null){const s=n.e;if(s!==null){var r=o,e=u;n.e=null;try{for(var l=0;l<s.length;l++){var a=s[l];Z(a.effect),K(a.reaction),Pt(a.fn)}}finally{Z(r),K(e)}}f=n.p,n.m=!0}return{}}function Q(){return!J||f!==null&&f.l===null}function ut(t,n){var r={f:0,v:t,reactions:null,equals:mt,rv:0,wv:0};return r}function Xn(t){return an(ut(t))}function tr(t,n=!1){var e;const r=ut(t);return n||(r.equals=Tt),J&&f!==null&&f.l!==null&&((e=f.l).s??(e.s=[])).push(r),r}function an(t){return u!==null&&!y&&(u.f&g)!==0&&(m===null?En([t]):m.push(t)),t}function nr(t,n){return u!==null&&!y&&Q()&&(u.f&(g|st))!==0&&(m===null||!m.includes(t))&&nn(),sn(t,n)}function sn(t,n){return t.equals(n)||(t.v,t.v=n,t.wv=Mt(),kt(t,D),Q()&&o!==null&&(o.f&v)!==0&&(o.f&(k|q))===0&&(T===null?yn([t]):T.push(t))),n}function kt(t,n){var r=t.reactions;if(r!==null)for(var e=Q(),l=r.length,a=0;a<l;a++){var s=r[a],i=s.f;(i&D)===0&&(!e&&s===o||(A(s,n),(i&(v|E))!==0&&((i&g)!==0?kt(s,N):tt(s))))}}let I=!1;function rr(t){I=t}let x;function F(t){if(t===null)throw At(),xt;return x=t}function er(){return F(P(x))}function lr(t){if(I){if(P(x)!==null)throw At(),xt;x=t}}function ar(){for(var t=0,n=x;;){if(n.nodeType===8){var r=n.data;if(r===ln){if(t===0)return n;t-=1}else(r===rn||r===en)&&(t+=1)}var e=P(n);n.remove(),n=e}}var pt,un,Rt,Dt;function sr(){if(pt===void 0){pt=window,un=/Firefox/.test(navigator.userAgent);var t=Element.prototype,n=Node.prototype;Rt=_t(n,"firstChild").get,Dt=_t(n,"nextSibling").get,t.__click=void 0,t.__className=void 0,t.__attributes=null,t.__styles=null,t.__e=void 0,Text.prototype.__t=void 0}}function nt(t=""){return document.createTextNode(t)}function rt(t){return Rt.call(t)}function P(t){return Dt.call(t)}function ur(t,n){if(!I)return rt(t);var r=rt(x);if(r===null)r=x.appendChild(nt());else if(n&&r.nodeType!==3){var e=nt();return r==null||r.before(e),F(e),e}return F(r),r}function or(t,n){if(!I){var r=rt(t);return r instanceof Comment&&r.data===""?P(r):r}return x}function fr(t,n=1,r=!1){let e=I?x:t;for(var l;n--;)l=e,e=P(e);if(!I)return e;var a=e==null?void 0:e.nodeType;if(r&&a!==3){var s=nt();return e===null?l==null||l.after(s):e.before(s),F(s),s}return F(e),e}function ir(t){t.textContent=""}function Ot(t){var n=g|D,r=u!==null&&(u.f&g)!==0?u:null;return o===null||r!==null&&(r.f&E)!==0?n|=E:o.f|=gt,{ctx:f,deps:null,effects:null,equals:mt,f:n,fn:t,reactions:null,rv:0,v:null,wv:0,parent:r??o}}function _r(t){const n=Ot(t);return n.equals=Tt,n}function St(t){var n=t.effects;if(n!==null){t.effects=null;for(var r=0;r<n.length;r+=1)O(n[r])}}function on(t){for(var n=t.parent;n!==null;){if((n.f&g)===0)return n;n=n.parent}return null}function fn(t){var n,r=o;Z(on(t));try{St(t),n=Ut(t)}finally{Z(r)}return n}function It(t){var n=fn(t),r=(R||(t.f&E)!==0)&&t.deps!==null?N:v;A(t,r),t.equals(n)||(t.v=n,t.wv=Mt())}function Nt(t){o===null&&u===null&&Qt(),u!==null&&(u.f&E)!==0&&o===null&&Jt(),ot&&zt()}function _n(t,n){var r=n.last;r===null?n.last=n.first=t:(r.next=t,t.prev=r,n.last=t)}function Y(t,n,r,e=!0){var l=(t&q)!==0,a=o,s={ctx:f,deps:null,nodes_start:null,nodes_end:null,f:t|D,first:null,fn:n,last:null,next:null,parent:l?null:a,prev:null,teardown:null,transitions:null,wv:0};if(r)try{ft(s),s.f|=Zt}catch(w){throw O(s),w}else n!==null&&tt(s);var i=r&&s.deps===null&&s.first===null&&s.nodes_start===null&&s.teardown===null&&(s.f&(gt|U))===0;if(!i&&!l&&e&&(a!==null&&_n(s,a),u!==null&&(u.f&g)!==0)){var _=u;(_.effects??(_.effects=[])).push(s)}return s}function cr(t){Nt();var n=o!==null&&(o.f&k)!==0&&f!==null&&!f.m;if(n){var r=f;(r.e??(r.e=[])).push({fn:t,effect:o,reaction:u})}else{var e=Pt(t);return e}}function vr(t){return Nt(),cn(t)}function pr(t){const n=Y(q,t,!0);return(r={})=>new Promise(e=>{r.outro?hn(n,()=>{O(n),e(void 0)}):(O(n),e(void 0))})}function Pt(t){return Y(Et,t,!1)}function cn(t){return Y(at,t,!0)}function hr(t,n=[],r=Ot){const e=n.map(r);return vn(()=>t(...e.map(Dn)))}function vn(t,n=0){return Y(at|st|n,t,!0)}function dr(t,n=!0){return Y(at|k,t,!0,n)}function bt(t){var n=t.teardown;if(n!==null){const r=ot,e=u;dt(!0),K(null);try{n.call(null)}finally{dt(r),K(e)}}}function Ft(t,n=!1){var r=t.first;for(t.first=t.last=null;r!==null;){var e=r.next;O(r,n),r=e}}function pn(t){for(var n=t.first;n!==null;){var r=n.next;(n.f&k)===0&&O(n),n=r}}function O(t,n=!0){var r=!1;if((n||(t.f&$t)!==0)&&t.nodes_start!==null){for(var e=t.nodes_start,l=t.nodes_end;e!==null;){var a=e===l?null:P(e);e.remove(),e=a}r=!0}Ft(t,n&&!r),W(t,0),A(t,z);var s=t.transitions;if(s!==null)for(const _ of s)_.stop();bt(t);var i=t.parent;i!==null&&i.first!==null&&Ct(t),t.next=t.prev=t.teardown=t.ctx=t.deps=t.fn=t.nodes_start=t.nodes_end=null}function Ct(t){var n=t.parent,r=t.prev,e=t.next;r!==null&&(r.next=e),e!==null&&(e.prev=r),n!==null&&(n.first===t&&(n.first=e),n.last===t&&(n.last=r))}function hn(t,n){var r=[];qt(t,r,!0),dn(r,()=>{O(t),n&&n()})}function dn(t,n){var r=t.length;if(r>0){var e=()=>--r||n();for(var l of t)l.out(e)}else n()}function qt(t,n,r){if((t.f&S)===0){if(t.f^=S,t.transitions!==null)for(const s of t.transitions)(s.is_global||r)&&n.push(s);for(var e=t.first;e!==null;){var l=e.next,a=(e.f&yt)!==0||(e.f&k)!==0;qt(e,n,a?r:!1),e=l}}}function wr(t){Yt(t,!0)}function Yt(t,n){if((t.f&S)!==0){t.f^=S,(t.f&v)===0&&(t.f^=v),L(t)&&(A(t,D),tt(t));for(var r=t.first;r!==null;){var e=r.next,l=(r.f&yt)!==0||(r.f&k)!==0;Yt(r,l?n:!1),r=e}if(t.transitions!==null)for(const a of t.transitions)(a.is_global||n)&&a.in()}}let C=[],et=[];function Lt(){var t=C;C=[],wt(t)}function wn(){var t=et;et=[],wt(t)}function Er(t){C.length===0&&queueMicrotask(Lt),C.push(t)}function ht(){C.length>0&&Lt(),et.length>0&&wn()}let M=!1,V=!1,G=null,B=!1,ot=!1;function dt(t){ot=t}let b=[];let u=null,y=!1;function K(t){u=t}let o=null;function Z(t){o=t}let m=null;function En(t){m=t}let c=null,d=0,T=null;function yn(t){T=t}let jt=1,$=0,R=!1;function Mt(){return++jt}function L(t){var p;var n=t.f;if((n&D)!==0)return!0;if((n&N)!==0){var r=t.deps,e=(n&E)!==0;if(r!==null){var l,a,s=(n&H)!==0,i=e&&o!==null&&!R,_=r.length;if(s||i){var w=t,j=w.parent;for(l=0;l<_;l++)a=r[l],(s||!((p=a==null?void 0:a.reactions)!=null&&p.includes(w)))&&(a.reactions??(a.reactions=[])).push(w);s&&(w.f^=H),i&&j!==null&&(j.f&E)===0&&(w.f^=E)}for(l=0;l<_;l++)if(a=r[l],L(a)&&It(a),a.wv>t.wv)return!0}(!e||o!==null&&!R)&&A(t,v)}return!1}function gn(t,n){for(var r=n;r!==null;){if((r.f&U)!==0)try{r.fn(t);return}catch{r.f^=U}r=r.parent}throw M=!1,t}function mn(t){return(t.f&z)===0&&(t.parent===null||(t.parent.f&U)===0)}function X(t,n,r,e){if(M){if(r===null&&(M=!1),mn(n))throw t;return}r!==null&&(M=!0);{gn(t,n);return}}function Bt(t,n,r=!0){var e=t.reactions;if(e!==null)for(var l=0;l<e.length;l++){var a=e[l];(a.f&g)!==0?Bt(a,n,!1):n===a&&(r?A(a,D):(a.f&v)!==0&&A(a,N),tt(a))}}function Ut(t){var it;var n=c,r=d,e=T,l=u,a=R,s=m,i=f,_=y,w=t.f;c=null,d=0,T=null,R=(w&E)!==0&&(y||!B||u===null),u=(w&(k|q))===0?t:null,m=null,vt(t.ctx),y=!1,$++;try{var j=(0,t.fn)(),p=t.deps;if(c!==null){var h;if(W(t,d),p!==null&&d>0)for(p.length=d+c.length,h=0;h<c.length;h++)p[d+h]=c[h];else t.deps=p=c;if(!R)for(h=d;h<p.length;h++)((it=p[h]).reactions??(it.reactions=[])).push(t)}else p!==null&&d<p.length&&(W(t,d),p.length=d);if(Q()&&T!==null&&!y&&p!==null&&(t.f&(g|N|D))===0)for(h=0;h<T.length;h++)Bt(T[h],t);return l!==null&&$++,j}finally{c=n,d=r,T=e,u=l,R=a,m=s,vt(i),y=_}}function Tn(t,n){let r=n.reactions;if(r!==null){var e=Vt.call(r,t);if(e!==-1){var l=r.length-1;l===0?r=n.reactions=null:(r[e]=r[l],r.pop())}}r===null&&(n.f&g)!==0&&(c===null||!c.includes(n))&&(A(n,N),(n.f&(E|H))===0&&(n.f^=H),St(n),W(n,0))}function W(t,n){var r=t.deps;if(r!==null)for(var e=n;e<r.length;e++)Tn(t,r[e])}function ft(t){var n=t.f;if((n&z)===0){A(t,v);var r=o,e=f,l=B;o=t,B=!0;try{(n&st)!==0?pn(t):Ft(t),bt(t);var a=Ut(t);t.teardown=typeof a=="function"?a:null,t.wv=jt;var s=t.deps,i}catch(_){X(_,t,r,e||t.ctx)}finally{B=l,o=r}}}function xn(){try{Xt()}catch(t){if(G!==null)X(t,G,null);else throw t}}function Ht(){try{for(var t=0;b.length>0;){t++>1e3&&xn();var n=b,r=n.length;b=[];for(var e=0;e<r;e++){var l=n[e];(l.f&v)===0&&(l.f^=v);var a=kn(l);An(a)}}}finally{V=!1,G=null}}function An(t){var n=t.length;if(n!==0)for(var r=0;r<n;r++){var e=t[r];if((e.f&(z|S))===0)try{L(e)&&(ft(e),e.deps===null&&e.first===null&&e.nodes_start===null&&(e.teardown===null?Ct(e):e.fn=null))}catch(l){X(l,e,null,e.ctx)}}}function tt(t){V||(V=!0,queueMicrotask(Ht));for(var n=G=t;n.parent!==null;){n=n.parent;var r=n.f;if((r&(q|k))!==0){if((r&v)===0)return;n.f^=v}}b.push(n)}function kn(t){for(var n=[],r=t.first;r!==null;){var e=r.f,l=(e&k)!==0,a=l&&(e&v)!==0;if(!a&&(e&S)===0){if((e&Et)!==0)n.push(r);else if(l)r.f^=v;else{var s=u;try{u=r,L(r)&&ft(r)}catch(w){X(w,r,null,r.ctx)}finally{u=s}}var i=r.first;if(i!==null){r=i;continue}}var _=r.parent;for(r=r.next;r===null&&_!==null;)r=_.next,_=_.parent}return n}function Rn(t){var n;for(ht();b.length>0;)V=!0,Ht(),ht();return n}async function yr(){await Promise.resolve(),Rn()}function Dn(t){var n=t.f,r=(n&g)!==0;if(u!==null&&!y){m!==null&&m.includes(t)&&tn();var e=u.deps;t.rv<$&&(t.rv=$,c===null&&e!==null&&e[d]===t?d++:c===null?c=[t]:(!R||!c.includes(t))&&c.push(t))}else if(r&&t.deps===null&&t.effects===null){var l=t,a=l.parent;a!==null&&(a.f&E)===0&&(l.f^=E)}return r&&(l=t,L(l)&&It(l)),t.v}function gr(t){var n=y;try{return y=!0,t()}finally{y=n}}const On=-7169;function A(t,n){t.f=t.f&On|n}function mr(t){if(!(typeof t!="object"||!t||t instanceof EventTarget)){if(ct in t)lt(t);else if(!Array.isArray(t))for(let n in t){const r=t[n];typeof r=="object"&&r&&ct in r&&lt(r)}}}function lt(t,n=new Set){if(typeof t=="object"&&t!==null&&!(t instanceof EventTarget)&&!n.has(t)){n.add(t),t instanceof Date&&t.getTime();for(let e in t)try{lt(t[e],n)}catch{}const r=Kt(t);if(r!==Object.prototype&&r!==Array.prototype&&r!==Map.prototype&&r!==Set.prototype&&r!==Date.prototype){const e=Gt(r);for(let l in e){const a=e[l].get;if(a)try{a.call(t)}catch{}}}}}export{bn as $,Nn as A,K as B,Z as C,Sn as D,yt as E,u as F,o as G,sr as H,rt as I,rn as J,P as K,xt as L,rr as M,F as N,er as O,ln as P,At as Q,Ln as R,ir as S,In as T,pr as U,nt as V,un as W,$n as X,Wn as Y,ct as Z,Pn as _,Gt as a,ut as a0,Bn as a1,zn as a2,nr as a3,_t as a4,Mn as a5,en as a6,ar as a7,wr as a8,hn as a9,Pt as aa,cn as ab,Er as ac,jn as ad,qn as ae,_r as af,Gn as ag,Tt as ah,tr as ai,Kn as aj,Yn as ak,Vn as al,Hn as am,Zn as an,Rn as ao,Xn as ap,yr as aq,Wt as ar,f as b,ur as c,gr as d,Un as e,or as f,Kt as g,I as h,vn as i,dr as j,O as k,J as l,x as m,Fn as n,vr as o,wt as p,Dn as q,lr as r,fr as s,Cn as t,cr as u,mr as v,Ot as w,Jn as x,hr as y,Qn as z};
