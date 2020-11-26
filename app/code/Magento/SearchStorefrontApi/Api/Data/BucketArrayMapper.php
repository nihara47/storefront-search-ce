<?php
// Generated by the Magento PHP proto generator.  DO NOT EDIT!

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\SearchStorefrontApi\Api\Data;

use Magento\Framework\ObjectManagerInterface;

/**
 * Autogenerated description for Bucket class
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
 * @SuppressWarnings(PHPMD.UnusedPrivateField)
 * @SuppressWarnings(PHPMD.NPathComplexity)
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 */
final class BucketArrayMapper
{
    /**
     * @var mixed
     */
    private $data;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    public function __construct(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Convert the DTO to the array with the data
     *
     * @param  Bucket $dto
     * @return array
     */
    public function convertToArray(Bucket $dto)
    {
        $result = [];
        $result["attribute"] = $dto->getAttribute();
        $result["label"] = $dto->getLabel();
        $result["count"] = $dto->getCount();
        /**
 * Convert complex Array field 
**/
        $fieldArray = [];
        foreach ($dto->getOptions() as $fieldArrayDto) {
            $fieldArray[] = $this->objectManager->get(\Magento\SearchStorefrontApi\Api\Data\BucketOptionArrayMapper::class)
                ->convertToArray($fieldArrayDto);
        }
        $result["options"] = $fieldArray;
        return $result;
    }
}
