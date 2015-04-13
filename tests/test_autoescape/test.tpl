outer 1: $test
escaped outer 1: <ste:escape>$test</ste:escape>
double escaped outer 1: <ste:escape><ste:escape>$test</ste:escape></ste:escape>
raw outer 1: <ste:raw>$test</ste:raw>
arg outer 1: <ste:echoarg echo="$test" />
<ste:autoescape mode="none">
nested 1: $test
escaped nested 1: <ste:escape>$test</ste:escape>
double escaped nested 1: <ste:escape><ste:escape>$test</ste:escape></ste:escape>
raw nested 1: <ste:raw>$test</ste:raw>
arg nested 1: <ste:echoarg echo="$test" />
<ste:autoescape mode="html">
innermost: $test
escaped innermost: <ste:escape>$test</ste:escape>
double escaped innermost: <ste:escape><ste:escape>$test</ste:escape></ste:escape>
raw innermost: <ste:raw>$test</ste:raw>
arg innermost: <ste:echoarg echo="$test" />
</ste:autoescape>
nested 2: $test
escaped nested 2: <ste:escape>$test</ste:escape>
double escaped nested 2: <ste:escape><ste:escape>$test</ste:escape></ste:escape>
raw nested 2: <ste:raw>$test</ste:raw>
arg nested 2: <ste:echoarg echo="$test" />
</ste:autoescape>
outer 2: $test
escaped outer 2: <ste:escape>$test</ste:escape>
double escaped outer 2: <ste:escape><ste:escape>$test</ste:escape></ste:escape>
raw outer 2: <ste:raw>$test</ste:raw>
arg outer 2: <ste:echoarg echo="$test" />
