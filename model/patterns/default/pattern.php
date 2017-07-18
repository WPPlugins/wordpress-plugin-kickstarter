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

class mpks_pattern_DefaultMVC extends Mvbapk_Model_Pattern {
    /*
     * Render readme.txt
     * 
     * @return string Rendered Content
     */

    public static function renderReadMe() {

        //read readme template
        $content = file_get_contents(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'readme.tmpl');

        return self::renderFileTemplate($content);
    }

    /*
     * Render plugin_config.xml
     * 
     * @return string Rendered Content
     */

    public static function renderConfigXML() {

        $root = new SimpleXMLElement('<kickstarter/>');
        $root->addChild('created', date(DATE_RFC850));
        $user = wp_get_current_user();
        $root->addChild('created_by', $user->user_login);
        $post = self::getPOST();
        if (is_array($post)) {
            foreach ($post as $key => $value) {
                $root->addChild($key, $value);
            }
        }

        return $root->asXML();
    }

    /*
     * Render {$plugin_name}.php
     * 
     * @return string Rendered Content
     */

    public static function renderMainFile() {
        //read main template
        $content = file_get_contents(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'main.tmpl');

        return self::renderFileTemplate($content);
    }

    /*
     * Render {$prefix}config.php
     * 
     * @return string Rendered Content
     */

    public static function renderConfigFile() {
        //read config template
        $content = file_get_contents(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'config.tmpl');

        return self::renderFileTemplate($content);
    }

    /*
     * Render {$prefix}functions.php
     * 
     * @return string Rendered Content
     */

    public static function renderFunctionFile() {
        //read function template
        $content = file_get_contents(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'functions.tmpl');

        return self::renderFileTemplate($content);
    }

    /*
     * Render control/control.php
     * 
     * @return string Rendered Content
     */

    public static function renderControlFile() {
        //read control template
        $content = file_get_contents(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'control.tmpl');

        return self::renderFileTemplate($content);
    }

    /*
     * Render model/model.php
     * 
     * @return string Rendered Content
     */

    public static function renderModelFile() {
        //read model template
        $content = file_get_contents(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'model.tmpl');

        return self::renderFileTemplate($content);
    }

    /*
     * Render view/view.php
     * 
     * @return string Rendered Content
     */

    public static function renderViewFile() {
        //read view template
        $content = file_get_contents(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'view.tmpl');

        return self::renderFileTemplate($content);
    }

    /*
     * Render view/css/admin-general.css
     * 
     * @return string Rendered Content
     */

    public static function renderCSSFile() {
        //read css template
        $content = file_get_contents(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'css.tmpl');

        return self::renderFileTemplate($content);
    }

    /*
     * Render view/js/admin-general.js
     * 
     * @return string Rendered Content
     */

    public static function renderJSFile() {
        //read js template
        $content = file_get_contents(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'js.tmpl');
        
        $content;

        return self::renderFileTemplate($content);
    }

}

?>