import Plugin from 'src/plugin-system/plugin.class';
import DomAccess from 'src/helper/dom-access.helper';
import HttpClient from 'src/service/http-client.service';


export default class FastOrderProduct extends Plugin{

    init(){
        this.fastOrderPlugin = window.PluginManager
            .getPluginInstanceFromElement(document.querySelector('[data-fast-order]'), 'FastOrder');
        this.fastOrderPlugin.fastOrderProductPlugins.push(this);

        this._client = new HttpClient();
        this._selectedProductNumber = null;
        this._productPriceText = this.el.nextElementSibling.querySelector('.fast-order-main-product-total-price');

        this.productNumberField = DomAccess.querySelector(this.el, '.fast-order-form-product-number-field');
        this.quantityField = DomAccess.querySelector(this.el, '.fast-order-form-product-quantity-field');

        this._registerEvents();
    }

    get selectedProductNumber(){
        return this._selectedProductNumber;
    }

    _registerEvents(){
        this.quantityField.addEventListener('change', this._onChangeQuantity.bind(this));

    }

    onSelectProduct(productNumber){
        this._selectedProductNumber = productNumber;

        if(this._selectedProductNumber !== null){
            this._calculate();
            this.fastOrderPlugin.calculateTotalAmount();
        }
    }

    onDeselectProduct(){
        this._selectedProductNumber = null;

        this._client.get('/fast-order/reset-price', (response) =>{

            if(this._productPriceText != null){
                this._productPriceText.innerHTML = response;
            }

            this.fastOrderPlugin.calculateTotalAmount();
        })
    }

    _calculate(){
        let productNumber = this.productNumberField.value;
        let productQuantity = this.quantityField.value;
        let calculateUrl = '/fast-order/calculate-product-price/' + productNumber + '/' + productQuantity;

        this._client.get(calculateUrl, (response) => {
            if(this._productPriceText !== null){
                this._productPriceText.innerHTML =  response;
            }
        });
    }

    _onChangeQuantity(){
        if(this._selectedProductNumber !== null){
            this._calculate();
            this.fastOrderPlugin.calculateTotalAmount();
        }
    }
}