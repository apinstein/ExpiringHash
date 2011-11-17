<?php

/**
 * Helper class for building expiring URLs.
 *
 * Generates a URL component you can use in your app that has a human-readable timestamp and a hash to ensure that it was not tampered with.
 */
class ExpiringHash
{
    const STATUS_TAMPERED = 'tampered';
    const STATUS_OK       = 'ok';
    const STATUS_EXPIRED  = 'expired';

    protected $secret;

    /**
     * @param string A secret to use for the hashing algorithm.
     */
    public function __construct($secret)
    {
        $this->secret = $secret;
    }

    /**
     * Convenience static constructor for fluent interfaces.
     *
     * @param string A secret to use for the hashing algorithm.
     * @return object ExpiringHash
     */
    public static function create($secret)
    {
        return new ExpiringHash($secret);
    }

    /**
     * Generate a hash for embedding in a URL that expires at the specified time.
     * @param string A date_create compatible timestamp string.
     * @return string A hash suitable for embedding in your URL.
     */
    public function generate($expirationTime)
    {
        $expirationDateTime = date_create($expirationTime);
        if ($expirationDateTime === false) throw new Exception("Couldn't create date from: {$expirationTime}");

        $hashInfo = $this->generateHash($expirationDateTime);
        return "{$hashInfo['date']}.{$hashInfo['hash']}";
    }

    private function generateHash($dateTime)
    {
        if (!($dateTime instanceof DateTime)) throw new Exception("DateTime required.");

        $expiryString = $dateTime->format('c');
        $hash = hash_hmac('sha256', $expiryString, $this->secret);
        return array(
            'date' => $expiryString,
            'hash' => $hash
        );
    }

    /**
     * Validate a hash from a URL.
     * @param string A hash from a URL to be validated.
     * @param string A date_create compatible timestamp string to use as the "now" for expiration check. Default: NOW.
     * @return string ExpiringHash::STATUS_OK if valid and not expired.
     *                ExpiringHash::STATUS_EXPIRED if valid and but expired.
     *                ExpiringHash::STATUS_TAMPERED if the hash has been tampered with.
     */
    public function validate($data, $asOfString = NULL)
    {
        list($hashExpiryString, $hash) = explode('.', $data);
        if (!$hashExpiryString) return self::STATUS_TAMPERED;
        if (!$hash) return self::STATUS_TAMPERED;

        $hashExpiryDateTime = date_create($hashExpiryString);
        if ($hashExpiryDateTime === false) throw new Exception("Couldn't create date from: {$hashExpiryString}");

        $expectedHashInfo = $this->generateHash($hashExpiryDateTime);
        if ($expectedHashInfo['hash'] !== $hash) return self::STATUS_TAMPERED;

        $asOfDateTime = date_create($asOfString, $hashExpiryDateTime->getTimezone());
        if ($asOfDateTime === false) throw new Exception("Couldn't create date from: {$asOfString}");
        $asOfUnix = $asOfDateTime->format('U');
        $hashExpiryUnix = $hashExpiryDateTime->format('U');
        if ($asOfUnix <= $hashExpiryUnix)
        {
            return self::STATUS_OK;
        }
        return self::STATUS_EXPIRED;
    }
}
