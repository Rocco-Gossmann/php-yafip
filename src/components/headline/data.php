<?php
    return [

        'welcome_msg' => [

            "username" => function($sKey, $mPrevValue) { 
                return rand(true, false) ? $mPrevValue : "You";  
            } // <-- If a value is callable, it will be called and is given the value, 
              //     that the field had before, as well as, the full component-path that it is used in
              //
              //     If you want the value to not change, you can return either `null` or `$mPrevValue`
        ],
    ];
