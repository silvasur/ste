<?php $transcompile_fx = function($ste)
{/*Array
(
    [0] => ste\TextNode Object
        (
            [text] => <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-us" lang="en">
<head>
	<title>
            [tpl] => master.html
            [offset] => 0
        )

    [1] => ste\TagNode Object
        (
            [name] => if
            [params] => Array
                (
                )

            [sub] => Array
                (
                    [0] => ste\VariableNode Object
                        (
                            [name] => title
                            [arrayfields] => Array
                                (
                                )

                            [tpl] => master.html
                            [offset] => 206
                        )

                    [1] => ste\TagNode Object
                        (
                            [name] => then
                            [params] => Array
                                (
                                )

                            [sub] => Array
                                (
                                    [0] => ste\VariableNode Object
                                        (
                                            [name] => title
                                            [arrayfields] => Array
                                                (
                                                )

                                            [tpl] => master.html
                                            [offset] => 222
                                        )

                                    [1] => ste\TextNode Object
                                        (
                                            [text] =>  - example
                                            [tpl] => master.html
                                            [offset] => 227
                                        )

                                )

                            [tpl] => master.html
                            [offset] => 211
                        )

                    [2] => ste\TagNode Object
                        (
                            [name] => else
                            [params] => Array
                                (
                                )

                            [sub] => Array
                                (
                                    [0] => ste\TextNode Object
                                        (
                                            [text] => example
                                            [tpl] => master.html
                                            [offset] => 258
                                        )

                                )

                            [tpl] => master.html
                            [offset] => 248
                        )

                )

            [tpl] => master.html
            [offset] => 197
        )

    [2] => ste\TextNode Object
        (
            [text] => </title>
            [tpl] => master.html
            [offset] => 285
        )

    [3] => ste\TextNode Object
        (
            [text] => <style type="text/css">
		* {
			font-family: sans-serif;
		}
		.online {
			color: #0a0;
		}
		.offline {
			color: #555;
			font-style: italic;
			
		}
	</style>
	
</head>
<body>
	<h1>example</h1>
	
	<div id="content">
		
            [tpl] => master.html
            [offset] => 384
        )

    [4] => ste\TagNode Object
        (
            [name] => block
            [params] => Array
                (
                    [name] => Array
                        (
                            [0] => ste\TextNode Object
                                (
                                    [text] => content
                                    [tpl] => master.html
                                    [offset] => 626
                                )

                        )

                )

            [sub] => Array
                (
                    [0] => ste\TextNode Object
                        (
                            [text] => Default content.
		
                            [tpl] => master.html
                            [offset] => 635
                        )

                )

            [tpl] => master.html
            [offset] => 609
        )

    [5] => ste\TextNode Object
        (
            [text] => </div>
	<div id="otherstuff">
		
            [tpl] => master.html
            [offset] => 670
        )

    [6] => ste\TagNode Object
        (
            [name] => block
            [params] => Array
                (
                    [name] => Array
                        (
                            [0] => ste\TextNode Object
                                (
                                    [text] => otherstuff
                                    [tpl] => master.html
                                    [offset] => 721
                                )

                        )

                )

            [sub] => Array
                (
                    [0] => ste\TextNode Object
                        (
                            [text] => <h2>List of users</h2>
			
                            [tpl] => master.html
                            [offset] => 733
                        )

                    [1] => ste\TextNode Object
                        (
                            [text] => <ul>
				
                            [tpl] => master.html
                            [offset] => 820
                        )

                    [2] => ste\TagNode Object
                        (
                            [name] => foreach
                            [params] => Array
                                (
                                    [array] => Array
                                        (
                                            [0] => ste\TextNode Object
                                                (
                                                    [text] => users
                                                    [tpl] => master.html
                                                    [offset] => 853
                                                )

                                        )

                                    [value] => Array
                                        (
                                            [0] => ste\TextNode Object
                                                (
                                                    [text] => user
                                                    [tpl] => master.html
                                                    [offset] => 867
                                                )

                                        )

                                )

                            [sub] => Array
                                (
                                    [0] => ste\TextNode Object
                                        (
                                            [text] => <li class="
                                            [tpl] => master.html
                                            [offset] => 873
                                        )

                                    [1] => ste\TagNode Object
                                        (
                                            [name] => if
                                            [params] => Array
                                                (
                                                )

                                            [sub] => Array
                                                (
                                                    [0] => ste\VariableNode Object
                                                        (
                                                            [name] => user
                                                            [arrayfields] => Array
                                                                (
                                                                    [0] => Array
                                                                        (
                                                                            [0] => ste\TextNode Object
                                                                                (
                                                                                    [text] => online
                                                                                    [tpl] => master.html
                                                                                    [offset] => 904
                                                                                )

                                                                        )

                                                                )

                                                            [tpl] => master.html
                                                            [offset] => 899
                                                        )

                                                    [1] => ste\TagNode Object
                                                        (
                                                            [name] => then
                                                            [params] => Array
                                                                (
                                                                )

                                                            [sub] => Array
                                                                (
                                                                    [0] => ste\TextNode Object
                                                                        (
                                                                            [text] => online
                                                                            [tpl] => master.html
                                                                            [offset] => 921
                                                                        )

                                                                )

                                                            [tpl] => master.html
                                                            [offset] => 911
                                                        )

                                                    [2] => ste\TagNode Object
                                                        (
                                                            [name] => else
                                                            [params] => Array
                                                                (
                                                                )

                                                            [sub] => Array
                                                                (
                                                                    [0] => ste\TextNode Object
                                                                        (
                                                                            [text] => offline
                                                                            [tpl] => master.html
                                                                            [offset] => 948
                                                                        )

                                                                )

                                                            [tpl] => master.html
                                                            [offset] => 938
                                                        )

                                                )

                                            [tpl] => master.html
                                            [offset] => 890
                                        )

                                    [2] => ste\TextNode Object
                                        (
                                            [text] => ">
                                            [tpl] => master.html
                                            [offset] => 975
                                        )

                                    [3] => ste\VariableNode Object
                                        (
                                            [name] => user
                                            [arrayfields] => Array
                                                (
                                                    [0] => Array
                                                        (
                                                            [0] => ste\TextNode Object
                                                                (
                                                                    [text] => name
                                                                    [tpl] => master.html
                                                                    [offset] => 983
                                                                )

                                                        )

                                                )

                                            [tpl] => master.html
                                            [offset] => 978
                                        )

                                    [4] => ste\TextNode Object
                                        (
                                            [text] =>  (
                                            [tpl] => master.html
                                            [offset] => 988
                                        )

                                    [5] => ste\VariableNode Object
                                        (
                                            [name] => user
                                            [arrayfields] => Array
                                                (
                                                    [0] => Array
                                                        (
                                                            [0] => ste\TextNode Object
                                                                (
                                                                    [text] => username
                                                                    [tpl] => master.html
                                                                    [offset] => 996
                                                                )

                                                        )

                                                )

                                            [tpl] => master.html
                                            [offset] => 991
                                        )

                                    [6] => ste\TextNode Object
                                        (
                                            [text] => )</li>
				
                                            [tpl] => master.html
                                            [offset] => 1005
                                        )

                                )

                            [tpl] => master.html
                            [offset] => 833
                        )

                    [3] => ste\TextNode Object
                        (
                            [text] => </ul>
		
                            [tpl] => master.html
                            [offset] => 1030
                        )

                )

            [tpl] => master.html
            [offset] => 704
        )

    [7] => ste\TextNode Object
        (
            [text] => </div>
</body>
</html>

            [tpl] => master.html
            [offset] => 1054
        )

)
*/
	$outputstack = array('');
	$outputstack_i = 0;
	$outputstack[$outputstack_i] .= "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\"\n\t\"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en-us\" lang=\"en\">\n<head>\n\t<title>";
	$outputstack[] = "";
	$outputstack_i++;
	$outputstack[$outputstack_i] .= @$ste->vars["title"];
	$outputstack_i--;
	if($ste->evalbool(array_pop($outputstack)))
	{
		$outputstack[$outputstack_i] .= @$ste->vars["title"] . " - example";
		
	}
	else
	{
		$outputstack[$outputstack_i] .= "example";
		
	}
	$outputstack[$outputstack_i] .= "</title>" . "<style type=\"text/css\">\n\t\t* {\n\t\t\tfont-family: sans-serif;\n\t\t}\n\t\t.online {\n\t\t\tcolor: #0a0;\n\t\t}\n\t\t.offline {\n\t\t\tcolor: #555;\n\t\t\tfont-style: italic;\n\t\t\t\n\t\t}\n\t</style>\n\t\n</head>\n<body>\n\t<h1>example</h1>\n\t\n\t<div id=\"content\">\n\t\t";
	$blockname_4f4e94ddd41342_39491438 = "content";
	$ste->blocks['4f4e94ddd41470.10353359'] = array_pop($outputstack);
	$ste->blockorder[] = '4f4e94ddd41470.10353359';
	$outputstack = array('');
	$outputstack_i = 0;
	$outputstack[$outputstack_i] .= "Default content.\n\t\t";
	$ste->blocks[$blockname_4f4e94ddd41342_39491438] = array_pop($outputstack);
	if(array_search($blockname_4f4e94ddd41342_39491438, $ste->blockorder) === FALSE)
		$ste->blockorder[] = $blockname_4f4e94ddd41342_39491438;
	$outputstack = array('');
	$outputstack_i = 0;
	$outputstack[$outputstack_i] .= "</div>\n\t<div id=\"otherstuff\">\n\t\t";
	$blockname_4f4e94ddd415e6_09352068 = "otherstuff";
	$ste->blocks['4f4e94ddd416b2.98760934'] = array_pop($outputstack);
	$ste->blockorder[] = '4f4e94ddd416b2.98760934';
	$outputstack = array('');
	$outputstack_i = 0;
	$outputstack[$outputstack_i] .= "<h2>List of users</h2>\n\t\t\t" . "<ul>\n\t\t\t\t";
	$foreachloop_4f4e94ddd417b7_50245786_arrayvar = "users";
	$foreachloop_4f4e94ddd417b7_50245786_valuevar = "user";
	$foreachloop_4f4e94ddd417b7_50245786_array = $ste->get_var_by_name($foreachloop_4f4e94ddd417b7_50245786_arrayvar);
	if(!is_array($foreachloop_4f4e94ddd417b7_50245786_array))
		$foreachloop_4f4e94ddd417b7_50245786_array = array();
	foreach($foreachloop_4f4e94ddd417b7_50245786_array as $foreachloop_4f4e94ddd417b7_50245786_key => $foreachloop_4f4e94ddd417b7_50245786_value)
	{
	try
	{
			$ste->set_var_by_name($foreachloop_4f4e94ddd417b7_50245786_valuevar, $foreachloop_4f4e94ddd417b7_50245786_value);
			
			$outputstack[$outputstack_i] .= "<li class=\"";
			$outputstack[] = "";
			$outputstack_i++;
			$outputstack[$outputstack_i] .= @$ste->vars["user"]["online"];
			$outputstack_i--;
			if($ste->evalbool(array_pop($outputstack)))
			{
				$outputstack[$outputstack_i] .= "online";
				
			}
			else
			{
				$outputstack[$outputstack_i] .= "offline";
				
			}
			$outputstack[$outputstack_i] .= "\">" . @$ste->vars["user"]["name"] . " (" . @$ste->vars["user"]["username"] . ")</li>\n\t\t\t\t";
			
	}
	catch(\ste\BreakException $e) { break; }
	catch(\ste\ContinueException $e) { continue; }
	
	}
	
	$outputstack[$outputstack_i] .= "</ul>\n\t\t";
	$ste->blocks[$blockname_4f4e94ddd415e6_09352068] = array_pop($outputstack);
	if(array_search($blockname_4f4e94ddd415e6_09352068, $ste->blockorder) === FALSE)
		$ste->blockorder[] = $blockname_4f4e94ddd415e6_09352068;
	$outputstack = array('');
	$outputstack_i = 0;
	$outputstack[$outputstack_i] .= "</div>\n</body>\n</html>\n";
	return array_pop($outputstack);
}; ?>