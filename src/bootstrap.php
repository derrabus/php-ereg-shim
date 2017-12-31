<?php

use Rabus\EregShim\Ereg as s;

if (!function_exists('ereg')) {
    function ereg($pattern, $string, &$regs = null)
    {
        return func_num_args() === 2
            ? s::ereg($pattern, $string)
            : s::ereg($pattern, $string, $regs);
    }
}

if (!function_exists('eregi')) {
    function eregi($pattern, $string, &$regs = null)
    {
        return func_num_args() === 2
            ? s::eregi($pattern, $string)
            : s::eregi($pattern, $string, $regs);
    }
}

if (!function_exists('ereg_replace')) {
    function ereg_replace($pattern , $replacement , $string)
    {
        return s::ereg_replace($pattern , $replacement , $string);
    }
}

if (!function_exists('eregi_replace')) {
    function eregi_replace($pattern , $replacement , $string)
    {
        return s::eregi_replace($pattern , $replacement , $string);
    }
}

if (!function_exists('split')) {
    function split($pattern, $string, $limit = -1)
    {
        return s::split($pattern, $string, $limit);
    }
}

if (!function_exists('spliti')) {
    function spliti($pattern, $string, $limit = -1)
    {
        return s::spliti($pattern, $string, $limit);
    }
}
