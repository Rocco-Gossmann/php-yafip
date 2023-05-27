<?php require_once __DIR__ . "/loader.php";

use rogoss\yafip\Page;

$oPage = Page::load('index');
$oPage->render();
