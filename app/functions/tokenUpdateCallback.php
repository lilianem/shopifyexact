<?php

/**
 * Callback function that sets values that expire and are refreshed by Connection.
 *
 * @param \Picqer\Financials\Exact\Connection $connection
 */
function tokenUpdateCallback(\Picqer\Financials\Exact\Connection $connection) {
    // Save the new tokens for next connections
    setValue('accesstoken', $connection->getAccessToken());
    setValue('refreshtoken', $connection->getRefreshToken());
    // Save expires time for next connections
    setValue('expires_in', $connection->getTokenExpires());
}