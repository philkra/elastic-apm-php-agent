<?php
/**
 * This file is part of the PhilKra/elastic-apm-php-agent library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license http://opensource.org/licenses/MIT MIT
 * @link https://github.com/philkra/elastic-apm-php-agent GitHub
 */

namespace PhilKra\Stores;

use PhilKra\Traces\Trace;

/**
 *
 * Registry for <b>all</b> captured Events
 *
 */
class TracesStore implements \JsonSerializable
{
    /**
     * Set of Traces
     *
     * @var array of PhilKra\Traces\Trace
     */
    protected $store = [];

    /**
     * Get all Registered Errors
     *
     * @return array of PhilKra\Traces\Trace
     */
    public function list() : array
    {
        return $this->store;
    }

    /**
     * Register a Trace
     *
     * @param Trace
     *
     * @return void
     */
    public function register(Trace $t) : void
    {
        $this->store [] = $t;
    }

    /**
     * Is the Store Empty ?
     *
     * @return bool
     */
    public function isEmpty() : bool
    {
        return empty($this->store);
    }

    /**
     * Empty the Store
     *
     * @return void
     */
    public function reset()
    {
        $this->store = [];
    }

    /**
     * Serialize the Events Store
     *
     * @return array
     */
    public function jsonSerialize() : array
    {
        return $this->store;
    }

    /**
     * Generator to ND-JSON for Intake API v2
     *
     * @return string
     */
    public function toNdJson() : string
    {
        return sprintf("%s\n", implode("\n", array_map(function($obj) {
            return json_encode($obj, JSON_FORCE_OBJECT);
        }, $this->list())));
    }

}
