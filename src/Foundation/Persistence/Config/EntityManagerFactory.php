<?php

namespace App\Foundation\Persistence\Config;

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

        $mappingPath = __DIR__ . '/../Mapping';

        $xmlDriver = new XmlDriver(
            new \Doctrine\Persistence\Mapping\Driver\DefaultFileLocator(
                [$mappingPath],
                '.xml'
            )
        );

        $config = ORMSetup::createConfiguration(isDevMode: true);
        $config->setMetadataDriverImpl($xmlDriver);
        $config->setAutoGenerateProxyClasses(true);

        // Controlliamo se su Render è presente la variabile DATABASE_URL
        $databaseUrl = $_ENV['DATABASE_URL'] ?? getenv('DATABASE_URL');

        if ($databaseUrl) {
            // ONLINE SU RENDER (Usa PostgreSQL)
            $dbparams = parse_url($databaseUrl);

            $connectionParams = [
                'driver' => 'pdo_pgsql', // Cambia il driver in PostgreSQL per Render!
                'user' => $dbparams['user'],
                'password' => $dbparams['pass'] ?? '',
                'host' => $dbparams['host'],
                'port' => $dbparams['port'] ?? 5432,
                'dbname' => ltrim($dbparams['path'], '/'),
                'charset' => 'utf8',
            ];
        } else {
            // IN LOCALE SUL TUO PC (Usa il tuo vecchio MySQL/XAMPP di sempre)
            $connectionParams = [
                'dbname' => 'gymfly',
                'user' => 'root',
                'password' => '',
                'host' => 'localhost',
                'driver' => 'pdo_mysql',
                'charset' => 'utf8mb4',
            ];
        }

        $connection = DriverManager::getConnection($connectionParams, $config);

        return new EntityManager($connection, $config);
    }
}
