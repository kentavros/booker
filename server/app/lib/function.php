<?php
include 'config.php';
/**
 * Autoload class function
 */
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

/**
 * For my test application, find sent param
 * and error in app
 * @param $data
 */
function dump($data)
{
    echo '<pre>';
    var_dump($data);
    echo '</pre>';
    exit();
}