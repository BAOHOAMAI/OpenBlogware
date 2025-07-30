import template from './werkl-cms-el-config-newest-listing.html.twig';
import './werkl-cms-el-config-newest-listing.scss';

const { EntityCollection, Criteria } = Shopware.Data;

Shopware.Component.register('werkl-cms-el-config-newest-listing', {
    template,

    inject: ['repositoryFactory'],

    mixins: [
        'cms-element'
    ],

    data() {
        return {
            categories: [],
            selectedCategories: null,
        };
    },

    computed: {
        blogCategoryRepository() {
            return this.repositoryFactory.create('werkl_blog_category');
        },

        blogListingSelectContext() {
            const context = { ...Shopware.Context.api };
            context.inheritance = true;

            return context;
        },

        blogCategoriesConfigValue() {
            return this.element.config.blogCategories.value;
        },
    },

    watch: {
        selectedCategories(value) {
            this.element.config.blogCategories.value = value.getIds();
            this.element.data.blogCategories = value
            // this.$set(this.element.data, 'blogCategories', value);
            this.$emit('element-update', this.element);
        },
    },

    created() {
        this.createdComponent();
    },

    methods: {
        async createdComponent() {
            this.initElementConfig('blog-newest-listing');
            await this.getSelectedCategories();
        },

        getSelectedCategories() {
            const context = { ...Shopware.Context.api };
            if (!Shopware.Utils.types.isEmpty(this.blogCategoriesConfigValue)) {
                const criteria = new Criteria();
                criteria.setIds(this.blogCategoriesConfigValue);

                this.blogCategoryRepository
                    .search(criteria, context)
                    .then((result) => {
                        this.selectedCategories = result;
                    });
            } else {
                this.selectedCategories = new EntityCollection(
                    this.blogCategoryRepository.route,
                    this.blogCategoryRepository.schema.entity,
                    context,
                    new Criteria(),
                );
            }
        },
    },
});
