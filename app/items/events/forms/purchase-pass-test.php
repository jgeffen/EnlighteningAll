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
$item = Database::Action("SELECT * FROM `events` WHERE `published` IS TRUE AND `page_url` = :child_url AND `date_start` >= CURDATE()", array(
        'child_url' => $dispatcher->getOption('child_url')
))->fetchObject(Items\Event::class);

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
$page_title       = sprintf("%s Free Reservation Form", $item->getTitle());
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
    }
</style>

<div class="container-fluid main-content">
    <div class="container">
        <div class="row">
            <div class="col">
                <h1><?php echo $page_title; ?></h1>

                <div class="trim pt-4 pb-2">
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
                                   value="<?php echo SITE_URL; ?>/events/<?php echo $dispatcher->getOption('child_url'); ?>/purchase-pass-auth?ref=<?php echo $member?->getId() ?? $_SESSION['referrer_id']; ?>">
                            <button type="button" class="btn btn-primary"
                                    onclick="navigator.clipboard.writeText(document.getElementById('referral-link').value)">
                                Copy Link
                            </button>
                        </div>
                        <small class="d-block mt-2 text-muted">Share this link so your friends can RSVP through you!</small>
                    </div>
                <?php endif; ?>
                <hr class="clear my-5">
                <?php if(Membership::LoggedIn(FALSE)): ?>
                    <div id="create-account-buttons" class="container">
                        <div class="row">
                            <div class="col flex-center">
                                <a href="/members/register" target="_blank" type="button" class="create-member-account btn btn-primary">Create Member Account</a>
                                <a href="/members/login?rel=<?php echo $_SERVER["REDIRECT_URL"]; ?>" type="button" class="member-login btn btn-primary">Login</a>
                                <button type="button" class="continue-as-guest btn btn-secondary">Continue as Guest</button>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <form class="package-data-table table-responsive <?php echo Membership::LoggedIn(FALSE) ? "d-none" : ""; ?>" autocomplete="off">
                    <div class="resp-table-lg mb-4">
                        <div class="row title-row">
                            <div class="col-12 col-lg">
                                <p id="package__name">Package</p>
                            </div>

                            <div class="col-12 col-lg">
                                <p id="package__price">Price</p>
                            </div>

                            <div class="col-12 col-lg">
                                <p id="package__qty">Quantity</p>
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
                                        <div data-tabletitle="package__qty">
                                            <div class="select-wrap form-control">
                                                <select class="package-qty" name="<?php echo sprintf("event_packages[%d]", $package->getId()); ?>">
                                                    <?php foreach(range(0, 1) as $value) : ?>
                                                        <option><?php echo $value; ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <div class="select-box"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div><br>
                                <?php if($package->isMusical()): ?>
                                <div class="form-row justify-content-center">
                                    <div class="col-lg-12 text-left">

                                            <div class="col-12 col-lg-6">
                                                <label for="purchase-pass-form-input-song-request" class="form-label">Request A Song?</label>
                                                <input id="purchase-pass-form-input-song-request" class="form-control w-80" placeholder="Enter Your Song Request" type="text" name="song_request" maxlength="40">
                                            </div>

                                    </div>
                                </div>
                                <?php endif; ?>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </form>

                <div id="purchase-pass-form__wrapper" class="title-bar-trim-combo" aria-label="Free Reservation Form" role="form">
                    <div class="title-bar">
                        <i class="fal fa-clipboard-list-check"></i>
                        <h2>Free Reservation Form</h2>
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
                                        <div class="form-group row">
                                            <label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="purchase-pass-form-input-address-line-1">Address Line 1:</label>
                                            <div class="col-lg-9">
                                                <input id="purchase-pass-form-input-address-line-1" class="form-control" type="text" name="address_line_1" placeholder="* Required" maxlength="255">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="purchase-pass-form-input-address-line-2">Address Line 2:</label>
                                            <div class="col-lg-9">
                                                <input id="purchase-pass-form-input-address-line-2" class="form-control" type="text" name="address_line_2" maxlength="255">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="purchase-pass-form-input-address-city">City:</label>
                                            <div class="col-lg-9">
                                                <input id="purchase-pass-form-input-address-city" class="form-control" type="text" name="address_city" placeholder="* Required" maxlength="255">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="purchase-pass-form-select-address-state">State</label>
                                            <div class="col-lg-9">
                                                <div class="select-wrap form-control">
                                                    <select id="purchase-pass-form-select-address-country" name="address_country">
                                                        <option value="US" selected>United States</option>
                                                    </select>
                                                    <div class="select-box"></div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="purchase-pass-form-select-address-country">Country</label>
                                            <div class="col-lg-9">
                                                <div class="select-wrap form-control">
                                                    <select id="purchase-pass-form-select-address-state" name="address_state" required>
                                                        <option value="">- Select State -</option>
                                                        <option value="AL">Alabama</option>
                                                        <option value="AK">Alaska</option>
                                                        <option value="AZ">Arizona</option>
                                                        <option value="AR">Arkansas</option>
                                                        <option value="CA">California</option>
                                                        <option value="CO">Colorado</option>
                                                        <option value="CT">Connecticut</option>
                                                        <option value="DE">Delaware</option>
                                                        <option value="FL">Florida</option>
                                                        <option value="GA">Georgia</option>
                                                        <option value="HI">Hawaii</option>
                                                        <option value="ID">Idaho</option>
                                                        <option value="IL">Illinois</option>
                                                        <option value="IN">Indiana</option>
                                                        <option value="IA">Iowa</option>
                                                        <option value="KS">Kansas</option>
                                                        <option value="KY">Kentucky</option>
                                                        <option value="LA">Louisiana</option>
                                                        <option value="ME">Maine</option>
                                                        <option value="MD">Maryland</option>
                                                        <option value="MA">Massachusetts</option>
                                                        <option value="MI">Michigan</option>
                                                        <option value="MN">Minnesota</option>
                                                        <option value="MS">Mississippi</option>
                                                        <option value="MO">Missouri</option>
                                                        <option value="MT">Montana</option>
                                                        <option value="NE">Nebraska</option>
                                                        <option value="NV">Nevada</option>
                                                        <option value="NH">New Hampshire</option>
                                                        <option value="NJ">New Jersey</option>
                                                        <option value="NM">New Mexico</option>
                                                        <option value="NY">New York</option>
                                                        <option value="NC">North Carolina</option>
                                                        <option value="ND">North Dakota</option>
                                                        <option value="OH">Ohio</option>
                                                        <option value="OK">Oklahoma</option>
                                                        <option value="OR">Oregon</option>
                                                        <option value="PA">Pennsylvania</option>
                                                        <option value="RI">Rhode Island</option>
                                                        <option value="SC">South Carolina</option>
                                                        <option value="SD">South Dakota</option>
                                                        <option value="TN">Tennessee</option>
                                                        <option value="TX">Texas</option>
                                                        <option value="UT">Utah</option>
                                                        <option value="VT">Vermont</option>
                                                        <option value="VA">Virginia</option>
                                                        <option value="WA">Washington</option>
                                                        <option value="WV">West Virginia</option>
                                                        <option value="WI">Wisconsin</option>
                                                        <option value="WY">Wyoming</option>
                                                    </select>
                                                    <div class="select-box"></div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="purchase-pass-form-input-address-zip-code">Zip Code:</label>
                                            <div class="col-lg-9">
                                                <input id="purchase-pass-form-input-address-zip-code" class="form-control" type="text" name="address_zip_code" placeholder="* Required" maxlength="255" data-format="zip">
                                            </div>
                                        </div>

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
                                        <input id="purchase-pass-form-select-cc-type" name="cc_type" value="Visa" type="hidden"/>
                                        <input id="purchase-pass-form-input-cc-number" class="form-control" name="cc_number" value="4111111111111111" type="hidden"/>
                                        <input id="purchase-pass-form-select-cc-expiry-month" name="cc_expiry_month" value="12" type="hidden"/>
                                        <input id="purchase-pass-form-select-cc-expiry-year" name="cc_expiry_year" value="2030" type="hidden"/>
                                        <input id="purchase-pass-form-input-cc-cvv" name="cc_cvv" value="321" type="hidden"/>
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
<script src="https://www.paypal.com/sdk/js?client-id=AU_aqkdxvcHEqW596MMhDnmna1TC8wgZTeIjzyMwLdVDwog98PLjVIjEPsNtKkC0OUjbVEp-VVk23HPC&currency=USD"></script>

