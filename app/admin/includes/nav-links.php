<ul>
	<li><a href="/user/dashboard"><i class="fa-solid fa-tachometer-alt"></i> Dashboard</a></li>
	
	<li>
		<a href="#"><i class="fa-solid fa-cogs"></i> Utilities</a>
		<ul>
			<li><a href="/" target="_blank"><i class="fa-solid fa-browser"></i> View Site</a></li>
			<li><a href="/user/guidelines"><i class="fa-solid fa-book"></i>Guidelines & Tips</a></li>
			
			<?php if(Admin\Privilege(2)) : ?>
				<li><a href="/user/view/logs"><i class="fa-solid fa-file-alt"></i> Logs</a></li>
			<?php endif; ?>
		</ul>
	</li>
	
	<?php if(Admin\Privilege(2)) : ?>
		<li>
			<a href="#"><i class="fa-solid fa-users"></i> Users</a>
			<ul>
				<li><a href="/user/view/users"><i class="fa-solid fa-search"></i> View</a></li>
				<li><a href="/user/add/users"><i class="fa-solid fa-plus"></i> Add</a></li>
			</ul>
		</li>
		
		<li>
			<a href="#">
				<i class="fa-solid fa-flag"></i> Reports
				<span class="badge badge-danger ml-1 reports-count"><?php echo Admin\Reports() ?: NULL; ?></span>
			</a>
			<ul>
				<li>
					<a href="/user/view/member-reports/comments">
						<i class="fa-solid fa-comment"></i> Comments
						<span class="badge badge-danger ml-1 reports-comments-count"><?php echo Admin\Reports('comments') ?: NULL; ?></span>
					</a>
				</li>
				<li>
					<a href="/user/view/member-reports/messages">
						<i class="fa-solid fa-messages"></i> Messages
						<span class="badge badge-danger ml-1 reports-messages-count"><?php echo Admin\Reports('messages') ?: NULL; ?></span>
					</a>
				</li>
				<li>
					<a href="/user/view/member-reports/posts">
						<i class="fa-solid fa-signs-post"></i> Posts
						<span class="badge badge-danger ml-1 reports-posts-count"><?php echo Admin\Reports('posts') ?: NULL; ?></span>
					</a>
				</li>
				<li>
					<a href="/user/view/member-reports/profiles">
						<i class="fa-solid fa-address-card"></i> Profiles
						<span class="badge badge-danger ml-1 reports-profiles-count"><?php echo Admin\Reports('profiles') ?: NULL; ?></span>
					</a>
				</li>
				<li>
					<a href="/user/view/member-reports/tickets">
						<i class="fa-solid fa-ticket"></i> Tickets
						<span class="badge badge-danger ml-1 reports-tickets-count"><?php echo Admin\Reports('tickets') ?: NULL; ?></span>
					</a>
				</li>
			</ul>
		</li>
	<?php endif; ?>
	
	<?php if(Admin\Privilege(array(1, 2, 4, 6))) : ?>
		<li>
			<a href="#"><i class="fa-solid fa-restroom"></i> Membership</a>
			<ul>
				<li><a href="/user/view/members"><i class="fa-solid fa-users"></i> View Members</a></li>
				<li><a href="/user/view/member-posts"><i class="fa-solid fa-address-card"></i> View Posts</a></li>
				<li><a href="/user/check-in/members"><i class="fa-solid fa-clipboard-list-check"></i> Check-In</a></li>
				<li><a href="/user/free-drink/members"><i class="fa-solid fa-martini-glass-citrus"></i> Free Drink</a></li>
				
				<?php if(Admin\Privilege(2)) : ?>
					<li><a href="/user/view/subscriptions"><i class="fa-solid fa-stars"></i> View Subscriptions</a></li>
					<li><a href="/user/view/member-settings"><i class="fa-solid fa-users-cog"></i> Member Settings</a>
					</li>
				<?php endif; ?>
				
				<?php if(Admin\Privilege(2)) : ?>
					<li><a href="/user/view/member-faqs"><i class="fa-solid fa-messages-question"></i> FAQs</a></li>
				<?php endif; ?>
				<li><a href="/user/stats/members"><i class="fa-solid fa-chart-line"></i> Statistics</a></li>
				<li><a href="/user/view/message-setting"><i class="fa-solid fa-users-cog"></i>Message Settings</a>
			</ul>
		</li>
	<?php endif; ?>
	
	<?php if(Admin\Privilege(array(1, 2, 5, 7))) : ?>
		<li>
			<a href="#"><i class="fa-solid fa-user-doctor"></i> Careers</a>
			<ul>
				<li><a href="/user/view/careers"><i class="fa-solid fa-search"></i> View Posts</a></li>
				<li><a href="/user/add/careers"><i class="fa-solid fa-plus"></i> Add Post</a></li>
				
				<?php if(Admin\Categories('careers')) : ?>
					<li><a href="/user/view/categories/careers"><i class="fa-solid fa-th-list"></i> Categories</a></li>
				<?php endif; ?>
				
				<li><a href="/user/view/forms-careers"><i class="fa-solid fa-download"></i> Career Applications</a></li>
			</ul>
		</li>
	<?php endif; ?>
	
	<?php if(Admin\Privilege(2)) : ?>
		<li>
			<a href="#"><i class="fa-regular fa-folders"></i> Categories</a>
			<ul>
				<li><a href="/user/view/categories"><i class="fa-solid fa-search"></i> View Categories</a></li>
				<li><a href="/user/add/categories"><i class="fa-solid fa-plus"></i> Add Category</a></li>
			</ul>
		</li>
	<?php endif; ?>
	
	
	<?php if(Admin\Privilege(array(1, 2, 5))) : ?>
		<li>
			<a href="#"><i class="fa-solid fa-trophy-star"></i> Contests</a>
			<ul>
				<li><a href="/user/view/contests"><i class="fa-solid fa-search"></i> View Contests</a></li>
				<li><a href="/user/add/contests"><i class="fa-solid fa-plus"></i> Add Contest</a></li>
			</ul>
		</li>
	<?php endif; ?>
	
	<?php if(Admin\Privilege(array(1, 2, 4, 5, 6))) : ?>
		<li>
			<a href="#"><i class="fa-solid fa-calendar-star"></i> Events</a>
			<ul>
				<?php if(Admin\Privilege(2)) : ?>
					<li><a href="/user/view/events"><i class="fa-solid fa-search"></i> View Events</a></li>
					<li><a href="/user/add/events"><i class="fa-solid fa-plus"></i> Add Event</a></li>
				<?php endif; ?>
				
				<li><a href="/user/reservations/events"><i class="fa-solid fa-hand-holding-box"></i> Reservations</a></li>
				
				<?php if(Admin\Privilege(2)) : ?>
					<li><a href="/user/view/event-packages"><i class="fa-solid fa-box-full"></i> Packages</a></li>
					<li><a href="/user/add/event-packages"><i class="fa-solid fa-plus"></i> Add Package</a></li>
					
					<?php if(Admin\Categories('events')) : ?>
						<li><a href="/user/view/categories/events"><i class="fa-solid fa-th-list"></i> Categories</a></li>
					<?php endif; ?>
					<li><a href="/user/settings/events"><i class="fa-solid fa-cog"></i> Settings</a></li>
				<?php endif; ?>
			</ul>
		</li>
	<?php endif; ?>
	
	<?php if(Admin\Privilege(2)) : ?>
		<li>
			<a href="#"><i class="fa-solid fa-question-circle"></i> FAQs</a>
			<ul>
				<li><a href="/user/view/faqs"><i class="fa-solid fa-search"></i> View FAQs</a></li>
				<li><a href="/user/add/faqs"><i class="fa-solid fa-plus"></i> Add FAQ</a></li>
				
				<?php if(Admin\Categories('faqs')) : ?>
					<li><a href="/user/view/categories/faqs"><i class="fa-solid fa-th-list"></i> Categories</a></li>
				<?php endif; ?>
				
				<?php if(Admin\Privilege(2)) : ?>
					<li><a href="/user/settings/faqs"><i class="fa-solid fa-cog"></i> Settings</a></li>
				<?php endif; ?>
			</ul>
		</li>
		
		<li>
			<a href="#"><i class="fa-solid fa-file-invoice"></i> Forms</a>
			<ul>
				<li><a href="/user/view/forms-intake"><i class="fa-solid fa-comment"></i> Intake Survey</a></li>
			</ul>
		</li>
		
		<li>
			<a href="#"><i class="fa-solid fa-images"></i> Galleries</a>
			<ul>
				<li><a href="/user/view/galleries"><i class="fa-solid fa-search"></i> View Galleries</a></li>
				<li><a href="/user/add/galleries"><i class="fa-solid fa-plus"></i> Add Gallery</a></li>
				
				<?php if(Admin\Categories('galleries')) : ?>
					<li><a href="/user/view/categories/galleries"><i class="fa-solid fa-th-list"></i> Categories</a>
					</li>
				<?php endif; ?>
				
				<?php if(Admin\Privilege(2)) : ?>
					<li><a href="/user/settings/galleries"><i class="fa-solid fa-cog"></i> Settings</a></li>
				<?php endif; ?>
			</ul>
		</li>
	<?php endif; ?>
	
	<?php if(Admin\Privilege(array(1, 2, 5))) : ?>
		<li>
			<a href="#"><i class="fa-solid fa-newspaper"></i> News</a>
			<ul>
				<li><a href="/user/view/news"><i class="fa-solid fa-search"></i> View Posts</a></li>
				<li><a href="/user/add/news"><i class="fa-solid fa-plus"></i> Add Post</a></li>
				
				<?php if(Admin\Categories('news')) : ?>
					<li><a href="/user/view/categories/news"><i class="fa-solid fa-th-list"></i> Categories</a></li>
				<?php endif; ?>
				
				<?php if(Admin\Privilege(2)) : ?>
					<li><a href="/user/settings/news"><i class="fa-solid fa-cog"></i> Settings</a></li>
				<?php endif; ?>
			</ul>
		</li>
	<?php endif; ?>
	
	<?php if(Admin\Privilege(2)) : ?>
		<li>
			<a href="#"><i class="fa-solid fa-file"></i> Pages</a>
			<ul>
				<li><a href="/user/view/pages"><i class="fa-solid fa-search"></i> View Pages</a></li>
				<li><a href="/user/add/pages"><i class="fa-solid fa-plus"></i> Add Page</a></li>
				
				<?php if(Admin\Categories('pages')) : ?>
					<li><a href="/user/view/categories/pages"><i class="fa-solid fa-th-list"></i> Categories</a></li>
				<?php endif; ?>
				
				<?php if(Admin\Privilege(2)) : ?>
					<li><a href="/user/settings/pages"><i class="fa-solid fa-cog"></i> Settings</a></li>
				<?php endif; ?>
			</ul>
		</li>
	<?php endif; ?>
	
	
	<?php if(Admin\Privilege(2)) : ?>
		<li>
			<a href="#"><i class="fa-solid fa-cart-shopping"></i> Products</a>
			<ul>
				<li><a href="/user/view/products"><i class="fa-solid fa-search"></i> View Products</a></li>
				<li><a href="/user/add/products"><i class="fa-solid fa-plus"></i> Add Products</a></li>
				<?php if(Admin\Categories('products')) : ?>
					<li><a href="/user/view/categories/products"><i class="fa-solid fa-th-list"></i> Categories</a></li>
				<?php endif; ?>
			</ul>
		</li>
