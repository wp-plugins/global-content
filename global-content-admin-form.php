<form id="gccbb-form" action="<?php echo $gcbb_form_data['form_action'] ?>" method="post">
	<div id="poststuff">
		<div id="post-body" class="metabox-holder columns-2">
		
			<div id="post-body-content">
			
				<div id="titlediv">
					<div id="titlewrap">
						<label class="screen-reader-text" id="title-prompt-text" for="title">Enter variable name here</label>
						<input type="text" name="gcbb_variable_name" size="30" value="<?php echo $gcbb_form_data['variable'] ?>" id="title" autocomplete="off" />
					</div>
				</div>
				
				<?php wp_editor($gcbb_form_data['content'], 'gcbb_variable_content', array('textarea_rows' => 14)); ?>
				<table id="post-status-info" cellspacing="0">
					<tr><td id="wp-word-count">Word count: <?php echo $gcbb_form_data['word_count'] ?></td></tr>
				</table>
			</div>
			
			<div id="postbox-container-1" class="postbox-container">
				<div id="side-sortables" class="meta-box-sortables ui-sortable">
					<div id="submitdiv" class="postbox " >
						<div class="handlediv" title="Click to toggle"><br /></div><h3 class='hndle'><span>Save</span></h3>
						<div id="major-publishing-actions">
							<?php if(isset($gcbb_form_data['trash_action'])) : ?>
								<div id="delete-action">
									<a class="submitdelete deletion" href="<?php echo $gcbb_form_data['trash_action'] ?>">Move to Trash</a>
								</div>
							<?php endif; ?>
							
							<div id="publishing-action">
								<span class="spinner"></span>
								<input type="submit" name="save" id="publish" class="button button-primary button-large" value="<?php echo $gcbb_form_data['submit'] ?>" accesskey="p"  />
							</div>
							
							<div class="clear">
						</div>
					</div>
				</div>
			</div>
			
		</div>
		<br class="clear" />
	</div>
</form>	