import Plugin from 'src/plugin-system/plugin.class';
import HttpClient from 'src/service/http-client.service';
import DomAccess from 'src/helper/dom-access.helper';
import DeviceDetection from 'src/helper/device-detection.helper';

export default class FastOrder extends Plugin{

    init(){
        this.fastOrderProductPlugin = [];
        this.inputEventClick = (DeviceDetection.isTouchDevice()) ? 'touchstart' : 'click';


        this._totalAmount = 0;
        this._totalAmountText = DomAccess
        // event click on body for hide search result
        document.body.addEventListener(this.inputEventClick, this.bodyClick.bind(this));
    }

    bodyClick(){
        this.$emitter.publish('bodyClick');
    }

    calculateTotalAmount() {
        for (let product of this.fastOrderProductPlugin) {
            if (product.selectedProductNumber == null) {
                continue;
            }

            this._totalAmount += product.calculatedPrice;


        }

        this._displayTotalAmount();
    }


    _displayTotalAmount(){
        console.log(this._totalAmount);
    }
















    hello(){
        alert();
    }
}



