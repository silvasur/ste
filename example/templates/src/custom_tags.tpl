<ste:comment>A foreach that will break after 'max' iterations</ste:comment>
<ste:mktag name="foreach_limit" mandatory="array|value|max">
	<ste:foreach array="$_tag_parameters[array]" value="$_tag_parameters[value]" counter="i">
		<ste:if>
			<ste:cmp var_a="i" op="gte" var_b="_tag_parameters[max]" />
			<ste:then>
				<ste:break />
			</ste:then>
		</ste:if>

		<ste:if>
			$_tag_parameters[counter]
			<ste:then>
				<ste:set var="$_tag_parameters[counter]">$i</ste:set>
			</ste:then>
		</ste:if>
		<ste:tagcontent />
	</ste:foreach>
</ste:mktag>
