<?php

namespace App\Infrastructure\Doctrine;

use Doctrine\ORM\ORMSetup;
use Doctrine\ORM\EntityManager;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\Mapping\Driver\XmlDriver;
use Doctrine\Persistence\Mapping\Driver\MappingDriverChain;

final class EntityManagerFactory
{
    public static function create(): EntityManager
    {
        $mappingPath = __DIR__ . '/../../Foundation/Persistence/Mapping';

        $xmlDriver = new XmlDriver(
            new \Doctrine\Persistence\Mapping\Driver\DefaultFileLocator(
                [$mappingPath],
                '.xml'
            )
        );

        $config = ORMSetup::createConfiguration(isDevMode: true);
        $config->setMetadataDriverImpl($xmlDriver);
        $config->setAutoGenerateProxyClasses(true);

        $connection = DriverManager::getConnection([
            'dbname'   => 'gymfly',
            'user'     => 'root',
            'password' => '',
            'host'     => 'localhost',
            'driver'   => 'pdo_mysql',
            'charset'  => 'utf8mb4',
        ], $config);

        return new EntityManager($connection, $config);
    }
}