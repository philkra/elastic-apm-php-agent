<?php

namespace PhilKra\Helper;

/*
 * Stack Trace manipulation and creation functions
 */
class StackTrace
{

    /**
     * Function to convert debug_backtrace results to an array of stack frames
     *
     * @param array $backtrace
     * @return array
     */
    public static function convertBacktraceToStackFrames(?array $backtrace)
    {
        $return_value = array();
        foreach ($backtrace as $single_backtrace) {
            $stacktrace             = array();
            $stacktrace['abs_path'] = $single_backtrace['file'];
            $stacktrace['filename'] = basename($single_backtrace['file']);
            $stacktrace['function'] = $single_backtrace['function'] ?? null;
            $stacktrace['lineno']   = $single_backtrace['line'] ?? null;
            $stacktrace['module']   = $single_backtrace['class'] ?? null;
            $stacktrace['vars']     = $single_backtrace['args'] ?? null;
            $return_value[]         = $stacktrace;
        }
        return $return_value;
    }
}