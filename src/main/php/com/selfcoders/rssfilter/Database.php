<?php
namespace com\selfcoders\rssfilter;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;

class Database
{
    private static ?EntityManager $entityManager = null;

    public static function init()
    {
        $connection = [
            "driver" => "pdo_mysql",
            "host" => getenv("DATABASE_HOST"),
            "dbname" => getenv("DATABASE_NAME"),
            "user" => getenv("DATABASE_USERNAME"),
            "password" => getenv("DATABASE_PASSWORD")
        ];

        $config = Setup::createAnnotationMetadataConfiguration([SRC_ROOT], useSimpleAnnotationReader: false);
        self::$entityManager = EntityManager::create($connection, $config);
    }

    public static function getEntityManager(): EntityManager
    {
        if (self::$entityManager === null) {
            self::init();
        }

        return self::$entityManager;
    }
}