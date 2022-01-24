import Plugin from 'src/plugin-system/plugin.class';


export default class FastOrderProduct extends Plugin{

    init(){
        this.fastOrderPlugin = window.PluginManager
            .getPluginInstanceFromElement(document.querySelector('[data-fast-order]'), 'FastOrder');


        this.fastOrderPlugin.fastOrderProductPlugin.push(this);
    }

    /**
     * @public
     */
    hello(){
        alert('Hello from FastOrderProduct');
    }
}