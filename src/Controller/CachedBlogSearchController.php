<?php
declare(strict_types=1);

namespace Werkl\OpenBlogware\Controller;

use Shopware\Core\Framework\Util\Json;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Shopware\Storefront\Controller\StorefrontController;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\Framework\Adapter\Cache\CacheValueCompressor;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Shopware\Core\Framework\Adapter\Cache\Event\AddCacheTagEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Cache\EntityCacheKeyGenerator;

/**
 * Handle Cache for BlogSearchController
 */
#[\Symfony\Component\Routing\Attribute\Route(defaults: ['_routeScope' => ['storefront']])]
class CachedBlogSearchController extends StorefrontController
{
    public const SEARCH_TAG = 'werkl-blog-search';

    public function __construct(
        private readonly BlogSearchController $decorated,
        private readonly CacheInterface $cache,
        private readonly EntityCacheKeyGenerator $generator,
        private readonly EventDispatcherInterface $eventDispatcher
    )
    {
    }

    public static function buildName(string $salesChannelId): string
    {
        return 'werkl-blog-search-' . $salesChannelId;
    }

    #[\Symfony\Component\Routing\Attribute\Route(path: '/werkl_blog_search', name: 'werkl.frontend.blog.search', methods: ['GET'])]
    public function search(Request $request, SalesChannelContext $context): Response
    {
        $key = $this->generateSearchKey($request, $context);

        $value = $this->cache->get($key, function (ItemInterface $item) use ($request, $context) {
            $response = $this->decorated->search($request, $context);

            $item->tag($this->generateSearchTags($context));

            return CacheValueCompressor::compress($response);
        });

        return CacheValueCompressor::uncompress($value);
    }

    /**
     * @throws RoutingException
     */
    #[\Symfony\Component\Routing\Attribute\Route(path: '/widgets/blog-search', name: 'widgets.blog.search.pagelet', methods: ['GET', 'POST'], defaults: ['XmlHttpRequest' => true])]
    public function ajax(Request $request, SalesChannelContext $context): Response
    {
        $key = $this->generateSearchKey($request, $context);

        $value = $this->cache->get($key, function (ItemInterface $item) use ($request, $context) {
            $name = self::buildName($context->getSalesChannelId());
            
            // Directly call the method without tracing
            $response = $this->decorated->ajax($request, $context);
            
            // Trigger an event to collect cache tags
            $this->eventDispatcher->dispatch(new AddCacheTagEvent($name));

            $item->tag($this->generateSearchTags($context));

            return CacheValueCompressor::compress($response);
        });

        return CacheValueCompressor::uncompress($value);
    }

    private function generateSearchKey(Request $request, SalesChannelContext $context): string
    {
        $parts = array_merge(
            $request->query->all(),
            $request->request->all(),
            [$this->generator->getSalesChannelContextHash($context)],
        );

        return self::buildName($context->getSalesChannelId()) . '-' . md5(Json::encode($parts));
    }

    /**
     * @return array<string>
     */
    private function generateSearchTags(SalesChannelContext $context): array
    {
        $tags = [self::buildName($context->getSalesChannelId()), self::SEARCH_TAG];

        $event = new AddCacheTagEvent(...$tags);
        $this->eventDispatcher->dispatch($event);
    
        return array_unique(array_filter($event->tags));
    }
}
