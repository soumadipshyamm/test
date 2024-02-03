var selected = false;

var getLat = ''
var getLng = ''
$(function () {

    $('#address').blur(function () {
        if (!selected) {
            $(this).val('')
            $("#lattitude").val('')
            $("#longitude").val('')
        }
    });

    $('#address').focus(function () {
        selected = false;
        var options = {
            componentRestrictions: { country: ["in"] }
        };
        autocomplete = new google.maps.places.SearchBox(document.getElementById('address'), options);

        autocomplete.addListener('places_changed', function () {
            selected = true;
            let places = autocomplete.getPlaces()
            places.forEach(function (place) {
                getLat = place.geometry.location.lat()
                getLng = place.geometry.location.lng()
            })
            $("#lattitude").val(getLat)
            $("#longitude").val(getLng)
        });
    });


    $("#address").keypress(function (event) {
        if (event.keyCode == 13 || event.keyCode == 9) {
            $(event.target).blur();
            if ($(".pac-container .pac-item:first span:eq(3)").text() == "")
                firstValue = $(".pac-container .pac-item:first .pac-item-query").text();
            else
                firstValue = $(".pac-container .pac-item:first .pac-item-query").text() + ", " + $(".pac-container .pac-item:first span:eq(3)").text();
            event.target.value = firstValue;

        } else {
            return true;
        }
        // alert(firstValue);
    });

    let address1 = "";
    let postcode = "";
    let locality = "";
    let state = "";
    let country = "";
    let landmark = "";
    let lat = "";
    let lng = "";

    if ($(".address").length) {
        $(".address").each(function () {
            var id = $(this).attr("id")
            $('#' + id).focus(function () {
                selected = false;
                var options = {
                    componentRestrictions: { country: ["in"] }
                };
                autocomplete = new google.maps.places.SearchBox(document.getElementById(id), options);

                autocomplete.addListener('places_changed', function () {
                    selected = true;
                    let place = autocomplete.getPlaces()
                    console.log(place);
                    const addressName = place ? place[0].name : '';
                    lat = place[0].geometry.location.lat();
                    lng = place[0].geometry.location.lng();
                    for (const component of place[0].address_components) {
                        // @ts-ignore remove once typings fixed
                        const componentType = component.types[0];
                        switch (componentType) {
                            case "subpremise": {
                                address1 = `${component.long_name} ${address1}`;
                                break;
                            }
                            case "premise": {
                                address1 += component.short_name;
                                break;
                            }
                            case "postal_code": {
                                postcode = `${component.long_name}${postcode}`;
                                break;
                            }
                            case "postal_code_suffix": {
                                postcode = `${postcode}-${component.long_name}`;
                                break;
                            }
                            case "locality":
                                locality = component.long_name;
                                break;
                            case "administrative_area_level_1": {
                                state = component.long_name;
                                break;
                            }
                            case "country":
                                country = component.long_name;
                                break;
                            case "sublocality_level_3":
                                landmark = component.long_name;
                                break;
                        }
                    }
                    console.log(
                        `address : ${address1}
                        postcode : ${postcode}
                        locality_city : ${locality}
                        state : ${state}
                        country : ${country}
                        landmark : ${landmark}
                        lat : ${lat}
                        lng : ${lng}`
                    );
                    $('#address_1').val(addressName);
                    $('#address_2').val(address1);
                    $('#landmark').val(landmark);
                    $('#city_id_add').val(locality);
                    $('#state').val(state);
                    $('#lat').val(lat);
                    $('#lng').val(lng);
                    if (!(SEGMENT.trim() == 'contact-us' || SEGMENT.trim() == 'my-account' || SEGMENT.trim() == 'service-booking-additional')) {
                        // alert(1);
                        latlngToLocation(lat, lng, true);
                        // setCurrentLocationInCookies(addressName, address1, locality, state, country, lat, lng);
                    } else {
                        // alert(2);
                        latlngToLocation(lat, lng, false);
                    }
                });
            })
        })
    }
})

function getCurrentLocation(isHeader = true) {
    $('.getUpdateLocation').html('Fatching Location Info...');
    // alert(isHeader);
    if (geoPosition.init()) {
        geoPosition.getCurrentPosition(success_callback, error_callback, {
            enableHighAccuracy: true
        });
    } else {
        // You cannot use Geolocation in this device
    }
    geoPositionSimulator.init();

    // p : geolocation object
    function success_callback(p) {
        const latitude = p.coords.latitude;
        const longitude = p.coords.longitude;
        // const isHeader = true;

        latlngToLocation(latitude, longitude, isHeader);
    }

    function error_callback(p) {
        console.log(p);
    }
}

