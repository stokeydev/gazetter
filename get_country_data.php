<?php
function getCountryData($lat, $lon) {
    $openCageApiKey = 'bddb9c2c02fc4525b7898292a0743ba3';
    $openWeatherApiKey = 'YOUR_OPENWEATHER_API_KEY';
    $restCountriesUrl = 'https://restcountries.com/v2/alpha/';
    $openExchangeRatesApiKey = 'bddb9c2c02fc4525b7898292a0743ba3';

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

    // Get currency exchange rate from Open Exchange Rates
    $currency = $countryData['currencies'][0]['code'];
    $exchangeRateUrl = "https://openexchangerates.org/api/latest.json?app_id={$openExchangeRatesApiKey}";
    $exchangeRateResponse = file_get_contents($exchangeRateUrl);
    $exchangeRateData = json_decode($exchangeRateResponse, true);
    $exchangeRate = $exchangeRateData['rates'][$currency];

    // Prepare data for frontend
    $result = [
        'name' => $countryData['name'],
        'capital' => $countryData['capital'],
        'population' => number_format($countryData['population']),
        'currency' => "{$currency} ({$countryData['currencies'][0]['name']})",
        'exchangeRate' => number_format($exchangeRate, 2),
        'weather' => "{$weatherData['weather'][0]['description']}, {$weatherData['main']['temp']}°C",
        'wikipedia' => $countryData['flag']
    ];

    return json_encode($result);
}

if (isset($_POST['lat']) && isset($_POST['lon'])) {
    $lat = $_POST['lat'];
    $lon = $_POST['lon'];
    echo getCountryData($lat, $lon);
} elseif (isset($_POST['code'])) {
    $countryCode = $_POST['code'];
   
    $latLonMap = [
        'US' => ['lat' => 37.7749, 'lon' => -122.4194],

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