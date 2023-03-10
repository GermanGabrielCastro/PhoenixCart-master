<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  class database_order_builder {

    public static $column_keys = null;
    public static $attributes_sql = <<<'EOSQL'
SELECT
   products_options as `option`,
   products_options_values AS value,
   price_prefix AS prefix,
   options_values_price AS price
 FROM orders_products_attributes
 WHERE orders_id = %d AND orders_products_id = %d
EOSQL;

    protected $order;
    protected $data;
    protected $order_status;

    public function __construct(&$order) {
      if (is_null(static::$column_keys)) {
        static::$column_keys = [
          'qty' => 'products_quantity',
          'id' => 'products_id',
          'name' => 'products_name',
          'model' => 'products_model',
          'tax' => 'products_tax',
          'price' => 'products_price',
          'final_price' => 'final_price',
          'orders_products_id' => 'orders_products_id',
        ];

        $parameters = [
          'column_keys' => &static::$column_keys,
          'attributes_sql' => &static::$attributes_sql,
        ];
        $GLOBALS['all_hooks']->cat('databaseOrderProductColumns', $parameters);
      }

      $this->order =& $order;

      $order_query = $GLOBALS['db']->query("SELECT * FROM orders WHERE orders_id = " . (int)$this->order->get_id());
      $this->data = $order_query->fetch_assoc();
    }

    protected function extract_address($prefix) {
      $length = strlen($prefix);

      $address = [];
      foreach ($this->data as $k => $v) {
        if (substr($k, 0, $length) === $prefix) {
          $address[substr($k, $length)] = $v;
        }
      }
      $address['country'] = ['title' => $address['country']];
      $address['format_id'] = $address['address_format_id'];

      return $address;
    }

    public function build_info() {
      $order_status_query = $GLOBALS['db']->query(sprintf(
        "SELECT orders_status_name FROM orders_status WHERE orders_status_id = %d AND language_id = %d",
        (int)$this->data['orders_status'], (int)$_SESSION['languages_id']));
      $order_status = $order_status_query->fetch_assoc();

      $this->order->info = [
        'currency' => $this->data['currency'],
        'currency_value' => $this->data['currency_value'],
        'payment_method' => $this->data['payment_method'],
        'date_purchased' => $this->data['date_purchased'],
        'orders_status' => $order_status['orders_status_name'],
        'orders_status_id' => $this->data['orders_status'],
        'last_modified' => $this->data['last_modified'],
      ];
    }

    public function build_addresses() {
      $this->order->customer = $this->extract_address('customers_');

      $this->order->delivery = $this->extract_address('delivery_');
      if (('' === trim($this->order->delivery['name'])) && empty($this->order->delivery['street_address'])) {
        $this->order->delivery = false;
      }

      $this->order->billing = $this->extract_address('billing_');
    }

    public function build_totals() {
      $totals_query = $GLOBALS['db']->query("SELECT title, text, class FROM orders_total WHERE orders_id = " . (int)$this->order->get_id() . " ORDER BY sort_order");
      while ($total = $totals_query->fetch_assoc()) {
        $this->order->totals[] =  [
          'title' => $total['title'],
          'text' => $total['text'],
        ];

        switch ($total['class']) {
          case 'ot_total':
            $this->order->info['total'] = strip_tags($total['text']);
            break;
          case 'ot_shipping':
            $this->order->info['shipping_method'] = trim(rtrim(strip_tags($total['title']), ': '));
            break;
        }
      }
    }

    public function build_products() {
      $order_products_query = $GLOBALS['db']->query("SELECT * FROM orders_products WHERE orders_id = " . (int)$this->order->get_id());
      while ($order_product = $order_products_query->fetch_assoc()) {
        $current = [];
        foreach (static::$column_keys as $order_key => $database_key) {
          $current[$order_key] = $order_product[$database_key];
        }

        $attributes_query = $GLOBALS['db']->query(sprintf(
          static::$attributes_sql,
          (int)$this->order->get_id(),
          (int)$order_product['orders_products_id']));
        if (mysqli_num_rows($attributes_query)) {
          $current['attributes'] = [];
          while ($attribute = $attributes_query->fetch_assoc()) {
            $current['attributes'][] = $attribute;
          }
        }

        $this->order->products[] = $current;
        $this->order->info['tax_groups']["{$current['tax']}"] = '1';
      }
    }

    public function get($key) {
      return $this->data[$key] ?? null;
    }

    public static function build(&$order) {
      $builder = new database_order_builder($order);

      $builder->build_info();
      $builder->build_addresses();
      $builder->build_totals();
      $builder->build_products();

      $parameters = [
        'builder' => $builder,
        'order' => &$order,
      ];
      $GLOBALS['all_hooks']->cat('databaseOrderBuild', $parameters);
    }

  }
