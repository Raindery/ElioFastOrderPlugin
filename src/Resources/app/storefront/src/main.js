import FastOrderSearch from "./fast-order-search/fast-order-search.plugin";
import FastOrder from "./fast-order/fast-order.plugin";
import FastOrderProduct from "./fast-order-product/fast-order-product.plugin";

const PluginManager = window.PluginManager;

PluginManager.register('FastOrderSearch', FastOrderSearch, '[data-fast-order-search]');
PluginManager.register('FastOrder', FastOrder, '[data-fast-order]');
PluginManager.register('FastOrderProduct', FastOrderProduct, '[data-fast-order-product]');