
{capture name=path}{l s='Map' mod='khlordadr'}{/capture}

<h1 class="page-heading">{l s='Map' mod='khlordadr'}</h1>

{if !empty($error)}
<div class="alert alert-danger">{$error}</div>
{else}

<div id="adrsmap" style="height:500px;"></div>
<script>
var map;
var addresses = {$addresses};
var startLocation = {$start};
var markers = [];
{literal}
function initMap() {
    map = new google.maps.Map(document.getElementById('adrsmap'), {
        center: {lat: startLocation.lat, lng: startLocation.lng},
        zoom: 8
    });

	for(i in addresses){
		//console.log({lat: Number(addresses[i].latitude), lng: Number(addresses[i].longitude)});
		var marker = new google.maps.Marker({
	    	position: {lat: Number(addresses[i].latitude), lng: Number(addresses[i].longitude)},
	        map: map
	    });

	}
}
{/literal}
</script>
<script src="https://maps.googleapis.com/maps/api/js?callback=initMap"
    async defer></script>
{/if}