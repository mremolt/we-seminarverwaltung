<?php

namespace library;
use \PDO;

/**
 * Repräsentiert eine Datenbankverbindung
 *
 * Diese Klasse stellt eine Variante des Singleton-Design-Patterns dar, da nicht ein
 * Exemplar der Klasse selbst, sondern ein PDO-Objekt zurückgegeben wird.
 *
 * @author Marc Remolt <m.remolt@webmasters.de>
 * @package library
 */
final class Database
{
    /**
     * @var PDO
     */
    private static $db = null;

    /**
     * Verhindere, dass von Database ein Exemplar erzeugt werden kann.
     */
    private function  __construct()
    {

    }

    /**
     * Gibt die Datenbankverbindung (PDO) zurück.
     * 
     * @return PDO
     */
    public static function getInstance()
    {
        if (! static::$db instanceof PDO) {
            $dsn     = DB_DSN;
            $user    = DB_USER;
            $pass    = DB_PASS;
            $options = array(
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_PERSISTENT         => true,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            );
            static::$db = new PDO($dsn, $user, $pass, $options);
            static::$db->query('SET NAMES utf8');
        }
        return static::$db;
    }
}