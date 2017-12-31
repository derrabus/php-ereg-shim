<?php

namespace Rabus\EregShim;

use PHPUnit\Framework\TestCase;

class SplitiTest extends TestCase
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

    /**
     * @dataProvider provideMixedCaseTestCases
     */
    public function testMixedCaseSpliti($pattern, $string)
    {
        $this->assertSame(array('--- ', ' ---'), \spliti($pattern, $string));
    }

    public function provideMixedCaseTestCases()
    {
        return array(
            array('[a-z]', '--- A ---'),
            array('[A-Z]', '--- a ---'),
            array('[[:lower:]]', '--- A ---'),
            array('[[:upper:]]', '--- a ---'),
        );
    }

    /**
     * @dataProvider provideLimitTestCases
     */
    public function testSplitiWithLimit($pattern, $string, $expectedResult)
    {
        $this->assertSame(
            $expectedResult,
            \spliti($pattern, $string . ' |1| ' . $string . ' |2| ' . $string, 2)
        );
    }

    public function provideLimitTestCases()
    {
        return array(
            array('..(a|b|c)(a|b|c)..', '--- ab ---', array('--', '-- |1| --- ab --- |2| --- ab ---')),
            array('[x]|[^x]', 'abcdef', array('', 'bcdef |1| abcdef |2| abcdef')),
            array(
                '(a{1})(a{1,}) (b{1,3}) (c+) (d?ddd|e)',
                '--- aaa bbb ccc ddd ---',
                array('--- ', ' --- |1| --- aaa bbb ccc ddd --- |2| --- aaa bbb ccc ddd ---')
            ),
            array(
                '\\\\\`\^\.\[\$\(\)\|\*\+\?\{\\\'',
                '\\`^.[$()|*+?{\'',
                array('', ' |1| \`^.[$()|*+?{\' |2| \`^.[$()|*+?{\'')
            ),
            // FIXME
            // array('\\a', 'a', array('', ' |1| a |2| a')),
            array('[0-9][^0-9]', '2a', array('', ' |1| 2a |2| 2a')),
            array(
                '^[[:alnum:]]{62,62}$',
                '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
                array('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ |1| 0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ |2| 0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')
            ),
            array('^[[:digit:]]{5}', '0123456789', array('', '56789 |1| 0123456789 |2| 0123456789')),
            array('[[:digit:]]{5}$', '0123456789', array('0123456789 |1| 0123456789 |2| 01234', '')),
            array('[[:blank:]]{1,10}', "\n \t", array("\n", "|1| \n \t |2| \n \t")),
            array('[[:print:]]{3}', " a ", array('', ' |1|  a  |2|  a ')),
        );
    }

    /**
     * @dataProvider provideNoLimitTestCases
     */
    public function testSplitiWithoutLimit($pattern, $string, $expectedResult)
    {
        $this->assertSame(
            $expectedResult,
            \spliti($pattern, $string . ' |1| ' . $string . ' |2| ' . $string)
        );
    }

    public function provideNoLimitTestCases()
    {
        return array(
            array(
                '..(a|b|c)(a|b|c)..',
                '--- ab ---',
                array('--', '-- |1| --', '-- |2| --', '--')
            ),
            array(
                '[x]|[^x]',
                'abcdef',
                array('', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '')),
            array(
                '(a{1})(a{1,}) (b{1,3}) (c+) (d?ddd|e)',
                '--- aaa bbb ccc ddd ---',
                array('--- ', ' --- |1| --- ', ' --- |2| --- ', ' ---')
            ),
            array(
                '\\\\\`\^\.\[\$\(\)\|\*\+\?\{\\\'',
                '\\`^.[$()|*+?{\'',
                array('', ' |1| ', ' |2| ', '')
            ),
            // FIXME
            // array(
            //    '\\a',
            //    'a',
            //    array('', ' |1| ', ' |2| ', '')
            // ),
            array(
                '[0-9][^0-9]',
                '2a',
                array('', ' |', ' ', ' |', ' ', '')
            ),
            array(
                '^[[:alnum:]]{62,62}$',
                '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
                array('0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ |1| 0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ |2| 0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')
            ),
            // FIXME
            // array(
            //     '^[[:digit:]]{5}',
            //     '0123456789',
            //     array('', '', ' |1| 0123456789 |2| 0123456789')
            // ),
            array(
                '[[:digit:]]{5}$',
                '0123456789',
                array('0123456789 |1| 0123456789 |2| 01234', '')
            ),
            array(
                '[[:blank:]]{1,10}',
                "\n \t",
                array("\n", '|1|', "\n", '|2|', "\n", '')
            ),
            array(
                '[[:print:]]{3}',
                " a ",
                array('', '', '', '', '', '', ' ')
            ),
        );
    }

    /**
     * @dataProvider provideNonMatchingTestCases
     */
    public function testNonMatchingSpliti($pattern, $string)
    {
        $this->assertSame(array($string), \split($pattern, $string));
    }

    public function provideNonMatchingTestCases()
    {
        return array(
            array('[A-Z]', '-- 0 --'),
            array('(a){4}', '--- aaa ---'),
            array('^a', '--- ba ---'),
            array('b$', '--- ba ---'),
            // FIXME
            // array('[:alpha:]', '--- x ---'),
        );
    }
}
