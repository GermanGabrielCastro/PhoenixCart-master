<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  class bm_shopping_cart extends abstract_block_module {

    const CONFIG_KEY_BASE = 'MODULE_BOXES_SHOPPING_CART_';

    function execute() {
      global $currencies;

      $cart_totalised = sprintf(MODULE_BOXES_SHOPPING_CART_BOX_CART_TOTAL, $currencies->format($_SESSION['cart']->show_total()));

      $tpl_data = ['group' => $this->group, 'file' => __FILE__];
      include 'includes/modules/block_template.php';
    }

    protected function get_parameters() {
      return [
        'MODULE_BOXES_SHOPPING_CART_STATUS' => [
          'title' => 'Enable Shopping Cart Module',
          'value' => 'True',
          'desc' => 'Do you want to add the module to your shop?',
          'set_func' => "Config::select_one(['True', 'False'], ",
        ],
        'MODULE_BOXES_SHOPPING_CART_CONTENT_PLACEMENT' => [
          'title' => 'Content Placement',
          'value' => 'Right Column',
          'desc' => 'Should the module be loaded in the left or right column?',
          'set_func' => "Config::select_one(['Left Column', 'Right Column'], ",
        ],
        'MODULE_BOXES_SHOPPING_CART_SORT_ORDER' => [
          'title' => 'Sort Order',
          'value' => '0',
          'desc' => 'Sort order of display. Lowest is displayed first.',
        ],
      ];
    }

  }
