<ul>
	<li><a href="<?php echo curSiteURL(); ?>/">Home</a></li>
    <li><a href="/events">Classes & Events</a></li>
	<li><a href="/services">Services</a></li>
	<li><a href="/products">Products</a></li>
	<li><a href="/faqs">FAQ's</a></li>
	<li><a href="/careers">Careers</a></li>
	<li><a href="/contact">Contact</a></li>
	<li style="background: #e9e9e9;">
		<a class="text-uppercase" href="#">Social <span class="badge badge-primary ml-2 mr-1 total-count"></span> <i class="fa fa-caret-down"></i></a>
		<ul>
			<?php if(Membership::LoggedIn(FALSE)): ?>
				<li><a href="/members/register">Sign Up</a></li>
				<li><a href="/members/login">Login</a></li>
			<?php else: ?>
				<?php $member ??= new Membership(); ?>
				<?php if($member->isTeacherApproved()): ?>
					<li><a href="/class-attendance"><i class="fa-solid fa-screen-users"></i> My Classes</a></li>
				<?php endif; ?>
				<li>
					<a href="/members/notifications">
						Notifications <span class="badge badge-primary ml-1 notification-count"></span>
					</a>
				</li>
				
				<li>
					<a href="/members/messages">
						Messages <span class="badge badge-primary ml-1 messages-count"></span>
					</a>
				</li>
				<li>
					<a href="/bar">
						Food & Drinks
					</a>
				</li>
				<!-- <li>
					<a href="/members/my-wallet">
						My Wallet
					</a>
				</li> -->
				<li>
					<a href="/members/reservations">
						My Reservations
					</a>
				</li>
				
				<li>
					<a href="/members/transactions">
						My Purchases
					</a>
				</li>
				
				<li><a href="<?php echo $member->getLink(); ?>">My Profile</a></li>
				<li><a href="/members/posts/social/manage">My Posts</a></li>
				<li><a href="/members/settings">Profile Settings</a></li>
				<li><a href="/members/profile-link">Profile Link</a></li>
				<!--- <li><a href="/members/subscription">Subscription</a></li> -->
				<li><a href="/members/walls/public">Members Wall</a></li>
				
				<?php if(!$member->friends()->empty()): ?>
					<!-- <li><a href="/members/walls/friends">Friends Wall</a></li> -->
					<li><a href="/members/friends">My Circle</a></li>
				<?php endif; ?>
				
				<?php if(is_null($member->getCheckIn())): ?>
					<li><a href="/members/check-in">Check-In</a></li>
				<?php else: ?>
					<li><a href="/members/check-out">Check-Out</a></li>
				<?php endif; ?>
				
				<?php if(filter_input(INPUT_SERVER, 'REMOTE_ADDR') == '184.90.247.126'): ?>
					<?php if($member->subscription()?->isPaid()): ?>
						<li><a href="/members/free-drink">Free Drink</a></li>
					<?php endif; ?>
				<?php endif; ?>
				
				<?php if(!$member->isApproved()): ?>
					<li><a href="/members/account-approval">Account Approval</a></li>
				<?php endif; ?>
				
				<li><a href="/members/contests">Contests</a></li>
				<li><a href="/members/faqs">FAQs</a></li>
				<li><a href="#" data-nav-action="member-lookup">Member Lookup</a></li>
				<?php if(!$member->isIntakeSurvey()): ?>
					<li><a href="/intake">Intake Survey</a></li>
				<?php endif; ?>
				<li><a href="/members/logout">Logout</a></li>
			<?php endif; ?>
		</ul>
	</li>
</ul>