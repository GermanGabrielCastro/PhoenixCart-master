<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  class cm_i_customer_greeting extends abstract_executable_module {

    const CONFIG_KEY_BASE = 'MODULE_CONTENT_CUSTOMER_GREETING_';

    function __construct() {
      parent::__construct(__FILE__);
    }

    public function execute() {
      global $customer, $Linker;

      if (isset($customer) && ($customer instanceof customer)) {
        $customer_greeting = sprintf(MODULE_CONTENT_CUSTOMER_GREETING_PERSONAL, htmlspecialchars($customer->get('short_name')), $Linker->build('products_new.php'));
      } else {
        $customer_greeting = sprintf(MODULE_CONTENT_CUSTOMER_GREETING_GUEST, $Linker->build('login.php'), $Linker->build('create_account.php'));
      }

      $tpl_data = [ 'group' => $this->group, 'file' => __FILE__ ];
      include 'includes/modules/content/cm_template.php';
    }

    public function get_parameters() {
      return [
        'MODULE_CONTENT_CUSTOMER_GREETING_STATUS' => [
          'title' => 'Enable Customer Greeting Module',
          'value' => 'True',
          'desc' => 'Do you want to enable this module?',
          'set_func' => "Config::select_one(['True', 'False'], ",
        ],
        'MODULE_CONTENT_CUSTOMER_GREETING_CONTENT_WIDTH' => [
          'title' => 'Content Width',
          'value' => '12',
          'desc' => 'What width container should the content be shown in? (12 = full width, 6 = half width).',
          'set_func' => "Config::select_one(['12', '11', '10', '9', '8', '7', '6', '5', '4', '3', '2', '1'], ",
        ],
        'MODULE_CONTENT_CUSTOMER_GREETING_SORT_ORDER' => [
          'title' => 'Sort Order',
          'value' => '100',
          'desc' => 'Sort order of display. Lowest is displayed first.',
        ],
      ];
    }

  }

