<?php
/*
	Copyright (c) 2021 FenclWebDesign.com
	This script may not be copied, reproduced or altered in whole or in part.
	We check the Internet regularly for illegal copies of our scripts.
	Do not edit or copy this script for someone else, because you will be held responsible as well.
	This copyright shall be enforced to the full extent permitted by law.
	Licenses to use this script on a single website may be purchased from FenclWebDesign.com
	@Author: Daerik
	*/

use Items\Abstracts;
use Items\Collections;
use Items\Enums\Statuses;
use Items\TravelAffiliateMembers;
use Items\TravelAffiliateMembers\Actions;
use JetBrains\PhpStorm\Pure;

class TravelAffiliateMembership extends Abstracts\TravelAffiliateMember {


    const LOGGED_IN  = TRUE;
    const LOGGED_OUT = FALSE;

    /**
     * @throws Exception
     */
    public function __construct() {
        if (isset($this->id)) return;

        $membership = self::Init($_SESSION['travel_affiliate_member']['id'] ?? NULL)?->toArray();
        is_array($membership) && array_map(fn (string $property, mixed $value) => $this->$property = $value, array_keys($membership), $membership);
    }

    /**
     * @param null|int $id
     *
     * @return null|$this
     */
    public static function Init(?int $id = NULL): ?self {
        return Database::Action("SELECT * FROM `travel_affiliate_members` WHERE `id` = :id", array(
            'id' => $id ?? $_SESSION['travel_affiliate_member']['id'] ?? NULL
        ))->fetchObject(self::class) ?: NULL;
    }

    /**
     * @param PDOStatement $statement
     *
     * @return null|static
     */
    public static function Fetch(PDOStatement $statement): ?self {
        return $statement->fetchObject(self::class) ?: NULL;
    }

    /**
     * @param PDOStatement $statement
     *
     * @return self[]
     */
    public static function FetchAll(PDOStatement $statement): array {
        return $statement->fetchAll(PDO::FETCH_CLASS, self::class);
    }

    /**
     * @param null|string $email
     * @param bool        $throwable
     *
     * @return null|$this
     *
     * @throws Exception
     */
    public static function FromEmail(?string $email, bool $throwable = TRUE): ?self {
        $instance = Database::Action("SELECT * FROM `travel_affiliate_members` WHERE :email IN (`email`, `username`)", array(
            'email' => $email
        ))->fetchObject(self::class) ?: NULL;

        if ($throwable && is_null($instance)) throw new Exception('Member not found.');

        return $instance;
    }

    /**
     * @param null|string $hash
     * @param bool        $throwable
     *
     * @return null|$this
     *
     * @throws Exception
     */
    public static function FromHash(?string $hash, bool $throwable = TRUE): ?self {
        $instance = Database::Action("SELECT * FROM `travel_affiliate_members` WHERE MD5(`email`) = :hash", array(
            'hash' => $hash
        ))->fetchObject(self::class) ?: NULL;

        if ($throwable && is_null($instance)) throw new Exception('Member not found.');

        return $instance;
    }

    /**
     * @return Actions\Account
     */
    public function account(): Actions\Account {
        return Actions\Account::Init($this);
    }

    /**
     * @return Actions\Log
     */
    public function log(): Actions\Log {
        return Actions\Log::Init($this);
    }


    public function poll(): int {
        return Database::Action(
            "SELECT UNIX_TIMESTAMP(IFNULL(MAX(`timestamp`), CURDATE())) FROM (SELECT GREATEST(MAX(`timestamp`), MAX(`last_timestamp`)) AS `timestamp` FROM `travel_affiliate_members` WHERE `id` = :member_id) AS `collective`",
            array(
                'member_id'  => $this->getId(),
            )
        )->fetchColumn();
    }


    /**
     * Redirects user whether logged in or not.
     *
     * @param bool   $status TRUE (Logged In) || FALSE (Logged Out)
     * @param string $url
     * @param bool   $rel_link
     *
     * @return void
     */

    public static function CheckRedirect(bool $status, string $url, bool $rel_link = FALSE): void {
        if (static::LoggedIn() !== $status) {
            Helpers::Redirect($url, $rel_link);
        }

        static::LoggedIn() && static::CheckBan();
    }

    /**
     * Checks if member is logged in.
     *
     * @param bool $status TRUE (Logged In) || FALSE (Logged Out)
     *
     * @return bool
     */
    public static function LoggedIn(bool $status = TRUE): bool {
        return !empty($_SESSION['travel_affiliate_member']) === $status;
    }

    /**
     * Checks if member is banned.
     *
     * @return void
     */
    public static function CheckBan(): void {
        $member = new TravelAffiliateMembership();

        if ($member->isBanned()) {
            unset($_SESSION['travel_affiliate_member']);
            header('Location: /travel-affiliate-members/login');
            exit;
        }
    }

    /**
     * @param string      $email
     * @param null|string $ignore
     *
     * @return bool
     */
    public static function EmailExists(string $email, ?string $ignore = NULL): bool {
        return Database::Action("SELECT `id` FROM `travel_affiliate_members` WHERE `email` = :email AND :email != :ignore", array(
            'email'  => $email,
            'ignore' => $ignore
        ))->rowCount();
    }

    /**
     * @param string      $username
     * @param null|string $ignore
     *
     * @return bool
     */
    public static function UsernameExists(string $username, ?string $ignore = NULL): bool {
        return Database::Action("SELECT `id` FROM `travel_affiliate_members` WHERE `username` = :username AND :username != :ignore", array(
            'username' => $username,
            'ignore'   => $ignore
        ))->rowCount();
    }

    /**
     * @return string
     */
    public function getHash(): string {
        return md5($this->getEmail());
    }

    /**
     * @return string
     */
    public function getVerificationLink(): string {
        return Helpers::CurrentWebsite(sprintf("/travel-affiliate-members/verify-email/%s", $this->getHash()));
    }

    /**
     * @return string
     */
    public function getPasswordResetLink(): string {
        return Helpers::CurrentWebsite(sprintf("/travel-affiliate-members/reset-password/%s", $this->getHash()));
    }
}
