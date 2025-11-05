</main>
<script>
    var p = document.createElement('script');
    p.src = "https://www.paypal.com/sdk/js?client-id=sb&currency=USD";
    p.onload = () => console.log('✅ PayPal SDK loaded successfully.');
    p.onerror = (e) => console.error('❌ PayPal SDK failed:', e);
    document.head.appendChild(p);
</script>

<?php Render::Structure('footer/footer-default'); ?>

<?php /*--||--------------------------------------------------------||------
----------||				<- Sample Modal Template ->				||------
----------||--------------------------------------------------------||--*/ ?>
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<i class="fal fa-exclamation-triangle" aria-hidden="true"></i>
				<h3 class="modal-title">Sample Modal</h3>
			</div>
			<div class="modal-body">
				<p>Your text will go here. We need information from you to replace this content. Please send us some information so we can update the text for you. Your text will go here. We need information from you to replace this content. Please send us some information so we can update the text for you. Your text will go here. We need information from you to replace this content. Please send us some information so we can update the text for you. Your text will go here. We need information from you to replace this content.</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-outline" data-dismiss="modal">Close</button>
				<button type="button" class="btn btn-primary">Save</button>
			</div>
		</div>
	</div>
</div>

<?php /*--||--------------------------------------------------------||------
----------||				<- QR Code Modal Template ->				||------
----------||--------------------------------------------------------||--*/ ?>
<div class="modal fade" id="showQRcode" tabindex="-1" role="dialog" aria-labelledby="showQRcode" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-mg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h3 class="modal-title"></h3>
			</div>
			<div class="modal-body">
				<img class="img-fluid mb-0" src="" id="Qrcodeimage">
			</div>
			
		</div>
	</div>
</div>

<?php /*--||--------------------------------------------------------||------
----------||		<- Post Report Modal Template ->				||------
----------||--------------------------------------------------------||--*/ ?>
<div class="modal fade" id="post-report" tabindex="-1" role="dialog" aria-labelledby="post-report" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-mg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<i class="far fa-exclamation-triangle"></i>
				<h3 class="modal-title">Report Post</h3>
				
			</div>
			<div class="modal-body">
				            <div class="form-wrap">
               <div id="post-report-form" role="form" aria-label="Post Report Form">
                  <form id="post-reports">
                      <div class="form-group row">
						<label for="exampleFormControlSelect1">Type</label>
						<select class="form-control" id="post-report-type" name="post_report_type" required>
						  <option value="Abusive Language">Abusive Language</option>
						  <option value="Criminal">Criminal</option>
						  <option value="Fake">Fake</option>
						  <option value="Fraudulent">Fraudulent</option>
						  <option value="Harassment">Harassment</option>
						  <option value="Hateful">Hateful</option>
						  <option value="Inappropriate">Inappropriate</option>
						  <option value="Racist">Racist</option>
						  <option value="Spam">Spam</option>
						  
						</select>
					  </div>
                        <div class="form-group row">
							<label for="post_report_commen">Message</label>
							<textarea class="form-control" id="post_report_comment" name="post_report_comment" rows="3" required></textarea>
						    <input id="postID" class="form-control" type="hidden" value="" name="report_postID">  
					  </div>
					  
					  
                     <button type="submit" class="btn btn-primary btn-block-xs submit-btn right mb-3" data-post-action="report" >Send Report</button>
                     <button type="button" class="btn btn-outline btn-block-xs right mr-xs-0 mr-sm-2 mb-3" data-dismiss="modal">Close</button>
                  </form>
               </div>
            </div>
			</div>
			
		</div>
	</div>
</div>


