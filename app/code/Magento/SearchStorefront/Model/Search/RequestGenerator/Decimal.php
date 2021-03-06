<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\SearchStorefront\Model\Search\RequestGenerator;

use Magento\Framework\Search\Request\BucketInterface;
use Magento\Framework\Search\Request\FilterInterface;

/**
 * Catalog search range request generator.
 */
class Decimal implements GeneratorInterface
{
    /**
     * @inheritdoc
     */
    public function getFilterData($attribute, $filterName)
    {
        return [
            'type' => FilterInterface::TYPE_RANGE,
            'name' => $filterName,
            'field' => $attribute->getAttributeCode(),
            'from' => '$' . $attribute->getAttributeCode() . '.from$',
            'to' => '$' . $attribute->getAttributeCode() . '.to$',
        ];
    }

    /**
     * @inheritdoc
     */
    public function getAggregationData($attribute, $bucketName)
    {
        return [
            'type' => BucketInterface::TYPE_DYNAMIC,
            'name' => $bucketName,
            'field' => $attribute->getAttributeCode(),
            'method' => 'manual',
            'metric' => [['type' => 'count']],
        ];
    }
}
