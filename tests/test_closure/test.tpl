<ste:mktag name="initfoo">
	<ste:set var="x"><ste:tagcontent /></ste:set>
	<ste:mktag name="foo">
		<ste:calc>$x + <ste:tagcontent /></ste:calc>
	</ste:mktag>
</ste:mktag>
<ste:initfoo>10</ste:initfoo>
<ste:foo>20</ste:foo>