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

namespace PhilKra\Traces;

use PhilKra\Helper\Timer;

/**
 *
 * Trace with Timing Context
 *
 */
class TimedTrace implements Trace
{

    /**
     * @var Timer
     */
    private $timer;

    /**
     * Init the Event with the Timestamp
     */
    public function __construct()
    {
        $this->timer = new Timer();
    }

    /**
     * Start the Event Time (at microtime X)
     *
     * @param float|null $initAt
     */
    public function start(?float $initAt = null) : void
    {
        $this->timer->start($initAt);
    }

    /**
     * Stop the Timer
     */
    public function stop() : void
    {
        $this->timer->stop();
    }

    /**
     * Get the Duration
     *
     * @return int
     */
    public function getDuration() : int
    {
        return $this->timer->getElapsed();
    }

    /**
     * @return Timer
     */
    protected function getTimer() : Timer
    {
        return $this->timer;
    }

    /**
     * @{inheritDoc}
     */
    public function jsonSerialize() : array
    {
        return [];
    }

}
