<?php

namespace PhilKra\Stores;

use PhilKra\Events\Error;

/**
 *
 * Registry for captured the Errors/Excpetions
 *
 */
class ErrorsStore extends Store
{
    /**
     * Register an Error Event
     *
     * @param \PhilKra\Events\Error $error
     *
     * @return void
     */
    public function register(Error $error)
    {
        $this->store [] = $error;
    }
}
