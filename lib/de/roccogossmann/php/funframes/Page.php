<?php namespace de\roccogossmann\php\funframes;

use de\roccogossmann\php\core\Utils;

class Page extends Component {

    /** @var Array<string, mixed> */
    private $aData = [];

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

    private function __constructor(){ 
        parent::__construct(); 
        $this->setRoot($this); 
    } 
}

class PageException extends \Exception {} 
