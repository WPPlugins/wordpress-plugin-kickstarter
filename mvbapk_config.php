<?php

/*
  Copyright (C) <2011>  Vasyl Martyniuk <martyniuk.vasyl@gmail.com>

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

define('MVBAPK_PREFIX', 'Mvbapk_');
define('MVBAPK_BASE_DIR', dirname(__FILE__) . '/');
define('MVBAPK_PLUGIN_NAME', basename(MVBAPK_BASE_DIR));
define('MVBAPK_BASE_URL', WP_PLUGIN_URL . '/' . MVBAPK_PLUGIN_NAME . '/');
define('MVBAPK_HTML_DIR', MVBAPK_BASE_DIR . 'view/html/');
define('MVBAPK_JS_DIR', MVBAPK_BASE_URL . 'view/js/');
define('MVBAPK_CSS_DIR', MVBAPK_BASE_URL . 'view/css/');
define('MVBAPK_OPTION_PAGE', 'mvbapk');
define('MVBAPK_ABOUT_PAGE', 'awm-group');

require_once('mvbapk_functions.php');

//register autoload function
spl_autoload_register('mvbapk_autoload');

define('MVBAPK_NO_ERRORS', 0);
define('MVBAPK_WARNING', 1);
define('MVBAPK_ERROR', 2);

if (!class_exists('phpQuery')) {
    require_once('view/phpQuery/phpQuery.php');
}

?>