<?php
/**
 * Autoload class function
 */
include 'config.php';
function __autoload($class)
{
    $path = "../../app/lib/{$class}.php";
    if (file_exists($path))
    {
        require_once ($path);
    }
    elseif (file_exists($path = "../../app/models/{$class}.php"))
    {
        require_once ($path);
    }
    else
    {
        die ("File {$class} not found!");
    }
}

function dump($data)
{
    echo '<pre>';
    var_dump($data);
    echo '</pre>';
}