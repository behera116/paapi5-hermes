<?php
//implementing pa-api5 functionality using single Class
namespace Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1;

/**
Common dependencies
**/
use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\api\DefaultApi;
use Amazon\ProductAdvertisingAPI\v1\ApiException;
use Amazon\ProductAdvertisingAPI\v1\Configuration;
use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\PartnerType;
use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\ProductAdvertisingAPIClientException;

/**
ItemLookup dependencies
**/
use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\GetItemsRequest;
use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\GetItemsResource;

/**
BrowserNode dependencies
**/
use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\GetBrowseNodesRequest;
use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\GetBrowseNodesResource;

/**
VariationResource dependencies
**/
use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\GetVariationsRequest;
use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\GetVariationsResource;

/**
SearchItems dependencies
**/
use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\SearchItemsRequest;
use Amazon\ProductAdvertisingAPI\v1\com\amazon\paapi5\v1\SearchItemsResource;



class ProductLookup{
    
    private $apiInstance;
    private $partnerTag;
    private $region = "eu-west-1";
    private $host = "webservices.amazon.in";
    private $searchIndex = 'Electronics';

    public function __construct($configParams){
        $requiredParams = ['accessKey','secretKey','partnerTag'];
        foreach($requiredParams as $param){
            if(!array_key_exists($param,$configParams) || empty($configParams[$param])){
                throw new Exception("{$param} value is missing from argument.");
            }
        }

        $this->partnerTag = $configParams['partnerTag'];

        if(!empty($configParams['region'])){
            $this->region = $configParams['region'];
        }

        if(!empty($configParams['host'])){
            $this->host = $configParams['host'];
        }

        $config = new Configuration();
        $config->setAccessKey($configParams['accessKey']);
        $config->setSecretKey($configParams['secretKey']);
        $config->setHost($this->host);
        $config->setRegion($this->region);
       
        $this->apiInstance = new DefaultApi(new \GuzzleHttp\Client(), $config);
 
    }


    public function getItems($asins,$resources=array()){//StringArray
    
        $resources = array(        
        GetItemsResource::ITEM_INFOTITLE,
        GetItemsResource::OFFERSLISTINGSPRICE,
        GetItemsResource::OFFERSLISTINGSCONDITION,
        GetItemsResource::OFFERSLISTINGSAVAILABILITYTYPE,
        GetItemsResource::OFFERSSUMMARIESLOWEST_PRICE,
        GetItemsResource::OFFERSLISTINGSPROMOTIONS,
        GetItemsResource::OFFERSLISTINGSAVAILABILITYMESSAGE,
        GetItemsResource::OFFERSLISTINGSMERCHANT_INFO,
        GetItemsResource::BROWSE_NODE_INFOBROWSE_NODES,
        GetItemsResource::ITEM_INFOPRODUCT_INFO,
        GetItemsResource::ITEM_INFOTECHNICAL_INFO,
        GetItemsResource::ITEM_INFOCONTENT_RATING,
        GetItemsResource::ITEM_INFOCLASSIFICATIONS,
        GetItemsResource::IMAGESPRIMARYLARGE,
        GetItemsResource::IMAGESVARIANTSLARGE,
        GetItemsResource::RENTAL_OFFERSLISTINGSDELIVERY_INFOIS_AMAZON_FULFILLED,
        GetItemsResource::RENTAL_OFFERSLISTINGSCONDITION,
        GetItemsResource::RENTAL_OFFERSLISTINGSDELIVERY_INFOIS_FREE_SHIPPING_ELIGIBLE,
        GetItemsResource::RENTAL_OFFERSLISTINGSDELIVERY_INFOSHIPPING_CHARGES,
        GetItemsResource::ITEM_INFOTITLE);

    
        $itemRequest = new GetItemsRequest();
        $itemRequest->setItemIds($asins);
        $itemRequest->setPartnerTag($this->partnerTag);
        $itemRequest->setPartnerType(PartnerType::ASSOCIATES);        
        $itemRequest->setResources($resources);
   
        //validating itemRequest 
        $this->checkInvalidProperty($itemRequest); 

        $itemResponse = $this->apiInstance->getItems($itemRequest);
        $itemsResult = json_decode($itemResponse,true);

        $items = !empty($itemsResult['ItemsResult']['Items'])?$itemsResult['ItemsResult']['Items']:null;
        
        $result = [];
        if(!empty($itemsResult['Errors'])){
            $result['Errors'] = $itemsResult['Errors'];
        }

        if(!empty($items)){
            $itemList = null; 
            foreach($items as $index => $item){
                $asin = $item['ASIN'];
                $itemList[$asin] = $item;
            }
            $result['Items'] = $itemList;
        }
        return $result;

    }


    //work in progress by Amazon Services(500 Internal Error)
    public function browseNodes($nodeids){//string array for nodes
        
        $resources = array(
        GetBrowseNodesResource::ANCESTOR,
        GetBrowseNodesResource::CHILDREN);

        $browserNodesRequest = new GetBrowseNodesRequest();
        $browserNodesRequest->setBrowseNodeIds($nodeids);
        $browserNodesRequest->setPartnerTag($this->partnerTag);
        $browserNodesRequest->setPartnerType(PartnerType::ASSOCIATES);
        $browserNodesRequest->setResources($resources);

        //validating browser node request
        $this->checkInvalidProperty($browserNodesRequest);        

        $nodeResponse = $this->apiInstance->getBrowseNodes($browserNodesRequest);
        //echo "Complete response",$nodeResponse,PHP_EOL;

        $response = json_decode($nodeResponse,true);
        $nodes = $response['BrowseNodesResult']['BrowseNodes'];

        $nodeList = null;
        if(!empty($nodes)){
            foreach($nodes as $node){
                $nodeid = $node['Id'];
                $nodeList[$nodeid] = $node;
            }

        }
        
        return $nodeList;
    }


    public function searchItems($params){
        
        if(empty($params['keyword']))
            return null;

        $keyword = $params['keyword'];
        $itemCount = empty($params['itemCount'])?$this->itemCount:$params['itemCount'];
        $searchIndex = empty($params['searchIndex'])?$this->searchIndex:$params['searchIndex'];

        $resources = array(
        SearchItemsResource::ITEM_INFOTITLE,
        SearchItemsResource::ITEM_INFOPRODUCT_INFO,
        SearchItemsResource::IMAGESPRIMARYLARGE,
        SearchItemsResource::OFFERSLISTINGSPRICE);

        # Forming the request
        $searchItemsRequest = new SearchItemsRequest();
        $searchItemsRequest->setSearchIndex($searchIndex);
        $searchItemsRequest->setKeywords($keyword);
        $searchItemsRequest->setItemCount($itemCount);
        $searchItemsRequest->setPartnerTag($this->partnerTag);
        $searchItemsRequest->setPartnerType(PartnerType::ASSOCIATES);
        $searchItemsRequest->setResources($resources);
   
        $this->checkInvalidProperty($searchItemsRequest);

        $response = $this->apiInstance->searchItems($searchItemsRequest);
        $result = json_decode($response,true);
        
        return $result;
    }   

 
    private function checkInvalidProperty($nodeRequest){
        
        $invalidProperties = $nodeRequest->listInvalidProperties();
        if(count($invalidProperties)>0){
            $invalidString = implode(",",$invalidProperties);
            throw new Exception("error occurred while forming item request. invalid parameters for - ".$invalidString.PHP_EOL);
        }
    
    }


}



?>
