<?php
	
	// LOOPS THROUGH THE DATA SOURCE AND ADDS ADDITIONAL REQUIRED FIELDS
	$data_source['data'] = array_map(function($article) use ($data_source) {
		return array_merge($article, array(
			'title'   => $article['heading'] ?? $article['title'] ?? 'No Title',
			'image'   => !empty($article['filename']) ? $data_source['image_path'] . $article['filename'] : '',
			'alt'     => htmlentities(!empty($article['filename_alt']) ? $article['filename_alt'] : $article['title'], ENT_QUOTES),
			'content' => shortdesc($article['content'], 560),
			'link'    => !empty($article['page_url']) ? $article['page_url'] . '.html' : '',
			'date'    => date('F jS, Y', strtotime($article['timestamp'])),
		));
	}, $data_source['data']);

?>

<?php if(!empty($data_source['data'])): ?>
	<section aria-label="Recent <?php echo ucfirst($data_source['set_table']); ?>">
		<div class="container-fluid article-boxes-3up">
			<div class="container">
				<div class="row ab3up-title align-items-center">
					<div class="col-md-6">
						<h2 class="title-super-md"><b>Recent</b> <?php echo ucfirst($data_source['set_table']); ?></h2>
					</div>
					<div class="col-md-6 d-none d-md-block">
						<a href="/<?php echo $data_source['set_table']; ?>.html" class="btn btn-outline-white">View All Posts</a>
					</div>
					<div class="col">
						<hr>
					</div>
				</div>
				<div class="row">
					<?php foreach($data_source['data'] as $item): ?>
						<div class="col-lg-4">
							<section aria-label="Preview of <?php echo $item['title']; ?>">
								<div class="ab3up-article-wrap">
									<h2 class="equal-title">
										<a href="<?php echo $item->getLink(); ?>"><?php echo $item['title']; ?></a>
									</h2>
									<div class="equal-content">
										<?php if(!empty($item['image'])): ?>
											<a href="<?php echo $item->getLink(); ?>">
												<img src="<?php echo $item['image']; ?>" class="img-fluid border" alt="<?php echo $item->getAlt(); ?>">
											</a>
										<?php endif; ?>
										<p class="mb-2"><b>Posted: </b><?php echo $item['date'] ?></p>
										<p><?php echo empty($item['image']) ? $item->getContent() : shortdesc($item->getContent(), 160); ?></p>
									</div>
									<a class="btn btn-outline-white btn-block" href="<?php echo $item->getLink(); ?>" title="Read more on <?php echo $item['title']; ?>">
										Read More
									</a>
								</div>
							</section>
						</div>
					<?php endforeach; ?>
					<div class="col d-md-none">
						<hr>
						<a href="/<?php echo $data_source['set_table']; ?>.html" class="btn btn-outline-white btn-block">View All Posts</a>
					</div>
				</div>
			</div>
		</div>
	</section>
<?php endif; ?>