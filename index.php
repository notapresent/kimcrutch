<?php
error_reporting(-1); ini_set('display_errors', 1);
require 'vendor/autoload.php';

$alias = strtolower($_GET['alias']);
$classname = '\\KimonoCrutch\\' . ucfirst($alias) . 'Crutch';
$crutch = new $classname;
$action = isset($_GET['action']) ? $_GET['action'] : 'index';

if($action == 'index') {
    echo $crutch->index();
} else {
    $url = urldecode($_GET['url']);
    echo $crutch->detail($url);
}
