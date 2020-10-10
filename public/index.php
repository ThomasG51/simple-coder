<?php

// Une autre methode ?
require '../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;

$request = Request::create('', 'GET', ['name' => 'Thomas']);

dump($request);
dump($request->get('name'));