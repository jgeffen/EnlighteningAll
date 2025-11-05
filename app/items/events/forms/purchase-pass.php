<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
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
 * @var null|Membership   $member
 */

// Variable Defaults
$item = Database::Action("
    SELECT * FROM `events`
    WHERE `published` IS TRUE
      AND `page_url` = :child_url
    LIMIT 1
", [
        'child_url' => $dispatcher->getOption('child_url')
])->fetchObject(Items\Event::class);


// Get the referral ID
if(isset($_GET['ref'])) {
    $referrer_id = (int)$_GET['ref'];

    // Skip if it's the same as the logged-in member
    if(empty($member) || $referrer_id !== $member->getId()) {
        // Save into session until checkout
        if(!empty($referrer_id) && (!isset($_SESSION['referrer_id']) || $_SESSION['referrer_id'] != $referrer_id)) {
            $_SESSION['referrer_id'] = $referrer_id;
        }
    }
}

// Check Item & Packages
if(empty($item) || empty($item->getPackagesIds())) Render::ErrorDocument(404);

// Search Engine Optimization
$page_title       = sprintf("%s", $item->getTitle());
$page_description = "";

// Page Variables
$no_index = TRUE;

$currentDate = new DateTime();
$currentDate->setTime(0, 0, 0);

// Instead of modify('+10 days'), manually add 10 days
$threshold = new DateTime($currentDate->format('Y-m-d'));
$threshold->add(new DateInterval('P10D'));

$start_date = $item->getStartDate() instanceof DateTime
        ? $item->getStartDate()
        : new DateTime($item->getStartDate());

// within the next 10 days, inclusive, and not past
// ✅ Null-safe subscription chain and default to false if anything is missing
$discount_applies = ($start_date > $threshold) && (($member?->subscription()?->isPaid()) ?? false);
$discount_rate    = $discount_applies ? 0.10 : 0.00;

// $4 flat discount for subscribed members only
$flat_discount = 0.00;
if((($member?->subscription()?->isPaid()) ?? false)) {
    $flat_discount = 4.00;
}

// Start Header
include('includes/header.php');
?>

<style>
    .flex-center {
        display: grid;
        gap: 1rem;
        justify-content: center;
    }

    @media (min-width: 768px) {
        .flex-center {
            display: flex;
        }

        #paypal-button-container {
            min-height: 45px;
            display: block !important;
            text-align: center;
            margin-top: 1rem;
        }
        iframe[src*="paypal.com"] {
            min-height: 45px !important;
            width: 100% !important;
            display: inline-block !important;
        }
        .package-data-table .row.btn-reveal-trigger {
            border-bottom: 1px solid #ddd;
            margin: 0;
            padding: 10px 0;
        }

        .package-data-table .row.btn-reveal-trigger.border-top {
            border-top: 1px solid #ddd;
        }

        .package-data-table label.form-label {
            font-weight: 600;
        }
</style>

