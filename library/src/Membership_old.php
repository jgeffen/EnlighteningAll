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
use Items\Members;
use Items\Members\Actions;
use JetBrains\PhpStorm\Pure;
use Members\Messages\Contact;
use Members\QRCode;

class Membership_old extends Abstracts\Member {
    /* -------------------------------------------------------------------------
     * Extra Member Fields (not present in Abstracts\Member)
     * ---------------------------------------------------------------------- */
    public ?string $role = null;
    public ?string $status = null;
    public ?string $created_at = null;
    public ?string $updated_at = null;

    /* -------------------------------------------------------------------------
     * Collections and Related Objects
     * ---------------------------------------------------------------------- */
    private Collections\Comments      $comments;
    private Collections\Contests      $contests;
    private Collections\Likes         $likes;
    private Collections\Notifications $notifications;
    private Collections\Reports       $reports;
    private Collections\Reservations  $reservations;
    private Collections\Settings      $settings;
    private Collections\Tickets       $tickets;

    private ?Members\Subscription $subscription;
    private ?Members\Wallet       $wallet;

    const LOGGED_IN  = TRUE;
    const LOGGED_OUT = FALSE;

    private array $room_ids = [];

    /**
     * @throws Exception
     */
    public function __construct() {
        if (isset($this->id)) return;

        $membership = self::Init($_SESSION['member']['id'] ?? NULL)?->toArray();
        is_array($membership) && array_map(fn(string $property, mixed $value) => $this->$property = $value, array_keys($membership), $membership);
    }

    public static function Init(?int $id = NULL): ?self {
        return Database::Action("SELECT * FROM `members` WHERE `id` = :id", [
            'id' => $id ?? $_SESSION['member']['id'] ?? NULL
        ])->fetchObject(self::class) ?: NULL;
    }

    public static function Fetch(PDOStatement $statement): ?self {
        return $statement->fetchObject(self::class) ?: NULL;
    }

    public static function FetchAll(PDOStatement $statement): array {
        return $statement->fetchAll(PDO::FETCH_CLASS, self::class);
    }

    public static function FromEmail(?string $email, bool $throwable = TRUE): ?self {
        $instance = Database::Action("SELECT * FROM `members` WHERE :email IN (`email`, `username`)", [
            'email' => $email
        ])->fetchObject(self::class) ?: NULL;

        if ($throwable && is_null($instance)) throw new Exception('Member not found.');
        return $instance;
    }

    public static function FromHash(?string $hash, bool $throwable = TRUE): ?self {
        $instance = Database::Action("SELECT * FROM `members` WHERE MD5(`email`) = :hash", [
            'hash' => $hash
        ])->fetchObject(self::class) ?: NULL;

        if ($throwable && is_null($instance)) throw new Exception('Member not found.');
        return $instance;
    }

    public function account(): Actions\Account { return Actions\Account::Init($this); }
    public function block(Abstracts\Member $member): Actions\Block { return Actions\Block::Init($this, $member); }

    public function comments(): Collections\Comments {
        return $this->comments ??= new Collections\Comments(Database::Action(
            "SELECT * FROM `member_post_comments` WHERE `member_id` = :member_id",
            ['member_id' => $this->getId()]
        ));
    }

    public function contests(): Collections\Contests {
        return $this->contests ??= new Collections\Contests(Database::Action(
            "SELECT * FROM `member_contests` WHERE `member_id` = :member_id",
            ['member_id' => $this->getId()]
        ));
    }

    public function comment(Abstracts\Post $post): Actions\Comment {
        return Actions\Comment::Init($this, $post);
    }

    public function friend(Abstracts\Member $member): Actions\Friend {
        return Actions\Friend::Init($this, $member);
    }

    public function likes(): Collections\Likes {
        return $this->likes ??= new Collections\Likes(Database::Action(
            "SELECT * FROM `member_post_likes` WHERE `member_id` = :member_id",
            ['member_id' => $this->getId()]
        ));
    }

    public function like(Abstracts\Post $post): Actions\Like {
        return Actions\Like::Init($this, $post);
    }

    public function log(): Actions\Log {
        return Actions\Log::Init($this);
    }

    public function message(Abstracts\Member $member): Actions\Message {
        return Actions\Message::Init($this, $member);
    }

    public function notifications(?bool $unseen = NULL): Collections\Notifications {
        return $this->notifications ??= new Collections\Notifications(Database::Action(
            "SELECT * FROM `member_notifications` WHERE :member_id IN (`member_1`, `member_2`) 
				 AND `initiated_by` != :member_id 
				 AND (`seen` != :unseen OR ISNULL(:unseen)) 
				 ORDER BY `timestamp` DESC",
            [
                'member_id' => $this->getId(),
                'unseen' => $unseen
            ]
        ), $this);
    }

    public function notify(Abstracts\Member $member): Actions\Notification {
        return Actions\Notification::Init($this, $member);
    }

    public function qrCode(): QRCode { return new QRCode($this); }

