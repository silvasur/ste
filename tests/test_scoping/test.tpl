<ste:set var="a">A</ste:set>
<ste:set var="b">B</ste:set>
<ste:mktag name="foo">
	in foo: \$a = $a
	in foo: \$b = $b
	in foo: \$c = $c
	<ste:set var="a">X</ste:set>
	<ste:setlocal var="b">Y</ste:setlocal>
	<ste:set var="c">Z</ste:set>
	in foo (after set): \$a = $a
	in foo (after set): \$b = $b
	in foo (after set): \$c = $c
</ste:mktag>

<ste:foo />
\$a = $a
\$b = $b
\$c = $c