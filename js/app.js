$(document).ready(function () {
    // Initialize the map
    var map = L.map('map').setView([0, 0], 2);

    // Load and display tile layers
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 18,
    }).addTo(map);

    // Function to get and display user's current location
    function showCurrentLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function (position) {
                var lat = position.coords.latitude;
                var lon = position.coords.longitude;
                map.setView([lat, lon], 5);

                // AJAX call to PHP to get country data based on lat/lon
                $.ajax({
                    url: 'get_country_data.php',
                    type: 'POST',
                    data: { lat: lat, lon: lon },
                    success: function (data) {
                        var countryData = JSON.parse(data);
                        displayCountryData(countryData);
                    },
                    error: function (xhr, status, error) {
                        console.error('AJAX Error:', error);
                    }
                });
            }, function (error) {
                console.error('Error getting geolocation:', error);
            });
        } else {
            alert("Geolocation is not supported by this browser.");
        }
    }

    // Function to display country data in the modal
    function displayCountryData(countryData) {
        $('#countryName').text(countryData.name);
        var countryInfoHtml = `
            <p>Capital: ${countryData.capital}</p>
            <p>Population: ${countryData.population}</p>
            <p>Currency: ${countryData.currency} (${countryData.exchangeRate})</p>
            <p>Weather: ${countryData.weather}</p>
            <p><a href="${countryData.wikipedia}" target="_blank">More Info</a></p>
        `;
        $('#countryInfo').html(countryInfoHtml);
        $('#countryInfoModal').modal('show');
    }

    // Populate country select options
    $.ajax({
        url: 'get_country_list.php',
        type: 'GET',
        success: function (data) {
            var countries = JSON.parse(data);
            $.each(countries, function (index, country) {
                $('#countrySelect').append(new Option(country.name, country.code));
            });
        },
        error: function (xhr, status, error) {
            console.error('AJAX Error:', error);
        }
    });

    // Event listener for country select change
    $('#countrySelect').change(function () {
        var countryCode = $(this).val();
        // AJAX call to PHP to get selected country data
        $.ajax({
            url: 'get_country_data.php',
            type: 'POST',
            data: { code: countryCode },
            success: function (data) {
                var countryData = JSON.parse(data);
                displayCountryData(countryData);
            },
            error: function (xhr, status, error) {
                console.error('AJAX Error:', error);
            }
        });
    });

    // Load current location on map load
    showCurrentLocation();
});
