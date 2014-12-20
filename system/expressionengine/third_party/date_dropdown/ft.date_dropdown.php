<?php if (!defined('BASEPATH')) die('No direct script access allowed');

/**
 * fieldtype
 *
 * @package     MSM Site Select fieldtype module
 * @category    Modules
 * @author      Rein de Vries <info@reinos.nl>
 * @link        http://reinos.nl
 * @copyright   Copyright (c) 2014 Reinos.nl Internet Media
 */

include(PATH_THIRD.'date_dropdown/config.php');

class Date_dropdown_ft extends EE_Fieldtype
{
  public $info = array(
    'name'      => DATE_DD_NAME,
    'version'   => DATE_DD_VERSION
  );

  public $settings = array();

  public $default_settings = array();

  public $has_array_data = TRUE;

  private $prefix;

  // ----------------------------------------------------------------------

  /**
   * Constructor
   *
   * @access public
   *
   * Calls the parent constructor
   */
  public function __construct()
  {
    if (version_compare(APP_VER, '2.1.4', '>')) { parent::__construct(); } else { parent::EE_Fieldtype(); }

    $this->prefix = DATE_DD_MAP.'_';

    //load lang file
    ee()->lang->loadfile(DATE_DD_MAP);

    //do we need to update
    $this->update();

    $this->_time_format = $this->EE->config->item('time_format');

    ee()->load->add_package_path(PATH_THIRD . 'date_dropdown/');
    ee()->load->library('date_dropdown_lib');
    //ee()->load->remove_package_path(PATH_THIRD . 'date_dropdown/');

    //require settings
    require 'settings.php';
  }

  // ----------------------------------------------------------------------
  // Before saving the content to the database
  // ----------------------------------------------------------------------

  /**
   * save (Native EE)
   *
   * @access public
   */
  public function save($data)
  {
    return $this->_save($data);
  }

  // ----------------------------------------------------------------------

  /**
   * save (Low Variables)
   *
   * @access public
   */
  /*public function save_var_field($data)
  {
    return $this->_save($data);
  }*/

  // ----------------------------------------------------------------------

  /**
   * save (Matrix)
   *
   * @access public
   */
  /*public function save_cell($data)
  {
    return $this->_save($data);
  }*/

  // ----------------------------------------------------------------------

  /**
   * save (Content Elements)
   *
   * @access public
   */
  /*public function save_element($data)
  {
    return $this->_save($data);
  }*/

  // ----------------------------------------------------------------------

  /**
   * save (Bulk Edit)
   *
   * @access public
   */
  public function bulk_edit_save($data)
  {
    return $this->_save($data);
  }

  // ----------------------------------------------------------------------

  /**
   * save (Entry API)
   *
   * @access public
   */
  //public function entry_api_save($data)
  //{
  //   return $this->_save($data);
  //}


  // ----------------------------------------------------------------------

  /**
   * save
   *
   * @access public
   */
  private function _save($data = '')
  {
    $field_data = $data;

    if ( ! is_array($field_data))
    {
      return '';
    }

    //picker enable
    if($this->settings['date_dropdown_show_picker'] && isset($field_data['picker']) && $field_data['picker'] != '')
    {
      $day    = substr($field_data['picker'], 0, 2);
      $month  = substr($field_data['picker'], 3, 2);
      $year   = substr($field_data['picker'], 6, 4);
      $hour   = substr($field_data['picker'], 11, 2);
      $hour   = $hour == false ? '00' : $hour;
      $minute = substr($field_data['picker'], 14, 2);
      $minute = $minute == false ? '00' : $minute;
    }

    //no picker
    else
    {
      $day    = $field_data['day'];
      $month  = $field_data['month'];
      $year   = $field_data['year'];
      $hour   = isset($field_data['hour']) ? $field_data['hour'] : 00;
      $minute = isset($field_data['minute']) ? $field_data['minute'] : 00;
    }

    // Do we have the bare minimum?
    if ( ! $day OR ! $month OR ! $year)
    {
      return '';
    }

    // Format the strings.
    $day    = str_pad($day, 2, '0', STR_PAD_LEFT);
    $month  = str_pad($month, 2, '0', STR_PAD_LEFT);
    $hour    = str_pad($hour, 2, '0', STR_PAD_LEFT);
    $minute  = str_pad($minute, 2, '0', STR_PAD_LEFT);

    return $year.$month.$day.$hour.$minute;
  }