<!--        <li>-->
<!--            <a href="#"><i class="fa-solid fa-snowflake"></i> Refrigerator</a>-->
<!--            <ul>-->
<!--                <li><a href="/user/view/fridge-spaces"><i class="fa-solid fa-search"></i> View Spaces</a></li>-->
<!--                <li><a href="/user/add/fridge-space"><i class="fa-solid fa-plus"></i> Add Space</a></li>-->
<!--            </ul>-->
<!--        </li>-->

        <li>
			<a href="#"><i class="fa-solid fa-bell-concierge"></i> Services</a>
			<ul>
				<li><a href="/user/view/services"><i class="fa-solid fa-search"></i> View Posts</a></li>
				<li><a href="/user/add/services"><i class="fa-solid fa-plus"></i> Add Post</a></li>
				
				<?php if(Admin\Categories('services')) : ?>
					<li><a href="/user/view/categories/services"><i class="fa-solid fa-th-list"></i> Categories</a></li>
				<?php endif; ?>
				
				<?php if(Admin\Privilege(2)) : ?>
					<li><a href="/user/settings/services"><i class="fa-solid fa-cog"></i> Settings</a></li>
				<?php endif; ?>
			</ul>
		</li>
		
		<li>
			<a href="#"><i class="fa-solid fa-image"></i> Sliders</a>
			<ul>
				<li><a href="/user/view/sliders"><i class="fa-solid fa-search"></i> View Slides</a></li>
				<li><a href="/user/add/sliders"><i class="fa-solid fa-plus"></i> Add Slide</a></li>
				
				<?php if(Admin\Categories('sliders')) : ?>
					<li><a href="/user/view/categories/sliders"><i class="fa-solid fa-th-list"></i> Categories</a></li>
				<?php endif; ?>
				
				<?php if(Admin\Privilege(2)) : ?>
					<li><a href="/user/settings/sliders"><i class="fa-solid fa-cog"></i> Settings</a></li>
				<?php endif; ?>
			</ul>
		</li>
		
		<li>
			<a href="#"><i class="fa-solid fa-people-group"></i> Staff</a>
			<ul>
				<li><a href="/user/view/staff"><i class="fa-solid fa-search"></i> View Staff Members</a></li>
				<li><a href="/user/add/staff"><i class="fa-solid fa-plus"></i> Add Staff Member</a></li>
				
				<?php if(Admin\Categories('staff')) : ?>
					<li><a href="/user/view/categories/staff"><i class="fa-solid fa-th-list"></i> Categories</a></li>
				<?php endif; ?>
				
				<?php if(Admin\Privilege(2)) : ?>
					<li><a href="/user/settings/staff"><i class="fa-solid fa-cog"></i> Settings</a></li>
				<?php endif; ?>
			</ul>
		</li>
		
		<li>
			<a href="#"><i class="fa-solid fa-piggy-bank"></i> Transactions</a>
			<ul>
				<li><a href="/user/manage/transactions"><i class="fa-solid fa-exchange"></i> Manage Transactions</a>
				</li>
				<li><a href="/user/stats/transactions"><i class="fa-solid fa-chart-line"></i> Statistics</a></li>
			</ul>
		</li>

        <li>
			<a href="#"><i class="fa-solid fa-gauge-high"></i> Rate Limits</a>
			<ul>
				<li><a href="/user/view/rate-limiting"><i class="fa-solid fa-search"></i> View</a></li>
			</ul>
		</li>
		
		<li><a href="/user/upload"><i class="fa-solid fa-upload"></i> Upload</a></li>
	<?php endif; ?>
</ul>