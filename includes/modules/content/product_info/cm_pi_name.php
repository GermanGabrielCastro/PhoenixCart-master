<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  class cm_pi_name extends abstract_executable_module {

    const CONFIG_KEY_BASE = 'MODULE_CONTENT_PI_NAME_';

    public function __construct() {
      parent::__construct(__FILE__);
    }

    public function execute() {
      $tpl_data = [ 'group' => $this->group, 'file' => __FILE__ ];
      include 'includes/modules/content/cm_template.php';
    }

    protected function get_parameters() {
      return [
        'MODULE_CONTENT_PI_NAME_STATUS' => [
          'title' => 'Enable Name Module',
          'value' => 'True',
          'desc' => 'Should this module be shown on the product info page?',
          'set_func' => "Config::select_one(['True', 'False'], ",
        ],
        'MODULE_CONTENT_PI_NAME_CONTENT_WIDTH' => [
          'title' => 'Content Width',
          'value' => '9',
          'desc' => 'What width container should the content be shown in?',
          'set_func' => "Config::select_one(['12', '11', '10', '9', '8', '7', '6', '5', '4', '3', '2', '1'], ",
        ],
        'MODULE_CONTENT_PI_NAME_SORT_ORDER' => [
          'title' => 'Sort Order',
          'value' => '40',
          'desc' => 'Sort order of display. Lowest is displayed first.',
        ],
      ];
    }

  }