<div class="container-fluid main-content">
    <div class="container">
        <div class="row">
            <div class="col">
                <div class="trim pt-4 pb-2">
                    <div class="title-bar">
                        <i class="fa-light fa-calendar"></i>
                        <h2><?php echo $page_title; ?></h2>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <p>
                                <i class="fal fa-calendar-alt"></i>
                                <b>Date:</b> <?php
                                $date = trim($item->getDate());
                                $dateLower = strtolower($date);

                                if (
                                        str_contains($dateLower, 'dec 31') ||
                                        str_contains($dateLower, '12/31') ||
                                        str_contains($dateLower, '2025-12-31') ||
                                        str_contains($dateLower, '-12-31') || // catches 2024-12-31, etc.
                                        str_contains($dateLower, 'december 31')
                                ) {
                                    echo 'To Be Announced';
                                } else {
                                    echo htmlspecialchars($date);
                                }
                                ?>
                            </p>
                        </div>

                        <?php if($item->getLocation()) : ?>
                            <div class="col-md-6">
                                <p>
                                    <i class="fal fa-map-marked"></i>
                                    <b>Location:</b> <?php echo $item->getLocation(); ?>
                                </p>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <p>
                                <i class="fal fa-tags"></i>
                                <?php
                                $priceText = trim(strtolower($item->getPriceText()));

                                if ($priceText === 'free' || floatval($priceText) == 0.00) {
                                    $displayPrice = htmlspecialchars($item->getPriceText());
                                } else {
                                    $displayPrice = '$' . htmlspecialchars($item->getPriceText());
                                }
                                ?>
                                <b>Price:</b> <?= $displayPrice ?>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p>
                                <i class="fa-solid fa-clock"></i>
                                <b>Time:</b> <?php echo $item->getEventTimes(); ?>
                            </p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <p>
                                <i class="fa-solid fa-file-lines"></i>
                                <b>Event Description:</b> <?php echo htmlspecialchars($item->getDescription()); ?>
                            </p>
                        </div>
                    </div>
                <?php // ✅ Fully null-safe chain: reservations()?->lookup()?->isPaid() ?>
                <?php if(($member?->reservations()?->lookup($item)?->isPaid()) ?? false) : ?>
                    <div class="alert alert-danger text-center" role="alert">
                        <p class="font-weight-bolder">You have already registered for this event.</p>
                        <p class="mb-0">By continuing you are aware that you are <u>making an additional reservation.</u></p>
                    </div>
                <?php endif; ?>

                <?php if(!empty($member) || isset($_SESSION['referrer_id'])): ?>
                    <div class="alert alert-info text-center mt-4">
                        <label for="referral-link" class="form-label mb-1">Share this event with friends:</label>
                        <div class="input-group justify-content-center" style="max-width:600px;margin:auto;">
                            <input id="referral-link" type="text" class="form-control" readonly
                                   value="<?php echo SITE_URL; ?>/events/<?php echo $dispatcher->getOption('child_url'); ?>/purchase-pass?ref=<?php echo $member?->getId() ?? $_SESSION['referrer_id']; ?>">
                            <button type="button" class="btn btn-primary"
                                    onclick="navigator.clipboard.writeText(document.getElementById('referral-link').value)">
                                Copy Link
                            </button>
                        </div>
                        <small class="d-block mt-2 text-muted">Share this link so your friends can RSVP through you!</small>
                    </div>
                <?php endif; ?>

                <?php // if(Membership::LoggedIn(FALSE)): ?>
                    <!-- <div id="create-account-buttons" class="container">
                        <div class="row">
                            <div class="col flex-center">
                                <a href="/members/register" target="_blank" type="button" class="create-member-account btn btn-primary">Create Member Account</a>
                                <a href="/members/login?rel=<?php // echo $_SERVER["REDIRECT_URL"]; ?>" type="button" class="member-login btn btn-primary">Login</a>
                                <button type="button" class="continue-as-guest btn btn-secondary">Continue as Guest</button>
                            </div>
                        </div>
                    </div> -->
                <?php // endif; ?>

                <!-- <form class="package-data-table table-responsive <?php // echo Membership::LoggedIn(FALSE) ? "d-none" : ""; ?>" autocomplete="off"> -->
                    <form class="package-data-table table-responsive" autocomplete="off">
                    <div class="resp-table-lg mb-4">
                        <div class="row title-row">
                            <div class="col-12 col-lg">
                                <p id="package__name"><i class="fa-solid fa-box-open"></i>&nbsp; Event Package</p>
                            </div>

                            <div class="col-12 col-lg">
                                <p id="package__price"><i class="fa-solid fa-money-bill-wave"></i>&nbsp; Price</p>
                            </div>

                            <div class="col-12 col-lg">
                                <p id="package__qty"><i class="fa-solid fa-ticket"></i>&nbsp; Quantity</p>
                            </div>
                        </div>

                        <?php foreach($item->getPackages() as $package) : ?>
                            <?php
                            $base_price = $package->getPrice();
                            // total discount (percent off + flat off)
                            $total_discount = ($base_price * $discount_rate) + $flat_discount;

                            // final price
                            $final_price = max($base_price - $total_discount, 0);
                            ?>
                            <?php if($package->getAvailableQuantity()): ?>
                        <div class="row btn-reveal-trigger"
                             data-package="<?php echo $package->toJson(JSON_HEX_QUOT); ?>"
                             data-price="<?php echo $package->getPrice(); ?>">
                                <div class="col-12 col-lg">
                                        <p data-tabletitle="package__name">
                                            <?php echo $package->getName(); ?>
                                        </p>
                                    </div>

                                    <input name="package_id" value="<?php echo $package->getId(); ?>" type="hidden"/>

                                    <div class="col-12 col-lg">
                                        <p data-tabletitle="package__price"><?php if($package->getPrice() == 0.00):?>Free<?php else:?>$<?php echo $package->getPrice(); ?><?php endif;?></p>
                                        <input type="hidden" name="price" value="<?php echo $package->getPrice(); ?>">
                                    </div>

                                    <div class="col-12 col-lg">
                                        <div data-tabletitle="">
                                            <div class="select-wrap form-control">1 Ticket
                                                <!-- <select class="package-qty" name="<?php // echo sprintf("event_packages[%d]", $package->getId()); ?>">
                                                    <?php // foreach(range(0, 1) as $value) : ?>
                                                        <option><?php // echo $value; ?></option>
                                                    <?php // endforeach; ?>
                                                </select> -->
                                                <input type="hidden" name="<?php echo sprintf("event_packages[%d]", $package->getId()); ?>" value="1">
                                            </div>
                                        </div>
                                    </div>
                                </div><br>
                                <?php if($package->isMusical()): ?>
                                    <div class="row btn-reveal-trigger border-top align-items-center" data-package="<?php echo $package->toJson(JSON_HEX_QUOT); ?>">
                                        <div class="col-12 col-lg-4">
                                            <label for="purchase-pass-form-input-song-request" class="form-label mb-0"><i class="fa-solid fa-comment-music"></i> Request A Song?</label>
                                        </div>
                                        <div class="col-12 col-lg-8">
                                            <input id="purchase-pass-form-input-song-request" class="form-control" placeholder="Enter Your Song Request" type="text" name="song_request" maxlength="40">
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                    </div>
                </form>

                <div id="purchase-pass-form__wrapper" class="title-bar-trim-combo" aria-label="Free Reservation Form" role="form">
                    <div class="title-bar">
                        <i class="fal fa-clipboard-list-check"></i>
                        <h2>Event Reservation Form</h2>
                    </div>

                    <div id="purchase-pass-form" class="form-wrap trim p-lg-4">
                        <form class="mt-lg-2">
                            <input type="hidden" name="event_id" value="<?php echo $item->getId(); ?>">
                            <input type="hidden" name="payment_token" id="payment_token">
                            <input type="hidden" name="price" id="computed_price" value="<?php echo $item->getPriceText(); ?>">
                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="form-group row">
                                        <label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="purchase-pass-form-input-amount">Amount: </label>
                                        <div class="col-lg-9">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
														<span class="input-group-text" id="purchase-pass-form-addon-amount">
															<i class="fas fa-fw fa-dollar-sign"></i> <?php if($package->getPrice() == 0.00):?>Free<?php else:?><?php echo $package->getPrice(); ?><?php endif;?>
														</span>
                                                    <input type="hidden" name="original_price" value="<?php echo $base_price; ?>">
                                                    <input type="hidden" name="discount" value="<?php echo number_format($total_discount, 2, '.', ''); ?>">
                                                    <input id="purchase-pass-form-input-amount" class="form-control" name="price" type="hidden" value="<?php echo $package->getPrice(); ?>" aria-describedby="purchase-pass-form-addon-amount" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="name-on-pass-wrapper" class="form-group row d-none">
                                        <label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="name_on_pass">
                                            Name on Pass:
                                        </label>
                                        <div class="col-lg-9">
                                            <select id="name_on_pass" name="name_on_pass" class="form-control bg-light">
                                                <?php if(!empty($member) && !empty($member->friends())): ?>
                                            <option value="<?php echo $member?->getId(); ?>" selected>
                                            <?php else:?>
                                                <option type="hidden" value="1" selected>
                                                    <?php endif; ?>
                                                    <?php echo $member?->getFullName(); ?><?php echo $member ? ' (Me)' : ''; ?>
                                                </option>
                                                <?php if(!empty($member) && !empty($member->friends())): ?>
                                                    <?php foreach($member->friends() as $friend): ?>
                                                        <option value="<?php echo $friend->getId(); ?>"><?php echo $friend->getFullName(); ?></option>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </select>
                                            <input type="hidden" id="name_on_pass_hidden" name="name_on_pass_hidden" value="<?php echo $member?->getId(); ?>">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="purchase-pass-form-input-first-name">First Name:</label>
                                        <div class="col-lg-9">
                                            <input id="purchase-pass-form-input-first-name" class="form-control" type="text" name="first_name" placeholder="* Required" maxlength="50">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="purchase-pass-form-input-last-name">Last Name:</label>
                                        <div class="col-lg-9">
                                            <input id="purchase-pass-form-input-last-name" class="form-control" type="text" name="last_name" placeholder="* Required" maxlength="50">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="purchase-pass-form-input-phone">Phone:</label>
                                        <div class="col-lg-9">
                                            <input id="purchase-pass-form-input-phone" class="form-control" type="text" name="phone" placeholder="* Required" maxlength="255" data-format="phone">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="purchase-pass-form-input-email">Email:</label>
                                        <div class="col-lg-9">
                                            <input id="purchase-pass-form-input-email" class="form-control" type="email" name="email" placeholder="* Required" maxlength="255">
                                        </div>
                                    </div>
                                    <?php if ($item->getPriceText() != '0.00'): ?>
                                        <!-- SHOW CREDIT CARD INPUTS -->
                                        <p class="text-center mt-3 mb-1"><strong>Payment Method</strong></p>
                                        <p class="text-center mb-2"><strong>Total Due: <span id="amount_display" class="text-success">Free</span></strong></p>
                                        <div id="paypal-button-container" class="text-center mt-3"></div>
                                        <div id="paypal-warning"></div>

                                    <?php else: ?>
                                        <!-- START: HIDDEN CC PLACEHOLDERS FOR FREE EVENTS -->
                                        <input id="purchase-pass-form-input-address-line-1" name="address_line_1" value="1272 Sarno Rd." type="hidden"/>
                                        <input id="purchase-pass-form-input-address-city" name="address_city" value="Melbourne" type="hidden"/>
                                        <input id="purchase-pass-form-select-address-state" name="address_state" value="FL" type="hidden"/>
                                        <input id="purchase-pass-form-select-address-country" name="address_country" value="US" type="hidden"/>
                                        <input id="purchase-pass-form-input-address-zip-code" name="address_zip_code" value="32935" type="hidden"/>
                                        <!-- END: HIDDEN CC PLACEHOLDERS FOR FREE EVENTS -->
                                    <?php endif; ?>

                                    <?php // ✅ Null-safe wallet points check with default 0 ?>
                                    <?php if((($member?->wallet()?->getPoints()) ?? 0) >= $final_price): ?>
                                </div>
                                <div class="col-lg-6">
                                    <!-- POINTS DISPLAY -->
                                    <!-- <div class="alert alert-success text-center">
                                        <p>You have enough points to cover this package!</p>
                                        <p>
                                            <strong><?php // echo $final_price; ?> points</strong> will be deducted from your wallet.
                                            <br>
                                            <small>(You must use points if your points are greater than or equal to the total amount.)</small>
                                        </p>
                                    </div> -->
                                    <input type="hidden" name="payment_method" value="points">

                                    <div class="form-group">
                                        <label class="col-form-label" for="purchase-pass-form-textarea-comments">Comments:</label>
                                        <textarea id="purchase-pass-form-textarea-comments" class="form-control" name="comments" rows="6"></textarea>
                                    </div>

                                    <div class="form-group">
                                        <div class="cap-wrap text-center">
                                            <fieldset>
                                                <label class="col-form-label" for="purchase-pass-form-captcha">Enter the Characters Shown Below</label>
                                                <input id="purchase-pass-form-captcha" class="form-control" type="text" name="captcha" placeholder="* Required">
                                            </fieldset>

                                            <noscript>
                                                <p class="help-block"><span class="text-danger">(Javascript must be enabled to submit the form.)</span></p>
                                            </noscript>
                                        </div>
                                    </div>

                                    <div class="form-group row justify-content-end">
                                        <div class="col-sm-7">
                                            <button id="purchase-pass-form-button-submit" class="btn btn-block btn-primary submit-btn mt-3 mb-2" type="submit">
                                                Submit Reservation
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <?php else: ?>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="col-form-label" for="purchase-pass-form-textarea-comments">Comments:</label>
                                    <textarea id="purchase-pass-form-textarea-comments" class="form-control" name="comments" rows="6"></textarea>
                                </div>

                                <div class="form-group">
                                    <div class="cap-wrap text-center">
                                        <fieldset>
                                            <label class="col-form-label" for="purchase-pass-form-captcha">Enter the Characters Shown Below</label>
                                            <input id="purchase-pass-form-captcha" class="form-control" type="text" name="captcha" placeholder="* Required">
                                        </fieldset>

                                        <noscript>
                                            <p class="help-block"><span class="text-danger">(Javascript must be enabled to submit the form.)</span></p>
                                        </noscript>
                                    </div>
                                </div>

                                <div class="form-group row justify-content-end">
                                    <div class="col-sm-7">
                                        <button id="purchase-pass-form-button-submit" class="btn btn-block btn-primary submit-btn mt-3 mb-2" type="submit">
                                            Submit Reservation
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>

