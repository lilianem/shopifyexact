<?php

/**
 * Function to retrieve persisted data for the example
 * @param string $key
 * @return null|string
 */
function getValue($key)
{
    if (file_exists ( 'storage.json' ))
    {
        $storage = json_decode(file_get_contents('storage.json'), true);
        if (array_key_exists($key, $storage)) {
            return $storage[$key];
        }
        return null;
    }    
}