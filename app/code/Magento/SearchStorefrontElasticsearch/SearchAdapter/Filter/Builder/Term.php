<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\SearchStorefrontElasticsearch\SearchAdapter\Filter\Builder;

use Magento\Framework\Search\Request\Filter\Term as TermFilterRequest;
use Magento\Framework\Search\Request\FilterInterface as RequestFilterInterface;
use Magento\SearchStorefrontElasticsearch\Model\Adapter\FieldMapper\Product\AttributeProvider;
use Magento\SearchStorefrontElasticsearch\Model\Adapter\FieldMapper\Product\FieldProvider\FieldType\ConverterInterface
    as FieldTypeConverterInterface;
use Magento\SearchStorefrontElasticsearch\Model\Adapter\FieldMapperInterface;

/**
 * Term filter builder
 * Copy of Elasticsearch\SearchAdapter\Filter\Builder\Term
 */
class Term implements FilterInterface
{
    /**
     * @var FieldMapperInterface
     */
    private $fieldMapper;

    /**
     * @var AttributeProvider
     */
    private $attributeAdapterProvider;

    /**
     * @var array
     * @see \Magento\SearchStorefrontElasticsearch\Elasticsearch5\Model\Adapter\FieldMapper\Product\FieldProvider\FieldType\Resolver\IntegerType::$integerTypeAttributes
     */
    private $integerTypeAttributes = ['category_ids'];

    /**
     * @param FieldMapperInterface $fieldMapper
     * @param AttributeProvider    $attributeAdapterProvider
     * @param array                $integerTypeAttributes
     */
    public function __construct(
        FieldMapperInterface $fieldMapper,
        AttributeProvider $attributeAdapterProvider,
        array $integerTypeAttributes = []
    ) {
        $this->fieldMapper = $fieldMapper;
        $this->attributeAdapterProvider = $attributeAdapterProvider;
        $this->integerTypeAttributes = array_merge($this->integerTypeAttributes, $integerTypeAttributes);
    }

    /**
     * Build term filter request
     *
     * @param  RequestFilterInterface|TermFilterRequest $filter
     * @return array
     */
    public function buildFilter(RequestFilterInterface $filter)
    {
        $filterQuery = [];

        $attribute = $this->attributeAdapterProvider->getByAttributeCode($filter->getField());
        $fieldName = $this->fieldMapper->getFieldName($filter->getField());

        if ($attribute->isTextType() && !in_array($attribute->getAttributeCode(), $this->integerTypeAttributes)) {
            $suffix = FieldTypeConverterInterface::INTERNAL_DATA_TYPE_KEYWORD;
            $fieldName .= '.' . $suffix;
        }

        if ($filter->getValue() !== false) {
            $operator = is_array($filter->getValue()) ? 'terms' : 'term';
            $filterQuery []= [
                $operator => [
                    $fieldName => $filter->getValue(),
                ],
            ];
        }
        return $filterQuery;
    }
}
