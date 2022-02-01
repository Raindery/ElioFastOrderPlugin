import template from './fast-order-index.html.twig';

const {Criteria} = Shopware.Data;

Shopware.Component.register('fast-order-index', {
    inject: ['repositoryFactory'],
    template,

    data: function () {
        return {
            fastOrderEntities: undefined,
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
            .then((result) => {
                this.fastOrderEntities = result;
                console.log(this.fastOrderEntities);
            })
    }
});