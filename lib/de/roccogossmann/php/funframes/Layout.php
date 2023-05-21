<?php namespace de\roccogossmann\php\funframes;

class Layout {
        
    /**
     * create a new Template out of a Raw HTML File
     *
     * @param string $sDefinitionFile the name of that will become the template
     *
     * @return static returns a valid Layout Instance
     */
    public static function create($sRawTemplateFile) {
        $oI = new static();
        
        $oI->sRawFile = $sRawTemplateFile;
        $oI->sFileHash = hash_file("sha256", $sRawTemplateFile);

        $hF = fopen($sRawTemplateFile, "r");
        if($hF) {

            $aChunks = [];
            $aTokens = [];

            $iReadHead = 0;
            $iLastChunkStart = 0;
            $sLast4 = "    ";
            $sTPL = "";

            $bKeepReading = true;

            $mode = 0;

            while($bKeepReading) {

                if(feof($hF)) {

                    $sTPL = substr($sTPL, 0, -3);

                    if(strpos($sTPL, ".") !== false) 
                        throw new LayoutException("tokens cant contain '.'", LayoutException::PARSE_ERROR);

                    switch($mode) {
                        case 0: $aChunks[] = ["raw", $iLastChunkStart, $iReadHead-$iLastChunkStart+2]; break;
                        case 1: $aChunks[] = ["tpl", $sTPL]  ;$aTokens[$sTPL] = $sTPL  ; break;
                        case 2: $aChunks[] = ["dyn", $sTPL]  ;$aTokens[$sTPL] = $sTPL  ; break;
                        case 3: $aChunks[] = ["html", $sTPL] ;$aTokens[$sTPL] = $sTPL  ; break;
                        case 4: $aChunks[] = ["dhtm", $sTPL] ;$aTokens[$sTPL] = $sTPL  ; break;
                    }

                    $bKeepReading=false;
                }
                else {

                    $chr = fread($hF, 1);
                    $sLast4 = substr($sLast4, 1).$chr;
                    $iReadHead++;

                    switch($mode) {
                    case 0:
                        switch($sLast4) {
                        case "[[--": $mode = 1;
                        case "{{--": if($sLast4 === "{{--") $mode = 2;
                        case "[@--": if($sLast4 === "[@--") $mode = 3;
                        case "{@--": if($sLast4 === "{@--") $mode = 4;
                            $aChunks[] = ["raw", $iLastChunkStart, ($iReadHead-4) - $iLastChunkStart];
                            $iLastChunkStart = $iReadHead;
                            $sTPL = "";
                        }
                        break;

                    case 1: $sTPLType = "tpl"; $sTPLClose = "--]]";
                    case 2: if($mode == 2) { $sTPLType = "dyn"; $sTPLClose = "--}}"; }
                    case 3: if($mode == 3) { $sTPLType = "html"; $sTPLClose = "--]]"; }
                    case 4: if($mode == 4) { $sTPLType = "dhtm"; $sTPLClose = "--}}"; }
                        if($sLast4 === $sTPLClose) {

                            $sTPL = substr($sTPL, 0, -3);
                            if(strpos($sTPL, ".") !== false) 
                                throw new LayoutException("tokens cant contain '.'", LayoutException::PARSE_ERROR);

                            $aChunks[] = [$sTPLType, ];
                            $aTokens[$sTPL] = $sTPL;
                            $iLastChunkStart = $iReadHead;
                            $mode = 0;
                        }
                        else $sTPL.=$chr;
                        break;
                    }
                }
            }

            $oI->aChunks = $aChunks;
            $oI->aTokens = $aTokens;

            fclose($hF);
        } else throw new LayoutException("failed to open '$sRawTemplateFile'", LayoutException::FAILED_TO_OPEN_FILE);

        return $oI;
    }

    private $sRawFile = "";
    private $sFileHash = "";
    private $aChunks = [];
    private $aTokens = [];


    /**
     * Load an already defined Template file
     *
     * @param string $sDefinitionFile the path to the template defintion file created by Layout::Create(...)->compile(...);
     *
     * @return static A valid Layout - Instance
     */
    public static function load($sDefinitionFile) {

        $sRawFile = $sDefinitionFile."/layout.html";
        $sDefinitionFile = $sDefinitionFile."/layout.ff.php";

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
                $oI->aTokens = $tokens;

                return $oI;
            }
            else $bRecompile = true;        
        }
        else $bRecompile=true;

        if($bRecompile) {
            $oI = static::create($sRawFile);
            $oI->compile($sDefinitionFile);
            return $oI;
        }
    }

    public function compile($sDefinitionFileName) {
        $hF = fopen($sDefinitionFileName, "w");
        if(!$hF)  throw new LayoutException("failed to open '$sDefinitionFileName'", LayoutException::FAILED_TO_OPEN_FILE);

        fwrite($hF, "<?php\n \$file="); fwrite($hF, var_export($this->sRawFile, true));
        fwrite($hF, ";\n\n \$hash=");   fwrite($hF, var_export($this->sFileHash, true));
        fwrite($hF, ";\n\n \$chunks="); fwrite($hF, var_export($this->aChunks, true));
        fwrite($hF, ";\n\n \$tokens="); fwrite($hF, var_export($this->aTokens, true));
        fwrite($hF, ";\n");

        fclose($hF);
    }

    public function getTokens() { return $this->aTokens; }

    public function render($slotCallback, $sPrefix="") {
        if(empty($this->aChunks)) return;

        $myPrefix = empty($sPrefix) ? "" : $sPrefix."."; 

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
                case 'html':
                    call_user_func($slotCallback, $aChunk[1], $aChunk[0] == 'html' ? 'raw' : 'html', $myPrefix . strtolower($aChunk[1]));
                    break;

                case 'dyn':
                case 'dhtm':
                    echo "<span data-phpff=\"", htmlspecialchars($myPrefix . strtolower($aChunk[1])), "\">";
                    call_user_func(
                        $slotCallback, $aChunk[1], 
                        $aChunk[0] == 'dhtm' ? "raw" : 'html', 
                        $myPrefix . strtolower($aChunk[1]));
                    echo "</span>";
                    break;

                }
            }
    
            fclose($hF);

        } else throw new LayoutException("failed to open '{$$this->sRawFile}'", LayoutException::FAILED_TO_OPEN_FILE);
    }

    private function __constructor(){}
}

class LayoutException extends \Exception {
    const FAILED_TO_OPEN_FILE = 1;
    const PARSE_ERROR = 2;
}
