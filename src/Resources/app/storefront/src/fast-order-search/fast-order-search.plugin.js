import Plugin from 'src/plugin-system/plugin.class';
import HttpClient from 'src/service/http-client.service';
import Debouncer from 'src/helper/debouncer.helper';
import DeviceDetection from 'src/helper/device-detection.helper';
import DomAccess from 'src/helper/dom-access.helper';

export default class FastOrderSearch extends Plugin {

    /**
     *
     * @type {{fastOrderSearchControllerFunctionRoute: string, fastOrderSearchQueryVariable: string, fastOrderSearchActionDelay: number, fastOrderSearchProductsBlocksClass: string, fastOrderSearchInputId: string, fastOrderSearchResultContainerId: string, fastOrderSearchProductNumberDataAttributeName: string}}
     */
    static options = {
        fastOrderSearchInputId: 'search-input',
        fastOrderSearchResultContainerId: 'search-result',
        fastOrderSearchControllerFunctionRoute: '/fast-order-search-products',
        fastOrderSearchQueryVariable: 'searchInput',
        fastOrderSearchProductsBlocksClass: 'fast-order-search-result-product',
        fastOrderSearchProductNumberDataAttributeName: 'data-product-number',

        fastOrderSearchSelectedProductClassName: 'fast-order-search-selected-product',
        fastOrderSearchActionDelay: 250,
    }

    init() {
        this.inputEventClick = (DeviceDetection.isTouchDevice()) ? 'touchstart' : 'click';

        this._client = new HttpClient();
        this.searchUrl = this.options.fastOrderSearchControllerFunctionRoute + '?' + this.options.fastOrderSearchQueryVariable + '=';

        this.input = this.el.children[this.options.fastOrderSearchInputId];
        this.searchResult = this.el.children[this.options.fastOrderSearchResultContainerId];

        this._registerEvents();
    }

    /**
     *
     * @private
     */
    _registerEvents() {

        // on input fetch
        this.input.addEventListener(
            'input',
            Debouncer.debounce(this._fetch.bind(this), this.options.fastOrderSearchActionDelay),
            {
                capture: true,
                passive: true,
            },
            );

        // on focus fetch
        this.input.addEventListener('focus', ()=>{
            if(this.input.value !== ''){
                this._fetch();
            }
        })

        // event click on body for hide search result
        document.body.addEventListener(this.inputEventClick, this._onBodyClick.bind(this));
    }

    /**
     * @private
     */
    _fetch() {
        let inputTerm = this.input.value.trim();
        this._client.get(this.searchUrl + inputTerm, this._setContent.bind(this));
    }

    /**
     *
     * @param data
     * @private
     */
    _setContent(data) {
        this._clearSearchResult();
        this.searchResult.insertAdjacentHTML('beforeend', data);

        this._registerEventsToSearchResult();
    }

    /**
     *
     * @private
     */
    _registerEventsToSearchResult(){

        // register events to product blocks for fill the input with product number
        let productsBlocks = this.searchResult.getElementsByClassName(this.options.fastOrderSearchProductsBlocksClass);

        for(let i = 0; i<productsBlocks.length; i++){
            let productNumber = productsBlocks[i].getAttribute(this.options.fastOrderSearchProductNumberDataAttributeName);
            productsBlocks[i].addEventListener(this.inputEventClick, () => {
                this._onProductSelect(productNumber);
            });
        }
    }

    /**
     * @private
     */
    _onBodyClick(){
        this._clearSearchResult();
    }

    _onProductSelect(productNumber){
        this.input.value = productNumber;
        this._client.get('/fast-order-search-products/select-product/' + productNumber, this._setSelectedProduct.bind(this));
    }

    /**
     *
     * @param {Element} selectedProductContainer
     * @private
     */
    _onProductDeselect(selectedProductContainer){
        this.input.type = 'input';

        selectedProductContainer.remove();
    }

    _setSelectedProduct(data){
        this.input.type = 'hidden';

        let selectedProductContainer = document.createElement('div');
        selectedProductContainer.className = this.options.fastOrderSearchSelectedProductClassName;
        selectedProductContainer.insertAdjacentHTML('beforeend', data);

        this.el.insertAdjacentElement('beforeend', selectedProductContainer);

        let deselectButton = DomAccess.querySelector(selectedProductContainer, '.fast-order-search-selected-product-deselect-button');
        deselectButton.addEventListener(this.inputEventClick, this._onProductDeselect.bind(this, selectedProductContainer));
    }

    /**
     * @private
     */
    _clearSearchResult(){
        this.searchResult.innerHTML = '';
    }
}