<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Update file for the update to 14
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
 
class Date_dropdown_upd_14
{
	private $EE;
	private $version = '1.4';
	
	/**
	 * Construct method
	 *
	 * @return      boolean         TRUE
	 */
	public function __construct()
	{	
		//load the classes
		ee()->load->dbforge();
	}
	
	/**
	 * Run the update
	 *
	 * @return      boolean         TRUE
	 */
	public function run_update()
	{
		$sql = array();
		
		//add new extension
		$sql[] = "ALTER TABLE  `exp_date_dropdown_search` ADD  `hour` VARCHAR( 4 ) NOT NULL AFTER  `entry_id` ;";
		$sql[] = "ALTER TABLE  `exp_date_dropdown_search` ADD  `minute` VARCHAR( 4 ) NOT NULL AFTER  `hour` ;";

		foreach ($sql as $query)
		{
			ee()->db->query($query);
		}
	}
}

/* End of file default_upd_1.php  */
/* Location: ./system/expressionengine/third_party/default/updates/default_upd_1.php */