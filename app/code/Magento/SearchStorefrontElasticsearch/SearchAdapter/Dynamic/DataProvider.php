<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\SearchStorefrontElasticsearch\SearchAdapter\Dynamic;

use Magento\SearchStorefrontElasticsearch\SearchAdapter\QueryAwareInterface;
use Magento\SearchStorefrontElasticsearch\SearchAdapter\QueryContainer;
use Magento\SearchStorefrontStore\Model\Scope\ScopeResolver;

/**
 * Elastic search data provider
 */
class DataProvider implements \Magento\Framework\Search\Dynamic\DataProviderInterface, QueryAwareInterface
{
    /**
     * @var \Magento\SearchStorefrontElasticsearch\SearchAdapter\ConnectionManager
     */
    protected $connectionManager;

    /**
     * @var \Magento\SearchStorefrontElasticsearch\Model\Adapter\FieldMapperInterface
     */
    protected $fieldMapper;

    /**
     * @var \Magento\Framework\Search\Dynamic\IntervalFactory
     */
    protected $intervalFactory;

    /**
     * @var \Magento\SearchStorefrontElasticsearch\Model\Config
     * and should only modify existing query
     */
    protected $clientConfig;

    /**
     * @var \Magento\SearchStorefrontElasticsearch\SearchAdapter\SearchIndexNameResolver
     * and should only modify existing query
     */
    protected $searchIndexNameResolver;

    /**
     * @var string
     * and should only modify existing query
     */
    protected $indexerId;

    /**
     * @var \Magento\Framework\App\ScopeResolverInterface
     */
    protected $scopeResolver;

    /**
     * @var QueryContainer
     */
    private $queryContainer;

    /**
     * @var \Magento\SearchStorefront\Model\Filter\Price\Range
     */
    private $range;

    /**
     * @param \Magento\SearchStorefrontElasticsearch\SearchAdapter\ConnectionManager       $connectionManager
     * @param \Magento\SearchStorefrontElasticsearch\Model\Adapter\FieldMapperInterface    $fieldMapper
     * @param \Magento\SearchStorefront\Model\Filter\Price\Range                           $range
     * @param \Magento\Framework\Search\Dynamic\IntervalFactory                            $intervalFactory
     * @param \Magento\SearchStorefrontElasticsearch\Model\Config                          $clientConfig
     * @param \Magento\SearchStorefrontElasticsearch\SearchAdapter\SearchIndexNameResolver $searchIndexNameResolver
     * @param string                                                                       $indexerId
     * @param ScopeResolver                                                                $scopeResolver
     * @param QueryContainer|null                                                          $queryContainer
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\SearchStorefrontElasticsearch\SearchAdapter\ConnectionManager $connectionManager,
        \Magento\SearchStorefrontElasticsearch\Model\Adapter\FieldMapperInterface $fieldMapper,
        \Magento\SearchStorefront\Model\Filter\Price\Range $range,
        \Magento\Framework\Search\Dynamic\IntervalFactory $intervalFactory,
        \Magento\SearchStorefrontElasticsearch\Model\Config $clientConfig,
        \Magento\SearchStorefrontElasticsearch\SearchAdapter\SearchIndexNameResolver $searchIndexNameResolver,
        $indexerId,
        ScopeResolver $scopeResolver,
        QueryContainer $queryContainer = null
    ) {
        $this->connectionManager = $connectionManager;
        $this->fieldMapper = $fieldMapper;
        $this->intervalFactory = $intervalFactory;
        $this->clientConfig = $clientConfig;
        $this->searchIndexNameResolver = $searchIndexNameResolver;
        $this->indexerId = $indexerId;
        $this->scopeResolver = $scopeResolver;
        $this->queryContainer = $queryContainer;
        $this->range = $range;
    }

    /**
     * @inheritdoc
     */
    public function getRange()
    {
        return $this->range->getPriceRange();
    }

