<?php

namespace PhilKra\Traces;

use PhilKra\Helper\Config;
use PhilKra\Traces\Metadata\Process;
use PhilKra\Traces\Metadata\Service;
use PhilKra\Traces\Metadata\System;
use PhilKra\Traces\Metadata\User;

/**
 * APM Metadata
 *
 * @link https://www.elastic.co/guide/en/apm/server/6.7/metadata-api.html
 * @version 6.7 (v2)
 */
class Metadata implements Trace
{

    /** @var PhilKra\Traces\Metadata\Process **/
    private $process;

    /** @var PhilKra\Traces\Metadata\Service **/
    private $service;

    /** @var PhilKra\Traces\Metadata\System **/
    private $system;

    /** @var PhilKra\Traces\Metadata\User **/
    private $user;

    /**
     * Auto Instrument the Metadata
     *
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->process = new Process();
        $this->system  = new System($config);
        $this->service = new Service($config);
        $this->user    = new User();
    }

    /**
     * @return PhilKra\Traces\Metadata\Process
     */
    public function getProcess() : Process
    {
        return $this->process;
    }

    /**
     * @return PhilKra\Traces\Metadata\Service
     */
    public function getService() : Service
    {
        return $this->service;
    }

    /**
     * @return PhilKra\Traces\Metadata\System
     */
    public function getSystem() : System
    {
        return $this->system;
    }

    /**
     * @return PhilKra\Traces\Metadata\User
     */
    public function getUser() : User
    {
        return $this->user;
    }

    /**
     * Serialize Metadata Trace
     *
     * @return array
     */
    public function jsonSerialize() : array
    {
        $payload = [
          'metadata' => [
              'process' => $this->process,
              'system'  => $this->system,
              'service' => $this->service,
          ]
      ];

      if($this->user->isSet() === true) {
          $payload['metadata']['user'] = $this->user;
      }

      return $payload;
    }

}
