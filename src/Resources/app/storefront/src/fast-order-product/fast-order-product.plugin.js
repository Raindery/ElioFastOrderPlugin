import Plugin from 'src/plugin-system/plugin.class';
import DomAccess from 'src/helper/dom-access.helper';
import HttpClient from 'src/service/http-client.service';


export default class FastOrderProduct extends Plugin{

    init(){
        this.fastOrderPlugin = window.PluginManager
            .getPluginInstanceFromElement(document.querySelector('[data-fast-order]'), 'FastOrder');
        this.fastOrderPlugin.fastOrderProductPlugin.push(this);

        this._client = new HttpClient();

        this._selectedProductNumber = null;
        this._calculatedPrice = 0;

        this.productNumberField = DomAccess.querySelector(this.el, '.fast-order-form-product-number-field');
        this.quantityField = DomAccess.querySelector(this.el, '.fast-order-form-product-quantity-field');


        this._registerEvents();
    }

    get calculatedPrice(){
        return this._calculatedPrice;
    }

    get selectedProductNumber(){
        return this._selectedProductNumber;
    }

    onSelectProduct(productNumber){
        this._selectedProductNumber = productNumber;

        if(this._selectedProductNumber !== null){
            this._calculate();
        }
    }

    onDeselectProduct(){
        this._selectedProductNumber = null;
        this._calculatedPrice = 0;
    }






    _registerEvents(){
        this.quantityField.addEventListener('change', this._onChangeQuantity.bind(this));
        this.$emitter.subscribe('setCalculatedPrice', this._onSetCalculatedPrice.bind(this));
    }

    _onSetCalculatedPrice(){
        console.log(this.calculatedPrice);
    }

    _onChangeQuantity(){
        if(this._selectedProductNumber !== null){
            this._calculate();
        }
    }

    _calculate(){
        let productNumber = this.productNumberField.value;
        let productQuantity = this.quantityField.value;
        let calculateUrl = '/fast-order/change-quantity/' + productNumber + '/' + productQuantity;

        this._client.get(calculateUrl, this._setCalculatedPrice.bind(this), 'application/json', true);
    }

    _setCalculatedPrice(data){
        this._calculatedPrice = JSON.parse(data).jsonCalculatedPrice;
        this.$emitter.publish('setCalculatedPrice');
        this.fastOrderPlugin.calculateTotalAmount();
    }
}