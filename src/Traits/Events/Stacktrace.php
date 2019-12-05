<?php

namespace PhilKra\Traits\Events;

/**
 * Get the Stacktrace from debug_backtrace
 *
 * @link https://github.com/philkra/elastic-apm-php-agent/pull/112
 */
trait Stacktrace {

    /**
     * Creates a backtrace, converts it to a stacktrace and sets the Stacktrace for the span
     *
     * @param int $limit, Def: 0 (unlimited)
     */
    public function setDebugBacktrace(int $limit = 0)
    {
        parent::setStackTrace($this->getDebugBacktrace($limit));
    }

    /**
     * Function to convert debug_backtrace results to an array of stack frames
     *
     * @param int $limit
     *
     * @return array
     */
    protected function getDebugBacktrace(int $limit) : array
    {
        $traces = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, $limit);
        for ($it = 1; $it < count($traces); $it++) {
            if(isset($traces[$it]['file']) === true) {
                $backtrace[] = [
                    'abs_path' => $traces[$it]['file'],
                    'filename' => basename($traces[$it]['file']),
                    'function' => $traces[$it]['function'] ?? null,
                    'lineno'   => $traces[$it]['line'] ?? null,
                    'module'   => $traces[$it]['class'] ?? null,
                    'vars'     => $traces[$it]['args'] ?? null,
                ];
            }
        }

        return $backtrace;
    }

}