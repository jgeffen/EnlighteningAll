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
 * @var Membership        $member
 */

// Variable Defaults
$page = Items\Page::FromPageUrl(pathinfo(__FILE__, PATHINFO_FILENAME));
$page->setNews(Database::Action("SELECT * FROM `news` LIMIT 12"));

// Check Page
if(is_null($page)) Render::ErrorDocument(404);

// Search Engine Optimization
$page_title       = $page->getTitle();
$page_description = $page->getDescription();

// Page Booleans
$homepage = TRUE;

// Start Header
include('includes/header.php');
?>

<style>
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
</style>

<div class="container-fluid main-content border-top-0">
    <div class="container">
        <div class="w-100 text-center my-4">
            <!-- Top "Click Here for Free Events" Button -->
            <a href="/events" class="home-events-btn">Click Here For Events</a>
        </div>

        <div class="row">
            <div class="col">
                <h1>ENLIGHTENING ALL™ - Mind, Body & Business™</h1>

                <div class="services-section">
                    <h2 class="mb-4 text-center">MIND - BODY - BUSINESS Services</h2>

                    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3 mb-5 text-center">
                        <div class="mb-2">Yoga & Meditation</div>
                        <div>Educational Classes</div>
                        <div>AI Training & Services</div>
                        <div class="mb-2">Business Networking</div>
                        <div>Web Development</div>
                        <div>Social Media</div>
                        <div class="mb-2">Video & Production</div>
                        <div>Sound & Recording</div>
                        <div>Business Events</div>
                        <div class="mb-2">Entertainment Events</div>
                        <div>Social Events</div>
                        <div>Special Events</div>
                        <div class="mb-2 text-align-center">Wellness Events</div>
                    </div>

                    <h2><strong>Yoga - Meditation - Education - AI - Web Design - Social Media</strong></h2>

                    <p>
                        At <strong>ENLIGHTENING ALL™</strong>, we do more than offer a physical space — we create digital and real-world platforms where ideas thrive. Whether you're launching a business, building a brand, producing music, hosting a class, or planning an event, our studio is built to help you create with clarity and purpose.
                    </p>

                    <hr>

                    <h3><strong>Web Design & Digital Services</strong></h3>
                    <p><strong>Built by Fencl Web Design, Powered by Purpose</strong></p>
                    <p>
                        At the core of <strong>ENLIGHTENING ALL™</strong> is our <strong>full-service web design studio</strong>, backed by the expert team at <strong>Fencl Web Design</strong>. With over two decades of experience, we have built <strong>custom websites</strong> that are fast, SEO-ready, and easy to manage — no templates, no bloat. Whether you're launching a brand, building an online store, or redesigning your digital presence, we deliver code that performs and design that converts.
                    </p>
                    <p>Our services include:</p>
                    <ul>
                        <li>Custom web design & development</li>
                        <li>Our proprietary <strong>Custom CMS</strong></li>
                        <li>Search engine optimization (SEO)</li>
                        <li>Landing pages, eCommerce, and blogs</li>
                        <li>Google Business profile setup & management</li>
                        <li>AI-written content management</li>
                    </ul>
                    <p>Everything is built in-house with your growth in mind.</p>

                    <hr>

                    <h3><strong>Yoga, Meditation, & Wellness</strong></h3>
                    <p>
                        Our spacious studio is fully equipped for yoga classes, workshops, and healing experiences. You can rent the space for your own sessions or join classes led by guest instructors. We provide:
                    </p>
                    <ul>
                        <li>30–40 yoga mats (including extra-large 7×30" mats)</li>
                        <li>Yoga blocks, straps, and light dumbbells</li>
                        <li>432Hz crystal singing bowls and a large ceremonial gong for <strong>sound healing & meditation</strong></li>
                    </ul>
                    <p>This space was created with peace and energy in mind — perfect for mindfulness, movement, and community.</p>

                    <hr>

                    <h3><strong>Recording Studio</strong></h3>
                    <p>Step into a pro-level recording setup with:</p>
                    <ul>
                        <li><strong>Tascam DP-24SD 24-track studio</strong>, Shure MV7+ lapel & headset mic</li>
                        <li><strong>3000-watt amp</strong> with wireless mics</li>
                        <li>Great acoustics for clean audio</li>
                        <li>Remote collaboration with <strong>BandLab</strong></li>
                        <li>Publishing support via <strong>DistroKid</strong></li>
                        <li>Editing, mixing, and mastering available</li>
                    </ul>
                    <p>
                        Whether you're tracking a demo, launching a podcast, or just exploring, our studio’s got the tools to back you up.
                    </p>

                    <hr>

                    <h3><strong>Podcast & Video Production</strong></h3>
                    <p>
                        Our space doubles as a <strong>podcast and video studio</strong> equipped with:
                    </p>
                    <ul>
                        <li><strong>Three YouTube Streaming-Ready AI-powered PTZ cameras</strong> (Tenveo)</li>
                        <li>Nikon P1000 4K camera</li>
                        <li>Green screen & studio lighting</li>
                        <li>Audio/video editing support</li>
                        <li>Live streaming + publishing options</li>
                    </ul>
                    <p>
                        Set up interviews, stream your message, or shoot content for YouTube or social — we’ll help you bring it to life.
                    </p>

                    <hr>

                    <h3><strong>Event Space for Creators & Community</strong></h3>
                    <p><strong>ENLIGHTENING ALL™</strong> transforms easily into a warm, inviting event venue:</p>
                    <ul>
                        <li>Seats up to 100 guests or 80 with tables</li>
                        <li>Great for workshops, parties, pop-ups, and private events</li>
                        <li>5 restrooms and cold beverages available</li>
                        <li>No alcohol, drugs, smoking, vaping, or bad vibes</li>
                    </ul>
                    <p>
                        We’re a creative-first space — open to good people doing good things.
                    </p>
                    <p>
                        Founder Bret Fencl envisions ENLIGHTENING ALL™ as a place where individuals can cultivate balance in Mind, Body & Business. The facility serves as an ideal location for a wide variety of events, from wellness workshops to business networking and creative sessions.
                    </p>
                    <p>
                        With new seating and tables for up to 80 guests — including 800 lb.-capacity chairs — ENLIGHTENING ALL™ is designed for flexibility, accessibility, and room to move. Members can select their yoga mat position just like selecting an airline seat, and friends can see and book nearby mats. This makes it perfect for Girls' Nights Out, birthdays, wedding parties, and getting healthy with friends.
                    </p>
                    <p>
                        We also offer chairs for anyone unable or uncomfortable sitting on a yoga mat. Fun yoga classes include themed music nights like Country, EDM, and Rock — wear your favorite concert T-shirt and request a song to be played during class or the post-class social.
                    </p>

                    <h3>A Hub for Yoga, Meditation, and Digital Empowerment</h3>
                    <p>
                        At the core of ENLIGHTENING ALL™ is its commitment to holistic wellness. The studio offers a diverse range of yoga and meditation classes focused on range-of-motion exercises and well-being. This is complemented by advanced digital services including AI training, web design, social media marketing, business networking, and conferences.
                    </p>
                    <p>
                        Bret Fencl’s company, FenclWebDesign.com, has been a staple in the digital landscape for over 25 years. This vision now expands to include AI and digital education alongside traditional wellness practices.
                    </p>

                    <p>
                        We have AI cameras that follow speakers and project them onto 68" and 75" jumbo screens on opposing walls. These can also stream to YouTube or Facebook and be recorded for later viewing.
                    </p>

                    <hr>

                    <h3><strong>Why Choose ENLIGHTENING ALL™?</strong></h3>
                    <p>
                        Because this isn’t just a studio — it’s a launchpad. A build station. A sanctuary for creators, developers, dreamers, and doers. Whether you're building a brand, teaching breathwork, or recording your next podcast episode, you'll find the tools, space, and support to help your project shine.
                    </p>
                    <p><strong>Let’s build something bright — online and off.</strong></p>

                    <!-- Bottom "Click Here for Free Events" Button -->
                    <div class="w-100 text-center mt-5 mb-3">
                        <a href="/events" class="home-events-btn">Click Here For Events</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if(!empty($page->getNews())): ?>
    <div class="events-carousel-wrap py-5" role="region" aria-label="Events Carousel">
        <h2 class="title-super-lg text-center mt-0 mb-5">Recent <b>News</b></h2>
        <div class="swiper-button-prev"><i class="fa-light fa-chevron-left"></i></div>
        <div class="swiper events-carousel">
            <div class="swiper-wrapper align-items-center">
                <?php foreach($page->getNews() as $event): ?>
                    <div class="swiper-slide text-center" role="region" aria-label="Slide <?php echo $event->getAlt(); ?>">
                        <h3 class="mb-1">
                            <b>
                                <a href="<?php echo $event->getLink(); ?>" title="<?php echo $event->getAlt(); ?>">
                                    <?php echo $event->getHeading(); ?>
                                </a>
                            </b>
                        </h3>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="swiper-button-next"><i class="fa-light fa-chevron-right"></i></div>
        <div class="row justify-content-center mt-4 mb-2">
            <div class="col-sm-10 col-md-8 col-lg-6 col-xl-4">
                <a class="btn btn-outline btn-block" href="/news">View All Recent News</a>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php include('includes/siteplan-breakout.php'); ?>

<div class="container-fluid main-content p-0" style="line-height: 0;">
    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3518.787092332972!2d-80.64312858727668!3d28.122517875846782!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x88de0ff4f0105a0d%3A0xfcef3092e88962e5!2sEnlightening%20All!5e0!3m2!1sen!2sus!4v1754335980221!5m2!1sen!2sus" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
</div>

<?php include('includes/footer.php'); ?>
<?php include('includes/body-close.php'); ?>
