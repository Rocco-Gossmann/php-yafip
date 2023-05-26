<?php

namespace de\roccogossmann\php\funframes;

use de\roccogossmann\php\core\Utils;
use Exception;


/** @property string $label the label as defined in the page/component file */
class ComponentChunk
{

    /** @var ComponentChunk */
    public ComponentChunk $parent;

    /** @var ComponentChunk[] */
    public array  $components = [];

    /** @var string|null */
    public $data = null;

    /** @var Layout */
    public $layout = null;

    private $_sLabel = "";

    public function __construct($sLabel)
    {
        $this->_sLabel = $sLabel;
    }

    public function __set($sField, $mValue)
    {
        switch ($sField) {
            case "label":
                $this->_sLabel = $mValue;
        }
    }

    public function __get($sField)
    {
        switch ($sField) {
            case "label":
                return (empty($this->parent) ? '' : $this->parent->label . ".") . $this->_sLabel;
            default:

                if (isset($this->$sField)) return $this->$sField;
        }
    }

    public function isDataChunk()
    {
        return !empty($this->data);
    }
}

class Page
{

    public static function load($sPageName)
    {

        $sPathDocRoot = rtrim($_SERVER['DOCUMENT_ROOT'], "\\/") . "/";

        $sPagesPath     = $sPathDocRoot . rtrim(self::getEnvVar("DE_ROCCOGOSSMANN_PHP_FUNFRAMES_PAGESROOT")     , "\\/");
        $sComponentPath = $sPathDocRoot . rtrim(self::getEnvVar("DE_ROCCOGOSSMANN_PHP_FUNFRAMES_COMPONENTSROOT"), "\\/");

        $sPagePath     = $sPagesPath. "/" . rtrim($sPageName, "\\/");
        $sPageTemplate = $sPagePath . "/_template.ff.php";
        $sPageCompiled = $sPagePath . "/_page.ff.php";

        $oI = (file_exists($sPageCompiled))
            ? static::fromCompiled($sPageCompiled)
            : static::createFromLayout($sPagePath, $sComponentPath)
                ->compileTemplate($sPageTemplate)
                ->compilePage($sPageCompiled)
        ;

        if(!$oI->fresh) {
            $bSpoiled=false;
            foreach($oI->aLayouts as $oLayout) 
                if($oLayout->recompiled) {
                    $bSpoiled = true;
                    break;
                }
                
            if($bSpoiled) {
                $oI = static::createFromLayout($sPageName, $sComponentPath)
                    ->compileTemplate($sPageTemplate)
                    ->compilePage($sPageCompiled)
                ;
            }

        }

        return $oI;
    }


    /**
     * Generates a Page-Instances from an include file
     *
     * @param string $sFile the file, that can be included
     *
     * @return static
     */
    protected static function fromCompiled($sFile)
    {
        throw new Exception("todo implement");
    }

    protected static function createFromLayout($sPath, $sComponentsPath)
    {
        $oI = new static();
        $oI->oLayout = $oLayout = Layout::load($sPath);
        $oI->fresh = true;

        $aComponentTree = [];

        /** @var ComponentChunk[] */
        $aProcessList = [];

        /** @var string[] */
        $aProcessKeys = [];

        $aCaches = [];

        $aDataTokens = [];
        $aComponentTokens = [];

        $aDataFiles = [];

        $aData = file_exists($sPath . "/data.php") 
            ? include ($aDataFiles[] = $sPath . "/data.php")
            : []
        ;

        if (!is_array($aData)) $aData = [];

        foreach ($oLayout->getTokens() as $sKey) {
            $aComponentTree[$sKey] = new ComponentChunk($sKey);
            $aProcessList[] = &$aComponentTree[$sKey];
            $aProcessKeys[] = $sKey;
            $aData[$sKey] = [];
            $aDataProcess[] = &$aData[$sKey];
        }

        $iIndex = 0;
        while ($sKey = array_shift($aProcessKeys)) {

            if (file_exists($sComponentsPath . "/" . $sKey)) {
                $oLayout = $aCaches[$sKey] ?? Layout::load($sComponentsPath . "/" . $sKey);
                $aProcessList[$iIndex]->layout = $oLayout;

                $aCompData = file_exists($sComponentsPath . "/" . $sKey . "/data.php") 
                    ? include ($aDataFiles[] = $sComponentsPath . "/" . $sKey . "/data.php") 
                    : []
                ;

                if (!is_array($aCompData)) $aCompData = [];

                $aComponentTokens[] = &$aProcessList[$iIndex];

                // $aDataProcess[$iIndex] = &array_replace_recursive($aDataProcess[$iIndex], $aCompData);
                Utils::mutateArrayRecursive($aDataProcess[$iIndex], $aCompData);

                $aCaches[$sKey] = $oLayout;
                $aLayoutTokens = $oLayout->getTokens();
                if (count($aLayoutTokens)) {
                    foreach ($aLayoutTokens as $sTokenKey) {
                        $aProcessList[$iIndex]->components[$sTokenKey] = new ComponentChunk($sTokenKey, $oLayout);
                        $aProcessList[$iIndex]->components[$sTokenKey]->parent = $aProcessList[$iIndex];
                        $aProcessList[] = &$aProcessList[$iIndex]->components[$sTokenKey];
                        $aProcessKeys[] = $sTokenKey;
                        $aDataProcess[] = &$aDataProcess[$iIndex][$sTokenKey];
                    }
                }
            } else {
                $aProcessList[$iIndex]->data = "";
                $aDataTokens[] = &$aProcessList[$iIndex];
            }

            $iIndex++;
        }

        $oI->aComponentTree = $aComponentTree;
        $oI->aLayouts = $aCaches;
        $oI->aDataFiles = $aDataFiles;

        $oI->aData = Utils::flattenArray($aData);

        return $oI;
    }

