<script type="text/javascript">
  (g=>{var h,a,k,p="The Google Maps JavaScript API",c="google",l="importLibrary",q="__ib__",m=document,b=window;b=b[c]||(b[c]={});var d=b.maps||(b.maps={}),r=new Set,e=new URLSearchParams,u=()=>h||(h=new Promise(async(f,n)=>{await (a=m.createElement("script"));e.set("libraries",[...r]+"");for(k in g)e.set(k.replace(/[A-Z]/g,t=>"_"+t[0].toLowerCase()),g[k]);e.set("callback",c+".maps."+q);a.src=`https://maps.${c}apis.com/maps/api/js?`+e;d[q]=f;a.onerror=()=>h=n(Error(p+" could not load."));a.nonce=m.querySelector("script[nonce]")?.nonce||"";m.head.append(a)}));d[l]?console.warn(p+" only loads once. Ignoring:",g):d[l]=(f,...n)=>r.add(f)&&u().then(()=>d[l](f,...n))})({
    key: "<?php echo osc_get_preference('maps_key', 'google_maps')?>",
    v: "weekly",
  });
</script>

<?php

if($item['d_coord_lat'] == '' || $item['d_coord_lat'] == null || $item['d_coord_long'] == '' || $item['d_coord_long'] == null) {

    $location = google_maps_fetch_geo($item);
    if ($location !== false) {
        $item['d_coord_lat'] = $location->lat;
        $item['d_coord_long'] = $location->lng;
    }
}

if($item['d_coord_lat'] !== '' && $item['d_coord_lat'] !== null && $item['d_coord_long'] !== '' && $item['d_coord_long'] !== null) {
?>

<div id="google_maps_map" style="width: 100%; height: 280px; background: #F7F7F7;">
    <svg width="640" height="280" xmlns="http://www.w3.org/2000/svg" align="right"><g fill="none" fill-rule="evenodd"><path fill="#F7F7F7" d="M0 0h640v280H0z"/><rect fill="#D8D8D8" x="587" y="175" width="39" height="80" rx="3"/><rect fill="#D8D8D8" x="587" y="11" width="39" height="40" rx="3"/><g transform="translate(306 104)" stroke="#979797"><path d="M13 35 3.03 22.564c-.056-.071-.41-.564-.41-.564C.915 19.627-.005 16.729 0 13.75 0 6.156 5.82 0 13 0s13 6.156 13 13.75c.004 2.978-.915 5.875-2.617 8.247L23.38 22s-.355.493-.408.559L13 35Z" fill="#D8D8D8" fill-rule="nonzero"/><circle fill="#979797" cx="13" cy="13" r="4.5"/></g></g></svg>
</div>

<script type="text/javascript">
    async function google_maps_init() {
        const { Map } = await google.maps.importLibrary("maps");
        const { Marker } = await google.maps.importLibrary("marker");

        let latlng = { lat: <?php echo $item['d_coord_lat']; ?>, lng: <?php echo $item['d_coord_long']; ?> };

        let myOptions = {
            zoom: 11,
            mapTypeControl: false,
            streetViewControl: false,
            center: latlng,
        }

        let map = new Map(document.getElementById("google_maps_map"), myOptions);

        let marker = new Marker({
            map: map,
            position: latlng
        });
    }

    let google_maps_observer = new IntersectionObserver((entries, observer) => {

        let observed = false;
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                observer.unobserve(entry.target);
                observed = true;
            }
        });

        if (observed === true) {
            google_maps_init();
        }

    });

    google_maps_observer.observe(document.querySelector('#google_maps_map'));
</script>
<?php } ?>
