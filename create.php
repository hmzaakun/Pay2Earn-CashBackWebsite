<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require 'vendor/autoload.php';
include('includes/db.php');
session_start();
if (isset($_SESSION['email']))
 {
  $email = $_SESSION['email'];
}

// This is your test secret API key.
\Stripe\Stripe::setApiKey('sk_test_51Klh64Lgsqs3lgtnsOVqBxLPyB9bxjYciruLW93788bDmsZavou8Es1wWs8VXr6zEP0HvqiICtF8xojnVIGObv5b00tGQ1NsAh');
$cacheck = $db->prepare("SELECT ca FROM entreprise WHERE email = ?");
$cacheck->execute([$email]);
$cacheck = $cacheck->fetch();

function calculateCotisation($cacheck){
  $ca=$cacheck[0];
  /*
  Moins de 200 000 €  Gratuit
Entre 200 000 € et 800 000 € 0,8% du chiffre d’affaires annuel
Entre 800000 € et 1500000 € 0,6 % du chiffre d’affaires annuel
Entre 1500000 € et 3000 000 € 0,4 % du chiffre d’affaires annuel
Au-delà de 3000000 € 0,3 % du chiffre d’affaires annuel
*/
if($ca<200000){
  $cotisation = 0;
}
if($ca>=200000 && $ca<800000 ){
  $cotisation = $ca*0.008;
}
if($ca>=800000 && $ca<1500000 ){
  $cotisation = $ca*0.006;
}
if($ca>=1500000 && $ca<3000000 ){
  $cotisation = $ca*0.004;
}
if($ca>=3000000){
  $cotisation = $ca*0.003;
}


//convertion  *100

    return $cotisation*100;
}


header('Content-Type: application/json');

try {
    // retrieve JSON from POST body
    $jsonStr = file_get_contents('php://input');
    $jsonObj = json_decode($jsonStr);

    // Create a PaymentIntent with amount and currency
    $paymentIntent = \Stripe\PaymentIntent::create([
        'amount' => calculateCotisation($cacheck),
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
