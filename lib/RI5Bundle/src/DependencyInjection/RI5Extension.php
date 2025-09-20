<?php

namespace RI5\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Summary of RI5Bundle
 */
class RI5Extension extends Extension 
{
    public function load(array $configs, ContainerBuilder $container)
    {
        //var_dump('We\'re alive!');die;
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yaml');   
    
    }
  
    // public function load(array $configs, ContainerBuilder $container)
    // {
    //     //var_dump('We\'re alive!');die;
    //     $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
    //     $loader->load('services.xml');
    // }

}