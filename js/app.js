$(document).ready(function () {
    // Initialize the map focused on the UK
    var map = L.map('map').setView([55.3781, -3.4360], 5);

    // Load and display tile layers
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 18,
    }).addTo(map);

    // Function to display country data in the modal
    function displayCountryData(countryData) {
        $('#countryName').text(countryData.name);
        var countryInfoHtml = `
            <p>Capital: ${countryData.capital}</p>
            <p>Population: ${countryData.population}</p>
            <p>Currency: ${countryData.currency}</p>
            <p>Weather: ${countryData.weather}</p>
            <p><img src="${countryData.flag}" alt="Flag of ${countryData.name}" style="width: 100px;"></p>
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

    // Function to get and display UK location
    function showUKLocation() {
        // Mock UK coordinates for demonstration
        var ukLat = 55.3781;
        var ukLon = -3.4360;

        // AJAX call to PHP to get country data based on UK lat/lon
        $.ajax({
            url: 'get_country_data.php',
            type: 'POST',
            data: { lat: ukLat, lon: ukLon },
            success: function (data) {
                var countryData = JSON.parse(data);
                displayCountryData(countryData);
            },
            error: function (xhr, status, error) {
                console.error('AJAX Error:', error);
            }
        });
    }

    // Load UK location on map load
    showUKLocation();
});
