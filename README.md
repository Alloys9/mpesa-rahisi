# M-PESA rahisi

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

**Mpesa Rahisi** provides an easy integration of M-Pesa payment services with your Laravel application, allowing you to process payments, handle transaction callbacks, and manage customer billing through Safaricom's M-Pesa API.


## Features
- Seamless M-Pesa integration with Laravel applications.
- Easy-to-use interface for initiating STK push requests.
- Automatically handles M-Pesa callback URLs and updates transactions.
- Configuration options to customize M-Pesa API settings.
- Compatible with Laravel 11.x.

## Installation

### Step 1: Install the package via Composer

```bash
composer require alloys9/mpesa_rahisi:dev-main@dev

```

### Step 2: Run the installation command
The next step is to publish the application configurations. Run the following command

```bash
php artisan mpesa-rahisi:install

```

This command will:

- Replace the config/app.php file
- Copy controllers to app/Http/Controllers
- Copy migrations to database/migrations
- Copy models to app/Models
- Copy views to resources/views
- Append necessary routes to routes/web.php
- Replace the CSRF middleware file

### Step 3: Migrate the database
Run the following command to migrate the necessary database tables:
```bash
php artisan migrate
```

### Step 4: Add the following to your .env file to save the variables

```bash
MPESA_ENVIRONMENT=
SAFARICOM_PASSKEY=
MPESA_BUSINESS_SHORTCODE=
MPESA_CONSUMER_KEY=
MPESA_CONSUMER_SECRET=
MPESA_INITIATOR_PASSWORD=
MPESA_INITIATOR_NAME=
MPESA_SHORTCODE=

MPESA_CALLBACK_URL="${APP_URL}/payments/stkcallback"

MPESA_TEST_URLPRE="https://sandbox.safaricom.co.ke"

MPESA_TOKEN_URL="${MPESA_TEST_URLPRE}/oauth/v1/generate?grant_type=client_credentials"

MPESA_INITIATE_URL="${MPESA_TEST_URLPRE}/mpesa/stkpush/v1/processrequest"

MPESA_STK_QUERY_URL="${MPESA_TEST_URLPRE}/mpesa/stkpushquery/v1/query"

MPESA_SIMULATE_URL="${MPESA_TEST_URLPRE}/mpesa/c2b/v1/simulate"

MPESA_REGISTER_URL="${MPESA_TEST_URLPRE}/mpesa/c2b/v1/registerurl"

MPESA_CONFIRMATION_URL="${APP_URL}/payments/confirmation"

MPESA_VALIDATION_URL="${APP_URL}/payments/validation"

MPESA_B2C_TIMEOUT_URL="${APP_URL}/payments/b2ctimeout"
MPESA_B2C_RESULT_URL="${APP_URL}/payments/b2cresult"

MPESA_REVERSAL_TIMEOUT_URL="${APP_URL}/payments/reversalResult"
MPESA_REVERSAL_RESULT_URL="${APP_URL}/payments/reversalTimeout"
```

Then you are done! That simple!
### If an error occurs run
```bash
php artisan vendor:publish --provider="Alloys9\MpesaRahisi\MpesaRahisiServiceProvider"
```

Then run
```bash
php artisan mpesa-rahisi:install

```

## License
This package is open-sourced software licensed under the MIT license.

MIT License

Copyright 2024 Alloys Amasakha

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the “Software”), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED “AS IS”, WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

