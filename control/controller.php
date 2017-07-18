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

class Mvbapk_Control_Controller {

    protected $modelNode;

    protected $viewNode;

    function __construct() {

        //create Model node
        $this->modelNode = new Mvbapk_Model_Model($this);
        //create View node
        $this->viewNode = new Mvbapk_View_View($this);
        //notify nodes about each other
        $this->modelNode->setViewNode($this->viewNode);
        $this->viewNode->setModelNode($this->modelNode);

        //register ajax holder
        add_action('wp_ajax_mvbapk', array($this, 'ajax'));
    }

    public function getViewNode() {

        return $this->viewNode;
    }

    public function ajax() {

        $action = $_POST['sub_action'];

        switch ($action) {
            case 'check_name':
                $c = $this->modelNode->checkName($_POST['name']);
                switch ($c) {
                    case MVBAPK_WARNING:
                        $m = __('Plugin with a name <b>' . $_POST['name'] . '</b> already exists. Are you sure you want to rewrite it?');
                        break;

                    case MVBAPK_ERROR:
                        $m = __('Rewriting the plugin "' . $_POST['name'] . '" denied. Please create empty file ALLOW_REWRITING and put it into this plugin\'s root directory');
                        break;
                }
                $result = array(
                    'status' => 'success',
                    'result' => $c,
                    'message' => $m
                );
                break;

            default:
                $result = '';
                break;
        }
        
        die(json_encode($result));
    }
}

?>