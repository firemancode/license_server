<?php
/**
 * Example script to generate HMAC signature for license verification
 * 
 * This script shows how clients should generate the X-Signature header
 * for the /api/verify-license endpoint.
 */

// Configuration
$secretKey = 'egGGWWCxz/EtImJl7FPtcDkXvcZB/5Hlv+pkiS54QdY='; // Should match LICENSE_SECRET in .env
$licenseKey = 'your-license-key-here';
$domain = 'example.com'; // Optional, can be empty string
$timestamp = time(); // Current Unix timestamp

// Create the data string for HMAC
$dataString = $licenseKey . $domain . $timestamp;

// Generate HMAC SHA-256 signature
$signature = hash_hmac('sha256', $dataString, $secretKey);

echo "=== License Verification Signature Generator ===\n";
echo "License Key: {$licenseKey}\n";
echo "Domain: {$domain}\n";
echo "Timestamp: {$timestamp}\n";
echo "Data String: {$dataString}\n";
echo "Secret Key: {$secretKey}\n";
echo "Generated Signature: {$signature}\n\n";

echo "=== cURL Example ===\n";
echo "curl -X POST http://localhost/api/verify-license \\\n";
echo "  -H \"Content-Type: application/json\" \\\n";
echo "  -H \"X-Signature: {$signature}\" \\\n";
echo "  -H \"X-Timestamp: {$timestamp}\" \\\n";
echo "  -d '{\"license_key\":\"{$licenseKey}\",\"domain\":\"{$domain}\"}'\n\n";

echo "=== JavaScript Example ===\n";
echo "const crypto = require('crypto');\n\n";
echo "const secretKey = '{$secretKey}';\n";
echo "const licenseKey = '{$licenseKey}';\n";
echo "const domain = '{$domain}';\n";
echo "const timestamp = Math.floor(Date.now() / 1000);\n";
echo "const dataString = licenseKey + domain + timestamp;\n";
echo "const signature = crypto.createHmac('sha256', secretKey).update(dataString).digest('hex');\n\n";
echo "fetch('http://localhost/api/verify-license', {\n";
echo "  method: 'POST',\n";
echo "  headers: {\n";
echo "    'Content-Type': 'application/json',\n";
echo "    'X-Signature': signature,\n";
echo "    'X-Timestamp': timestamp.toString()\n";
echo "  },\n";
echo "  body: JSON.stringify({ license_key: licenseKey, domain: domain })\n";
echo "});\n";
?>