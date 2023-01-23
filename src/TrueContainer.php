<?php
namespace App;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;

class TrueContainer extends ContainerBuilder {

    public static function buildContainer($rootPath)
    {
        $container = new self();
        $container->setParameter('app_root', $rootPath);
        $loader = new YamlFileLoader(
            $container,
            new FileLocator($rootPath . '/config')
        );
        $loader->load('services.yaml');
        $container->compile();

        return $container;
    }

    public function get($id, $invalidBehavior = ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE) : ?object {
        if (strtolower($id) == 'service_container') {
            if (ContainerInterface::EXCEPTION_ON_INVALID_REFERENCE 
                !== 
                $invalidBehavior
            ) {
                return null;
            }
            throw new InvalidArgumentException(
                'The service definition "service_container" does not exist.'
            );
        }
        
        return parent::get($id, $invalidBehavior);
    }
}