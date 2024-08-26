# M-PESA rahisi
[![Latest Version](https://img.shields.io/github/v/release/vendor/package-name.svg?style=flat-square)](https://github.com/vendor/package-name/releases)
[![Total Downloads](https://img.shields.io/packagist/dt/vendor/package-name.svg?style=flat-square)](https://packagist.org/packages/vendor/package-name)
[![Build Status](https://img.shields.io/github/actions/workflow/status/vendor/package-name/tests.yml?branch=main)](https://github.com/vendor/package-name/actions)

This package provides an easy integration of M-Pesa payment services with your Laravel application, allowing you to process payments, handle transaction callbacks, and manage customer billing through Safaricom's M-Pesa API.


## Features

- Seamless M-Pesa integration with Laravel applications.
- Easy-to-use interface for initiating STK push requests.
- Automatically handles M-Pesa callback URLs and updates transactions.
- Configuration options to customize M-Pesa API settings.
- Compatible with Laravel 11.x.

## Installation

You can install the package via Composer:

```bash
composer require alloys9/mpesa_rahisi:dev-main@dev

```

## Configuration
The next step is to publish the application configurations. Run the following command

```bash
php artisan mpesa-rahisi:install

```

Add the following to your .env file to save the variables

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
if an error occurs run

```bash
php artisan vendor:publish --provider="Alloys9\MpesaRahisi\MpesaRahisiServiceProvider"

```
