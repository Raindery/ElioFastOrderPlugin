import FastOrderSearch from "./fast-order-search/fast-order-search.plugin";

const PluginManager = window.PluginManager;

PluginManager.register('FastOrderSearch', FastOrderSearch, '[data-fast-order-search]');