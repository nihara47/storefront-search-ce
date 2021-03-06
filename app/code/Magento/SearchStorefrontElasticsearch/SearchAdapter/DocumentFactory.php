<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\SearchStorefrontElasticsearch\SearchAdapter;

use Magento\Framework\Api\AttributeInterface;
use Magento\Framework\Api\AttributeValue;
use Magento\Framework\Api\CustomAttributesDataInterface;
use Magento\Framework\Api\Search\Document;
use Magento\Framework\Api\Search\DocumentInterface;
use Magento\Framework\Search\EntityMetadata;

/**
 * Document Factory to create Search Document instance
 * Copy of Elasticsearch\SearchAdapter\DocumentFactory
 */
class DocumentFactory
{
    /**
     * @var EntityMetadata
     */
    private $entityMetadata;

    /**
     * @param EntityMetadata $entityMetadata
     */
    public function __construct(EntityMetadata $entityMetadata)
    {
        $this->entityMetadata = $entityMetadata;
    }

    /**
     * Create Search Document instance
     *
     * @param  array $rawDocument
     * @return Document
     */
    public function create($rawDocument)
    {
        $attributes = [];
        $documentId = null;
        $entityId = $this->entityMetadata->getEntityId();
        foreach ($rawDocument as $fieldName => $value) {
            if ($fieldName === $entityId) {
                $documentId = $value;
            } elseif ($fieldName === '_score') {
                $attributes['score'] = new AttributeValue(
                    [
                        AttributeInterface::ATTRIBUTE_CODE => $fieldName,
                        AttributeInterface::VALUE => $value,
                    ]
                );
            }
        }

        return new Document(
            [
                DocumentInterface::ID => $documentId,
                CustomAttributesDataInterface::CUSTOM_ATTRIBUTES => $attributes,
            ]
        );
    }
}
