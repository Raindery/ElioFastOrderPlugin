import deDE from './snippet/de-DE';
import enGB from './snippet/en-GB';

Shopware.Module.register('fast-order-administration', {
    type: 'plugin',
    name: 'FastOrderAdministration',
    title: 'elio-fast-order-administration.general.mainMenuItemGeneral',
    description: 'elio-fast-order-administration.general.descriptionTextModule',
    color: '#ff3d58',
    icon: 'default-shopping-paper-bag-product',

    snippets: {
        'de-DE': deDE,
        'en-GB': enGB
    }
})



