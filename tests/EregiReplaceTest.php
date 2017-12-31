<?php

namespace Rabus\EregShim;

use PHPUnit\Framework\TestCase;

class EregiReplaceTest extends TestCase
{
    private $oldErrorReporting;

    protected function setUp()
    {
        $this->oldErrorReporting = \error_reporting(E_ALL & ~E_DEPRECATED);
    }

    protected function tearDown()
    {
        \error_reporting($this->oldErrorReporting);
    }

    public function testMixedCaseReplacement()
    {
        $this->assertSame(
            'UPPERCASE_characterS, lowercase_characters, MIxED CaSe_characterS',
            \eregi_replace('([[:lower:]]+) word', '\\1_character', 'UPPERCASE WORDS, lowercase words, MIxED CaSe woRdS')
        );
    }

    /**
     * @dataProvider provideMatchingTestCases
     */
    public function testReplacement($pattern, $match, $expectedResult)
    {
        $this->assertSame(
            $expectedResult,
            \eregi_replace($pattern, '[this is a replacement]', $match . ' this contains some matches ' . $match)
        );
    }

    public function provideMatchingTestCases()
    {
        return array(
            array(
                '..(a|b|c)(a|b|c)..',
                '--- ab ---',
                '--[this is a replacement]-- this contains some matches --[this is a replacement]--'
            ),
            array(
                '()',
                '',
                '[this is a replacement] [this is a replacement]t[this is a replacement]h[this is a replacement]i[this is a replacement]s[this is a replacement] [this is a replacement]c[this is a replacement]o[this is a replacement]n[this is a replacement]t[this is a replacement]a[this is a replacement]i[this is a replacement]n[this is a replacement]s[this is a replacement] [this is a replacement]s[this is a replacement]o[this is a replacement]m[this is a replacement]e[this is a replacement] [this is a replacement]m[this is a replacement]a[this is a replacement]t[this is a replacement]c[this is a replacement]h[this is a replacement]e[this is a replacement]s[this is a replacement] [this is a replacement]'
            ),
            array(
                '()',
                'abcdef',
                '[this is a replacement]a[this is a replacement]b[this is a replacement]c[this is a replacement]d[this is a replacement]e[this is a replacement]f[this is a replacement] [this is a replacement]t[this is a replacement]h[this is a replacement]i[this is a replacement]s[this is a replacement] [this is a replacement]c[this is a replacement]o[this is a replacement]n[this is a replacement]t[this is a replacement]a[this is a replacement]i[this is a replacement]n[this is a replacement]s[this is a replacement] [this is a replacement]s[this is a replacement]o[this is a replacement]m[this is a replacement]e[this is a replacement] [this is a replacement]m[this is a replacement]a[this is a replacement]t[this is a replacement]c[this is a replacement]h[this is a replacement]e[this is a replacement]s[this is a replacement] [this is a replacement]a[this is a replacement]b[this is a replacement]c[this is a replacement]d[this is a replacement]e[this is a replacement]f[this is a replacement]'
            ),
            array(
                '[x]|[^x]',
                'abcdef',
                '[this is a replacement][this is a replacement][this is a replacement][this is a replacement][this is a replacement][this is a replacement][this is a replacement][this is a replacement][this is a replacement][this is a replacement][this is a replacement][this is a replacement][this is a replacement][this is a replacement][this is a replacement][this is a replacement][this is a replacement][this is a replacement][this is a replacement][this is a replacement][this is a replacement][this is a replacement][this is a replacement][this is a replacement][this is a replacement][this is a replacement][this is a replacement][this is a replacement][this is a replacement][this is a replacement][this is a replacement][this is a replacement][this is a replacement][this is a replacement][this is a replacement][this is a replacement][this is a replacement][this is a replacement][this is a replacement][this is a replacement]'
            ),
            array(
                '(a{1})(a{1,}) (b{1,3}) (c+) (d?ddd|e)',
                '--- aaa bbb ccc ddd ---',
                '--- [this is a replacement] --- this contains some matches --- [this is a replacement] ---'
            ),
            array(
                '\\\\\`\^\.\[\$\(\)\|\*\+\?\{\\\'',
                '\\`^.[$()|*+?{\'',
                '[this is a replacement] this contains some matches [this is a replacement]'
            ),
            // FIXME
            // array(
            //     '\\a',
            //     'a',
            //     '[this is a replacement] this cont[this is a replacement]ins some m[this is a replacement]tches [this is a replacement]'
            // ),
            array(
                '[0-9][^0-9]',
                '2a',
                '[this is a replacement] this contains some matches [this is a replacement]'
            ),
            array(
                '^[[:alnum:]]{62,62}$',
                '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
                '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ this contains some matches 0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'
            ),
            array(
                '^[[:digit:]]{5}',
                '0123456789',
                '[this is a replacement]56789 this contains some matches 0123456789'
            ),
            array(
                '[[:digit:]]{5}$',
                '0123456789',
                '0123456789 this contains some matches 01234[this is a replacement]'
            ),
            array(
                '[[:blank:]]{1,10}',
                "\n \t",
                '
[this is a replacement]this[this is a replacement]contains[this is a replacement]some[this is a replacement]matches[this is a replacement]
[this is a replacement]'
            ),
            array(
                '[[:print:]]{3}',
                " a ",
                '[this is a replacement][this is a replacement][this is a replacement][this is a replacement][this is a replacement][this is a replacement][this is a replacement][this is a replacement][this is a replacement][this is a replacement][this is a replacement] '
            ),
        );
    }

    /**
     * @dataProvider provideNonMatchingTestCases
     */
    public function testNoReplacement($pattern, $string)
    {
        $this->assertSame($string, \eregi_replace($pattern, 'r', $string));
    }

    public function provideNonMatchingTestCases()
    {
        return array(
            array('[A-Z]', '0'),
            array('(a){4}', 'aaa'),
            array('^a', 'ba'),
            array('b$', 'ba'),
            // FIXME
            // array('[:alpha:]', 'x'),
        );
    }
}
