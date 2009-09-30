<?php
define('PROJECT_DIRECTORY', dirname($_SERVER['SCRIPT_FILENAME']));
define('PROJECT_URL', dirname($_SERVER['SCRIPT_NAME']));
define('RELATIVE_URL', str_replace(PROJECT_URL, '', $_SERVER['REQUEST_URI']));
