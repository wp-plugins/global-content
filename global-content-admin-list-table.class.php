<?php 

require_once( 'global-content-config.php' );

if(!class_exists('WP_List_Table')){
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

if(!class_exists('GCBB_Functions')){
	require_once( 'global-content-functions.class.php' );
}

require_once( 'global-content-config.php' );

class GCBB_Admin_List_Table extends WP_List_Table 
{
	/**
	 * Constructor
	 */
	function __construct()
	{
		global $status, $page;
	
		//Set parent defaults
		parent::__construct( array(
				'singular'  => 'variable',
				'plural'    => 'variables',
				'ajax'      => false
		) );
	}
	
	/**
	 * Build the table's column
	 * @see WP_List_Table::get_columns()
	 */
	function get_columns()
	{
		$columns = array(
				'cb'        => '<input type="checkbox" />',
				'name'     => 'Variable',
				'content'    => 'Content',
				'template' => 'Usage in Templates',
				'post' => 'Usage in Posts/Pages',
		);
		return $columns;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see WP_List_Table::get_sortable_columns()
	 */
	function get_sortable_columns() 
	{
		$sortable_columns = array(
				'name'     => array('name',false),
		);
		return $sortable_columns;
	}
	
	/**
	 * Default method the build columns
	 * @param array $item
	 * @param string $column_name
	 * @return string
	 */
	function column_default($item, $column_name)
	{
		if(isset($item[$column_name]))
			return $item[$column_name];
		else
			return '';
	}
	
	/**
	 * 
	 * @param array $item
	 * @return string
	 */
	function column_cb($item)
	{
		return sprintf(
				'<input type="checkbox" name="%1$s[]" value="%2$s" />',
				$this->_args['plural'],
				$item['ID']
		);
	}
	
	/**
	 * 
	 * @param array $item
	 * @return string
	 */
	function column_name($item)
	{
		$edit = sprintf('?page=%s&id=%d',dirname($_REQUEST['page']) . '/global-content-admin-edit.php',$item['ID']);
		$actions = array(
				'edit'      => sprintf('<a href="%s">Edit</a>',$edit),
				'delete'    => sprintf('<a href="?page=%s&action=%s&id=%d">Delete</a>',$_REQUEST['page'],'delete',$item['ID']),
		);
	
		return sprintf('<strong><a class="row_title" href="%s" title="Edit %s">%s</a></strong>%s',
				$edit,
				ucwords(GCBB_Functions::unslugify($item['name'])),
				ucwords(GCBB_Functions::unslugify($item['name'])),
				$this->row_actions($actions)
		);
	}
	
	/**
	 * 
	 * @param array $item
	 * @return string
	 */
	function column_content($item)
	{
		if( strlen($item['content']) < 80 )
			return htmlentities($item['content']);
		else
			return htmlentities(substr($item['content'], 0, 70) . "...");
	}
	
	/**
	 * 
	 * @param array $item
	 */
	function column_template($item)
	{
		return htmlentities(sprintf('<?php echo gcbb_get("%s") ?>', $item['name']));
	}
	
	/**
	 *
	 * @param array $item
	 */
	function column_post($item)
	{
		return htmlentities(sprintf(GCBB_TAG_PATTERN, $item['name']));
	}
	
	/**
	 * (non-PHPdoc)
	 * @see WP_List_Table::get_bulk_actions()
	 */
	function get_bulk_actions() 
	{
		$actions = array(
				'delete'    => 'Delete'
		);
		return $actions;
	}
	
	/**
	 * 
	 */
	function process_bulk_action() {
		global $wpdb, $messages;
		
		$current_user = wp_get_current_user();
		$table_name = $wpdb->prefix . GCBB_DB_TABLE;
		
		switch($this->current_action())
		{
			case 'new':
				if(isset($_POST['save']) && isset($_POST['gcbb_variable_name']) && $_POST['gcbb_variable_name'] != '' && isset($_POST['gcbb_variable_content']))
				{
					$name = GCBB_Functions::slugify($_POST['gcbb_variable_name']);
					$result = $wpdb->insert( $table_name, array( 'time' => current_time('mysql'), 'name' => $name, 'content' => stripslashes($_POST['gcbb_variable_content']), 'author' => $current_user->ID ) );
					if($result !== false)
						$messages[] = sprintf('The variable %s has been successfully created', $name);
					else
						$messages[] = sprintf('<strong>The variable %s couldn\'t be created, please try again</strong>', $name);
				}
				break;
					
			case 'delete':
				if(isset($_GET['id']) || isset($_GET['variables']))
				{
					$variable_ids = array();
					
					if(isset($_GET['id'])) $variable_ids[] = $_GET['id'];
					if(isset($_GET['variables'])) $variable_ids = array_merge($variable_ids, $_GET['variables']);
					
					while($id = array_shift($variable_ids))
					{
						$result = $wpdb->get_row(
								"SELECT name FROM " . $table_name . " WHERE id = '" . $id . "'",
								ARRAY_A
						);
			
						if(isset($result['name']))
						{
							$name = $result['name'];
								
							$result = $wpdb->query(
									$wpdb->prepare(
											"
											DELETE FROM $table_name
											WHERE ID = %d
											",
											$id
									)
							);
								
							if($result !== false)
								$messages[] = sprintf('The variable %s has been successfully deleted', $name);
										else
								$messages[] = sprintf('<strong>The variable %s couldn\'t be deleted, please try again</strong>', $name);
						}
						else
						{
							$messages[] = '<strong>The variable you\'re trying to delete doesn\'t exist...</strong>';
						}
					}
				}
				break;
				
			case 'edit':
				if(isset($_GET['id']) && isset($_POST['save']) && isset($_POST['gcbb_variable_name']) && $_POST['gcbb_variable_name'] != '' && isset($_POST['gcbb_variable_content']))
				{
					$result = $wpdb->get_row(
							"SELECT * FROM " . $table_name . " WHERE id = '" . $_GET['id'] . "'",
							ARRAY_A
					);
				
					if($result != null)
					{
						$name = GCBB_Functions::slugify($_POST['gcbb_variable_name']);
	
						$result = $wpdb->update(
								$table_name,
								array(
										'name' => $name,
										'content' => stripslashes($_POST['gcbb_variable_content']),
										),
								array(
										'ID' => $_GET['id'],
										)
								);
									
						if($result !== false)
							$messages[] = sprintf('The variable %s has been successfully edited', $name);
						else
							$messages[] = sprintf('<strong>The variable %s couldn\'t be edited, please try again</strong>', $name);
					}
					else
					{
						$messages[] = '<strong>The variable you\'re trying to edit doesn\'t exist...</strong>';
					}
				}
				break;
		}
	}
	
	/**
	 * (non-PHPdoc)
	 * @see WP_List_Table::prepare_items()
	 */
	function prepare_items() {
		global $wpdb;
	
		$per_page = 5;
		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();
		$this->_column_headers = array($columns, $hidden, $sortable);
		$this->process_bulk_action();
	
		$table_name = $wpdb->prefix . GCBB_DB_TABLE;
		$query = "SELECT * FROM $table_name";
		$data = $wpdb->get_results($query, ARRAY_A);
	
		$current_page = $this->get_pagenum();
		$total_items = count($data);
		$data = array_slice($data,(($current_page-1)*$per_page),$per_page);
	
		$this->items = $data;
	
		$this->set_pagination_args( array(
				'total_items' => $total_items,
				'per_page'    => $per_page,
				'total_pages' => ceil($total_items/$per_page)
		) );
	}
}