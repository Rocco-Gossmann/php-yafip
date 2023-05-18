<?php
$__autoload_classes=array (
  'de\\roccogossmann\\php\\funframes\\Page' => 'lib/de/roccogossmann/php/funframes/Page.php',
  'de\\roccogossmann\\php\\funframes\\Layout' => 'lib/de/roccogossmann/php/funframes/Layout.php',
  'de\\roccogossmann\\php\\funframes\\LayoutException' => 'lib/de/roccogossmann/php/funframes/Layout.php',
);


spl_autoload_register( function($sClass) {
    global $__autoload_classes;

    if(isset($__autoload_classes[$sClass])) {
        require_once(__DIR__ . "/" . $__autoload_classes[$sClass]);
        return true;
    }

    return false;
} );
