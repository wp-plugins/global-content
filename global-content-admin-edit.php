<?php 
	
	require_once( 'global-content-config.php' );
	
	$gcbb_form_data = array(
			'form_action' => "?page=" . dirname($_REQUEST['page']) . "/global-content-admin-list.php&action=edit",
			'variable' => '',
			'content' => '',
			'word_count' => 0,
			'submit' => 'Edit Variable',
	);
	
	if(isset($_GET['id']))
	{
		global $wpdb;
		
		$table_name = $wpdb->prefix . GCBB_DB_TABLE;
		
		$variable = $wpdb->get_row(
				"SELECT * FROM " . $table_name . " WHERE id = '" . $_GET['id'] . "'",
				ARRAY_A
		);
		
		$gcbb_form_data['form_action'] = "?page=" . dirname($_REQUEST['page']) . "/global-content-admin-list.php&action=edit&id=".$variable['ID'];
		$gcbb_form_data['variable'] = $variable['name'];
		$gcbb_form_data['content'] = $variable['content'];
		$gcbb_form_data['word_count'] = strlen($variable['content']);
		$gcbb_form_data['trash_action'] = "?page=" . dirname($_REQUEST['page']) . "/global-content-admin-list.php&action=delete&id=".$variable['ID'];
	}

	
?>
<div class="wrap">
	<div id="icon-gcbb" class="icon32"><br/></div>
	<h2>
		<?php echo esc_html( 'Edit Variable' ); ?>
		<a href="?page=<?php echo dirname($_REQUEST['page']) ?>/global-content-admin-new.php" class="add-new-h2"><?php echo esc_html('Add New'); ?></a>
	</h2>
	
	<?php include 'global-content-admin-form.php' ?>
</div>