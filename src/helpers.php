<?php
declare(strict_types=1);

/**
 * Sheet column number convert to letters
 * @param int $col_num
 * @return string
*/
function colNum2Letter(int $col_num): string
{
    $letter = '';
    if ($col_num <= 0 ) {
        return $letter;
    }

    while ($col_num > 0) {
        $a = floor(($col_num - 1) / 26);
        $b = ($col_num - 1) % 26;
        $letter .= chr($b + 65);
        $col_num = $a;
    }
    return strrev($letter);
}

/**
 * Sheet column string convert to number
 * @param string $col_str
 * @return int
*/
function colLetter2Num(string $col_str): int
{
    $col_num = 0;

    $col_str = strtoupper($col_str);

    if (!preg_match('/[A-Z]+/', $col_str)) {
        return $col_num;
    }

    $col_str = strrev($col_str);

    for ($i = 0; $i < strlen($col_str); $i++) {
        $time = (ord($col_str[$i]) - 65) + 1;
        $col_num += $time * pow(26, $i);
    }

    return $col_num;
}