<?php
function getCountryData($lat, $lon) {
    $openCageApiKey = 'Ybddb9c2c02fc4525b7898292a0743ba3';
    $openWeatherApiKey = '093e2b867b39e631fd1a82fa8ea71f3f';
    $restCountriesUrl = 'https://restcountries.com/v2/alpha/';
    $openExchangeRatesApiKey = '0fc01c89e8ca4735b691aaaa9fc0c835';

    // Get country code from OpenCage
    $geocodeUrl = "https://api.opencagedata.com/geocode/v1/json?q={$lat}+{$lon}&key={$openCageApiKey}";
    $geocodeResponse = file_get_contents($geocodeUrl);
    $geocodeData = json_decode($geocodeResponse, true);
    $countryCode = $geocodeData['results'][0]['components']['country_code'];

    // Get country data from Rest Countries
    $countryUrl = $restCountriesUrl . strtoupper($countryCode);
    $countryResponse = file_get_contents($countryUrl);
    $countryData = json_decode($countryResponse, true);

    // Get current weather from OpenWeather
    $weatherUrl = "https://api.openweathermap.org/data/2.5/weather?lat={$lat}&lon={$lon}&appid={$openWeatherApiKey}&units=metric";
    $weatherResponse = file_get_contents($weatherUrl);
    $weatherData = json_decode($weatherResponse, true);

    // Prepare data for frontend
    $result = [
        'name' => $countryData['name'],
        'capital' => $countryData['capital'],
        'population' => number_format($countryData['population']),
        'currency' => $countryData['currencies'][0]['name'] . " (" . $countryData['currencies'][0]['code'] . ")",
        'weather' => "{$weatherData['weather'][0]['description']}, {$weatherData['main']['temp']}°C",
        'flag' => "https://flagcdn.com/w320/" . strtolower($countryCode) . ".png",
        'wikipedia' => "https://en.wikipedia.org/wiki/{$countryData['name']}"
    ];

    return json_encode($result);
}

if (isset($_POST['lat']) && isset($_POST['lon'])) {
    $lat = $_POST['lat'];
    $lon = $_POST['lon'];
    echo getCountryData($lat, $lon);
} elseif (isset($_POST['code'])) {
    $countryCode = $_POST['code'];
   
    // Mock location for the UK
    $latLonMap = [
        'GB' => ['lat' => 55.3781, 'lon' => -3.4360],
    ];
    if (array_key_exists($countryCode, $latLonMap)) {
        $lat = $latLonMap[$countryCode]['lat'];
        $lon = $latLonMap[$countryCode]['lon'];
        echo getCountryData($lat, $lon);
    } else {
        echo json_encode(['error' => 'Country not supported']);
    }
}
?>
