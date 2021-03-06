<?php
// Generated by the Magento PHP proto generator.  DO NOT EDIT!

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\SearchStorefrontApi\Api;

use \Magento\SearchStorefrontApi\Api\Data\ProductSearchRequestInterface;
use \Magento\SearchStorefrontApi\Api\Data\ProductsSearchResultInterface;
use \Magento\SearchStorefrontApi\Proto\ProductSearchRequest;
use \Magento\SearchStorefrontApi\Proto\ProductsSearchResult;
use \Magento\SearchStorefrontApi\Proto\SearchClient;

/**
 * Autogenerated description for SearchProxyServer class
 *
 * @SuppressWarnings(PHPMD)
 */
class SearchProxyServer implements \Magento\SearchStorefrontApi\Proto\SearchInterface
{
    /**
     * @var SearchServerInterface
     */
    private $service;

    /**
     * @param SearchServerInterface $service
     */
    public function __construct(
        SearchServerInterface $service
    ) {
        $this->service = $service;
    }

    /**
     * Autogenerated description for searchProducts method
     *
     * @param \Spiral\GRPC\ContextInterface $ctx
     * @param ProductSearchRequest          $in
     * @return ProductsSearchResult
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function searchProducts(\Spiral\GRPC\ContextInterface $ctx, ProductSearchRequest $in): ProductsSearchResult
    {
        try {
            $magentoDtoRequest = $this->searchProductsFromProto($in);
            $magentoDtoResponse = $this->service->searchProducts($magentoDtoRequest);
            return $this->searchProductsToProto($magentoDtoResponse);
        } catch (\Exception $e) {
            throw new \Spiral\GRPC\Exception\InvokeException(
                $e->getMessage(),
                \Spiral\GRPC\StatusCode::UNKNOWN,
                [],
                $e
            );
        }
    }

    /**
     * Autogenerated description for searchProducts method
     *
     * @param  ProductSearchRequest $value
     * @return ProductSearchRequestInterface
     */
    private function searchProductsFromProto(ProductSearchRequest $value): ProductSearchRequestInterface
    {
        // convert data from \Magento\SearchStorefrontApi\Proto\ProductSearchRequest
        // to \Magento\SearchStorefrontApi\Api\Data\ProductSearchRequest
        /**
 * @var \Magento\SearchStorefrontApi\Proto\ProductSearchRequest $value
**/
        $p = function () use ($value) {
            $r = new \Magento\SearchStorefrontApi\Api\Data\ProductSearchRequest();
            $r->setPhrase($value->getPhrase());
            $r->setStore($value->getStore());
            $r->setPageSize($value->getPageSize());
            $r->setCurrentPage($value->getCurrentPage());
            $res = [];
            foreach ($value->getFilters() as $item5) {
                // convert data from \Magento\SearchStorefrontApi\Proto\Filter
                // to \Magento\SearchStorefrontApi\Api\Data\Filter
                /**
 * @var \Magento\SearchStorefrontApi\Proto\Filter $item5
**/
                $p = function () use ($item5) {
                    $r = new \Magento\SearchStorefrontApi\Api\Data\Filter();
                    $r->setAttribute($item5->getAttribute());
                    $values = [];
                    foreach ($item5->getIn() as $newValue) {
                        $values[] = $newValue;
                    }
                    $r->setIn($values);
                    $r->setEq($item5->getEq());
                    $prop9 = $item5->getRange();
                    if ($prop9 !== null) {
                        // convert data from \Magento\SearchStorefrontApi\Proto\SearchRange
                        // to \Magento\SearchStorefrontApi\Api\Data\SearchRange
                        /**
 * @var \Magento\SearchStorefrontApi\Proto\SearchRange $prop9
**/
                        $p = function () use ($prop9) {
                            $r = new \Magento\SearchStorefrontApi\Api\Data\SearchRange();
                            $r->setFrom($prop9->getFrom());
                            $r->setTo($prop9->getTo());
                            return $r;
                        };
                        $out = $p();
                        $r->setRange($out);
                    }
                    return $r;
                };
                $out = $p();
                $res[] = $out;
            }
            $r->setFilters($res);
            $res = [];
            foreach ($value->getSort() as $item6) {
                // convert data from \Magento\SearchStorefrontApi\Proto\Sort
                // to \Magento\SearchStorefrontApi\Api\Data\Sort
                /**
 * @var \Magento\SearchStorefrontApi\Proto\Sort $item6
**/
                $p = function () use ($item6) {
                    $r = new \Magento\SearchStorefrontApi\Api\Data\Sort();
                    $r->setAttribute($item6->getAttribute());
                    $r->setDirection($item6->getDirection());
                    return $r;
                };
                $out = $p();
                $res[] = $out;
            }
            $r->setSort($res);
            $r->setIncludeAggregations($value->getIncludeAggregations());
            $r->setCustomerGroupId($value->getCustomerGroupId());
            return $r;
        };
        $out = $p();

        return $out;
    }

