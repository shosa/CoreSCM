<?php

namespace App\Core;

use Illuminate\Database\Capsule\Manager as Capsule;

/**
 * Database Connection Manager usando Eloquent
 */
class Database
{
    private static $capsule;

    /**
     * Inizializza la connessione database
     */
    public static function init()
    {
        if (self::$capsule !== null) {
            return self::$capsule;
        }

        $config = require __DIR__ . '/../../config/database.php';

        self::$capsule = new Capsule;
        self::$capsule->addConnection($config);
        self::$capsule->setAsGlobal();
        self::$capsule->bootEloquent();

        return self::$capsule;
    }

    /**
     * Ottieni istanza PDO
     */
    public static function getPdo()
    {
        return self::$capsule->getConnection()->getPdo();
    }
}
