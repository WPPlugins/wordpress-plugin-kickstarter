<?php
/*
  Plugin Name: WordPress Plugin Kickstarter
  Description: Quick and easy way to create a template for future WordPress Plugin
  Version: 0.7.2
  Author: Vasyl Martyniuk  <whimba@gmail.com>
  Author URI: http://www.whimba.org
 */

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

require('mvbapk_config.php');

class Mvbapk_Kickstarter {

    private $controlNode;

    function __construct() {
        
        $this->controlNode = new Mvbapk_Control_Controller();
    }

    static public function activate() {
        global $wp_version;

        /* Version check */
        $exit_msg = 'Plugin requires WordPress 3.0 or newer. '
                . '<a href="' . WP_UPGRADE_URL . '">Update now!</a>';

        if (version_compare($wp_version, '3.0', '<')) {
            exit($exit_msg);
        }
        
        $exit_msg = 'WordPress Plugin Kickstarter requires PHP 5.0 and newer.';
        
        if (phpversion() < '5.0') {
            exit($exit_msg);
        }
    }
}

register_activation_hook(__FILE__, array('Mvbapk_Kickstarter', 'activate'));
add_action('init', 'mvbapk_init');
?>