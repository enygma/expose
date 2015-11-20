<?php

namespace Expose\Converter;

/**
 * Class forklifted with premission primarily from PHPIDS
 * ConvertSQL is an anti-evasion normalization module focused on SQL tricks
 */
class ConvertSQL
{

    /**
     * Converts SQLHEX to plain text
     * 
     * @param string $value the value to convert
     * @return string
     */
    public function convertFromSQLHex($value)
    {
        $matches = array();
        if (preg_match_all('/(?:(?:\A|[^\d])0x[a-f\d]{3,}[a-f\d]*)+/im', $value, $matches)) {
            foreach ($matches[0] as $match) {
                $converted = '';
                foreach (str_split($match, 2) as $hex_index) {
                    if (preg_match('/[a-f\d]{2,3}/i', $hex_index)) {
                        $converted .= chr(hexdec($hex_index));
                    }
                }
                $value = str_replace($match, $converted, $value);
            }
        }
        // take care of hex encoded ctrl chars
        $value = preg_replace('/0x\d+/m', ' 1 ', $value);
        return $value;
    }

    /**
     * Converts basic SQL keywords and obfuscations
     * 
     * @param string $value the value to convert
     * @return string
     */
    public function convertFromSQLKeywords($value)
    {
        $pattern = array(
            '/(?:is\s+null)|(like\s+null)|' .
            '(?:(?:^|\W)in[+\s]*\([\s\d"]+[^()]*\))/ims'
        );
        $value   = preg_replace($pattern, '"=0', $value);
        $value   = preg_replace('/[^\w\)]+\s*like\s*[^\w\s]+/ims', '1" OR "1"', $value);
        $value   = preg_replace('/null([,"\s])/ims', '0$1', $value);
        $value   = preg_replace('/\d+\./ims', ' 1', $value);
        $value   = preg_replace('/,null/ims', ',0', $value);
        $value   = preg_replace('/(?:between)/ims', 'or', $value);
        $value   = preg_replace('/(?:and\s+\d+\.?\d*)/ims', '', $value);
        $value   = preg_replace('/(?:\s+and\s+)/ims', ' or ', $value);
        $pattern = array(
            '/(?:not\s+between)|(?:is\s+not)|(?:not\s+in)|' .
            '(?:xor|<>|rlike(?:\s+binary)?)|' .
            '(?:regexp\s+binary)|' .
            '(?:sounds\s+like)/ims'
        );
        $value   = preg_replace($pattern, '!', $value);
        $value   = preg_replace('/"\s+\d/', '"', $value);
        $value   = preg_replace('/(\W)div(\W)/ims', '$1 OR $2', $value);
        $value   = preg_replace('/\/(?:\d+|null)/', null, $value);
        return $value;
    }

    /**
     * This method removes encoded sql # comments
     * 
     * @param string $value the value to convert
     * @return string
     */
    public function convertFromUrlencodeSqlComment($value)
    {
        if (preg_match_all('/(?:\%23.*?\%0a)/im',$value,$matches)){
            $converted = $value;
            foreach($matches[0] as $match){
                $converted = str_replace($match,' ',$converted);
            }
            $value .= "\n" . $converted;
        }
        return $value;
    }
}
