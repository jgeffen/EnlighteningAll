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
use Helpers;
use net\authorize\api\constants\ANetEnvironment;

class AuthorizeNet {
    public const string DEVELOPMENT_MODE = 'development';
    public const string PRODUCTION_MODE  = 'production';

    protected string  $config_path;
    /** Default to PRODUCTION for live usage */
    protected string  $mode = self::DEVELOPMENT_MODE;

    private ?string $api_login_id   = null;
    private ?string $transaction_key = null;
    private ?string $client_key      = null;
    private ?string $currency        = null;
    private bool $useCIM;


    /**
     * @param string|null $mode "development" | "production"
     *
     * @throws Exception
     */
    public function __construct(?string $mode = null) {
        $this->config_path = Helpers::PathAbsolute('/library/settings/authorizenet.json');

        if (!is_readable($this->config_path)) {
            throw new Exception(sprintf('Unable to read %s.', $this->config_path));
        }

        // Load JSON config safely
        $configRaw = file_get_contents($this->config_path);
        if ($configRaw === false) {
            throw new Exception(sprintf('Unable to open %s.', basename($this->config_path)));
        }

        $config = json_decode($configRaw, true);
        if (!is_array($config)) {
            throw new Exception(sprintf('Invalid JSON in %s.', basename($this->config_path)));
        }

        // Determine mode: explicit param wins; fall back to file; default to PRODUCTION
        $this->mode = $mode ?: ($config['mode'] ?? self::PRODUCTION_MODE);

        if (!in_array($this->mode, [self::DEVELOPMENT_MODE, self::PRODUCTION_MODE], true)) {
            throw new Exception(sprintf('Unknown mode "%s".', $this->mode));
        }

        // Load credentials for the selected mode
        if (empty($config[$this->mode]) || !is_array($config[$this->mode])) {
            throw new Exception(sprintf('Missing "%s" section in %s.', $this->mode, basename($this->config_path)));
        }

        $section = $config[$this->mode];

        // Assign known keys if present
        foreach (['api_login_id','transaction_key','client_key','currency'] as $key) {
            if (array_key_exists($key, $section)) {
                $this->{$key} = $section[$key] ?: null;
            }
        }
    }
    public function usesCIM(): bool {
        return $this->mode === self::DEVELOPMENT_MODE
            ? true // sandbox often uses CIM for testing
            : false; // production disables CIM
    }

    /**
     * Gets the appropriate Authorize.Net environment based on mode.
     *
     * @return string
     */
    public function getEnvironment(): string {
        // Prefer Authorize.Net SDK constants (identical to raw URLs)
        return $this->mode === self::PRODUCTION_MODE
            ? ANetEnvironment::PRODUCTION
            : ANetEnvironment::SANDBOX;
    }

    /**
     * @return null|string
     */
    public function getClientKey(): ?string {
        return $this->client_key;
    }

    /**
     * @return string
     */
    public function getConfigPath(): string {
        return $this->config_path;
    }

    /**
     * @return string
     */
    public function getMode(): string {
        return $this->mode;
    }

    /**
     * @return null|string
     */
    public function getApiLoginId(): ?string {
        return $this->api_login_id;
    }

    /**
     * @return null|string
     */
    public function getTransactionKey(): ?string {
        return $this->transaction_key;
    }

    /**
     * @return null|string
     */
    public function getCurrency(): ?string {
        return $this->currency;
    }

    /**
     * @return bool
     */
    public function isSandbox(): bool {
        return $this->mode === self::DEVELOPMENT_MODE;
    }
}
