<?php

function authorizer()
{
    $connection = new \Picqer\Financials\Exact\Connection();
    $connection->setBaseUrl('https://start.exactonline.be');
    $connection->setRedirectUrl(env('CALLBACK_URL'));
    $connection->setExactClientId(env('CLIENT_ID'));
    $connection->setExactClientSecret(env('CLIENT_SECRET'));
    $connection->redirectForAuthorization();
}