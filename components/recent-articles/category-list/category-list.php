<?php
	// LOOPS THROUGH THE DATA SOURCE AND ADDS ADDITIONAL REQUIRED FIELDS
	$data_source['category'] = array_map(function($category) use ($data_source){
		$items = array_map(function($items) use ($data_source){
			return array_merge($items, array(
				'link'  	=> !empty($items['page_url']) ? $items['page_url'] . '.html' : '',
			));
		},	(new DatabaseHandler())->fetchAll("SELECT * FROM `" . $data_source['table'] . "` WHERE `category_id` = '" . $category['id'] . "' ORDER BY `position` DESC LIMIT 5"));
		return array_merge($category, array(
			'link'  	=> !empty($category['page_url']) ? $category['page_url'] . '.html' : '',
			'items'		=> $items
		));
	},	$data_source['category']);
	
	$component_classes = array(
		!empty($data_source['background_color']) ? 'component-background-' . $data_source['background_color'] : '',
		!empty($data_source['text_color']) ? 'component-text-' . $data_source['text_color'] : ''
	);
?>

<nav class="category-list-recent-articles <?php echo implode(' ', $component_classes); ?>" aria-label="Recent Articles">
	<div class="container-fluid title-wrap">
		<div class="container">
			<h2 class="title-super-sm"><?php echo (!empty($data_source['main_title'])) ? $data_source['main_title'] : 'Recent Articles'; ?></h2>
		</div>
	</div>
	<div class="container-fluid">
		<div class="container">
			<div class="row">
				<?php foreach($data_source['category'] as $category): ?>
					<div class="col-md-6 col-lg-4 mb-0 mb-md-4">
						<section aria-label="<?php echo $category['title']; ?>">
							<h3 class="title-underlined equal-title"><?php echo $category['title']; ?></h3>
							<ul>
								<?php foreach($category['items'] as $item): ?>
									<li><a href="<?php echo $item->getLink(); ?>"><?php echo $item['title']; ?></a></li>
								<?php endforeach; ?>
								<li><a href="<?php echo $category['link']; ?>">[see more]</a></li>
							</ul>
						</section>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
</nav>