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

namespace PhilKra\Traces\Metricset;

use PhilKra\Traces\Trace;

/**
 * APM Metric Object
 *
 * @link https://www.elastic.co/guide/en/apm/server/6.7/metricset-api.html#metricset-schema
 * @version 6.7 (v2)
 */
class Metric
{

    /** @var string */
    private $name;

    /** @var int */
    private $value;

    /**
     * @param string $name
     * @param int $value
     */
    public function __construct(string $name, int $value)
    {
        $this->name  = trim($name);
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getValue() : int
    {
        return $this->value;
    }

}
