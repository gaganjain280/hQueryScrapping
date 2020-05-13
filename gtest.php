<?php
use duzun\hQuery;
include_once 'hquery.php';
include 'include/config.php';
hQuery::$cache_path = "/path/to/cache";
// Time to keed request data in cache, seconds
// A value of 0 disables cahce
hQuery::$cache_expires = 3600; // default one hour
class ScrappLogic{
       //********* Main responsible for get vendor data from front end **************//        
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
          $t=1;
        foreach($category_list as $cat_cont) {
         $category_url = $cat_cont['cat_url'];
          $get_product_array= $getAccessSites->catagoryPageScrap($category_url);
            foreach($get_product_array as $product_count_obj) {
            $get_product_array=$product_count_obj; 
             // $single_product_link="https://www.rossmann.de/de/haushalt-palmolive-geschirrspuelmittel-limonenfrisch/p/4011200576901";
              $single_product_link=$get_product_array;
              echo "<pre>";print_r($single_product_link);echo "</pre>";
               $getAccessSites->singlePageScrap($single_product_link);
             }
             print_r($t."time");
             $t++;
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
    $cat_dom = hQuery::fromFile((string)$category_url,false);
    $cat_container=$product_link=$getpro="";
    $i=$j=0;
    foreach($cat_dom->find(".rm-category__products .rm-grid__wrapper .rm-grid__content") as $cat_container) {
	    foreach($cat_container->find(".rm-tile-product .rm-tile-product__advises") as $getpro) {
					 $product_linkk = (string)$getpro->href;	              
	                if(in_array($product_linkk,$product_url_array)){
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
        // $doc = new \DOMDocument();
        $dom_req_ulr = hQuery::fromFile($req_ulr, false);
         if(!empty($dom_req_ulr)){
        $sale_price=$attr= $original_price=$title=$cart=$imgsrc=$discription_div=$discriptiontext=$sale_price="";$i = 0;
        //*********  process to fectch image url***********//  
        foreach($dom_req_ulr->find(".rm-product__image .rm-product__image") as $mage) {
           $imgsrc = (string)$mage->src;
         }
       $image_thumbs="";
       $b=0;
       $product_gallery = array();
       // $docimage_thumbs = new \DOMDocument();
        foreach($dom_req_ulr->find(".rm-productdetail__image-thumbs .swiper-container .swiper-wrapper .swiper-slide .rm-product__image-thumb") as $image_thumbs){        
               $thumbsimgsrc = (string)$image_thumbs->src;
               array_push($product_gallery,$thumbsimgsrc);
               $b++;
        }
         //*********  process to fetch description   ***********//  
        foreach($dom_req_ulr->find(".rm-accordion__detail") as $discription_div) {
           $discription = $discription_div->text();
           $discriptiontext=$discriptiontext.'</br><br>'.$discription;
        }
        foreach($dom_req_ulr->find('.rm-productdetail__card-wrapper .rm-product__card') as $cart ) {
                foreach($cart->find('.rm-product__title') as $title ) {
                $titletext = $title->text();
                $title = preg_replace('/\&#034;/', "'", $titletext);
               }   
                foreach($cart->find('.rm-price__current') as $sale_p ) {
                $sale_price = $sale_p->content; 
                }
                if($dom_req_ulr->find(".rm-price__strikethrough")){   
	                foreach($cart->find('.rm-price__strikethrough') as $original_p ) {
	        		 $original_pr = (string)$original_p->text();
	                 $original_price = trim($original_pr," € ");
	                 $original_price =((float)str_replace(",",".",$original_price));
	                 }
                }      
        }  
        $variation=$urlvariation="";
        $variations = array();
         if($dom_req_ulr->find(".yCmsComponent .rm-variations")){
         foreach($dom_req_ulr->find(".rm-productdetail__card-wrapper .rm-product__card .page-details-variants-select .rm-variations .rm-variations__colors .rm-grid__wrapper") as $variation){
            foreach($variation->find(".rm-variations__item") as $urlvariation) {
            $variation_option = $urlvariation->text(); 
            $variation_option = preg_replace('/\s+/', ' ', $variation_option);
            $variationsrc = (string)$urlvariation->href;
            $variations[$i]["option_value"]=$variation_option;
            $variations[$i]["url"]=$variationsrc;
            $varitiondom= hQuery::fromFile($variationsrc, false);
            //
            $variation_cart=$variation_original_p="";
            foreach($varitiondom->find('.rm-productdetail__card-wrapper .rm-product__card') as $variation_cart ) {
                foreach($variation_cart->find('.rm-price__current') as $variation_sale_p ) {
                $variation_s_price = $variation_sale_p->content; 
                $variations[$i]["sale_price"]=$variation_s_price;
                }   
                if($varitiondom->find(".rm-price__strikethrough")){  
	                foreach($variation_cart->find('.rm-price__strikethrough') as $variation_original_p ) {
	        		 $variation_original_pr = (string)$variation_original_p->text();
	                 $variation_original_price = trim($variation_original_pr," € ");
	                 $variation_original_price =((float)str_replace(",",".",$variation_original_price));
	                 $variations[$i]["original_price"]=$variation_original_price;
	                }  
                }        
            }   
            $i++;
            }
         }
        }
            $encode_product_gallery=json_encode($product_gallery);
	        if(sizeof($variations) == 0 ){ 
	        	 $sql = "INSERT INTO product(title,single_page_link,image,main_price,sale_price,description,product_gallery,variation_url) VALUES ('$title','$req_ulr','$imgsrc','$original_price','$sale_price','$discriptiontext','$encode_product_gallery','')";
	                $res=mysql_query($sql);
	        }else{
	        $encode_variations=json_encode($variations);
	        $sql = "INSERT INTO product(title,single_page_link,image,main_price,sale_price,description,product_gallery,variation_url) VALUES ('$title','$req_ulr','$imgsrc','$original_price','$sale_price','$discriptiontext','$encode_product_gallery','$encode_variations')";
	                $res=mysql_query($sql);
	        }
        }else{
            echo "DOM Failed to get data";
        }
         // exit; 
        return true;
    }
}
class DecideFactoryToScrap{
public function createScraping($class, $storeOderDetail){   
    return new $class($storeOderDetail);
  }
}
$Scrapobj=new ScrappLogic();
