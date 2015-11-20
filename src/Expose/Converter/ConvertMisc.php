<?php

namespace Expose\Converter;

/**
 * Class forklifted with premission primarily from PHPIDS
 * ConvertMisc is an anti-evasion normalization module for other evasions
 */
class ConvertMisc
{
    /**
     * Check for comments and erases them if available
     *
     * @param string $value the value to convert
     * @return string
     */
    public function convertFromCommented($value)
    {
        // check for existing comments
        if (preg_match('/(?:\<!-|-->|\/\*|\*\/|\/\/\W*\w+\s*$)|(?:--[^-]*-)/ms', $value)) {
            $pattern = array(
                '/(?:(?:<!)(?:(?:--(?:[^-]*(?:-[^-]+)*)--\s*)*)(?:>))/ms',
                '/(?:(?:\/\*\/*[^\/\*]*)+\*\/)/ms',
                '/(?:--[^-]*-)/ms'
            );
            $converted = preg_replace($pattern, ';', $value);
            $value    .= "\n" . $converted;
        }
        //make sure inline comments are detected and converted correctly
        $value = preg_replace('/(<\w+)\/+(\w+=?)/m', '$1/$2', $value);
        $value = preg_replace('/[^\\\:]\/\/(.*)$/m', '/**/$1', $value);
        $value = preg_replace('/([^\-&])#.*[\r\n\v\f]/m', '$1', $value);
        $value = preg_replace('/([^&\-])#.*\n/m', '$1 ', $value);
        $value = preg_replace('/^#.*\n/m', ' ', $value);
        return $value;
    }

    /**
     * Strip newlines
     *
     * @param string $value the value to convert
     * @return string
     */
    public function convertFromWhiteSpace($value)
    {
        //check for inline linebreaks
        $search = array('\r', '\n', '\f', '\t', '\v');
        $value  = str_replace($search, ';', $value);
        // replace replacement characters regular spaces
        $value = str_replace('�', ' ', $value);
        //convert real linebreaks
        return preg_replace('/(?:\n|\r|\v)/m', '  ', $value);
    }

    /**
     * Converts from hex/dec entities
     *
     * @param string $value the value to convert
     * @return string
     */
    public function convertEntities($value)
    {
        $converted = null;
        //deal with double encoded payload
        $value = preg_replace('/&amp;/', '&', $value);
        if (preg_match('/&#x?[\w]+/ms', $value)) {
            $converted = preg_replace('/(&#x?[\w]{2}\d?);?/ms', '$1;', $value);
            $converted = html_entity_decode($converted, ENT_QUOTES, 'UTF-8');
            $value    .= "\n" . str_replace(';;', ';', $converted);
        }
        // normalize obfuscated protocol handlers
        $value = preg_replace(
            '/(?:j\s*a\s*v\s*a\s*s\s*c\s*r\s*i\s*p\s*t\s*:)|(d\s*a\s*t\s*a\s*:)/ms',
            'javascript:',
            $value
        );
        return $value;
    }

    /**
     * Normalize quotes
     *
     * @param string $value the value to convert
     * @return string
     */
    public function convertQuotes($value)
    {
        // normalize different quotes to "
        $pattern = array('\'', '`', '´', '’', '‘');
        $value   = str_replace($pattern, '"', $value);
        //make sure harmless quoted strings don't generate false alerts
        $value = preg_replace('/^"([^"=\\!><~]+)"$/', '$1', $value);
        return $value;
    }

