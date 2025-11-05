<?php
/*
    Copyright (c) 2021 FenclWebDesign.com
    This script may not be copied, reproduced or altered in whole or in part.
    We check the Internet regularly for illegal copies of our scripts.
    Do not edit or copy this script for someone else, because you will be held responsible as well.
    This copyright shall be enforced to the full extent permitted by law.
    Licenses to use this script on a single website may be purchased from FenclWebDesign.com
    @Author: Deryk
*/

namespace Items;

use Database;
use Exception;
use Items;
use Items\Enums\Statuses;
use PDO;
use PDOStatement;

class Member extends Abstracts\Member
{
    /** @var null|Members\Subscription */
    private ?Members\Subscription $subscription = null;

    /** @var null|string Member role (admin, member, etc.) */
    public ?string $role = null;

    /** @var null|string Member status (active, suspended, etc.) */
    public ?string $status = null;

    /** @var null|string Creation timestamp */
    public ?string $created_at = null;

    /**
     * Initialize a member by ID (legacy-safe)
     *
     * - If $id is provided: loads that member
     * - If $id is null: tries to load from session (legacy Membership::Init() support)
     *
     * @param null|int $id
     * @return null|$this
     */
    public static function Init(?int $id = null): ?self
    {
        // Legacy support: load from session if no ID provided
        if ($id === null && isset($_SESSION['member_id'])) {
            $id = (int) $_SESSION['member_id'];
        }

        // Still no ID? Return null gracefully
        if ($id === null) {
            return null;
        }

        return Database::Action(
            "SELECT * FROM `members` WHERE `id` = :id",
            ['id' => $id]
        )->fetchObject(self::class) ?: null;
    }

    /**
     * Fetch one member from a prepared statement
     *
     * @param PDOStatement $statement
     * @return null|static
     */
    public static function Fetch(PDOStatement $statement): ?self
    {
        return $statement->fetchObject(self::class) ?: null;
    }

    /**
     * Fetch all members from a prepared statement
     *
     * @param PDOStatement $statement
     * @return self[]
     */
    public static function FetchAll(PDOStatement $statement): array
    {
        return $statement->fetchAll(PDO::FETCH_CLASS, self::class);
    }

    /**
     * Return active subscription
     *
     * @return null|Members\Subscription
     */
    public function subscription(): ?Members\Subscription
    {
        return $this->subscription ??= Members\Subscription::Fetch(
            Database::Action(
                "SELECT * FROM `member_subscriptions` 
                 WHERE `member_id` = :member_id 
                   AND `status` = :status",
                [
                    'member_id' => $this->getId(),
                    'status'    => Statuses\Subscription::ACTIVE->getValue()
                ]
            )
        );
    }

    /**
     * Check if the member has a given subscription
     *
     * @param null|Items\Subscription $subscription
     * @return bool
     */
    public function hasSubscription(?Items\Subscription $subscription): bool
    {
        return (bool) Database::Action(
            "SELECT * FROM `member_subscriptions`
             WHERE `member_id` = :member_id 
               AND `subscription_id` = :subscription_id",
            [
                'member_id'       => $this->getId(),
                'subscription_id' => $subscription?->getId()
            ]
        )->rowCount();
    }

    /**
     * Issue a free drink if member has paid subscription
     *
     * @param null|int $author
     * @return void
     * @throws Exception
     */
    public function issueFreeDrink(?int $author = null): void
    {
        if ($this->subscription()?->isPaid()) {
            Database::Action(
                "INSERT INTO `member_free_drinks`
                 SET `member_id` = :member_id,
                     `expiration_date` = :expiration_date,
                     `author` = :author,
                     `user_agent` = :user_agent,
                     `ip_address` = :ip_address
                 ON DUPLICATE KEY UPDATE
                     `expiration_date` = :expiration_date,
                     `author` = :author,
                     `user_agent` = :user_agent,
                     `ip_address` = :ip_address",
                [
                    'member_id'       => $this->getId(),
                    'expiration_date' => $this->subscription()->getRenewalDate()->format('Y-m-d H:i:s'),
                    'author'          => $author,
                    'user_agent'      => filter_input(INPUT_SERVER, 'HTTP_USER_AGENT'),
                    'ip_address'      => filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_VALIDATE_IP)
                ]
            );
        } else {
            throw new Exception('Member has no paid subscription.');
        }
    }

    /**
     * Legacy fallback for member settings (restores compatibility with older templates)
     */
    public function settings(): object
    {
        static $settings = null;

        if ($settings === null) {
            try {
                $stmt = Database::Action(
                    "SELECT * FROM member_settings WHERE member_id = :id LIMIT 1",
                    ['id' => $this->getId()]
                );
                $data = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
            } catch (\Throwable $e) {
                $data = [];
            }

            $settings = (object)$data;
        }

        return $settings;
    }

    /**
     * Legacy static check for Membership::LoggedIn()
     */
    public static function LoggedIn(bool $throw = true): bool
    {
        $loggedIn = !empty($_SESSION['member_id']);

        if ($throw && !$loggedIn) {
            throw new \Exception('You must be logged in to perform this action.');
        }

        return $loggedIn;
    }
}
