<?php namespace de\roccogossmann\php\funframes;

class Layout {
        
    /**
     * create a new Template out of a Raw HTML File
     *
     * @param string $sDefinitionFile the name of that will become the template
     *
     * @return static returns a valid Layout Instance
     */
    public static function buildNew($sRawTemplateFile) {
        $oI = new static();
        
        $oI->sRawFile = $sRawTemplateFile;
        $oI->sFileHash = hash_file("sha256", $sRawTemplateFile);

        $hF = fopen($sRawTemplateFile, "r");
        if($hF) {

            $aChunks = [];

            $iReadHead = 0;
            $iLastChunkStart = 0;
            $sLast4 = "    ";
            $sTPL = "";

            $bKeepReading = true;

            $mode = 0;

            while($bKeepReading) {

                if(feof($hF)) {

                    switch($mode) {
                        case 0: $aChunks[] = ["raw", $iLastChunkStart, ($iReadHead-4) - $iLastChunkStart]; break;
                        case 1: $aChunks[] = ["tpl", $sTPL]; break;
                    }

                    $bKeepReading=false;

                }
                else {

                    $chr = fread($hF, 1);
                    $sLast4 = substr($sLast4, 1).$chr;
                    $iReadHead++;

                    switch($mode) {
                    case 0:
                        if($sLast4 === "[[--") {
                            $aChunks[] = ["raw", $iLastChunkStart, ($iReadHead-4) - $iLastChunkStart];
                            $iLastChunkStart = $iReadHead;
                            $sTPL = "";
                            $mode = 1;
                        }
                        break;

                    case 1:
                        if($sLast4 === "--]]") {
                            $aChunks[] = ["tpl", substr($sTPL, 0, -3)];
                            $iLastChunkStart = $iReadHead;
                            $mode = 0;
                        }
                        else $sTPL.=$chr;
                        break;

                    }
                }
            }

            $oI->aChunks = $aChunks;

            fclose($hF);
        } else throw new LayoutException("failed to open '$sRawTemplateFile'", LayoutException::FAILED_TO_OPEN_FILE);

        return $oI;
    }



    private $sRawFile = "";
    private $sFileHash = "";
    private $aChunks = [];


    /**
     * Load an already defined Template file
     *
     * @param string $sDefinitionFile the path to the template defintion file created by Layout::Create(...)->compile(...);
     *
     * @return static A valid Layout - Instance
     */
    public static function load($sDefinitionFile) {

        $sRawFile = $sDefinitionFile.".html";
        $sDefinitionFile = $sDefinitionFile.".ff.php";

        $oI = new static();
        $bRecompile = false;
        if(file_exists($sDefinitionFile)) {
            include $sDefinitionFile;

            if(!isset($hash) or !isset($file) or !isset($chunks)) 
                throw new LayoutException("failed to open '$sDefinitionFileName' => does not fullfill expected format", LayoutException::FAILED_TO_OPEN_FILE);
             
            if($hash === hash_file("sha256", $file)) {
                $oI->sRawFile = $file;
                $oI->sFileHash = $hash;
                $oI->aChunks = $chunks;

                return $oI;
            }
            else $bRecompile = true;        
        }
        else $bRecompile=true;


        if($bRecompile) {
            $oI = static::buildNew($sRawFile);
            $oI->compile($sDefinitionFile);
            return $oI;
        }

    }


    public function compile($sDefinitionFileName) {
        $hF = fopen($sDefinitionFileName, "w");
        if(!$hF)  throw new LayoutException("failed to open '$sDefinitionFileName'", LayoutException::FAILED_TO_OPEN_FILE);

        fwrite($hF, "<?php\n \$file=");
        fwrite($hF, var_export($this->sRawFile, true));
        fwrite($hF, ";\n\n \$hash=");
        fwrite($hF, var_export($this->sFileHash, true));
        fwrite($hF, ";\n\n \$chunks=");
        fwrite($hF, var_export($this->aChunks, true));
        fwrite($hF, ";\n");

        fclose($hF);
    }

    public function fillTemplate($sTemplate) {
        echo "Template: " . strtolower($sTemplate);
    }

    public function render() {
        if(empty($this->aChunks)) return;

        $hF = fopen($this->sRawFile, "r");
        if($hF) {

            foreach($this->aChunks as $aChunk) {
                switch($aChunk[0]) {
                case 'raw':
                    if((int)$aChunk[2] > 0) {
                        fseek($hF, $aChunk[1]);
                        echo fread($hF, $aChunk[2]);
                    }
                    break;
                case 'tpl':
                    $this->fillTemplate($aChunk[1]);
                    break;
                }
            }
    
            fclose($hF);

        } else throw new LayoutException("failed to open '$sRawTemplateFile'", LayoutException::FAILED_TO_OPEN_FILE);

    }


    private function __constructor(){}
}

class LayoutException extends \Exception {
    const FAILED_TO_OPEN_FILE = 1;
}