<?php /*--||--------------------------------------------------------||------
----------||		<- Profile Report Modal Template ->				||------
----------||--------------------------------------------------------||--*/ ?>
<div class="modal fade" id="profile-report" tabindex="-1" role="dialog" aria-labelledby="profile-report" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-mg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<i class="far fa-exclamation-triangle"></i>
				<h3 class="modal-title">Report Profile</h3>
				
			</div>
			<div class="modal-body">
				            <div class="form-wrap">
               <div id="profile-report-form" role="form" aria-label="Profile Report Form">
                  <form id="profile-reports">
                      <div class="form-group row">
						<label for="exampleFormControlSelect1">Type</label>
						<select class="form-control" id="profile-report-type" name="profile_report_type" required>
						  <option value="Abusive Language">Abusive Language</option>
						  <option value="Criminal">Criminal</option>
						  <option value="Fake">Fake</option>
						  <option value="Fraudulent">Fraudulent</option>
						  <option value="Harassment">Harassment</option>
						  <option value="Hateful">Hateful</option>
						  <option value="Inappropriate">Inappropriate</option>
						  <option value="Racist">Racist</option>
						  <option value="Spam">Spam</option>
						  
						</select>
					  </div>
                        <div class="form-group row">
							<label for="profile_report_commen">Message</label>
							<textarea class="form-control" id="profile_report_comment" name="profile_report_comment" rows="3" required></textarea>
						    <input id="profileID" class="form-control" type="hidden" value="" name="report_profileID">  
					  </div>
					  
					  
                     <button type="submit" class="btn btn-primary btn-block-xs submit-btn right mb-3" data-profile-action="report" >Send Report</button>
                     <button type="button" class="btn btn-outline btn-block-xs right mr-xs-0 mr-sm-2 mb-3" data-dismiss="modal">Close</button>
                  </form>
               </div>
            </div>
			</div>
			
		</div>
	</div>
</div>

<?php /*--||--------------------------------------------------------||------
----------||		<- Comment Report Modal Template ->				||------
----------||--------------------------------------------------------||--*/ ?>
<div class="modal fade" id="comment-report" tabindex="-1" role="dialog" aria-labelledby="comment-report" aria-hidden="true" style="z-index:9908">
	<div class="modal-dialog modal-dialog-centered modal-mg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<i class="far fa-exclamation-triangle"></i>
				<h3 class="modal-title">Report Comment</h3>
				
			</div>
			<div class="modal-body">
				            <div class="form-wrap">
               <div id="comment-report-form" role="form" aria-label="Comment Report Form">
                  <form id="comment-reports">
                      <div class="form-group row">
						<label for="exampleFormControlSelect1">Type</label>
						<select class="form-control" id="comment-report-type" name="comment_report_type" required>
						  <option value="Abusive Language">Abusive Language</option>
						  <option value="Criminal">Criminal</option>
						  <option value="Fake">Fake</option>
						  <option value="Fraudulent">Fraudulent</option>
						  <option value="Harassment">Harassment</option>
						  <option value="Hateful">Hateful</option>
						  <option value="Inappropriate">Inappropriate</option>
						  <option value="Racist">Racist</option>
						  <option value="Spam">Spam</option>
						  
						</select>
					  </div>
                        <div class="form-group row">
							<label for="comment_report_commen">Message</label>
							<textarea class="form-control" id="comment_report_comment" name="comment_report_comment" rows="3" required></textarea>
						    <input id="commentID" class="form-control" type="hidden" value="" name="report_commentID">  
					  </div>
					  
					  
                     <button type="submit" class="btn btn-primary btn-block-xs submit-btn right mb-3" data-comment-action="report" >Send Report</button>
                     <button type="button" class="btn btn-outline btn-block-xs right mr-xs-0 mr-sm-2 mb-3" data-dismiss="modal">Close</button>
                  </form>
               </div>
            </div>
			</div>
			
		</div>
	</div>
</div>


<?php /*--||--------------------------------------------------------||------
----------||		<- Sand friend Request Modal Template ->		||------
----------||--------------------------------------------------------||--*/ ?>
<div class="modal fade" id="send_friend_request" tabindex="-1" role="dialog" aria-labelledby="send_friend_request" aria-hidden="true">
   <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <i class="fal fa-exclamation-triangle" aria-hidden="true"></i>
            <h3 class="modal-title">Confirmation For Send Friend Request </h3>
         </div>
         <div class="modal-body">
            <div class="form-wrap">
               <div id="confirmation-form" role="form" aria-label="Confirmation Form">
                  <form id="confirmation-friend">
                     <div class="form-group row">
                        <label class="col-form-label col-lg-12 text-lg-left pt-0 pt-lg-1" for="testimonial-input-name">Where did you meet this person?* </label>
                        <div class="col-lg-12">
                           <input id="confirmation_answer-input-name" class="form-control" type="text" placeholder="* Required" name="confirmation_answer" maxlength="255" required data-type="general">
                        </div>
                     </div>
                     <div class="form-group row">
                        <label class="col-form-label col-lg-12 text-lg-left pt-0 pt-lg-1" for="testimonial-input-phone">Do you know this person from real life?*</label>
                        <div class="col-lg-12">
                           <div class="row m-lg-1">
							<div class="col-lg-1">
                              <input class="form-check-input" name="confirmation_answer"  type="radio" value="Yes" > Yes
                           </div>
                           <div class="col-lg-1">
                              <input class="form-check-input"  name="confirmation_answer" type="radio" value="No" > No
                           </div>
							</div>
                        </div>
                     </div>
					   <input id="confirmation_questions-input-person" class="form-control" type="hidden" value="Where did you meet this person?" name="confirmation_questions">
					  <input id="confirmation_questions-input-life" class="form-control" type="hidden" value="Do you know this person from real life?" name="confirmation_questions">
                     <button type="submit" class="btn btn-primary btn-block-xs submit-btn right mb-3" data-profile-action="friend-request-send" >Send Friend Request</button>
                     <button type="button" class="btn btn-outline btn-block-xs right mr-xs-0 mr-sm-2 mb-3" data-dismiss="modal">Close</button>
                  </form>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>


