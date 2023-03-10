<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  abstract class abstract_zoneable_module extends abstract_module {

    public function __construct() {
      parent::__construct();

      if ($this->enabled) {
        $this->update_status();
      }
    }

    abstract public function update_status();

    public function update_status_by($address) {
      if (!$this->enabled) {
        return;
      }

      $geo_zone_id = $this->base_constant('ZONE') ?? 0;
      if (0 >= (int)$geo_zone_id) {
        return;
      }

      if (!isset($address['zone_id'])) {
        $this->enabled = false;
        return;
      }

      if (isset($address['country']['id'])) {
        $check_query = $GLOBALS['db']->query("SELECT zone_id FROM zones_to_geo_zones WHERE geo_zone_id = " . (int)$geo_zone_id . " AND zone_country_id = " . (int)$address['country']['id'] . " ORDER BY zone_id");
        while ($check = $check_query->fetch_assoc()) {
          if (($check['zone_id'] < 1) || ($check['zone_id'] == $address['zone_id'])) {
            return;
          }
        }

        $this->enabled = false;
      }
    }

  }

