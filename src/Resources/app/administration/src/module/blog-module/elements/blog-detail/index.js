import './component';
import './config';
import './preview';

Shopware.Service('cmsService').registerCmsElement({
    name: 'blog-detail',
    label: 'Blog Detail',
    component: 'werkl-blog-el-blog-detail',
    configComponent: 'sw-cms-el-config-blog-detail',
    previewComponent: 'werkl-blog-el-blog-detail-preview',
    defaultConfig: {
        showCategory: {
            source: 'static',
            value: true,
        },
        showAuthor: {
            source: 'static',
            value: true,
        },
        fullWidth: {
            source: 'static',
            value: false,
        },
    },
});
