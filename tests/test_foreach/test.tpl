<ste:foreach array="foo" key="k" value="v" counter="i">
	$i: $k -> $v[a], $v[b]
</ste:foreach>
---
<ste:foreach array="bar" value="v">
	$v
	<ste:else>--empty--</ste:else>
</ste:foreach>
