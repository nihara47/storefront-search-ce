<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\SearchStorefrontElasticsearch\SearchAdapter\Query\Builder;

use Magento\Framework\Search\Request\QueryInterface as RequestQueryInterface;

/**
 * Copy of Magento\SearchStorefrontElasticsearch\SearchAdapter\Query\Builder\QueryInterface
 */
interface QueryInterface
{
    /**
     * Build search query
     *
     * @param  array                 $selectQuery
     * @param  RequestQueryInterface $requestQuery
     * @param  string                $conditionType
     * @return array
     */
    public function build(
        array $selectQuery,
        RequestQueryInterface $requestQuery,
        $conditionType
    );
}
