# License API Documentation

Base URL: `http://your-domain.com/api`

## Endpoints

**Note**: Each endpoint has two available paths for flexibility:
- Grouped: `/api/license/{action}`
- Direct: `/api/{action}-license`

### 1. Verify License
**POST** `/api/license/verify` or **POST** `/api/verify-license`

Memverifikasi apakah license key valid dan aktif.

> **Note**: Route `/api/verify-license` memerlukan signature verification menggunakan HMAC SHA-256.

**Required Headers for `/api/verify-license`:**
- `X-Signature`: HMAC SHA-256 dari `license_key + domain + timestamp`
- `X-Timestamp`: Unix timestamp (maksimal 5 menit yang lalu)

**Request Body:**
```json
{
    "license_key": "your-license-key",
    "domain": "example.com" // optional
}
```

**Success Response (200):**
```json
{
    "status": "valid",
    "message": "License is valid and active",
    "valid": true,
    "license": {
        "id": 1,
        "license_key": "your-license-key",
        "status": "active",
        "expires_at": "2024-12-31T23:59:59.000000Z",
        "product": {
            "id": 1,
            "name": "My Product",
            "slug": "my-product"
        }
    },
    "activation": {
        "is_activated": true,
        "activated_at": "2024-01-01T10:00:00.000000Z"
    }
}
```

**Error Response (404):**
```json
{
    "status": "invalid",
    "message": "License key not found",
    "valid": false
}
```

### 2. Activate License
**POST** `/api/license/activate` or **POST** `/api/activate-license`

Mengaktifkan license untuk domain tertentu.

**Request Body:**
```json
{
    "license_key": "your-license-key",
    "domain": "example.com"
}
```

**Success Response (201):**
```json
{
    "status": "success",
    "message": "License activated successfully for domain",
    "activation": {
        "id": 1,
        "domain": "example.com",
        "ip_address": "192.168.1.1",
        "activated_at": "2024-01-01T10:00:00.000000Z"
    },
    "license": {
        "license_key": "your-license-key",
        "status": "active",
        "product_name": "My Product"
    }
}
```

**Error Response (409):**
```json
{
    "status": "error",
    "message": "Domain is already activated for this license",
    "activation": {
        "domain": "example.com",
        "activated_at": "2024-01-01T10:00:00.000000Z",
        "ip_address": "192.168.1.1"
    }
}
```

### 3. Deactivate License
**POST** `/api/license/deactivate` or **POST** `/api/deactivate-license`

Menonaktifkan license untuk domain tertentu.

**Request Body:**
```json
{
    "license_key": "your-license-key",
    "domain": "example.com"
}
```

**Success Response (200):**
```json
{
    "status": "success",
    "message": "License deactivated successfully for domain",
    "deactivated_activation": {
        "domain": "example.com",
        "activated_at": "2024-01-01T10:00:00.000000Z",
        "ip_address": "192.168.1.1"
    },
    "license": {
        "license_key": "your-license-key",
        "status": "active",
        "product_name": "My Product"
    }
}
```

**Error Response (404):**
```json
{
    "status": "error",
    "message": "No activation found for this domain and license"
}
```

## Status Codes

- **200**: Success
- **201**: Created (for activation)
- **400**: Bad Request (license inactive/expired)
- **404**: Not Found (license/activation not found)
- **409**: Conflict (domain already activated)
- **422**: Validation Error

## Signature Verification (for `/api/verify-license`)

### How to Generate Signature

1. **Create data string**: Concatenate `license_key + domain + timestamp`
2. **Generate HMAC**: Use HMAC SHA-256 with your secret key
3. **Add headers**: Include `X-Signature` and `X-Timestamp` in request

### Example Signature Generation

**PHP:**
```php
$secretKey = 'your-license-secret-key';
$licenseKey = 'ABC123';
$domain = 'example.com';
$timestamp = time();
$dataString = $licenseKey . $domain . $timestamp;
$signature = hash_hmac('sha256', $dataString, $secretKey);
```

**JavaScript:**
```javascript
const crypto = require('crypto');
const secretKey = 'your-license-secret-key';
const licenseKey = 'ABC123';
const domain = 'example.com';
const timestamp = Math.floor(Date.now() / 1000);
const dataString = licenseKey + domain + timestamp;
const signature = crypto.createHmac('sha256', secretKey)
  .update(dataString)
  .digest('hex');
```

### Signature Verification Errors

```json
{
    "status": "error",
    "message": "Missing required headers: X-Signature and X-Timestamp"
}
```

```json
{
    "status": "error", 
    "message": "Request timestamp is too old (must be within 5 minutes)"
}
```

```json
{
    "status": "error",
    "message": "Invalid signature"
}
```

## Error Response Format

```json
{
    "status": "error",
    "message": "Error description",
    "errors": {
        "field_name": ["validation error message"]
    }
}
```