<script>
    $(function() {
        var mainCSS          = $('link[href^="/css/styles-main.min.css"]');
        var ajaxForm         = $('#purchase-pass-form');
        var form             = $('#purchase-pass-form form');
        var captcha          = $('#purchase-pass-form-captcha');
        var packages         = $('select.package-qty'); // ✅ ensure these selects have class="package-qty"
        var amountDisplay    = $('#amount_display');
        var computedPrice    = $('#computed_price');
        var amountHidden     = $('#purchase-pass-form-input-amount');
        var originalPrice    = $('#original_price');
        var discountTotal    = $('#discount_total');
        var paypalContainer  = $('#paypal-button-container'); // ensure exists

        const yes = document.getElementById('purchase-friend-yes');
        const no = document.getElementById('purchase-friend-no');
        const nameSelect = document.getElementById('name_on_pass');
        const hiddenName = document.getElementById('name_on_pass_hidden');
        const nameWrapper = document.getElementById('name-on-pass-wrapper');

        // 🔒 Force disable on page load if "No" is selected
        if (no && no.checked && nameSelect) {
            nameSelect.disabled = true;
            nameSelect.classList.add('bg-light');
            nameSelect.selectedIndex = 0;
            hiddenName.value = nameSelect.value;
        }

        // 🎟️ Enforce BOGO seat limit
        $('select.seat-bogo-select').on('afterSelect', function(event, values) {
            var $select = $(this);
            var maxSeats = 2;
            var selectedOptions = $select.find('option:selected');
            if (selectedOptions.length > maxSeats) {
                var lastValue = values[values.length - 1];
                $select.multiSelect('deselect', lastValue);
                alert('You can only select ' + maxSeats + ' seats.');
            }
        });

        // 👤 Sync “Name on Pass” toggle
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

        // 📝 Mirror hidden field on submit if locked
        $('#purchase-pass-form form').on('submit', function() {
            if (nameSelect && nameSelect.disabled && hiddenName) {
                $(this).find('input[name="name_on_pass"]').remove();
                $('<input>', {
                    type: 'hidden',
                    name: 'name_on_pass',
                    value: hiddenName.value
                }).appendTo(this);
            }
        });

        // 🧩 Init Plugins and Captcha
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
                var requestData = Object.assign(form.serializeObject(), $('.package-data-table').serializeObject());
                $.ajax('/ajax/events/packages/purchase-pass', {
                    data: requestData,
                    dataType: 'json',
                    method: 'post',
                    async: true,
                    beforeSend: showLoader,
                    complete: hideLoader,
                    success: function(response) {
                        switch (response.status) {
                            case 'success':
                                ajaxForm.html(response.html);
                                packages.parents('.package-data-table').remove();

                                // ✅ Add Referral Share Section
                                let shareSection = `
                            <div class="alert alert-info text-center mt-4 p-4" style="max-width:600px;margin:auto;">
                                <h5>Share This Event!</h5>
                                <p>Invite friends to RSVP and join you — copy your unique link below:</p>
                                <div class="input-group mb-2">
                                    <input id="referral-link" type="text" class="form-control" readonly
                                        value="${window.location.origin}/events/${window.location.pathname.split('/')[2]}/purchase-pass-auth?ref=${window.memberId || 'guest'}">
                                    <button type="button" class="btn btn-primary"
                                        onclick="navigator.clipboard.writeText(document.getElementById('referral-link').value)">
                                        Copy Link
                                    </button>
                                </div>
                                <small class="d-block text-muted">Your friends can reserve free passes too!</small>
                            </div>`;
                                $('#purchase-pass-form').append(shareSection);

                                // Scroll to Top
                                $('html, body').animate({
                                    scrollTop: ajaxForm.offset().top - ($('#nav-wrapper').height() || 0) - 30
                                }, 1000);
                                break;

                            case 'error':
                                displayMessage(
                                    response.message ||
                                    Object.keys(response.errors).map(function(key) {
                                        return response.errors[key];
                                    }).join('<br>'),
                                    'alert'
                                );
                                break;

                            default:
                                displayMessage(response.message || 'Something went wrong.', 'alert');
                        }
                    }
                });
            });
        });

        // 💲 Pricing + PayPal Visibility
        function recalc() {
            var total = 0.00;
            $('.package-data-table .btn-reveal-trigger').each(function() {
                var row   = $(this);
                var price = parseFloat(row.data('price')) || 0;
                var qty   = parseInt(row.find('select.package-qty').val(), 10) || 0;
                total += (price * qty);
            });

            var displayTotal = total;
            if (displayTotal <= 0) {
                amountDisplay.text('Free');
                paypalContainer.empty();
            } else {
                amountDisplay.text(displayTotal.toFixed(2));
                initPayPalButton(displayTotal.toFixed(2)); // PayPal init
            }

            computedPrice.val(displayTotal.toFixed(2));
            amountHidden.val(displayTotal.toFixed(2));
            originalPrice.val(displayTotal.toFixed(2));
        }

        // 🪙 PayPal Buttons
        function initPayPalButton(total) {
            paypalContainer.empty();
            paypal.Buttons({
                style: { layout: 'vertical', color: 'blue', shape: 'rect', label: 'paypal' },
                createOrder: function(data, actions) {
                    return actions.order.create({
                        purchase_units: [{ amount: { value: total } }]
                    });
                },
                onApprove: function(data, actions) {
                    return actions.order.capture().then(function(details) {
                        // Add PayPal transaction id then submit via AJAX
                        $('<input>', {
                            type: 'hidden',
                            name: 'paypal_transaction_id',
                            value: details.id
                        }).appendTo(form);
                        form.trigger('submit'); // ✅ Correct final call
                    });
                },
                onError: function(err) {
                    console.error(err);
                    alert('Payment error: ' + err);
                }
            }).render('#paypal-button-container');
        }

        // Initial calc + live updates
        recalc();
        $(document).on('change', 'select.package-qty', recalc);
    });
</script>


<?php include('includes/body-close.php'); ?>
