<ul>
    <ste:foreach array="data" value="x">
        <li?{$x[foo]| class="foo"|}>$x[content]</li>
    </ste:foreach>
</ul>