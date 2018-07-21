<?php 

use Http\Request;
use Http\Response;
use Service\Render;
use ZohoCRM\Lead;

require 'src/Autoloader.php';
require 'src/config.php';

$request = Request::fromGlobals();
$path = $request->getUri();
$action = null;

try {
	
	if($path == '/') {
		$action = function(Request $request) {
			return new Response(Render::render('view/form.html'));
		};
	} elseif($path == '/send') {
		$action = function(Request $request) {
			(new Lead($request->getBody()))->save();
			return (new Response(''))->withHeader('Location', '/');
		};
	}
	
	if($action) {
		$response = $action($request);
	} else {
		$response = (new Response('Not found'))->withHeader('HTTP/1.0 404 Not Found');
	}
	
} catch(Exception $e) {
	$response = (new Response("Error: {$e->getMessage()}"));
}

$response->emit();