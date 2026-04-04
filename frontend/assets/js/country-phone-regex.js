(function () {
    'use strict';

    /**
     * AuthMe — Country Phone Data Registry
     *
     * Pre-defined country data for international phone number validation.
     * Used by register.js and host-request.js to populate country code dropdowns
     * and validate mobile numbers against country-specific regex patterns.
     *
     * Each entry contains:
     *   code    - Country calling code (e.g., +91)
     *   region  - ISO 3166-1 alpha-2 region code (e.g., IN for India)
     *   country - Display country name
     *   flag    - Emoji flag for visual display
     *   regex   - Regular expression pattern for valid mobile numbers
     *   example - Example number for placeholder text
     *
     * @package AuthMe
     */

    window.authmeCountryPhoneData = [
        {
            code: '+91',
            region: 'IN',
            country: 'India',
            flag: '🇮🇳',
            regex: /^[6-9]\d{9}$/,
            example: '9876543210'
        },
        {
            code: '+1',
            region: 'US',
            country: 'USA / Canada',
            flag: '🇺🇸',
            regex: /^[2-9]\d{9}$/,
            example: '5551234567'
        },
        {
            code: '+44',
            region: 'GB',
            country: 'United Kingdom',
            flag: '🇬🇧',
            regex: /^[1-9]\d{9,10}$/,
            example: '7911123456'
        },
        {
            code: '+61',
            region: 'AU',
            country: 'Australia',
            flag: '🇦🇺',
            regex: /^[2-9]\d{8}$/,
            example: '412345678'
        },
        {
            code: '+971',
            region: 'AE',
            country: 'UAE',
            flag: '🇦🇪',
            regex: /^[2-9]\d{8}$/,
            example: '501234567'
        },
        {
            code: '+65',
            region: 'SG',
            country: 'Singapore',
            flag: '🇸🇬',
            regex: /^[689]\d{7}$/,
            example: '91234567'
        },
        {
            code: '+49',
            region: 'DE',
            country: 'Germany',
            flag: '🇩🇪',
            regex: /^[1-9]\d{10,11}$/,
            example: '15123456789'
        },
        {
            code: '+33',
            region: 'FR',
            country: 'France',
            flag: '🇫🇷',
            regex: /^[1-9]\d{8}$/,
            example: '612345678'
        },
        {
            code: '+81',
            region: 'JP',
            country: 'Japan',
            flag: '🇯🇵',
            regex: /^[0-9]{9,10}$/,
            example: '9012345678'
        },
        {
            code: '+27',
            region: 'ZA',
            country: 'South Africa',
            flag: '🇿🇦',
            regex: /^[1-9]\d{8}$/,
            example: '712345678'
        }
    ];

})();
