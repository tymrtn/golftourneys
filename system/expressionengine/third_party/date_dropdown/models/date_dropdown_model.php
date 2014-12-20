<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Default Model
 *
 * @package		Default name
 * @category	Modules
 * @author		Rein de Vries <info@reinos.nl>
 * @link		http://reinos.nl
 * @copyright 	Copyright (c) 2014 Reinos.nl Internet Media
 */

/**
 * Include the config file
 */
require_once PATH_THIRD.'date_dropdown/config.php';

class Date_dropdown_model
{

	private $EE;

	public function __construct()
	{							
		// Creat EE Instance
		$this->EE =& get_instance();
	}

	// --------------------------------------------------------------------

	/**
	 * Cout all itemst
	 *
	 * @access	public
	 * @return	array
	 */
	public function count_items()
	{
		$q = ee()->db->get(DATE_DD_MAP.'_paths');
		return $q->num_rows();
	}

	// --------------------------------------------------------------------

	/**
	 * Get all aliases
	 *
	 * @access	public
	 * @return	void
	 */
	public function get_all_items($entry_id = '', $start = 0, $limit = false, $order = array())
	{
		$results = array();
		$q = '';

		//get all alias for an specific site_id
		if($entry_id == '')
		{
			ee()->db->select('*');
			ee()->db->from(DATE_DD_MAP.'_paths');
			ee()->db->where('site_id', ee()->config->item('site_id'));
		}

		//Fetch a list of entries in array
		else if(is_array($entry_id) && !empty($entry_id))
		{
			ee()->db->select('*');
			ee()->db->from(DATE_DD_MAP.'_paths');
			ee()->db->where('site_id', ee()->config->item('site_id'));
			ee()->db->where_in('entry_id', $entry_id);
		}

		//fetch only the alias for an entry_id
		else if(!is_array($entry_id))
		{
			ee()->db->select('*');
			ee()->db->from(DATE_DD_MAP.'_paths');
			ee()->db->where('site_id', ee()->config->item('site_id'));
			ee()->db->where('entry_id', $entry_id);
		}

		//do nothing
		else
		{
			return array();
		}

		//is there a start and limit
		if($limit !== false)
		{
			ee()->db->limit($start, $limit);
		}

		//do we need to order
		//given by the mcp table method http://ellislab.com/expressionengine/user-guide/development/usage/table.html
		if(!empty($order))
		{
			if(isset($order[DATE_DD_MAP.'_current_path']))
			{
				ee()->db->order_by('current_path', $order[DATE_DD_MAP.'_current_path']);	
			}
		}
		
		//get the result
		$q = ee()->db->get();

		//format result
		if($q != '' && $q->num_rows())
		{
			foreach($q->result() as $val)
			{
				$results[] = $val;
			}
		}

		return $results;
	}
	
	// --------------------------------------------------------------------

	/**
	 * Search the items
	 *
	 * @access	public
	 * @return	void
	 */
	public function search($year = '', $month = '', $day = '', $orderby = 'time', $sort = 'asc', $start_from = '', $limit = 99999, $status = 'Open')
	{
		ee()->db->select(DATE_DD_MAP.'_search.entry_id');
		ee()->db->from(DATE_DD_MAP.'_search');
		if($year != '')
		{
			ee()->db->where(DATE_DD_MAP.'_search.year', $year);
		}
		if($month != '')
		{
			ee()->db->where(DATE_DD_MAP.'_search.month', $month);
		}
		if($day != '')
		{
			ee()->db->where(DATE_DD_MAP.'_search.day', $day);
		}

		//sorts
		ee()->db->order_by(DATE_DD_MAP.'_search.'.$orderby, $sort); 
		
		//start from
		if($start_from != '') 
		{
			//echo $orderby.' > , '. $start_from;exit;
			ee()->db->where(DATE_DD_MAP.'_search.'.$orderby.' >=', $start_from);
		}
		
		//set the limit
		ee()->db->limit($limit);
		
		//set the join on the channel_titles table, to match the status
		ee()->db->join('channel_titles', 'channel_titles.entry_id = '.DATE_DD_MAP.'_search.entry_id');
		
		//set the where on the status
		ee()->db->where_in('channel_titles.status', explode('|', $status));
		
		//ee()->db->save_queries = true;
		$query = ee()->db->get();
		//echo ee()->db->last_query();exit;
		
		return $query;
	}


} // END CLASS

/* End of file default_model.php  */
/* Location: ./system/expressionengine/third_party/default/models/default_model.php */