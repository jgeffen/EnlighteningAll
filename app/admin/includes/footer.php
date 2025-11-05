					</main>
					</div>
					</div>
					</div>
					</div>

					<footer id="footer" class="container-fluid">
						<div class="row">
							<div class="col-12 text-center">
								<small>
									&copy;<?php echo date('Y'); ?> <a href="/"><?php echo SITE_COMPANY; ?></a>.
									Site created by <a href="<?php echo DEV_LINK; ?>" target="_blank"><?php echo DEV_NAME; ?></a>.
								</small>
							</div>
						</div>
					</footer>
					</div>

					<!--Tool Tip Modal-->
					<div class="modal fade" id="tool-tip-modal" tabindex="-1" role="dialog" aria-label="Tool Tips" aria-hidden="true">
						<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
							<div class="modal-content">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
									<i class="fal fa-info-circle" aria-hidden="true"></i>
									<h3 class="modal-title"></h3>
								</div>
								<div class="modal-body">
									<p></p>
								</div>
							</div>
						</div>
					</div>

					<?php if (Admin\LoggedIn()) : ?>
						<?php include('nav-sm.php'); ?>
					<?php endif; ?>

					<script src="/js/scripts-admin.min.js"></script>