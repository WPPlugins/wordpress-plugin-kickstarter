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

class Mvbapk_View_View {
    /*
     * Control Node
     * 
     * @var object <mpks_control_Control>
     * @access protected
     */

    protected $controlNode;

    /*
     * Model Node
     * 
     * @var object <mpks_model_Model>
     * @access protected
     */
    protected $modelNode;

    /*
     * Constructor
     */

    function __construct($control) {

        $this->controlNode = $control;
        //register necessary hooks
        add_action('admin_print_scripts', array($this, 'admin_print_scripts'));
        add_action('admin_print_styles', array($this, 'admin_print_styles'));
    }

    /*
     * Set Model Node
     * 
     * @param object Model Node
     */

    public function setModelNode($modelNode) {
        $this->modelNode = $modelNode;
    }

    /*
     * Get Main Manager Page
     * 
     * @param array List of errors if so
     */

    public function getManagerPage($errors = array()) {
        $html = phpQuery::newDocumentFileHTML(MVBAPK_HTML_DIR . 'admin/manager-page.html');

        if ($_POST['submited']) {
            if (count($errors)) {
                $html['#notice-message > div:first']->removeClass('ui-state-highlight');
                $html['#notice-message > div:first']->addClass('ui-state-error');
                $html['#notice-message #message']->html(implode('<br/>', $errors));
            }
        } else {
            $html['#notice-message']->addClass('dialog');
        }
        //add list of styles
        $styles = $this->modelNode->getStyleList();
        if (is_array($styles)) {
            foreach ($styles as $id => $style) {
                $id = esc_attr($id);
                $label = esc_js($style['label']);
                $html['#style']->append("<option value='{$id}'>{$label}</option>");
            }
            reset($styles);
            $first = current($styles);
            $html['#current-style-display'] = $first['label'];
        }

        echo $html->htmlOuter();
    }

    /*
     * Render proper js scripts
     */

    public function admin_print_scripts() {

        if ($this->loadJS_CSS()) {
            switch ($_GET['page']) {
                case MVBAPK_OPTION_PAGE:
                    wp_enqueue_script('postbox');
                    wp_enqueue_script('dashboard');
                    wp_enqueue_script('thickbox');
                    wp_enqueue_script('media-upload');
                    wp_enqueue_script(MVBAPK_PREFIX . 'ui', MVBAPK_JS_DIR . 'ui/jquery-ui.js', array('jquery'));
                    wp_enqueue_script(MVBAPK_PREFIX . 'admin-general', MVBAPK_JS_DIR . 'admin-general.js');
                    break;

                case MVBAPK_ABOUT_PAGE:
                    wp_enqueue_script('awm-group-js', 'http://whimba.org/public/awm-group/awm_about.js');
                    wp_enqueue_script('awm-group-ui', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/jquery-ui.min.js');
                    break;

                default:
                    break;
            }
        }
    }

    /*
     * Render proper css stylesheets
     */

    public function admin_print_styles() {

        if ($this->loadJS_CSS()) {
            switch ($_GET['page']) {
                case MVBAPK_OPTION_PAGE:
                    wp_enqueue_style('dashboard');
                    wp_enqueue_style('global');
                    wp_enqueue_style('wp-admin');
                    wp_enqueue_style(MVBAPK_PREFIX . 'ui', MVBAPK_CSS_DIR . 'ui/jquery-ui.css');
                    wp_enqueue_style(MVBAPK_PREFIX . 'admin-general', MVBAPK_CSS_DIR . 'admin-general.css');

                    break;

                case MVBAPK_ABOUT_PAGE:
                    wp_enqueue_style('awm-group-style', 'http://whimba.org/public/awm-group/awm_about.css');
                    wp_enqueue_style('awm-group-ui', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/smoothness/jquery.ui.all.css');
                    break;

                default:
                    break;
            }
        }
    }

    /**
     * Check if is necessary to load Additional JS script and CSS
     * 
     * @return bool TRUE if to load
     */
    protected function loadJS_CSS() {

        return (is_admin() ? TRUE : FALSE);
    }

}

?>
