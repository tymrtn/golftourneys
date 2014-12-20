<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * the settings for the module
 *
 * @package		Default module
 * @category	Modules
 * @author		Rein de Vries <info@reinos.nl>
 * @link		http://reinos.nl
 * @copyright 	Copyright (c) 2014 Reinos.nl Internet Media
 */

//updates
$this->updates = array(
	'1.4',
);

//Default Post
$this->default_post = array(
	'license_key'   		=> '',
	'report_date' 			=> time(),
	'report_stats' 			=> true,
);

//overrides
$this->overide_settings = array(
	//'gmaps_icon_dir' => '[theme_dir]images/icons/',
	//'gmaps_icon_url' => '[theme_url]images/icons/',
);

// Backwards-compatibility with pre-2.6 Localize class
$this->format_date_fn = (version_compare(APP_VER, '2.6', '>=')) ? 'format_date' : 'decode_date';

//mcp veld header
$this->table_headers = array(
	DATE_DD_MAP.'_entry_id' => array('data' => lang(DATE_DD_MAP.'_entry_id'), 'style' => 'width:10%;'),
	DATE_DD_MAP.'_current_path' => array('data' => lang(DATE_DD_MAP.'_current_path'), 'style' => 'width:40%;'),
	DATE_DD_MAP.'_alias_path' => array('data' => lang(DATE_DD_MAP.'_alias_path'), 'style' => 'width:40%;'),
	'actions' => array('data' => '', 'style' => 'width:10%;')
);

$this->fieldtype_settings = array(
	/*array(
		'label' => lang('date_format', 'date_format'),
		'name' => 'date_format',
		'type' => 's', // s=select, m=multiselect t=text
		'options' => array('unix' => 'unix', 'ymd' => 'ymd'),
		'def_value' => 'unix'
	),*/
	array(
		'label' => lang('year_range', 'year_range'),
		'name' => 'year_range',
		'type' => 't', // s=select, m=multiselect t=text
		'options' => '1900-2020',
		'def_value' => '1900-2020'
	),
	// array(
	// 	'label' => lang('pattern', 'pattern'),
	// 	'name' => 'pattern',
	// 	'type' => 't', // s=select, m=multiselect t=text
	// 	'options' => '1900-2020',
	// 	'def_value' => '1900-2020'
	// ),
	array(
		'label' => lang('show_picker', 'show_picker'),
		'name' => 'show_picker',
		'type' => 's', // s=select, m=multiselect t=text
		'options' => array(
			lang('no'),
			lang('yes')
		),
		'def_value' => '1900-2020'
	),
	array(
		'label' => lang('show_time', 'show_time'),
		'name' => 'show_time',
		'type' => 's', // s=select, m=multiselect t=text
		'options' => array(
			lang('no'),
			lang('yes')
		),
		'def_value' => '1900-2020'
	),
	// array(
	// 	'label' => lang('pattern', 'pattern'),
	// 	'name' => 'pattern',
	// 	'type' => 's', // s=select, m=multiselect t=text
	// 	'options' => array(
	// 		'd-m-y' => 'd-m-y',
	// 		'm-d-y' => 'm-d-y',
	// 		'y-m-d' => 'y-m-d',
	// 		'y-d-m' => 'y-d-m'
	// 	),
	// 	'def_value' => 'd-m-y'
	// ),

);

/* End of file settings.php  */
/* Location: ./system/expressionengine/third_party/default/settings.php */