  // ----------------------------------------------------------------------

  /**
   * Save extra data
   *
   * @access public
   */
  public function post_save($data = '')
  {
    ee()->load->library('date_dropdown_lib');

    //save the data to the search field
    //remove old data
    ee()->db->where('entry_id', (int) $this->settings['entry_id']);
    ee()->db->delete(DATE_DD_MAP.'_search');

    //insert the new data
    if($data != '')
    {
      //extract date parts
      $date = ee()->date_dropdown_lib->extract_date($data);

      ee()->db->insert(DATE_DD_MAP.'_search', array(
        'entry_id' => (int) $this->settings['entry_id'],
        'hour' => $date['hour'],
        'minute' => $date['minute'],
        'day' => $date['day'],
        'month' => $date['month'],
        'year' => $date['year'],
        'date' => $data
      ));
    }
  }

  // ----------------------------------------------------------------------

  /**
   * post_save (Entry API)
   *
   * @access public
   */
  public function entry_api_post_save($data)
  {
    $this->post_save($data);
  }

  // ----------------------------------------------------------------------

  /**
   * post_save (Bulk Edit)
   *
   * @access public
   */
  public function bulk_edit_post_save($data)
  {
    $this->post_save($data);
  }

  // ----------------------------------------------------------------------

  /**
   * delete
   *
   * @access public
   */
  public function delete($ids = array())
  {
    ee()->db->where_in('entry_id', $ids);
    ee()->db->delete(DATE_DD_MAP.'_search');
  }

  // ----------------------------------------------------------------------

  /**
   * entry_api_delete (Entry API)
   *
   * @access public
   */
  public function entry_api_delete($data = null, $entry_id = 0)
  {
    $this->delete(array($entry_id));
  }

  // ----------------------------------------------------------------------
  // Display the field for all types
  // ----------------------------------------------------------------------

  /**
   * display_field
   *
   * @access public
   */
  function display_field($data)
  {
    //generate the data
    return $this->_display_field($data);
  }

  // ----------------------------------------------------------------------
  // Display the field for Grid
  // ----------------------------------------------------------------------

  /**
   * display_field
   *
   * @access public
   */
  function grid_display_field($data)
  {
    //generate the data
    return $this->_display_field($data, 'grid');
  }

  // ----------------------------------------------------------------------

  /**
   * display_var_field (Low variables)
   *
   * @access public
   */
  /*function display_var_field ($data)
  {
    //generate the data
    return $this->_display_field($data, 'low_variables');
  }*/

  // ----------------------------------------------------------------------

  /**
   * display_cell (MATRIX)
   *
   * @access public
   */
  /*function display_cell( $data )
  {
     return $this->_display_field($data, 'matrix');
  }*/

  // ----------------------------------------------------------------------

  /**
   * display_element (Content Elements)
   *
   * http://www.krea.com/docs/content-elements/element-development/ee2-functions-reference
   *
   * @access public
   */
  /*function display_element($data)
  {
    return $this->_display_field($data, 'content_elements');
  }*/

  // ----------------------------------------------------------------------

  /**
   * display_var_field (Bulk edit)
   *
   * @access public
   */
  function bulk_edit_display_field($data)
  {
    //generate the data
    return $this->_display_field($data, 'bulk_edit');
  }

  // ----------------------------------------------------------------------

