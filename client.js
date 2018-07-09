(function () {
    // URLs to be used for data collection. Change the analyticsURL to point to the location of your analytics.php script!
    const analyticsURL = "http://localhost:8080/analytics.php";
    const ipfinder = "https://www.canihazip.com/s";
    const iplocator = "https://ipapi.co/";
    let ip = "";
    let country = "";
    let countrycode = "";
    let ineu = false;
    let page = window.location.href;

    function fetch(url, callback) { // Fetch the services async so we dont cause a loading indicator.
        let xhr = createCORSRequest('GET', url); // Create CORS compliant request or it will fail.
        if (!xhr) {
            alert('CORS not supported');
            return;
        }

        xhr.onload = function () {
            return callback(xhr);
        };

        xhr.onerror = function () {
            console.log('Woops, there was an error making the request.');
        };

        xhr.send();
    }


    function createCORSRequest(method, url) {
        let xhr = new XMLHttpRequest();
        if ("withCredentials" in xhr) {

            // Check if the XMLHttpRequest object has a "withCredentials" property.
            // "withCredentials" only exists on XMLHTTPRequest2 objects.
            xhr.open(method, url, true);

        } else if (typeof XDomainRequest !== "undefined") {

            // Otherwise, check if XDomainRequest.
            // XDomainRequest only exists in IE, and is IE's way of making CORS requests.
            xhr = new XDomainRequest();
            xhr.open(method, url);

        } else {
            // Otherwise, CORS is not supported by the browser.
            xhr = null;
        }
        return xhr;
    }

    document.addEventListener("DOMContentLoaded", function () { // Wait until the DOM is loaded fully.
        fetch(ipfinder, function (xhttp) { // We contact the IP finder to get the IP of the client.
            ip = xhttp.responseText;
            fetch(iplocator + ip + "/json/", function (xhttp) { // Then the IP geolocator is contacted with the client IP to get more data.
                let json = JSON.parse(xhttp.responseText);
                country = json["country_name"];
                countrycode = json["country"];
                ip = json["ip"];
                ineu = json["in_eu"];
		// We send a GET request to the analytics script to store the data.
                fetch(analyticsURL + "?country=" + country + "&countrycode=" + countrycode + "&ip=" + ip + "&ineu=" + ineu + "&page=" + page, function () {
                    console.log("Successful request!");
                });
            });
        });
    });
})();
