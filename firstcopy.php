<?php
use duzun\hQuery;
include_once 'hquery.php';
include 'include/config.php';
// Set the cache path - must be a writable folder
// If not set, hQuery::fromURL() whould make a new request on each call
hQuery::$cache_path = "/path/to/cache";
// Time to keed request data in cache, seconds
// A value of 0 disables cahce
hQuery::$cache_expires = 3600; // default one hour
class ScrappLogic{
       //********* Main responsible for get data from front end **************//        
       public function __construct(){  
        $thtml = "";
        $scrapingDetails = new ScrapFactoryMain;
        $DecideFactory  = new DecideFactoryToScrap;
        $site='1';
        if($site==1){
         $getAccessSites  = $DecideFactory->createScraping("ScrapRossmann", $scrapingDetails);
         $category_list=array();
         $category_list[0]['cat_url'] = "https://www.rossmann.de/de/haushalt/spuelmittel/c/olcat2_23?q=%3Arelevance&page=";
         $category_list[1]['cat_url'] = "https://www.rossmann.de/de/haushalt/putzmittel/c/olcat2_n_8?q=%3Arelevance&page=";
         $category_list[2]['cat_url'] = "https://www.rossmann.de/de/haushalt/putzutensilien/c/olcat2_21?q=%3Arelevance&page=";
         $category_list[3]['cat_url'] = "https://www.rossmann.de/de/haushalt/haushaltspapier/c/olcat2_25?q=%3Arelevance&page=";
         foreach($category_list as $cat_cont) {
         $category_url = $cat_cont['cat_url'];

         $get_product_array= $getAccessSites->catagoryPageScrap($category_url);
         // echo "<pre>";  print_r($get_product_array); echo "</pre>"; 
         // print_r($get_product_array);exit;
            foreach($get_product_array as $product_count_obj) {
             $get_product_array=$product_count_obj; 
             $single_product_link=$get_product_array;
             // $single_product_link="https://www.rossmann.de".$get_product_array;
// print_r($single_product_link);
             // exit;
             // print_r($single_product_link);exit;
             $getAccessSites->singlePageScrap($single_product_link);
            }
         }
         echo "Data Copied";
        }else{
            echo "unauthorize registered scrapping";
        }
       }
}
interface ScrapFunctions{
     public function catagoryPageScrap($category_url);
     public function singlePageScrap($req_ulr);
 }
