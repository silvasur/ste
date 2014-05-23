<ste:mktag name="double"><ste:calc>2 * <ste:tagcontent /></ste:calc></ste:mktag>
<ste:mktag name="foo" mandatory="a|b">
	<ste:for counter="i" start="0" stop="$_tag_parameters[a]">
		<ste:if>
			<ste:even>$i</ste:even>
			<ste:then><ste:double>$i</ste:double></ste:then>
			<ste:else>$_tag_parameters[b]</ste:else>
		</ste:if>
	</ste:for>
</ste:mktag>
<ste:foo a="10" b="-" />