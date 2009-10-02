<?php

namespace library;
use models\Benutzer;

/**
 * Klasse, die sich um die Authentifizierung von Benutzern kümmert
 *
 * @author Marc Remolt <m.remolt@webmasters.de>
 * @package library
 */
class Auth
{
    /**
     * Diese Klasse enthält nur statische Methoden, also macht new keinen Sinn
     */
    private function  __construct()
    {

    }

    /**
     * Authentifiziert einen Benutzer
     *
     * @param string $email
     * @param string $password
     * @return boolean
     */
    public static function authenticate($email, $password)
    {
        $correct = False;
        
        $email = '"'. addslashes($email) . '"';
        $benutzer = Benutzer::findBy('email', $email);
        if ($benutzer) {
            $benutzer = $benutzer[0];
            if ($benutzer->validatePasswort($password)) {
                $correct = True;
                $_SESSION['logged_in'] = intval($benutzer->getId());
            }
        }
        return $correct;
    }

    /**
     * Gibt zurück, ob ein Benutzer eingeloggt ist
     *
     * @return boolean
     */
    public static function isLoggedIn()
    {
        if (! array_key_exists('logged_in', $_SESSION)) {
            $_SESSION['logged_in'] = null;
        }
        return $_SESSION['logged_in'] > 0;
    }

    /**
     * Hiermit kann ein Codeblock mit einer Authentifizierung geschützt werden
     */
    public static function requireLogin()
    {
        if (! static::isLoggedIn()) {
            $errorUrl = Router::getInstance()->getUrlFor('error', 'forbidden');
            header('Location: ' . $errorUrl);
            exit();
        }
    }

    /**
     * Gibt den eingeloggten Benutzer zurück
     *
     * @return Benutzer
     */
    public static function getLoggedInBenutzer()
    {
        if (static::isLoggedIn()) {
            $benutzer = Benutzer::find($_SESSION['logged_in']);
        } else {
            $benutzer = new Benutzer();
        }
        return $benutzer;
    }

    /**
     * Loggt den Benutzer aus
     */
    public static function logout()
    {
        $_SESSION['logged_in'] = null;
    }
}