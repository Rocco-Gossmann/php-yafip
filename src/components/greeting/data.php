<?php

    return [

        "formula" => function($sKey, $mPrev) {
            $iTime = date("G");
            $sRet = "Hello";
    
            if($iTime > 17) $sRet = "Good evening";
            elseif($iTime > 12) $sRet = "Good day";
            elseif($iTime > 0) $sRet = "Good morning";

            return $sRet;
        },


        "username" => function($sKey, $mPrev) {     // this field was also defined in `pages/index/data.php` 
            return empty($mPrev) ? "Sir" : $mPrev;  // but by defining it again in the components data, we can
        }                                           // overwrite the existing definition 
                                                    //
                                                    // the data definition of the component always takes priority 
                                                    // over the definitions, that the parent component/page makes
                                                    // the priority of definition goes:   currents field definition 
                                                    //                                  > parent components field definition 
                                                    //                                  > parents parent field definition
                                                    //                                  > parents parent parent ...
                                                    //                                  > ...
                                                    //                                  > page field defintion  
                                                    //
                                                    // however, by using the $mPrev parameter (which is the parents value for the field)
                                                    // we can also turn it around and make the components definition act
                                                    // as a fallback instead.
    ];
