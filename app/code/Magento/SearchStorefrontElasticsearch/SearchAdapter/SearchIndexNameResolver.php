<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\SearchStorefrontElasticsearch\SearchAdapter;

use Magento\SearchStorefrontElasticsearch\Model\Config;

/**
 * Alias name resolver
 * Copy of Elasticsearch\SearchAdapter\SearchIndexNameResolver
 * removed usage of Fulltext::INDEXER_ID of catalog search module
 */
class SearchIndexNameResolver
{
    const INDEXER_ID = 'catalogsearch_fulltext';

    /**
     * @var Config
     */
    private $clientConfig;

    /**
     * @param Config $clientConfig
     */
    public function __construct(
        Config $clientConfig
    ) {
        $this->clientConfig = $clientConfig;
    }

    /**
     * Returns the index (alias) name
     *
     * @param  int    $storeId
     * @param  string $indexerId
     * @return string
     */
    public function getIndexName($storeId, $indexerId)
    {
        $mappedIndexerId = $this->getIndexMapping($indexerId);
        return $this->clientConfig->getIndexPrefix() . '_' . $mappedIndexerId . '_' . $storeId;
    }

    /**
     * Get index name by indexer ID
     *
     * @param  string $indexerId
     * @return string
     */
    private function getIndexMapping($indexerId)
    {
        if ($indexerId == self::INDEXER_ID) {
            $mappedIndexerId = 'product';
        } else {
            $mappedIndexerId = $indexerId;
        }
        return $mappedIndexerId;
    }
}
