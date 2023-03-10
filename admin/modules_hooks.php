<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  require 'includes/application_top.php';

  $hooks = new hooks('shop');
  $template_name = defined('TEMPLATE_SELECTION') ? TEMPLATE_SELECTION : 'default';
  $template_name .= '_template';
  $template = new $template_name();
  $directories = $hooks->get_hook_directories();

  function phoenix_find_contents($base, $test) {
    $contents = [];
    if (is_dir($base) && ($handle = @dir($base))) {
      while ($file = $handle->read()) {
        if (('.' !== $file[0]) && $test("$base/$file")) {
          $contents[] = $file;
        }
      }

      $handle->close();
    }

    return $contents;
  }

  function phoenix_find_listeners($class) {
    $listeners = [];

    if (class_exists($class)) {
      $prefix = 'listen_';
      $length = strlen($prefix);
      foreach (get_class_methods($class) as $method) {
        if (substr($method, 0, $length) === $prefix) {
          $listeners[] = substr($method, $length);
        }
      }
    }

    return $listeners;
  }

  $contents = [];
  foreach ($directories as $directory) {
    $directory = dirname($directory);
    foreach (phoenix_find_contents($directory, 'is_dir') as $site) {
      foreach (phoenix_find_contents("$directory/$site", 'is_dir') as $group) {
        foreach (phoenix_find_contents("$directory/$site/$group", 'is_file') as $file) {
          $pathinfo = pathinfo("$directory/$site/$group/$file");
          if ('php' !== ($pathinfo['extension'] ?? null)) {
            continue;
          }

          $class = "hook_{$site}_{$group}_{$pathinfo['filename']}";
          foreach (phoenix_find_listeners($class) as $listener) {
            Guarantor::guarantee_all(
              $contents,
              $site,
              $group,
              $listener,
              $pathinfo['filename']
            )[] = $directory;
          }
        }
      }
    }
  }

  $hooks_query = $db->query(sprintf(<<<'EOSQL'
SELECT hooks_site, hooks_group, hooks_action, hooks_code, hooks_class, hooks_method
 FROM hooks
EOSQL
    , $db->escape(Text::input($file))));
  while ($hook = $hooks_query->fetch_assoc()) {
    $callable = [];
    if (!empty($hook['hooks_class'])) {
      $callable[] = $hook['hooks_class'];
    }

    if (!empty($hook['hooks_method'])) {
      $callable[] = $hook['hooks_method'];
    }

    Guarantor::guarantee_all(
      $contents,
      $hook['hooks_site'],
      $hook['hooks_group'],
      $hook['hooks_action'],
      $hook['hooks_code']
    )[] = $callable;
  }

  require 'includes/template_top.php';
?>

  <h1 class="display-4 mb-2"><?= HEADING_TITLE ?></h1>

  <div class="table-responsive">
    <table class="table table-striped table-hover">
      <?php
  foreach ( $contents as $site => $groups ) {
?>
      <thead class="thead-dark">
        <tr>
          <th colspan="4"><?php printf(TABLE_HEADING_LOCATION, $site); ?></th>
        </tr>
      </thead>
      <thead class="thead-light">
        <tr>
          <th><?= TABLE_HEADING_GROUP ?></th>
          <th><?= TABLE_HEADING_FILE ?></th>
          <th><?= TABLE_HEADING_METHOD ?></th>
          <th class="text-right"><?= TABLE_HEADING_VERSION ?></th>
        </tr>
      </thead>
      <tbody>
        <?php
    foreach ( $groups as $group => $actions ) {
      foreach ( $actions as $action => $codes ) {
        foreach ( $codes as $code => $locations) {
          foreach ($locations as $location) {
            if (is_array($location)) {
              $file = implode('->', $location);
              $class = explode('::', $location[0])[0];
              $version = class_exists($class) ? (get_class_vars($class)['version'] ?? null) : null;
            } else {
              $file = "$code.php";
              $version = get_class_vars("hook_{$site}_{$group}_{$code}")['version'] ?? null;
            }
?>
        <tr>
          <td><?= $group ?></td>
          <td><?= $file ?></td>
          <td><?= $action ?></td>
          <td class="text-right"><?= $version ?? 'N/A' ?></td>
        </tr>
        <?php
          }
        }
      }
    }
  }
?>
      </tbody>
    </table>
  </div>

  <hr>

  <p><?= TEXT_HOOKS_DIRECTORY . ' ' . DIR_FS_CATALOG . 'includes/hooks/' ?></p>

<?php
  require 'includes/template_bottom.php';
  require 'includes/application_bottom.php';
?>
