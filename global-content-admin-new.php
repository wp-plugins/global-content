<?php 
	require_once( 'global-content-config.php' );

	$gcbb_form_data = array(
			'form_action' => "?page=" . dirname($_REQUEST['page']) . "/global-content-admin-list.php&action=new",
			'variable' => '',
			'content' => '',
			'word_count' => 0,
			'submit' => 'Create Vriable',
			);
?>
<div class="wrap">
	<div id="icon-gcbb" class="icon32"><br/></div>
	<h2>
		<?php echo esc_html( 'Add New Variable' ); ?>
	</h2>
	
	<?php include 'global-content-admin-form.php' ?>
</div>