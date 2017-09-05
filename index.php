<?php
/**
 * Created by PhpStorm.
 * User: celldweller
 * Date: 7/24/17
 * Time: 11:07 PM
 */
require __DIR__ . '/vendor/autoload.php';

use App\contentParse;
$obj = new contentParse();
$obj->run();