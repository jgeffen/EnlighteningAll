<?php 
	
	// LOOPS THROUGH THE DATA SOURCE AND ADDS ADDITIONAL REQUIRED FIELDS
	$data_source['data'] = array_map(function($article) use ($data_source){
		return array_merge($article, array(
			'image' 	=> !empty($article['filename']) ? $data_source['image_path'] . $article['filename'] : '',
			'alt'   	=> htmlentities(!empty($article['filename_alt']) ? $article['filename_alt'] : $article['title'], ENT_QUOTES),
			'content'  	=> shortdesc($article['content'], 560),
			'link'  	=> !empty($article['page_url']) ? $article['page_url'] . '.html' : '',
		));
	},	$data_source['data']);

?>

<div class="container-fluid tall-photo-articles">
	<div class="row no-gutters align-items-stretch justify-content-center">
		<?php foreach($data_source['data'] as $article): ?>
			<div class="col-md-6 col-lg-6 col-xl-3">
				<article aria-label="Preview of <?php echo $article['title']; ?>">
					<a href="<?php echo $article['link']; ?>" class="article-wrap <?php if(empty($article['image'])) echo 'no-article-img' ;?>">
						<?php if(!empty($article['image'])): ?>
							<img src="/images/layout/default-portrait.jpg" data-src="<?php echo $article['image']; ?>" class="lazy" alt="<?php echo $article['alt']; ?>">
						<?php endif; ?>
						<div class="content-wrap equal-content">
							<h2 class="article-title"><?php echo $article['title']; ?></h2>
							<p><?php echo empty($article['image']) ? $article['content'] : shortdesc($article['content'], 160); ?></p>
						</div>
					</a>
				</article>
			</div>
		<?php endforeach; ?>
	</div>
</div>