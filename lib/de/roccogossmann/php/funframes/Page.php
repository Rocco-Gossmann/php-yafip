<?php namespace de\roccogossmann\php\funframes;

use de\roccogossmann\php\core\Utils;

class ComponentChunk {
    /** @var ComponentChunk[] */
    public array  $components = [];

    /** @var string|null */
    public $data = null;
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
        
        foreach($oLayout->getTokens() as $sKey) {
            $aComponentTree[$sKey] = new ComponentChunk();
            $aProcessList[] = &$aComponentTree[$sKey];
            $aProcessKeys[] = $sKey;
        }
        
        $iIndex = 0; 
        while($sKey = array_shift($aProcessKeys)) {

            if(file_exists($sComponentsPath . "/" . $sKey)) { 
                $oLayout = $aCaches[$sKey] ?? Layout::load($sComponentsPath . "/" . $sKey); 
                $aCaches[$sKey] = $oLayout;
                $aLayoutTokens = $oLayout->getTokens();
                if(count($aLayoutTokens)) {
                    foreach($aLayoutTokens as $sTokenKey) {
                        $aProcessList[$iIndex]->components[$sTokenKey] = new ComponentChunk();
                        $aProcessList[] = &$aProcessList[$iIndex]->components[$sTokenKey];
                        $aProcessKeys[] = $sTokenKey;
                    }
                }
            }
            else $aProcessList[$iIndex]->data = "";

            $iIndex++;
        }

        $oI->aComponentTree = $aComponentTree;
        $oI->aLayouts = $aCaches;

        return $oI;
    }



    /** @var Array<string, mixed> */
    private $aData = [];

    /** @var array the list of components as they are arranged by the layout */
    private $aComponentTree = [];
    
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
