<?php
// Generated by the Magento PHP proto generator.  DO NOT EDIT!

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\SearchStorefrontApi\Api\Data;

/**
 * Autogenerated description for BucketOption class
 *
 * phpcs:disable Magento2.PHP.FinalImplementation
 *
 * @SuppressWarnings(PHPMD)
 * @SuppressWarnings(PHPCPD)
 */
final class BucketOption implements BucketOptionInterface
{

    /**
     * @var string
     */
    private $value;

    /**
     * @var string
     */
    private $label;

    /**
     * @var int
     */
    private $count;
    
    /**
     * @inheritdoc
     *
     * @return string
     */
    public function getValue(): string
    {
        return (string) $this->value;
    }
    
    /**
     * @inheritdoc
     *
     * @param  string $value
     * @return void
     */
    public function setValue(string $value): void
    {
        $this->value = $value;
    }
    
    /**
     * @inheritdoc
     *
     * @return string
     */
    public function getLabel(): string
    {
        return (string) $this->label;
    }
    
    /**
     * @inheritdoc
     *
     * @param  string $value
     * @return void
     */
    public function setLabel(string $value): void
    {
        $this->label = $value;
    }
    
    /**
     * @inheritdoc
     *
     * @return int
     */
    public function getCount(): int
    {
        return (int) $this->count;
    }
    
    /**
     * @inheritdoc
     *
     * @param  int $value
     * @return void
     */
    public function setCount(int $value): void
    {
        $this->count = $value;
    }
}