    /**
     * Detects nullbytes and controls chars via ord()
     *
     * @param string $value the value to convert
     * @return string
     */
    public function convertFromControlChars($value)
    {
        // critical ctrl values
        $search = array(
            chr(0), chr(1), chr(2), chr(3), chr(4), chr(5),
            chr(6), chr(7), chr(8), chr(11), chr(12), chr(14),
            chr(15), chr(16), chr(17), chr(18), chr(19), chr(24),
            chr(25), chr(192), chr(193), chr(238), chr(255), '\\0'
        );
        $value = str_replace($search, '%00', $value);
        //take care for malicious unicode characters
        $value = urldecode(
            preg_replace(
                '/(?:%E(?:2|3)%8(?:0|1)%(?:A|8|9)\w|%EF%BB%BF|%EF%BF%BD)|(?:&#(?:65|8)\d{3};?)/i',
                null,
                urlencode($value)
            )
        );
        $value = urlencode($value);
        $value = preg_replace('/(?:%F0%80%BE)/i', '>', $value);
        $value = preg_replace('/(?:%F0%80%BC)/i', '<', $value);
        $value = preg_replace('/(?:%F0%80%A2)/i', '"', $value);
        $value = preg_replace('/(?:%F0%80%A7)/i', '\'', $value);
        $value = urldecode($value);
        $value = preg_replace('/(?:%ff1c)/', '<', $value);
        $value = preg_replace('/(?:&[#x]*(200|820|200|820|zwn?j|lrm|rlm)\w?;?)/i', null, $value);
        $value = preg_replace(
            '/(?:&#(?:65|8)\d{3};?)|' .
            '(?:&#(?:56|7)3\d{2};?)|' .
            '(?:&#x(?:fe|20)\w{2};?)|' .
            '(?:&#x(?:d[c-f])\w{2};?)/i',
            null,
            $value
        );
        $value = str_replace(
            array(
                '«',
                '〈',
                '＜',
                '‹',
                '〈',
                '⟨'
            ),
            '<',
            $value
        );
        $value = str_replace(
            array(
                '»',
                '〉',
                '＞',
                '›',
                '〉',
                '⟩'
            ),
            '>',
            $value
        );
        return $value;
    }

    /**
     * This method matches and translates base64 strings and fragments
     * used in data URIs
     *
     * @param string $value the value to convert
     * @return string
     */
    public function convertFromNestedBase64($value)
    {
        $matches = array();
        preg_match_all('/(?:^|[,&?])\s*([a-z0-9]{50,}=*)(?:\W|$)/im', $value, $matches);
        foreach ($matches[1] as $item) {
            if (isset($item) && !preg_match('/[a-f0-9]{32}/i', $item)) {
                $base64_item = base64_decode($item);
                $value = str_replace($item, $base64_item, $value);
            }
        }
        return $value;
    }

    /**
     * Detects nullbytes and controls chars via ord()
     *
     * @param string $value the value to convert
     * @return string
     */
    public function convertFromOutOfRangeChars($value)
    {
        $values = str_split($value);
        foreach ($values as $item) {
            if (ord($item) >= 127) {
                $value = str_replace($item, ' ', $value);
            }
        }
        return $value;
    }

    /**
     * Strip XML patterns
     *
     * @param string $value the value to convert
     * @return string
     */
    public function convertFromXML($value)
    {
        $converted = strip_tags($value);
        if (!$converted || $converted === $value) {
            return $value;
        } else {
            return $value . "\n" . $converted;
        }
    }

    /**
     * Converts relevant UTF-7 tags to UTF-8
     *
     * @param string $value the value to convert
     * @return string
     */
    public function convertFromUTF7($value)
    {
        if (preg_match('/\+A\w+-?/m', $value)) {
            if (function_exists('mb_convert_encoding')) {
                if (version_compare(PHP_VERSION, '5.2.8', '<')) {
                    $tmp_chars = str_split($value);
                    $value = '';
                    foreach ($tmp_chars as $char) {
                        if (ord($char) <= 127) {
                            $value .= $char;
                        }
                    }
                }
                $value .= "\n" . mb_convert_encoding($value, 'UTF-8', 'UTF-7');
            } else {
                //list of all critical UTF7 codepoints
                $schemes = array(
                    '+ACI-'      => '"',
                    '+ADw-'      => '<',
                    '+AD4-'      => '>',
                    '+AFs-'      => '[',
                    '+AF0-'      => ']',
                    '+AHs-'      => '{',
                    '+AH0-'      => '}',
                    '+AFw-'      => '\\',
                    '+ADs-'      => ';',
                    '+ACM-'      => '#',
                    '+ACY-'      => '&',
                    '+ACU-'      => '%',
                    '+ACQ-'      => '$',
                    '+AD0-'      => '=',
                    '+AGA-'      => '`',
                    '+ALQ-'      => '"',
                    '+IBg-'      => '"',
                    '+IBk-'      => '"',
                    '+AHw-'      => '|',
                    '+ACo-'      => '*',
                    '+AF4-'      => '^',
                    '+ACIAPg-'   => '">',
                    '+ACIAPgA8-' => '">'
                );
                $value = str_ireplace(
                    array_keys($schemes),
                    array_values($schemes),
                    $value
                );
            }
        }
        return $value;
    }

