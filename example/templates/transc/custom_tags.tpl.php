<?php $transcompile_fx = function($ste)
{
	$outputstack = array('');
	$outputstack_i = 0;
	$outputstack[] = '';
	$outputstack_i++;
	$outputstack[$outputstack_i] .= "array|value|max";
	$outputstack_i--;
	$mandatory_params = explode('|', array_pop($outputstack));
	$tag_fx = function($ste, $params, $sub) use ($mandatory_params)
	{
		$outputstack = array(); $outputstack_i = 0;$ste->vars['_tag_parameters'] = $params;
		foreach($mandatory_params as $mp)
		{
			if(!isset($params[$mp]))
				throw new \ste\RuntimeError("$mp missing in <ste:" . "foreach_limit" . ">.");
		}$foreachloop_4f4e9064144651_57761022_arrayvar = @$ste->vars["_tag_parameters"]["array"];
		$foreachloop_4f4e9064144651_57761022_valuevar = @$ste->vars["_tag_parameters"]["value"];
		$foreachloop_4f4e9064144651_57761022_countervar = "i";
		$foreachloop_4f4e9064144651_57761022_array = $ste->get_var_by_name($foreachloop_4f4e9064144651_57761022_arrayvar);
		if(!is_array($foreachloop_4f4e9064144651_57761022_array))
			$foreachloop_4f4e9064144651_57761022_array = array();
		$foreachloop_4f4e9064144651_57761022_counter = -1;
		foreach($foreachloop_4f4e9064144651_57761022_array as $foreachloop_4f4e9064144651_57761022_key => $foreachloop_4f4e9064144651_57761022_value)
		{
		try
		{
				$foreachloop_4f4e9064144651_57761022_counter++;
				$ste->set_var_by_name($foreachloop_4f4e9064144651_57761022_countervar, $foreachloop_4f4e9064144651_57761022_counter);
				$ste->set_var_by_name($foreachloop_4f4e9064144651_57761022_valuevar, $foreachloop_4f4e9064144651_57761022_value);
				
				$outputstack[] = "";
				$outputstack_i++;
				$outputstack[$outputstack_i] .= (($ste->get_var_by_name("i")) >= ($ste->get_var_by_name("_tag_parameters[max]"))) ? 'yes' : '';
				$outputstack_i--;
				if($ste->evalbool(array_pop($outputstack)))
				{
					throw new \ste\BreakException();
					
				}
				$outputstack[] = "";
				$outputstack_i++;
				$outputstack[$outputstack_i] .= "\n\t\t\t" . @$ste->vars["_tag_parameters"]["counter"];
				$outputstack_i--;
				if($ste->evalbool(array_pop($outputstack)))
				{
					$outputstack[] = '';
					$outputstack_i++;
					$outputstack[$outputstack_i] .= @$ste->vars["i"];
					$outputstack_i--;
					$ste->set_var_by_name(@$ste->vars["_tag_parameters"]["counter"], array_pop($outputstack));
					
				}
				$outputstack[$outputstack_i] .= $sub($ste);
		}
		catch(\ste\BreakException $e) { break; }
		catch(\ste\ContinueException $e) { continue; }
		
		}
		
		return array_pop($outputstack);
	};
	$ste->register_tag("foreach_limit", $tag_fx);
	return array_pop($outputstack);
}; ?>