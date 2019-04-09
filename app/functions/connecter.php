<?php

/**
 * Function to connect to Exact, this creates the client and automatically retrieves oAuth tokens if needed
 *
 * @return \Picqer\Financials\Exact\Connection
 * @throws Exception
 */
function connecter()
{
    $connection = new \Picqer\Financials\Exact\Connection();
    $connection->setBaseUrl('https://start.exactonline.be');
    $connection->setRedirectUrl(env('CALLBACK_URL'));
    $connection->setExactClientId(env('CLIENT_ID'));
    $connection->setExactClientSecret(env('CLIENT_SECRET'));    
    // Retrieves authorizationcode from database
    if (getValue('authorizationcode')) {
        $connection->setAuthorizationCode(getValue('authorizationcode'));
    }
    // Retrieves accesstoken from database
    if (getValue('accesstoken')) {
        $connection->setAccessToken(getValue('accesstoken'));
    }
    // Retrieves refreshtoken from database
    if (getValue('refreshtoken')) {
        $connection->setRefreshToken(getValue('refreshtoken'));
    }
    // Retrieves expires timestamp from database
    if (getValue('expires_in')) {
        $connection->setTokenExpires(getValue('expires_in'));
    }
    // Set callback to save newly generated tokens
    $connection->setTokenUpdateCallback('tokenUpdateCallback');
    // Make the client connect and exchange tokens    
    try {
        $connection->connect();
    } catch (\Exception $e) {
        throw new Exception('Could not connect to Exact: ' . $e->getMessage());
    }    
    return $connection;
}