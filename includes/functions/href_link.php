<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  function tep_href_link($page = '', $parameters = '', $connection = null, $add_session_id = true, $search_engine_safe = null) {
    trigger_error('The tep_href_link function has been deprecated.', E_USER_DEPRECATED);
    return Guarantor::ensure_global('Linker')->build($page, phoenix_parameterize($parameters), $add_session_id);
  }
