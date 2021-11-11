<x-general.form-section submit="updateGeoLocation">
    <x-slot name="title">
        {{ __('text.Update Geo Location') }}
    </x-slot>

    <x-slot name="description">
        {{ __('text.Update store\'s Location.') }}
    </x-slot>

    <x-slot name="form">
        <x-general.action-message on="saved">
            {{ __('text.Saved.') }}
        </x-general.action-message>
        <div class="w-md-75" wire:ignore>
            <div id="map" style="height:350px"></div>
        </div>
    </x-slot>

    <x-slot name="actions">
        <x-general.button>
            {{ __('text.Save') }}
        </x-general.button>
    </x-slot>
</x-general.form-section>

@push('script')
    {{-- //google map --}}

<script>


    // Note: This example requires that you consent to location sharing when
    // prompted by your browser. If you see the error "The Geolocation service
    // failed.", it means you probably did not give permission for the browser to
    // locate you.
    let map, infoWindow;

    function initMap() {
    map = new google.maps.Map(document.getElementById("map"), {
        center: { lat: -34.397, lng: 150.644 },
        zoom: 12,
    });
    infoWindow = new google.maps.InfoWindow();
    const locationButton = document.createElement("button");
    locationButton.textContent = "@lang('text.Determine your location')";
    locationButton.classList.add("custom-map-control-button",'btn' , 'btn-primary','mb-3');
    let pos= {
                lat: {{ explode(',',$geoLocation)[0] }},
                lng: {{ explode(',',$geoLocation)[1] }},
            };
    infoWindow.setPosition(pos);
    infoWindow.setContent("@lang('text.Current Location.')");
    infoWindow.open(map);
    map.controls[google.maps.ControlPosition.BOTTOM_CENTER].push(locationButton);
    locationButton.addEventListener("click", () => {
        // Try HTML5 geolocation.
        if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            (position) => {
            const pos = {
                lat: position.coords.latitude,
                lng: position.coords.longitude,
            };
            @this.set('geoLocation',position.coords.latitude+","+position.coords.longitude)
            infoWindow.setPosition(pos);
            infoWindow.setContent("@lang('text.Location found.')");
            infoWindow.open(map);
            map.setCenter(pos);
            },
            () => {
            handleLocationError(true, infoWindow, map.getCenter());
            }
        );
        } else {
        // Browser doesn't support Geolocation
        handleLocationError(false, infoWindow, map.getCenter());
        }
    });
    }

    function handleLocationError(browserHasGeolocation, infoWindow, pos) {
        infoWindow.setPosition(pos);
        infoWindow.setContent(
            browserHasGeolocation
            ? "Error: The Geolocation service failed."
            : "Error: Your browser doesn't support geolocation."
        );
        infoWindow.open(map);
    }


</script>
@endpush
