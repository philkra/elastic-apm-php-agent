<?php

namespace PhilKra\Events;

/**
 *
 * Event Bean for Metricset
 *
 * @link https://www.elastic.co/guide/en/apm/server/7.3/metricset-api.html
 * @link https://www.elastic.co/guide/en/apm/server/current/exported-fields-system.html
 *
 */
class Metricset extends EventBean implements \JsonSerializable
{
    /**
     * @var array
     */
    private $samples = [];

    /**
     * @var array
     */
    private $tags = [];

    /**
     * @param array $set
     * @param array $tags
     */
    public function __construct(array $set, array $tags)
    {
        parent::__construct([]);
        foreach($set as $k => $v) {
            $this->samples[$k] = ['value' => $v];
        }
        $this->tags = $tags;
    }

    /**
     * Serialize Metricset
     *
     * TODO -- add tags
     *
     * @return array
     */
    public function jsonSerialize() : array
    {
        return [
            'metricset' => [
                'samples'   => $this->samples,
                'timestamp' => $this->getTimestamp(),
            ]
        ];
    }

}
