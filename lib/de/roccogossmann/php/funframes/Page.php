<?php namespace de\roccogossmann\php\funframes;

use de\roccogossmann\php\core\Utils;

class ComponentChunk {

    /** @var ComponentChunk */
    public ComponentChunk $parent;

    /** @var ComponentChunk[] */
    public array  $components = [];

    /** @var string|null */
    public $data = null;

    protected $sLabel = "";

    public function __construct($sLabel) {
        $this->sLabel = $sLabel;
    }

    public function getLabel() {
        return (empty($this->parent) ? '' : $this->parent->getLabel() . ".") . $this->sLabel;
    }
}


class Page extends Component {

    public static function createFromLayout($sPath, $sComponentsPath) {

        $oI = new static();
        $oI->oLayout = $oLayout = Layout::load($sPath);

        $aComponentTree = [];

        /** @var ComponentChunk[] */ 
        $aProcessList = [];

        /** @var string[] */ 
        $aProcessKeys = [];

        $aCaches = [];

        $aDataTokens = [];
        $aComponentTokens = [];


        $aData = file_exists($sPath . "/data.php") ? include $sPath . "/data.php" : []; 
        if(!is_array($aData)) $aData = [];


        foreach($oLayout->getTokens() as $sKey) {
            $aComponentTree[$sKey] = new ComponentChunk($sKey);
            $aProcessList[] = &$aComponentTree[$sKey];
            $aProcessKeys[] = $sKey;
            $aData[$sKey] = [];
            $aDataProcess[] = &$aData[$sKey];
        }
        
        $iIndex = 0; 
        while($sKey = array_shift($aProcessKeys)) {

            if(file_exists($sComponentsPath . "/" . $sKey)) { 
                $oLayout = $aCaches[$sKey] ?? Layout::load($sComponentsPath . "/" . $sKey); 

                $aCompData = file_exists($sComponentsPath . "/" . $sKey . "/data.php") ? include $sComponentsPath . "/" . $sKey . "/data.php" : []; 
                if(!is_array($aCompData)) $aCompData = [];

                $aComponentTokens[] = &$aProcessList[$iIndex];

//                $aDataProcess[$iIndex] = &array_replace_recursive($aDataProcess[$iIndex], $aCompData);
                Utils::mutateArrayRecursive($aDataProcess[$iIndex], $aCompData);

                $aCaches[$sKey] = $oLayout;
                $aLayoutTokens = $oLayout->getTokens();
                if(count($aLayoutTokens)) {
                    foreach($aLayoutTokens as $sTokenKey) {
                        $aProcessList[$iIndex]->components[$sTokenKey] = new ComponentChunk($sTokenKey);
                        $aProcessList[$iIndex]->components[$sTokenKey]->parent = $aProcessList[$iIndex];
                        $aProcessList[] = &$aProcessList[$iIndex]->components[$sTokenKey];
                        $aProcessKeys[] = $sTokenKey;
                        $aDataProcess[] = &$aDataProcess[$iIndex][$sTokenKey];
                    }
                }
                
            }
            else {
                $aProcessList[$iIndex]->data = "";
                $aDataTokens[] = &$aProcessList[$iIndex];
            }

            $iIndex++;
        }

        $oI->aComponentTree = $aComponentTree;
        $oI->aLayouts = $aCaches;
        $oI->aData = Utils::flattenArray($aData);

        foreach($aDataTokens as &$oToken) {
            $oI->aDataTokens[$oToken->getLabel()]=&$oToken;
        }

        foreach($aComponentTokens as &$oToken) {
            $oI->aComponentTokens[$oToken->getLabel()]=&$oToken;
        }

        return $oI;
    }


    /** @var Array<string, mixed> */
    private $aData = [];

    /** @var array the list of components as they are arranged by the layout */
    private $aComponentTree = [];

    /** @var ComponentChunk[] - a list of all found Tokens, that are supposed to hold data */
    private $aDataTokens = [];
    
    /** @var ComponentChunk[] - a list of all found Tokens, that are supposed to hold data */
    private $aComponentTokens = [];

    /** @var array a list of component instances */
    private $aLayouts = [];

    /**
     * Loads data from a PHP-File 
     * @param string $sPHPFile the PHP-Include, that returns an array of data to set
     * @return static returns $this, because Builder-Pattern
     */
    public function loadData($sPHPInclude) {

        if(!file_exists($sPHPInclude)) {
            trigger_error("no such file '$sPHPInclude'", E_USER_WARNING);
            return $this;
        }
        
        $aData = include $sPHPInclude;
        if(empty($aData)  || !is_array($aData)) return $this;

        $aFlat = Utils::flattenArray($aData, );

        foreach($aFlat as $sKey=>$mData) 
            $this->aData[strtolower($sKey)] = $mData;

        return $this;
    }

    public function render($prefix="") {
        echo "<!DOCTYPE html>";
        //TODO: render styles and scripts
        parent::render();
    }

    public function getData($sPath) { return $this->aData[$sPath] ?? false; }
}

class PageException extends \Exception {} 
