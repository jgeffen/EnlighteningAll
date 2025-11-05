<?php
/*
	Copyright (c) 2022 FenclWebDesign.com
	This script may not be copied, reproduced or altered in whole or in part.
	We check the Internet regularly for illegal copies of our scripts.
	Do not edit or copy this script for someone else, because you will be held responsible as well.
	This copyright shall be enforced to the full extent permitted by law.
	Licenses to use this script on a single website may be purchased from FenclWebDesign.com
	@Author: Deryk
*/

namespace Config;

use Exception;

/**
 * MobiusPay Config
 */
class MobiusPay {
    const DEVELOPMENT_MODE = 'development';
    const PRODUCTION_MODE  = 'production';

    private ?string $account     = NULL;
    private ?string $mode        = NULL;
    private ?string $private_key = NULL;
    private ?string $public_key  = NULL;

    // ✅ Added for compatibility with MobiusPay\Client
    private ?string $username    = NULL;
    private ?string $password    = NULL;
    private string  $endpoint    = 'https://secure.mobiusgateway.com/api/transact.php';

    /**
     * @param string|null $mode "development" | "production"
     * @param string|null $config_path
     *
     * @throws \Exception
     */
    public function __construct(?string $mode = NULL, ?string $config_path = NULL) {
        $config_path ??= sprintf('%s/settings/mobiuspay.json', dirname(__DIR__, 2));
        if (!is_file($config_path) || !is_readable($config_path)) {
            throw new \Exception(sprintf('Unable to open %s.', $config_path));
        }

        $json   = file_get_contents($config_path);
        $config = json_decode($json, TRUE);

        if (!is_array($config)) {
            throw new \Exception(sprintf('Invalid JSON in %s: %s', basename($config_path), json_last_error_msg()));
        }

        // Resolve mode: explicit arg > JSON > env > fail
        $mode = $mode ?: ($config['mode'] ?? getenv('MOBIUSPAY_MODE') ?: NULL);
        $mode = strtolower((string)$mode);

        // Accept common aliases
        $aliases = array('dev' => 'development', 'prod' => 'production', 'production' => 'production', 'development' => 'development');
        $this->mode = $aliases[$mode] ?? NULL;

        $env = $config[$this->mode];

        // Helper to pick first present key from a list
        $pick = function(array $source, array $keys, ?string $fallback = NULL) {
            foreach ($keys as $k) {
                if (array_key_exists($k, $source) && $source[$k] !== '' && $source[$k] !== NULL) {
                    return $source[$k];
                }
            }
            return $fallback;
        };

        $this->account     = (string)$pick($env, array('account', 'username', 'user'));
        $this->private_key = (string)$pick($env, array('private-key', 'private_key', 'security_key', 'security-key', 'privateKey', 'key'));
        $this->public_key  = (string)$pick($env, array('public-key', 'public_key', 'client_key', 'client-key', 'publicKey'));

        // ✅ Added: set alias properties used by Client
        $this->username = $this->account;
        $this->password = $this->private_key;

        // ✅ Use sandbox endpoint when in dev mode
        if ($this->mode === self::DEVELOPMENT_MODE) {
            $this->endpoint = 'https://sandbox.mobiusgateway.com/api/transact.php';
        }
    }

    /** @return string|null */
    public function getAccount(): ?string { return $this->account; }

    /** @return string|null */
    public function getMode(): ?string { return $this->mode; }

    /** @return string|null */
    public function getPrivateKey(): ?string { return $this->private_key; }

    /** @return string|null */
    public function getPublicKey(): ?string { return $this->public_key; }

    /* === Added for Client compatibility === */

    /** @return string|null */
    public function getUsername(): ?string { return $this->username; }

    /** @return string|null */
    public function getPassword(): ?string { return $this->password; }

    /** @return string */
    public function getEndpoint(): string { return $this->endpoint; }
}
