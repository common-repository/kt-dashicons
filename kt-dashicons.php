<?php

/*
 * Plugin Name: Dashicons Viewer
 * Description: View all Dashicons on one page
 * Version: 1.0
 * Author: Gáravo
 * Author URI: http://profiles.wordpress.org/kungtiger
 * License: GPL2
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: kt-dashicons-viewer
 */

define('KT_DASHICONS_VIEWER_CAP', 'read');

function kt_dashicons_menu() {
    $title = __('Dashicons', 'kt-dashicons-viewer');
    $cap = apply_filters('kt_dashicons_viewer_cap', KT_DASHICONS_VIEWER_CAP);
    add_management_page($title, $title, $cap, 'kt_dashicons_viewer', 'kt_dashicons_viewer');
}

function kt_dashicons_viewer() {
    $print = array();
    $css = file(ABSPATH . WPINC . '/css/dashicons.css');
    $m = array('', '');
    $n = $_n = $i = 0;
    foreach ($css as $line) {
        if (preg_match('~^/\*([^\*]+?)\*/$~', $line, $m)) {
            if ($_n) {
                $print[$i] .= " [$_n]";
                $i += 2;
                $_n = 0;
            }
            $print[$i] = $m[1];
            $print[$i + 1] = '';
        } else if (preg_match('~\.(dashicons-[^:]+):before~', $line, $m) && $m[1] != 'dashicons-before') {
            $n++;
            $_n++;
            $print[$i + 1] .= "
  <div alt='%1\$s' class='dashicons-before $m[1]' title='$m[1]'></div>";
        } else if (preg_match('~content:\s*["\']\\\\([a-f0-9]{4})["\']~', $line, $m)) {
            $print[$i + 1] = sprintf($print[$i + 1], $m[1]);
        }
    }
    if ($_n && $print[0]) {
        $print[$i] .= " [$_n]";
    }
    $title = __('Dashicons', 'kt-dashicons-viewer');
    print "
<style type='text/css'>
#kt_dashicons_viewer .dashicons-before,
#kt_dashicons_viewer h4 {
  float: left; }
#kt_dashicons_viewer h4 {
  width: 100%;
  border-top: 1px solid #CCC;
  margin: 1.6em 0 0;
  padding-top: .7em; }
#kt_dashicons_viewer .dashicons-before {
  position: relative;
  box-sizing: content-box;
  padding: 10px;
  width: 30px;
  height: 30px;
  white-space: nowrap;
  font-size: 30px;
  line-height: 1; }
#kt_dashicons_viewer .dashicons-before:before {
  font-size: inherit; }
#kt_dashicons_viewer .dashicons-before:after {
  content: attr(alt);
  display: block;
  font-size: 9px;
  color: #999;
  text-align: center; }
</style>
<div class='wrap' id='kt_dashicons_viewer'>
  <h1>$title [$n]</h1>";
    foreach ($print as $i => $line) {
        if ($i % 2 == 0) {
            print "\n
  <h4>$line</h4>";
        } else {
            print $line;
        }
    }
    print '</div>';
}

add_action('admin_menu', 'kt_dashicons_menu');
