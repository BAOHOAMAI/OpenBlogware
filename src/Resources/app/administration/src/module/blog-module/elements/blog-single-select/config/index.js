import template from './sw-cms-el-config-blog-single-select.html.twig';

const { Mixin } = Shopware;

export default {
    template,

    inject: ['repositoryFactory'],

    mixins: [
        Mixin.getByName('cms-element'),
    ],

    data() {
        return {
            blogEntry: null,
            selectedEntry: null,
        };
    },
    computed: {
        blogEntryRepository() {
            return this.repositoryFactory.create('werkl_blog_entries');
        },
    },

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            this.initElementConfig('blog-single-select');
        },
    },
};
