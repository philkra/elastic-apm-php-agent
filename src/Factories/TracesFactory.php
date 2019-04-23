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

namespace PhilKra\Factories;

use PhilKra\Traces\Error;
use PhilKra\Traces\Span;
use PhilKra\Traces\Transaction;
use PhilKra\Traces\Metadata;
use PhilKra\Traces\Metricset;

/**
 * Interface for the Traces Factories
 */
interface TracesFactory
{

    /**
     * Creates a new Error Trace
     *
     * @param \Throwable $throwable
     *
     * @return Error
     */
    public function newError(\Throwable $throwable) : Error;

    /**
     * Generate new Span
     *
     * @param string $name
     * @param string $type
     *
     * @return Span
     */
    public function newSpan(string $name, string $type) : Span;

    /**
     * Generate new Transaction
     *
     * @param string $name
     * @param string $type
     *
     * @return Transaction
     */
    public function newTransaction(string $name, string $type) : Transaction;

    /**
     * Creates a new Metricset Trace
     *
     * @return Metricset
     */
    public function newMetricset() : Metricset;

    /**
     * Creates a new Metadata Trace
     *
     * @return Metadata
     */
    public function newMetadata() : Metadata;

}
