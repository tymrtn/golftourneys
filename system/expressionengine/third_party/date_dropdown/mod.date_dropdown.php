<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Default Module file
 *
 * @package     Default module
 * @category    Modules
 * @author      Rein de Vries <info@reinos.nl>
 * @link        http://reinos.nl
 * @copyright   Copyright (c) 2014 Reinos.nl Internet Media
 */
 
/**
 * Include the config file
 */
require_once PATH_THIRD.'date_dropdown/config.php';

class Date_dropdown {
        
    private $EE;
    
    // ----------------------------------------------------------------------
    
    /**
     * Constructor
     */
    public function __construct()
    {       
        //load default helper
        ee()->load->library(DATE_DD_MAP.'_lib');

        //require the default settings
        require PATH_THIRD.DATE_DD_MAP.'/settings.php';

    }

    // ----------------------------------------------------------------------
    
    /**
     * search 
     */
    public function search()
    {   
        //get the params    
        $year = ee()->TMPL->fetch_param('year', '');
        $month = ee()->TMPL->fetch_param('month', '');
        $day = ee()->TMPL->fetch_param('day', '');
        
        //order
        $orderby = ee()->TMPL->fetch_param('orderby', 'time');
        $sort = ee()->TMPL->fetch_param('sort', 'asc');
        
        //start from
        $start_from = ee()->TMPL->fetch_param('start_on', ee()->TMPL->fetch_param('start_from', ''));
        //limit
        $limit = ee()->TMPL->fetch_param('limit', 99999);
        
        //check the other month aswell
        $check_next_month = date_dropdown_helper::check_yes(ee()->TMPL->fetch_param('check_next_month', 'no'));
        
        //set the status
        $status = ee()->TMPL->fetch_param('status', 'Open');

        //define array
        $variables = array();
        $entry_ids = array();
        
        //get the results
        $query = ee()->date_dropdown_model->search($year, $month, $day, $orderby, $sort, $start_from, $limit, $status);
        
        if($query->num_rows() > 0)
        {
            foreach($query->result() as $row)
            {
                $entry_ids[] = $row->entry_id;
            }
        }
        
        //if the result is lower then the limit, 
        //and the $check_next_month is true, than check also the next month
        if($check_next_month && count($entry_ids) < $limit)
        {
            //new limit
            $new_limit = $limit - count($entry_ids);
            //next month
            $month = $this->set_next_month($month);
            //reset the start from
            $start_from = '01';
            //do the query
            $query = ee()->date_dropdown_model->search($year, $month, $day, $orderby, $sort, $start_from, $new_limit, $status);
            if($query->num_rows() > 0)
            {
                foreach($query->result() as $row)
                {
                    $entry_ids[] = $row->entry_id;
                }
            }
        }

        //assign vars
        $variables[0]['entry_ids'] = implode('|', $entry_ids);

        //return the data
        return ee()->TMPL->parse_variables(ee()->TMPL->tagdata, $variables);

    }

    // ----------------------------------------------------------------------

    /**
     * set next month
     */
    public function set_next_month($month)
    {
        //last month?
        if($month == '12')
        {
            $month = '01';
        }
        
        //normal
        else
        {
            //do we have a zero as leading,
            //uhu, always!!
            $month = (int)$month + 1;
            $month = '0'.$month;
        }
        
        return $month;
    } 

    // ----------------------------------------------------------------

}


/* End of file mod.default.php */
/* Location: /system/expressionengine/third_party/default/mod.default.php */