function latlngToLocation(latitude, longitude, isHeader = false) {
    jQuery.get(`https://maps.googleapis.com/maps/api/geocode/json?latlng=${latitude},${longitude}&sensor=true&key=AIzaSyBvhh3BKrnIsuvRCLuPlsUEdcOt4Kj5stY`, function (data) {
        const place = data.results[0];
        console.warn(place);
        // console.log(place.address_components);
        const formatted_address = place?.formatted_address ?? '';
        const addressName = formatted_address;
        let address = '';
        let pincode = '';
        let city = '';
        let state = '';
        let country = '';
        let landmark = '';
        for (const component of place.address_components) {
            const componentType = component.types[0];
            switch (componentType) {
                case "subpremise": {
                    address = `${component.long_name} ${address}`;
                    break;
                }
                case "premise": {
                    address += component.short_name;
                    break;
                }
                case "postal_code": {
                    pincode = `${component.long_name}${pincode}`;
                    break;
                }
                case "postal_code_suffix": {
                    pincode = `${pincode}-${component.long_name}`;
                    break;
                }
                case "locality":
                    city = component.long_name;
                    break;
                case "administrative_area_level_1": {
                    state = component.long_name;
                    break;
                }
                case "country":
                    country = component.long_name;
                    break;
                case "sublocality_level_3":
                    landmark = component.long_name;
                    break;
            }
        }

        console.log(`address:${address} pincode:${pincode} city:${city} state:${state} country:${country} landmark:${landmark}`);
        if (isHeader) {
            setCurrentLocationInCookies(formatted_address, address, city, state, country, pincode, latitude, longitude);
        } else {
            $('#address').val(formatted_address);
            $('#address_1').val(addressName);
            $('#address_2').val(address);
            $('#landmark').val(landmark);
            $('#city_id_add').val(city);
            $('#state').val(state);
            $('#lat').val(latitude);
            $('#lng').val(longitude);
            initMap('live_map', latitude, longitude);
        }
    });
}

// function placeToAddress(place){
//     var address = {};
//     place.address_components.forEach(function(c) {
//         switch(c.types[0]){
//             case 'street_number':
//                 address.StreetNumber = c;
//                 break;
//             case 'route':
//                 address.StreetName = c;
//                 break;
//             case 'neighborhood': case 'locality':    // North Hollywood or Los Angeles?
//                 address.City = c;
//                 break;
//             case 'administrative_area_level_1':     //  Note some countries don't have states
//                 address.State = c;
//                 break;
//             case 'postal_code':
//                 address.Zip = c;
//                 break;
//             case 'country':
//                 address.Country = c;
//                 break;
//             /*
//             *   . . . 
//             */
//         }
//     });

//     return address;
// }

function setCurrentLocationInCookies(formatted_address, address, city, state, country, pincode, latitude, longitude) {
    // alert();
    let msg = 'Go to homepage (Kolkata)';
    if(userAddress == 'india'){
        msg = 'Go to homepage (Kolkata)';
    }else{
        msg = 'Go to homepage (Dubai)';
    }
    $.ajax({
        method: 'post',
        url: BASE_URL + "location-fetch-and-save",
        data: { formatted_address, address, city, state, country, pincode, latitude, longitude },
        success: function (res) {
            if (res == 'reload') {
                swal({
                    title: "You change your location to other country",
                    buttons: true,
                    type: "success",
                    confirmButtonText: "Please Login",
                });
            } else if (res == 'ok') {
                swal("Address Located", "", "success");
            } else if (res == 'not-allowed') {
                swal({
                    title: "Service currently not available in this location",
                    buttons: true,
                    type: "error",
                    confirmButtonText: msg,
                });
            } else {
                swal("Address not located", "Not Set", "error");
            }
        }
    });
}

function initMap(divid, lat, lng) {
    const center = new google.maps.LatLng(lat, lng);
    const map = new google.maps.Map(document.getElementById(divid), {
        zoom: 16,
        center: center,
    });
    const svgMarker = {
        path: "M-1.547 12l6.563-6.609-1.406-1.406-5.156 5.203-2.063-2.109-1.406 1.406zM0 0q2.906 0 4.945 2.039t2.039 4.945q0 1.453-0.727 3.328t-1.758 3.516-2.039 3.070-1.711 2.273l-0.75 0.797q-0.281-0.328-0.75-0.867t-1.688-2.156-2.133-3.141-1.664-3.445-0.75-3.375q0-2.906 2.039-4.945t4.945-2.039z",
        fillColor: "blue",
        fillOpacity: 0.6,
        strokeWeight: 0,
        rotation: 0,
        scale: 2,
        anchor: new google.maps.Point(0, 20),
    };

    new google.maps.Marker({
        position: map.getCenter(),
        icon: svgMarker,
        map: map,
    });
}

//   window.initMap = initMap;

// <script src="https://maps.googleapis.com/maps/api/js?key=<?=  GOOGLE_KEY; ?>&amp;libraries=places&amp;callback=initAutocomplete" async="" defer=""></script>
// <script src="<?= base_url('public/js/geoPositionSimulator.js') ?>"></script>
// <script src="<?= base_url('public/js/geoPosition.js') ?>"></script>


