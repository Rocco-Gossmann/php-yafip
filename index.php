<?php require_once __DIR__ . "/loader.php";

use de\roccogossmann\php\funframes\Component;
use \de\roccogossmann\php\funframes\Layout;
use \de\roccogossmann\php\funframes\Page;

Page::create(Layout::load("./pages/index"))
    ->setChildCompnent("content", Component::create(Layout::load("./components/debugging")))
    ->loadData("./data/debugger.php") 
    ->render()
;
