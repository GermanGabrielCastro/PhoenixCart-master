<?php
/*
  $Id$

  Copyright (c) 2020:
    Dan Cole - @Dan Cole
    James Keebaugh - @kymation
    Lambros - @Tsimi
    Rainer Schmied - @raiwa

  All rights reserved.

  Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:

  1. Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.

  2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.

  3. Neither the name of the copyright holder nor the names of its contributors may be used to endorse or promote products derived from this software without specific prior written permission.

  THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/

  class cm_sc_product_listing extends abstract_executable_module {

    const CONFIG_KEY_BASE = 'MODULE_CONTENT_SC_PRODUCT_LISTING_';

    public function __construct() {
      parent::__construct(__FILE__);
    }

    public function execute() {
      if ($_SESSION['cart']->count_contents() > 0) {
        $content_width = (int)MODULE_CONTENT_SC_PRODUCT_LISTING_CONTENT_WIDTH;

        $GLOBALS['any_out_of_stock'] = false;
        $products = $_SESSION['cart']->get_products();
        $form = new Form('cart_quantity', $GLOBALS['Linker']->build('shopping_cart.php', ['action' => 'update_product']));

        foreach ($products as $product) {
          // Push all attributes information in an array
          foreach (($product->get('attribute_selections') ?? []) as $option => $value) {
            $form->hide('id[' . $product->get('uprid') . '][' . $option . ']', $value);
          }
        }

        $tpl_data = [ 'group' => $this->group, 'file' => __FILE__ ];
        include 'includes/modules/content/cm_template.php';
      }
    }

    protected function get_parameters() {
      return [
        'MODULE_CONTENT_SC_PRODUCT_LISTING_STATUS' => [
          'title' => 'Enable Shopping Cart Product Listing',
          'value' => 'True',
          'desc' => 'Do you want to add the module to your shop?',
          'set_func' => "Config::select_one(['True', 'False'], ",
        ],
        'MODULE_CONTENT_SC_PRODUCT_LISTING_CONTENT_WIDTH' => [
          'title' => 'Content Width',
          'value' => '12',
          'desc' => 'What width container should the content be shown in?',
          'set_func' => "Config::select_one(['12', '11', '10', '9', '8', '7', '6', '5', '4', '3', '2', '1'], ",
        ],
        'MODULE_CONTENT_SC_PRODUCT_LISTING_SORT_ORDER' => [
          'title' => 'Sort Order',
          'value' => '120',
          'desc' => 'Sort order of display. Lowest is displayed first.',
        ],
      ];
    }

  }
