import Plugin from 'src/plugin-system/plugin.class';
import HttpClient from 'src/service/http-client.service';
import Debouncer from 'src/helper/debouncer.helper';
import DeviceDetection from 'src/helper/device-detection.helper';

export default class FastOrderSearch extends Plugin {

    /**
     *
     * @type {{fastOrderSearchControllerFunctionRoute: string, fastOrderSearchQueryVariable: string, fastOrderSearchActionDelay: number, fastOrderSearchProductsBlocksClass: string, fastOrderSearchInputId: string, fastOrderSearchResultContainerId: string, fastOrderSearchProductNumberDataAttributeName: string}}
     */
    static options = {
        fastOrderSearchInputId: 'search-input',
        fastOrderSearchResultContainerId: 'search-result',
        fastOrderSearchControllerFunctionRoute: '/fast-order-test',
        fastOrderSearchQueryVariable: 'searchInput',
        fastOrderSearchProductsBlocksClass: 'fast-order-search-result-product',
        fastOrderSearchProductNumberDataAttributeName: 'data-product-number',

        fastOrderSearchActionDelay: 250,
    }

    init() {
        this.inputEventClick = (DeviceDetection.isTouchDevice()) ? 'touchstart' : 'click';
        this._client = new HttpClient();

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
     *
     * @private
     */
    _fetch() {
        let inputTerm = this.input.value.trim();
        let url = this.options.fastOrderSearchControllerFunctionRoute + '?' + this.options.fastOrderSearchQueryVariable +'=' + inputTerm;

        this._client.get(url, this._setContent.bind(this));
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

            productsBlocks[i].addEventListener(this.inputEventClick, ()=>{
                this.input.value = productNumber;
            })
        }
    }

    /**
     * @private
     */
    _onBodyClick(){
        this._clearSearchResult();
    }

    /**
     * @private
     */
    _clearSearchResult(){
        this.searchResult.innerHTML = '';
    }
}