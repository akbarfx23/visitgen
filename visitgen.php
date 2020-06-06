<?php
error_reporting(0);
use phpseclib\Net\SSH2;
require __DIR__ .'/vendor/autoload.php';
require_once __DIR__.'/userAgent.php';

class autovisitor extends userAgent{
	public function __construct($url, $title) {
		$this->url = $url;
		$this->title = $title;
		$this->agent = new userAgent();
	}
	private function curl() {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->url);
		curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5 );
		curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, 1);
		curl_setopt($ch, CURLOPT_PROXY, 'socks5://127.0.0.1:9050');
		//curl_setopt($ch, CURLOPT_HEADER, 1);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_setopt($ch, CURLOPT_CAINFO, '/etc/ssl/certs/ca-certificates.crt');
		curl_setopt($ch, CURLOPT_CAPATH, '/etc/ssl/certs/ca-certificates.crt');
		curl_setopt($ch, CURLOPT_REFERER, $this->acakReferer().'/search?q='.urlencode($this->title));
		curl_setopt($ch, CURLOPT_HTTPHEADER, ['Cache-Control: no-cache', 'Content-Type: text/html; charset=UTF-8']);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		//curl_setopt($ch, CURLOPT_COOKIESESSION, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
		//curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
		curl_setopt($ch, CURLOPT_USERAGENT, $this->generate('android'));
		$result = curl_exec($ch);
		if(!curl_errno($ch)){
        	$info = curl_getinfo($ch);
			//echo json_encode($info, JSON_PRETTY_PRINT).PHP_EOL;
		}
		curl_close($ch);
		return $result;
	}

	private function xflush() {
		static $output_handler = null;
		if ($output_handler === null) {
			$output_handler = @ini_get('output_handler');
		}
		if ($output_handler == 'ob_gzhandler') {
			return;
		}
		flush();
		if (function_exists('ob_flush') AND function_exists('ob_get_length') AND ob_get_length() !== false) {
			@ob_flush();
		} else if (function_exists('ob_end_flush') AND function_exists('ob_start') AND function_exists('ob_get_length') AND ob_get_length() !== FALSE) {
			@ob_end_flush();
			@ob_start();
		}
	}
	private function acakReferer() {
		$list = array();
		/* Asal traffic yang di submit */ 
		$list[] = "https://facebook.com";
		$list[] = "https://google.com.sg";
		$list[] = "https://twitter.com";
		$list[] = "https://google.co.id";
		$list[] = "https://google.com.my";
		$list[] = "https://google.jp";
		$list[] = "https://google.us";
		$list[] = "https://google.tl";
		$list[] = "https://google.ac";
		$list[] = "https://google.ad";
		$list[] = "https://google.ae";
		$list[] = "https://google.af";
		$list[] = "https://google.ag";
		$list[] = "https://google.ru";
		$list[] = "https://google.by";
		$list[] = "https://google.ca";
		$list[] = "https://google.cn";
		$list[] = "https://google.cl";
		$list[] = "https://google.cm";
		$list[] = "https://google.cv";
		$list[] = "https://google.gg";
		$list[] = "https://google.ge";
		$list[] = "https://google.gr";
		$list[] = "https://google.com.tw";
		$list[] = "https://search.yahoo.com";
		$list[] = "https://www.beinyu.com";
		$acak = array_rand($list,1);
		return $list[$acak];
	}

	public function jalankan() {
		$this->xflush();
		$this->curl();
		$this->xflush();
	}

} 
$ssh = new SSH2('127.0.0.1');
if (!$ssh->login('root', 'QWeas1324@')) {
    exit('Login Failed');
}
while(true){
	$url = json_decode(file_get_contents("https://www.icipnet.store/feeds/posts/default?alt=json&start=0&max-results=999999999"));
	foreach($url->feed->entry as $entry){
		$url = $entry->link[4]->href;
		foreach($entry->title as $title => $text){
			if($text !== 'text'){
				$title = $text;
			}
		}
		if(!isset($text)){
			$title = 'Icipnet Store';
		}
		if($ssh->exec('systemctl restart tor') !== null){
			sleep(10);
			$class = new autovisitor($url.'?m=1', $title);
			$class->jalankan();
			unset($class);
		}
	}
}
?>