<?php /*--||--------------------------------------------------------||------
----------||	<- Set Private Photo Limit Modal Template ->		||------
----------||--------------------------------------------------------||--*/ ?>
<div class="modal fade" id="set_private_photo_limit" tabindex="-1" role="dialog" aria-labelledby="set_private_photo_limit" aria-hidden="true">
   <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <i class="fal fa-exclamation-triangle" aria-hidden="true"></i>
            <h3 class="modal-title">Set Private Photo Limit </h3>
         </div>
         <div class="modal-body">
            <div class="form-wrap">
               <div id="private-photo-limit-form" role="form" aria-label="Set Private Photo Limit Form">
                  <form id="private-photo-limit-friend">
                     <div class="form-group row">
                        <label class="col-form-label col-lg-12 text-lg-left pt-0 pt-lg-1" for="private-photo-input-limit">Private Photo Limit* </label>
                        <div class="col-lg-12">
                           <input id="private-photo-input-limit" class="form-control" type="number" placeholder="* Required" name="private_photos_limit" min="0" maxlength="255" required data-type="general">
                        </div>
                     </div>
                     
					
                     <button type="submit" class="btn btn-primary btn-block-xs submit-btn right mb-3" data-private-photo-action="private-photo-limit" >Update</button>
                     <button type="button" class="btn btn-outline btn-block-xs right mr-xs-0 mr-sm-2 mb-3" data-dismiss="modal">Close</button>
                  </form>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>


<?php /*--||----------------------------------------------------------||------
----------||<- View confirmation Sand friend Request Modal Template ->||------
----------||----------------------------------------------------------||--*/ ?>
<div class="modal fade" id="view_confirmation_friend_request" tabindex="-1" role="dialog" aria-labelledby="view_confirmation_friend_request" aria-hidden="true">
   <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <i class="fal fa-exclamation-triangle" aria-hidden="true"></i>
            <h3 class="modal-title">verify For Friend Request </h3>
         </div>
         <div class="modal-body">
            <div class="form-wrap">
               <div id="view_confirmation-list" role="form" aria-label="Confirmation Form">
				  
               </div>
				<div class="form-check pl-0">
					 <div class="form-group row">
				  <label for="Verify" class="col-form-label col-lg-3 text-lg-left pt-0 pt-lg-1 font-weight-bold">Validate Member</label>
				  <input id="Verify" class=" " name="verify" type="checkbox" data-toggle="toggle" >
				 </div>
				</div>
            </div>
         </div>
		  <div class="modal-footer p-4">
			  	<button class="btn btn-success" type="button" data-profile-action="friend-request-accept" id="verify-profile-request" data-profile-id="">
														<i class="fas fa-user-check"></i> Accept
													</button>
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
			</div>
      </div>
   </div>
</div>

<?php /*--||--------------------------------------------------------||------
----------||					<- Youtube Modal ->					||------
----------||--------------------------------------------------------||--*/ ?>
<div class="modal fade" id="youtube-modal" tabindex="-1" role="dialog" aria-label="Video Modal" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<i class="fab fa-youtube" aria-hidden="true"></i>
				<h2 class="modal-title"></h2>
			</div>
			<div class="modal-body"></div>
		</div>
	</div>
</div>

