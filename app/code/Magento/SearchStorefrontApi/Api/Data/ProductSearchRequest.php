<?php
// Generated by the Magento PHP proto generator.  DO NOT EDIT!

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\SearchStorefrontApi\Api\Data;

/**
 * Autogenerated description for ProductSearchRequest class
 *
 * phpcs:disable Magento2.PHP.FinalImplementation
 *
 * @SuppressWarnings(PHPMD)
 * @SuppressWarnings(PHPCPD)
 */
final class ProductSearchRequest implements ProductSearchRequestInterface
{

    /**
     * @var string
     */
    private $phrase;

    /**
     * @var string
     */
    private $store;

    /**
     * @var int
     */
    private $pageSize;

    /**
     * @var int
     */
    private $currentPage;

    /**
     * @var array
     */
    private $filters;

    /**
     * @var array
     */
    private $sort;

    /**
     * @var bool
     */
    private $includeAggregations;

    /**
     * @var int
     */
    private $customerGroupId;
    
    /**
     * @inheritdoc
     *
     * @return string
     */
    public function getPhrase(): string
    {
        return (string) $this->phrase;
    }
    
    /**
     * @inheritdoc
     *
     * @param  string $value
     * @return void
     */
    public function setPhrase(string $value): void
    {
        $this->phrase = $value;
    }
    
    /**
     * @inheritdoc
     *
     * @return string
     */
    public function getStore(): string
    {
        return (string) $this->store;
    }
    
    /**
     * @inheritdoc
     *
     * @param  string $value
     * @return void
     */
    public function setStore(string $value): void
    {
        $this->store = $value;
    }
    
    /**
     * @inheritdoc
     *
     * @return int
     */
    public function getPageSize(): int
    {
        return (int) $this->pageSize;
    }
    
    /**
     * @inheritdoc
     *
     * @param  int $value
     * @return void
     */
    public function setPageSize(int $value): void
    {
        $this->pageSize = $value;
    }
    
    /**
     * @inheritdoc
     *
     * @return int
     */
    public function getCurrentPage(): int
    {
        return (int) $this->currentPage;
    }
    
    /**
     * @inheritdoc
     *
     * @param  int $value
     * @return void
     */
    public function setCurrentPage(int $value): void
    {
        $this->currentPage = $value;
    }
    
    /**
     * @inheritdoc
     *
     * @return \Magento\SearchStorefrontApi\Api\Data\FilterInterface[]
     */
    public function getFilters(): array
    {
        return (array) $this->filters;
    }
    
    /**
     * @inheritdoc
     *
     * @param  \Magento\SearchStorefrontApi\Api\Data\FilterInterface[] $value
     * @return void
     */
    public function setFilters(array $value): void
    {
        $this->filters = $value;
    }
    
    /**
     * @inheritdoc
     *
     * @return \Magento\SearchStorefrontApi\Api\Data\SortInterface[]
     */
    public function getSort(): array
    {
        return (array) $this->sort;
    }
    
    /**
     * @inheritdoc
     *
     * @param  \Magento\SearchStorefrontApi\Api\Data\SortInterface[] $value
     * @return void
     */
    public function setSort(array $value): void
    {
        $this->sort = $value;
    }
    
    /**
     * @inheritdoc
     *
     * @return bool
     */
    public function getIncludeAggregations(): bool
    {
        return (bool) $this->includeAggregations;
    }
    
    /**
     * @inheritdoc
     *
     * @param  bool $value
     * @return void
     */
    public function setIncludeAggregations(bool $value): void
    {
        $this->includeAggregations = $value;
    }
    
    /**
     * @inheritdoc
     *
     * @return int
     */
    public function getCustomerGroupId(): int
    {
        return (int) $this->customerGroupId;
    }
    
    /**
     * @inheritdoc
     *
     * @param  int $value
     * @return void
     */
    public function setCustomerGroupId(int $value): void
    {
        $this->customerGroupId = $value;
    }
}
