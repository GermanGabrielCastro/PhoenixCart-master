<li class="nav-item dropdown nb-currencies">
  <a class="nav-link dropdown-toggle" href="#" id="navDropdownCurrencies" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    <?= sprintf(MODULE_NAVBAR_CURRENCIES_SELECTED_CURRENCY, $_SESSION['currency']) ?>
  </a>
  <div class="dropdown-menu<?= (('Right' === MODULE_NAVBAR_CURRENCIES_CONTENT_PLACEMENT) ? ' dropdown-menu-right' : '') ?>" aria-labelledby="navDropdownCurrencies">
    <?php
    foreach ($GLOBALS['currencies']->currencies as $key => $value) {
      echo '<a class="dropdown-item" href="'
      . $GLOBALS['Linker']->build()->retain_query_except(['language'])->set_parameter('currency', $key) . '">'
      . $value['title']
      . '</a>' . PHP_EOL;
    }
    ?>
  </div>
</li>

<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/
?>