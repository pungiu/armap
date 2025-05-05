<?php
require_once 'db.php';
session_start();
?>
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

        <div class="map-container" id="map-container">
            <div id="map"></div>
        </div>

        <div class="moznosti" id="moda">
            <div id="login">
                <div id="login-container">
                    <form action="login.php" method="post" class="hdx">
                        <label for="regin">Vnesi uporabnisko ime</label>
                        <input type="text" placeholder="Uporabnisko ime" name="username">
                        <label for="regin">Vnesi geslo</label>
                        <input type="password" placeholder="Geslo" name="geslo">
                        <input type="text" name="email" placeholder="Vnesi email za registracijo" id="skrit">
                        <input type="submit" value="prijava" id="submitgumb">
                    </form>
                    <div onclick="regi()">Registracija</div>
                </div>
            </div>
        </div>

        <div class="dock">
            <div class="main-icon" id="icon1" onclick="gumbi(this)"><svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M480-480q-66 0-113-47t-47-113q0-66 47-113t113-47q66 0 113 47t47 113q0 66-47 113t-113 47ZM160-160v-112q0-34 17.5-62.5T224-378q62-31 126-46.5T480-440q66 0 130 15.5T736-378q29 15 46.5 43.5T800-272v112H160Zm80-80h480v-32q0-11-5.5-20T700-306q-54-27-109-40.5T480-360q-56 0-111 13.5T260-306q-9 5-14.5 14t-5.5 20v32Zm240-320q33 0 56.5-23.5T560-640q0-33-23.5-56.5T480-720q-33 0-56.5 23.5T400-640q0 33 23.5 56.5T480-560Zm0-80Zm0 400Z"/></svg></div>
            <div class="main-icon" id="icon2" onclick="gumbi(this)">b</div>
            <div class="main-icon" id="icon3" onclick="gumbi(this)">c</div>
        </div>
        <?php var_dump($_SESSION); ?>
    </div>

    <div id="status">Initializing...</div>

    <script src="script.js"></script>

</body>
</html>
