import template from './fast-order-detail.html.twig';

const {Criteria} = Shopware.Data;

Shopware.Component.register('fast-order-detail', {

    inject: ['repositoryFactory'],
    template,

    props:{
        fastOrderId:{
            type: String,
            required: true,
        },
    },

    data: function (){
        return {
            fastOrderEntity: null,
            fastOrderLineItemEntities: null,
            fastOrderLineItemColumns: [
                {property: 'product.id', label: this.$tc('elio-fast-order.detail.columnIdLabel'), primary: true, inlineEdit:'string'},
                {property: 'product.productNumber', label: this.$tc('elio-fast-order.detail.columnProductNumberLabel'), inlineEdit: 'string'},
                {property: 'quantity', label: this.$tc('elio-fast-order.detail.columnQuantityLabel'), inlineEdit: 'string'},
            ],
        }
    },

    metaInfo() {
        return {
            title: this.$createTitle()
        };
    },

    computed: {

        fastOrderRepository() {
            return this.repositoryFactory.create('fast_order');
        },

        fastOrderProductLineItemRepository() {
            return this.repositoryFactory.create('fast_order_product_line_item');
        },

        fastOrderProductLineItemCriteria() {
           const criteria = new Criteria();

           criteria
               .addAssociation('product')
               .addAssociation('fastOrder')
               .addFilter(Criteria.equals('fastOrderId', this.fastOrderId))
               .addSorting(Criteria.sort('position'));

           return criteria;
        }
    },

    created() {
        this.fastOrderProductLineItemRepository
            .search(this.fastOrderProductLineItemCriteria, Shopware.Context.api)
            .then(results => {
                this.fastOrderLineItemEntities = results;
            });

        this.fastOrderRepository
            .get(this.fastOrderId, Shopware.Context.api)
            .then(fastOrderEntity => {
                this.fastOrderEntity = fastOrderEntity;
            })
    },

});