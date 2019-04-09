<?php

/**
 * Function to persist some data for the example
 * @param string $key
 * @param string $value
 */
function setValue($key, $value)
{
    if (file_exists ( 'storage.json' ))
    {
        $storage = json_decode(file_get_contents('storage.json'), true);
        $storage[$key] = $value;
        file_put_contents('storage.json', json_encode($storage));
    }
    else
    {
        $storage[$key] = $value;
        file_put_contents('storage.json', json_encode($storage));
    }
}