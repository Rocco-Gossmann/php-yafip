<?php require_once __DIR__ . "/loader.php";

use \de\roccogossmann\php\funframes\Layout;

$oLayout = Layout::load("./pages/index/layout");
$oLayout->render(function($slot) { 
    switch($slot) {
    case "HEADERS":
        echo "<script type=\"text/javascript\">alert('hello')</script>";
        break;

    default:
        echo "TEMPLATE: $slot";
        break;
    }
} );

