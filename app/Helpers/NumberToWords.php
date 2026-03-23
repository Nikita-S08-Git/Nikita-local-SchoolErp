<?php

/**
 * Convert a number to words (e.g., 100 -> "One Hundred Rupees")
 *
 * @param float|int $number
 * @return string
 */
function numberToWords($number)
{
    // Ensure we have a numeric value
    if (!is_numeric($number)) {
        return 'Zero';
    }
    
    $number = round(floatval($number), 2);
    
    // Handle zero case
    if ($number <= 0) {
        return 'Zero';
    }
    
    $decimal = floor($number);
    $fraction = round(($number - $decimal) * 100);
    
    $words = '';
    
    if ($decimal > 0) {
        $words = convertIntegerToWords($decimal) . ' Rupees';
    }
    
    if ($fraction > 0) {
        $words .= ' and ' . convertIntegerToWords($fraction) . ' Paise';
    }
    
    return trim($words);
}

/**
 * Convert an integer to words
 *
 * @param int $integer
 * @return string
 */
function convertIntegerToWords($integer)
{
    if ($integer < 0) {
        return 'Negative ' . convertIntegerToWords(-$integer);
    }
    
    if ($integer === 0) {
        return '';
    }
    
    $words = '';
    
    if ($integer >= 10000000) {
        $crore = floor($integer / 10000000);
        $words .= convertIntegerToWords($crore) . ' Crore ';
        $integer %= 10000000;
    }
    
    if ($integer >= 100000) {
        $lakh = floor($integer / 100000);
        $words .= convertIntegerToWords($lakh) . ' Lakh ';
        $integer %= 100000;
    }
    
    if ($integer >= 1000) {
        $thousand = floor($integer / 1000);
        $words .= convertIntegerToWords($thousand) . ' Thousand ';
        $integer %= 1000;
    }
    
    if ($integer >= 100) {
        $hundred = floor($integer / 100);
        $words .= getHundredWord($hundred) . ' Hundred ';
        $integer %= 100;
    }
    
    if ($integer > 0) {
        $words .= getTensWord($integer);
    }
    
    return trim($words);
}

/**
 * Get word for hundreds (1-9)
 *
 * @param int $num
 * @return string
 */
function getHundredWord($num)
{
    $hundreds = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine'];
    return $hundreds[$num] ?? '';
}

/**
 * Get word for tens (1-99)
 *
 * @param int $num
 * @return string
 */
function getTensWord($num)
{
    $tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];
    $ones = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine', 'Ten', 
             'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];
    
    if ($num < 20) {
        return $ones[$num];
    }
    
    $ten = floor($num / 10);
    $one = $num % 10;
    
    return $tens[$ten] . ($one > 0 ? ' ' . $ones[$one] : '');
}