    /**
     * @inheritdoc
     */
    public function getAggregations(\Magento\Framework\Search\Dynamic\EntityStorage $entityStorage)
    {
        $aggregations = [
            'count' => 0,
            'max' => 0,
            'min' => 0,
            'std' => 0,
        ];

        $query = $this->getBasicSearchQuery($entityStorage);

        $fieldName = $this->fieldMapper->getFieldName('price');
        $query['body']['aggregations'] = [
            'prices' => [
                'extended_stats' => [
                    'field' => $fieldName,
                ],
            ],
        ];

        $queryResult = $this->connectionManager->getConnection()
            ->query($query);

        if (isset($queryResult['aggregations']['prices'])) {
            $aggregations = [
                'count' => $queryResult['aggregations']['prices']['count'],
                'max' => $queryResult['aggregations']['prices']['max'],
                'min' => $queryResult['aggregations']['prices']['min'],
                'std' => $queryResult['aggregations']['prices']['std_deviation'],
            ];
        }

        return $aggregations;
    }

    /**
     * @inheritdoc
     */
    public function getInterval(
        \Magento\Framework\Search\Request\BucketInterface $bucket,
        array $dimensions,
        \Magento\Framework\Search\Dynamic\EntityStorage $entityStorage
    ) {
        $entityIds = $entityStorage->getSource();
        $fieldName = $this->fieldMapper->getFieldName('price');
        $dimension = current($dimensions);

        $storeId = $this->scopeResolver->getScope($dimension->getValue())->getId();

        return $this->intervalFactory->create(
            [
                'entityIds' => $entityIds,
                'storeId' => $storeId,
                'fieldName' => $fieldName,
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function getAggregation(
        \Magento\Framework\Search\Request\BucketInterface $bucket,
        array $dimensions,
        $range,
        \Magento\Framework\Search\Dynamic\EntityStorage $entityStorage
    ) {
        $result = [];

        $query = $this->getBasicSearchQuery($entityStorage);

        $fieldName = $this->fieldMapper->getFieldName($bucket->getField());
        $query['body']['aggregations'] = [
            'prices' => [
                'histogram' => [
                    'field' => $fieldName,
                    'interval' => (float)$range,
                    'min_doc_count' => 1,
                ],
            ],
        ];

        $queryResult = $this->connectionManager->getConnection()
            ->query($query);
        foreach ($queryResult['aggregations']['prices']['buckets'] as $bucket) {
            $key = (int)($bucket['key'] / $range + 1);
            $result[$key] = $bucket['doc_count'];
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function prepareData($range, array $dbRanges)
    {
        $data = [];
        if (!empty($dbRanges)) {
            foreach ($dbRanges as $index => $count) {
                $fromPrice = $index == 1 ? 0 : ($index - 1) * $range;
                $toPrice = $index * $range;
                $data[] = [
                    'from' => $fromPrice,
                    'to' => $toPrice,
                    'count' => $count,
                ];
            }
        }

        return $data;
    }

    /**
     * Returns a basic search query which can be used for aggregations calculation
     *
     * The query may be requested from a query container if it has been set
     * or may be build by entity storage and dimensions.
     *
     * Building a query by entity storage is actually deprecated as the query
     * built in this way may cause ElasticSearch's TooManyClauses exception.
     *
     * The code which is responsible for building query in-place should be removed someday,
     * but for now it's a question of backward compatibility as this class may be used somewhere else
     * by extension developers and we can't guarantee that they'll pass a query into constructor.
     *
     * @param  \Magento\Framework\Search\Dynamic\EntityStorage $entityStorage
     * @param  array                                           $dimensions
     * @return array
     */
    private function getBasicSearchQuery(
        \Magento\Framework\Search\Dynamic\EntityStorage $entityStorage,
        array $dimensions = []
    ) {
        if (null !== $this->queryContainer) {
            return $this->queryContainer->getQuery();
        }

        $entityIds = $entityStorage->getSource();

        $dimension = current($dimensions);

        $scopeId = $dimension ? $dimension->getValue() : null;
        $storeId = $this->scopeResolver->getScope($scopeId)->getId();

        $query = [
            'index' => $this->searchIndexNameResolver->getIndexName($storeId, $this->indexerId),
            'type' => $this->clientConfig->getEntityType(),
            'body' => [
                'fields' => [
                    '_id',
                    '_score',
                ],
                'query' => [
                    'bool' => [
                        'must' => [
                            [
                                'terms' => [
                                    '_id' => $entityIds,
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        return $query;
    }
}
