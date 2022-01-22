import Plugin from 'src/plugin-system/plugin.class';
import HttpClient from 'src/service/http-client.service';
import Debouncer from 'src/helper/debouncer.helper';
import DeviceDetection from 'src/helper/device-detection.helper';

export default class FastOrderSearch extends Plugin {

    init() {

        this.inputEventClick = (DeviceDetection.isTouchDevice()) ? 'touchstart' : 'click';
        this._client = new HttpClient(window.accessKey);

        this.input = this.el.children['search-input'];
        this.searchResult = this.el.children['search-result'];
        this._registerEvents();
    }

    /**
     *
     * @private
     */
    _registerEvents() {
        this.input.addEventListener(
            'input',
            Debouncer.debounce(this._fetch.bind(this), 250),
            {
                capture: true,
                passive: true,
            },
            );

        document.body.addEventListener(this.inputEventClick, this._onBodyClick.bind(this));
    }

    /**
     *
     * @private
     */
    _fetch() {
        let inputTerm = this.input.value.trim();
        let url = '/fast-order-test?searchInput=' + inputTerm;

        this._client.get(url, this._setContent.bind(this));
    }

    /**
     *
     * @param data
     * @private
     */
    _setContent(data) {
        this.searchResult.innerHTML = '';
        this.searchResult.insertAdjacentHTML('beforeend', data);

        this._registerEventsToSearchResult();
    }

    /**
     *
     * @private
     */
    _registerEventsToSearchResult(){
        // register events to product blocks for fill the input with product number
        let productsBlocks = this.searchResult.getElementsByClassName('fast-order-search-result-product');
        for(let i = 0; i<productsBlocks.length; i++){

            let productNumber = productsBlocks[i].getAttribute('data-product-number');

            productsBlocks[i].addEventListener(this.inputEventClick, ()=>{
                this.input.value = productNumber;
            })
        }
    }

    _onBodyClick(){
        this.searchResult.innerHTML = '';
    }
}