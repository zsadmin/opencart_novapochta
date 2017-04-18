<?php

class Url {
	private $url;
	private $ssl;
	private $rewrite = array();
	
	public function __construct($url, $ssl = '') {
		$this->url = $url;
		$this->ssl = $ssl;
	}
		
	public function addRewrite($rewrite) {
		$this->rewrite[] = $rewrite;
	}
		
	public function link($route, $args = '', $connection = 'NONSSL') {
         
        $get_route = isset($_GET['route']) ? $_GET['route'] : (isset($_GET['_route_']) ? $_GET['_route_'] : '');
               
        // This code replace link to the checkout page, comment or delete this block if you want to use the standart checkout page instead off  the novapochtacheckout page 
        if ($route == 'checkout/checkout' && $get_route != 'checkout/checkout') {
            $route = 'checkout/novapochtacheckout';
        }
        
        if ($route == 'account/register' && $get_route != 'account/register') {
            $route = 'account/novapochtaregister';
        }
  
		if ($connection ==  'NONSSL') {
			$url = $this->url;
		} else {
			$url = $this->ssl;	
		}
		
		$url .= 'index.php?route=' . $route;
			
		if ($args) {
			$url .= str_replace('&', '&amp;', '&' . ltrim($args, '&')); 
		}
		
		foreach ($this->rewrite as $rewrite) {
			$url = $rewrite->rewrite($url);
		}
				
		return $url;
	}
}
?>
