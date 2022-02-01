import template from './fast-order-detail.html.twig';

Shopware.Component.register('fast-order-detail', {
    inject: ['repositoryFactory'],
    template,


    metaInfo() {
        return {
            title: this.$createTitle()
        };
    },

    computed: {
        fastOrderProductLineItemRepository() {
            return this.repositoryFactory.create('fast_order_product_line_item');
        }
    }
});