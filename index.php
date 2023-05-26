<?php require_once __DIR__ . "/loader.php";

use \de\roccogossmann\php\funframes\Page;
header("content-type: text/plain");

$oPage = Page::load('index');
$oPage->render();
