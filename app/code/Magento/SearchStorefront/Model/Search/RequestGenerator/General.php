<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\SearchStorefront\Model\Search\RequestGenerator;

use Magento\Framework\Search\Request\BucketInterface;
use Magento\Framework\Search\Request\FilterInterface;

/**
 * Catalog search request generator.
 */
class General implements GeneratorInterface
{
    /**
     * @inheritdoc
     */
    public function getFilterData($attribute, $filterName)
    {
        return [
            'type' => FilterInterface::TYPE_TERM,
            'name' => $filterName,
            'field' => $attribute->getAttributeCode(),
            'value' => '$' . $attribute->getAttributeCode() . '$',
        ];
    }

    /**
     * @inheritdoc
     */
    public function getAggregationData($attribute, $bucketName)
    {
        return [
            'type' => BucketInterface::TYPE_TERM,
            'name' => $bucketName,
            'field' => $attribute->getAttributeCode(),
            'metric' => [['type' => 'count']],
        ];
    }
}
