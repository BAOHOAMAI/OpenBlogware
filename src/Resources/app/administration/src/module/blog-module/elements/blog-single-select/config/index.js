import template from './sw-cms-el-config-blog-single-select.html.twig';
const { Mixin, Data } = Shopware;

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
            blogEntries: [],
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

        onBlogEntryChange(value) {
            console.log('Bài viết được chọn:', value);
            this.element.config.blogEntry.value = value;
            this.$emit('element-update', this.element); 
        },
    },

    watch: {
        'element.config.blogEntry.value': function (newValue) {
            console.log('Giá trị blogEntry đã thay đổi:', newValue);
        },
    },
};