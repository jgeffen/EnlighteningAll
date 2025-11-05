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

// Imports
use Items\Collections;

// Variable Defaults
$subscriptions = new Collections\Subscriptions(
        Database::Action("SELECT * FROM subscriptions ORDER BY position DESC")
);

// Search Engine Optimization
$page_title = sprintf("Register Now - %s", SITE_COMPANY);
$page_description = "Verified Members Event Network";

// Start Header
include('includes/header.php');
?>

    <div class="container-fluid main-content">
        <div class="container">
            <div class="row">
                <div class="col">
                    <h1 class="title-underlined mb-4">
                        Join Enlightening All Social - Verified Members Event Network
                    </h1>
                    <h1 class="title mb-4">Your Subscription Benefits</h1>

                    <?php foreach ($subscriptions as $subscription) : ?>
                        <div id="<?php echo sprintf("subscription__wrapper-%s", $subscription->getId()); ?>"
                             class="title-bar-trim-combo mt-5"
                             data-id="<?php echo $subscription->getId(); ?>">
                            <div class="title-bar">
                                <?php echo $subscription->renderIcon('fa-light'); ?>
                                <h2><?php echo $subscription->getName(); ?></h2>
                            </div>
                            <div class="form-wrap trim p-lg-4">
                                <div class="mt-lg-2">
                                    <div class="row justify-content-center">
                                        <div class="col-lg-6">
                                            <h2 class="title-underlined mb-2">Plan Benefits</h2>
                                            <?php echo $subscription->getBenefits(); ?>
                                        </div>
                                        <div class="col-lg-6">
                                            <h2 class="title-underlined mb-2">Payment</h2>
                                            <?php echo $subscription->getContent(); ?>
                                            <?php if ($subscription->getPrice()) : ?>
                                                <p>All this for just <b><?php echo $subscription->getPrice(TRUE); ?></b> a month.</p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <p>Use the form below to create an account for Enlightening All Social.</p>

            <div role="form" class="title-bar-trim-combo mt-5">
                <div class="title-bar">
                    <i class="fal fa-clipboard-list-check"></i>
                    <h2>Registration Form</h2>
                </div>

                <div id="register-form" class="form-wrap trim p-lg-4">
                    <form class="mt-lg-2">
                        <div class="row justify-content-center">
                            <div class="col-lg-6">
                                <?php
                                Render::Component('form-units/input.field', [
                                        'form' => 'register-form',
                                        'label' => 'First Name',
                                        'column' => 'first_name',
                                        'type' => 'text',
                                        'validate' => 'general',
                                        'max_length' => 16,
                                        'horizontal' => TRUE
                                ]);

                                Render::Component('form-units/input.field', [
                                        'form' => 'register-form',
                                        'label' => 'Last Name',
                                        'column' => 'last_name',
                                        'type' => 'text',
                                        'validate' => 'general',
                                        'max_length' => 16,
                                        'horizontal' => TRUE
                                ]);

                                Render::Component('form-units/input.field', [
                                        'form' => 'register-form',
                                        'label' => 'Email',
                                        'column' => 'email',
                                        'type' => 'email',
                                        'validate' => 'email',
                                        'max_length' => 64,
                                        'horizontal' => TRUE
                                ]);

                                Render::Component('form-units/input.field', [
                                        'form' => 'register-form',
                                        'label' => 'Phone',
                                        'column' => 'phone',
                                        'type' => 'text',
                                        'mask' => 'phone',
                                        'max_length' => 14,
                                        'horizontal' => TRUE
                                ]);

                                Render::Component('form-units/select.field', [
                                        'form' => 'register-form',
                                        'label' => 'Country',
                                        'column' => 'country',
                                        'horizontal' => TRUE,
                                        'options' => Locations\Country::Options(
                                                Database::Action("SELECT * FROM location_countries ORDER BY name")
                                        ),
                                        'default' => 'US'
                                ]);

                                Render::Component('form-units/input.field', [
                                        'form' => 'register-form',
                                        'label' => 'Postal Code',
                                        'column' => 'postal_code',
                                        'type' => 'text',
                                        'max_length' => 5,
                                        'horizontal' => TRUE,
                                        'mask' => 'postal'
                                ]);
                                ?>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group row">
                                    <label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1"
                                           for="register-form-file-avatar">
                                        Upload Profile Photo :
                                    </label>
                                    <div class="col-lg-9">
                                        <div class="input-group">
                                            <div class="custom-file">
                                                <input id="register-form-file-avatar" class="custom-file-input"
                                                       type="file" name="avatar"
                                                       accept="image/png,image/jpeg">
                                                <label class="custom-file-label"
                                                       for="register-form-file-avatar">* Required</label>
                                            </div>
                                        </div>
                                        <small class="text-danger">Please do not upload your photo ID.</small>
                                    </div>

                                    <div class="col-lg-9 mt-4">
                                        <?php
                                        Render::Component('form-units/checkbox.field', [
                                                'form' => 'register-form',
                                                'label' => 'Teacher Account?',
                                                'column' => 'teacher',
                                                'options' => [1 => 'Yes', 0 => 'No'],
                                                'values' => [0],
                                                'max_length' => 1,
                                                'horizontal' => FALSE
                                        ]);
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="mt-0 mb-4">

                        <div class="row justify-content-center">
                            <div class="col-lg-6">
                                <?php
                                Render::Component('form-units/input.field', [
                                        'form' => 'register-form',
                                        'label' => 'Username',
                                        'column' => 'username',
                                        'type' => 'text',
                                        'validate' => 'general',
                                        'max_length' => 16,
                                        'horizontal' => TRUE
                                ]);

                                Render::Component('form-units/input.field', [
                                        'form' => 'register-form',
                                        'label' => 'Password',
                                        'column' => 'password',
                                        'type' => 'password',
                                        'validate' => 'general',
                                        'horizontal' => TRUE
                                ]);

                                Render::Component('form-units/input.field', [
                                        'form' => 'register-form',
                                        'label' => 'Re-Type Password',
                                        'column' => 'retype_password',
                                        'type' => 'password',
                                        'validate' => 'general',
                                        'horizontal' => TRUE
                                ]);
                                ?>
                            </div>

                            <div class="col-lg-6">
                                <div class="password-block mt-3 mt-lg-0 mb-3">
                                    <h3 class="title-underlined">Password Requirements</h3>
                                    <div class="form-group row split-list justify-content-center mb-0">
                                        <div class="col-xl-6">
                                            <ul class="fa-ul">
                                                <li><span class="fa-li"><i class="fas fa-exclamation-circle text-danger"></i></span> At least 8 Characters Long</li>
                                                <li><span class="fa-li"><i class="fas fa-exclamation-circle text-danger"></i></span> At least 1 Uppercase Character</li>
                                                <li><span class="fa-li"><i class="fas fa-exclamation-circle text-danger"></i></span> At least 1 Lowercase Character</li>
                                            </ul>
                                        </div>
                                        <div class="col-xl-6">
                                            <ul class="fa-ul">
                                                <li><span class="fa-li"><i class="fas fa-exclamation-circle text-danger"></i></span> At least 1 Number</li>
                                                <li><span class="fa-li"><i class="fas fa-exclamation-circle text-danger"></i></span> At least 1 Symbol($@#)</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="row justify-content-center">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <div class="cap-wrap text-center">
                                        <fieldset>
                                            <label class="col-form-label" for="captcha">
                                                Enter the Characters Shown Below
                                            </label>
                                            <input type="text" name="captcha" class="form-control" id="captcha"
                                                   required data-type="general" placeholder="* Required">
                                        </fieldset>
                                        <noscript>
                                            <p class="help-block">
                                                <span class="text-danger">(Javascript must be enabled to submit the form.)</span>
                                            </p>
                                        </noscript>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group row h-100 align-items-end justify-content-end">
                                    <div class="col-sm-9">
                                        <input class="form-check-input" type="checkbox" id="medicalWaiverCheckbox" required>
                                        <label class="form-check-label" for="medicalWaiverCheckbox">
                                            I have read and agree to the <a href="#" data-toggle="modal" data-target="#medical-waiver-modal">Yoga & Fitness – Waiver of Liability</a>.
                                        </label>
                                        <button type="submit" class="btn btn-block btn-primary submit-btn">Submit</button>

                                        <div class="text-center my-2">
                                            <small class="text-muted">
                                                By clicking "Submit" you agree to the
                                                <a href="#" data-toggle="modal"
                                                   data-target="#terms-and-conditions-for-users">Terms and Conditions</a>
                                                for users of this website.
                                            </small>
                                        </div>

                                        <div class="text-center my-2">
                                            <small class="text-muted">
                                                Already have an account?
                                                <a href="/members/login">Login</a>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Medical Waiver Modal -->
    <div class="modal fade" id="medical-waiver-modal" tabindex="-1" role="dialog" aria-labelledby="medicalWaiverLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="medicalWaiverLabel">Medical Waiver</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <p>
                        <strong>Disclaimer:</strong> By participating in any Enlightening All events or activities,
                        you acknowledge that physical movement and exercise may involve risk of injury.
                        You certify that you are physically fit to take part and assume all responsibility
                        for your own well-being. Enlightening All and its affiliates are not liable for
                        any injury, loss, or damages resulting from participation.
                    </p>
                    <p>Please read this waiver carefully before agreeing.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    <style>
        /* --- Local mobile centering fix --- */
        @media (max-width: 767.98px) {
            /* Force the last row (checkbox + button) to center */
            #register-form .form-group.row.h-100.align-items-end.justify-content-end {
                justify-content: center !important;
                text-align: center !important;
            }

            /* Make the inner column stack & center */
            #register-form .form-group.row.h-100.align-items-end.justify-content-end .col-sm-9 {
                flex: 0 0 100% !important;
                max-width: 100% !important;
                display: flex !important;
                flex-direction: column !important;
                align-items: center !important;
                text-align: center !important;
            }

            /* Center checkbox and label */
            #medicalWaiverCheckbox {
                margin: 0 auto 0.5rem auto !important;
                display: block !important;
            }

            label[for="medicalWaiverCheckbox"] {
                display: block !important;
                text-align: center !important;
                margin-bottom: 1rem !important;
            }

            /* Center submit button */
            #register-form .submit-btn {
                display: block !important;
                width: 90% !important;
                max-width: 380px !important;
                margin: 0 auto 1rem auto !important;
            }

            /* Center the disclaimer text under the button */
            #register-form .text-center.my-2 small {
                text-align: center !important;
                display: block !important;
            }
        }
    </style>

