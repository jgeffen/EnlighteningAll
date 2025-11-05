<?php
/*
Copyright (c) 2021, 2022 FenclWebDesign.com
This script may not be copied, reproduced or altered in whole or in part.
We check the Internet regularly for illegal copies of our scripts.
Do not edit or copy this script for someone else, because you will be held responsible as well.
This copyright shall be enforced to the full extent permitted by law.
Licenses to use this script on a single website may be purchased from FenclWebDesign.com
@Author: Developer
*/

/**
 * @var Router\Dispatcher $dispatcher
 * @var Items\Event[]     $items
 * @var Membership        $member
 */

use Items\Event;

try {
    $pagination = new Pagination();

    // ✅ Optimized SQL ordering (sort_order first)
    $pagination->setQuery("
        SELECT * FROM `events`
        WHERE `published` = 1
        ORDER BY 
            /*sort_order IS NULL ASC,
            sort_order ASC, */
            `date_end` >= CURDATE() DESC,
            `date_start` ASC,
            `date_end` ASC,
            `page_title` ASC
    ");

    $pagination->setPaginator(30, $dispatcher->getOption('page', 1));
    $pagination->setOriginalPageUrl($dispatcher->getRoute()->getLink());

    $paginator = $pagination->getPaginator();
    $items     = $pagination->getItems(Items\Event::class);
} catch(Exception $exception) {
    echo Debug::Exception($exception);
    exit;
}

if($dispatcher->getOption('page') > $paginator->getPageCount()) Render::ErrorDocument(404);

$page_title       = $pagination->formatPageString("Events - Yoga, Meditation, Education, AI, Web Design, Social Media");
$page_description = $pagination->formatPageString("Events - Yoga, Meditation, Education, AI, Web Design, Social Media");

include('includes/header.php');
?>

<style>
    /* ✅ Fade-in effect for lazy images */
    img.lazy-fade {
        opacity: 0;
        transition: opacity 0.6s ease-in-out;
    }
    img.lazy-fade.loaded {
        opacity: 1;
    }
</style>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        // Add fade-in once images load
        const imgs = document.querySelectorAll("img.lazy-fade");
        imgs.forEach(img => {
            if (img.complete) {
                img.classList.add("loaded");
            } else {
                img.addEventListener("load", () => img.classList.add("loaded"));
            }
        });
    });
</script>

