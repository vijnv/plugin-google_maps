<?php
/*
Plugin Name: Google Maps
Plugin URI: https://github.com/vijnv/plugin-google_maps
Description: This plugin shows a Google Map on the location space of every item.
Version: 3.0.0
Author: Osclass, kingsult, Vincent Vijn
Author URI: https://github.com/vijnv/plugin-google_maps
Plugin update URI:
*/

function google_maps_install()
{
    osc_set_preference('maps_key', '', 'google_maps', 'STRING');
    osc_set_preference('maps_server_key', '', 'google_maps', 'STRING');
    osc_reset_preferences();
}

function google_maps_uninstall()
{
    osc_delete_preference('maps_key', 'google_maps');
    osc_delete_preference('maps_server_key', 'google_maps');
    osc_reset_preferences();
}


function google_maps_location() {
    $item = osc_item();
    require 'map.php';
}

function insert_geo_location($item, $location = null) {
    $itemId = $item['pk_i_id'];
    if ($location == null) {
        $location = google_maps_fetch_geo($item);
    }

    if ($location !== false) {    
        ItemLocation::newInstance()->update (array('d_coord_lat' => $location->lat,
                                                   'd_coord_long' => $location->lng)
                                            ,array('fk_i_item_id' => $itemId));
    }
}

function google_maps_fetch_geo($item) {
    $sAddress = (isset($item['s_address']) ? $item['s_address'] : '');
    $sCity = (isset($item['s_city']) ? $item['s_city'] : '');
    $sRegion = (isset($item['s_region']) ? $item['s_region'] : '');
    $sCountry = (isset($item['s_country']) ? $item['s_country'] : '');
    $address = sprintf('%s, %s, %s, %s', $sAddress, $sCity, $sRegion, $sCountry);

    $response = osc_file_get_contents(sprintf('https://maps.googleapis.com/maps/api/geocode/json?address=%s&key='.osc_get_preference('maps_server_key', 'google_maps'), urlencode($address)));
    $jsonResponse = json_decode($response);
    
    if (isset($jsonResponse->results[0]->geometry->location))       {
        $location = $jsonResponse->results[0]->geometry->location;
        return $location;
    } else {
        error_log("Failed to insert geo location for item ".$item['pk_i_id'].". Response: ".$response);
        return false;
    }
}

osc_add_hook('location', 'google_maps_location');
osc_add_hook('posted_item', 'insert_geo_location');
osc_add_hook('edited_item', 'insert_geo_location');

osc_add_route('google_maps_settings', 'google_maps_settings', 'google_maps_settings', 'google_maps/admin/settings.php');
osc_add_hook('admin_menu_init', function() {
    osc_add_admin_submenu_divider('plugins', 'Google Maps Plugin', 'google_maps_divider', 'administrator');
    osc_add_admin_submenu_page('plugins', __('Settings', 'google_maps'), osc_route_admin_url('google_maps_settings'), 'google_maps_setting', 'administrator');
});

osc_register_plugin(osc_plugin_path(__FILE__), 'google_maps_install');
osc_add_hook(osc_plugin_path(__FILE__)."_uninstall", 'google_maps_uninstall');
