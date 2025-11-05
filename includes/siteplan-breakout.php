<?php
	Render::Component('breakouts/content-breakout/content-breakout', array(
		'content_breakout_id'     => $content_breakout_id = ($content_breakout_id ?? 0) + 1,
		'image'                   => '', //IMAGE SIZE: 1000 x 667px
		'image_position'          => 'left', //OPTIONS: left, right
		'background_image'        => '/images/layout/default-landscape.jpg', //OPTIONS: Provide path for Background Image
		'background_image_scroll' => 'parallax', //OPTIONS: parallax, fixed - Leave empty for normal background image
		'full_width_content'      => FALSE, //OPTIONS TRUE, FALSE - Setting to TRUE will make the content expand to the edge of the window
		'height'                  => '', //OPTIONS: Set a fixed height in px if there is no content and it's just a background image(example: 500px)
		'background_color'        => 'black', //OPTIONS: primary, accent, neutral, black, white
		'text_color'              => 'white', //OPTIONS: primary, accent, neutral, black, white
		'html'                    => '
			<div class="py-5">
				<div class="row justify-content-around align-items-center">
					<div class="col-sm-10 col-md-8 col-lg-6 col-xl-5 text-center">
						<h2 class="title-super-md bold mb-2">Host Your Own Event</h2>
						<p class="content-super mb-4">Contact us today to get started hosting your events at Enlightening All!</p>
						
					</div>
					<div class="col-lg-3 text-center my-3">
						<p><b>Location:</b></p>
						<div class="lightbox">
							<a href="/images/enlightening-all-location.jpg">
								<img src="/images/enlightening-all-location.jpg" class="img-fluid m-0 border" alt="Enlightening All Siteplan">
							</a>
						</div>
					</div>
					
				</div>
			</div>
		'
	));