<?php 
	require_once( 'global-content-config.php' );
	require_once( 'global-content-admin-list-table.class.php' );
	
	/* ACTIONS */
	$messages = array();
	
	/* LIST OF VARIABLE */
	$admin_list_table = new GCBB_Admin_List_Table();
	$admin_list_table->prepare_items();
?>
<div class="wrap">
	<div id="icon-gcbb" class="icon32"><br/></div>
	<h2>
		<?php echo esc_html( $title ); ?>
		<a href="?page=<?php echo dirname($_REQUEST['page']) ?>/global-content-admin-new.php" class="add-new-h2"><?php echo esc_html('Add New'); ?></a>
	</h2>
	
	<?php if ( count($messages) > 0 ) : ?>
		<div id="message" class="updated">
			<ul>
				<?php foreach($messages as $msg) : ?>
					<li><p><?php echo $msg ?></p></li>
				<?php endforeach; ?>
			</ul>
		</div>
	<?php endif; ?>
	
	<form id="gccbb-form" action="" method="get">
		<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
		<?php $admin_list_table->display(); ?>
	</form>
</div>