<div class="container-fluid main-content">
    <div class="container">
        <div class="row">
            <div class="col">
                <h1 class="title-underlined mt-0 mb-3 mb-md-4 d-inline-block w-100">
                    <?php echo $pagination->formatPageString('Events At Enlightening All'); ?>
                </h1>

                <!-- <div class="song-announcement text-center my-5 p-5" style="background: radial-gradient(circle at top, #111 0%, #000 90%); border: 1px solid #ff66cc; border-radius: 16px; max-width: 760px; margin: 0 auto;">
                    <h2 style="color:#00e6ff; font-weight:700; text-transform:uppercase; letter-spacing:1px;">🎉 Grand Opening Launch Offer</h2>
                    <p style="font-size:1.1rem; color:white; margin-top:15px;">
                        Experience Enlightening All™ — Yoga, Meditation, & Community Events built to recharge your body and mind.
                    </p>

                    <ul style="list-style:none; padding:0; margin:25px 0; font-size:1.05rem; color:#fff; text-align:left; display:inline-block;">
                        <li>✅ <b>Get FREE Tickets</b> — try any yoga or dance class until November 8th.</li>
                        <li>💳 <i>No payment or membership required to pre-register.</i></li>
                    </ul>

                    <a href="#events-list" class="btn btn-lg" style="background:#00e6ff; color:#000; font-weight:700; border-radius:8px; padding:12px 28px; margin-top:15px; display:inline-block;">
                        Reserve My Free Class
                    </a>

                    <p style="margin-top:25px; color:#ccc; font-size:0.9rem; line-height:1.5;">
                        🎟 Seats, mats, and dates are assigned in order of registration.<br>
                        Offer valid through <b>November 8th 2025</b>.<br>
                        ✨ Government ID required at door. One-time free offer per person.
                    </p>
                </div> -->


                <?php if($paginator->isFirst()): ?>
                    <?php // optional calendar render ?>
                <?php endif; ?>

                <?php if(!empty($items)): ?>
                    <?php foreach($items as $item): ?>
                        <article class="full-width-article trim full-width-article__style-standard" aria-label="<?php echo $item->getAlt(); ?>">
                            <div class="row align-items-center justify-content-between">
                                <div class="col-lg-auto" id="events-list">
                                    <?php
                                    $class_type_label = Items\Event::Options('class_types', $item->getClassType()) ?? $item->getAlt();
                                    $mapped_image = Items\Event::Options('image_types', $item->getClassType()) ?? sprintf('/images/event-types/%s.png', $item->getClassType()) ?? '/images/event-types/main-logo.png';
                                    $data_src = $item->getPosterImage() ?: $mapped_image;

                                    // ✅ Auto WebP fallback
                                    $webp_path = preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $data_src);
                                    $final_src = file_exists($_SERVER['DOCUMENT_ROOT'] . $webp_path) ? $webp_path : $data_src;
                                    ?>
                                    <a href="<?php echo $item->getLink(); ?>" title="<?php echo $item->getAlt(); ?>">
                                        <picture>
                                            <source srcset="<?php echo $final_src; ?>" type="image/webp">
                                            <img
                                                    src="<?php echo $final_src; ?>"
                                                    loading="lazy"
                                                    alt="<?php echo htmlspecialchars($class_type_label); ?>"
                                                    class="full-width-article__image border lazy-fade"
                                            >
                                        </picture>
                                    </a>
                                </div>

                                <div id="<?php echo sprintf("event-toolbar-%s", $item->getId()); ?>" class="col col-xl-6" data-event-id="<?php echo $item->getid(); ?>">
                                    <div class="full-width-article__content text-center">
                                        <h2 class="title-super-sm">
                                            <a href="<?php echo $item->getLink(); ?>" title="<?php echo $item->getAlt(); ?>">
                                                <?php echo $item->getHeading(); ?>
                                            </a>
                                        </h2>
                                        <p><b>Date(s):</b> <?= $item->getEventTimes(); ?></p>
                                        <p><b>Time:</b> <?= $item->getDate(); ?></p>
                                        <p><b>Price:</b> <?php if($item->getPriceText() == 'Free' || $item->getPriceText() == '0.00' || $item->getPriceText() == '0' ):?>Free<?php else:?><?php echo '$' . $item->getPriceText(); ?><?php endif;?></p>

                                        <p><b>Location:</b> <?php echo $item->getLocation(); ?></p>

                                        <?php if($item->getContentPreview()): ?>
                                            <?php
                                            // Strip HTML and clean text
                                            $preview = strip_tags($item->getContentPreview());

                                            // Split into words and limit
                                            $words = explode(' ', $preview);
                                            $word_limit = 15; // ✅ shorter preview (about one concise line)

                                            if (count($words) > $word_limit) {
                                                $limited_preview = implode(' ', array_slice($words, 0, $word_limit)) . '...';
                                            } else {
                                                $limited_preview = $preview;
                                            }
                                            ?>

                                            <p>
                                                <?php echo $limited_preview; ?>
                                                <a href="<?php echo $item->getLink(); ?>" title="<?php echo $item->getAlt(); ?>">
                                                    <span class="nobr">Read More</span>
                                                </a>
                                            </p>
                                        <?php endif; ?>



                                        <div class="d-flex justify-content-center">
                                            <div class="btn-toolbar mx-auto" role="toolbar" aria-label="<?php echo sprintf("Button Toolbar for %s", $item->getAlt()); ?>">
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <?php if(Admin\Privilege(2)): ?>
                                                        <a class="btn btn-warning" style="font-size:1.1rem;padding:0.75rem 1.5rem;" href="<?php echo $item->getButton(Event::BUTTON_EDIT); ?>" target="_blank">
                                                            <i class="fas fa-edit fa-fw"></i>
                                                            <span class="d-none d-lg-inline-block">Edit</span>
                                                        </a>
                                                    <?php endif; ?>

                                                    <?php // if($item->isUpcomingEvent()): ?>
                                                        <?php if(Membership::LoggedIn() && $member->reservations()->lookup($item) && $member->reservations()->lookup($item)->isPaid()): ?>
                                                            <button type="button" class="btn btn-danger" data-event-action="rsvp-remove">
                                                                <i class="fas fa-times-circle fa-fw"></i>
                                                                <span class="d-none d-lg-inline-block">Remove RSVP</span>
                                                            </button>
                                                        <?php endif; ?>

                                                        <?php if($item->getPackagesIds()): ?>
                                                            <a class="btn btn-info"
                                                               style="font-size:1.1rem;padding:0.75rem 1.5rem;display:inline-flex;align-items:center;gap:0.4rem;"
                                                               href="<?php echo $item->getLink(); ?>">
                                                                <i class="fas fa-ticket-alt fa-fw"></i>
                                                                <span class="d-none d-lg-inline-block">More Information</span>
                                                                <span class="d-inline d-lg-none">More Information</span>
                                                            </a>
                                                        <?php endif; ?>

                                                    <?php // endif; ?>
                                                </div>
                                            </div>
                                        </div>

                                        <?php if(Membership::LoggedIn() && $item->isDisplayRsvps() && $item->getTotalReservations()): ?>
                                            <h3 class="mt-3"><sup>*</sup>Members Attending: <?php echo $item->getTotalReservations(); ?></h3>
                                            <small class="text-muted font-weight-bold my-3">
                                                <sup>*</sup>Numbers may not reflect pay-at-door attendees.
                                            </small>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="col-auto d-none d-lg-block">
                                    <a href="<?php echo $item->getLink(); ?>" class="full-width-article__btn" title="<?php echo $item->getAlt(); ?>">
                                        <i class="fa-light fa-chevron-right"></i>
                                    </a>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>

                    <?php if($paginator->getPageCount() > 1): ?>
                        <hr>
                        <nav aria-label="Events page navigation.">
                            <ul class="pagination justify-content-center">
                                <?php if($paginator->isFirst()): ?>
                                    <li class="page-item disabled"><a class="page-link">Previous</a></li>
                                <?php else: ?>
                                    <li class="page-item"><a class="page-link" href="<?php echo $pagination->formatPageLink($paginator->getPage() - 1); ?>">Previous</a></li>
                                <?php endif; ?>

                                <?php foreach($pagination->getButtons() as $page): ?>
                                    <?php if(is_int($page)): ?>
                                        <li class="page-item <?php echo ($page == $paginator->getPage()) ? 'active' : ''; ?>">
                                            <a class="page-link" href="<?php echo $pagination->formatPageLink($page); ?>"><?php echo $page; ?></a>
                                        </li>
                                    <?php else: ?>
                                        <li class="page-item disabled"><a class="page-link" href="#"><?php echo $page; ?></a></li>
                                    <?php endif; ?>
                                <?php endforeach; ?>

                                <?php if($paginator->isLast()): ?>
                                    <li class="page-item disabled"><a class="page-link">Next</a></li>
                                <?php else: ?>
                                    <li class="page-item"><a class="page-link" href="<?php echo $pagination->formatPageLink($paginator->getPage() + 1); ?>">Next</a></li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    <?php endif; ?>
                <?php else: ?>
                    <p>Sorry, there is nothing to show at this time. Please check back soon!</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>
<?php include('includes/body-close.php'); ?>
