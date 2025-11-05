<?php
/*
    Copyright (c) 2021, 2022 FenclWebDesign.com
    This script may not be copied, reproduced or altered in whole or in part.
    We check the Internet regularly for illegal copies of our scripts.
    Do not edit or copy this script for someone else, because you will be held responsible as well.
    This copyright shall be enforced to the full extent permitted by law.
    Licenses to use this script on a single website may be purchased from FenclWebDesign.com
    @Author: Deryk
*/

/**
 * @var Router\Dispatcher $dispatcher
 * @var Membership        $member
 */

// Imports
use Items\Enums\Options;
use Items\Enums\Types;

// Fetch all published events
$events = Items\Event::FetchAll(Database::Action("
    SELECT * FROM `events`
    WHERE `published` = 1
    ORDER BY 
        sort_order IS NULL ASC,
        sort_order ASC,
        `date_end` >= CURDATE() DESC,
        `date_start` ASC,
        `date_end` ASC,
        `page_title` ASC
"));

// SEO
$page_title       = 'Upcoming Events';
$page_description = 'View upcoming classes and events at Enlightening All.';

// Header
include('includes/header.php');
?>

<style>
    .title-bar {
        display: flex;
        justify-content: center;
        align-items: center;
        text-align: center;
    }

    .title-bar h3 {
        margin: 0;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .home-events-btn {
        display: inline-block;
        background-color: #d4af37; /* Gold */
        color: black;
        font-weight: bold;
        padding: 14px 32px;
        border-radius: 10px;
        text-transform: uppercase;
        text-decoration: none;
        font-size: 1.1rem;
        box-shadow: 0 3px 6px rgba(0,0,0,0.25);
        transition: all 0.3s ease;
        margin: 0 auto;
        text-align: center;
    }

    .home-events-btn:hover {
        background-color: #c49b2e;
        color: #fff;
        transform: translateY(-2px);
    }

    @media (max-width: 768px) {
        .home-events-btn {
            width: 90%;
            display: block;
            font-size: 1rem;
        }
    }

    #events-sidebar .title-bar {
        width: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
        text-align: center;
    }

    #events-sidebar .title-bar h3 {
        margin: 0 auto;
        text-align: center;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .event-list h4 a {
        color: #000;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .event-list h4 a:hover {
        color: #c49b2e;
        text-decoration: underline;
    }
</style>

<div class="container-fluid main-content">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 text-center mt-5 mb-4">
                <a href="/events" class="home-events-btn">Click Here For Events</a>
            </div>

            <?php if(!empty($events)) : ?>
                <div class="col-sm-10 col-md-8 col-lg-6 col-xl-5 mx-auto" id="events-sidebar">
                    <div class="events-sidebar__inner text-center">
                        <div class="title-bar-trim-combo mt-0">
                            <div class="title-bar mb-3">
                                <h3>Upcoming Events</h3>
                            </div>
                            <div class="trim p-4 event-list">
                                <?php foreach($events as $event) : ?>
                                    <h4 class="mb-3">
                                        <b>
                                            <a href="<?php echo $event->getLink(); ?>"
                                               title="<?php echo $event->getAlt(); ?>" target="_blank">
                                                <?php echo $event->getHeading(); ?>
                                            </a>
                                        </b>
                                    </h4>
                                    <hr class="w-50 mx-auto">
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else : ?>
                <div class="col text-center mt-5">
                    <h4>No upcoming events at this time. Please check back soon!</h4>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>



<?php include('includes/body-close.php'); ?>
