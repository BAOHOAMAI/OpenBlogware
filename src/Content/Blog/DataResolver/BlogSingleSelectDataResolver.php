<?php
declare(strict_types=1);

namespace Werkl\OpenBlogware\Content\Blog\DataResolver;

use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use Shopware\Core\Content\Cms\DataResolver\CriteriaCollection;
use Shopware\Core\Content\Cms\DataResolver\Element\AbstractCmsElementResolver;
use Shopware\Core\Content\Cms\DataResolver\Element\ElementDataCollection;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\ResolverContext;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\ContainsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\OrFilter;
use Werkl\OpenBlogware\Content\Blog\BlogEntriesDefinition;

class BlogSingleSelectDataResolver extends AbstractCmsElementResolver
{
    public function getType(): string
    {
        return 'blog-single-select';
    }

    public function collect(CmsSlotEntity $slot, ResolverContext $resolverContext): ?CriteriaCollection
    {
        /* get the config from the element */
        $config = $slot->getFieldConfig();

        $criteria = new Criteria();
        $blogEntryConfig = $config->get('blogEntry') ?? null;

        if ($blogEntryConfig === null) {
            return null;
        }

        /** @var mixed $blogId */
        $blogId = $blogEntryConfig->getValue();
        $criteria->addFilter(
            new EqualsFilter('id', $blogId)
        );
        $criteria->addFilter(new OrFilter([
            new ContainsFilter('customFields.salesChannelIds', $resolverContext->getSalesChannelContext()->getSalesChannelId()),
            new EqualsFilter('customFields.salesChannelIds', null),
        ]));
        $criteria->addAssociations(['blogAuthor', 'blogAuthor.media', 'blogAuthor.blogEntries', 'blogCategories', 'tags']);

        $criteriaCollection = new CriteriaCollection();

        $criteriaCollection->add(
            'werkl_blog_single_select',
            BlogEntriesDefinition::class,
            $criteria
        );

        return $criteriaCollection;
    }

    public function enrich(CmsSlotEntity $slot, ResolverContext $resolverContext, ElementDataCollection $result): void
    {
        $result = $result->get('werkl_blog_single_select') ?? null;

        if ($result !== null && $result->first() !== null) {
            $slot->setData($result->first());
        }
    }
}