  /**
   * display_field
   *
   * @access public
   */
  private function _display_field($data, $type = 'default')
  {
    ee()->load->library('date_dropdown_lib');

    //save data
    $field_data = @trim($data);

    //fieldname
    switch($type)
    {
      case 'matrix': $field_name = $this->cell_name ;
        break;
      case 'default':
      default : $field_name = $this->field_name;
      break;
    }

    // Days.
    $days[] = lang('day');
    for ($count = 1; $count <= 31; $count++)
    {
      $days[str_pad($count, 2, '0', STR_PAD_LEFT)] = str_pad($count, 2, '0', STR_PAD_LEFT);
    }

    $hours = $minutes = array();

    //set minutes
    $minutes[] = lang('minutes');
    for ($count = 0; $count <= 59; $count++)
    {
      $minutes[str_pad($count, 2, '0', STR_PAD_LEFT)] = str_pad($count, 2, '0', STR_PAD_LEFT);
    }

    $hours[] = lang('hours');
    for ($count = 0; $count <= 23; $count++)
    {
      $hours[str_pad($count, 2, '0', STR_PAD_LEFT)] = str_pad($count, 2, '0', STR_PAD_LEFT);
    }

    // Months.
    $months = array(
      '00' => lang('month'),
      '01' => lang('jan'),
      '02' => lang('feb'),
      '03' => lang('mar'),
      '04' => lang('apr'),
      '05' => lang('may'),
      '06' => lang('jun'),
      '07' => lang('jul'),
      '08' => lang('aug'),
      '09' => lang('sep'),
      '10' => lang('oct'),
      '11' => lang('nov'),
      '11' => lang('dec')
    );

    //get the year range
    $year_range = isset($this->settings['date_dropdown_year_range']) ? $this->settings['date_dropdown_year_range'] : $this->default_settings['date_dropdown_year_range'];
    $year_range = str_replace('now', date('Y', time()), $year_range);


    if (preg_match('/^([0-9]{4})([\+|\-]{1}\d+)?-([0-9]{4})([\+|\-]{1}\d+)?$/', $year_range, $matches))
    {
      $from_year = isset($matches[2])
        ? (int) $matches[1] + (int) $matches[2]
        : (int) $matches[1];

      $to_year = isset($matches[4])
        ? (int) $matches[3] + (int) $matches[4]
        : (int) $matches[3];
    }
    else
    {
      $from_year  = 1900;
      $to_year  = 2020;
    }

    //support for counting backwards (e.g. 2020-1990).
    $years[]        = lang('year');
    $year_step      = $from_year > $to_year ? -1 : 1;
    $year_counter   = $from_year;

    while ($year_counter != ($to_year + $year_step))
    {
      $years[$year_counter] = $year_counter;
      $year_counter += $year_step;
    }



    // We start by assuming there is no previously-saved data OR submitted data.
    $saved_year = $saved_month = $saved_day = $saved_hour = $saved_minute = '';
    if ($field_data)
    {
      //extract date parts
      $date = ee()->date_dropdown_lib->extract_date($data);

      // Previously-saved data, in UNIX format.
      $saved_year         = $date['year'];
      $saved_month        = $date['month'];
      $saved_day          = $date['day'];
      $saved_hour         = isset($date['hour']) ? $date['hour'] : '';
      $saved_minute       = isset($date['minute']) ? $date['minute'] : '';
    }

    $time = '';
    if($this->settings['date_dropdown_show_time'])
    {
      $time = ' : '
        . form_dropdown($field_name ."[hour]", $hours, $saved_hour)
        . ' : '
        . form_dropdown($field_name ."[minute]", $minutes, $saved_minute);
    }

    // Begin building output.
    //if($this->settings['date_dropdown_pattern'] == 'd-m-y')
    //{
    $output = ''
      . form_dropdown($field_name ."[day]", $days, $saved_day)
      . ' - '
      . form_dropdown($field_name ."[month]", $months, $saved_month)
      . ' - '
      . form_dropdown($field_name ."[year]", $years, $saved_year)
      . $time;


    // }
    // else  if($this->settings['date_dropdown_pattern'] == 'm-d-y')
    // {
    //     $output = ''
    //       . form_dropdown($field_name ."[month]", $months, $saved_month)
    //       . ' - '
    //       . form_dropdown($field_name ."[day]", $days, $saved_day)
    //       . ' - '
    //       . form_dropdown($field_name ."[year]", $years, $saved_year)
    //       . $time;
    // }
    // else if($this->settings['date_dropdown_pattern'] == 'y-m-d')
    // {
    //     $output = ''
    //       . form_dropdown($field_name ."[year]", $years, $saved_year)
    //       . ' - '
    //       . form_dropdown($field_name ."[month]", $months, $saved_month)
    //       . ' - '
    //       . form_dropdown($field_name ."[day]", $days, $saved_day)
    //       . $time;

    // }
    // else if($this->settings['date_dropdown_pattern'] == 'y-d-m')
    // {
    //     $output = ''
    //       . form_dropdown($field_name ."[year]", $years, $saved_year)
    //       . ' - '
    //       . form_dropdown($field_name ."[day]", $days, $saved_day)
    //       . ' - '
    //       . form_dropdown($field_name ."[month]", $months, $saved_month)
    //       . $time;
    // }
    // else
    // {
    //     $output = ''
    //       . form_dropdown($field_name ."[day]", $days, $saved_day)
    //       . ' - '
    //       . form_dropdown($field_name ."[month]", $months, $saved_month)
    //       . ' - '
    //       . form_dropdown($field_name ."[year]", $years, $saved_year)
    //       . $time;
    // }

    //date picker?
    if(isset($this->settings['date_dropdown_show_picker']) && $this->settings['date_dropdown_show_picker'])
    {
      date_dropdown_helper::mcp_meta_parser('css', 'jquery-ui-timepicker-addon.min.css');
      date_dropdown_helper::mcp_meta_parser('js', 'jquery-ui-timepicker-addon.js');
      date_dropdown_helper::mcp_meta_parser('js', 'i18n/jquery-ui-timepicker-addon-i18n.min.js');
      date_dropdown_helper::mcp_meta_parser('js', 'jquery-ui-sliderAccess.js');

      $value = ee()->date_dropdown_lib->extract_date($field_data);
      if(isset($value['year']) && $value['year'] != false)
      {
        $field_data = $value['day'].'-'.$value['month'].'-'.$value['year'];

        if($this->settings['date_dropdown_show_time'])
        {
          $field_data .= ' '.$value['hour'].':'.$value['minute'];
        }
      }


      $data = array(
        'name'        => $field_name.'[picker]',
        'id'          => $field_name.'_picker',
        'value'       => $field_data
      );

      if($type == 'grid')
      {
        unset($data['id']);
        $data['class'] = 'date_dropdown_picker';
      }

      $output = "<div style='display:none;'>".$output."</div>";

      $output .= form_input($data);


      if($this->settings['date_dropdown_show_time'])
      {
        date_dropdown_helper::mcp_meta_parser('js_inline', '
                    $("#'.$field_name.'_picker").datetimepicker({
                        dateFormat: "dd-mm-yy",
                        timeFormat: "hh:mm",
                        yearRange: "'.$from_year.':'.$to_year.'",
                        changeMonth: true,
                        changeYear: true
                    });
                ');

        if($type == 'grid')
        {
          date_dropdown_helper::mcp_meta_parser('js_inline', '
            $("#field_id_'.$this->settings['grid_field_id'].' .grid_link_add, .grid_button_add").click(function () {
              setTimeout(function(){
                $("#field_id_'.$this->settings['grid_field_id'].' .grid_row:last .date_dropdown_picker").datetimepicker({
                  dateFormat: "dd-mm-yy",
                  timeFormat: "hh:mm",
                  yearRange: "'.$from_year.':'.$to_year.'",
                  changeMonth: true,
                  changeYear: true
                });
              }, 300);
            });
          ');
        }
      }
      else
      {
        date_dropdown_helper::mcp_meta_parser('js_inline', '
                    $("#'.$field_name.'_picker").datepicker({
                        dateFormat: "dd-mm-yy", 
                        yearRange: "'.$from_year.':'.$to_year.'",
                        changeMonth: true,
                        changeYear: true
                    });
                ');

        if($type == 'grid')
        {
          date_dropdown_helper::mcp_meta_parser('js_inline', '
            $("#field_id_'.$this->settings['grid_field_id'].' .grid_link_add, .grid_button_add").click(function () {
              setTimeout(function(){
                $("#field_id_'.$this->settings['grid_field_id'].' .grid_row:last .date_dropdown_picker").datepicker({
                  dateFormat: "dd-mm-yy",
                  yearRange: "'.$from_year.':'.$to_year.'",
                  changeMonth: true,
                  changeYear: true
                });
              }, 300);
            });
          ');
        }

      }


    }

    // Return generated HTML.
    return $output;
  }

  // ----------------------------------------------------------------------
  // Replace the tags for all types
  // ----------------------------------------------------------------------

  /**
   * display_var_tag (Low variables)
   *
   * @access public
   */
  /*public function display_var_tag($var_data, $tagparams, $tagdata)
  {
    return $this->replace_tag($var_data, $tagparams, $tagdata);
  }*/

  // ----------------------------------------------------------------------

  /**
   * replace_element_tag (Content Elements)
   *
   * @access public
   */
  /*public function replace_element_tag($data, $params = array(), $tagdata)
  {
    return $this->replace_tag($data, $params, $tagdata);
  }*/

  // ----------------------------------------------------------------------

  /**
   * replace_tag
   *
   * @access public
   */
  public function replace_tag($data, $params = array(), $tagdata = FALSE)
  {
    ee()->load->library('date_dropdown_lib');

    $field_data = $data;

    if ( ! $field_data)
    {
      return '';
    }

    if($params == false)
    {
      $params = array();
    }

    $params = array_merge(array('format' => 'U'), $params);

    //convert
    $field_data = ee()->date_dropdown_lib->calculate_timestamp($field_data);

    // if there's a percentage sign in the format, use EE's native date function for language file use
    if (strpos($params['format'], '%') === FALSE)
    {
      return date($params['format'], $field_data);
    }
    else
    {
      return ee()->localize->{$this->format_date_fn}($params['format'], $field_data);
    }
  }

  // ----------------------------------------------------------------------

  /**
   * replace_tag_catchall
   *
   * @access public
   */
  /*function replace_tag_catchall($file_info, $params = array(), $tagdata = FALSE, $modifier)
  {

  }*/

  // ----------------------------------------------------------------------

  // ----------------------------------------------------------------------

  /**
   * Zenbu display support
   *
   * @access public
   */
  function zenbu_display($entry_id, $channel_id, $data, $table_data, $field_id, $settings, $rules, $upload_prefs, $installed_addons)
  {
    if($data == '')
      return '';

    $field_settings = ee()->date_dropdown_lib->grab_field_settings($field_id);

    $field_data = ee()->date_dropdown_lib->calculate_timestamp($data);

    if($field_settings['date_dropdown_show_time'])
    {
      return date('d-m-Y H:i', $field_data);
    }
    else
    {
      return date('d-m-Y', $field_data);
    }
  }

  /**
   * entry_api_pre_process (Entry API)
   * just return timestamp
   *
   * @access public
   */
  public function entry_api_pre_process($data = null)
  {
    ee()->load->library('date_dropdown_lib');

    if ( ! $data)
    {
      return '';
    }

    return ee()->date_dropdown_lib->calculate_timestamp($data);
  }

  // ----------------------------------------------------------------------
  // Display the settings for all types
  // ----------------------------------------------------------------------

  /**
   * Display settings screen (Default EE)
   *
   * @access  public
   */
  function display_settings($data)
  {
    foreach($this->_display_settings($data) as $val)
    {
      ee()->table->add_row($val);
    }
  }

  // --------------------------------------------------------------------

  /**
   * Display settings screen (Matrix)
   *
   * @access  public
   */
  /* function display_cell_settings($data)
    {
      return $this->_display_settings($data, array('matrix_input' => 'class="matrix-textarea"'));
    }*/

  // --------------------------------------------------------------------

  /**
   * Display settings screen (Low variables)
   *
   * @access  public
   */
  /*function display_var_settings($data)
  {
    return $this->_display_settings($data);
  }*/

  // --------------------------------------------------------------------

  /**
   * Display settings screen (EE 2.7 GRID)
   *
   * @access  public
   */
  function grid_display_settings($data)
  {
    return $this->_display_settings($data, array(), 'grid');
  }

  // --------------------------------------------------------------------

  /**
   * Display settings screen (Content Elements)
   *
   * @access  public
   */
  /*function display_element_settings($data)
    {
      return $this->_display_settings($data);
    }*/

  // --------------------------------------------------------------------

  /**
   * Display settings screen
   *
   * @access  public
   */
  private function _display_settings($data, $options = array(), $type = '')
  {
    $matrix_input = isset($options['matrix_input']) ? $options['matrix_input'] : '';

    $return = array();

    if(!empty($this->fieldtype_settings))
    {
      foreach($this->fieldtype_settings as $field=>$options)
      {
        switch($options['type'])
        {
          //multiselect
          case 'm' :
            //grid
            if($type == 'grid')
            {
              $return[] = $this->grid_dropdown_row(
                $options['label'],
                $this->prefix.$options['name'].'[]',
                $options['options'],
                isset($data[$this->prefix.$options['name']]) ? $data[$this->prefix.$options['name']] : $options['def_value'],
                true
              );
            }

            //normal
            else
            {
              $return[] = array(
                $options['label'],
                form_multiselect($this->prefix.$options['name'].'[]', $options['options'], (isset($data[$this->prefix.$options['name']]) ? $data[$this->prefix.$options['name']] : $options['def_value'] ))
              );
            }
            break;

          //select field
          case 's' :
            //grid
            if($type == 'grid')
            {
              $return[] = $this->grid_dropdown_row(
                $options['label'],
                $this->prefix.$options['name'],
                $options['options'],
                isset($data[$this->prefix.$options['name']]) ? $data[$this->prefix.$options['name']] : $options['def_value']
              );
            }

            //normal
            else
            {
              $return[] = array(
                $options['label'],
                form_dropdown($this->prefix.$options['name'], $options['options'], (isset($data[$this->prefix.$options['name']]) ? $data[$this->prefix.$options['name']] : $options['def_value'] ))
              );
            }

            break;

          //text field
          default:
          case 't' :
            //grid
            if($type == 'grid')
            {
              $return[] =  form_label($options['label']).NBS.NBS.NBS.
                form_input(array(
                  'name'  => $this->prefix.$options['name'],
                  'value' => (isset($data[$this->prefix.$options['name']]) ? $data[$this->prefix.$options['name']] : $options['def_value'] ),
                  'class' => 'grid_input_text_small'
                )).NBS.NBS.NBS;
            }

            //normal
            else
            {
              $return[] = array(
                $options['label'],
                form_input($this->prefix.$options['name'], (isset($data[$this->prefix.$options['name']]) ? $data[$this->prefix.$options['name']] : $options['def_value'] ), $matrix_input)
              );
            }
            break;
        }
      }
    }

    return $return;
  }

  // ----------------------------------------------------------------------
  // Save the settings for all types
  // ----------------------------------------------------------------------

  /**
   * save_settings (Default EE)
   *
   * @access public
   */
  function save_settings($data)
  {
    return $this->_save_settings($data);
  }

  // --------------------------------------------------------------------

  /**
   * save_settings (matrix)
   *
   * @access public
   */
  /*function save_cell_settings($data)
  {
    return $this->_save_settings($data, true);
  } */

  // --------------------------------------------------------------------

  /**
   * save_settings ((Low variables))
   *
   * @access public
   */
  /*function save_var_settings($data)
  {
    return $this->_save_settings($data);
  } */

  // --------------------------------------------------------------------

  /**
   * save_settings (EE 2.7 GRID)
   *
   * @access public
   */
  function grid_save_settings($data)
  {
    return $this->_save_settings($data, true);
  }

  // --------------------------------------------------------------------

  /**
   * save_settings
   *
   * @access public
   */
  private function _save_settings($data, $use_data = false)
  {
    $return = array();

    if(!empty($this->fieldtype_settings))
    {
      foreach($this->fieldtype_settings as $field=>$options)
      {
        $return[$this->prefix.$options['name']] =  $use_data ? $data[$this->prefix.$options['name']] : ee()->input->post($this->prefix.$options['name']);
      }
    }

    return $return;
  }

  // ----------------------------------------------------------------------

  /**
   * install
   *
   * @access public
   */
  function install()
  {
    $return = array();

    if(!empty($this->fieldtype_settings))
    {
      foreach($this->fieldtype_settings as $field=>$options)
      {
        $return[$this->prefix.$options['name']] = $options['def_value'];
      }
    }

    return $return;
  }

  // ----------------------------------------------------------------------

  /**
   * Update
   */
  function update()
  {
    //get latest version
    /*$version = ee()->db->select('version, settings')->from('fieldtypes')->where('name', 'msm_site_select')->get()->row();

    if(isset($version->version))
    {
      $from = $version->version;

      if (! $from || $from == DATE_DD_VERSION) return FALSE;

       //update to version 1.1
      if (version_compare($from, '1.1', '<'))
      {
        $version->settings = unserialize(base64_decode($version->settings));
        $version->settings['allowed_sites'] = '';

        //update the settings
        ee()->db->where('name', 'msm_site_select');
        ee()->db->update('fieldtypes', array(
          'settings' => base64_encode(serialize($version->settings)),
          'version' =>  DATE_DD_VERSION
        ));
      }
    }

    return TRUE;*/
  }

  // ----------------------------------------------------------------------

  /**
   * accepts_content_type (GRID EE 2.7)
   *
   * @access public
   */
  public function accepts_content_type($name)
  {
    return ($name == 'channel' || $name == 'grid');
    //return true;
  }

  // --------------------------------------------------------------------



  // ----------------------------------------------------------------------
  // Custom
  // ----------------------------------------------------------------------

  /**
   * _set_map_data
   *
   * @access private
   */
  public function get_allowed_sites()
  {
    //get the sites
    ee()->db->select('*');
    ee()->db->from('sites');
    $result = ee()->db->get();

    $list = array();

    if($result->num_rows() > 0)
    {
      foreach($result->result() as $row)
      {
        $list[$row->site_id] = $row->site_label;
      }
    }

    return $list;
  }

  // ----------------------------------------------------------------------

  /**
   * _set_params
   *
   * @access private
   */
  /*private function _set_params($params)
  {
    $new_params = '';
    $method = 'geocoding'; //default
    $allowed_methods = array('geocoding', 'polygon', 'polyline'); //allowed

    if(!empty($params))
    {
      foreach($params as $key=>$val)
      {
        if($key == 'method')
        {
          if(in_array($val, $allowed_methods))
          {
            $method = $val;
          }
        }
        else
        {
          $new_params .= $key.'="'.$val.'"';
        }
      }
    }

    return array('method' => $method, 'params' => $new_params);
  }*/

  // ----------------------------------------------------------------------

}

/* End of file ft.gmaps_fieldtype.php */
/* Location: ./system/expressionengine/third_party/gmaps_fieldtype/ft.gmaps_fieldtype.php */