    /**
     * Converts basic concatenations
     *
     * @param string $value the value to convert
     * @return string
     */
    public function convertFromConcatenated($value)
    {
        //normalize remaining backslashes
        if ($value != preg_replace('/(\w)\\\/', "$1", $value)) {
            $value .= preg_replace('/(\w)\\\/', "$1", $value);
        }
        $compare = stripslashes($value);
        $pattern = array(
            '/(?:<\/\w+>\+<\w+>)/s',
            '/(?:":\d+[^"[]+")/s',
            '/(?:"?"\+\w+\+")/s',
            '/(?:"\s*;[^"]+")|(?:";[^"]+:\s*")/s',
            '/(?:"\s*(?:;|\+).{8,18}:\s*")/s',
            '/(?:";\w+=)|(?:!""&&")|(?:~)/s',
            '/(?:"?"\+""?\+?"?)|(?:;\w+=")|(?:"[|&]{2,})/s',
            '/(?:"\s*\W+")/s',
            '/(?:";\w\s*\+=\s*\w?\s*")/s',
            '/(?:"[|&;]+\s*[^|&\n]*[|&]+\s*"?)/s',
            '/(?:";\s*\w+\W+\w*\s*[|&]*")/s',
            '/(?:"\s*"\s*\.)/s',
            '/(?:\s*new\s+\w+\s*[+",])/',
            '/(?:(?:^|\s+)(?:do|else)\s+)/',
            '/(?:[{(]\s*new\s+\w+\s*[)}])/',
            '/(?:(this|self)\.)/',
            '/(?:undefined)/',
            '/(?:in\s+)/'
        );
        // strip out concatenations
        $converted = preg_replace($pattern, null, $compare);
        //strip object traversal
        $converted = preg_replace('/\w(\.\w\()/', "$1", $converted);
        // normalize obfuscated method calls
        $converted = preg_replace('/\)\s*\+/', ")", $converted);
        //convert JS special numbers
        $converted = preg_replace(
            '/(?:\(*[.\d]e[+-]*[^a-z\W]+\)*)|(?:NaN|Infinity)\W/ims',
            1,
            $converted
        );
        if ($converted && ($compare != $converted)) {
            $value .= "\n" . $converted;
        }
        return $value;
    }


    /**
     * This method collects and decodes proprietary encoding types
     *
     * @param string $value the value to convert
     * @return string
     */
    public function convertFromProprietaryEncodings($value)
    {
        //Xajax error reportings
        $value = preg_replace('/<!\[CDATA\[(\W+)\]\]>/im', '$1', $value);
        //strip false alert triggering apostrophes
        $value = preg_replace('/(\w)\"(s)/m', '$1$2', $value);
        //strip quotes within typical search patterns
        $value = preg_replace('/^"([^"=\\!><~]+)"$/', '$1', $value);
        //OpenID login tokens
        $value = preg_replace('/{[\w-]{8,9}\}(?:\{[\w=]{8}\}){2}/', null, $value);
        //convert Content and \sdo\s to null
        $value = preg_replace('/Content|\Wdo\s/', null, $value);
        //strip emoticons
        $value = preg_replace(
            '/(?:\s[:;]-[)\/PD]+)|(?:\s;[)PD]+)|(?:\s:[)PD]+)|-\.-|\^\^/m',
            null,
            $value
        );
        //normalize separation char repetion
        $value = preg_replace('/([.+~=*_\-;])\1{2,}/m', '$1', $value);
        //normalize multiple single quotes
        $value = preg_replace('/"{2,}/m', '"', $value);
        //normalize quoted numerical values and asterisks
        $value = preg_replace('/"(\d+)"/m', '$1', $value);
        //normalize pipe separated request parameters
        $value = preg_replace('/\|(\w+=\w+)/m', '&$1', $value);
        //normalize ampersand listings
        $value = preg_replace('/(\w\s)&\s(\w)/', '$1$2', $value);
        //normalize escaped RegExp modifiers
        $value = preg_replace('/\/\\\(\w)/', '/$1', $value);
        return $value;
    }


    /**
     * Check for basic urlencoded information
     *
     * @param string $value the value to convert
     * @return string
     */
    public function convertFromUrlEncode($value)
    {
        $converted = urldecode($value);
        if (!$converted || $converted === $value) {
            return $value;
        } else {
            return $value . "\n" . $converted;
        }
    }
    
    
}