<?php /*--||--------------------------------------------------------||------
----------||				<- Testimonials Modal ->				||------
----------||--------------------------------------------------------||--*/ ?>
<div id="testimonial-modal" class="modal fade" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<i class="fal fa-comment-edit" style="font-size: 36px;"></i>
				<h3 class="modal-title">Submit Your Testimonial</h3>
			</div>
			<div class="modal-body">
				<div class="form-wrap">
					<div id="testimonial-form" role="form" aria-label="Testimonial Form">
						<form>
							<div class="form-group row">
								<label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="testimonial-input-name">* Name:</label>
								<div class="col-lg-9">
									<input id="testimonial-input-name" class="form-control" type="text" placeholder="* Required" name="full_name" maxlength="255" required data-type="general">
								</div>
							</div>

							<div class="form-group row">
								<label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="testimonial-input-phone">* Phone:</label>
								<div class="col-lg-9">
									<input id="testimonial-input-phone" class="form-control" type="text" placeholder="* Required" name="phone" maxlength="50" data-format="phone" required data-type="phone">
								</div>
							</div>

							<div class="form-group row">
								<label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="testimonial-input-email">* E-mail:</label>
								<div class="col-lg-9">
									<input id="testimonial-input-email" class="form-control" type="email" placeholder="* Required" name="email" maxlength="100" required data-type="email">
								</div>
							</div>

							<div class="form-group">
								<label for="testimonial-textarea-testimonial">* Enter Your Testimonial:</label>
								<textarea id="testimonial-textarea-testimonial" class="form-control" placeholder="* Required" name="testimonial" rows="8" required data-type="general"></textarea>
							</div>

							<div class="form-group">
								<div class="cap-wrap text-center">
									<fieldset>
										<label for="captcha" aria-hidden="true">Enter the Characters Shown Below</label>
										<input type="text" name="captcha" class="form-control" id="captcha" required data-type="general">
									</fieldset>
									<noscript>
										<p class="help-block"><span class="text-danger">(Javascript must be enabled to submit the form.)</span></p>
									</noscript>
								</div>
							</div>

							<button type="submit" class="btn btn-primary btn-block-xs submit-btn right mb-3">Submit</button>

							<button type="button" class="btn btn-outline btn-block-xs right mr-xs-0 mr-sm-2 mb-3" data-dismiss="modal">Close</button>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

</div> <?php /* Close Main Wrapper */ ?>

<div id="terms-and-conditions-for-users" class="modal fade" tabindex="-1">
	<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">TERMS and CONDITIONS for USERS</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>

			<div class="modal-body"></div>

			<div class="modal-footer p-4">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>


<div id="terms-privacy" class="modal fade" tabindex="-1">
	<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Terms & Privacy</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>

			<div class="modal-body"></div>

			<div class="modal-footer p-4" style="justify-content: space-between;">
				<div class="form-group row" style="align-items:center">
					<label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="terms_privacy_signature_modal">
						Signature
					</label>

					<div class="col-lg-9">
						<input id="terms_privacy_signature_modal" class="form-control" type="text" name="terms_privacy_signature_modal" placeholder="* Required">
					</div>
				</div>
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>

<div id="affiliate-terms-conditions" class="modal fade" tabindex="-1">
	<div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Affiliate Terms & Conditions</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>

			<div class="modal-body"></div>

			<div class="modal-footer p-4" style="justify-content: space-between;">
				<div class="form-group row" style="align-items:center">
					<label class="col-form-label col-lg-3 text-lg-right pt-0 pt-lg-1" for="affiliate_terms_conditions_signature_modal">
						Signature
					</label>

					<div class="col-lg-9">
						<input id="affiliate_terms_conditions_signature_modal" class="form-control" type="text" name="affiliate_terms_conditions_signature_modal" placeholder="* Required">
					</div>
				</div>
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>

<?php Render::Structure('nav-mobile/nav-mobile-default'); ?>
<!-- PayPal SDK (must load globally and only once) -->
<script
        src="https://www.paypal.com/sdk/js?client-id=AU_aqkdxvcHEqW596MMhDnmna1TC8wgZTeIjzyMwLdVDwog98PLjVIjEPsNtKkC0OUjbVEp-VVk23HPC&currency=USD"
        data-namespace="paypal_sdk_main"
        crossorigin="anonymous"
></script>

<script src="/js/scripts-main.min.js"></script>

<script type="text/javascript">
	var _userway_config = {
		position: '5',
		size: 'small',
		color: '#666'
	};
</script>
<script data-account="<?php echo UW_CODE; ?>" src="https://cdn.userway.org/widget.js"></script>