<?php include('includes/footer.php'); ?>

    <script>
        // check temporary email
        $(document).ready(function() {
            var tempEmailDomains = [
                '10minutemail.com', 'guerrillamail.com', 'mailinator.com',
                'temp-mail.org', 'yopmail.com', 'dispostable.com',
                'getnada.com', 'trashmail.com', 'maildrop.cc', 'fakeinbox.com'
            ];

            function isTemporaryEmail(email) {
                var emailDomain = email.split('@')[1];
                return tempEmailDomains.includes(emailDomain);
            }

            $('#register-form-input-email').on('keyup', function() {
                var email = $(this).val();
                $('span.text-danger:contains("Temporary email addresses are not allowed")').remove();

                if (isTemporaryEmail(email)) {
                    $('<span class="text-danger">Temporary email addresses are not allowed</span>')
                        .insertAfter('#register-form-input-email');
                    $('.submit-btn').prop('disabled', true);
                } else {
                    $('span.text-danger:contains("Temporary email addresses are not allowed")').remove();
                    $('.submit-btn').prop('disabled', false);
                }
            });
        });

        $(function() {
            var mainCSS = $('link[href^="/css/styles-main.min.css"]');
            var ajaxForm = $('#register-form');
            var captcha = $('#captcha');
            var partner = {
                section: $('#register-form-partner'),
                selector: $('select[name="couple"]')
            };

            $.when(
                $('<link/>', { type: 'text/css', rel: 'stylesheet', href: '/js/realperson/jquery.realperson.ada.css' }).insertBefore(mainCSS),
                $.ajax('/js/realperson/jquery.plugin.min.js', { async: false, dataType: 'script' }),
                $.ajax('/js/realperson/jquery.realperson.ada.js', { async: false, dataType: 'script' }),
                $.ajax('/js/quickdeploy/jquery.dependent.fields.min.js', { async: false, dataType: 'script' }),
                $.Deferred(function(deferred) { $(deferred.resolve); })
            ).done(function() {
                captcha.realperson();
                partner.section.dependsOn({ selector: partner.selector, value: ['1'], wrapper: null });

                ajaxForm.on('change', 'input[type="file"]', function() {
                    var input = $(this);
                    var allowed = input.prop('accept').split(',');

                    if (this.files[0].size > window.settings.maxFilesize.B) {
                        input.val('');
                        input.siblings('label').text('* Required');
                        displayMessage('The maximum file size is ' + window.settings.maxFilesize.MB + 'MB.', 'alert');
                        return;
                    }

                    if ($.inArray(this.files[0].type, allowed) === -1) {
                        input.val('');
                        input.siblings('label').text('* Required');
                        displayMessage('The only acceptable file types are ' + allowed.join(' or '), 'alert');
                        return;
                    }

                    input.siblings('label').text(this.files[0].name);
                });

                ajaxForm.on('submit', 'form', function(event) {
                    event.preventDefault();
                    var progressBar;

                    $.ajax('/ajax/members/registration', {
                        data: new FormData(this),
                        dataType: 'json',
                        method: 'post',
                        contentType: false,
                        processData: false,
                        async: true,
                        beforeSend: function() {
                            $.ajax('/ajax/members/registration/progress-bar', {
                                method: 'post',
                                dataType: 'html',
                                async: false,
                                success: function(modal) {
                                    progressBar = $(modal);
                                    progressBar.on('hidden.bs.modal', destroyModal).modal({ backdrop: 'static', keyboard: false });
                                }
                            });
                        },
                        success: function(response) {
                            progressBar.on('hidden.bs.modal', function() {
                                switch (response.status) {
                                    case 'success':
                                        ajaxForm.html(response.html);
                                        $('html, body').animate({
                                            scrollTop: ajaxForm.offset().top - ($('#nav-wrapper').height() || 0) - 90
                                        }, 1000);
                                        break;
                                    case 'error':
                                        displayMessage(response.message || Object.keys(response.errors).map(function(key) {
                                            return response.errors[key];
                                        }).join('<br>'), 'alert', null);
                                        break;
                                    default:
                                        displayMessage(response.message || 'Something went wrong.', 'alert');
                                }
                            }).modal('hide');
                        },
                        xhr: function() {
                            var myXhr = $.ajaxSettings.xhr();
                            if (myXhr.upload) {
                                myXhr.upload.addEventListener('progress', function(event) {
                                    var progress = (event.loaded / event.total * 100).toFixed(0) + '%';
                                    if (progressBar) {
                                        progressBar.find('.progress-bar').css('width', progress);
                                        progressBar.find('#progress-label').html(progress);
                                    }
                                }, false);
                            }
                            return myXhr;
                        }
                    });
                });
            });

            $(document.body).on('click', '[data-target="#terms-and-conditions-for-users"]', function() {
                $.ajax({
                    type: 'post',
                    url: '/ajax/pages/fetch-page-content-by-url',
                    data: { page_slug: 'terms-and-conditions-for-users' },
                    success: function(response) {
                        $(document)
                            .find('#terms-and-conditions-for-users')
                            .find('.modal-body')
                            .html(response.data.content);
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                    }
                });
            });
        });
    </script>

<?php include('includes/body-close.php'); ?>