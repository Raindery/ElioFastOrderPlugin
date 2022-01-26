(window.webpackJsonp=window.webpackJsonp||[]).push([["elio-fast-order"],{lrO3:function(t,e,r){"use strict";r.r(e);var n=r("FGIj"),o=r("k8s9"),u=r("nhVY"),i=r("gHbT");function c(t){return(c="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(t){return typeof t}:function(t){return t&&"function"==typeof Symbol&&t.constructor===Symbol&&t!==Symbol.prototype?"symbol":typeof t})(t)}function a(t,e){if(!(t instanceof e))throw new TypeError("Cannot call a class as a function")}function s(t,e){for(var r=0;r<e.length;r++){var n=e[r];n.enumerable=n.enumerable||!1,n.configurable=!0,"value"in n&&(n.writable=!0),Object.defineProperty(t,n.key,n)}}function l(t,e){return!e||"object"!==c(e)&&"function"!=typeof e?function(t){if(void 0===t)throw new ReferenceError("this hasn't been initialised - super() hasn't been called");return t}(t):e}function d(t){return(d=Object.setPrototypeOf?Object.getPrototypeOf:function(t){return t.__proto__||Object.getPrototypeOf(t)})(t)}function f(t,e){return(f=Object.setPrototypeOf||function(t,e){return t.__proto__=e,t})(t,e)}var h,p,y,b=function(t){function e(){return a(this,e),l(this,d(e).apply(this,arguments))}var r,n,c;return function(t,e){if("function"!=typeof e&&null!==e)throw new TypeError("Super expression must either be null or a function");t.prototype=Object.create(e&&e.prototype,{constructor:{value:t,writable:!0,configurable:!0}}),e&&f(t,e)}(e,t),r=e,(n=[{key:"init",value:function(){this._fastOrderPlugin=window.PluginManager.getPluginInstanceFromElement(document.querySelector("[data-fast-order]"),"FastOrder"),this._fastOrderProductPlugin=window.PluginManager.getPluginInstanceFromElement(this.el.parentElement,"FastOrderProduct"),this._client=new o.a,this.searchUrl="/fast-order-search-products?searchInput=",this.productNumberInput=this._fastOrderProductPlugin.productNumberField,this.searchProductResult=i.a.querySelector(this.el,".fast-order-search-result"),this._registerEvents()}},{key:"_registerEvents",value:function(){var t=u.a.debounce(this._fetchProducts.bind(this),this.options.fastOrderSearchInputDelay);this.productNumberInput.addEventListener("input",t,{capture:!0,passive:!0}),this.productNumberInput.addEventListener("focus",this._onInputFocus.bind(this))}},{key:"_onInputFocus",value:function(){""!==this.productNumberInput.value&&this._fetchProducts()}},{key:"_fetchProducts",value:function(){var t=this.productNumberInput.value.trim();this._client.get(this.searchUrl+t,this._showSearchResult.bind(this))}},{key:"_showSearchResult",value:function(t){this._clearSearchResult(),this.searchProductResult.insertAdjacentHTML("beforeend",t),this._fastOrderPlugin.$emitter.subscribe("bodyClick",this._clearSearchResult.bind(this),{once:!0});var e=i.a.querySelectorAll(this.searchProductResult,".fast-order-search-result-product"),r=!0,n=!1,o=void 0;try{for(var u,c=e[Symbol.iterator]();!(r=(u=c.next()).done);r=!0){var a=u.value,s=a.getAttribute("data-product-number");a.addEventListener(this._fastOrderPlugin.inputEventClick,this._productSelect.bind(this,s))}}catch(t){n=!0,o=t}finally{try{r||null==c.return||c.return()}finally{if(n)throw o}}}},{key:"_clearSearchResult",value:function(){this.searchProductResult.innerHTML=""}},{key:"_productSelect",value:function(t){this.productNumberInput.value=t,this._client.get("/fast-order-search-products/select-product/"+t,this._setSelectedProduct.bind(this)),this._fastOrderProductPlugin.onSelectProduct(t)}},{key:"_productDeselect",value:function(t){this.productNumberInput.type="input",t.remove(),this._fastOrderProductPlugin.onDeselectProduct()}},{key:"_setSelectedProduct",value:function(t){this.productNumberInput.type="hidden";var e=document.createElement("div");e.className="fast-order-search-selected-product",e.insertAdjacentHTML("beforeend",t),this.el.insertAdjacentElement("beforeend",e),i.a.querySelector(e,".fast-order-search-selected-product-deselect-button").addEventListener(this._fastOrderPlugin.inputEventClick,this._productDeselect.bind(this,e))}}])&&s(r.prototype,n),c&&s(r,c),e}(n.a);y={fastOrderSearchInputDelay:250},(p="options")in(h=b)?Object.defineProperty(h,p,{value:y,enumerable:!0,configurable:!0,writable:!0}):h[p]=y;var m=r("41MI");function v(t){return(v="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(t){return typeof t}:function(t){return t&&"function"==typeof Symbol&&t.constructor===Symbol&&t!==Symbol.prototype?"symbol":typeof t})(t)}function _(t,e){if(!(t instanceof e))throw new TypeError("Cannot call a class as a function")}function P(t,e){for(var r=0;r<e.length;r++){var n=e[r];n.enumerable=n.enumerable||!1,n.configurable=!0,"value"in n&&(n.writable=!0),Object.defineProperty(t,n.key,n)}}function g(t,e){return!e||"object"!==v(e)&&"function"!=typeof e?function(t){if(void 0===t)throw new ReferenceError("this hasn't been initialised - super() hasn't been called");return t}(t):e}function O(t){return(O=Object.setPrototypeOf?Object.getPrototypeOf:function(t){return t.__proto__||Object.getPrototypeOf(t)})(t)}function w(t,e){return(w=Object.setPrototypeOf||function(t,e){return t.__proto__=e,t})(t,e)}var S=function(t){function e(){return _(this,e),g(this,O(e).apply(this,arguments))}var r,n,u;return function(t,e){if("function"!=typeof e&&null!==e)throw new TypeError("Super expression must either be null or a function");t.prototype=Object.create(e&&e.prototype,{constructor:{value:t,writable:!0,configurable:!0}}),e&&w(t,e)}(e,t),r=e,(n=[{key:"init",value:function(){this.fastOrderProductPlugins=[],this.inputEventClick=m.a.isTouchDevice()?"touchstart":"click",this._client=new o.a,this._totalAmount=0,this._totalAmountText=i.a.querySelector(this.el,".fast-order-total-amount"),document.body.addEventListener(this.inputEventClick,this.bodyClick.bind(this))}},{key:"bodyClick",value:function(){this.$emitter.publish("bodyClick")}},{key:"calculateTotalAmount",value:function(){var t=this,e="",r=!0,n=!1,o=void 0;try{for(var u,i=this.fastOrderProductPlugins[Symbol.iterator]();!(r=(u=i.next()).done);r=!0){var c=u.value;null!==c.selectedProductNumber&&(e+="productNumbers[]="+c.selectedProductNumber+"&productQuantities[]="+c.quantityField.value+"&")}}catch(t){n=!0,o=t}finally{try{r||null==i.return||i.return()}finally{if(n)throw o}}""!==e?this._client.get("/fast-order/calculate-total-amount?"+e,(function(e){t._totalAmountText.innerHTML=e})):this._client.get("/fast-order/reset-price",(function(e){t._totalAmountText.innerHTML=e}))}},{key:"_displayTotalAmount",value:function(){console.log(this._totalAmount)}},{key:"hello",value:function(){alert()}}])&&P(r.prototype,n),u&&P(r,u),e}(n.a);function k(t){return(k="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(t){return typeof t}:function(t){return t&&"function"==typeof Symbol&&t.constructor===Symbol&&t!==Symbol.prototype?"symbol":typeof t})(t)}function E(t,e){if(!(t instanceof e))throw new TypeError("Cannot call a class as a function")}function T(t,e){for(var r=0;r<e.length;r++){var n=e[r];n.enumerable=n.enumerable||!1,n.configurable=!0,"value"in n&&(n.writable=!0),Object.defineProperty(t,n.key,n)}}function j(t,e){return!e||"object"!==k(e)&&"function"!=typeof e?function(t){if(void 0===t)throw new ReferenceError("this hasn't been initialised - super() hasn't been called");return t}(t):e}function N(t){return(N=Object.setPrototypeOf?Object.getPrototypeOf:function(t){return t.__proto__||Object.getPrototypeOf(t)})(t)}function F(t,e){return(F=Object.setPrototypeOf||function(t,e){return t.__proto__=e,t})(t,e)}var I=function(t){function e(){return E(this,e),j(this,N(e).apply(this,arguments))}var r,n,u;return function(t,e){if("function"!=typeof e&&null!==e)throw new TypeError("Super expression must either be null or a function");t.prototype=Object.create(e&&e.prototype,{constructor:{value:t,writable:!0,configurable:!0}}),e&&F(t,e)}(e,t),r=e,(n=[{key:"init",value:function(){this.fastOrderPlugin=window.PluginManager.getPluginInstanceFromElement(document.querySelector("[data-fast-order]"),"FastOrder"),this.fastOrderPlugin.fastOrderProductPlugins.push(this),this._client=new o.a,this._selectedProductNumber=null,this._productPriceText=this.el.nextElementSibling.querySelector(".fast-order-main-product-total-price"),this.productNumberField=i.a.querySelector(this.el,".fast-order-form-product-number-field"),this.quantityField=i.a.querySelector(this.el,".fast-order-form-product-quantity-field"),this._registerEvents()}},{key:"_registerEvents",value:function(){this.quantityField.addEventListener("change",this._onChangeQuantity.bind(this))}},{key:"onSelectProduct",value:function(t){this._selectedProductNumber=t,null!==this._selectedProductNumber&&(this._calculate(),this.fastOrderPlugin.calculateTotalAmount())}},{key:"onDeselectProduct",value:function(){var t=this;this._selectedProductNumber=null,this._client.get("/fast-order/reset-price",(function(e){null!=t._productPriceText&&(t._productPriceText.innerHTML=e),t.fastOrderPlugin.calculateTotalAmount()}))}},{key:"_calculate",value:function(){var t=this,e="/fast-order/calculate-product-price/"+this.productNumberField.value+"/"+this.quantityField.value;this._client.get(e,(function(e){null!==t._productPriceText&&(t._productPriceText.innerHTML=e)}))}},{key:"_onChangeQuantity",value:function(){null!==this._selectedProductNumber&&(this._calculate(),this.fastOrderPlugin.calculateTotalAmount())}},{key:"selectedProductNumber",get:function(){return this._selectedProductNumber}}])&&T(r.prototype,n),u&&T(r,u),e}(n.a),A=window.PluginManager;A.register("FastOrder",S,"[data-fast-order]"),A.register("FastOrderProduct",I,"[data-fast-order-product]"),A.register("FastOrderSearch",b,"[data-fast-order-search]")}},[["lrO3","runtime","vendor-node","vendor-shared"]]]);