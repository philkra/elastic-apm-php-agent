<?php

namespace PhilKra\Helper;

use PhilKra\Exception\Timer\AlreadyRunningException;
use PhilKra\Exception\Timer\NotStartedException;
use PhilKra\Exception\Timer\NotStoppedException;

/**
 * Timer for Duration tracing
 */
class Timer
{
    /**
     * Starting Timestamp
     *
     * @var double
     */
    private $startedOn = null;

    /**
     * Ending Timestamp
     *
     * @var double
     */
    private $stoppedOn = null;

    /**
     * Get the Event's Timestamp Epoch in Micro
     *
     * @return int
     */
    public function getNow() : int
    {
        return $this->toMicro(round(microtime(true)));
    }

    /**
     * Start the Timer
     *
     * @return void
     * @throws AlreadyRunningException
     */
    public function start(float $startTime = null) : void
    {
        if (null !== $this->startedOn) {
            throw new AlreadyRunningException();
        }

        $this->startedOn = ($startTime !== null) ? $startTime : microtime(true);
    }

    /**
     * Stop the Timer
     *
     * @throws \PhilKra\Exception\Timer\NotStartedException
     *
     * @return void
     */
    public function stop() : void
    {
        if ($this->startedOn === null) {
            throw new NotStartedException();
        }

        $this->stoppedOn = microtime(true);
    }

    /**
     * Get the elapsed Duration of this Timer in MicroSeconds
     *
     * @throws \PhilKra\Exception\Timer\NotStoppedException
     *
     * @return int
     */
    public function getDuration() : int
    {
        if ($this->stoppedOn === null) {
            throw new NotStoppedException();
        }

        return $this->toMicro($this->stoppedOn - $this->startedOn);
    }

    /**
     * Get the current elapsed Interval of the Timer in MicroSeconds
     *
     * @throws \PhilKra\Exception\Timer\NotStartedException
     *
     * @return float
     */
    public function getElapsed() : float
    {
        if ($this->startedOn === null) {
            throw new NotStartedException();
        }

        return ($this->stoppedOn === null) ?
            $this->toMicro(microtime(true) - $this->startedOn) :
            $this->getDuration();
    }

    /**
     * Convert the Duration from Seconds to Micro-Seconds
     *
     * @param  float $num
     *
     * @return float
     */
    private function toMicro(float $num) : float
    {
        return $num * 1000000;
    }

}
