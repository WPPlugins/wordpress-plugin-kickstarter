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

function mvbapk_init() {
    static $mainObj;

    $mainObj = new Mvbapk_Kickstarter();
}

function mvbapk_autoload($class_name) {

    $parts = preg_split('/_/', $class_name);
    if (array_shift($parts) . '_' == MVBAPK_PREFIX) {
        $path = MVBAPK_BASE_DIR . strtolower(implode(DIRECTORY_SEPARATOR, $parts) . '.php');
        if (file_exists($path)) {
            require($path);
        }
    }
}

?>