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
	 * @var Membership        $member
	 */
	
	// Imports
	use Items\Enums\Sizes;
	use Items\Members\Reservation;
	
	// Variable Defaults
	$item = Items\Event::Init($dispatcher->getRoute()?->getTableId());
	
	// Check Item
	if(is_null($item)) Render::ErrorDocument(404);
	
	// Sort Reservations
	$item->sortReservations(fn(Reservation $a, Reservation $b) => $a->getMember()?->getUsername() <=> $b->getMember()?->getUsername());
	
	$now        = new DateTime();
	$threshold  = (clone $now)->modify('+10 days');
	$start_date = $item->getStartDate() instanceof DateTime
		? $item->getStartDate()
		: new DateTime($item->getStartDate());
	
	// Search Engine Optimization
	$page_title       = $item->getTitle();
	$page_description = $item->getDescription();
	
	// Page Variables
	$top_image = $item->getLandscapeImage();
	
	// Start Header
	include('includes/header.php');
?>
<style>
    @media (max-width: 767.98px) {
        .btn-toolbar {
            flex-wrap: wrap !important;
        }
        .btn-toolbar .btn {
            flex: 1 1 100%;
            margin-bottom: 0.5rem;
        }
    }

</style>

<div class="container-fluid main-content">
	<div class="container">
		<div class="row">
			<div class="col">
                <div class="trim pt-4 pb-2">
                    <div class="title-bar">
                        <i class="fal fa-clipboard-list-check"></i>
                        <h2><?php echo $item->getHeading(); ?></h2>
                    </div>
					<div class="row">
						<div class="col-md-6">
							<p>
								<i class="fal fa-calendar-alt"></i>
								<b>Date:</b> <?php echo $item->getDate(); ?>
							</p>
						</div>
						
						<?php if($item->getEventTimes()): ?>
							<div class="col-md-6">
								<p>
									<i class="fa-solid fa-clock"></i>
                                <b>Time:</b> <?php echo $item->getEventTimes(); ?>
								</p>
							</div>
						<?php endif; ?>
						
						<?php if($item->getPriceText()): ?>
							<div class="col-md-6">
								<p>
									<i class="fal fa-tags"></i>
									<b>Price:</b> <?php if($item->getPriceText() != 'Free'):?>$<?php endif;?><?php echo $item->getPriceText();?>
								</p>
							</div>
						<?php endif; ?>
						
						<div class="col-md-6">
							<p>
								<i class="fa-solid fa-share"></i>
								<b>Share:</b>
								<a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($item->getLink()); ?>" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-facebook">
									<i class="fab fa-facebook-f"></i>
								</a>
								
								<a href="https://twitter.com/intent/tweet?url=<?php echo urlencode($item->getLink()); ?>&text=<?php echo urlencode($item->getLink()); ?>" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-twitter">
									<i class="fab fa-x-twitter"></i>
								</a>
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

                    <?php // if(Admin\Privilege(2) || $item->isUpcomingEvent()): ?>
                        <div id="<?php echo sprintf("event-toolbar-%s", $item->getId()); ?>" class="card-footer" data-event-id="<?php echo $item->getId(); ?>">
                            <div class="d-flex justify-content-center">
                                <div class="btn-toolbar mx-auto" role="toolbar" aria-label="<?php echo sprintf("Button Toolbar for %s", $item->getAlt()); ?>">
                                    <div class="btn-group btn-group-sm" role="group" aria-label="<?php echo sprintf("Button Group for %s", $item->getAlt()); ?>">
                                        <?php if(Admin\Privilege(2)): ?>
                                            <a class="btn btn-info" style="display: flex; justify-content: center; align-items: center; font-size: 1.1rem; padding: 0.75rem 1.5rem; text-align: center;" href="<?php echo $item->getButton(Items\Event::BUTTON_EDIT); ?>" target="_blank">
                                                <i class="fas fa-edit fa-fw"></i>
                                                <span class="d-none d-lg-inline-block">Edit</span>
                                            </a>
                                        <?php endif; ?>



                                            <?php if(Membership::LoggedIn()): ?>
                                                <?php if($member?->reservations()->lookup($item)): ?>
                                                    <?php if(!$member?->reservations()->lookup($item)->isPaid()): ?>
                                                        <button type="button" class="btn btn-danger" data-event-action="rsvp-remove">
                                                            <i class="fas fa-times-circle fa-fw"></i>
                                                            <span class="d-none d-lg-inline-block">Remove RSVP</span>
                                                        </button>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            <?php endif; ?>

                                            <?php foreach($item->getPackages() as $package): ?>
                                                    <a class="btn btn-warning"
                                                       style="display:flex;justify-content:center;align-items:center;font-size:1.1rem;padding:0.75rem 1.5rem;text-align:center;"
                                                       href="<?php echo $item->getButton(Items\Event::BUTTON_PASS); ?>">
                                                        <i class="fa-solid fa-ticket"></i>
                                                        <span class="d-none d-lg-inline-block">Get Pass</span>
                                                        <span class="d-inline d-lg-none">Get Pass</span>
                                                    </a>
                                            <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php // endif; ?>
               <br>
				<?php echo $item->getContent(); ?>
            <!-- Addition of the image uploaded in the events add/edit page --->
                <?php
                $class_type_label = Items\Event::Options('class_types', $item->getClassType()) ?? $item->getAlt();
                $mapped_image = Items\Event::Options('image_types', $item->getClassType()) ?? sprintf('/images/event-types/%s.png', $item->getClassType()) ?? '/images/event-types/main-logo.png';
                $data_src = $item->getPosterImage() ?: $mapped_image;

                // ✅ Auto WebP fallback
                $webp_path = preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $data_src);
                $final_src = file_exists($_SERVER['DOCUMENT_ROOT'] . $webp_path) ? $webp_path : $data_src;
                ?>
                <a href="<?php echo $item->getLink(); ?>" title="<?php echo $item->getAlt(); ?>"
                   style="display:block;text-align:center;">
                    <picture style="display:inline-block;max-width:100%;">
                        <source srcset="<?php echo $final_src; ?>" type="image/webp">
                        <img
                                src="<?php echo $final_src; ?>"
                                loading="lazy"
                                alt="<?php echo htmlspecialchars($class_type_label); ?>"
                                class="full-width-article__image border lazy-fade"
                                style="max-width:100%;height:auto;width:auto;margin:0 auto;display:block;"
                        >
                    </picture>
                </a>
                <?php if($item->getYoutubeId()): ?>
					<hr class="clear mb-5">
					
					<div class="row justify-content-center">
						<div class="col-md-8 col-lg-6">
							<div class="fitvid">
								<?php echo $item->getYoutubeEmbed(); ?>
							</div>
						</div>
					</div>
				<?php endif; ?>
				
				<?php if($item->getPDFs()): ?>
					<hr class="clear my-5">
					
					<?php
					Render::component('one-page-articles/titlebar-trim-article/titlebar-trim-article', array(
						'items'    => $item->getPDFs(),
						'icon'     => '<i class="fa-light fa-file-pdf"></i>',
						'cols'     => '3',
						'btn_text' => 'Download PDF'
					));
					?>
				<?php endif; ?>
				
				<?php if(Membership::LoggedIn()): ?>
					<?php if($item->isDisplayRsvps()): ?>
						<div id="<?php echo sprintf("event-rsvps-%s", $item->getId()); ?>">
							<?php if($item->getReservations()): ?>
								<hr class="clear my-5">
								
								<h3 class="mt-3"><sup>*</sup>Members Attending: <?php echo $item->getTotalReservations(); ?></h3>
								
								<div class="row row-cols-3 row-cols-md-4 row-cols-lg-5 row-cols-xl-6">
									<?php foreach($item->getReservations() as $reservation): ?>
										<?php if($reservation->getMember()): ?>
											<?php if($reservation->getMember()->isDisplayRsvps()): ?>
												<div class="col col-mb" data-profile-id="<?php echo $reservation->getMember()->getId(); ?>">
													<a class="card" href="<?php echo $reservation->getMember()->getLink(); ?>">
														<img class="card-img-top" src="<?php echo Items\Defaults::AVATAR_LG; ?>" data-src="<?php echo $reservation->getMember()->getAvatar()?->getImage(Sizes\Avatar::LG); ?>" alt="<?php echo $reservation->getMember()->getUsername(); ?>">
														<div class="card-footer text-center">
															<p class="text-truncate mb-0"><small>@<?php echo $reservation->getMember()->getUsername(); ?></small></p>
														</div>
													</a>
												</div>
											<?php else: ?>
												<div class="col col-mb">
													<a class="card" href="#">
														<img class="card-img-top" src="<?php echo Items\Defaults::AVATAR_LG; ?>">
														<div class="card-footer text-center">
															<p class="text-truncate mb-0"><small>Private</small></p>
														</div>
													</a>
												</div>
											<?php endif; ?>
										<?php endif; ?>
									<?php endforeach; ?>
								</div>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>

<?php include('includes/footer.php'); ?>

<?php include('includes/body-close.php'); ?>

