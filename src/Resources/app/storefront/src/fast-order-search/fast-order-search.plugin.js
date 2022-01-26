import Plugin from 'src/plugin-system/plugin.class';
import HttpClient from 'src/service/http-client.service';
import Debouncer from 'src/helper/debouncer.helper';

import DomAccess from 'src/helper/dom-access.helper';

export default class FastOrderSearch extends Plugin {



    static options = {
        fastOrderSearchInputDelay: 250,
    }

    init() {
        this._fastOrderPlugin = window.PluginManager
            .getPluginInstanceFromElement(document.querySelector('[data-fast-order]'), 'FastOrder');
        this._fastOrderProductPlugin = window.PluginManager
            .getPluginInstanceFromElement(this.el.parentElement, 'FastOrderProduct');

        this._client = new HttpClient();
        this.searchUrl = '/fast-order-search-products?searchInput=';

        this.productNumberInput = this._fastOrderProductPlugin.productNumberField;
        this.searchProductResult = DomAccess.querySelector(this.el, '.fast-order-search-result');

        this._registerEvents();
    }


    _registerEvents() {
        const inputDebouncer = Debouncer.debounce(
            this._fetchProducts.bind(this),
            this.options.fastOrderSearchInputDelay
        );

        // add listener to the productNumberInput input event
        this.productNumberInput.addEventListener(
            'input',
            inputDebouncer,
            {capture:true, passive: true,}
        );

        // on focus fetch
        this.productNumberInput.addEventListener('focus', this._onInputFocus.bind(this));
    }

    _onInputFocus(){
        if(this.productNumberInput.value !== ''){
            this._fetchProducts();
        }
    }

    /**
     * @private
     */
    _fetchProducts() {
        let inputTerm = this.productNumberInput.value.trim();
        this._client.get(this.searchUrl + inputTerm, this._showSearchResult.bind(this));
    }

    /**
     * @param data
     * @private
     */
    _showSearchResult(data) {
        this._clearSearchResult();
        this.searchProductResult.insertAdjacentHTML('beforeend', data);
        this._fastOrderPlugin.$emitter.subscribe('bodyClick', this._clearSearchResult.bind(this), {once: true});

        let products = DomAccess.querySelectorAll(this.searchProductResult, '.fast-order-search-result-product');

        for(let product of products){
            let productNumber = product.getAttribute('data-product-number');
            product.addEventListener(this._fastOrderPlugin.inputEventClick, this._productSelect.bind(this, productNumber));
        }
    }

    _clearSearchResult(){
        this.searchProductResult.innerHTML = '';
    }


    _productSelect(productNumber){
        this.productNumberInput.value = productNumber;
        this._client.get('/fast-order-search-products/select-product/' + productNumber, this._setSelectedProduct.bind(this));

        this._fastOrderProductPlugin.onSelectProduct(productNumber);
    }

    /**
     *
     * @param {Element} selectedProductContainer
     * @private
     */
    _productDeselect(selectedProductContainer){
        this.productNumberInput.type = 'input';
        selectedProductContainer.remove();

        this._fastOrderProductPlugin.onDeselectProduct();
    }

    _setSelectedProduct(data){
        this.productNumberInput.type = 'hidden';

        let selectedProductContainer = document.createElement('div');
        selectedProductContainer.className = 'fast-order-search-selected-product';
        selectedProductContainer.insertAdjacentHTML('beforeend', data);

        this.el.insertAdjacentElement('beforeend', selectedProductContainer);

        let deselectButton = DomAccess.querySelector(selectedProductContainer, '.fast-order-search-selected-product-deselect-button');
        deselectButton.addEventListener(this._fastOrderPlugin.inputEventClick, this._productDeselect.bind(this, selectedProductContainer));
    }

}