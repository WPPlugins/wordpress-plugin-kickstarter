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

class Mvbapk_Model_Model extends Mvbapk_Model_Pattern {

    protected $viewNode;
    protected $controlNode;
    private $plugin_name;
    protected $post;
    private $errors = array();

    function __construct($control) {

        $this->controlNode = $control;
        //configure environment
        add_action('admin_menu', array($this, 'admin_menu'), 999);
    }

    /*
     * Set View Node
     * 
     * @param object View Node
     */

    public function setViewNode($viewNode) {
        $this->viewNode = $viewNode;
    }

    /*
     * Render additional submenu
     * 
     * Render Kickstarter submenu for Tools
     */

    public function admin_menu() {
        global $submenu, $menu;

        if (!isset($submenu['awm-group'])) {
            add_menu_page(__('AWM Group', 'aam'), __('AWM Group', 'aam'), 'administrator', 'awm-group', array($this, 'awm_group'), MVBAPK_CSS_DIR . 'images/active-menu.png');
        }

        add_submenu_page('awm-group', __('Plugin Kickstarter', 'apk'), __('Plugin Kickstarter', 'apk'), 'administrator', MVBAPK_OPTION_PAGE, array($this, 'manager_page'));
    }

    public function awm_group() {

        $m = new Mvbapk_Model_About();
        $m->manage();
    }

    /*
     * Render Kickstarte main page
     * 
     */

    public function manager_page() {

        $this->viewNode = $this->controlNode->getViewNode();

        if ($_POST['submited']) { // lets go and create a new plugin :)
            $this->post = $this->getPOST();

            $pname = WP_PLUGIN_DIR . '/' . $this->getPluginFolderName($this->post['name']);

            //check if dir alread exist
            $allowed = TRUE;
            if (file_exists($pname)) {
                if (!file_exists($pname . '/__ALLOW_REWRITING')) {
                    $allowed = FALSE;
                }
            }

            if ($allowed) {
                //create main dir
                if (is_dir($pname) || mkdir($pname)) {
                    $this->renderPlugin($pname);
                } else {
                    $this->errors[] = $this->renderFileError($pname);
                }
            } else {
                $this->errors[] = __('Does not allowed to rewrite a plugin');
            }
        }

        $this->viewNode->getManagerPage($this->errors);
    }

    /*
     * Render an filesystem error
     * 
     * Prepare and render the error that can't create or write a file or folder
     * 
     * @param string File or Folder name
     * @return string Prepared error
     */

    protected function renderFileError($fname) {

        $error = sprintf('Filesystem error: %s', $fname);

        return $error;
    }

    /*
     * Render Plugin files and folders
     * 
     * @param string Path to Plugin dir
     * @return bool TRUE if plugin created successfully
     */

    protected function renderPlugin($dir) {

        //get proper style
        $style = $this->post['style'];
        $styles = $this->getStyleList();

        //parse style and start rendering
        if (file_exists($styles[$style]['config'])) {
            require_once($styles[$style]['pattern']);
            $reader = simplexml_load_file($styles[$style]['config']);
            if ($reader) {
                $tree = $reader->xpath('/style/plugin_tree');
                if (is_object($tree[0])) {
                    $this->parseTree($dir, $tree[0]);
                }
            } else {
                $this->errors[] = __('Kickstart style was not found');
            }
        } else {
            $this->errors[] = __('Kickstart style was not found');
        }
    }

    /*
     * Parse Plugin Tree and create proper files and folders
     * 
     * @param string Path to working dir
     * @param object <SimpleXMLElement>
     * @param int level
     * @return bool TRUE if process finished successfully
     */

