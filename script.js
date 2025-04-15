// --- A-Frame Component for Interaction ---
AFRAME.registerComponent("model-interact", {
    init: function () {
      const entity = this.el;
      const statusElement = document.getElementById("status");
      entity.addEventListener("click", (evt) => {
        const modelName = entity.dataset.name || "Unnamed Model";
        const modelId = entity.dataset.id || "Unknown ID";
        console.log(`Clicked on model ID: ${modelId}, Name: ${modelName}`);
        if (statusElement) {
          const originalStatus = statusElement.textContent;
          statusElement.textContent = `Tapped: ${modelName}`;
          setTimeout(() => {
            if (statusElement.textContent === `Tapped: ${modelName}`) {
              statusElement.textContent = originalStatus;
            }
          }, 3000);
        }
        const originalScale = entity.getAttribute("scale");
        if (originalScale) {
          entity.setAttribute("scale", {
            x: originalScale.x * 1.2,
            y: originalScale.y * 1.2,
            z: originalScale.z * 1.2,
          });
          setTimeout(() => {
            if (entity.parentNode) { // Check if entity still exists
               entity.setAttribute("scale", originalScale);
            }
          }, 500);
        }
      });
    },
  });

  // --- Main Application Logic ---
  window.onload = () => {
    const statusElement = document.getElementById("status");
    const arScene = document.getElementById("ar-scene");
    let map = null;
    let userMarker = null;
    let mapMarkers = {};
    let currentModels = {}; // { id: { data, entity } }
    let lastFetchPosition = null;
    let isFetchingModels = false;
    let currentUserAltitude = null;
    let currentUserAltitudeAccuracy = null;

    // --- Configuration ---
    const FETCH_RADIUS_KM = 5.0;
    const MIN_FETCH_DISTANCE_M = 500;
    const LOCATION_TIMEOUT = 30000; // How long to wait for a single position update
    const HIGH_ACCURACY = true; // Request high accuracy (GPS)
    const REQUIRED_ACCURACY_M = 150; // Max horizontal accuracy error (m) to show models
    const REQUIRED_ALTITUDE_ACCURACY_M = 75; // Max vertical accuracy error (m) to use altitude
    const LOCATION_MAX_AGE_MS = 1000; // 1000ms = 1 second

    // --- Helper Functions ---
    const showError = (message) => {
      statusElement.textContent = `Error: ${message}`;
      console.error(message);
    };
    const updateStatus = (message) => {
      statusElement.textContent = message;
      // console.log(message); // Keep console log minimal unless debugging
    };
    const degreesToRadians = (degrees) => degrees * (Math.PI / 180);
    const calculateDistance = (lat1, lon1, lat2, lon2) => {
      const earthRadiusM = 6371000;
      const dLat = degreesToRadians(lat2 - lat1);
      const dLon = degreesToRadians(lon2 - lon1);
      const radLat1 = degreesToRadians(lat1);
      const radLat2 = degreesToRadians(lat2);
      const a =
        Math.sin(dLat / 2) * Math.sin(dLat / 2) +
        Math.cos(radLat1) *
          Math.cos(radLat2) *
          Math.sin(dLon / 2) *
          Math.sin(dLon / 2);
      const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
      return earthRadiusM * c;
    };

    // --- Map Management ---
    const initMap = (lat, lon) => {
      if (!map) {
        map = L.map("map").setView([lat, lon], 13);
        L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
          attribution:
            '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        }).addTo(map);
        userMarker = L.marker([lat, lon]).addTo(map).bindPopup("Your Location");
      } else {
        // Only update view if it's significantly different? Optional optimization.
        map.setView([lat, lon], map.getZoom());
        userMarker.setLatLng([lat, lon]);
      }
    };

    const updateMapMarkers = (modelsData) => {
      const currentModelIds = Object.keys(modelsData);
      // Remove old markers
      Object.keys(mapMarkers).forEach((id) => {
        if (!currentModelIds.includes(id)) {
          if (map.hasLayer(mapMarkers[id])) {
               map.removeLayer(mapMarkers[id]);
          }
          delete mapMarkers[id];
        }
      });
      // Add/update current markers
      currentModelIds.forEach((id) => {
        const model = modelsData[id].data;
        if (!mapMarkers[id]) {
          mapMarkers[id] = L.marker([model.latitude, model.longitude])
            .addTo(map)
            .bindPopup(model.name);
        } else {
          mapMarkers[id].setLatLng([model.latitude, model.longitude]);
        }
      });
    };

    const fetchNearbyModels = async (latitude, longitude) => {
      if (isFetchingModels) return;
      isFetchingModels = true;
      // updateStatus("Fetching nearby models...");
      console.log(`Fetching models near ${latitude}, ${longitude} within ${FETCH_RADIUS_KM}km`);
      try {
        const response = await fetch(
          `get_models.php?lat=${latitude}&lon=${longitude}&radius=${FETCH_RADIUS_KM}`
        );
        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
        const result = await response.json();
        if (result.success) {
          console.log(`Received ${result.models.length} models from server.`);
          updateDisplayedModels(result.models); // Process results
          lastFetchPosition = { latitude, longitude }; // Update last fetch pos on success
        } else {
          throw new Error(result.message || "Failed to fetch models");
        }
      } catch (error) {
        showError(`Could not fetch models: ${error.message}`);
        console.error("Fetch error details:", error);
      } finally {
        isFetchingModels = false;
        // Status will be updated by the next handleLocationSuccess call
      }
    };

    // Add/Remove entities based on fetched data
    const updateDisplayedModels = (nearbyModelsData) => {
      const nearbyModelIds = nearbyModelsData.map((m) => m.id.toString());
      const currentModelIds = Object.keys(currentModels);

      // 1. Remove models no longer nearby
      currentModelIds.forEach((id) => {
        if (!nearbyModelIds.includes(id)) {
          console.log("Removing model:", id, currentModels[id]?.data?.name);
          if (currentModels[id]?.entity?.parentNode) {
            currentModels[id].entity.parentNode.removeChild(currentModels[id].entity);
          }
          delete currentModels[id];
        }
      });

      // 2. Add new nearby models
      nearbyModelsData.forEach((modelData) => {
        const modelIdStr = modelData.id.toString();
        if (!currentModels[modelIdStr]) {
          console.log("Adding model:", modelIdStr, modelData.name);
          const entity = createModelEntity(modelData);
          if (entity) {
              arScene.appendChild(entity);
              currentModels[modelIdStr] = { data: modelData, entity: entity };
          }
        }
      });

      console.log("Current models in scene:", Object.keys(currentModels).length);
      updateMapMarkers(currentModels); // Update map based on current scene
    };

    // Create a single A-Frame entity
    const createModelEntity = (modelData) => {
       try {
          const entity = document.createElement("a-entity");
          entity.setAttribute("id", `model-${modelData.id}`);
          entity.setAttribute("gps-entity-place", {
              latitude: modelData.latitude,
              longitude: modelData.longitude,
          });
          entity.setAttribute("gltf-model", `url(${modelData.model_url})`);
          entity.setAttribute("scale", "0.01 0.01 0.01"); // Start small/hidden
          entity.setAttribute("visible", false);
          entity.setAttribute("class", "clickable");
          entity.setAttribute("model-interact", "");
          entity.hasLoaded = false; // Flag to check if model geometry is loaded

          // Store data via dataset
          entity.dataset.id = modelData.id;
          entity.dataset.name = modelData.name;
          entity.dataset.latitude = modelData.latitude;
          entity.dataset.longitude = modelData.longitude;
          entity.dataset.modelUrl = modelData.model_url;
          entity.dataset.baseScale = modelData.base_scale;
          entity.dataset.minScale = modelData.min_scale;
          entity.dataset.maxScale = modelData.max_scale;
          entity.dataset.refDist = modelData.reference_distance;
          entity.dataset.visThresh = modelData.visibility_threshold;
          entity.dataset.targetAltitude = modelData.target_altitude;

          entity.addEventListener("model-error", (e) => {
              console.error(`Failed to load 3D model: ${modelData.name} (${modelData.model_url})`, e);
              showError(`Failed to load model: ${modelData.name}`);
              if(entity.parentNode) entity.parentNode.removeChild(entity);
              delete currentModels[modelData.id.toString()];
          });
           entity.addEventListener("model-loaded", () => {
               console.log(`3D model loaded successfully: ${modelData.name} (ID: ${modelData.id})`);
               entity.hasLoaded = true; // Mark as loaded
           });

          return entity;
       } catch (error) {
           console.error("Error creating entity for model:", modelData.name, error);
           return null;
       }
    };


    // --- Location Handling ---
    const handleLocationSuccess = (position) => {
      const userLat = position.coords.latitude;
      const userLon = position.coords.longitude;
      const accuracy = position.coords.accuracy;
      currentUserAltitude = position.coords.altitude;
      currentUserAltitudeAccuracy = position.coords.altitudeAccuracy;

      // Build status string
      let statusText = `Loc: ${userLat.toFixed(5)}, ${userLon.toFixed(5)} (Acc: ${accuracy.toFixed(1)}m)`;
      if (currentUserAltitude !== null) {
          statusText += ` | Alt: ${currentUserAltitude.toFixed(1)}m`;
          if (currentUserAltitudeAccuracy !== null) {
              statusText += ` (Acc: ${currentUserAltitudeAccuracy.toFixed(1)}m)`;
          }
      } else {
          statusText += ` | Alt: N/A`;
      }

      initMap(userLat, userLon); // Update map

      // --- Decide whether to fetch new models ---
      let shouldFetch = false;
      if (!lastFetchPosition) {
        shouldFetch = true;
      } else {
        const distMoved = calculateDistance(
          userLat, userLon,
          lastFetchPosition.latitude, lastFetchPosition.longitude
        );
        if (distMoved > MIN_FETCH_DISTANCE_M) {
          shouldFetch = true;
        }
      }
      if (shouldFetch && !isFetchingModels) {
        // Log fetch trigger only when it happens
        if (!lastFetchPosition) { console.log("First location fix, fetching models."); }
        else { console.log(`Moved > ${MIN_FETCH_DISTANCE_M}m since last fetch, fetching new models.`); }
        fetchNearbyModels(userLat, userLon);
      }

      // --- Update scale, visibility, AND ALTITUDE for CURRENTLY DISPLAYED models ---
      let visibleModelsCount = 0;
      const canUseAltitude = currentUserAltitude !== null &&
                             currentUserAltitudeAccuracy !== null &&
                             currentUserAltitudeAccuracy < REQUIRED_ALTITUDE_ACCURACY_M;

      if (accuracy < REQUIRED_ACCURACY_M) {
        Object.values(currentModels).forEach(({ data, entity }) => {
          // Ensure entity and its underlying 3D object are ready AND model loaded
          if (!entity || !entity.object3D || !entity.hasLoaded) {
              return; // Skip if not ready
          }

          const distance = calculateDistance(userLat, userLon, data.latitude, data.longitude);
          const visThresh = parseFloat(entity.dataset.visThresh || 200);

          if (distance < visThresh) {
            // Scaling
            const baseScale = parseFloat(entity.dataset.baseScale || 5.0);
            const refDist = parseFloat(entity.dataset.refDist || 25.0);
            const minScale = parseFloat(entity.dataset.minScale || 0.5);
            const maxScale = parseFloat(entity.dataset.maxScale || 15.0);

            if (isNaN(baseScale) || isNaN(refDist) || isNaN(minScale) || isNaN(maxScale) || refDist <= 0) {
                console.error(`Invalid scaling parameters for model ${entity.dataset.id}:`, entity.dataset);
                entity.setAttribute("visible", false);
                return; // Skip bad data
            }

            let scaleFactor = refDist / Math.max(distance, 0.1);
            let calculatedScale = baseScale * scaleFactor;
            let finalScale = Math.max(minScale, Math.min(maxScale, calculatedScale));

            // Apply Scale only if it has changed significantly? (Optional optimization)
            const currentScale = entity.getAttribute('scale');
            if (!currentScale || Math.abs(currentScale.x - finalScale) > 0.01) { // Threshold to avoid tiny updates
               entity.setAttribute("scale", { x: finalScale, y: finalScale, z: finalScale });
            }


            // Altitude Adjustment
            const targetAltitude = parseFloat(entity.dataset.targetAltitude || 0);
            let verticalOffset = targetAltitude;
            if (canUseAltitude) {
                verticalOffset = targetAltitude - currentUserAltitude;
            }
            // Apply altitude only if it changed significantly? (Optional optimization)
            if (Math.abs(entity.object3D.position.y - verticalOffset) > 0.1) {
               entity.object3D.position.y = verticalOffset;
            }


            entity.setAttribute("visible", true);
            visibleModelsCount++;
          } else {
            entity.setAttribute("visible", false); // Hide if too far
          }
        });
         statusText += ` | ${visibleModelsCount}/${Object.keys(currentModels).length} models visible`;
         if (!canUseAltitude && visibleModelsCount > 0) {
             statusText += ` (Alt unreliable)`;
         }

      } else {
        // Accuracy too low, hide all models
        Object.values(currentModels).forEach(({ entity }) => {
           if (entity) entity.setAttribute("visible", false);
        });
        statusText += ` | Accuracy too low for AR`;
      }

      // Update status only if not currently fetching
      if (!isFetchingModels) {
          updateStatus(statusText);
      }
    };

    const handleLocationError = (error) => {
      let message = "";
      switch (error.code) {
        case error.PERMISSION_DENIED: message = "User denied Geolocation."; break;
        case error.POSITION_UNAVAILABLE: message = "Location unavailable."; break;
        case error.TIMEOUT: message = "Location request timed out."; break; // This might happen more with lower maximumAge if GPS is slow
        default: message = "Unknown location error."; break;
      }
      showError(message);
    };

    // --- Initialization ---
    updateStatus("Requesting location permission...");
    if ("geolocation" in navigator) {
      navigator.geolocation.watchPosition(
        handleLocationSuccess,
        handleLocationError,
        {
          enableHighAccuracy: HIGH_ACCURACY,
          timeout: LOCATION_TIMEOUT,
          // *** Use the updated maximumAge value ***
          maximumAge: LOCATION_MAX_AGE_MS,
        }
      );
    } else {
      showError("Geolocation is not supported by this browser.");
    }
  }; // End window.onload
