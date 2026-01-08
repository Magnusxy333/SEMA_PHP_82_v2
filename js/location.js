document.addEventListener("DOMContentLoaded", function () {
  const getLocationBtn = document.getElementById("getLocation");
  const locationStatus = document.getElementById("locationStatus");
  const locationData = document.getElementById("locationData");
  const searchAddressBtn = document.getElementById("searchAddress");
  const addressData = document.getElementById("addressData");
  const mapContainer = document.getElementById("map");

  // Função para obter a localização
  function getLocation() {
    locationStatus.textContent = "Obtendo localização...";
    locationStatus.className = "status";

    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(showPosition, handleError, {
        enableHighAccuracy: true,
        timeout: 10000,
        maximumAge: 60000,
      });
    } else {
      locationStatus.textContent =
        "Geolocalização não é suportada por este navegador.";
      locationStatus.className = "status error";
    }
  }

  // Função para exibir a posição
  function showPosition(position) {
    const latitude = position.coords.latitude;
    const longitude = position.coords.longitude;
    const accuracy = position.coords.accuracy;

    document.getElementById("latitude").textContent = latitude.toFixed(6);
    document.getElementById("longitude").textContent = longitude.toFixed(6);
    document.getElementById("accuracy").textContent = `${accuracy.toFixed(
      2
    )} metros`;

    locationData.style.display = "block";
    locationStatus.textContent = "Localização obtida com sucesso!";
    locationStatus.className = "status success";

    // Exibir mapa estático
    showMap(latitude, longitude);
  }

  // Função para tratar erros
  function handleError(error) {
    let errorMessage;

    switch (error.code) {
      case error.PERMISSION_DENIED:
        errorMessage = "Permissão para acesso à localização negada.";
        break;
      case error.POSITION_UNAVAILABLE:
        errorMessage = "Informações de localização não disponíveis.";
        break;
      case error.TIMEOUT:
        errorMessage = "Tempo de solicitação de localização esgotado.";
        break;
      default:
        errorMessage = "Erro desconhecido.";
    }

    locationStatus.textContent = errorMessage;
    locationStatus.className = "status error";
  }

  // Função para exibir mapa estático
  function showMap(lat, lng) {
    const mapUrl = `https://maps.google.com/maps?q=${lat},${lng}&z=15&output=embed`;

    mapContainer.innerHTML = `
                    <iframe 
                        width="100%" 
                        height="100%" 
                        frameborder="0" 
                        scrolling="no" 
                        marginheight="0" 
                        marginwidth="0" 
                        src="${mapUrl}"
                        style="border:0;">
                    </iframe>
                `;
  }

  // Função para buscar endereço
  function searchAddress() {
    const address = document.getElementById("addressInput").value.trim();

    if (!address) {
      alert("Por favor, digite um endereço para buscar.");
      return;
    }

    // Usando a API de Geocodificação do Nominatim (OpenStreetMap)
    const url = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(
      address
    )}`;

    fetch(url)
      .then((response) => response.json())
      .then((data) => {
        if (data && data.length > 0) {
          const firstResult = data[0];
          const lat = firstResult.lat;
          const lon = firstResult.lon;
          const displayName = firstResult.display_name;

          document.getElementById("formattedAddress").textContent = displayName;
          document.getElementById(
            "searchedCoords"
          ).textContent = `${lat}, ${lon}`;
          addressData.style.display = "block";

          // Atualizar o mapa
          showMap(lat, lon);
        } else {
          alert("Nenhum resultado encontrado para este endereço.");
        }
      })
      .catch((error) => {
        console.error("Erro ao buscar endereço:", error);
        alert("Ocorreu um erro ao buscar o endereço. Tente novamente.");
      });
  }

  // Event Listeners
  getLocationBtn.addEventListener("click", getLocation);
  searchAddressBtn.addEventListener("click", searchAddress);

  // Permitir busca com Enter
  document
    .getElementById("addressInput")
    .addEventListener("keypress", function (e) {
      if (e.key === "Enter") {
        searchAddress();
      }
    });
});