    protected function parseTree($dir, $tree, $level = 0) {

        if ($tree->count()) { //this element has childer
            foreach ($tree->children() as $child) {
                $node = $child->getName();
                switch ($node) {
                    case 'file': //create a file
                        $attr = array();
                        foreach ($child->attributes() as $a => $b) {
                            $attr[$a] = $b;
                        }
                        $content = $this->renderContent($attr['content']);
                        if ($attr['name']) {
                            $fname = $this->parseFileName($attr['name']);
                            if (file_put_contents($dir . '/' . $fname, $content) === FALSE) {
                                $this->errors[] = $this->renderFileError($dir . '/' . $fname);
                            }
                        }
                        break;

                    case 'copy':
                        $attr = array();
                        foreach ($child->attributes() as $a => $b) {
                            $attr[$a] = $b;
                        }
                        if (trim($attr['file'])) {
                            $filename = basename($attr['file']);
                            //check if file all exists, just be sure :)
                            if (($filename == 'all') && !file_exists(ABSPATH . $attr['file']) || is_dir($dir . '/' . $filename)) {
                                $this->full_copy(dirname(ABSPATH . $attr['file']), $dir);
                            } else {
                                if (!copy(ABSPATH . $attr['file'], $dir)) {
                                    $this->errors[] = $this->renderFileError(ABSPATH . $attr['file']);
                                }
                            }
                        }
                        break;

                    default: //create a dir
                        $node = $dir . '/' . $node;
                        if ((is_dir($node) || mkdir($node) ) && $child->count()) {
                            $this->parseTree($node, $child, $level + 1);
                        }
                        break;
                }
            }
        }
    }

    /*
     * Copy all files and folder
     * 
     * @param string
     * @param string
     */

    function full_copy($source, $target) {
        if (is_dir($source)) {
            @mkdir($target);
            $d = dir($source);
            while (FALSE !== ( $entry = $d->read() )) {
                if ($entry == '.' || $entry == '..') {
                    continue;
                }
                $Entry = $source . '/' . $entry;
                if (is_dir($Entry)) {
                    $this->full_copy($Entry, $target . '/' . $entry);
                    continue;
                }
                copy($Entry, $target . '/' . $entry);
            }

            $d->close();
        } else {
            copy($source, $target);
        }
    }

    /*
     * Parse file name and replace markers
     * 
     * Get file name form XML and replace pre-defined marker in file name. Allowed
     * next markers:
     *  - {$prefix} This is Plugin Prefix
     *  - {$plugin_name} Plugin name
     * 
     * @param string File name taken from XML style
     * @return string Parsed file name
     */

    protected function parseFileName($filename) {

        $filename = str_replace(array('{$prefix}', '{$plugin_name}'), array($this->post['prefix'], $this->plugin_name), $filename);
        $filename = apply_filters(MVBAPK_PREFIX . 'kickstarter_filename', $filename);

        return $filename;
    }

    /*
     * Render Content for a file
     * 
     * @param string function for content rendering
     * @param string Rendered Content
     */

    protected function renderContent($callback) {

        $parts = preg_split('/\->/', $callback);
        $count = count($parts);
        if ($count == 1) { //this is just a function
            if (function_exists($parts[0])) {
                $content = $parts[0]();
            }
        } elseif ($count == 2) { //this is an object
            if (method_exists($parts[0], $parts[1]) || method_exists($parts[0], '__callStatic')) {
                $content = call_user_func(array($parts[0], $parts[1]));
            }
        }

        return $content;
    }

    /*
     * Prepare Plugin name
     * 
     * @param string Plugin name
     * @return string Prepared plugin folder name
     */

    protected function getPluginFolderName($name) {

        $name = $this->convertPluginName($name);
        $this->plugin_name = $name;

        return $name;
    }

    /*
     * Check plugin name
     * 
     * Check if plugin with pointed name already exist and if exists then check
     * if file ALLOW_REWRITING placed.
     * The result of execution can be the next:
     * 0 - Doesn't exist;
     * 1 - Exist and rewriting allowed;
     * 2 - Exist and rewriting restricted
     * 
     * @param string Plugin name
     * @return int Result value.
     */

    public function checkName($name) {

        $path = WP_PLUGIN_DIR . '/' . $this->getPluginFolderName($name);

        if (file_exists($path)) {
            if (file_exists($path . '/__ALLOW_REWRITING')) {
                $result = MVBAPK_WARNING;
            } else {
                $result = MVBAPK_ERROR;
            }
        } else {
            $result = MVBAPK_NO_ERRORS;
        }

        return $result;
    }

}

?>