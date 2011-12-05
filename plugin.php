<?php
/*
Plugin Name: Multiflow Contacts
Plugin URI: http://mildfuzz.com
Description: A plugin for creating a contact form that easily reroutes the email form to relevant people through the use of sections.
Version: 0.1
Author: Mild Fuzz
Author URI: http://mildfuzz.com
License: GPL2
*/
//load core classes
include 'core_classes.php';

global $mf_plugin;
$pluginTables = array(
		'tables' => array(
					"contacts" => array(
							'email' => 'text NOT NULL',
							'name'	=> 'text NOT NULL',
							'section' => 'text NOT NULL'
						)
					)
				);
				
$mf_plugin = new PluginTables($pluginTables);
		
register_activation_hook(__FILE__,'mf_activate_plugin');
register_deactivation_hook(__FILE__,'mf_deactivate_plugin');
	
function mf_activate_plugin(){
	global $mf_plugin;
	$mf_plugin->activate();
}
function mf_deactivate_plugin(){
	global $mf_plugin;
	
	$mf_plugin->deactivate();
}




//after install
include 'pages.php';
include 'utilities.php';
include 'shortcode.php';


?>