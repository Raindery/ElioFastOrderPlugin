import './page/fast-order-index';
import deDE from './snippet/de-DE.json';
import enGB from './snippet/en-GB.json';

Shopware.Module.register('elio-fast-order', {
    type:'plugin',
    name: 'ElioFastOrder',
    title: 'elio-fast-order.general.mainMenuItemGeneral',
    description: 'elio-fast-order.general.descriptionTextModule',
    color: '#ff3d58',
    icon: 'default-shopping-paper-bag-product',

    routes:{
      overview: {
          component: 'fast-order-index',
          path: 'overview'
      },
      detail: {
          component: 'fast-order-detail',
          path: 'detail/:id',
          meta: {
              parentPath: 'elio.fast.order.overview'
          },
          props:{
              default: ($route)=>{
                  return {fastOrderId: $route.params.id}
              },
          }
      }
    },

    snippets:{
        'de-DE': deDE,
        'en-GB': enGB
    },

    navigation: [{
        label: 'elio-fast-order.general.mainMenuItemGeneral',
        color: '#ff3d58',
        path: 'elio.fast.order.overview',
        icon: 'default-shopping-paper-bag-product',
        parent: 'sw-order'
    }]
});