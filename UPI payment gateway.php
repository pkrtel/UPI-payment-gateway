<?php

// Include the PHP Mailer library
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

// Set the UPI payment gateway API key
$api_key = "1234567890abcdefghijklmnopqrstuvwxyz";

// Set the email address of the administrator
$admin_email = "admin@example.com";

// Check if the payment form has been submitted
if (isset($_POST['submit'])) {
  // Get the payment details from the form
  $amount = $_POST['amount'];
  $name = $_POST['name'];
  $email = $_POST['email'];

  // Send the payment request to the UPI payment gateway
  $response = sendPaymentRequest($api_key, $amount, $name, $email);

  // Check if the payment was successful
  if ($response['success']) {
    // Send an email notification to the administrator
    $mail = new PHPMailer(true);
    try {
      // Set the email server and credentials
      $mail->SMTPDebug = 0;
      $mail->isSMTP();
      $mail->Host = 'smtp.example.com';
      $mail->SMTPAuth = true;
      $mail->Username = 'noreply@example.com';
      $mail->Password = 'password';
      $mail->SMTPSecure = 'tls';
      $mail->Port = 587;

      // Set the email details
      $mail->setFrom('noreply@example.com', 'Example Website');
      $mail->addAddress($admin_email, 'Administrator');
      $mail->isHTML(true);
      $mail->Subject = 'New Payment Received';
      $mail->Body = "A new payment of $amount has been received from $name ($email).";
      $mail->send();
    } catch (Exception $e) {
      // Display an error message if the email could not be sent
      echo "An error occurred while sending the email notification: " . $mail->ErrorInfo;
    }

    // Display a success message
    echo "Your payment of $amount was successful!";
  } else {
    // Display an error message if the payment failed
    echo "There was an error processing your payment: " . $response['error'];
  }
}

/**
 * Sends a payment request to the UPI payment gateway.
 *
 * @param string $api_key The API key for the UPI payment gateway.
 * @param float $amount The amount to be paid.
 * @param string $name The name of the payer.
 * @param string $email The email address of the payer.
 *
 * @return array An array containing the success status and any error message.
 */
function sendPaymentRequest($api_key, $amount, $name, $email) {
  // Set the API endpoint and request parameters
  $endpoint = "https://upi.example.com/pay";
