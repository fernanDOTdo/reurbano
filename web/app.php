<?php
require_once __DIR__.'/../app/bootstrap.php.cache';
require_once __DIR__.'/../app/AppKernel.php';
//require_once __DIR__.'/../app/bootstrap_cache.php.cache';
//require_once __DIR__.'/../app/AppCache.php';

use Symfony\Component\HttpFoundation\Request;

//$kernel = new AppCache(new AppKernel('prod', false));
if(strpos(@$_SERVER['REMOTE_ADDR'], '192.168.0') === false && !in_array(@$_SERVER['REMOTE_ADDR'], array('127.0.0.1', '::1'))) {
	$kernel = new AppKernel('prod', false);
}else{
        umask(0000);
	$kernel = new AppKernel('dev', true);
}
$kernel->loadClassCache();
$kernel->handle(Request::createFromGlobals())->send();
