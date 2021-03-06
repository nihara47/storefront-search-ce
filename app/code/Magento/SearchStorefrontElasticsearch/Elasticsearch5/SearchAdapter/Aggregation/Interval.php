<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\SearchStorefrontElasticsearch\Elasticsearch5\SearchAdapter\Aggregation;

use Magento\Framework\Search\Dynamic\IntervalInterface;
use Magento\SearchStorefrontElasticsearch\Model\Config;
use Magento\SearchStorefrontElasticsearch\SearchAdapter\ConnectionManager;
use Magento\SearchStorefrontElasticsearch\SearchAdapter\SearchIndexNameResolver;

/**
 * Aggregate price intervals for search query result.
 * Copy of Elasticsearch\Elasticsearch5\SearchAdapter\Aggregation\Interval
 * removed usage of Fulltext::INDEXER_ID constant
 */
class Interval implements IntervalInterface
{
    /**
     * Minimal possible value
     */
    const DELTA = 0.005;

    /**
     * @var ConnectionManager
     */
    private $connectionManager;

    /**
     * @var Config
     */
    private $clientConfig;

    /**
     * @var string
     */
    private $fieldName;

    /**
     * @var string
     */
    private $storeId;

    /**
     * @var array
     */
    private $entityIds;

    /**
     * @var SearchIndexNameResolver
     */
    private $searchIndexNameResolver;

    /**
     * @param ConnectionManager       $connectionManager
     * @param Config                  $clientConfig
     * @param SearchIndexNameResolver $searchIndexNameResolver
     * @param string                  $fieldName
     * @param string                  $storeId
     * @param array                   $entityIds
     */
    public function __construct(
        ConnectionManager $connectionManager,
        Config $clientConfig,
        SearchIndexNameResolver $searchIndexNameResolver,
        string $fieldName,
        string $storeId,
        array $entityIds
    ) {
        $this->connectionManager = $connectionManager;
        $this->clientConfig = $clientConfig;
        $this->fieldName = $fieldName;
        $this->storeId = $storeId;
        $this->entityIds = $entityIds;
        $this->searchIndexNameResolver = $searchIndexNameResolver;
    }

    /**
     * @inheritdoc
     */
    public function load($limit, $offset = null, $lower = null, $upper = null)
    {
        $from = $to = [];
        if ($lower) {
            $from = ['gte' => $lower - self::DELTA];
        }
        if ($upper) {
            $to = ['lt' => $upper - self::DELTA];
        }

        $requestQuery = $this->prepareBaseRequestQuery($from, $to);
        $requestQuery = array_merge_recursive(
            $requestQuery,
            ['body' => ['stored_fields' => [$this->fieldName], 'size' => $limit]]
        );

        if ($offset) {
            $requestQuery['body']['from'] = $offset;
        }

        $queryResult = $this->connectionManager->getConnection()
            ->query($requestQuery);

        return $this->arrayValuesToFloat($queryResult['hits']['hits'], $this->fieldName);
    }

    /**
     * @inheritdoc
     */
    public function loadPrevious($data, $index, $lower = null)
    {
        if ($lower) {
            $from = ['gte' => $lower - self::DELTA];
        }
        if ($data) {
            $to = ['lt' => $data - self::DELTA];
        }

        $requestQuery = $this->prepareBaseRequestQuery($from ?? [], $to ?? []);
        $requestQuery = array_merge_recursive(
            $requestQuery,
            ['size' => 0]
        );

        $queryResult = $this->connectionManager->getConnection()
            ->query($requestQuery);

        $offset = $queryResult['hits']['total'];
        if (!$offset) {
            return false;
        }

        if (is_array($offset)) {
            $offset = $offset['value'];
        }

        return $this->load($index - $offset + 1, $offset - 1, $lower);
    }

    /**
     * @inheritdoc
     */
    public function loadNext($data, $rightIndex, $upper = null)
    {
        $from = ['gt' => $data + self::DELTA];
        $to = ['lt' => $data - self::DELTA];

        $requestCountQuery = $this->prepareBaseRequestQuery($from, $to);
        $requestCountQuery = array_merge_recursive(
            $requestCountQuery,
            ['size' => 0]
        );

        $queryCountResult = $this->connectionManager->getConnection()
            ->query($requestCountQuery);

        $offset = $queryCountResult['hits']['total'];
        if (!$offset) {
            return false;
        }

        if (is_array($offset)) {
            $offset = $offset['value'];
        }

        $from = ['gte' => $data - self::DELTA];
        if ($upper !== null) {
            $to = ['lt' => $data - self::DELTA];
        }

        $requestQuery = $requestCountQuery;

        $requestCountQuery['body']['query']['bool']['filter']['bool']['must']['range'] =
            [$this->fieldName => array_merge($from, $to)];
        $requestCountQuery['body']['from'] = $offset - 1;
        $requestCountQuery['body']['size'] = $rightIndex - $offset + 1;
        $queryResult = $this->connectionManager->getConnection()
            ->query($requestQuery);

        return array_reverse($this->arrayValuesToFloat($queryResult['hits']['hits'], $this->fieldName));
    }

    /**
     * Conver array values to float type.
     *
     * @param array  $hits
     * @param string $fieldName
     *
     * @return float[]
     */
    private function arrayValuesToFloat(array $hits, string $fieldName): array
    {
        $returnPrices = [];
        foreach ($hits as $hit) {
            $returnPrices[] = (float)$hit['fields'][$fieldName][0];
        }

        return $returnPrices;
    }

    /**
     * Prepare base query for search.
     *
     * @param  array|null $from
     * @param  array|null $to
     * @return array
     * removed soft dependency on Fulltext::INDEXER_ID
     */
    private function prepareBaseRequestQuery($from = null, $to = null): array
    {
        $requestQuery = [
            'index' => $this->searchIndexNameResolver->getIndexName(
                $this->storeId,
                $this->searchIndexNameResolver::INDEXER_ID
            ),
            'type' => $this->clientConfig->getEntityType(),
            'body' => [
                'stored_fields' => [
                    '_id',
                ],
                'query' => [
                    'bool' => [
                        'must' => [
                            'match_all' => new \stdClass(),
                        ],
                        'filter' => [
                            'bool' => [
                                'must' => [
                                    [
                                        'terms' => [
                                            '_id' => $this->entityIds,
                                        ],
                                    ],
                                    [
                                        'range' => [
                                            $this->fieldName => array_merge($from, $to),
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
                'sort' => [
                    $this->fieldName,
                ],
            ],
        ];

        return $requestQuery;
    }
}
