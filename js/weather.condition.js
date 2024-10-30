/*
@author: Rakib & Rashed Amin
Date: 27 Jun 2015, Bangladesh
*/

window.onload = getWeather();

//HTML Elements
var wind_icon = document.getElementById("wind_icon");
var hum_icon = document.getElementById("hum_icon");
var temp_today = document.getElementById("temp_today");
var humidity = document.getElementById("humidity");
var forecast = document.getElementById("forecast");
var wind = document.getElementById("wind");
var temp_label = document.getElementById("temp_label");
var weather_block = document.getElementById("weather-block");

//Utility Functions
function ms2mph(speed){
    return 2.23694*speed;
}

function capitalizeFirstLetter(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}

//Data Processing Functions
function processWeatherData(json){
    var obj = JSON.parse(json);
    temp_label.innerHTML = "Today\'s Temperature";
    humidity.innerHTML = obj.main.humidity + "%";
    forecast.innerHTML = capitalizeFirstLetter(obj.weather[0].description);
    wind.innerHTML = ms2mph(obj.wind.speed).toFixed(2) + " mph / " + obj.wind.deg + " &deg;";
    temp_today.innerHTML = (obj.main.temp).toFixed(2)+" &deg;C";
    wind_icon.src += "img/wind.png";
    hum_icon.src += "img/humidity.png";
}

function getJSON(url){
    var req = new XMLHttpRequest();
    req.open('GET', url, true);
    req.onreadystatechange = function (aEvt) {
      if (req.readyState == 4) {
        if(req.status == 200){
           processWeatherData(req.responseText);
        }
         else{
            errorReport("Error loading page");
        }
      }
    };
    req.send(null);
}

//Main Functions to be called on page Load
function getWeather() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(showWeather, showError);
    } else {
        errorReport("Geolocation is not supported by this browser.");
    }
}


/*
 * Open Weather Map API is used here
 * source: http://openweathermap.org/api
 * site: http://openweathermap.org/
 * @API ID: 9872d7bf81b20545d237d90197a7fa3a
 * Subscription: Free
 */

function showWeather(position) {
    getJSON('http://api.openweathermap.org/data/2.5/weather?lat='+Math.round(position.coords.latitude)+'&lon='
            +Math.round(position.coords.longitude)+'&units=metric&APPID=9872d7bf81b20545d237d90197a7fa3a');
}


//Error handling
function errorReport(cause){
    temp_label.innerHTML = "";
    humidity.innerHTML = "";
    forecast.innerHTML = cause;
    wind.innerHTML = "";
    temp_today.innerHTML = "";
    sunrise.innerHTML = "";
    sunset.innerHTML = "";
    wind_icon.src += "img/blank.png";
    hum_icon.src += "img/accessd.png";
    weather_block.style.background = "#d64036";
}

function showError(error) {
    switch(error.code) {
        case error.PERMISSION_DENIED:
            errorReport("User denied the request for Geolocation, please 'Allow' Geolocation to find you");
            break;
        case error.POSITION_UNAVAILABLE:
            errorReport("Location information is unavailable (kindly Check your Network Connection), please try again later");
            break;
        case error.TIMEOUT:
            errorReport("The request to get user location timed out, please try again later")
            break;
        case error.UNKNOWN_ERROR:
            errorReport("An unknown error occurred, please try again later");
            break;
    }
}


