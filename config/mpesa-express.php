<?php

// config for Emchegep/MpesaExpress

return [
    'consumer_key' => env('MPESA_CONSUMER_KEY',''),

    'consumer_secret' => env('MPESA_CONSUMER_SECRET',''),

    'pass_key' => env('MPESA_PASS_KEY',''),

    /**
     *
     * This is the organization's shortcode (Paybill or Buygoods - A 5 to 6-digit account number)
     * e.g. 654321
     *
     */
    'business_short_code' => env('MPESA_BUSINESS_SHORT_CODE',''),

    /**
     *
     * This is the transaction type that is used to identify the transaction.
     * For M-PESA Express is "CustomerPayBillOnline"
     *
     */
    'transaction_type' => env('MPESA_TRANSACTION_TYPE','CustomerPayBillOnline'),

    /**
     *
     * A valid secure URL that is used to receive notifications from M-Pesa API.
     * https://domain/path e.g. https://mydomain.com/path
     *
     */
    'callback_url' => env('MPESA_CALLBACK_URL',''),

    /**
     *
     * Account Reference: This is an Alpha-Numeric parameter that is defined by your
     * system as an Identifier of the transaction for the CustomerPayBillOnline transaction type.
     * Maximum of 12 characters. e.g. "Jumia Online"
     *
     */
    'account_reference' => env('MPESA_ACCOUNT_REFERENCE',''),

    /**
     *
     * This is any additional information/comment that can be sent along with the request from your system.
     * Maximum of 13 Characters. e.g. "Payment for goods"
     *
     */
    'transaction_desc' => env('MPESA_TRANSACTION_DESC',''),

    /**
     *
     *  M-PESA entity type i.e. sandbox or production
     *
     */
    'environment' => env('MPESA_ENV','sandbox'),
];
