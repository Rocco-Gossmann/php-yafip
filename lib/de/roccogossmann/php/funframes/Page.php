<?php namespace de\roccogossmann\php\funframes;

use de\roccogossmann\php\core\Utils;

class Page extends Component {


    public static function createFromLayout($sPath, $sComponentsPath) {

        $oI = new static();

        $oLayout = Layout::load($sPath);

        $aComponentTree = [];
        $aProcessList = [];
        $aProcessKeys = [];
        $aDataTokens = [];

        $aCaches = [];
        
        foreach($oLayout->getTokens() as $sKey) {
            $aComponentTree[$sKey] = [];
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
                        $aProcessList[$iIndex][$sTokenKey] = [];
                        $aProcessList[] = &$aProcessList[$iIndex][$sTokenKey];
                        $aProcessKeys[] = $sTokenKey;
                    }
                }
                else $aProcessList[$iIndex] = "empty";
            }
            else $aProcessList[$iIndex] = "";

            $iIndex++;
        }

        $oI->aComponentTree = $aComponentTree;
        $oI->aComponents = $aCaches;
        $oI->aFlattenedTree = array_filter(Utils::flattenArray($oI->aComponentTree), fn($e) => strcmp($e, 'empty'));

        print_r($oI);
        
        return $oI;
    }



    /** @var Array<string, mixed> */
    private $aData = [];

    /** @var array the list of components as they are arranged by the layout */
    private $aComponentTree = [];
    
    /** @var array a list of component instances */
    private $aComponents = [];

    /** @var array a 1 dimensional list of tokens, that can contain data 
     * A token, that does not have a component assotiated with it, is concidered a data-token
     */
    private $aDataTokens = [];

    /** @var array a 1 dimensional representation of all components an the Page */
    private $aFlattenedTree = [];

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
