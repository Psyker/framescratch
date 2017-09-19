<?php

use Framework\App;

require '../vendor/autoload.php';

$app = new App();

$response = $app->run(\GuzzleHttp\Psr7\ServerRequest::fromGlobals());

\Http\Response\send($response);
