<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\SearchStorefrontElasticsearch\Model\Adapter\FieldMapper\Product\FieldProvider\FieldIndex;

use Magento\SearchStorefrontElasticsearch\Model\Adapter\FieldMapper\Product\AttributeAdapter;

/**
 * Field index type resolver interface.
 * Copy of Magento\Elasticsearch\Model\Adapter\FieldMapper\Product\FieldProvider\FieldIndex\ResolverInterface
 */
interface ResolverInterface
{
    /**
     * Get field index.
     *
     * @param AttributeAdapter $attribute
     * @return string|boolean
     */
    public function getFieldIndex(AttributeAdapter $attribute);
}
