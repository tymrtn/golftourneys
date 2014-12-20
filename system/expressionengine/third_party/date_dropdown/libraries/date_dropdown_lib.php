<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Default library helper
 *
 * @package		Module name
 * @category	Modules
 * @author		Rein de Vries <info@reinos.nl>
 * @link		http://reinos.nl
 * @copyright 	Copyright (c) 2014 Reinos.nl Internet Media
 */

/**
 * Include the config file
 */
require_once(PATH_THIRD.'date_dropdown/config.php');

/**
 * Include helper
 */
require_once(PATH_THIRD.'date_dropdown/libraries/date_dropdown_helper.php');

class Date_dropdown_lib
{
	private $default_settings;
	private $settings;
	private $EE;

	//debug array
	public $debug = array();

	public function __construct()
	{							
		//load model
		ee()->load->model(DATE_DD_MAP.'_model');

		//load the channel data
		ee()->load->driver('channel_data');

		//load the settings
		ee()->load->library(DATE_DD_MAP.'_settings');

		//load logger
		ee()->load->library('logger');
		
		//require the default settings
		require PATH_THIRD.DATE_DD_MAP.'/settings.php';
	}

	// ----------------------------------------------------------------------
	// CUSTOM FUNCTIONS
	// ----------------------------------------------------------------------

	/**
	 * Get the parts of the date
	 *
	 * @return void
	 */
	public function extract_date($date = '')
	{
		return array(
			'hour' => substr($date, 8,2),
			'minute' => substr($date, 10,2),
			'day' => substr($date, 6,2),
			'month' => substr($date, 4,2),
			'year' => substr($date, 0,4)
		);
	}

	// ----------------------------------------------------------------------

	/**
	 * Get the parts of the date
	 *
	 * @return void
	 */
	public function calculate_timestamp($date = '')
	{
		$date = $this->extract_date($date);

		//calculate only on years lower then 1970
		//http://stackoverflow.com/a/21523011/1779042
		if($date['year'] < 1970)
		{
			//validate the date
			// if($this->is_date($date['day']."/".$date['month']."/".$date['year']))
			// {
				$date1 = new DateTime($date['year']."-".$date['month']."-".$date['day']." ".$date['hour'].":".$date['minute']);
				
 				//$date = $date1->getTimestamp();
				$date = $date1->format("U");

				// $date2 = new DateTime("1970-01-01 ".$date['hour'].":".$date['minute']);

				// $diff_days = $date2->diff($date1)->format("%a");

				// $date = ("-".$diff_days) * 86400;



			// }
			// //other wise return this day
			// else
			// {
			// 	$date = ee()->localize->now;
			// }
			
		}
		else
		{
			$date = ee()->localize->string_to_timestamp($date['month']."/".$date['day']."/".$date['year']." ".$date['hour'].":".$date['minute']);
		}

		return $date;
	}

	function is_date($date)
	{
		return 1 === preg_match(
			'~^[0-9]{1,2)/[0-9]{1,2)/[0-9]{4)~',
			$date
		);
	}

	/**
	 * Grab File Module Settings
	 * @return array
	 */
	public function grab_field_settings($field_id=0, $settings=array())
	{
		if (isset(ee()->session->cache['date_dropdown']['field_settings'][$field_id]) === false)
		{

			if ($settings == false) {
				$query = ee()->db->select('field_settings')->from('channel_fields')->where('field_id', $field_id)->get();
				$settings = @unserialize(base64_decode($query->row('field_settings')));
			}

			// Empty? Let's make it then
			if (is_array($settings) === false) {
				$settings = array('date_dropdown' => array());
			}

			if (isset($settings['date_dropdown']) === true) {
				$settings = $settings['date_dropdown'];
			}

			ee()->session->cache['date_dropdown']['field_settings'][$field_id] = $settings;
		}
		else
		{
			$settings = ee()->session->cache['date_dropdown']['field_settings'][$field_id];
		}

		return $settings;
	}

	

	// ----------------------------------------------------------------------

	// ----------------------------------------------------------------------
	// PRIVATE FUNCTIONS
	// ----------------------------------------------------------------------
	
	// ----------------------------------------------------------------------
	// DEFAULT FUNCTIONS
	// ----------------------------------------------------------------------

	// --------------------------------------------------------------------
        
    /**
     * Hook - allows each method to check for relevant hooks
     */
    public function activate_hook($hook='', $data=array())
    {
        if ($hook AND ee()->extensions->active_hook(DATE_DD_MAP.'_'.$hook) === TRUE)
        {
                $data = ee()->extensions->call(DATE_DD_MAP.'_'.$hook, $data);
                if (ee()->extensions->end_script === TRUE) return;
        }
        
        return $data;
    }

	// ----------------------------------------------------------------------

	/**
	 * Log all messages
	 *
	 * @param array $logs The debug messages.
	 * @return void
	 */
	public function expose_log()
	{
		if(!empty($this->debug))
		{
			foreach ($this->debug as $log)
			{
				ee()->TMPL->log_item('&nbsp;&nbsp;***&nbsp;&nbsp;'.DATE_DD_CLASS.' debug: ' . $log);
			}
		}
	} 
		
	// ----------------------------------------------------------------------
	 
	
	
} // END CLASS

/* End of file default_library.php  */
/* Location: ./system/expressionengine/third_party/default/libraries/default_library.php */