<?php if(Membership::LoggedIn(FALSE)) { ?>
    <script>
        $(function() {
            var checkInterval = setInterval(function() {
                $.ajax({
                    url: '/ajax/members/member-logged-in',
                    type: 'post',
                    dataType: 'json',
                    success: function(data) {
                        if (data.loggedIn) {
                            console.log('User is logged in');
                            $(document).find('.package-data-table').removeClass('d-none');
                            $(document).find('#create-account-buttons').remove();
                            clearInterval(checkInterval);
                        } else {
                            console.log(data.loggedIn);
                        }
                    },
                    error: function(jqXHR, textStatus) {
                        console.log('Error fetching login status:', textStatus);
                    }
                });
            }, 5000);

            $(document).on('click', '.continue-as-guest', function(event) {
                event.preventDefault();
                $(document).find('.package-data-table').removeClass('d-none');
                $(document).find('#create-account-buttons').remove();
            });
        });
    </script>
<?php } ?>
<?php
// 🧭 Auto-switch PayPal environment based on domain
$is_dev = (strpos($_SERVER['HTTP_HOST'], 'new.') === 0);

if ($is_dev) {
    // 🧪 Sandbox (test mode)
    $paypal_client_id = 'AQXAfCXz_eV7Im-WVhN0pesURDOut4QRTQg2o7fnMInsW9L197m17JJzoAHh9wrVso11VPvIKZMvh024';
    $paypal_sdk_url   = 'https://www.sandbox.paypal.com/sdk/js';
    $paypal_env       = 'sandbox';
} else {
    // 💳 Live (production)
    $paypal_client_id = 'AU_aqkdxvcHEqW596MMhDnmna1TC8wgZTeIjzyMwLdVDwog98PLjVIjEPsNtKkC0OUjbVEp-VVk23HPC';
    $paypal_sdk_url   = 'https://www.paypal.com/sdk/js';
    $paypal_env       = 'live';
}
?>

    <!-- ✅ Load PayPal SDK dynamically (auto-switches sandbox/live) -->
    <script
            src="<?php echo $paypal_sdk_url; ?>?client-id=<?php echo $paypal_client_id; ?>&currency=USD"
            data-namespace="paypal_sdk_main"
            crossorigin="anonymous">
    </script>

