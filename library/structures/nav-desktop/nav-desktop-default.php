<div id="nav-top">
	<nav class="container-fluid" id="nav-wrapper" role="navigation">
		<div class="container">
			<div class="row">
				<div class="col">
					<div class="nav-content nav-bar-lg d-none d-xl-block">
						<?php include('includes/nav-links.php'); ?>
					</div>
					<div class="nav-content nav-bar-sm d-xl-none">
						<ul>
							<li>
								<a href="tel: <?php echo SITE_PHONE; ?>" title="Call <?php echo SITE_COMPANY; ?>">
									<i class="fas fa-phone"></i>
								</a>
							</li>
                            <li style="margin:0;padding:0;list-style:none;">
                                <a href="/events" title="See Schedule"
                                   style="
                                   display:block;
                                   background-color:#d4af37;
                                   color:black;
                                   font-weight:bold;
                                   padding:12px 24px;
                                   border-radius:10px;
                                   text-transform:uppercase;
                                   text-align:center;
                                   text-decoration:none;
                                   transition:all 0.3s ease;
                                   box-shadow:0 2px 5px rgba(0,0,0,0.3);
                                 "
                                   onmouseover="this.style.backgroundColor='#c49b2e';"
                                   onmouseout="this.style.backgroundColor='#d4af37';">
                                    SEE SCHEDULE
                                </a>
                            </li>

                            <?php if(Membership::LoggedIn()): ?>
								<li>
									<a href="/members/posts/social/add" title="Create New Post">
										<i class="fa-solid fa-camera-retro border-0 ml-0 rounded-0" style="font-size:42px;"></i>
									</a>
								</li>
							<?php endif; ?>
							
							<li>
								<a href="#" class="open-menu" title="Click Enter to Open Main Menu">
									<i class="fas fa-bars"></i>
								</a>
							</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</nav>
</div>
