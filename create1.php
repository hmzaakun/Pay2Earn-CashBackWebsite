<?php

require 'vendor/autoload.php';

// This is your test secret API key.
\Stripe\Stripe::setApiKey('sk_test_51Klh64Lgsqs3lgtnsOVqBxLPyB9bxjYciruLW93788bDmsZavou8Es1wWs8VXr6zEP0HvqiICtF8xojnVIGObv5b00tGQ1NsAh');

function calculateOrderAmount($oui){


    return $oui->items[0]->price;
}

header('Content-Type: application/json');
//$oui = json_decode('{"items":[{"price":123456}]}');
//var_dump($oui->items[0]->price);

try {
    // retrieve JSON from POST body
    $jsonStr = file_get_contents('php://input');
    $jsonObj = json_decode($jsonStr);

    // Create a PaymentIntent with amount and currency
    $paymentIntent = \Stripe\PaymentIntent::create([
        'amount' => calculateOrderAmount($jsonObj),
        'currency' => 'eur',
        'automatic_payment_methods' => [
            'enabled' => true,
        ],
    ]);

    $output = [
        'clientSecret' => $paymentIntent->client_secret,
    ];

    echo json_encode($output);
} catch (Error $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}