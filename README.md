#PA-API5 implementation by extending Amazon SDK

## Installation

Run the following command using composer
```sh
$ composer require composer require behera116/paapi5-hermes
```

## Sample Execution

```php
<?php
require_once "vendor/autoload.php";// give absolute path for vendor if not included in include_path
use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\ProductLookup;

$params = [
    'accessKey' =>  PAAPI_ACCESS_KEY, //mandatory field
    'secretKey' => PAAPI_SECRET_KEY, //mandatory field
    'partnerTag' => PAAPI_PARTNER_TAG, //mandatory field
    'region' => 'eu-west-1', //default: eu-east-1
    'host' => 'webservices.amazon.in' // default: webservices.amazon.in
];

$instance = new ProductLookup($params);

//get product information using ASIN list
$asins = ['B07HGGYWL6'];
$items = $instance->getItems($asins);
print_r($items);

//Get Breadcrumb of a category using list of Category IDs
$nodes = ['1805560031','4363894031'];
$browseNodes = $instance->browseNodes($nodes);
print_r($browseNodes);

//Search for Product using keyword (default searchIndex:Electronics, default itemCount:3)
$params = [
    'keyword' => 'Samsung Galaxy M10',
    'searchIndex' => 'Electronics',
    'itemCount' => 1
];
$items = $instance->searchItems($params);
print_r($items);

?>
```


# Product Advertising API SDK for PHP (v1)

