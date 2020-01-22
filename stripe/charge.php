<?php
namespace Stripe;

require_once('config.php');
require_once('init.php');

$token  = $_POST['stripeToken'];

\Stripe\Stripe::setApiKey($stripe['secret_key']);

$customer = \Stripe\Customer::create(array(
  'email' => 'customer@example.com',
  'source'  => $token
));

$charge = \Stripe\Charge::create(array(
  'customer' => $customer->id,
  'amount'   => 5000,
  'currency' => 'usd'
));

echo "<pre>".print_r($_POST, true)."</pre>";
echo "<pre>".print_r($customer, true)."</pre>";
echo "<pre>".print_r($charge, true)."</pre>";
?>
