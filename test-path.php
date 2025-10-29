<?php
echo "DIR: " . __DIR__ . "<br>";
echo "File exists app/views/login.php: " . (file_exists(__DIR__ . '/app/views/login.php') ? 'YES' : 'NO') . "<br>";
echo "File exists ../app/views/login.php: " . (file_exists(__DIR__ . '/../app/views/login.php') ? 'YES' : 'NO') . "<br>";
echo "DOCUMENT_ROOT: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
echo "SCRIPT_FILENAME: " . $_SERVER['SCRIPT_FILENAME'] . "<br>";
