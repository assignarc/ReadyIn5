<?php

require_once '../vendor/autoload.php';
require_once 'secrets.php';

$stripe = new \Stripe\StripeClient($stripeSecretKey);

function calculateTax($stripe, $items, $currency) {
    $taxCalculation = $stripe->tax->calculations->create([
        'currency' => $currency,
        'customer_details' => [
            'address' => [
                'line1' => '920 5th Ave',
                'city' => 'Seattle',
                'state' => 'WA',
                'postal_code' => '98104',
                'country' => 'US',
            ],
            'address_source' => 'shipping',
        ],
        'line_items' => array_map('buildLineItem', $items),
    ]);

    return $taxCalculation;
}

function buildLineItem($item) {
    return [
        'amount' => $item->amount, // Amount in cents
        'reference' => $item->id, // Unique reference for the item in the scope of the calculation
    ];
}
// Securely calculate the order amount, including tax
function calculateOrderAmount($items, $taxCalculation) {
    // Replace this constant with a calculation of the order's amount
    // Calculate the order total with any exclusive taxes on the server to prevent
    // people from directly manipulating the amount on the client
    $orderAmount = 1400;
    $orderAmount += $taxCalculation->tax_amount_exclusive;
    return $orderAmount;
}

header('Content-Type: application/json');

try {
    // retrieve JSON from POST body
    $jsonStr = file_get_contents('php://input');
    $jsonObj = json_decode($jsonStr);

    // Create a Tax Calculation for the items being sold
    $taxCalculation = calculateTax($stripe, $jsonObj->items, 'usd');

    // Create a PaymentIntent with amount and currency
    $paymentIntent = $stripe->paymentIntents->create([
        'amount' => calculateOrderAmount($jsonObj->items, $taxCalculation),
        'currency' => 'usd',
        // In the latest version of the API, specifying the `automatic_payment_methods` parameter is optional because Stripe enables its functionality by default.
        'automatic_payment_methods' => [
            'enabled' => true,
        ],
        'metadata' => [
            'tax_calculation' => $taxCalculation->id
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

// Invoke this method in your webhook handler when `payment_intent.succeeded` webhook is received
function handlePaymentIntentSucceeded($stripe, $paymentIntent) {
    // Create a Tax Transaction for the successful payment
    $stripe->tax->transactions->createFromCalculation([
        "calculation" => $paymentIntent->metadata['tax_calculation'],
        "reference" => "myOrder_123", // Replace with a unique reference from your checkout/order system
    ]);
}