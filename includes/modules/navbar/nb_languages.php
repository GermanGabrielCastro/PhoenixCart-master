<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  class nb_languages extends abstract_block_module {

    const CONFIG_KEY_BASE = 'MODULE_NAVBAR_LANGUAGES_';

    public $group = 'navbar_modules_right';

    function getOutput() {
      if (substr(basename(Request::get_page()), 0, strlen('checkout')) !== 'checkout') {
        if (($GLOBALS['lng'] ?? null) instanceof language) {
          $lng =& $GLOBALS['lng'];
        } else {
          $lng = new language();
        }

        $tpl_data = ['group' => $this->group, 'file' => __FILE__];
        include 'includes/modules/block_template.php';
      }
    }

    protected function get_parameters() {
      return [
        'MODULE_NAVBAR_LANGUAGES_STATUS' => [
          'title' => 'Enable Languages Module',
          'value' => 'True',
          'desc' => 'Do you want to add the module to your Navbar?',
          'set_func' => "Config::select_one(['True', 'False'], ",
        ],
        'MODULE_NAVBAR_LANGUAGES_CONTENT_PLACEMENT' => [
          'title' => 'Content Placement',
          'value' => 'Right',
          'desc' => 'Should the module be loaded in the Left or Right or the Home area of the Navbar?',
          'set_func' => "Config::select_one(['Home', 'Left', 'Center', 'Right'], ",
        ],
        'MODULE_NAVBAR_LANGUAGES_SORT_ORDER' => [
          'title' => 'Sort Order',
          'value' => '535',
          'desc' => 'Sort order of display. Lowest is displayed first.',
        ],
      ];
    }

  }
