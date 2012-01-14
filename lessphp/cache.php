<?php
if(substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip'))
	ob_start("ob_gzhandler");
else
	ob_start();

$file=dirname(__FILE__) . '/../../' . $_GET['f'];

if(!file_exists($file) || !preg_match('/\.(less\.css)$/i', $_GET['f']))
	die ;
$filecrumb = explode('/', $_GET['f']);
$filename = str_replace(".less.css","", $filecrumb[sizeof($filecrumb)-1]);

$time = mktime(0, 0, 0, 21, 5, 1980);
$cache = 'cache/'.$filename.'.css';

$fileTime = filemtime(dirname(__FILE__) . '/../../' . $_GET['f']);
if($fileTime > $time) {  
    $time = $fileTime;  
} 
	
if(file_exists($cache)) {
	$cacheTime = filemtime($cache);
	if($cacheTime < $time) {
		$time = $cacheTime;
		$recache = true;
	} else {
		$recache = false;
	}
} else {
	$recache = true;
}  

if(!$recache && isset($_SERVER['If-Modified-Since']) && strtotime($_SERVER['If-Modified-Since']) >= $time){  
    header("HTTP/1.0 304 Not Modified");  
} else {  
    header('Content-type: text/css');  
    header('Last-Modified: ' . gmdate("D, d M Y H:i:s",$time) . " GMT");  
  
    if($recache) {  
        require 'lessc.inc.php';  
        $lc = new lessc();  
        $css = '';  
        $css .= file_get_contents($file);  
        $css = $lc->parse($css);  
        file_put_contents($cache, $css);  
        echo $css.'/*'.$time.'*/';  
    } else {  
        readfile($cache);  
    }  
}  
?> 