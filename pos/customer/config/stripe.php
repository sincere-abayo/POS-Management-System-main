<?php
// Load .env file
$dotenv = Dotenv\Dotenv::createImmutable('../../');
$dotenv->load();

return [
    'secret_key' => $_ENV['STRIPE_SECRET_KEY'],
    'publishable_key' => $_ENV['STRIPE_PUBLISHABLE_KEY'],
]; 