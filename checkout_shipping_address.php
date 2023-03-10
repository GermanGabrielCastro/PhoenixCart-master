<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  require 'includes/application_top.php';

  require 'includes/system/segments/checkout/pipeline.php';

// needs to be included earlier to set the success message in the messageStack
  require language::map_to_translation('checkout_shipping_address.php');

  $message_stack_area = 'checkout_address';

  $error = false;
  $process = false;
  if (Form::validate_action_is('submit')) {
// process a new shipping address
    $customer_details = $customer_data->process($customer_data->get_fields_for_page('address_book'));
    $hooks->cat('injectFormVerify');
    if (Form::is_valid()) {
      $customer_details['id'] = $customer->get_id();
      $customer_data->add_address($customer_details);

      $_SESSION['sendto'] = $customer_data->get('address_book_id', $customer_details);

      unset($_SESSION['shipping']);

      Href::redirect($Linker->build('checkout_shipping.php'));
    }
  } elseif (isset($_POST['address']) && Form::validate_action_is('select')) {
// change to the selected shipping destination
    $reset_shipping = isset($_SESSION['sendto'], $_SESSION['shipping']) && ($_SESSION['sendto'] != $_POST['address']);
    $_SESSION['sendto'] = $_POST['address'];

    if ($customer->fetch_to_address((int)$_SESSION['sendto'])) {
      if ($reset_shipping) {
        unset($_SESSION['shipping']);
      }

      Href::redirect($Linker->build('checkout_shipping.php'));
    } else {
      unset($_SESSION['sendto']);
    }
  }

// if no shipping destination address was selected, use their own address as default
  if (!isset($_SESSION['sendto'])) {
    $_SESSION['sendto'] = $customer->get('default_sendto');
    $sendto =& $_SESSION['sendto'];
  }

  $addresses_count = $customer->count_addresses();

  require $Template->map(__FILE__, 'page');

  require 'includes/application_bottom.php';
