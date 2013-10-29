<?php

/*
 * This file is part of the Monolog package.
 *
 * (c) Thomas Tourlourat <thomas@tourlourat.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Hn\MessageFormatBundle\Handler;

use Hn\MessageFormatBundle\Formatter\LogstashFormatter;
use Monolog\Logger;
use Monolog\Formatter\LineFormatter;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Monolog\Handler\AbstractProcessingHandler;

/**
 * Logs to a Redis key and initializes with the logstash formatter
 *
 *
 * @author Thomas Tourlourat <thomas@tourlourat.com>
 */
class LogStashRedisHandler extends AbstractProcessingHandler
{

    private $redisClient;

    private $redisKey;

    protected $container;

    # redis instance, key to use
    public function __construct(ContainerInterface $container)
    {
        $redis = null;

        $this->container = $container;

        $host = $this->container->getParameter('hn_message_format.redis.host');
        $port = $this->container->getParameter('hn_message_format.redis.port');
        $list = $this->container->getParameter('hn_message_format.redis.list');
        $password = $this->container->getParameter('hn_message_format.redis.password');
        $name = $this->container->getParameter('hn_message_format.app_name');
        $level = $this->container->getParameter('hn_message_format.handler.level');
        $bubble = $this->container->getParameter('hn_message_format.handler.bubble');

        if (class_exists('\Predis\Client')) {
            $redis = new \Predis\Client(array(
                    'scheme' => 'tcp',
                    'host' => $host,
                    'port' => $port,
                    'password' => $password
                )
            );
        } else {
            throw new InvalidConfigurationException('Predis\Client class required.');
        }

        if (!(($redis instanceof \Predis\Client) || ($redis instanceof \Redis))) {
            throw new \InvalidArgumentException('Predis\Client or Redis instance required');
        }

        $this->redisClient = $redis;
        $this->redisKey = $list;

        parent::__construct($level, $bubble);

        $this->setFormatter(new LogstashFormatter($name));

    }

    protected function write(array $record)
    {
        $this->redisClient->lpush($this->redisKey, $record["formatted"]);
    }

    /**
     * {@inheritDoc}
     */
    protected function getDefaultFormatter()
    {
        return new LineFormatter();
    }

}