    /**
     * Autogenerated description for searchProducts method
     *
     * @param  ProductsSearchResultInterface $value
     * @return ProductsSearchResult
     * phpcs:disable Generic.Metrics.NestingLevel.TooHigh
     */
    private function searchProductsToProto(ProductsSearchResultInterface $value): ProductsSearchResult
    {
        // convert data from \Magento\SearchStorefrontApi\Api\Data\ProductsSearchResult
        // to \Magento\SearchStorefrontApi\Proto\ProductsSearchResult
        /**
 * @var \Magento\SearchStorefrontApi\Api\Data\ProductsSearchResult $value
**/
        $p = function () use ($value) {
            $r = new \Magento\SearchStorefrontApi\Proto\ProductsSearchResult();
            $r->setTotalCount($value->getTotalCount());
            $values = [];
            foreach ($value->getItems() as $newValue) {
                $values[] = $newValue;
            }
            $r->setItems($values);
            $res = [];
            foreach ($value->getFacets() as $item3) {
                // convert data from \Magento\SearchStorefrontApi\Api\Data\Bucket
                // to \Magento\SearchStorefrontApi\Proto\Bucket
                /**
 * @var \Magento\SearchStorefrontApi\Api\Data\Bucket $item3
**/
                $p = function () use ($item3) {
                    $r = new \Magento\SearchStorefrontApi\Proto\Bucket();
                    $r->setAttribute($item3->getAttribute());
                    $r->setLabel($item3->getLabel());
                    $r->setCount($item3->getCount());
                    $res = [];
                    foreach ($item3->getOptions() as $item7) {
                        // convert data from \Magento\SearchStorefrontApi\Api\Data\BucketOption
                        // to \Magento\SearchStorefrontApi\Proto\BucketOption
                        /**
 * @var \Magento\SearchStorefrontApi\Api\Data\BucketOption $item7
**/
                        $p = function () use ($item7) {
                            $r = new \Magento\SearchStorefrontApi\Proto\BucketOption();
                            $r->setValue($item7->getValue());
                            $r->setLabel($item7->getLabel());
                            $r->setCount($item7->getCount());
                            return $r;
                        };
                        $proto = $p();
                        $res[] = $proto;
                    }
                    $r->setOptions($res);
                    return $r;
                };
                $proto = $p();
                $res[] = $proto;
            }
            $r->setFacets($res);
            $prop4 = $value->getPageInfo();
            if ($prop4 !== null) {
                // convert data from \Magento\SearchStorefrontApi\Api\Data\SearchResultPageInfo
                // to \Magento\SearchStorefrontApi\Proto\SearchResultPageInfo
                /**
 * @var \Magento\SearchStorefrontApi\Api\Data\SearchResultPageInfo $prop4
**/
                $p = function () use ($prop4) {
                    $r = new \Magento\SearchStorefrontApi\Proto\SearchResultPageInfo();
                    $r->setCurrentPage($prop4->getCurrentPage());
                    $r->setPageSize($prop4->getPageSize());
                    $r->setTotalPages($prop4->getTotalPages());
                    return $r;
                };
                $proto = $p();
                $r->setPageInfo($proto);
            }
            return $r;
        };
        $proto = $p();

        return $proto;
    }
}