This repository contains the open source PHP SDK that allows you to access the [Product Advertising API](https://webservices.amazon.com/paapi5/documentation/index.html) from your PHP app.

## Installation

The Product Advertising API PHP SDK can be installed with [Composer](https://getcomposer.org/). Run this command:

```sh
$ composer require amazon/paapi5-php-sdk
```
## Usage

> **Note:** This version of the Product Advertising API SDK for PHP requires PHP 5.5 or greater.

Simple example for searching items.

```php
<?php

/**
 * Copyright 2019 Amazon.com, Inc. or its affiliates. All Rights Reserved.
 *
 * Licensed under the Apache License, Version 2.0 (the "License").
 * You may not use this file except in compliance with the License.
 * A copy of the License is located at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * or in the "license" file accompanying this file. This file is distributed
 * on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either
 * express or implied. See the License for the specific language governing
 * permissions and limitations under the License.
 */

/*
 * ProductAdvertisingAPI
 *
 * https://webservices.amazon.com/paapi5/documentation/index.html
 */

/*
 * This sample code snippet is for ProductAdvertisingAPI 5.0's SearchItems API
 *
 * For more details, refer: https://webservices.amazon.com/paapi5/documentation/search-items.html
 */

use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\api\DefaultApi;
use Amazon\ProductAdvertisingAPI\v1\ApiException;
use Amazon\ProductAdvertisingAPI\v1\Configuration;
use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\SearchItemsRequest;
use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\SearchItemsResource;
use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\PartnerType;
use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\ProductAdvertisingAPIClientException;

require_once(__DIR__ . '/vendor/autoload.php'); // change path as needed


$config = new Configuration();

/*
 * Add your credentials
 * Please add your access key here
 */
$config->setAccessKey('<YOUR ACCESS KEY>');
# Please add your secret key here
$config->setSecretKey('<YOUR SECRET KEY>');

# Please add your partner tag (store/tracking id) here
$partnerTag = '<YOUR PARTNER TAG>';

/*
 * PAAPI host and region to which you want to send request
 * For more details refer: https://webservices.amazon.com/paapi5/documentation/common-request-parameters.html#host-and-region
 */
$config->setHost('webservices.amazon.com');
$config->setRegion('us-east-1');

$apiInstance = new DefaultApi(
/*
 * If you want use custom http client, pass your client which implements `GuzzleHttp\ClientInterface`.
 * This is optional, `GuzzleHttp\Client` will be used as default.
 */
    new GuzzleHttp\Client(), $config);

# Request initialization

# Specify keywords
$keyword = 'Harry Potter';

/*
 * Specify the category in which search request is to be made
 * For more details, refer: https://webservices.amazon.com/paapi5/documentation/use-cases/organization-of-items-on-amazon/search-index.html
 */
$searchIndex = "All";

# Specify item count to be returned in search result
$itemCount = 1;

/*
 * Choose resources you want from SearchItemsResource enum
 * For more details, refer: https://webservices.amazon.com/paapi5/documentation/search-items.html#resources-parameter
 */
$resources = array(
    SearchItemsResource::ITEM_INFOTITLE,
    SearchItemsResource::OFFERSLISTINGSPRICE);

# Forming the request
$searchItemsRequest = new SearchItemsRequest();
$searchItemsRequest->setSearchIndex($searchIndex);
$searchItemsRequest->setKeywords($keyword);
$searchItemsRequest->setItemCount($itemCount);
$searchItemsRequest->setPartnerTag($partnerTag);
$searchItemsRequest->setPartnerType(PartnerType::ASSOCIATES);
$searchItemsRequest->setResources($resources);

# Validating request
$invalidPropertyList = $searchItemsRequest->listInvalidProperties();
$length = count($invalidPropertyList);
if ($length > 0) {
    echo "Error forming the request", PHP_EOL;
    foreach ($invalidPropertyList as $invalidProperty) {
        echo $invalidProperty, PHP_EOL;
    }
    return;
}

# Sending the request
try {
    $searchItemsResponse = $apiInstance->searchItems($searchItemsRequest);

    echo 'API called successfully', PHP_EOL;
    echo 'Complete Response: ', $searchItemsResponse, PHP_EOL;

    # Parsing the response
    if ($searchItemsResponse->getSearchResult() != null) {
        echo 'Printing first item information in SearchResult:', PHP_EOL;
        $item = $searchItemsResponse->getSearchResult()->getItems()[0];
        if ($item != null) {
            if ($item->getASIN() != null) {
                echo "ASIN: ", $item->getASIN(), PHP_EOL;
            }
            if ($item->getDetailPageURL() != null) {
                echo "DetailPageURL: ", $item->getDetailPageURL(), PHP_EOL;
            }
            if ($item->getItemInfo() != null
                and $item->getItemInfo()->getTitle() != null
                and $item->getItemInfo()->getTitle()->getDisplayValue() != null) {
                echo "Title: ", $item->getItemInfo()->getTitle()->getDisplayValue(), PHP_EOL;
            }
            if ($item->getOffers() != null
                and $item->getOffers() != null
                and $item->getOffers()->getListings() != null
                and $item->getOffers()->getListings()[0]->getPrice() != null
                and $item->getOffers()->getListings()[0]->getPrice()->getDisplayAmount() != null) {
                echo "Buying price: ", $item->getOffers()->getListings()[0]->getPrice()
                    ->getDisplayAmount(), PHP_EOL;
            }
        }
    }
    if ($searchItemsResponse->getErrors() != null) {
        echo PHP_EOL, 'Printing Errors:', PHP_EOL, 'Printing first error object from list of errors', PHP_EOL;
        echo 'Error code: ', $searchItemsResponse->getErrors()[0]->getCode(), PHP_EOL;
        echo 'Error message: ', $searchItemsResponse->getErrors()[0]->getMessage(), PHP_EOL;
    }
} catch (ApiException $exception) {
    echo "Error calling PA-API 5.0!", PHP_EOL;
    echo "HTTP Status Code: ", $exception->getCode(), PHP_EOL;
    echo "Error Message: ", $exception->getMessage(), PHP_EOL;
    if ($exception->getResponseObject() instanceof ProductAdvertisingAPIClientException) {
        $errors = $exception->getResponseObject()->getErrors();
        foreach ($errors as $error) {
            echo "Error Type: ", $error->getCode(), PHP_EOL;
            echo "Error Message: ", $error->getMessage(), PHP_EOL;
        }
    } else {
        echo "Error response body: ", $exception->getResponseBody(), PHP_EOL;
    }
} catch (Exception $exception) {
    echo "Error Message: ", $exception->getMessage(), PHP_EOL;
}
```

Complete documentation, installation instructions, and examples are available [here](https://webservices.amazon.com/paapi5/documentation/with-sdk.html).
