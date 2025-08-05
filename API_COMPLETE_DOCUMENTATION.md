# 🔌 License Server API Documentation

Complete API documentation for the License Server system with examples, error codes, and integration guides.

## 📋 Table of Contents
- [Base Information](#base-information)
- [Authentication](#authentication)
- [Endpoints](#endpoints)
- [Error Codes](#error-codes)
- [Integration Examples](#integration-examples)
- [Rate Limiting](#rate-limiting)
- [Best Practices](#best-practices)

## 🌐 Base Information

### Base URL
```
http://localhost/license-server/api/
```

### Content Type
All requests must include:
```
Content-Type: application/json
```

### Rate Limiting
- **Standard endpoints**: No limit
- **Critical endpoints** (`/activate-license`, `/deactivate-license`, `/verify-license`): 10 requests/minute per IP

## 🔐 Authentication

### Basic Authentication
Most endpoints require no authentication for license validation.

### Admin API (Optional)
For admin operations, use Sanctum token:
```
Authorization: Bearer YOUR_TOKEN
```

### HMAC Signature (Optional)
For enhanced security, enable signature verification:
```php
// In .env
LICENSE_SECRET=your-secret-key-for-hmac-verification
```

## 🎯 API Endpoints

### 1. License Verification

**Purpose**: Validate if a license is active and optionally check domain activation.

**Endpoint**: `POST /verify-license`

**Request**:
```json
{
    "license_key": "ABCDE-12345-FGHIJ",
    "domain": "example.com" // Optional
}
```

**Success Response** (200):
```json
{
    "status": "valid",
    "message": "License is valid and active",
    "valid": true,
    "license": {
        "id": 1,
        "license_key": "ABCDE-12345-FGHIJ",
        "status": "active",
        "expires_at": "2025-08-05T12:00:00.000000Z",
        "product": {
            "id": 1,
            "name": "Plugin WP Pro",
            "slug": "plugin-wp-pro"
        }
    },
    "activation": {
        "is_activated": true,
        "activated_at": "2024-08-05T10:30:00.000000Z"
    }
}
```

**Invalid License Response** (404):
```json
{
    "status": "invalid",
    "message": "License key not found",
    "valid": false
}
```

**Expired License Response** (200):
```json
{
    "status": "invalid",
    "message": "License has expired and has been automatically disabled",
    "valid": false,
    "expired_at": "2024-07-01T12:00:00.000000Z",
    "license_status": "expired"
}
```

---

### 2. License Activation

**Purpose**: Activate a license for a specific domain.

**Endpoint**: `POST /activate-license`  
**Rate Limit**: 10 requests/minute per IP

**Request**:
```json
{
    "license_key": "ABCDE-12345-FGHIJ",
    "domain": "example.com"
}
```

**Success Response** (201):
```json
{
    "status": "success",
    "message": "License activated successfully for domain",
    "activation": {
        "id": 1,
        "domain": "example.com",
        "ip_address": "192.168.1.100",
        "activated_at": "2024-08-05T10:30:00.000000Z"
    },
    "license": {
        "license_key": "ABCDE-12345-FGHIJ",
        "status": "active",
        "product_name": "Plugin WP Pro",
        "total_activations": 1,
        "max_activations": 1
    }
}
```

**Domain Already Activated** (409):
```json
{
    "status": "error",
    "message": "Domain is already activated for this license",
    "activation": {
        "domain": "example.com",
        "activated_at": "2024-08-01T10:00:00.000000Z",
        "ip_address": "192.168.1.50"
    }
}
```

**Cross-License Conflict** (409):
```json
{
    "status": "error",
    "message": "Domain is already activated for another license",
    "conflict": {
        "domain": "example.com",
        "activated_at": "2024-08-01T10:00:00.000000Z",
        "conflicting_license": "OTHER-12345-LICNS"
    }
}
```

**Activation Limit Reached** (429):
```json
{
    "status": "error",
    "message": "Maximum activation limit reached (1 activations allowed)",
    "current_activations": 1,
    "max_activations": 1,
    "existing_activations": [
        {
            "domain": "existing-domain.com",
            "activated_at": "2024-08-01T10:00:00.000000Z",
            "ip_address": "192.168.1.50"
        }
    ]
}
```

---

### 3. License Deactivation

**Purpose**: Remove domain activation from a license.

**Endpoint**: `POST /deactivate-license`  
**Rate Limit**: 10 requests/minute per IP

**Request**:
```json
{
    "license_key": "ABCDE-12345-FGHIJ",
    "domain": "example.com"
}
```

**Success Response** (200):
```json
{
    "status": "success",
    "message": "License deactivated successfully for domain",
    "deactivated_activation": {
        "domain": "example.com",
        "activated_at": "2024-08-05T10:30:00.000000Z",
        "ip_address": "192.168.1.100"
    },
    "license": {
        "license_key": "ABCDE-12345-FGHIJ",
        "status": "active",
        "product_name": "Plugin WP Pro"
    }
}
```

**No Activation Found** (404):
```json
{
    "status": "error",
    "message": "No activation found for this domain and license"
}
```

---

### 4. Admin API - Reset Activations

**Purpose**: Admin endpoint to reset all activations for a license.

**Endpoint**: `POST /api/admin/reset-activations`  
**Authentication**: Required (Sanctum token)

**Request**:
```json
{
    "license_key": "ABCDE-12345-FGHIJ"
}
```

**Success Response** (200):
```json
{
    "status": "success",
    "message": "Successfully reset 2 activation(s) for license ABCDE-12345-FGHIJ",
    "license_key": "ABCDE-12345-FGHIJ",
    "deleted_activations_count": 2,
    "deleted_activations": [
        {
            "domain": "example.com",
            "ip_address": "192.168.1.100",
            "activated_at": "2024-08-05T10:30:00.000000Z"
        },
        {
            "domain": "test.com",
            "ip_address": "192.168.1.101",
            "activated_at": "2024-08-05T11:30:00.000000Z"
        }
    ],
    "reset_at": "2024-08-05T12:00:00.000000Z"
}
```

## ⚠️ Error Codes

### HTTP Status Codes

| Code | Status | Meaning |
|------|--------|---------|
| 200 | OK | Success (may include invalid license info) |
| 201 | Created | Activation successful |
| 400 | Bad Request | Invalid input or license inactive |
| 404 | Not Found | License or activation not found |
| 409 | Conflict | Domain already activated |
| 422 | Unprocessable Entity | Validation failed |
| 429 | Too Many Requests | Rate limit exceeded |
| 500 | Internal Server Error | Server error |

### Common Error Responses

**Validation Error** (422):
```json
{
    "status": "error",
    "message": "Validation failed",
    "errors": {
        "license_key": ["The license key field is required."],
        "domain": ["The domain field is required."]
    }
}
```

**Rate Limit Exceeded** (429):
```json
{
    "message": "Too Many Attempts.",
    "exception": "Illuminate\\Http\\Exceptions\\ThrottleRequestsException"
}
```

**License Inactive** (400):
```json
{
    "status": "error",
    "message": "License is not active",
    "license_status": "disabled"
}
```

## 💡 Integration Examples

### PHP Example
```php
<?php

class LicenseClient {
    private $baseUrl;
    
    public function __construct($baseUrl) {
        $this->baseUrl = rtrim($baseUrl, '/');
    }
    
    public function verifyLicense($licenseKey, $domain = null) {
        $data = ['license_key' => $licenseKey];
        if ($domain) {
            $data['domain'] = $domain;
        }
        
        return $this->makeRequest('/verify-license', $data);
    }
    
    public function activateLicense($licenseKey, $domain) {
        return $this->makeRequest('/activate-license', [
            'license_key' => $licenseKey,
            'domain' => $domain
        ]);
    }
    
    public function deactivateLicense($licenseKey, $domain) {
        return $this->makeRequest('/deactivate-license', [
            'license_key' => $licenseKey,
            'domain' => $domain
        ]);
    }
    
    private function makeRequest($endpoint, $data) {
        $url = $this->baseUrl . '/api' . $endpoint;
        
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'User-Agent: MyApp/1.0'
            ],
            CURLOPT_TIMEOUT => 30
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        return [
            'http_code' => $httpCode,
            'data' => json_decode($response, true)
        ];
    }
}

// Usage
$client = new LicenseClient('http://localhost/license-server');

// Verify license
$result = $client->verifyLicense('ABCDE-12345-FGHIJ', 'example.com');
if ($result['http_code'] === 200 && $result['data']['valid']) {
    echo "License is valid!";
} else {
    echo "License is invalid: " . $result['data']['message'];
}

// Activate license
$result = $client->activateLicense('ABCDE-12345-FGHIJ', 'example.com');
if ($result['http_code'] === 201) {
    echo "License activated successfully!";
} else {
    echo "Activation failed: " . $result['data']['message'];
}
```

### JavaScript Example
```javascript
class LicenseClient {
    constructor(baseUrl) {
        this.baseUrl = baseUrl.replace(/\/$/, '');
    }
    
    async verifyLicense(licenseKey, domain = null) {
        const data = { license_key: licenseKey };
        if (domain) data.domain = domain;
        
        return await this.makeRequest('/verify-license', data);
    }
    
    async activateLicense(licenseKey, domain) {
        return await this.makeRequest('/activate-license', {
            license_key: licenseKey,
            domain: domain
        });
    }
    
    async deactivateLicense(licenseKey, domain) {
        return await this.makeRequest('/deactivate-license', {
            license_key: licenseKey,
            domain: domain
        });
    }
    
    async makeRequest(endpoint, data) {
        try {
            const response = await fetch(`${this.baseUrl}/api${endpoint}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'User-Agent': 'MyApp/1.0'
                },
                body: JSON.stringify(data)
            });
            
            const jsonData = await response.json();
            
            return {
                http_code: response.status,
                data: jsonData
            };
        } catch (error) {
            return {
                http_code: 0,
                error: error.message
            };
        }
    }
}

// Usage
const client = new LicenseClient('http://localhost/license-server');

// Verify license
client.verifyLicense('ABCDE-12345-FGHIJ', 'example.com')
    .then(result => {
        if (result.http_code === 200 && result.data.valid) {
            console.log('License is valid!');
        } else {
            console.log('License is invalid:', result.data.message);
        }
    });

// Activate license
client.activateLicense('ABCDE-12345-FGHIJ', 'example.com')
    .then(result => {
        if (result.http_code === 201) {
            console.log('License activated successfully!');
        } else {
            console.log('Activation failed:', result.data.message);
        }
    });
```

### cURL Examples
```bash
# Verify license
curl -X POST http://localhost/license-server/api/verify-license \
  -H "Content-Type: application/json" \
  -d '{"license_key": "ABCDE-12345-FGHIJ", "domain": "example.com"}'

# Activate license  
curl -X POST http://localhost/license-server/api/activate-license \
  -H "Content-Type: application/json" \
  -d '{"license_key": "ABCDE-12345-FGHIJ", "domain": "example.com"}'

# Deactivate license
curl -X POST http://localhost/license-server/api/deactivate-license \
  -H "Content-Type: application/json" \
  -d '{"license_key": "ABCDE-12345-FGHIJ", "domain": "example.com"}'

# Admin reset (with token)
curl -X POST http://localhost/license-server/api/admin/reset-activations \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{"license_key": "ABCDE-12345-FGHIJ"}'
```

## 🚦 Rate Limiting

### Default Limits
- **Standard endpoints**: Unlimited
- **Critical endpoints**: 10 requests/minute per IP
  - `/activate-license`
  - `/deactivate-license` 
  - `/verify-license` (with signature verification)

### Rate Limit Headers
When rate limited, responses include:
```
X-RateLimit-Limit: 10
X-RateLimit-Remaining: 0
Retry-After: 60
```

### Handling Rate Limits
```php
// PHP example with retry logic
function makeRequestWithRetry($client, $endpoint, $data, $maxRetries = 3) {
    for ($i = 0; $i < $maxRetries; $i++) {
        $result = $client->makeRequest($endpoint, $data);
        
        if ($result['http_code'] !== 429) {
            return $result;
        }
        
        // Rate limited, wait and retry
        $retryAfter = $result['data']['retry_after'] ?? 60;
        sleep($retryAfter);
    }
    
    throw new Exception('Rate limit exceeded after retries');
}
```

## 🎯 Best Practices

### 1. Error Handling
Always check both HTTP status code and response data:
```php
$result = $client->verifyLicense($key, $domain);

// Check HTTP status first
if ($result['http_code'] >= 400) {
    // Handle HTTP errors
    handleError($result);
    return;
}

// Check application-level status
if (!$result['data']['valid']) {
    // License is invalid
    handleInvalidLicense($result['data']);
    return;
}

// License is valid
handleValidLicense($result['data']);
```

### 2. Caching
Cache license verification results to reduce API calls:
```php
// Cache for 5 minutes
$cacheKey = "license_{$licenseKey}_{$domain}";
$cached = cache()->get($cacheKey);

if (!$cached) {
    $result = $client->verifyLicense($licenseKey, $domain);
    cache()->put($cacheKey, $result, 300); // 5 minutes
    return $result;
}

return $cached;
```

### 3. Retry Logic
Implement exponential backoff for rate-limited requests:
```php
function makeRequestWithBackoff($client, $endpoint, $data) {
    $maxRetries = 3;
    $baseDelay = 1; // seconds
    
    for ($i = 0; $i < $maxRetries; $i++) {
        $result = $client->makeRequest($endpoint, $data);
        
        if ($result['http_code'] !== 429) {
            return $result;
        }
        
        // Exponential backoff: 1s, 2s, 4s
        $delay = $baseDelay * pow(2, $i);
        sleep($delay);
    }
    
    throw new Exception('Max retries exceeded');
}
```

### 4. Security
- Always use HTTPS in production
- Validate SSL certificates
- Store API credentials securely
- Implement request signing for sensitive operations

### 5. Monitoring
Track API usage in your application:
```php
// Log API calls for monitoring
function logApiCall($endpoint, $licenseKey, $result) {
    Log::info('License API Call', [
        'endpoint' => $endpoint,
        'license_key' => $licenseKey,
        'http_code' => $result['http_code'],
        'status' => $result['data']['status'] ?? 'unknown',
        'timestamp' => now()
    ]);
}
```

## 🔧 Testing & Debugging

### Test Endpoints
```bash
# Health check (if implemented)
curl http://localhost/license-server/api/health

# Database test
curl http://localhost/license-server/test_mysql.php
```

### Debug Mode
Enable in development:
```env
APP_DEBUG=true
LOG_LEVEL=debug
```

### Common Issues
1. **CORS errors**: Add domain to allowed origins
2. **Rate limits**: Implement proper retry logic
3. **SSL issues**: Verify certificate configuration
4. **Timeouts**: Increase timeout values for slow networks

---

**For more examples and integration guides, visit the admin panel at `/admin/dashboard` or check the main README.md file.**
 