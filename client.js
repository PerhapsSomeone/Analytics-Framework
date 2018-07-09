(function () {
    const analyticsURL = "http://localhost:8080/analytics.php";
    const ipfinder = "https://www.canihazip.com/s";
    const iplocator = "https://ipapi.co/";
    let ip = "";
    let country = "";
    let countrycode = "";
    let ineu = false;
    let page = window.location.href;

    function fetch(url, callback) {
        let xhr = createCORSRequest('GET', url);
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

    document.addEventListener("DOMContentLoaded", function () {
        fetch(ipfinder, function (xhttp) {
            ip = xhttp.responseText;
            fetch(iplocator + ip + "/json/", function (xhttp) {
                let json = JSON.parse(xhttp.responseText);
                country = json["country_name"];
                countrycode = json["country"];
                ip = json["ip"];
                ineu = json["in_eu"];

                fetch(analyticsURL + "?country=" + country + "&countrycode=" + countrycode + "&ip=" + ip + "&ineu=" + ineu + "&page=" + page, function () {
                    console.log("Successful request!");
                });
            });
        });
    });
})();