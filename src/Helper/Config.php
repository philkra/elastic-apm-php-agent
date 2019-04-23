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

namespace PhilKra\Helper;

use PhilKra\Exception\MissingAppNameException;
use PhilKra\Exception\Serializers\UnsupportedApmVersionException;

/**
 *
 * Agent Config Store
 *
 */
class Config
{

    /**
     * Config Set
     *
     * @var array
     */
    private $config;

    /**
     * @param array $config
     */
    public function __construct(array $config)
    {
        if (isset($config['name']) === false) {
            throw new MissingAppNameException();
        }

        $this->config = array_replace_recursive($this->getDefaultConfig(), $config);
    }

    /**
     * Get Config Value
     *
     * @param string $key
     * @param mixed $default
     *
     * @return mixed: value | null
     */
    public function get(string $key, $default = null)
    {
        return $this->getValueByKey($key, $this->asArray(), $default);
    }

    /**
     * Get the all Config Set as array
     *
     * @return array
     */
    public function asArray() : array
    {
        return $this->config;
    }

    /**
     * Get the Default Config of the Agent
     *
     * @link https://github.com/philkra/elastic-apm-php-agent/issues/55
     *
     * @return array
     */
    private function getDefaultConfig() : array
    {
        return [
            'transport'      => [
                'method' => 'http',
                'host'   => 'http://127.0.0.1:8200',
                'config' => [
                    'timeout' => 5,
                ],
            ],
            'secretToken'    => null,
            'hostname'       => gethostname(),
            'appVersion'     => '0.0.0',
            'active'         => true,
            'environment'    => 'development',
            'env'            => [],
            'cookies'        => [],
            'backtraceLimit' => 0,
        ];
    }

    /**
     * Allow access to the Config with the dot.notation
     *
     * @credit Selvin Ortiz
     * @link https://selvinortiz.com/blog/traversing-arrays-using-dot-notation
     *
     * @param string $key
     * @param mixed $default
     *
     * @return mixed
     */
    private function getValueByKey($key, array $data, $default = null)
    {
        // @assert $key is a non-empty string
        // @assert $data is a loopable array
        // @otherwise return $default value
        if (!is_string($key) || empty($key) || !count($data))
        {
            return $default;
        }

        // @assert $key contains a dot notated string
        if (strpos($key, '.') !== false)
        {
            $keys = explode('.', $key);

            foreach ($keys as $innerKey)
            {
                // @assert $data[$innerKey] is available to continue
                // @otherwise return $default value
                if (!array_key_exists($innerKey, $data))
                {
                    return $default;
                }

                $data = $data[$innerKey];
            }

            return $data;
        }

        // @fallback returning value of $key in $data or $default value
        return array_key_exists($key, $data) ? $data[$key] : $default;
    }

}
