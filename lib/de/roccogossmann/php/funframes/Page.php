<?php namespace de\roccogossmann\php\funframes;

class Page {


    public static function CreateNew($layoutFile, $contentFile, $dataFile) {
        $oI = new static();

        $oI->layoutFile = $layoutFile;
        $oI->contentFile = $contentFile;
        $oI->dataFile = $dataFile;

        return $oI;
    }



    private function __constructor(){}

}
