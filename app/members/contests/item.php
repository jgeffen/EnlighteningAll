<?php
	/*
	Copyright (c) 2022 Daerik.com
	This script may not be copied, reproduced or altered in whole or in part.
	We check the Internet regularly for illegal copies of our scripts.
	Do not edit or copy this script for someone else, because you will be held responsible as well.
	This copyright shall be enforced to the full extent permitted by law.
	Licenses to use this script on a single website may be purchased from Daerik.com
	@Author: Daerik
	*/
	
	/**
	 * @var Router\Dispatcher $dispatcher
	 * @var Membership        $member
	 */
	
	// Fetch/Set Item
	$item = Items\Contest::Init($dispatcher->getPageUrl());
	
	// Check Item
	if(is_null($item)) Render::ErrorDocument(404);
	
	// Search Engine Optimization
	$page_title       = $item->getTitle();
	$page_description = $item->getDescription();
	
	// Page Variables
	$top_image = $item->getLandscapeImage();
	
	// Start Header
	include('includes/header.php');
?>

<div class="container-fluid main-content">
	<div class="container">
		<div class="row">
			<div class="col">
				<h1><?php echo $item->getHeading(); ?></h1>
				
				<p><b>Start Date: </b><?php echo $item->getDateStart()->format('F jS, Y'); ?></p>
				<p><b>End Date: </b><?php echo $item->getDateEnd()->format('F jS, Y'); ?></p>
				<p><b>Number of Winners: </b><?php echo $item->getNumberOfWinners(); ?></p>
				
				<?php if($item->hasImage()): ?>
					<div class="lightbox">
						<a class="right inset border mt-0 mt-sm-0 mt-md-1" href="<?php echo $item->getImage(); ?>">
							<img class="lazy" src="<?php echo Items\Defaults::LANDSCAPE_THUMB; ?>" data-src="<?php echo $item->getLandscapeImage(); ?>" alt="<?php echo $item->getFilenameAlt(); ?>">
						</a>
					</div>
				<?php endif; ?>
				
				<?php echo $item->getContent(); ?>
				
				<?php if($item->getYoutubeId()): ?>
					<hr class="clear my-5">
					
					<div class="row justify-content-center">
						<div class="col-md-8 col-lg-6">
							<div class="fitvid">
								<?php echo $item->getYoutubeEmbed(); ?>
							</div>
						</div>
					</div>
				<?php endif; ?>
				
				<?php if(!$item->getPosts($member)->empty()): ?>
					<hr class="clear my-5">
					
					<div class="row posts">
						<?php foreach($item->getPosts($member) as $post): ?>
							<div class="col-md-6 col-xl-4 col-mb posts__child" data-post-id="<?php echo $post->getId(); ?>">
								<a href="#" data-post-action="modal">
									<img class="img-fluid m-0 border border-bottom-0 lazy" src="<?php echo Items\Defaults::SQUARE; ?>" alt="<?php echo $post->getMember()->getUsername(); ?>" data-src="<?php echo $post->getImage(); ?>">
								</a>
								
								<div class="toolbar-footer rounded-bottom">
									<?php
										Render::Template('members/posts/toolbar/buttons/like.twig', array(
											'count'  => $post->likes()->count(),
											'active' => $post->likes()->lookup($member)
										));
									?>
									
									<div class="toolbar__separator"></div>
									
									<?php
										Render::Template('members/posts/toolbar/buttons/comment.twig', array(
											'action'     => 'modal',
											'active'     => $post->comments()->lookup($member),
											'collapse'   => FALSE,
											'count'      => $post->comments()->count(),
											'scrollable' => TRUE
										));
									?>
									
									<div class="toolbar__separator"></div>
									
									<?php
										Render::Template('members/posts/toolbar/buttons/report.twig', array(
											'active' => $post->reports()->lookup($member)
										));
									?>
								</div>
							</div>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>

<?php include('includes/footer.php'); ?>
<?php include('includes/body-close.php'); ?>
