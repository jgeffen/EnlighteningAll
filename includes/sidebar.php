<?php
	// Variable Defaults
	$sidebar_items = array(
		'blogs'  => array_map(function($item) {
			return array_merge($item, array(
				'alt'   => htmlentities($item['page_title'], ENT_QUOTES),
				'link'  => sprintf("/blogs/%s.html", $item['page_url']),
				'text'  => shortdesc($item['content'], 250),
				'image' => Render::Images(sprintf("/files/blogs/thumb/%s", $item['filename']))
			));
		}, Database::Action("SELECT * FROM `blogs` WHERE `published` IS TRUE ORDER BY `position` DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC)),
		'events' => array_map(function($item) {
			return array_merge($item, array(
				'alt'   => htmlentities($item['page_title'], ENT_QUOTES),
				'link'  => sprintf("/events/%s.html", $item['page_url']),
				'text'  => shortdesc($item['content'], 250),
				'image' => Render::Images(sprintf("/files/events/poster/thumb/%s", $item['filename']), '/images/events/default-poster.jpg'),
				'date'  => call_user_func(function($start, $end) {
					return date('Y-m-d', $start) == date('Y-m-d', $end) ? date('M d Y', $start) : date('M d', $start) . ' - ' . date('M d Y', $end);
				}, strtotime($item['date_start']), strtotime($item['date_end']))
			));
		}, Database::Action("SELECT * FROM `events` WHERE CURDATE() BETWEEN `date_start` AND `date_end` ORDER BY `date_start`, `date_end` LIMIT 5")->fetchAll(PDO::FETCH_ASSOC)),
		'news'   => array_map(function($item) {
			return array_merge($item, array(
				'alt'   => htmlentities($item['page_title'], ENT_QUOTES),
				'link'  => sprintf("/news/%s.html", $item['page_url']),
				'text'  => shortdesc($item['content'], 250),
				'image' => Render::Images(sprintf("/files/news/thumb/%s", $item['filename']))
			));
		}, Database::Action("SELECT * FROM `news` WHERE `is_published` = 1 ORDER BY `position` DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC))
	);
?>

<div class="sidebar">
	<?php if(!empty($sidebar_items['blogs'])): ?>
		<div class="well well-sm">
			<h3 class="title postfit"><i class="fa fa-comment-o"></i> Recent Blog Posts</h3>
			
			<ul>
				<?php foreach($sidebar_items['blogs'] as $sidebar_blog): ?>
					<li>
						<a href="<?php echo $sidebar_blog['link']; ?>" title="<?php echo $sidebar_blog['alt']; ?>">
							<?php echo $sidebar_blog['heading']; ?>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
			
			<p><a class="btn btn-info btn-block" href="/blogs.html">View All</a></p>
		</div>
	<?php endif; ?>
	
	<?php if(!empty($sidebar_items['events'])): ?>
		<div class="well well-sm">
			<h3 class="title postfit"><i class="fa fa-glass"></i> Events</h3>
			
			<ul>
				<?php foreach($sidebar_items['events'] as $sidebar_event): ?>
					<li>
						<a href="<?php echo $sidebar_event['link']; ?>" title="<?php echo $sidebar_event['alt']; ?>">
							<?php echo $sidebar_event['heading']; ?>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
			
			<p><a class="btn btn-info btn-block" href="/events">View All</a></p>
		</div>
	<?php endif; ?>
	
	<?php if(!empty($sidebar_items['news'])): ?>
		<div class="well well-sm">
			<h3 class="title postfit"><i class="fa fa-newspaper-o"></i> Recent News</h3>
			
			<ul>
				<?php foreach($sidebar_items['news'] as $sidebar_news): ?>
					<li>
						<a href="<?php echo $sidebar_news['link']; ?>" title="<?php echo $sidebar_news['alt']; ?>">
							<?php echo $sidebar_news['heading']; ?>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
			
			<p><a class="btn btn-info btn-block" href="/news.html">View All</a></p>
		</div>
	<?php endif; ?>
</div>