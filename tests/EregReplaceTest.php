<?php

namespace Rabus\EregShim;

use PHPUnit\Framework\TestCase;

class EregReplaceTest extends TestCase
{
    private $oldErrorReporting;

    /**
     * @before
     */
    protected function setupErrorReporting()
    {
        $this->oldErrorReporting = \error_reporting(E_ALL & ~E_DEPRECATED);
    }

    /**
     * @after
     */
    protected function restoreErrorReporting()
    {
        \error_reporting($this->oldErrorReporting);
    }

    public function testSimpleReplace()
    {
        $this->assertSame('abcdef', \ereg_replace('123', 'def', 'abc123'));
    }

    public function testReplaceWithEmptyString()
    {
        $this->assertSame('abc', \ereg_replace('123', '', 'abc123'));
    }

    public function testReplaceWithSingeQuote()
    {
        $this->assertSame("'test", \ereg_replace("\\\\'", "'", "\\'test"));
    }

    public function testReplaceAtStartOfLine()
    {
        $this->assertSame(
            'That is a nice and simple string',
            \ereg_replace('^This', 'That', 'This is a nice and simple string')
        );
    }

    public function testEmptyResult()
    {
        $this->assertSame('', \ereg_replace('abcd', '', 'abcd'));
    }

    public function testBackReferences()
    {
        $this->assertSame(
            "123 abc +-|=\n",
            \ereg_replace("([a-z]*)([-=+|]*)([0-9]+)", "\\3 \\1 \\2\n", "abc+-|=123")
        );
    }

    public function testLongBackReference()
    {
        $this->assertSame(
            'abc2222222222def2222222222',
            \ereg_replace('1(2*)3', '\\1def\\1', 'abc122222222223')
        );
    }

    public function testZeroBackreference()
    {
        $this->assertSame(
            'abcdef123ghi',
            \ereg_replace('123', 'def\\0ghi', 'abc123')
        );
    }

// FIXME
//    public function testNonExistingBackreference()
//    {
//        $this->assertSame(
//            'abcdef\\1ghi',
//            \ereg_replace("123", 'def\\1ghi', 'abc123')
//        );
//    }

// FIXME
//    public function testEscapesInReplaceString()
//    {
//        $this->assertSame(
//            'abcdef\\g\\\\hi\\',
//            \ereg_replace('123', 'def\\g\\\\hi\\', 'abc123')
//        );
//    }

    public function testBackreferencesNotReplacedRecursively()
    {
        $this->assertSame(
            '\\2',
            \ereg_replace('a(.*)b(.*)c', '\\1', 'a\\2bxc')
        );
    }

    public function testReplaceEmptyMatches()
    {
        $this->assertSame(
            'zabc123',
            \ereg_replace('^', 'z', 'abc123')
        );
    }

    public function testBackslashHandlingInRegularExpressions()
    {
        $this->assertSame(
            'abc123abc',
            \ereg_replace('\\?', 'abc', '?123?')
        );
    }

    /**
     * @dataProvider provideMatchingTestCases
     */
    public function testReplacement($pattern, $match, $expectedResult)
    {
        $this->assertSame(
            $expectedResult,
            \ereg_replace($pattern, '[this is a replacement]', $match . ' this contains some matches ' . $match)
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
                "\n[this is a replacement]this[this is a replacement]contains[this is a replacement]some[this is a replacement]matches[this is a replacement]\n[this is a replacement]"
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
        $this->assertSame($string, \ereg_replace($pattern, 'r', $string));
    }

    public function provideNonMatchingTestCases()
    {
        return array(
            array('A', 'a'),
            array('[A-Z]', '0'),
            array('(a){4}', 'aaa'),
            array('^a', 'ba'),
            array('b$', 'ba'),
            // FIXME
            // array('[:alpha:]', 'x'),
        );
    }
}
