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
<body>

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
                    <form action="login.php" method="post" class="hdx" style="height:100%;display:flex;justify-content:space-between;">

                            <!--<input type="text" id="username" placeholder="Uporabniško ime" name="username">-->

                            <div style="width: 100%;">
                                <div style="display: flex;
                                  align-items: center;
                                  background-color: #ffffff;
                                  border-width: 1px;
                                  border-style: solid;
                                  border-color: #e5e7eb;
                                  border-radius: 0.5rem;
                                  padding-left: 1rem;
                                  padding-right: 1rem;
                                  padding-top: 0.75rem;
                                  padding-bottom: 0.75rem;">
                                  <img src="images/user.svg" alt="user" style="width: 1.5rem;
                                    height: 1.5rem;">
                                  <input name="username" type="text" placeholder="Username"
                                         style="margin-left: 0.75rem;
                                           flex: 1 1 0%;
                                           background-color: transparent;"/>
                                </div>

                                <div style="display: flex;
                                  align-items: center;
                                  background-color: #ffffff;
                                  border-width: 1px;
                                  border-style: solid;
                                  border-color: #e5e7eb;
                                  border-radius: 0.5rem;
                                  padding-left: 1rem;
                                  padding-right: 1rem;
                                  padding-top: 0.75rem;
                                  padding-bottom: 0.75rem;">
                                  <img src="images/lock.svg" alt="password" style="width: 1.5rem;
                                    height: 1.5rem;">
                                  <input name="geslo" type="password" placeholder="Geslo"
                                         style="margin-left: 0.75rem;
                                           flex: 1 1 0%;
                                           background-color: transparent;"/>
                                </div>

                                <div id="emailField" style="display: none;
                                    color:black;
                                  align-items: center;
                                  background-color: #ffffff;
                                  border-width: 1px;
                                  border-style: solid;
                                  border-color: #e5e7eb;
                                  border-radius: 0.5rem;
                                  padding-left: 1rem;
                                  padding-right: 1rem;
                                  padding-top: 0.75rem;
                                  padding-bottom: 0.75rem;">
                                  <img src="images/mail.svg" alt="mail" style="width: 1.5rem;
                                    height: 1.5rem;">
                                  <input name="email" type="text" placeholder="Email"
                                         style="margin-left: 0.75rem;
                                           flex: 1 1 0%;
                                           background-color: transparent;"/>
                                </div>
                            </div>
                            <!--<input type="password" id="geslo" placeholder="Geslo" name="geslo">-->

                            <div style="display: flex;
                            flex-direction: column;">
                                <button type="button" id="registerBtn" style="background: blue; padding: 10px;  " onclick="regst()">Registracija</button>
                                <input type="submit" value="Potrdi" style="padding: 10px;
                                background: blue;
                                font-size: 22px;">
                            </div>
                        </form>
                </div>
            </div>
        </div>

        <div class="dock">
            <div class="main-icon" id="icon1" onclick="gumbi(this)"><svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M480-480q-66 0-113-47t-47-113q0-66 47-113t113-47q66 0 113 47t47 113q0 66-47 113t-113 47ZM160-160v-112q0-34 17.5-62.5T224-378q62-31 126-46.5T480-440q66 0 130 15.5T736-378q29 15 46.5 43.5T800-272v112H160Zm80-80h480v-32q0-11-5.5-20T700-306q-54-27-109-40.5T480-360q-56 0-111 13.5T260-306q-9 5-14.5 14t-5.5 20v32Zm240-320q33 0 56.5-23.5T560-640q0-33-23.5-56.5T480-720q-33 0-56.5 23.5T400-640q0 33 23.5 56.5T480-560Zm0-80Zm0 400Z"/></svg></div>
            <a href="create_group_form.php" class="main-icon" id="icon2" onclick="gumbi(this)"><svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M40-160v-112q0-34 17.5-62.5T104-378q62-31 126-46.5T360-440q66 0 130 15.5T616-378q29 15 46.5 43.5T680-272v112H40Zm720 0v-120q0-44-24.5-84.5T666-434q51 6 96 20.5t84 35.5q36 20 55 44.5t19 53.5v120H760ZM360-480q-66 0-113-47t-47-113q0-66 47-113t113-47q66 0 113 47t47 113q0 66-47 113t-113 47Zm400-160q0 66-47 113t-113 47q-11 0-28-2.5t-28-5.5q27-32 41.5-71t14.5-81q0-42-14.5-81T544-792q14-5 28-6.5t28-1.5q66 0 113 47t47 113ZM120-240h480v-32q0-11-5.5-20T580-306q-54-27-109-40.5T360-360q-56 0-111 13.5T140-306q-9 5-14.5 14t-5.5 20v32Zm240-320q33 0 56.5-23.5T440-640q0-33-23.5-56.5T360-720q-33 0-56.5 23.5T280-640q0 33 23.5 56.5T360-560Zm0 320Zm0-400Z"/></svg></a>
            <a href="join_group_form.php" class="main-icon" id="icon3" onclick="gumbi(this)"><svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M720-400v-120H600v-80h120v-120h80v120h120v80H800v120h-80Zm-360-80q-66 0-113-47t-47-113q0-66 47-113t113-47q66 0 113 47t47 113q0 66-47 113t-113 47ZM40-160v-112q0-34 17.5-62.5T104-378q62-31 126-46.5T360-440q66 0 130 15.5T616-378q29 15 46.5 43.5T680-272v112H40Zm80-80h480v-32q0-11-5.5-20T580-306q-54-27-109-40.5T360-360q-56 0-111 13.5T140-306q-9 5-14.5 14t-5.5 20v32Zm240-320q33 0 56.5-23.5T440-640q0-33-23.5-56.5T360-720q-33 0-56.5 23.5T280-640q0 33 23.5 56.5T360-560Zm0-80Zm0 400Z"/></svg></a>
        </div>
    </div>

    <div id="status">Initializing...</div>

    <script src="script.js"></script>

</body>
<script>
function regst() {
  let reg = document.getElementById("emailField");
  let regb = document.getElementById("registerBtn");

  regb.style.display = "none";
  reg.style.display = "flex";
  console.log("nekaj");
}
</script>
</html>
