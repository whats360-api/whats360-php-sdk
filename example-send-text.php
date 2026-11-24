<?php
// WHATS360_TOKEN=... WHATS360_INSTANCE_ID=... php example-send-text.php 201234567890 "Hello"
// Sends ONE real message. Use your own number first.
require __DIR__ . '/Client.php';
$c = new Whats360\Client(getenv('WHATS360_TOKEN'), getenv('WHATS360_INSTANCE_ID'));
print_r($c->sendText($argv[1] . '@s.whatsapp.net', $argv[2]));
