<?php
require '../vendor/autoload.php';
use App\Kernel;
use SimpleFW\HttpBasics\HttpRequest;

$kernel = new Kernel();

$request = HttpRequest::createFromGlobas();
$response = $kernel->handle($request);
$response->send();