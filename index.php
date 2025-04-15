<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>AR zemljevid</title>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
     integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
     crossorigin=""/>
    <link rel="stylesheet" href="style.css">

    <script src="https://aframe.io/releases/1.5.0/aframe.min.js"></script>
    <script src="https://raw.githack.com/AR-js-org/AR.js/master/aframe/build/aframe-ar-nft.js"></script>
    <script src="https://raw.githack.com/AR-js-org/AR.js/master/three.js/build/ar-threex-location-only.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
     integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
     crossorigin=""></script>
</head>
<body style='margin: 0; overflow: hidden;'>

    <div class="main-content-area">

        <a-scene
            embedded
            id="ar-scene"
            vr-mode-ui='enabled: false'
            arjs='sourceType: webcam; videoTexture: true; debugUIEnabled: false;'
            renderer='logarithmicDepthBuffer: true;'
            cursor='rayOrigin: mouse; fuse: false;'
            raycaster='objects: .clickable'
            >
            <a-camera gps-camera rotation-reader> </a-camera>
        </a-scene>

        <div class="map-container">
            <div id="map"></div>
        </div>

        <div class="dock">
            <div cass="">&cross;</div>
            <div cass="">&cross;</div>
            <div cass="">&cross;</div>
        </div>
    </div>

    <div id="status">Initializing...</div>

    <script src="script.js"></script>

</body>
</html>