<script>
    $(function() {
        console.log('🌎 PayPal environment:', '<?php echo strtoupper($paypal_env); ?>');

        var mainCSS          = $('link[href^="/css/styles-main.min.css"]');
        var ajaxForm         = $('#purchase-pass-form');
        var form             = $('#purchase-pass-form form');
        var captcha          = $('#purchase-pass-form-captcha');
        var packages         = $('select.package-qty');
        var amountDisplay    = $('#amount_display');
        var computedPrice    = $('#computed_price');
        var amountHidden     = $('#purchase-pass-form-input-amount');
        var originalPrice    = $('#original_price');
        var discountTotal    = $('#discount_total');
        var paypalContainer  = $('#paypal-button-container');

        const yes = document.getElementById('purchase-friend-yes');
        const no = document.getElementById('purchase-friend-no');
        const nameSelect = document.getElementById('name_on_pass');
        const hiddenName = document.getElementById('name_on_pass_hidden');
        const nameWrapper = document.getElementById('name-on-pass-wrapper');

        // 🔒 Disable name select when "No" chosen
        if (no && no.checked && nameSelect) {
            nameSelect.disabled = true;
            nameSelect.classList.add('bg-light');
            nameSelect.selectedIndex = 0;
            hiddenName.value = nameSelect.value;
        }

        // 👤 Sync "Name on Pass"
        function syncNameOnPass() {
            if (!yes || !no || !nameSelect || !hiddenName || !nameWrapper) return;
            if (no.checked) {
                nameWrapper.classList.add('d-none');
                nameSelect.selectedIndex = 0;
                nameSelect.disabled = true;
                hiddenName.value = nameSelect.value;
            } else {
                nameWrapper.classList.remove('d-none');
                nameSelect.disabled = false;
                hiddenName.value = '';
            }
        }

        yes && yes.addEventListener('change', syncNameOnPass);
        no && no.addEventListener('change', syncNameOnPass);

        // 🧩 Load captcha and plugins
        $.when(
            $('<link/>', {
                type: 'text/css',
                rel: 'stylesheet',
                href: '/js/realperson/jquery.realperson.ada.css'
            }).insertBefore(mainCSS),

            $.ajax('/js/realperson/jquery.plugin.min.js', { async: false, dataType: 'script' }),
            $.ajax('/js/realperson/jquery.realperson.ada.js', { async: false, dataType: 'script' }),
            $.ajax('/js/quickdeploy/jquery.dependent.fields.min.js', { async: false, dataType: 'script' }),
            $.ajax('/library/packages/accounting/accounting.min.js', { async: false, dataType: 'script' }),
            $.Deferred(function(deferred) { $(deferred.resolve); })
        ).then(function() {
            captcha.realperson();

            $('div#purchase-pass-form__wrapper').dependsOn({
                selector: packages,
                value: Array.apply(null, { length: 10 }).map(function(_, value) {
                    return (value + 1).toString();
                }),
                wrapper: null
            });

            // 💾 AJAX Form Submission
            ajaxForm.on('submit', 'form', function(event) {
                event.preventDefault();

                // 🧩 Ensure PayPal payment (for paid events)
                const price = parseFloat($('#computed_price').val()) || 0;
                const paypalId = $('#paypal_transaction_id').val();

                if (price > 0 && !paypalId) {
                    alert('Please complete your registration and payment information before submitting your reservation.');
                    return;
                }

                // 🧩 Gather form + package data
                var requestData = Object.assign(
                    form.serializeObject(),
                    $('.package-data-table').serializeObject()
                );

                $.ajax('/ajax/events/packages/purchase-pass', {
                    data: requestData,
                    dataType: 'json',
                    method: 'post',
                    async: true,
                    beforeSend: showLoader,
                    complete: hideLoader,
                    success: function(response) {
                        console.log('🛰️ Raw AJAX Response:', response);

                        try {
                            // Ensure JSON validity
                            if (typeof response !== 'object') {
                                console.warn('Response not parsed as JSON, attempting manual parse...');
                                response = JSON.parse(response);
                            }
                        } catch (e) {
                            console.error('🚨 JSON parse error:', e, response);
                            alert('Server returned invalid JSON. Check console for details.');
                            return;
                        }

                        // ✅ Now safely handle response object
                        if (!response || !response.status) {
                            console.error('⚠️ Unexpected response format:', response);
                            alert('Unexpected server response. Check console for details.');
                            return;
                        }

                        switch (response.status) {
                            case 'success':
                                console.log('✅ Reservation completed successfully.');

                                ajaxForm.html(response.html);
                                packages.parents('.package-data-table').remove();

                                // ✅ Add Referral Section
                                let shareSection = `
                        <div class="alert alert-info text-center mt-4 p-4" style="max-width:600px;margin:auto;">
                            <h5>Share This Event!</h5>
                            <p>Invite friends to RSVP and join you — copy your unique link below:</p>
                            <div class="input-group mb-2">
                                <input id="referral-link" type="text" class="form-control" readonly
                                    value="${window.location.origin}/events/${window.location.pathname.split('/')[2]}/purchase-pass?ref=${window.memberId || 'guest'}">
                                <button type="button" class="btn btn-primary"
                                    onclick="navigator.clipboard.writeText(document.getElementById('referral-link').value)">
                                    Copy Link
                                </button>
                            </div>
                            <small class="d-block text-muted">Your friends can reserve free passes too!</small>
                        </div>`;
                                $('#purchase-pass-form').append(shareSection);

                                $('html, body').animate({
                                    scrollTop: ajaxForm.offset().top - ($('#nav-wrapper').height() || 0) - 30
                                }, 1000);
                                break;

                            case 'error':
                                console.error('❌ Server Error:', response.message || response.errors);
                                alert(response.message || Object.values(response.errors || {}).join('\n') || 'Unknown error.');
                                break;

                            default:
                                console.warn('⚠️ Unexpected response status:', response);
                                alert(response.message || 'Something went wrong.');
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.error('🚨 AJAX Error:', textStatus, errorThrown, jqXHR.responseText);
                        alert('An error occurred while submitting your reservation. Please try again.');
                    }
                });
            });
        });

        // 💲 Price Recalculation & PayPal
        function recalc() {
            var total = 0.00;
            $('.package-data-table .btn-reveal-trigger').each(function() {
                var row = $(this);
                var price = parseFloat(row.data('price')) || 0;
               // var qty = parseInt(row.find('select.package-qty').val(), 10) || 0;
                var qty = 1; // default quantity
                var selectQty = row.find('select.package-qty');
                if (selectQty.length) {
                    qty = parseInt(selectQty.val(), 10) || 0;
                }

                total += (price * qty);
            });

            if (total <= 0) {
                amountDisplay.text('Free');
                paypalContainer.empty();
            } else {
                amountDisplay.text('$' + total.toFixed(2));
                initPayPalButton(total.toFixed(2));
            }

            computedPrice.val(total.toFixed(2));
            amountHidden.val(total.toFixed(2));
            originalPrice.val(total.toFixed(2));
        }

        // 🪙 PayPal Button Initialization
        function initPayPalButton(total) {
            console.log('🟢 initPayPalButton for amount:', total);
            paypalContainer.empty();

            function renderButtons(attempt = 1) {
                if (typeof paypal_sdk_main === 'undefined' || typeof paypal_sdk_main.Buttons !== 'function') {
                    console.warn('⏳ Waiting for PayPal SDK... attempt', attempt);
                    return setTimeout(() => renderButtons(attempt + 1), 300);
                }

                console.log('✅ PayPal SDK ready — rendering buttons.');
                paypal_sdk_main.Buttons({
                    style: { layout: 'vertical', color: 'blue', shape: 'rect', label: 'paypal' },
                    createOrder: function(data, actions) {
                        return actions.order.create({
                            purchase_units: [{ amount: { value: total } }]
                        });
                    },
                    onApprove: function(data, actions) {
                        return actions.order.capture().then(function(details) {
                            console.log('✅ Payment Approved:', details.id);

                            // Store PayPal transaction ID in hidden field
                            $('<input>', {
                                type: 'hidden',
                                name: 'paypal_transaction_id',
                                id: 'paypal_transaction_id',
                                value: details.id
                            }).appendTo(form);

                            // ✅ Show confirmation message
                            $('#paypal-warning').html(`
            <div class="alert alert-success text-center mt-3">
                <strong>Payment successful!</strong><br>
                Transaction ID: <code>${details.id}</code><br>
                Please click <b>Submit Reservation</b> below to finalize your booking.
            </div>
        `);

                            // ✅ Disable PayPal buttons to avoid duplicate payments
                            $('#paypal-button-container').html(
                                '<div class="alert alert-info text-center">Payment completed — waiting for reservation submission.</div>'
                            );
                        });
                    },

                    onError: function(err) {
                        console.error('❌ PayPal Error:', err);
                        alert('Payment error: ' + err.message);
                    }
                }).render('#paypal-button-container');
            }

            renderButtons();
        }

        recalc();
        $(document).on('change', 'select.package-qty', recalc);
    });
</script>

<?php include('includes/body-close.php'); ?>