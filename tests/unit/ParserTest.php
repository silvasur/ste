<?php


namespace tests\unit;

use PHPUnit\Framework\TestCase;
use r7r\ste\Parser;
use r7r\ste\TextNode;
use r7r\ste\VariableNode;
use r7r\ste\TagNode;
use r7r\ste\ParseCompileError;

class ParserTest extends TestCase
{
    /**
     * @dataProvider successfulParsingDataProvider
     */
    public function testSuccessfulParsing(string $source, array $expected)
    {
        $actual = Parser::parse($source, '-');

        self::assertEquals($expected, $actual);
    }

    public function successfulParsingDataProvider()
    {
        return [
            ['', []],

            ['Hello', [
                new TextNode('-', 0, 'Hello'),
            ]],

            ['$foo', [
                new VariableNode('-', 0, 'foo', []),
            ]],

            //01234567890
            ['foo$bar$baz', [
                new TextNode('-', 0, 'foo'),
                new VariableNode('-', 3, 'bar', []),
                new VariableNode('-', 7, 'baz', []),
            ]],

            //012345678
            ['${foo}bar', [
                new VariableNode('-', 0, 'foo', []),
                new TextNode('-', 6, 'bar'),
            ]],

            //012345678
            ['$foo[bar]', [
                new VariableNode('-', 0, 'foo', [
                    [new TextNode('-', 5, 'bar')],
                ]),
            ]],

            //01234567890
            ['${foo[bar]}', [
                new VariableNode('-', 0, 'foo', [
                    [new TextNode('-', 6, 'bar')],
                ]),
            ]],

            //012345678
            ['$foo[$bar]', [
                new VariableNode('-', 0, 'foo', [
                    [new VariableNode('-', 5, 'bar', [])],
                ]),
            ]],

            //012345678901234
            ['$foo[$bar[baz]]', [
                new VariableNode('-', 0, 'foo', [
                    [new VariableNode('-', 5, 'bar', [
                        [new TextNode('-', 10, 'baz')],
                    ])],
                ]),
            ]],

            //012345678901234
            ['$foo[$bar][baz]', [
                new VariableNode('-', 0, 'foo', [
                    [new VariableNode('-', 5, 'bar', [])],
                    [new TextNode('-', 11, 'baz')]
                ]),
            ]],

            //0123456789012345678901
            ['a${b[c$d[e${f}g]][h]}i', [
                new TextNode('-', 0, 'a'),
                new VariableNode('-', 1, 'b', [
                    [
                        new TextNode('-', 5, 'c'),
                        new VariableNode('-', 6, 'd', [
                            [
                                new TextNode('-', 9, 'e'),
                                new VariableNode('-', 10, 'f', []),
                                new TextNode('-', 14, 'g')
                            ]
                        ])
                    ],
                    [new TextNode('-', 18, 'h')],
                ]),
                new TextNode('-', 21, 'i'),
            ]],

            ['<ste:foo />', [
                new TagNode('-', 0, 'foo'),
            ]],

            ['<ste:foo></ste:foo>', [
                new TagNode('-', 0, 'foo'),
            ]],

            //0123456789012345678901
            ['<ste:foo>bar</ste:foo>', [
                new TagNode('-', 0, 'foo', [], [
                    new TextNode('-', 9, 'bar'),
                ]),
            ]],

            //0         1         2         3         4         5         6         7         8         9         0
            //012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234
            ['<ste:foo a="$b[0][$x]" c="\"" d="${e}f"><ste:foo><ste:xyz>abc</ste:xyz><ste:foo />$x</ste:foo></ste:foo>x', [
                new TagNode('-', 0, 'foo', [
                    'a' => [
                        new VariableNode('-', 12, 'b', [
                            [new TextNode('-', 15, '0')],
                            [new VariableNode('-', 18, 'x', [])],
                        ]),
                    ],
                    'c' => [
                        new TextNode('-', 26, '"'),
                    ],
                    'd' => [
                        new VariableNode('-', 33, 'e', []),
                        new TextNode('-', 37, 'f'),
                    ]
                ], [
                    new TagNode('-', 40, 'foo', [], [
                        new TagNode('-', 49, 'xyz', [], [
                            new TextNode('-', 58, 'abc'),
                        ]),
                        new TagNode('-', 71, 'foo'),
                        new VariableNode('-', 82, 'x', []),
                    ]),
                ]),
                new TextNode('-', 104, 'x'),
            ]],

            //0         1         2         3
            //01234567890123456789012345678901234567
            ['foo?{~{$x|eq|\}$y}|b|<ste:foo/>\}}$bar', [
                new TextNode('-', 0, 'foo'),
                new TagNode('-', 3, 'if', [], [
                    new TagNode('-', 5, 'cmp', [
                        'text_a' => [new VariableNode('-', 7, 'x', [])],
                        'op' => [new TextNode('-', 10, 'eq')],
                        'text_b' => [
                            new TextNode('-', 13, '}'),
                            new VariableNode('-', 15, 'y', []),
                        ],
                    ], []),
                    new TagNode('-', 3, 'then', [], [
                        new TextNode('-', 19, 'b'),
                    ]),
                    new TagNode('-', 3, 'else', [], [
                        new TagNode('-', 21, 'foo'),
                        new TextNode('-', 31, '}'),
                    ]),
                ]),
                new VariableNode('-', 34, 'bar', []),
            ]],

            ['<ste:comment>ignored</ste:comment>', []],

            ['foo<ste:comment>ignored</ste:comment>bar', [
                // These are not two TextNodes as the parser will collapse adjacent TextNodes
                new TextNode('-', 0, 'foobar'),
            ]],

            ['<ste:rawtext><ste:foo a="bla">$abc</ste:foo></ste:rawtext>', [
                new TextNode('-', 0, '<ste:foo a="bla">$abc</ste:foo>'),
            ]],

            ['<ste:rawtext><ste:foo a="bla"><ste:rawtext>$abc</ste:foo></ste:rawtext>', [
                new TextNode('-', 0, '<ste:foo a="bla"><ste:rawtext>$abc</ste:foo>'),
            ]],
        ];
    }

