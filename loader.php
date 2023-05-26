<?php
$__autoload_classes=array (
  'de\\roccogossmann\\php\\core\\Utils' => 'lib/de/roccogossmann/php/core/Utils.php',
  'de\\roccogossmann\\php\\core\\APIResponse' => 'lib/de/roccogossmann/php/core/APIServer.php',
  'de\\roccogossmann\\php\\core\\APIBase' => 'lib/de/roccogossmann/php/core/APIServer.php',
  'de\\roccogossmann\\php\\core\\APIServer' => 'lib/de/roccogossmann/php/core/APIServer.php',
  'de\\roccogossmann\\php\\core\\APIClient' => 'lib/de/roccogossmann/php/core/APIClient.php',
  'de\\roccogossmann\\php\\core\\FetchResult' => 'lib/de/roccogossmann/php/core/APIClient.php',
  'de\\roccogossmann\\php\\core\\APIClientException' => 'lib/de/roccogossmann/php/core/APIClient.php',
  'de\\roccogossmann\\php\\core\\FileHandler' => 'lib/de/roccogossmann/php/core/FileHandler.php',
  'de\\roccogossmann\\php\\core\\FileHandlerException' => 'lib/de/roccogossmann/php/core/FileHandler.php',
  'de\\roccogossmann\\php\\core\\tSingleton' => 'lib/de/roccogossmann/php/core/tSingleton.php',
  'de\\roccogossmann\\php\\core\\Mailer' => 'lib/de/roccogossmann/php/core/Mailer.php',
  'PHPMailer\\PHPMailer\\Exception' => 'lib/de/roccogossmann/php/core/vendor/PHPMailer/src/Exception.php',
  'de\\roccogossmann\\php\\core\\OSSL' => 'lib/de/roccogossmann/php/core/OSSL.php',
  'de\\roccogossmann\\php\\core\\OSSLException' => 'lib/de/roccogossmann/php/core/OSSL.php',
  'de\\roccogossmann\\php\\core\\SaltSet' => 'lib/de/roccogossmann/php/core/SaltSet.php',
  'de\\roccogossmann\\php\\core\\SaltSetException' => 'lib/de/roccogossmann/php/core/SaltSet.php',
  'de\\roccogossmann\\php\\core\\tDebug' => 'lib/de/roccogossmann/php/core/tDebug.php',
  'de\\roccogossmann\\php\\funframes\\Page' => 'lib/de/roccogossmann/php/funframes/Page.php',
  'de\\roccogossmann\\php\\funframes\\Layout' => 'lib/de/roccogossmann/php/funframes/Layout.php',
  'de\\roccogossmann\\php\\funframes\\LayoutException' => 'lib/de/roccogossmann/php/funframes/Layout.php',
  'de\\roccogossmann\\php\\funframes\\PageException' => 'lib/de/roccogossmann/php/funframes/Page.php',
  'de\\roccogossmann\\php\\funframes\\ComponentChunk' => 'lib/de/roccogossmann/php/funframes/ComponentChunk.php',
);


spl_autoload_register( function($sClass) {
    global $__autoload_classes;

    if(isset($__autoload_classes[$sClass])) {
        require_once(__DIR__ . "/" . $__autoload_classes[$sClass]);
        return true;
    }

    return false;
} );