    /** @var Layout */
    protected $oLayout = null;

    /** @var Layout[] a list of component instances */
    private $aLayouts = [];

    /** @var string[] */
    private $aDataFiles = [];

    /** @var bool defines, if the page has been compiled this cycle */
    private $fresh = false;

    /** @var Array<string, mixed> */
    private $aData = [];

    /** @var array the list of components as they are arranged by the layout */
    private $aComponentTree = [];



    protected function compilePage($sOutputFile) {

        $_data = [
              'mainlayout' => [ $this->oLayout->hash, $this->oLayout->filepath ]
            , 'sublayouts' => []
            , 'datafiles' => $this->aDataFiles 
        ];

        foreach($this->aLayouts as $oLayout) 
            $_data['sublayouts'][] = [ $oLayout->hash, $oLayout->filepath ];

        $hF = fopen($sOutputFile, "w");
        if(!$hF) throw PageException::noFile($sOutputFile);

        fwrite($hF, "<?php\n\n\$_data=");
        fwrite($hF, var_export($_data, true));
        fwrite($hF, ";");

        fclose($hF);

        return $this;

    }

    /**
     * Brings the Page and all of its components into a precached format.
     * 2 new files are created in the layouts directory "_template.ff.php" which is the combined prerender of all components
     *                                              and "_page.ff.php" which contains all the metadata, regarding the prerender
     *
     * @param string $sOutputFile the file, the content will be rendered to 
     *
     * @throws PageException - if no file is found
     * @throws Exception - if eanything goes wrong, while parsing the layout-tree
     *
     * @return static - returns $this, because builder-pattern
     */
    protected function compileTemplate($sOutputFile)
    {
        function recurse(Layout $oLayout, $hCacheFile, $me, &$aBranchRoot, $sSlot = "", $sPrefix = "")
        {
            foreach ($oLayout->chunks() as $aChunk) {
                switch ($aChunk['type']) {
                    case "raw":
                        fwrite($hCacheFile, $aChunk['text']);
                        break;

                    case "slot":
                        if (isset($aBranchRoot[$aChunk['slot']])) {
                            /** @var ComponentChunk */
                            $oChunk = $aBranchRoot[$aChunk['slot']];
                            if (empty($oChunk->layout)) fwrite($hCacheFile, "<?php printData('{$oChunk->label}'); ?>");
                            else                        recurse($oChunk->layout, $hCacheFile, $me, $oChunk->components, $aChunk['slot'], $sPrefix . "." . $sSlot);
                        }
                        break;
                }
            }
        }

        $hFile = fopen($sOutputFile . ".tmp", "w");
        if ($hFile) {
            try {
                recurse($this->oLayout, $hFile, $this, $this->aComponentTree);
            } catch (Exception $ex) {
                fclose($hFile);
                $hFile = null;
                unlink($sOutputFile . ".tmp");
                throw $ex;
            } finally {
                if ($hFile) {
                    fclose($hFile);
                    if (file_exists($sOutputFile)) unlink($sOutputFile);
                    rename($sOutputFile . ".tmp", $sOutputFile);
                    $hFile = null;
                }
            }
        } else throw PageException::noFile($sOutputFile);

        return $this;
    }

    public function render($prefix = "")
    {
        echo "<!DOCTYPE html>";
        //TODO: render styles and scripts
        //parent::render();
    }

    public function getData($sPath)
    {
        return $this->aData[$sPath] ?? false;
    }

    private static function getEnvVar($sVarName) {
        $sVar = getenv($sVarName) ?? null;
        if(empty($sVar)) throw PageException::noEnvVar($sVarName);
        return $sVar;
    }

}

class PageException extends \Exception
{
    const NO_FILE      = 1;
    const NO_EVNVAR    = 2;
    const MISSING_PAGE = 3;

    public static function noFile($sFileName)
    {
        return new static("missing file of failed to create '$sFileName'", static::NO_FILE);
    }

    public static function noEnvVar($sVarName)
    {
        return new static("Missing Environment-Varable '$sVarName' please define it first", static::NO_EVNVAR);
    }

    public static function missingPage($sFullPagePath) {
        return new static("The there is no page at the path '$sFullPagePath'", static::MISSING_PAGE); 
    }
}
