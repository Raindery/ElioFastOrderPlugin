import Plugin from 'src/plugin-system/plugin.class';
import HttpClient from 'src/service/http-client.service';
import DomAccess from 'src/helper/dom-access.helper';
import DeviceDetection from 'src/helper/device-detection.helper';

export default class FastOrder extends Plugin{

    init(){
        this.fastOrderProductPlugins = [];
        this.inputEventClick = (DeviceDetection.isTouchDevice()) ? 'touchstart' : 'click';

        this._client = new HttpClient();
        this._totalAmount = 0;
        this._showedForm = null;
        this._activeNavItem = null;

        this._totalAmountText = DomAccess.querySelector(this.el, '.fast-order-total-amount');

        this._navItemEnterArticles = DomAccess.querySelector(this.el, '#nav-item-enter-article');
        this._enterArticlesForm = DomAccess.querySelector(this.el, '#enter-articles-form');

        this._navItemUpload = DomAccess.querySelector(this.el, '#nav-item-upload');
        this._uploadForm = DomAccess.querySelector(this.el, '#upload-form');

        // event click on body for hide search result
        document.body.addEventListener(this.inputEventClick, this.bodyClick.bind(this));

        this._navItemEnterArticles.addEventListener(this.inputEventClick, this.showEnterArticlesForm.bind(this));
        this._navItemUpload.addEventListener(this.inputEventClick, this.showUploadForm.bind(this));

        // default active and show elements
        this.showEnterArticlesForm();
    }

    showEnterArticlesForm(){
        this.deactivateActiveElements();

        if(!this._navItemEnterArticles.classList.contains('fast-order-active')){
            this._navItemEnterArticles.classList.add('fast-order-active');
            this._activeNavItem = this._navItemEnterArticles;
        }
        if(!this._enterArticlesForm.classList.contains('fast-order-show-form')){
            this._enterArticlesForm.classList.add('fast-order-show-form');
            this._showedForm = this._enterArticlesForm;
        }
    }

    showUploadForm(){
        this.deactivateActiveElements();

        if(!this._navItemUpload.classList.contains('fast-order-active')){
            this._navItemUpload.classList.add('fast-order-active');
            this._activeNavItem = this._navItemUpload;
        }
        if(!this._uploadForm.classList.contains('fast-order-show-form')){
            this._uploadForm.classList.add('fast-order-show-form');
            this._showedForm = this._uploadForm;
        }
    }

    deactivateActiveElements(){
        if(this._showedForm){
            this._showedForm.classList.remove('fast-order-show-form');
        }
        if(this._activeNavItem){
            this._activeNavItem.classList.remove('fast-order-active');
        }
    }

    bodyClick(){
        this.$emitter.publish('bodyClick');
    }

    calculateTotalAmount() {

        let queryString = '';
        for(let product of this.fastOrderProductPlugins){
            if(product.selectedProductNumber !== null){
                queryString += 'productNumbers[]='
                    + product.selectedProductNumber
                    + '&productQuantities[]='
                    + product.quantityField.value
                    + '&';
            }
        }

        if(queryString !== ''){
            this._client.get('/fast-order/calculate-total-amount?' + queryString, (response) => {
                this._totalAmountText.innerHTML = response;
            })
        }
        else{
            this._client.get('/fast-order/reset-price', (response) => {
                this._totalAmountText.innerHTML = response;
            })
        }


    }
}



