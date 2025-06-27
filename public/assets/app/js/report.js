var map = document.getElementById("map");
var latitude = document.getElementById("latitude");
var longitude = document.getElementById("longitude");
var address = document.getElementById("address");

navigator.geolocation.getCurrentPosition(
    function (position) {
        var lat = position.coords.latitude;
        var lng = position.coords.longitude;

        // Set ke input hidden yang benar
        latitude.value = lat;
        longitude.value = lng;

        // Inisialisasi map
        var mymap = L.map("map").setView([lat, lng], 13);

        var marker = L.marker([lat, lng]).addTo(mymap);

        L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
            attribution:
                'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors',
            maxZoom: 18,
        }).addTo(mymap);

        // Reverse geocoding
        var geocodingUrl = `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`;

        fetch(geocodingUrl)
            .then((response) => response.json())
            .then((data) => {
                address.value = data.display_name;
                marker
                    .bindPopup(
                        `<b>Lokasi Laporan</b><br />Kamu berada di ${data.display_name}`
                    )
                    .openPopup();
            })
            .catch((error) =>
                console.error("Error fetching reverse geocoding data:", error)
            );
    },
    function (error) {
        console.error("Geolocation error:", error);
        alert(
            "Gagal mendapatkan lokasi. Pastikan GPS kamu aktif dan beri izin akses lokasi."
        );
    },
    {
        enableHighAccuracy: true,
        timeout: 10000,
        maximumAge: 0,
    }
);