    /**
     * @dataProvider failsToParseDataProvider
     * @param string $input
     */
    public function testFailsToParse(string $input)
    {
        self::expectException(ParseCompileError::class);

        Parser::parse($input, '-');
    }

    public function failsToParseDataProvider()
    {
        return [
            // Incomplete tag
            ['<ste:foo'],
            ['<ste:'],
            ['<ste:>'],
            ['<ste:foo /'],

            // Incomplete closing tag
            ['<ste:foo>bar</'],
            ['<ste:foo>bar</ste'],
            ['<ste:foo>bar</ste:'],
            ['<ste:foo>bar</ste:foo'],
            ['<ste:foo></'],
            ['<ste:foo></ste'],
            ['<ste:foo></ste:'],
            ['<ste:foo></ste:foo'],

            // Missing parameter value
            ['<ste:foo bar= />'],

            // Unclosed parameter
            ['<ste:foo bar=" />'],
            ['<ste:foo bar="baz />'],

            // Unclosed tag
            ['<ste:foo>bar'],

            // Trailing closing tag
            ['</ste:foo>'],
            ['abc</ste:foo>'],
            ['<ste:foo></ste:foo></ste:foo>'],

            // Open/close tag mismatch
            ['<ste:foo>bar</ste:baz>'],

            // Nesting error
            ['<ste:foo><ste:bar></ste:foo></ste:bar>'],

            // Invalid parameter name
            ['<ste:foo $bar />'],

            // Unclosed variable
            ['${foo'],
            ['${${foo}'],

            // Unclosed array
            ['$foo[bar'],

            // Incomplete variable
            ['$'],
            ['foo$'],
            ['foo${}'],

            // Incomplete shorthands
            ['?{foo|bar}'],
            ['?{foo|bar|baz'],
            ['?{foo|'],
            ['?{foo}'],
            ['?{'],
            ['~{foo|bar}'],
            ['~{foo|bar|baz'],
            ['~{foo|'],
            ['~{foo}'],
            ['~{'],

            // Unclosing pseudotags
            ['<ste:comment>foo'],
            ['<ste:rawtext>foo'],
        ];
    }
}
