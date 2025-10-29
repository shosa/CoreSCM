<?php
echo "REQUEST_URI: " . $_SERVER['REQUEST_URI'] . "<br>";
echo "SCRIPT_NAME: " . $_SERVER['SCRIPT_NAME'] . "<br>";

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
echo "PARSED URI: " . $uri . "<br>";
