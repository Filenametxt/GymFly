<?php

namespace App\Infrastructure\Doctrine;

use Doctrine\ORM\ORMSetup;
use Doctrine\ORM\EntityManager;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\Mapping\Driver\XmlDriver;

use App\Foundation\Persistence\Type\StringableTimeImmutableType;
use Doctrine\DBAL\Types\Type;

final class EntityManagerFactory
{
    public static function create(): EntityManager
    {
        // Override time_immutable to return stringable DateTimeImmutable objects
        Type::overrideType('time_immutable', StringableTimeImmutableType::class);

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