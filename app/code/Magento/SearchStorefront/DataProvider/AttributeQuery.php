<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\SearchStorefront\DataProvider;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\EntityManager\EntityMetadataInterface;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\SearchStorefrontStore\Model\Store;

/**
 * Copied from Magento\CatalogGraphQl
 *
 * Generic for build Select object to fetch eav attributes for provided entity type
 * @SuppressWarnings(PHPCPD)
 */
class AttributeQuery
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @var string
     */
    private $entityType;

    /**
     * List of attributes that need to be added/removed to fetch
     *
     * @var array
     */
    private $linkedAttributes;

    /**
     * @var array
     */
    private const SUPPORTED_BACKEND_TYPES = [
        'int',
        'decimal',
        'text',
        'varchar',
        'datetime',
    ];

    /**
     * @param string             $entityType
     * @param ResourceConnection $resourceConnection
     * @param MetadataPool       $metadataPool
     * @param array              $linkedAttributes
     */
    public function __construct(
        string $entityType,
        ResourceConnection $resourceConnection,
        MetadataPool $metadataPool,
        array $linkedAttributes = []
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->metadataPool = $metadataPool;
        $this->entityType = $entityType;
        $this->linkedAttributes = $linkedAttributes;
    }

    /**
     * Form and return query to get eav entity $attributes for given $entityIds.
     *
     * If eav entities were not found, then data is fetching from $entityTableName.
     *
     * @param  array $entityIds
     * @param  array $attributes
     * @param  int   $storeId
     * @return Select
     * @throws \Zend_Db_Select_Exception
     * @throws \Exception
     */
    public function getQuery(array $entityIds, array $attributes, int $storeId): Select
    {
        /**
         * @var EntityMetadataInterface $metadata
         */
        $metadata = $this->metadataPool->getMetadata($this->entityType);
        $entityTableName = $metadata->getEntityTable();

        $connection = $this->resourceConnection->getConnection();
        $entityTableAttributes = \array_keys($connection->describeTable($entityTableName));

        $attributeMetadataTable = $this->resourceConnection->getTableName('eav_attribute');
        $eavAttributes = $this->getEavAttributeCodes($attributes, $entityTableAttributes);
        $entityTableAttributes = \array_intersect($attributes, $entityTableAttributes);

        $eavAttributesMetaData = $this->getAttributesMetaData($connection, $attributeMetadataTable, $eavAttributes);

        if ($eavAttributesMetaData) {
            $select = $this->getEavAttributes(
                $connection,
                $metadata,
                $entityTableAttributes,
                $entityIds,
                $eavAttributesMetaData,
                $entityTableName,
                $storeId
            );
        } else {
            $select = $this->getAttributesFromEntityTable(
                $connection,
                $entityTableAttributes,
                $entityIds,
                $entityTableName
            );
        }

        return $select;
    }

    /**
     * Form and return query to get entity $entityTableAttributes for given $entityIds
     *
     * @param  AdapterInterface $connection
     * @param  array            $entityTableAttributes
     * @param  array            $entityIds
     * @param  string           $entityTableName
     * @return Select
     */
    private function getAttributesFromEntityTable(
        AdapterInterface $connection,
        array $entityTableAttributes,
        array $entityIds,
        string $entityTableName
    ): Select {
        return $connection->select()
            ->from(['e' => $entityTableName], $entityTableAttributes)
            ->where('e.entity_id IN (?)', $entityIds);
    }

    /**
     * Return ids of eav attributes by $eavAttributeCodes.
     *
     * @param  AdapterInterface $connection
     * @param  string           $attributeMetadataTable
     * @param  array            $eavAttributeCodes
     * @return array
     */
    private function getAttributesMetaData(
        AdapterInterface $connection,
        string $attributeMetadataTable,
        array $eavAttributeCodes
    ): array {
        $eavAttributeIdsSelect = $connection->select()
            ->from(['a' => $attributeMetadataTable], ['attribute_id', 'backend_type', 'attribute_code'])
            ->where('a.attribute_code IN (?)', $eavAttributeCodes)
            ->where('a.entity_type_id = ?', 3);

        return $connection->fetchAssoc($eavAttributeIdsSelect);
    }

    /**
     * Form and return query to get eav entity $attributes for given $entityIds.
     *
     * @param  AdapterInterface        $connection
     * @param  EntityMetadataInterface $metadata
     * @param  array                   $entityTableAttributes
     * @param  array                   $entityIds
     * @param  array                   $eavAttributesMetaData
     * @param  string                  $entityTableName
     * @param  int                     $storeId
     * @return Select
     * @throws \Zend_Db_Select_Exception
     */
    private function getEavAttributes(
        AdapterInterface $connection,
        EntityMetadataInterface $metadata,
        array $entityTableAttributes,
        array $entityIds,
        array $eavAttributesMetaData,
        string $entityTableName,
        int $storeId
    ): Select {
        $selects = [];
        $attributeValueExpression = $connection->getCheckSql(
            $connection->getIfNullSql('store_eav.value_id', -1) . ' > 0',
            'store_eav.value',
            'eav.value'
        );
        $linkField = $metadata->getLinkField();
        $attributesPerTable = $this->getAttributeCodeTables($entityTableName, $eavAttributesMetaData);
        foreach ($attributesPerTable as $attributeTable => $eavAttributes) {
            $attributeCodeExpression = $this->buildAttributeCodeExpression($eavAttributes);

            $selects[] = $connection->select()
                ->from(['e' => $entityTableName], $entityTableAttributes)
                ->joinLeft(
                    ['eav' => $this->resourceConnection->getTableName($attributeTable)],
                    \sprintf('e.%1$s = eav.%1$s', $linkField) .
                    $connection->quoteInto(' AND eav.attribute_id IN (?)', \array_keys($eavAttributesMetaData)) .
                    $connection->quoteInto(' AND eav.store_id = ?', Store::DEFAULT_STORE_ID),
                    []
                )
                ->joinLeft(
                    ['store_eav' => $this->resourceConnection->getTableName($attributeTable)],
                    \sprintf(
                        'e.%1$s = store_eav.%1$s AND store_eav.attribute_id = ' .
                        'eav.attribute_id and store_eav.store_id = %2$d',
                        $linkField,
                        $storeId
                    ),
                    []
                )
                ->where('e.entity_id IN (?)', $entityIds)
                ->columns(
                    [
                        'attribute_code' => $attributeCodeExpression,
                        'value' => $attributeValueExpression
                    ]
                );
        }

        return $connection->select()->union($selects, Select::SQL_UNION_ALL);
    }

    /**
     * Build expression for attribute code field.
     *
     * An example:
     *
     * ```
     * CASE
     * WHEN eav.attribute_id = '73' THEN 'name'
     * WHEN eav.attribute_id = '121' THEN 'url_key'
     * END
     * ```
     *
     * @param  array $eavAttributes
     * @return \Zend_Db_Expr
     */
    private function buildAttributeCodeExpression(array $eavAttributes): \Zend_Db_Expr
    {
        $dbConnection = $this->resourceConnection->getConnection();
        $expressionParts = ['CASE'];

        foreach ($eavAttributes as $attribute) {
            $expressionParts[]=
                $dbConnection->quoteInto('WHEN eav.attribute_id = ?', $attribute['attribute_id'], \Zend_Db::INT_TYPE) .
                $dbConnection->quoteInto(' THEN ?', $attribute['attribute_code'], 'string');
        }

        $expressionParts[]= 'END';

        return new \Zend_Db_Expr(implode(' ', $expressionParts));
    }

    /**
     * Get list of attribute tables.
     *
     * Returns result in the following format:     *
     * ```
     * $attributeAttributeCodeTables = [
     *      'm2_catalog_product_entity_varchar' =>
     *          '45' => [
     *              'attribute_id' => 45,
     *              'backend_type' => 'varchar',
     *              'name' => attribute_code,
     *          ]
     *      ]
     * ];
     * ```
     *
     * @param  string $entityTable
     * @param  array  $eavAttributesMetaData
     * @return array
     */
    private function getAttributeCodeTables($entityTable, $eavAttributesMetaData): array
    {
        $attributeAttributeCodeTables = [];
        $metaTypes = \array_unique(\array_column($eavAttributesMetaData, 'backend_type'));

        foreach ($metaTypes as $type) {
            if (\in_array($type, self::SUPPORTED_BACKEND_TYPES, true)) {
                $tableName = \sprintf('%s_%s', $entityTable, $type);
                $attributeAttributeCodeTables[$tableName] = array_filter(
                    $eavAttributesMetaData,
                    function ($attribute) use ($type) {
                        return $attribute['backend_type'] === $type;
                    }
                );
            }
        }

        return $attributeAttributeCodeTables;
    }

    /**
     * Get EAV attribute codes
     * Remove attributes from entity table and attributes from exclude list
     * Add linked attributes to output
     *
     * @param  array $attributes
     * @param  array $entityTableAttributes
     * @return array
     */
    private function getEavAttributeCodes($attributes, $entityTableAttributes): array
    {
        $attributes = \array_diff($attributes, $entityTableAttributes);
        $unusedAttributeList = [];
        $newAttributes = [];
        foreach ($this->linkedAttributes as $attribute => $linkedAttributes) {
            if (null === $linkedAttributes) {
                $unusedAttributeList[] = $attribute;
            } elseif (\is_array($linkedAttributes) && \in_array($attribute, $attributes, true)) {
                $newAttributes[] = $linkedAttributes;
            }
        }
        $attributes = \array_diff($attributes, $unusedAttributeList);

        return \array_unique(\array_merge($attributes, ...$newAttributes));
    }
}
