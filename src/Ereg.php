<?php

namespace Rabus\EregShim;

final class Ereg
{
    public static function ereg($pattern, $string, &$regs = null)
    {
        if ($pattern === '' || $pattern === null) {
            trigger_error('ereg(): REG_EMPTY', E_USER_WARNING);

            return false;
        }

        $pattern = self::convertPattern($pattern, false);

        return \func_num_args() === 2
            ? self::runPregMatch($pattern, $string)
            : self::runPregMatch($pattern, $string, $regs);
    }

    public static function eregi($pattern, $string, &$regs = null)
    {
        if ($pattern === '' || $pattern === null) {
            trigger_error('eregi(): REG_EMPTY', E_USER_WARNING);

            return false;
        }

        $pattern = self::convertPattern($pattern, true);

        return \func_num_args() === 2
            ? self::runPregMatch($pattern, $string)
            : self::runPregMatch($pattern, $string, $regs);
    }

    public static function ereg_replace($pattern, $replacement, $string)
    {
        if ($pattern === '' || $pattern === null) {
            trigger_error('ereg_replace(): REG_EMPTY', E_USER_WARNING);

            return false;
        }

        return \preg_replace(self::convertPattern($pattern, false), $replacement, $string);
    }

    public static function eregi_replace($pattern, $replacement, $string)
    {
        if ($pattern === '' || $pattern === null) {
            trigger_error('eregi_replace(): REG_EMPTY', E_USER_WARNING);

            return false;
        }

        return \preg_replace(self::convertPattern($pattern, true), $replacement, $string);
    }

    public static function split($pattern, $string, $limit = -1)
    {
        if ($pattern === '' || $pattern === null) {
            trigger_error('split(): REG_EMPTY', E_USER_WARNING);

            return false;
        }

        return \preg_split(self::convertPattern($pattern, false), $string, $limit);
    }

    public static function spliti($pattern, $string, $limit = -1)
    {
        if ($pattern === '' || $pattern === null) {
            trigger_error('spliti(): REG_EMPTY', E_USER_WARNING);

            return false;
        }

        return \preg_split(self::convertPattern($pattern, true), $string, $limit);
    }

    private static function runPregMatch($pattern, $string, &$regs = null)
    {
        $result = \preg_match($pattern, $string, $matches);
        if (!$result) {
            return false;
        }

        if (\func_num_args() === 2) {
            return 1;
        }

        $regs = \array_map(
            static function ($match) {
                return $match === '' ? false : $match;
            },
            $matches
        );

        return \max(1, \strlen($matches[0]));
    }

    private static function convertPattern($pattern, $i)
    {
        return \sprintf('/%s/%s', \addcslashes($pattern, '/'), $i ? 'i' : '');
    }
}
