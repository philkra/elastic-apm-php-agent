<?php

namespace PhilKra\Helper;

/*
 * Functions to convert values for transmission to ElasticSearch.
 */
class Encoding
{

    /**
     * The maximum number of characters that are accepted in a keyword field.
     */
    const KEYWORD_MAX_LENGTH = 1024;


    /**
     * Limit the size of keyword fields. This is the same approach used by the Python APM client.
     *
     * @param string $value
     * @return string
     */
    public static function keywordField($value)
    {
        if (strlen($value) > self::KEYWORD_MAX_LENGTH && mb_strlen($value, 'UTF-8') > self::KEYWORD_MAX_LENGTH) { // strlen is faster (O(1)), so we prefer to first check using it, and then double-checking with the slower mb_strlen (O(n)) only when necessary
            return mb_substr($value, 0, self::KEYWORD_MAX_LENGTH - 1, 'UTF-8') . '…';
        }

        return $value;
    }
}