class ScrapFactoryMain{
    public $catagoryPageScrap;
    public $singlePageScrap;
}
class ScrapRossmann implements ScrapFunctions{
    private $storeOderDetail;
    public function __construct(ScrapFactoryMain $oderDetails) {
        $this->storeOderDetail = $oderDetails;
    }   
    public function catagoryPageScrap($category_url){
    $product_url_array = array();
    $pagecount=0;

    while ($pagecount>-1) {
    $category_url=$category_url.$pagecount;
    // $cat_dom = file_get_html((string)$category_url, false);
    $cat_dom = hQuery::fromFile((string)$category_url,false);
    // print_r($cat_dom);exit;
    $cat_container=$product_link=$getpro="";
    $i=$j=0;
    // print_r($cat_dom);exit;
    foreach($cat_dom->find(".rm-category__products .rm-grid__wrapper .rm-grid__content") as $cat_container) {
	    foreach($cat_container->find(".rm-tile-product .rm-tile-product__advises") as $getpro) {
	 // $titles[] = $value->text();
	    	  	      // $getproo = $getpro->find('a')[0];

					 $product_linkk = (string)$getpro->href;
	                // print_r($product_linkk);
	               // $document = new \DOMDocument();
	               // $getproo=html_entity_decode($getpro);
	               // $document->loadHTML($getproo);
	               // $xpathx = new \DOMXPath($document);
	               // $product_linkk = $xpathx->evaluate("string(//a/@href)");
	               // $product_linkk = (string)$product_linkk;
	              
	                if(in_array($product_linkk,$product_url_array)){
	                 // echo "out";
	                 $pagecount=-1;
	                return $product_url_array;
	               }else{
	                array_push($product_url_array,$product_linkk);
	              }  
	           $i++;           
	    }
   
     }
      $pagecount++;
     }
    }
    public function singlePageScrap($req_ulr){
    	// print_r($req_ulr);exit;
        $doc = new \DOMDocument();
        $dom_req_ulr = hQuery::fromFile($req_ulr, false);
         if(!empty($dom_req_ulr)){
        $sale_price=$attr= $original_price=$title=$cart=$imgsrc=$discription_div=$discriptiontext=$sale_price="";$i = 0;
        //*********  process to fectch image url***********//  
        foreach($dom_req_ulr->find(".rm-product__image .rm-product__image") as $mage) {
        	 // $product_linkk =
           // $mage=html_entity_decode($mage);
           // $doc->loadHTML($mage);
           // $xpath = new \DOMXPath($doc);
           // $imgsrc = $xpath->evaluate("string(//img/@data-src)");
        	  	// $img = $value->find('img')[0];
				         	// $images[] = $img->src;
           $imgsrc = (string)$mage->src;
          // print_r($imgsrc);exit;/
         }
       $image_thumbs="";
       $b=0;
       $product_gallery = array();
       $docimage_thumbs = new \DOMDocument();
        foreach($dom_req_ulr->find(".rm-productdetail__image-thumbs .swiper-container .swiper-wrapper .swiper-slide .rm-product__image-thumb") as $image_thumbs){
               // $image_thumbs=html_entity_decode($image_thumbs);
               // $docimage_thumbs->loadHTML($image_thumbs);
               // $xpathathumb = new \DOMXPath($docimage_thumbs);
               $thumbsimgsrc = (string)$image_thumbs->src;
               // $thumbsimgsrc = $xpathathumb->evaluate("string(//img/@src)");
               // $product_gallery=$thumbsimgsrc;
               // print_r($thumbsimgsrc);exit;
            array_push($product_gallery,$thumbsimgsrc);

               $b++;
        }
         //*********  process to fetch description   ***********//  
        foreach($dom_req_ulr->find(".rm-accordion__detail") as $discription_div) {
          $discription = $discription_div->text();
          // $discription = html_entity_decode($discription_div->plaintext);
          //  $discriptiontext=$discriptiontext.'</br><br>'.$discription;
           $discriptiontext=$discriptiontext.'</br><br>'.$discription;
           // print_r($discriptiontext);exit;
        }
        foreach($dom_req_ulr->find('.rm-productdetail__card-wrapper .rm-product__card') as $cart ) {
                foreach($cart->find('.rm-product__title') as $title ) {
                $titletext = $title->text();
                $title = preg_replace('/\&#034;/', "'", $titletext);
                // print_r($title);
               }   
                foreach($cart->find('.rm-price__current') as $sale_p ) {
                //     $docc = new \DOMDocument();
                // $sale_price_obj = html_entity_decode($sale_price);
                // $docc->loadHTML($sale_price_obj);
                // $xpathprice = new \DOMXPath($docc);
                $sale_price = $sale_p->content; 
                // print_r($sale_price);exit;
                }   
                foreach($cart->find('.rm-price__strikethrough') as $original_p ) {
                	  $original_pr = (string)$original_p->text();
                // $original_price = html_entity_decode($original_price->plaintext);
                 $original_price = trim($original_pr," € ");
                 $original_price = preg_replace('/\s+/', ' ', $original_price);
                 $original_price =str_replace(",",".",$original_price);
                    	// print_r($original_pric);exit;  

                }          
        }  
        $variation=$urlvariation="";
        $doccurlvariation = new \DOMDocument();
        $variations = array();
        if($dom_req_ulr->find(".yCmsComponent .rm-variations")){
          foreach($dom_req_ulr->find(".rm-productdetail__card-wrapper .rm-product__card .page-details-variants-select .rm-variations .rm-variations__colors .rm-grid__wrapper") as $variation) {
            foreach($variation->find(".rm-variations__item") as $urlvariation) {
            $variation_option = $urlvariation->text(); 
            // $variation_option = html_entity_decode($urlvariation->plaintext); 
            $variation_option = preg_replace('/\s+/', ' ', $variation_option);
            // $urlvariation=html_entity_decode($urlvariation);
            // $doccurlvariation->loadHTML($urlvariation);
            // $doccurlvariationxpath = new \DOMXPath($doccurlvariation);

            $variationsrc = (string)$urlvariation->href;
            $variations[$i]["option_value"]=$variation_option;
            $variations[$i]["url"]=$variationsrc;
            // print_r($variations);exit;
            // $variationUrl=$variationsrc;
            // array_push($variations,$variations);
            $i++;
            }
         }
       }else{}
       $encode_variations=json_encode($variations);
       $encode_product_gallery=json_encode($product_gallery);
       $sql = "INSERT INTO product(title,single_page_link,image,main_price,sale_price,description,product_gallery,variation_url) VALUES ('$title','$req_ulr','$imgsrc','$original_price','$sale_price','$discriptiontext','$encode_product_gallery','$encode_variations')";
                $res=mysql_query($sql);
        }else{
            echo "DOM Failed to get data";
        }
         // exit; 
        return true;
    }
}
class DecideFactoryToScrap{
public function createScraping($class, $storeOderDetail){   
// print_r( $storeOderDetail);
    return new $class($storeOderDetail);
  }
}
$Scrapobj=new ScrappLogic();

