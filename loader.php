<?php
$__autoload_classes=array (
  'rogoss\\core\\Utils' => 'lib/rogoss/core/Utils.php',
  'rogoss\\core\\APIResponse' => 'lib/rogoss/core/APIServer.php',
  'rogoss\\core\\APIBase' => 'lib/rogoss/core/APIServer.php',
  'rogoss\\core\\APIServer' => 'lib/rogoss/core/APIServer.php',
  'rogoss\\core\\APIClient' => 'lib/rogoss/core/APIClient.php',
  'rogoss\\core\\FetchResult' => 'lib/rogoss/core/APIClient.php',
  'rogoss\\core\\APIClientException' => 'lib/rogoss/core/APIClient.php',
  'rogoss\\core\\FileHandler' => 'lib/rogoss/core/FileHandler.php',
  'rogoss\\core\\FileHandlerException' => 'lib/rogoss/core/FileHandler.php',
  'rogoss\\core\\tSingleton' => 'lib/rogoss/core/tSingleton.php',
  'rogoss\\core\\Mailer' => 'lib/rogoss/core/Mailer.php',
  'PHPMailer\\PHPMailer\\Exception' => 'lib/rogoss/core/vendor/PHPMailer/src/Exception.php',
  'rogoss\\core\\OSSL' => 'lib/rogoss/core/OSSL.php',
  'rogoss\\core\\OSSLException' => 'lib/rogoss/core/OSSL.php',
  'rogoss\\core\\SaltSet' => 'lib/rogoss/core/SaltSet.php',
  'rogoss\\core\\SaltSetException' => 'lib/rogoss/core/SaltSet.php',
  'rogoss\\core\\tDebug' => 'lib/rogoss/core/tDebug.php',
  'rogoss\\yafip\\Page' => 'lib/rogoss/yafip/Page.php',
  'rogoss\\yafip\\PageException' => 'lib/rogoss/yafip/Page.php',
  'rogoss\\yafip\\Layout' => 'lib/rogoss/yafip/Layout.php',
  'rogoss\\yafip\\LayoutException' => 'lib/rogoss/yafip/Layout.php',
  'rogoss\\yafip\\ComponentChunk' => 'lib/rogoss/yafip/ComponentChunk.php',
);


spl_autoload_register( function($sClass) {
    global $__autoload_classes;

    if(isset($__autoload_classes[$sClass])) {
        require_once(__DIR__ . "/" . $__autoload_classes[$sClass]);
        return true;
    }

    return false;
} );
