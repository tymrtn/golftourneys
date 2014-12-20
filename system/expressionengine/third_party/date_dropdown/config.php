<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Default config
 *
 * @package		Default module
 * @category	Modules
 * @author		Rein de Vries <info@reinos.nl>
 * @link		http://reinos.nl
 * @copyright 	Copyright (c) 2014 Reinos.nl Internet Media
 */

//contants
if ( ! defined('DATE_DD_NAME'))
{
	define('DATE_DD_NAME', 'Date Dropdown');
	define('DATE_DD_CLASS', 'Date_dropdown');
	define('DATE_DD_MAP', 'date_dropdown');
	define('DATE_DD_VERSION', '1.4.1');
	define('DATE_DD_DESCRIPTION', 'Date Dropdown');
	define('DATE_DD_DOCS', '');
	define('DATE_DD_DEVOTEE', '');
	define('DATE_DD_AUTHOR', 'Rein de Vries');
	define('DATE_DD_DEBUG', true);
	define('DATE_DD_STATS_URL', 'http://reinos.nl/index.php/module_stats_api/v1'); 
}

//configs
$config['name'] = DATE_DD_NAME;
$config['version'] = DATE_DD_VERSION;

//load compat file
require_once(PATH_THIRD.DATE_DD_MAP.'/compat.php');

/* End of file config.php */
/* Location: /system/expressionengine/third_party/default/config.php */