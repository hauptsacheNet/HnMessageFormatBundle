<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ivan
 * Date: 16.10.13
 * Time: 10:00
 * To change this template use File | Settings | File Templates.
 */

namespace Hn\MessageFormatBundle\Formatter;

use Hn\MessageFormatBundle\Formatter\NormalizerFormatter;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LogstashFormatter extends NormalizerFormatter {

    protected $systemName;

    protected $extraPrefix = null;

    protected $contextPrefix = null;

    protected $applicationName;

    /** @var ContainerInterface $container */
    protected $container;

    public function __construct($applicationName, $systemName = null, $extraPrefix = null, $contextPrefix = null )
    {
        $this->systemName = $systemName ?: gethostname();
        $this->extraPrefix = $extraPrefix;
        $this->contextPrefix = $contextPrefix;

        $this->applicationName = $applicationName;

    }

    /** currently Version 1 format */
    public function format(array $record)
    {
        $message = array(
            '@timestamp' => $record['datetime'] instanceof \DateTime ? $record['datetime']->getTimestamp() : $record['datetime'],
            '@version' => 1,
            'message' => $this->applicationName . ' ' . $record['message'],
            'host' => $this->systemName,
            'type' => $record['channel'],
            'channel' => $record['channel'],
            'level' => $record['level_name'],
            'app_name' => $this->applicationName
        );



        foreach ($record['extra'] as $key => $val) {
            $message[$this->extraPrefix . $key] = $val;
        }

        foreach ($record['context'] as $key => $val) {
            $message[$this->contextPrefix . $key] = $val;
        }

        return $this->toJson($message) ."\n";
    }

}