<?php

namespace Vellocet\SimpleBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\Common\Annotations\IndexedReader;
use Doctrine\Common\Annotations\AnnotationReader;

/**
 * Set up for unit tests
 * Class: TestBase
 *
 * @see WebTestCase
 */
class TestBase extends WebTestCase
{
  /**
   * Sets up entity manager using sqlite, and validator
   * setup
   */
  protected function setup()
  {
    $conn = \Doctrine\DBAL\DriverManager::getConnection(array(
      'driver' => 'pdo_sqlite',
      'memory' => true
    ));
    
    $config = new \Doctrine\ORM\Configuration();
    $config->setAutoGenerateProxyClasses(true);
    $config->setProxyDir(\sys_get_temp_dir());
    $config->setProxyNamespace('VellocetSimpleTests\Entities');
    $config->setMetadataDriverImpl(new AnnotationDriver(new IndexedReader(new AnnotationReader())));
    $config->setQueryCacheImpl(new \Doctrine\Common\Cache\ArrayCache());
    $config->setMetadataCacheImpl(new \Doctrine\Common\Cache\ArrayCache());
    $config->setEntityNamespaces(array(
      'VellocetSimpleBundle' => 'Vellocet\SimpleBundle\Entity'
    ));

    $params = array(
      'driver' => 'pdo_sqlite',
      'memory' => true,
    );
    
    $this->em =  \Doctrine\ORM\EntityManager::create($params, $config);  
           
    $schemaTool = new \Doctrine\ORM\Tools\SchemaTool($this->em);

    $classes = array(
       $this->em->getClassMetadata("\Vellocet\SimpleBundle\Entity\Category"),
       $this->em->getClassMetadata("\Vellocet\SimpleBundle\Entity\Item"),
    );

    // Clear and recreate the sqlite database after each test
    $schemaTool->dropSchema($classes);
    $schemaTool->createSchema($classes); 

    // It isn't ideal to hit the service container in a unit test
    // but the validator has the service container as one of its dependencies
    // so manually setting it up seems at the time somewhat impractical
    $this->client = static::createClient();
    $container = $this->client->getContainer();
    $this->validator = $container->get('validator');
  }
}
