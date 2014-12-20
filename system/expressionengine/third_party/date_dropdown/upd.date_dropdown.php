<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Default
 *
 * @package		Default module
 * @category	Modules
 * @author		Rein de Vries <info@reinos.nl>
 * @link		http://reinos.nl
 * @copyright 	Copyright (c) 2014 Reinos.nl Internet Media
 */

/**
 * Include the config file
 */
require_once PATH_THIRD.'date_dropdown/config.php';
 
class Date_dropdown_upd {
	
	public $version = DATE_DD_VERSION;
	
	/**
	 * Constructor
	 */
	public function __construct()
	{		
		//load the classes
		ee()->load->dbforge();
		
		//require the settings
		require PATH_THIRD.DATE_DD_MAP.'/settings.php';
	}
	
	// ----------------------------------------------------------------
	
	/**
	 * Installation Method
	 *
	 * @return 	boolean 	TRUE
	 */
	public function install()
	{		
		//set the module data
		$mod_data = array(
			'module_name'			=> DATE_DD_CLASS,
			'module_version'		=> DATE_DD_VERSION,
			'has_cp_backend'		=> 'n',
			'has_publish_fields'	=> 'n'
		);
	
		//insert the module
		ee()->db->insert('modules', $mod_data);
		
		//create some actions for the ajax in the control panel
		//$this->_register_action('ajax_cp');

		//install the extension
		//$this->_register_hook('sessions_start', 'sessions_start');
		
		//create the Login backup tables
		$this->_create_tables();

		//Add tabs
		//ee()->load->library('layout');
		//ee()->layout->add_layout_tabs($this->_tabs(), URL_ALIAS);

		//load the helper
		ee()->load->library(DATE_DD_MAP.'_lib');
		
		//insert the settings data
		ee()->date_dropdown_settings->first_import_settings();	
		
		return TRUE;
	}

	// ----------------------------------------------------------------
	
	/**
	 * Uninstall
	 *
	 * @return 	boolean 	TRUE
	 */	
	public function uninstall()
	{
		//delete the module
		ee()->db->where('module_name', DATE_DD_CLASS);
		ee()->db->delete('modules');

		//remove databases
		ee()->dbforge->drop_table(DATE_DD_CLASS.'_settings');
		ee()->dbforge->drop_table(DATE_DD_CLASS.'_search');
		
		//remove actions
		ee()->db->where('class', DATE_DD_CLASS);
		ee()->db->delete('actions');
		
		//remove the extension
		ee()->db->where('class', DATE_DD_CLASS.'_ext');
		ee()->db->delete('extensions');

		//delete tabs
		ee()->load->library('layout');
		ee()->layout->delete_layout_tabs($this->_tabs(), DATE_DD_MAP);
		
		return TRUE;
	}
	
	// ----------------------------------------------------------------
	
	/**
	 * Module Updater
	 *
	 * @return 	boolean 	TRUE
	 */	
	public function update($current = '')
	{
		//nothing to update
		if ($current == '' OR $current == $this->version)
			return FALSE;
		
		//loop through the updates and install them.
		if(!empty($this->updates))
		{
			foreach ($this->updates as $version)
			{
				//$current = str_replace('.', '', $current);
				//$version = str_replace('.', '', $version);
				
				if ($current < $version)
				{
					$this->_init_update($version);
				}
			}
		}
			
		return true;
	}
		
	// ----------------------------------------------------------------
	
