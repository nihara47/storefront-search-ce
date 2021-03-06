<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\SearchStorefrontElasticsearch\Model\Adapter\FieldMapper\Product\FieldProvider\FieldName;

use Magento\SearchStorefrontElasticsearch\Model\Adapter\FieldMapper\Product\AttributeAdapter;

/**
 * Field name resolver for preparing field key for elasticsearch mapping by attribute.
 * Copy of Elasticsearch\Model\Adapter\FieldMapper\Product\FieldProvider\FieldName\ResolverInterface
 */
interface ResolverInterface
{
    /**
     * Get field name.
     *
     * @param  AttributeAdapter $attribute
     * @param  array            $context
     * @return string
     */
    public function getFieldName(AttributeAdapter $attribute, $context = []): ?string;
}
