<?php
require_once "settings.php";


require_once "vendor/autoload.php";
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
