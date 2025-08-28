import template from './werkl-cms-el-config-newest-listing.html.twig';
import './werkl-cms-el-config-newest-listing.scss';

const { Mixin } = Shopware;
const { EntityCollection, Criteria } = Shopware.Data;

Shopware.Component.register('werkl-cms-el-config-newest-listing', {
    template,

    inject: ['repositoryFactory'],

    mixins: [
        Mixin.getByName('cms-element'),
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
            return Array.isArray(this.element.config.blogCategories?.value) 
                ? this.element.config.blogCategories.value 
                : [];
        },
    },

    watch: {
        selectedCategories: {
            handler(value) {
                if (value) {
                    this.element.config.blogCategories.value = value.getIds();
                    this.element.data.blogCategories = value;
                    this.$emit('element-update', this.element);
                }
            },
            deep: true, 
        },
    },

    created() {
        this.createdComponent();
    },

    methods: {
        async createdComponent() {
            this.initElementConfig('blog-newest-listing');
            await this.getSelectedCategories();
            this.$nextTick(() => {
                console.log('Blog Category :', this.element.config.blogCategories.value);
            });
        },

        async getSelectedCategories() {
            const context = { ...Shopware.Context.api };

            if (this.blogCategoriesConfigValue.length > 0) {
                const criteria = new Criteria();
                criteria.setIds(this.blogCategoriesConfigValue);

                try {
                    const result = await this.blogCategoryRepository.search(criteria, context);
                    this.selectedCategories = result;
                } catch (error) {
                    this.selectedCategories = new EntityCollection(
                        this.blogCategoryRepository.route,
                        this.blogCategoryRepository.entityName,
                        context,
                        new Criteria()
                    );
                }
            } else {
                this.selectedCategories = new EntityCollection(
                    this.blogCategoryRepository.route,
                    this.blogCategoryRepository.entityName,
                    context,
                    new Criteria()
                );
            }
        },
    },
});
