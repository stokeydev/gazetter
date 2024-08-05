<?php
// get_country_list.php

$data = file_get_contents('countryBorders.geo.json');
$countries = json_decode($data, true);
$countryList = array_map(function($country) {
    return [
        'name' => $country['properties']['name'],
        'code' => $country['properties']['iso_a2']
    ];
}, $countries['features']);

echo json_encode($countryList);
?>
