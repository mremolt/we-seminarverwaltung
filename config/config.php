<?php

// der Dateisystem-Ordner, in dem das Projekt liegt
define('PROJECT_DIRECTORY', dirname(dirname(__FILE__)));

// die ROOT-URL, unter der das Projekt zu erreichen ist
define('PROJECT_URL', dirname($_SERVER['SCRIPT_NAME']));

// die URL relativ zur PROJECT_URL
define('RELATIVE_URL', str_replace(PROJECT_URL, '', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ));

// die DB-Verbindungsdaten
define('DB_DSN', 'mysql:host=127.0.0.1;dbname=seminarverwaltung');
define('DB_USER', 'root');
define('DB_PASS', '');

// how many Elements should be displayed per page
define('ELEMENTS_PER_PAGE', 5);
