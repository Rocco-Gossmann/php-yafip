<?php require_once __DIR__ . "/loader.php";

use rogoss\yafip\Page;
header("content-type: text/plain");

$oPage = Page::load('index');
$oPage->render();