    public function reports(): Collections\Reports {
        return $this->reports ??= new Collections\Reports(Database::Action(
            "SELECT * FROM `member_post_reports` WHERE `member_id` = :member_id",
            ['member_id' => $this->getId()]
        ));
    }

    public function reservations(): Collections\Reservations {
        return $this->reservations ??= new Collections\Reservations(Database::Action(
            "SELECT * FROM `member_reservations` WHERE `member_id` = :member_id",
            ['member_id' => $this->getId()]
        ));
    }

    public function tickets(): Collections\Tickets {
        return $this->tickets ??= new Collections\Tickets(Database::Action(
            "SELECT `member_tickets`.* 
				 FROM (SELECT `id`, MAX(`timestamp`) AS `timestamp`
					   FROM `member_tickets`
					   WHERE `member_id` = :member_id
					   GROUP BY COALESCE(`member_ticket_id`, `id`)
					  ) AS `tickets`
				 JOIN `member_tickets`
					ON (`tickets`.`id` IN (`member_tickets`.`id`, `member_tickets`.`member_ticket_id`)
					AND `member_tickets`.`timestamp` = `tickets`.`timestamp`)",
            ['member_id' => $this->getId()]
        ));
    }

    public function wallet(): ?Members\Wallet {
        return $this->wallet ??= Members\Wallet::Fetch(Database::Action(
            "SELECT * FROM `member_wallets` WHERE `default` IS TRUE AND `member_id` = :member_id",
            ['member_id' => $this->getId()]
        ));
    }

    public static function CheckRedirect(bool $status, string $url, bool $rel_link = FALSE): void {
        if (static::LoggedIn() !== $status) {
            Helpers::Redirect($url, $rel_link);
        }
        static::LoggedIn() && static::CheckBan();
    }

    public static function LoggedIn(bool $status = TRUE): bool {
        return !empty($_SESSION['member']) === $status;
    }

    public static function CheckBan(): void {
        $member = new Membership_old();
        if ($member->isBanned()) {
            unset($_SESSION['member']);
            header('Location: /members/login');
            exit;
        }
    }
    /**
     * Gets the active subscription for this member.
     *
     * @return null|Members\Subscription
     */
    public function subscription(): ?Members\Subscription {
        return $this->subscription ??= Members\Subscription::Fetch(Database::Action(
            "SELECT * FROM `member_subscriptions` 
         WHERE `member_id` = :member_id 
           AND (`status` = 'active' OR `status` IS NULL) 
         ORDER BY `timestamp` DESC LIMIT 1",
            ['member_id' => $this->getId()]
        ));
    }


    public function isDisplayRsvps(): bool {
        return $this->subscription()?->isFree() ?: $this->display_rsvps;
    }

    public function __set(string $name, $value): void {
        $this->$name = $value;
    }

    /**
     * Polls for updates across multiple member tables.
     *
     * @param null|Abstracts\Post   $post
     * @param null|Abstracts\Member $contact
     *
     * @return int Unix timestamp of the most recent change
     */
    public function poll(?Abstracts\Post $post = NULL, ?Abstracts\Member $contact = NULL): int {
        return Database::Action("
            SELECT UNIX_TIMESTAMP(IFNULL(MAX(`timestamp`), CURDATE())) AS `timestamp`
            FROM (
                SELECT GREATEST(MAX(`timestamp`), MAX(`last_timestamp`)) AS `timestamp` FROM `members` WHERE `id` = :member_id
                UNION
                SELECT GREATEST(MAX(`timestamp`), MAX(`last_timestamp`)) AS `timestamp` FROM `member_avatars` WHERE `member_id` = :member_id
                UNION
                SELECT GREATEST(MAX(`timestamp`), MAX(`last_timestamp`)) AS `timestamp` 
                    FROM `member_messages` 
                    WHERE :member_id IN (`member_1`, `member_2`) 
                      AND `initiated_by` != :member_id
                UNION
                SELECT GREATEST(MAX(`timestamp`), MAX(`last_timestamp`)) AS `timestamp` 
                    FROM `member_notifications` 
                    WHERE :member_id IN (`member_1`, `member_2`) 
                      AND `initiated_by` != :member_id
                UNION
                SELECT MAX(`timestamp`) AS `timestamp` FROM `member_tickets` WHERE `member_id` = :member_id
                UNION
                SELECT MAX(`timestamp`) AS `timestamp` FROM `member_post_comments` WHERE `member_post_id` = :member_post_id
                UNION
                SELECT MAX(`timestamp`) AS `timestamp` FROM `member_post_likes` WHERE `member_post_id` = :member_post_id
                UNION
                SELECT GREATEST(MAX(`timestamp`), MAX(`last_timestamp`)) AS `timestamp` 
                    FROM `member_messages` 
                    WHERE :member_id IN (`member_1`, `member_2`) 
                       OR :contact_id IN (`member_1`, `member_2`)
            ) AS `collective`
        ", [
            'member_id'      => $this->getId(),
            'member_post_id' => $post?->getId(),
            'contact_id'     => $contact?->getId()
        ])->fetchColumn();
    }
}
