<?php

namespace PhilKra\Traces\Metadata;

use PhilKra\Traces\Trace;

/**
 * APM Metadata
 *
 * @link https://www.elastic.co/guide/en/apm/server/6.7/metadata-api.html#metadata-process-schema
 * @version 6.7 (v2)
 */
class Process implements Trace
{

    /**
     * Serialize Metadata Trace
     *
     * @return array
     */
    public function jsonSerialize() : array
    {
        // Build Payload to serialize
        $payload = [
            'pid' => getmypid(),
        ];

        // TODO -- getenv('argv')

        return $payload;
    }

}
