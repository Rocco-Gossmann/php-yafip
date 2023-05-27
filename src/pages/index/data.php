<?php
    return [

        "headline.welcome_msg.username" => "Visitor",   // <-- this field will be overwritten by 
                                                        //     `/src/components/headline/data.php`
                                                        //     in case `/src/components/headline/data.php` or one of its children 
                                                        //     does not redefine the field, this value is used
                                                        //
                                                        //     the data definition of the component always takes priority 
                                                        //     over the definitions, that the parent component/page makes
                                                        //     the priority of definition goes:    currents field definition 
                                                        //                                       > parent components field definition 
                                                        //                                       > parents parent field definition
                                                        //                                       > parents parent parent ...
                                                        //                                       > ...
                                                        //                                       > page field defintion  
                                                      

        "headline.title" => function ($sKey, $mPrevValue) {     // <-- Datafields can also contain callables,
                                                                //     these are executed during load / buildtime 
           // return "FunFrames v1.1";                          //     every place using the same component / placeholder
                                                                //     will then show the value returned by the function
                                                                
            return function() {                 // <-- but this callable can also return another callable.
                echo "FunFrames !!!<br /><small>"     //     the returned callable will run, whenever the field is to be rendered
                   , rand(true, false)          //     there is no return here. everything echoed / printed 
                        ? "aren't they fun?"    //     lands directly in the output
                        : "so much fun"
                   , "</small>";
            };

        },     
    ];
