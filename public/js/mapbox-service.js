// Only keep the code that initializes and connects to Mapbox
mapboxgl.accessToken = 'YOUR_MAPBOX_ACCESS_TOKEN';
const map = new mapboxgl.Map({
    container: 'map',
    style: 'mapbox://styles/mapbox/streets-v11',
    center: [-7.0926, 31.7917], // Default Morocco
    zoom: 10
});
