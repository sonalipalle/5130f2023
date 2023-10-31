// Function to fetch the user's location based on IP address
function getUserLocation() {
    fetch('https://ipinfo.io/json')
        .then(response => response.json())
        .then(data => {
            const location = data.city + ', ' + data.region;
            document.getElementById('current-location').textContent = location;
        })
        .catch(error => {
            console.error('Error fetching user location: ' + error);
        });
}

// Call the function to get the user's location
getUserLocation();
