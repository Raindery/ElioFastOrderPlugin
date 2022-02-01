import template from './fast-order-index.html.twig';

const {Criteria} = Shopware.Data;

Shopware.Component.register('fast-order-index', {
    inject: ['repositoryFactory'],
    template,

    data: function () {
        return {
            fastOrderEntities: undefined,
            fastOrderColumns: [
                {property: 'id', label: 'Id', primary: true, inlineEdit: 'string', routerLink: 'elio.fast.order.detail'},
                {property: 'sessionId', label: 'Session Id', inlineEdit: 'string'},
                {property: 'createdAt', label: 'Created At'}
            ]
        }
    },

    metaInfo() {
        return {
          title: this.$createTitle()
        };
    },

    computed: {
        fastOrderRepository(){
            return this.repositoryFactory.create('fast_order');
        }
    },

    created() {
        this.fastOrderRepository
            .search(new Criteria(), Shopware.Context.api)
            .then(result => {
                this.fastOrderEntities = result;
                console.log(this.fastOrderEntities);
            });
    },

    methods: {

    }
});