<?php
define('PROJECT_DIRECTORY', dirname(dirname(__FILE__)));
define('PROJECT_URL', dirname($_SERVER['SCRIPT_NAME']));
define('RELATIVE_URL', str_replace(PROJECT_URL, '', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ));
