<?php

namespace Expose\Converter;

/**
 * Concept forklifted with premission primarily from PHPIDS
 * Converter is an anti-evasion normalization module
 */
class Converter
{
    /**
     * Run all the existing conversion methods
     * 
     * @param string $value the value to convert
     * @return string
     */
    public function runAllConversions($value)
    {
        $misc = new \Expose\Converter\ConvertMisc;
        $value=$misc->convertFromUrlEncode($value);
        $value=$misc->convertFromCommented($value);
        $value=$misc->convertFromWhiteSpace($value);
        $value=$misc->convertEntities($value);
        $value=$misc->convertQuotes($value);
        $value=$misc->convertFromControlChars($value);
        $value=$misc->convertFromNestedBase64($value);
        $value=$misc->convertFromOutOfRangeChars($value);
        $value=$misc->convertFromXML($value);
        $value=$misc->convertFromUTF7($value);
        $value=$misc->convertFromConcatenated($value);
        $value=$misc->convertFromProprietaryEncodings($value);
        
        $js = new \Expose\Converter\ConvertJS;
        $value=$js->convertFromJSCharcode($value);
        $value=$js->convertJSRegexModifiers($value);
        $value=$js->convertFromJSUnicode($value);

        $sql = new \Expose\Converter\ConvertSQL;
        $value=$sql->convertFromSQLHex($value);
        $value=$sql->convertFromSQLKeywords($value);
        $value=$sql->convertFromUrlencodeSqlComment($value);

        return $value;
    }
}
