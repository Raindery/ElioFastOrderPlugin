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
        this._totalAmountText = DomAccess.querySelector(this.el, '.fast-order-total-amount');
        // event click on body for hide search result
        document.body.addEventListener(this.inputEventClick, this.bodyClick.bind(this));
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

    _displayTotalAmount(){
        console.log(this._totalAmount);
    }
}