	/**
	 * Add the tables for the module
	 *
	 * @return 	boolean 	TRUE
	 */	
	private function _create_tables()
	{			
		// add config tables
		$fields = array(
				'settings_id'	=> array(
									'type'			=> 'int',
									'constraint'		=> 7,
									'unsigned'		=> TRUE,
									'null'			=> FALSE,
									'auto_increment'	=> TRUE
								),
				'site_id'  => array(
									'type'			=> 'int',
									'constraint'		=> 7,
									'unsigned'		=> TRUE,
									'null'			=> FALSE,
									'default'			=> 0
								),
				'var'  => array(
									'type' 			=> 'varchar',
									'constraint'		=> '200',
									'null'			=> FALSE,
									'default'			=> ''
								),
				'value'  => array(
									'type' 			=> 'text',
									'null'			=> FALSE
								),
		);
		
		//create the backup database
		ee()->dbforge->add_field($fields);
		ee()->dbforge->add_key('settings_id', TRUE);
		ee()->dbforge->create_table(DATE_DD_MAP.'_settings', TRUE);

		// add index tables
		$fields = array(
				'search_id'	=> array(
									'type'			=> 'int',
									'constraint'		=> 7,
									'unsigned'		=> TRUE,
									'null'			=> FALSE,
									'auto_increment'	=> TRUE
								),
				'entry_id'  => array(
									'type'			=> 'int',
									'constraint'		=> 7,
									'unsigned'		=> TRUE,
									'null'			=> FALSE,
									'default'			=> 0
								),
				'hour'  => array(
									'type' 			=> 'varchar',
									'constraint'		=> '4',
									'null'			=> FALSE,
									'default'			=> ''
								),
				'minute'  => array(
									'type' 			=> 'varchar',
									'constraint'		=> '4',
									'null'			=> FALSE,
									'default'			=> ''
								),
				'day'  => array(
									'type' 			=> 'varchar',
									'constraint'		=> '4',
									'null'			=> FALSE,
									'default'			=> ''
								),
				'month'  => array(
									'type' 			=> 'varchar',
									'constraint'		=> '4',
									'null'			=> FALSE,
									'default'			=> ''
								),
				'year'  => array(
									'type' 			=> 'varchar',
									'constraint'		=> '4',
									'null'			=> FALSE,
									'default'			=> ''
								),
				'date'  => array(
									'type'			=> 'int',
									'constraint'		=> 10,
									'unsigned'		=> TRUE,
									'null'			=> FALSE
								),
				
		);
		
		//create the backup database
		ee()->dbforge->add_field($fields);
		ee()->dbforge->add_key('search_id', TRUE);
		ee()->dbforge->create_table(DATE_DD_MAP.'_search', TRUE);
		
	}
	
	// ----------------------------------------------------------------
	
	/**
	 * Install a hook for the extension
	 *
	 * @return 	boolean 	TRUE
	 */		
	private function _register_hook($hook, $method = NULL, $priority = 10)
	{
		if (is_null($method))
		{
			$method = $hook;
		}

		if (ee()->db->where('class', DATE_DD_CLASS.'_ext')
			->where('hook', $hook)
			->count_all_results('extensions') == 0)
		{
			ee()->db->insert('extensions', array(
				'class'		=> DATE_DD_CLASS.'_ext',
				'method'	=> $method,
				'hook'		=> $hook,
				'settings'	=> '',
				'priority'	=> $priority,
				'version'	=> DATE_DD_VERSION,
				'enabled'	=> 'y'
			));
		}
	}
	
	// ----------------------------------------------------------------
	
	/**
	 * Create a action
	 *
	 * @return 	boolean 	TRUE
	 */	
	private function _register_action($method)
	{		
		if (ee()->db->where('class', DATE_DD_CLASS)
			->where('method', $method)
			->count_all_results('actions') == 0)
		{
			ee()->db->insert('actions', array(
				'class' => DATE_DD_CLASS,
				'method' => $method
			));
		}
	}

	// ----------------------------------------------------------------
	
	/**
	 * Create a tab
	 *
	 * @return 	boolean 	TRUE
	 */	
	private function _tabs()
	{		
		$tabs['tab_name'] = array(
			'field_name_one'=> array(
				'visible'   => 'true',
				'collapse'  => 'false',
				'htmlbuttons'   => 'true',
				'width'     => '100%'
			),
				'field_name_two'=> array(
				'visible'   => 'true',
				'collapse'  => 'false',
				'htmlbuttons'   => 'true',
				'width'     => '100%'
			),
		);

    return $tabs;
	}
	
	// ----------------------------------------------------------------
	
	/**
	 * Run a update from a file
	 *
	 * @return 	boolean 	TRUE
	 */	
	
	private function _init_update($version, $data = '')
	{
		// run the update file
		$class_name = DATE_DD_CLASS.'_upd_'.str_replace('.', '', $version);
		require_once(PATH_THIRD.DATE_DD_MAP.'/updates/'.strtolower($class_name).'.php');
		$updater = new $class_name($data);
		return $updater->run_update();
	}
	
}
/* End of file upd.default.php */
/* Location: /system/expressionengine/third_party/default/upd.default.php */