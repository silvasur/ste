<?php $transcompile_fx = function($ste)
{
	$outputstack = array('');
	$outputstack_i = 0;
	$outputstack[$outputstack_i] .= $ste->load("custom_tags.tpl");
	$outputstack[$outputstack_i] .= $ste->load("master.html");
	$blockname_4feb57c8eb53c0_39108239 = "content";
	$ste->blocks['4feb57c8eb54f8.57715029'] = array_pop($outputstack);
	$ste->blockorder[] = '4feb57c8eb54f8.57715029';
	$outputstack = array('');
	$outputstack_i = 0;
	$outputstack[$outputstack_i] .= "<h2>Some Articles</h2>\n\t";
	$parameters_4feb57c8eb55d1_49937129 = array();
	$parameters_4feb57c8eb55d1_49937129['array'] = "articles";
	$parameters_4feb57c8eb55d1_49937129['value'] = "article";
	$parameters_4feb57c8eb55d1_49937129['max'] = "3";
	$parameters_4feb57c8eb55d1_49937129['counter'] = "i";
	$outputstack[$outputstack_i] .= $ste->call_tag('foreach_limit', $parameters_4feb57c8eb55d1_49937129, function($ste)
	{
		$outputstack = array('');
		$outputstack_i = 0;
		$outputstack[$outputstack_i] .= "<h3>";
		$parameters_4feb57c8eb59c3_59434363 = array();
		$outputstack[$outputstack_i] .= $ste->call_tag('uppercase', $parameters_4feb57c8eb59c3_59434363, function($ste)
		{
			$outputstack = array('');
			$outputstack_i = 0;
			$parameters_4feb57c8eb5a98_71645476 = array();
			$outputstack[$outputstack_i] .= $ste->call_tag('escape', $parameters_4feb57c8eb5a98_71645476, function($ste)
			{
				$outputstack = array('');
				$outputstack_i = 0;
				$outputstack[$outputstack_i] .= @$ste->vars["article"]["title"];
				return array_pop($outputstack);
			});
			return array_pop($outputstack);
		});
		$outputstack[$outputstack_i] .= "</h3>\n\t\t<div class=\"author\">Author: ";
		$parameters_4feb57c8eb5f92_71020272 = array();
		$outputstack[$outputstack_i] .= $ste->call_tag('escape', $parameters_4feb57c8eb5f92_71020272, function($ste)
		{
			$outputstack = array('');
			$outputstack_i = 0;
			$outputstack[$outputstack_i] .= @$ste->vars["article"]["author"];
			return array_pop($outputstack);
		});
		$outputstack[$outputstack_i] .= "</div>\n\t\t<div class=\"date\">";
		$parameters_4feb57c8eb62b8_11002450 = array();
		$parameters_4feb57c8eb62b8_11002450['timestamp'] = @$ste->vars["article"]["timestamp"];
		$outputstack[$outputstack_i] .= $ste->call_tag('date', $parameters_4feb57c8eb62b8_11002450, function($ste)
		{
			$outputstack = array('');
			$outputstack_i = 0;
			$outputstack[$outputstack_i] .= "%d. %h. %Y, %H:%M:%S";
			return array_pop($outputstack);
		});
		$outputstack[$outputstack_i] .= "</div>\n\t\t<div class=\"article_content\">\n\t\t\t";
		$outputstack[] = "";
		$outputstack_i++;
		$outputstack[$outputstack_i] .= (($ste->get_var_by_name("i")) == ("0")) ? 'yes' : '';
		$outputstack_i--;
		if($ste->evalbool(array_pop($outputstack)))
		{
			$outputstack[$outputstack_i] .= "\n\t\t\t\t\t" . @$ste->vars["article"]["full"];
			
		}
		else
		{
			$outputstack[$outputstack_i] .= "\n\t\t\t\t\t" . @$ste->vars["article"]["excerpt"];
			
		}
		$outputstack[$outputstack_i] .= "</div>\n\t\t<hr />\n\t";
		return array_pop($outputstack);
	});
	$outputstack[] = '';
	$outputstack_i++;
	$parameters_4feb57c8eb72f3_12728934 = array();
	$parameters_4feb57c8eb72f3_12728934['array'] = "articles";
	$outputstack[$outputstack_i] .= $ste->call_tag('arraylen', $parameters_4feb57c8eb72f3_12728934, function($ste) { return ''; });
	$outputstack_i--;
	$ste->set_var_by_name("articles_n", array_pop($outputstack));
	$outputstack[] = "";
	$outputstack_i++;
	$outputstack[$outputstack_i] .= ((@$ste->vars["articles_n"]) > ("3")) ? 'yes' : '';
	$outputstack_i--;
	if($ste->evalbool(array_pop($outputstack)))
	{
		$outputstack[$outputstack_i] .= "<p>There are <a href=\"#\">more articles</a>.</p>\n\t\t";
		
	}
	$outputstack[$outputstack_i] .= "<h2>Some more useless demo stuff...</h2>\n\t\t<h3>Counting from 10 to 0...</h3>\n\t\t<p>but take only the even ones and multiply by 5...</p>\n\t\t";
	$forloop_4feb57c8eb7965_88913250_start = "10";
	$forloop_4feb57c8eb7965_88913250_stop = "0";
	$forloop_4feb57c8eb7965_88913250_countername = "i";
	for($forloop_4feb57c8eb7965_88913250_counter = $forloop_4feb57c8eb7965_88913250_start; $forloop_4feb57c8eb7965_88913250_counter >= $forloop_4feb57c8eb7965_88913250_stop; $forloop_4feb57c8eb7965_88913250_counter += -1)
		{
		try
		{
				$ste->set_var_by_name($forloop_4feb57c8eb7965_88913250_countername, $forloop_4feb57c8eb7965_88913250_counter);
				$outputstack[] = "";
				$outputstack_i++;
				$outputstack[] = '';
				$outputstack_i++;
				$outputstack[$outputstack_i] .= @$ste->vars["i"];
				$outputstack_i--;
				$tmp_even = array_pop($outputstack);
				$outputstack[$outputstack_i] .= (is_numeric($tmp_even) and ($tmp_even % 2 == 0)) ? 'yes' : '';
				$outputstack_i--;
				if($ste->evalbool(array_pop($outputstack)))
				{
					$outputstack[] = '';
					$outputstack_i++;
					$outputstack[$outputstack_i] .= @$ste->vars["i"] . " * 5";
					$outputstack_i--;
					$outputstack[$outputstack_i] .= $ste->calc(array_pop($outputstack));
					$outputstack[$outputstack_i] .= "<br />\n\t\t\t\t";
					
				}
				
		}
		catch(\ste\BreakException $e) { break; }
		catch(\ste\ContinueException $e) { continue; }
		
		}
		
	$outputstack[$outputstack_i] .= "<h3>Repeat some text...</h3>\n\t\t";
	$parameters_4feb57c8eb86f4_27000372 = array();
	$parameters_4feb57c8eb86f4_27000372['n'] = "10";
	$outputstack[$outputstack_i] .= $ste->call_tag('repeat', $parameters_4feb57c8eb86f4_27000372, function($ste)
	{
		$outputstack = array('');
		$outputstack_i = 0;
		$outputstack[$outputstack_i] .= "<p>Bla</p>\n\t\t";
		return array_pop($outputstack);
	});
	$outputstack[$outputstack_i] .= "<h3>Get a variable's content dynamically</h3>\n\t\t";
	$outputstack[$outputstack_i] .= $ste->get_var_by_name(@$ste->vars["foo"] . "[" . @$ste->vars["bar"] . "]");$outputstack[$outputstack_i] .= "<h3>We will call ste:repeat with a non-numerical value for n here to see the handling of a RuntimeError</h3>\n\t\t";
	$parameters_4feb57c8eb8be8_11772552 = array();
	$parameters_4feb57c8eb8be8_11772552['n'] = "lol";
	$outputstack[$outputstack_i] .= $ste->call_tag('repeat', $parameters_4feb57c8eb8be8_11772552, function($ste)
	{
		$outputstack = array('');
		$outputstack_i = 0;
		$outputstack[$outputstack_i] .= "<p>Bla</p>\n\t\t";
		return array_pop($outputstack);
	});
	$parameters_4feb57c8eb8e64_48510077 = array();
	$parameters_4feb57c8eb8e64_48510077['array'] = "hai";
	$parameters_4feb57c8eb8e64_48510077['delim'] = ",";
	$outputstack[$outputstack_i] .= $ste->call_tag('split', $parameters_4feb57c8eb8e64_48510077, function($ste)
	{
		$outputstack = array('');
		$outputstack_i = 0;
		$outputstack[$outputstack_i] .= "a,b,c,d,e";
		return array_pop($outputstack);
	});
	$parameters_4feb57c8eb91f2_76021324 = array();
	$parameters_4feb57c8eb91f2_76021324['array'] = "hai";
	$outputstack[$outputstack_i] .= $ste->call_tag('join', $parameters_4feb57c8eb91f2_76021324, function($ste)
	{
		$outputstack = array('');
		$outputstack_i = 0;
		$outputstack[$outputstack_i] .= "<br />";
		return array_pop($outputstack);
	});
	$ste->blocks[$blockname_4feb57c8eb53c0_39108239] = array_pop($outputstack);
	if(array_search($blockname_4feb57c8eb53c0_39108239, $ste->blockorder) === FALSE)
		$ste->blockorder[] = $blockname_4feb57c8eb53c0_39108239;
	$outputstack = array('');
	$outputstack_i = 0;
	return array_pop($outputstack);
}; ?>