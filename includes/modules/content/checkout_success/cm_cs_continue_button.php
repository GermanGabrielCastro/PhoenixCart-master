<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  class cm_cs_continue_button extends abstract_executable_module {

    const CONFIG_KEY_BASE = 'MODULE_CONTENT_CS_CONTINUE_BUTTON_';

    public function __construct() {
      parent::__construct(__FILE__);
    }

    public function execute() {
      $content_width = (int)MODULE_CONTENT_CS_CONTINUE_BUTTON_CONTENT_WIDTH;

      $tpl_data = [ 'group' => $this->group, 'file' => __FILE__ ];
      include 'includes/modules/content/cm_template.php';
    }

    protected function get_parameters() {
      return [
        'MODULE_CONTENT_CS_CONTINUE_BUTTON_STATUS' => [
          'title' => 'Enable Continue Button',
          'value' => 'True',
          'desc' => 'Should this module be shown on the product info page?',
          'set_func' => "Config::select_one(['True', 'False'], ",
        ],
        'MODULE_CONTENT_CS_CONTINUE_BUTTON_CONTENT_WIDTH' => [
          'title' => 'Content Width',
          'value' => '12',
          'desc' => 'What width container should the content be shown in?',
          'set_func' => "Config::select_one(['12', '11', '10', '9', '8', '7', '6', '5', '4', '3', '2', '1'], ",
        ],
        'MODULE_CONTENT_CS_CONTINUE_BUTTON_SORT_ORDER' => [
          'title' => 'Sort Order',
          'value' => '5000',
          'desc' => 'Sort order of display. Lowest is displayed first.',
        ],
      ];
    }

  }
