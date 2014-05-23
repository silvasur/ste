<ste:mktag name="fac" mandatory="n">
	<ste:if>
		~{$_tag_parameters[n]|eq|0}
		<ste:then>1</ste:then>
		<ste:else>
			<ste:set var="nextn"><ste:calc>$_tag_parameters[n] - 1</ste:calc></ste:set>
			<ste:calc><ste:fac n="$nextn" /> * $_tag_parameters[n]</ste:calc>
		</ste:else>
	</ste:if>
</ste:mktag>
<ste:fac n="10" />