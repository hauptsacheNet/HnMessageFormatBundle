<?php

namespace Hn\MessageFormatBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class HnMessageFormatExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('hn_message_format.app_name', $config['app_name']);

        $container->setParameter('hn_message_format.redis.host', $config['redis']['host']);
        $container->setParameter('hn_message_format.redis.port', $config['redis']['port']);
        $container->setParameter('hn_message_format.redis.list', $config['redis']['list']);
        $container->setParameter('hn_message_format.redis.password', $config['redis']['password']);

        $level = is_int($config['handler']['level']) ? $config['handler']['level'] : constant('Monolog\Logger::'.strtoupper($config['handler']['level']));

        $container->setParameter('hn_message_format.handler.level', $level);

        $bubble = $config['handler']['bubble'] === 'true' ? true : false;

        $container->setParameter('hn_message_format.handler.bubble', $bubble);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');
    }

}
