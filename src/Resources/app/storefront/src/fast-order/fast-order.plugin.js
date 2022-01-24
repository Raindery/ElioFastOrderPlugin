import Plugin from 'src/plugin-system/plugin.class';
import HttpClient from 'src/service/http-client.service';
import DomAccess from 'src/helper/dom-access.helper';

export default class FastOrder extends Plugin{

    init(){
        this._client = new HttpClient();

        this.fastOrderProductPlugin = [];

        this.quantity = DomAccess.querySelector(this.el, '.fast-order-form-inputs-row-select-quantity');

        document.body.addEventListener('click', this.onBodyClick.bind(this));
    }

    onBodyClick(){

        for(let plugin of this.fastOrderProductPlugin){
            plugin.hello();
        }
    }

    _registerEvents(){
        this.quantity.addEventListener('change', this._onChangeQuantity.bind(this));
    }

    _onChangeQuantity(){
        this._client.post('/fast-order/change-quantity', null, (response)=>{alert(response)});
    }
}



