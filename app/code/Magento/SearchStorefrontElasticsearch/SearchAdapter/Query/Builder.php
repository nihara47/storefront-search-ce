<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\SearchStorefrontElasticsearch\SearchAdapter\Query;

use Magento\SearchStorefrontElasticsearch\Model\Config;
use Magento\SearchStorefrontElasticsearch\SearchAdapter\Query\Builder\Aggregation as AggregationBuilder;
use Magento\SearchStorefrontElasticsearch\SearchAdapter\Query\Builder\Sort;
use Magento\SearchStorefrontElasticsearch\SearchAdapter\SearchIndexNameResolver;
use Magento\Framework\App\ScopeResolverInterface;
use Magento\Framework\Search\RequestInterface;
use Magento\SearchStorefrontElasticsearch\Elasticsearch5\SearchAdapter\Query\Builder as Elasticsearch5Builder;

/**
 * Query builder for search adapter.
 *
 * Copy of Magento\Elasticsearch\SearchAdapter\Query\Builder
 */
class Builder extends Elasticsearch5Builder
{
    /**
     * @var Sort
     */
    private $sortBuilder;

    /**
     * @param Config $clientConfig
     * @param SearchIndexNameResolver $searchIndexNameResolver
     * @param AggregationBuilder $aggregationBuilder
     * @param ScopeResolverInterface $scopeResolver
     * @param Sort $sortBuilder
     */
    public function __construct(
        Config $clientConfig,
        SearchIndexNameResolver $searchIndexNameResolver,
        AggregationBuilder $aggregationBuilder,
        ScopeResolverInterface $scopeResolver,
        Sort $sortBuilder
    ) {
        parent::__construct($clientConfig, $searchIndexNameResolver, $aggregationBuilder, $scopeResolver);
        $this->sortBuilder = $sortBuilder;
    }

    /**
     * Set initial settings for query.
     *
     * @param RequestInterface $request
     * @return array
     * @since 100.1.0
     */
    public function initQuery(RequestInterface $request)
    {
        $dimension = current($request->getDimensions());
        $storeId = $this->scopeResolver->getScope($dimension->getValue())->getId();
        $searchQuery = [
            'index' => $this->searchIndexNameResolver->getIndexName($storeId, $request->getIndex()),
            'type' => $this->clientConfig->getEntityType(),
            'body' => [
                'from' => $request->getFrom(),
                'size' => $request->getSize(),
                'fields' => ['_id', '_score'],
                'sort' => $this->sortBuilder->getSort($request),
                'query' => [],
            ],
        ];
        return $searchQuery;
    }
}
