<?php require_once __DIR__ . "/loader.php";

use \de\roccogossmann\php\funframes\Page;
use \de\roccogossmann\php\funframes\Layout;

$oLayout = Layout::load("./pages/index/layout");
$oLayout->render();

exit;

