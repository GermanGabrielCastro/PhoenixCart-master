<?php
/*
  $Id$

  CE Phoenix, E-Commerce made Easy
  https://phoenixcart.org

  Copyright (c) 2021 Phoenix Cart

  Released under the GNU General Public License
*/

  function tep_find_all_files_under($directory, &$files) {
    trigger_error('The tep_find_all_files_under function has been deprecated.', E_USER_DEPRECATED);
    foreach (scandir($directory, SCANDIR_SORT_ASCENDING) as $entry) {
      // we have no file or directory names starting with a dot
      // so it's safe to screen out anything that does, like the current and parent directories
      if ('.' === $entry[0]) {
        continue;
      }

      $path = "$directory/$entry";
      if (is_file($path)) {
        $files[pathinfo($entry, PATHINFO_FILENAME)] = $path;
      } elseif (is_dir($path) && 'templates' !== $entry) {
        // templates directories are underneath the modules directory but do not contain classes
        tep_find_all_files_under($path, $files);
      }
    }
  }

  function tep_normalize_class_name($original_class) {
    trigger_error('The tep_normalize_class_name function has been deprecated.', E_USER_DEPRECATED);
    $class_position = strrpos($original_class, "\\");
    if (is_int($class_position)) {
      $original_class = substr($original_class, $class_position + 1);
    }

    return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $original_class));
  }

  function tep_calculate_hook_name($directory, $path) {
    trigger_error('The tep_calculate_hook_name function has been deprecated.', E_USER_DEPRECATED);
    $exclude_length = strlen($directory);

    list ($site, $group, $basename) = explode('/', substr($path, $exclude_length));
    return tep_normalize_class_name(
      'hook_' . $site . '_' . $group . '_' . pathinfo($basename, PATHINFO_FILENAME));
  }

  function tep_find_all_hooks_under($directory, &$files) {
    trigger_error('The tep_find_all_hooks_under function has been deprecated.', E_USER_DEPRECATED);
    foreach (scandir($directory, SCANDIR_SORT_ASCENDING) as $site) {
      // we have no file or directory names starting with a dot
      // so it's safe to screen out anything that does, like the current and parent directories
      if ('.' === $site[0] || !is_dir($site_path = "$directory$site")) {
        continue;
      }

      foreach (scandir($site_path, SCANDIR_SORT_ASCENDING) as $group) {
        if ('.' === $group[0] || !is_dir($group_path = "$site_path/$group")) {
          continue;
        }

        foreach (scandir($group_path, SCANDIR_SORT_ASCENDING) as $file) {
          if (is_file($path = "$group_path/$file")) {
            $files[tep_calculate_hook_name($directory, $path)] = $path;
          }
        }
      }
    }
  }

  function tep_find_all_templates_under($directory, &$files) {
    trigger_error('The tep_find_all_templates_under function has been deprecated.', E_USER_DEPRECATED);
    foreach (scandir($directory, SCANDIR_SORT_ASCENDING) as $template) {
      if ('.' !== $template[0]) {
        $files[$template . '_template'] = "$directory/$template/includes/template.php";
      }
    }
  }

  function tep_find_all_actions_under($directory, &$files) {
    trigger_error('The tep_find_all_actions_under function has been deprecated.', E_USER_DEPRECATED);
    foreach (scandir($directory, SCANDIR_SORT_ASCENDING) as $file) {
      if (is_file($path = "$directory/$file")) {
        $files[tep_normalize_class_name(pathinfo($file, PATHINFO_FILENAME))] = $path;
      }
    }
  }

  function tep_build_catalog_autoload_index() {
    trigger_error('The tep_build_catalog_autoload_index function has been deprecated.', E_USER_DEPRECATED);
    $class_files = [];

    tep_find_all_hooks_under(DIR_FS_CATALOG . 'includes/hooks/', $class_files);
    tep_find_all_templates_under(DIR_FS_CATALOG . 'templates', $class_files);

    tep_find_all_files_under(DIR_FS_CATALOG . 'includes/modules', $class_files);
    tep_find_all_files_under(DIR_FS_CATALOG . 'includes/classes', $class_files);
    tep_find_all_actions_under(DIR_FS_CATALOG . 'includes/actions', $class_files);
    tep_find_all_files_under(DIR_FS_CATALOG . 'includes/system/versioned', $class_files);

    $overrides_directory = DIR_FS_CATALOG . 'includes/system/override';
    if (is_dir($overrides_directory)) {
      tep_find_all_files_under($overrides_directory, $class_files);
    }

    return $class_files;
  }

  function tep_autoload_catalog($original_class) {
    trigger_error('The tep_autoload_catalog function has been deprecated.', E_USER_DEPRECATED);
    static $class_files;
    static $modules_directory_length;

    if (!isset($class_files)) {
      $modules_directory_length = strlen(DIR_FS_CATALOG . 'includes/modules');
      $class_files = tep_build_catalog_autoload_index();
    }

    // convert camelCase class names to snake_case filenames
    $class = tep_normalize_class_name($original_class);

    if (isset($class_files[$class])) {
      if (isset($_SESSION['language']) && DIR_FS_CATALOG . 'includes/modules' === substr($class_files[$class], 0, $modules_directory_length)) {
        $language_file = language::map_to_translation('modules' . substr($class_files[$class], $modules_directory_length));
        if (file_exists($language_file)) {
          include $language_file;
        }
      }

      require $class_files[$class];
    }
  }

