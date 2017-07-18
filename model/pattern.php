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

class Mvbapk_Model_Pattern {
    /*
     * Get List of available styles
     * 
     * @return array List of style
     */

    public static function getStyleList() {

        $styles = array(
            'default' => array(
                'label' => 'Default MVC',
                'config' => MVBAPK_BASE_DIR . 'model/patterns/default/config.xml',
                'pattern' => MVBAPK_BASE_DIR . 'model/patterns/default/pattern.php',
            )
        );

        return apply_filters(MVBAPK_PREFIX . 'style-list', $styles);
    }

    /*
     * Get Kickstart's post data
     * 
     * @return array POST data
     */

    public static function getPOST() {
        global $wp_version;

        $post = $_POST['mp']['kickstarter'];
        //check required field and fill this dummy data if necessary
        if (!trim($post['name'])) {
            $post['name'] = 'Dummy Plugin';
        }
        if (!trim($post['prefix'])) {
            $post['prefix'] = 'dp_';
        }
        if (!trim($post['required'])) {
            $post['required'] = $wp_version;
        }
        //check style
        $styles = self::getStyleList();
        if (!isset($styles[$post['style']])) {
            $s_list = array_keys($styles);
            $post['style'] = $s_list[0];
        }

        return $post;
    }
    
    /*
     * Convert a label to plugin name
     * 
     * @param string Plugin Name Entered by the User
     * @param bool Whether replace white spaces with underscore
     * @return string Converted Plugin Name
     */

    public static function convertPluginName($name, $underscore = FALSE) {

        $name = strtolower(trim($name));
        if ($underscore) {
            $name = str_replace(array(' ', '"', "'"), array('_'), $name);
        } else {
            $name = str_replace(array(' ', '_', '"', "'"), array('-'), $name);
        }

        return $name;
    }

    /*
     * Render File Template
     * 
     * Replace all predefined markers with a proper values
     * 
     * @param string Template
     * @return string Parsed Template with replaces markers
     */

    public static function renderFileTemplate($template) {

        $post = self::getPOST();

        if (is_array($post)) {
            $markers = array(
                '{$plugin_label}' => $post['name'],
                '{$plugin_name}' => self::convertPluginName($post['name'], true),
                '{$plugin_name_upper}' => strtoupper(self::convertPluginName($post['name'], true)),
                '{$year}' => date('Y'),
                '{$date}' => date(DATE_RFC1036),
                '{$phpversion}' => (isset($post['php_version']) && preg_match('/[\d\.]/', $post['php_version']) ?  $post['php_version'] : phpversion()),
                '{$installation}' => '', //TODO
                '{$questions}' => '', //TODO
                '{$questions}' => '', //TODO
                '{$screenshots}' => '', //TODO
                '{$changelog}' => '', //TODO
                '{$arbitrary}' => '', //TODO
                '{$brief}' => '', //TODO
            );
            foreach ($post as $key => $value) {
                $markers['{$' . $key . '}'] = $value;
            }

            $markers = apply_filters(MVBAPK_PREFIX . 'kickstarter_markers', $markers);

            if (is_array($markers)) {
                foreach ($markers as $key => $value) {
                    $template = str_replace($key, $value, $template);
                }
            }
        }


        return $template;
    }

}

?>