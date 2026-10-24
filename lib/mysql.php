<?php

/*
 * MySQL access through mysqli (ext/mysql is gone since PHP 7). The db_*
 * functions return what the ext/mysql functions they replaced did, e.g. false
 * rather than null. Must stay PHP 5.5 compatible until the server is upgraded.
 */

function db_link() {
  static $link = null;
  if ($link === null) {
    // Since PHP 8.1 mysqli throws on every error by default; this code checks return values
    mysqli_report(MYSQLI_REPORT_OFF);
    $link = mysqli_connect("p:localhost", DB_USER, DB_PASS, DB_NAME)
      or die("COULD NOT CONNECT: ".mysqli_connect_error());
    mysqli_query($link, 'SET NAMES utf8');
  }
  return $link;
}

function db_qw() {
  $args = func_get_args();
  $query = call_user_func_array("db_make_qw", $args);
  return mysqli_query(db_link(), $query);
}

function db_make_qw() {
  $args = func_get_args();
  $tmpl =& $args[0];
  $tmpl = str_replace("%", "%%", $tmpl);
  $tmpl = str_replace("?", "%s", $tmpl);
  foreach ($args as $i => $v) {
    if (!$i) continue;
    if(is_int($v)) continue;
    $args[$i] = "'".db_escape($v)."'";
  }
  for ($i = $c = count($args) - 1; $i < $c + 20; $i++)
    $args[$i + 1] = "UNKNOWN_PLACEHOLDER_$i";
  return call_user_func_array("sprintf", $args);
}

function db_fetch_assoc($result) {
  if (!$result) return false;
  $row = mysqli_fetch_assoc($result);
  return $row === null ? false : $row;
}

// Replaces mysql_result(): one value by row number and column number or name
function db_result($result, $row = 0, $field = 0) {
  if (!$result || $row >= mysqli_num_rows($result) || !mysqli_data_seek($result, $row)) return false;
  $data = mysqli_fetch_array($result, MYSQLI_BOTH);
  return ($data && array_key_exists($field, $data)) ? $data[$field] : false;
}

function db_insert_id() {
  return mysqli_insert_id(db_link());
}

function db_escape($string) {
  return mysqli_real_escape_string(db_link(), (string) $string);
}

db_link();
?>
