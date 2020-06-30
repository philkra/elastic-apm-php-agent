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

    public function __construct(float $startTime = null)
    {
        $this->startedOn = $startTime;
    }

    /**
     * Start the Timer
     *
     * @return void
     * @throws AlreadyRunningException
     */
    public function start()
    {
        if (null !== $this->startedOn) {
            throw new AlreadyRunningException();
        }

        $this->startedOn = microtime(true);
    }

    /**
     * Stop the Timer
     *
     * @return void
     * @throws NotStartedException
     *
     */
    public function stop()
    {
        if ($this->startedOn === null) {
            throw new NotStartedException();
        }

        $this->stoppedOn = microtime(true);
    }

    /**
     * Get the current elapsed Interval of the Timer in MicroSeconds
     *
     * @return float
     * @throws NotStartedException
     *
     */
    public function getElapsed(): float
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
     * @param float $num
     *
     * @return float
     */
    private function toMicro(float $num): float
    {
        return $num * 1000000;
    }

    /**
     * Get the elapsed Duration of this Timer in MicroSeconds
     *
     * @return float
     * @throws NotStoppedException
     *
     */
    public function getDuration(): float
    {
        if ($this->stoppedOn === null) {
            throw new NotStoppedException();
        }

        return $this->toMicro($this->stoppedOn - $this->startedOn);
    }

    /**
     * Get the current elapsed Interval of the Timer in MilliSeconds
     *
     * @return float
     * @throws NotStartedException
     *
     */
    public function getElapsedInMilliseconds(): float
    {
        if ($this->startedOn === null) {
            throw new NotStartedException();
        }

        return ($this->stoppedOn === null) ?
            $this->toMilli(microtime(true) - $this->startedOn) :
            $this->getDurationInMilliseconds();
    }

    /**
     * Convert the Duration from Seconds to Milli-Seconds
     *
     * @param float $num
     *
     * @return float
     */
    private function toMilli(float $num): float
    {
        return $num * 1000;
    }

    /**
     * Get the elapsed Duration of this Timer in MilliSeconds
     *
     * @return float
     * @throws NotStoppedException
     *
     */
    public function getDurationInMilliseconds(): float
    {
        if ($this->stoppedOn === null) {
            throw new NotStoppedException();
        }

        return $this->toMilli($this->stoppedOn - $this->startedOn);
    }
}
