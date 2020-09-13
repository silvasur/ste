<ste:mktag name="inittag" mandatory="innername">
	<ste:set var="x"><ste:tagcontent /></ste:set>
	<ste:mktag name="$_tag_parameters[innername]">
		<ste:calc>$x + <ste:tagcontent /></ste:calc>
	</ste:mktag>
</ste:mktag>
<ste:inittag innername="foo">10</ste:inittag>
<ste:inittag innername="bar">20</ste:inittag>
<ste:foo>30</ste:foo>
<ste:bar>40</ste:bar>