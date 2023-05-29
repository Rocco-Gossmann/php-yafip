<?php
    return [
//==============================================================================
// Static Data
//==============================================================================
        "greeting.username" => "Visitor",   // <-- This specific field contains static data                                            //     but it woll also be overwritten by the greetings component
                                            //     
                                            //     the "." in the fieldname/key shows, that 
                                            //     this data belongs to a child component, called "greeting"
                                            //     
                                            //     You can try to remove the field, to see what happens with
                                            //     the rendered page 
                                            //
                                            //     checkout      
                                            //     `/src/components/greeting/data.php` for more info


//==============================================================================
// Data-Functions
//==============================================================================
        "version" => function($sKey, $mPrev) {      // Datafields can also be functions. Then, they are executed
            return getenv("ROGOSS_YAFIP_VERSION");  // When ever a Page is loaded
        },


        "title" => function($sKey, $mPrev) {            // Function Data fields can also return a Callable
                                                        // The Callable will be exectuted, when ever the field is renderd
            return function($sShort="", $sBold="") {   // You can pass arguments to that callable by adding a ":" to your datafields  
                if(!empty($sBold)) echo "<b>";          // for example [[--title:short--]]


                switch($sShort) {
                    case 'short': echo "YaFiP"; break;
                    default: echo "YaFiP (Yet another Framework in PHP)";
                }

                if(!empty($sBold)) echo "</b>";
            };
        },


//==============================================================================
// Data-Generators
//==============================================================================
        "generatorcnt" => function ($sKey, $mPrevValue) { // <-- Datafields can also be Generators,
                            // they are helpfull, if you want to keep track of data regarding the field itself 
            $rendercount = 0;
            $aArgs=[];     // the generator is invoked via the Generator::send method.
                           // Therefore each, yield will also return the array of arguments.
                           // Simililar to the function for the "title" field.

            while(true) {
                $sData = empty($_ = $aArgs[0]) ? "[ no data ]" : $_; // <- process data recievied by yield

                $aArgs =  yield (//<- Yield out the processed data \/ and also wait for the next call 
                                 //    >  then pass data of next call back to $aArgs
                        "I have been rendered " . $rendercount++ . " time(s) => {$sData}"
                );
            }

            //    
            // since we are using send, the very first call has already been made in the background
            // without its contents being processed
        },     
    ];
