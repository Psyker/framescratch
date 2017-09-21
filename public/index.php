<?php

use App\Blog\BlogModule;
use Framework\App;

require '../vendor/autoload.php';

$app = new App([
    BlogModule::class
]);

$response = $app->run(\GuzzleHttp\Psr7\ServerRequest::fromGlobals());

\Http\Response\send($response);
