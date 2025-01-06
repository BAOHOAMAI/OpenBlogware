<?php
declare(strict_types=1);

namespace Werkl\OpenBlogware\Content\Blog\Aggregate;

use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\Framework\DataAbstractionLayer\MappingEntityDefinition;
use Shopware\Core\System\Tag\TagDefinition;
use Werkl\OpenBlogware\Content\Blog\BlogEntriesDefinition;


class BlogEntriesTagMappingDefinition extends MappingEntityDefinition
{
    public const ENTITY_NAME = 'werkl_blog_entries_tag';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new FkField('werkl_blog_entries_id', 'blogId', BlogEntriesDefinition::class))->addFlags(new PrimaryKey(), new Required()),
            (new FkField('tag_id', 'tagId', TagDefinition::class))->addFlags(new PrimaryKey(), new Required()),

            new ManyToOneAssociationField('blog', 'werkl_blog_entries_id', BlogEntriesDefinition::class, 'id', false),
            new ManyToOneAssociationField('tag', 'tag_id', TagDefinition::class, 'id', false),
        ]);
    }
}
