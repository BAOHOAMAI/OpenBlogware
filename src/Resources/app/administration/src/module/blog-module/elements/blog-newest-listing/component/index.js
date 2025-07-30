import template from './werkl-cms-el-newest-listing.html.twig';
import './werkl-cms-el-newest-listing.scss';

const { Mixin } = Shopware;

export default {
    template,

    mixins: [
        Mixin.getByName('cms-element'),
    ],

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            this.initElementConfig('blog-newest-listing');
        },
    },
};
