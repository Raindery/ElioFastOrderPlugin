(window.webpackJsonp=window.webpackJsonp||[]).push([["elio-fast-order"],{lrO3:function(t,e,r){"use strict";r.r(e);var n=r("FGIj"),i=r("k8s9"),o=r("nhVY"),s=r("41MI");function a(t){return(a="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(t){return typeof t}:function(t){return t&&"function"==typeof Symbol&&t.constructor===Symbol&&t!==Symbol.prototype?"symbol":typeof t})(t)}function u(t,e){if(!(t instanceof e))throw new TypeError("Cannot call a class as a function")}function c(t,e){for(var r=0;r<e.length;r++){var n=e[r];n.enumerable=n.enumerable||!1,n.configurable=!0,"value"in n&&(n.writable=!0),Object.defineProperty(t,n.key,n)}}function l(t,e){return!e||"object"!==a(e)&&"function"!=typeof e?function(t){if(void 0===t)throw new ReferenceError("this hasn't been initialised - super() hasn't been called");return t}(t):e}function h(t){return(h=Object.setPrototypeOf?Object.getPrototypeOf:function(t){return t.__proto__||Object.getPrototypeOf(t)})(t)}function f(t,e){return(f=Object.setPrototypeOf||function(t,e){return t.__proto__=e,t})(t,e)}var d,p,b,y=function(t){function e(){return u(this,e),l(this,h(e).apply(this,arguments))}var r,n,a;return function(t,e){if("function"!=typeof e&&null!==e)throw new TypeError("Super expression must either be null or a function");t.prototype=Object.create(e&&e.prototype,{constructor:{value:t,writable:!0,configurable:!0}}),e&&f(t,e)}(e,t),r=e,(n=[{key:"init",value:function(){this.inputEventClick=s.a.isTouchDevice()?"touchstart":"click",this._client=new i.a,this.input=this.el.children[this.options.fastOrderSearchInputId],this.searchResult=this.el.children[this.options.fastOrderSearchResultContainerId],this._registerEvents()}},{key:"_registerEvents",value:function(){var t=this;this.input.addEventListener("input",o.a.debounce(this._fetch.bind(this),this.options.fastOrderSearchActionDelay),{capture:!0,passive:!0}),this.input.addEventListener("focus",(function(){""!==t.input.value&&t._fetch()})),document.body.addEventListener(this.inputEventClick,this._onBodyClick.bind(this))}},{key:"_fetch",value:function(){var t=this.input.value.trim(),e=this.options.fastOrderSearchControllerFunctionRoute+"?"+this.options.fastOrderSearchQueryVariable+"="+t;this._client.get(e,this._setContent.bind(this))}},{key:"_setContent",value:function(t){this._clearSearchResult(),this.searchResult.insertAdjacentHTML("beforeend",t),this._registerEventsToSearchResult()}},{key:"_registerEventsToSearchResult",value:function(){for(var t=this,e=this.searchResult.getElementsByClassName(this.options.fastOrderSearchProductsBlocksClass),r=function(r){var n=e[r].getAttribute(t.options.fastOrderSearchProductNumberDataAttributeName);e[r].addEventListener(t.inputEventClick,(function(){t.input.value=n}))},n=0;n<e.length;n++)r(n)}},{key:"_onBodyClick",value:function(){this._clearSearchResult()}},{key:"_clearSearchResult",value:function(){this.searchResult.innerHTML=""}}])&&c(r.prototype,n),a&&c(r,a),e}(n.a);b={fastOrderSearchInputId:"search-input",fastOrderSearchResultContainerId:"search-result",fastOrderSearchControllerFunctionRoute:"/fast-order-test",fastOrderSearchQueryVariable:"searchInput",fastOrderSearchProductsBlocksClass:"fast-order-search-result-product",fastOrderSearchProductNumberDataAttributeName:"data-product-number",fastOrderSearchActionDelay:250},(p="options")in(d=y)?Object.defineProperty(d,p,{value:b,enumerable:!0,configurable:!0,writable:!0}):d[p]=b,window.PluginManager.register("FastOrderSearch",y,"[data-fast-order-search]")}},[["lrO3","runtime","vendor-node","vendor-shared"]]]);