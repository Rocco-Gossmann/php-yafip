<?php

namespace de\roccogossmann\php\funframes;

use de\roccogossmann\php\core\Utils;

class Component {

    public static function createFromLayout($sPath, $sComponentsPath) {

        $oI = new static();

        $oI->oLayout = Layout::load($sPath);

        $aComponentTree = [];
        $aProcessList = [];
        $aProcessKeys = [];

        $aCaches = [];
        
        foreach($oI->oLayout->getTokens() as $sKey) {
            $aComponentTree[$sKey] = [];
            $aProcessList[] = &$aComponentTree[$sKey];
            $aProcessKeys[] = $sKey;
        }
        
        $iIndex = 0; 
        while($sKey = array_shift($aProcessKeys)) {

            $aList = $aProcessList[$iIndex];
 
            if(file_exists($sComponentsPath . "/" . $sKey)) { 
                $oLayout = $aCaches[$sKey] ?? Layout::load($sComponentsPath . "/" . $sKey); 
                $aCaches[$sKey] = $oLayout;

                foreach($oLayout->getTokens() as $sTokenKey) {
                    $aProcessList[$iIndex][$sTokenKey] = [];
                    $aProcessList[] = &$aList[$sTokenKey];
                    $aProcessKeys[] = $sTokenKey;
                }
            }

            $iIndex++;
        }

        $oI->aComponentTree = $aComponentTree;

        var_dump($oI->aComponentTree);

        return $oI;
    }


//==============================================================================
// Class - Methods
//==============================================================================

    /** @var Layout */
    private $oLayout = null;

    /** @var Page */
    private $oTreeRoot = null;

    private $aComponentTree = [];
    private $aFlattenedTree = [];

    /**
     * @param string $sLabel the label under which to assign that child to the layout
     * @param Component $oComponent
     *
     * @throws ComponentException if a label is already in use within this component
     *
     * @return static $this because builder pattern.
     */
    public function setChildCompnent($sLabel, Component $oComponent) {
#       $sLabel = strtolower(trim($sLabel));
#       if (isset($this->aCompontents[$sLabel]))
#           throw new ComponentException("label '{$sLabel}' already assigned", ComponentException::LABEL_EXISTS);

#       $this->aCompontents[$sLabel] = $oComponent;
#       if (!empty($this->oTreeRoot))
#           $oComponent->setRoot($oTreeRoot);

        return $this;
    }

    /**
     * registers a Component to be rendered
     *
     * @param string $sLabel the placeholder, under which the component is identfied in the layout (not case sensitive)
     * @param Component $oComponent the component to register
     *
     * @throws PageException - PageException::LABEL_EXISTS in case the label is already assigned
     *
     * @return static == $this because builder pattern.
     */
    public function renderComponent($sLabel, $sMode = "html", $sPrefix = "") {
#       $sLabel = strtolower(trim($sLabel));

#       if (isset($this->aCompontents[$sLabel]))
#           echo $this->aCompontents[$sLabel]->render($sPrefix);

#       elseif ($this->oTreeRoot and !empty($tmp = $this->oTreeRoot->getData($sPrefix)))
#           echo $sMode == 'html'
#               ? htmlentities($tmp)
#               : $tmp;
    }

    public function setRoot(Page &$oPage) {
        if ($this->oTreeRoot !== null and $this->oTreeRoot !== $oPage)
            throw new ComponentException("component already has a root", ComponentException::ROOT_SET);

        $this->oTreeRoot = $oPage;

#       foreach ($this->aCompontents as &$oComponent)
#           $oComponent->setRoot($oPage);
    }

    public function render($sPrefix = "") {
        $this->oLayout->render([$this, "renderComponent"], $sPrefix);
    }

    protected function __construct() { }
}


class ComponentException extends \Exception {
    const LABEL_EXISTS = 1;
    const ROOT_SET = 2;
}
