(window.webpackJsonp=window.webpackJsonp||[]).push([["elio-fast-order"],{lrO3:function(t,e,n){"use strict";n.r(e);var r=n("FGIj"),i=n("k8s9"),o=n("nhVY"),s=n("41MI");function u(t){return(u="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(t){return typeof t}:function(t){return t&&"function"==typeof Symbol&&t.constructor===Symbol&&t!==Symbol.prototype?"symbol":typeof t})(t)}function c(t,e){if(!(t instanceof e))throw new TypeError("Cannot call a class as a function")}function a(t,e){for(var n=0;n<e.length;n++){var r=e[n];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(t,r.key,r)}}function l(t,e){return!e||"object"!==u(e)&&"function"!=typeof e?function(t){if(void 0===t)throw new ReferenceError("this hasn't been initialised - super() hasn't been called");return t}(t):e}function f(t){return(f=Object.setPrototypeOf?Object.getPrototypeOf:function(t){return t.__proto__||Object.getPrototypeOf(t)})(t)}function h(t,e){return(h=Object.setPrototypeOf||function(t,e){return t.__proto__=e,t})(t,e)}var p=function(t){function e(){return c(this,e),l(this,f(e).apply(this,arguments))}var n,r,u;return function(t,e){if("function"!=typeof e&&null!==e)throw new TypeError("Super expression must either be null or a function");t.prototype=Object.create(e&&e.prototype,{constructor:{value:t,writable:!0,configurable:!0}}),e&&h(t,e)}(e,t),n=e,(r=[{key:"init",value:function(){this.inputEventClick=s.a.isTouchDevice()?"touchstart":"click",this._client=new i.a(window.accessKey),this.input=this.el.children["search-input"],this.searchResult=this.el.children["search-result"],this._registerEvents()}},{key:"_registerEvents",value:function(){this.input.addEventListener("input",o.a.debounce(this._fetch.bind(this),250),{capture:!0,passive:!0}),document.body.addEventListener(this.inputEventClick,this._onBodyClick.bind(this))}},{key:"_fetch",value:function(){var t="/fast-order-test?searchInput="+this.input.value.trim();this._client.get(t,this._setContent.bind(this))}},{key:"_setContent",value:function(t){this.searchResult.innerHTML="",this.searchResult.insertAdjacentHTML("beforeend",t),this._registerEventsToSearchResult()}},{key:"_registerEventsToSearchResult",value:function(){for(var t=this,e=this.searchResult.getElementsByClassName("fast-order-search-result-product"),n=function(n){var r=e[n].getAttribute("data-product-number");e[n].addEventListener(t.inputEventClick,(function(){t.input.value=r}))},r=0;r<e.length;r++)n(r)}},{key:"_onBodyClick",value:function(){this.searchResult.innerHTML=""}}])&&a(n.prototype,r),u&&a(n,u),e}(r.a);window.PluginManager.register("FastOrderSearch",p,"[data-fast-order-search]")}},[["lrO3","runtime","vendor-node","vendor-shared"]]]);