import Plugin from 'src/plugin-system/plugin.class';
import HttpClient from 'src/service/http-client.service';

export default class FastOrderSearch extends Plugin {

    init() {
        this._client = new HttpClient(window.accessKey);

        this.input = this.el.children['search-input'];
        this.searchResult = this.el.children['search-result'];

        this._registerEvents();
    }

    _registerEvents() {
        this.input.oninput = this._fetch.bind(this);
    }

    _fetch() {
        let inputTerm = this.input.value;
        let url = '/fast-order-test?searchInput=' + inputTerm;
        this._client.get(url, this._setContent.bind(this), 'application', true);
    }

    _setContent(data) {

    }
}