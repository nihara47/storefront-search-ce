<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\SearchStorefrontElasticsearch\Elasticsearch5\Model\Adapter\FieldMapper\Product\FieldProvider\FieldType\Resolver;

use Magento\SearchStorefrontElasticsearch\Model\Adapter\FieldMapper\Product\AttributeAdapter;
use Magento\SearchStorefrontElasticsearch\Model\Adapter\FieldMapper\Product\FieldProvider\FieldType\ConverterInterface;
use Magento\SearchStorefrontElasticsearch\Model\Adapter\FieldMapper\Product\FieldProvider\FieldType\ResolverInterface;

/**
 * Keyword type resolver.
 * Copy of Magento\Elasticsearch\Elasticsearch5\Model\Adapter\FieldMapper\Product\FieldProvider\FieldType\Resolver\KeywordType
 */
class KeywordType implements ResolverInterface
{
    /**
     * @var ConverterInterface
     */
    private $fieldTypeConverter;

    /**
     * @param ConverterInterface $fieldTypeConverter
     */
    public function __construct(ConverterInterface $fieldTypeConverter)
    {
        $this->fieldTypeConverter = $fieldTypeConverter;
    }

    /**
     * Get field type.
     *
     * @param  AttributeAdapter $attribute
     * @return string
     */
    public function getFieldType(AttributeAdapter $attribute): ?string
    {
        if (($attribute->isComplexType()
            || (!$attribute->isSearchable() && !$attribute->isAlwaysIndexable() && $attribute->isFilterable()))
            && !$attribute->isBooleanType()
        ) {
            return $this->fieldTypeConverter->convert(ConverterInterface::INTERNAL_DATA_TYPE_KEYWORD);
        }

        return null;
    }
}
