<?php
// Optionally use namespaces
use duzun\hQuery;


$a="0.99 € â‚¬";
$b=(float)$a;
print_r($b);exit; 
// Either use composer, or include this file:
include_once 'hquery.php';

// Set the cache path - must be a writable folder
// If not set, hQuery::fromURL() whould make a new request on each call
hQuery::$cache_path = "/path/to/cache";

// Time to keed request data in cache, seconds
// A value of 0 disables cahce
hQuery::$cache_expires = 3600; // default one hour

class ScrapProduct {
  
  function get_single_page_contnet($url){
      $single_product = hQuery::fromFile('https://www.rossmann.de/de/haushalt/haushaltspapier/c/olcat2_25', false);
      // print_r($single_product);exit;
  }
  
   function get_category_page_contnet($url){
    
		$images = array();
		$titles = array();
		$price  = array();
		$brand  = array();
		$retail_price  = array();
		$product_data  = array(); 

		for ($i=0; $i < count($url) ; $i++) { 
		// print_r($url[$i]);
        $cat_page = hQuery::fromFile($url[$i], false);
		// Find all product (images inside anchors)
		$product = $cat_page->find('.rm-category__products');

		// Extract links and images
		// If the result of find() is not empty
		// $product is a collection of elements (hQuery_Element)
		if ( $product ) {
		    // Iterate over the result
		    foreach($product as $pos => $a) {
				         $pro_title = $a->find('.rm-product__title');
				         foreach ($pro_title as $key => $value) {
				         	$titles[] = $value->text();
				         }
				         $pro_retail_price = $a->find('.rm-price__retail');
				         foreach ($pro_retail_price as $key => $value) {
				         	$retail_price[] = $value->text();
				         }
				         $pro_price = $a->find('.rm-price__current');
				         foreach ($pro_price as $key => $value) {
				         	$price[] = $value->text();
				         }
				         $pro_img = $a->find('picture');
				         foreach ($pro_img as $key => $value) {
				         	$img = $value->find('img')[0];
				         	$images[] = $img->src;
				         }
				         $pro_brand = $a->find('.rm-product__brand');
				         foreach ($pro_brand as $key => $value) {
				         	$brand[] = $value->text();
				         }
		            }
               }
	      }
        $product_data[] = $titles;
        $product_data[] = $images;
        $product_data[] = $price;
        $product_data[] = $retail_price;
        $product_data[] = $brand;
       return $product_data;
     }  
}	 

$scrap_obj = new ScrapProduct();		
$url_arr = array();
for ($i=0; $i <2 ; $i++) { 
  $url_arr[] = 'https://www.rossmann.de/de/haushalt/haushaltspapier/c/olcat2_25?q=%3Arelevance&page='.$i.'&pageSize=24'; 
  // print_r($url_arr);
}
$product_con =  $scrap_obj->get_category_page_contnet($url_arr);
 echo "<pre>";
print_r($product_con);
 echo "</pre>";
?>