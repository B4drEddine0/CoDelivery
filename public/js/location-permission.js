/**
 * Location Permission System
 * Handles requesting, verifying, and storing user location
 */
class LocationPermissionSystem {
    constructor(options = {}) {
        this.options = {
            forceShow: false,
            onLocationConfirmed: null,
            onLocationDenied: null,
            ...options
        };
        
        this.currentPosition = null;
        this.modalId = 'locationPermissionModal';
        this.confirmModalId = 'locationConfirmModal';
        
        this.init();
    }
    
    init() {
        // Create modals if they don't exist
        this.createModals();
        
        // Check if we should show the permission modal
        if (this.options.forceShow || !this.hasLocationPermission()) {
            this.showPermissionModal();
        }
        
        // Add event listeners
        this.addEventListeners();
    }
    
    createModals() {
        // Create permission request modal
        if (!document.getElementById(this.modalId)) {
            const permissionModal = document.createElement('div');
            permissionModal.id = this.modalId;
            permissionModal.className = 'fixed inset-0 bg-gray-600 bg-opacity-75 flex items-center justify-center z-50 hidden';
            permissionModal.innerHTML = `
                <div class="bg-white rounded-lg p-6 max-w-md mx-auto">
                    <h2 class="text-xl font-bold mb-4">Autorisation de localisation</h2>
                    <p class="mb-4">Pour une meilleure expérience, CoDelivery a besoin d'accéder à votre position actuelle.</p>
                    <div class="flex justify-end space-x-2">
                        <button id="denyLocationBtn" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">Refuser</button>
                        <button id="allowLocationBtn" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Autoriser</button>
                    </div>
                </div>
            `;
            document.body.appendChild(permissionModal);
        }
        
        // Create location confirmation modal
        if (!document.getElementById(this.confirmModalId)) {
            const confirmModal = document.createElement('div');
            confirmModal.id = this.confirmModalId;
            confirmModal.className = 'fixed inset-0 bg-gray-600 bg-opacity-75 flex items-center justify-center z-50 hidden';
            confirmModal.innerHTML = `
                <div class="bg-white rounded-lg p-6 max-w-md mx-auto">
                    <h2 class="text-xl font-bold mb-4">Confirmer votre position</h2>
                    <p class="mb-4">Est-ce votre position actuelle?</p>
                    <div id="locationPreviewMap" class="h-48 mb-4 rounded border"></div>
                    <div class="flex justify-end space-x-2">
                        <button id="retryLocationBtn" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">Réessayer</button>
                        <button id="confirmLocationBtn" class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">Confirmer</button>
                    </div>
                </div>
            `;
            document.body.appendChild(confirmModal);
        }
    }
    
    addEventListeners() {
        // Permission modal buttons
        document.getElementById('allowLocationBtn').addEventListener('click', () => this.requestLocation());
        document.getElementById('denyLocationBtn').addEventListener('click', () => this.handleLocationDenied());
        
        // Confirmation modal buttons
        document.getElementById('confirmLocationBtn').addEventListener('click', () => this.confirmLocation());
        document.getElementById('retryLocationBtn').addEventListener('click', () => this.requestLocation());
    }
    
    showPermissionModal() {
        document.getElementById(this.modalId).classList.remove('hidden');
    }
    
    hidePermissionModal() {
        document.getElementById(this.modalId).classList.add('hidden');
    }
    
    showConfirmModal() {
        document.getElementById(this.confirmModalId).classList.remove('hidden');
        this.initLocationPreviewMap();
    }
    
    hideConfirmModal() {
        document.getElementById(this.confirmModalId).classList.add('hidden');
    }
    
    requestLocation() {
        this.hidePermissionModal();
        
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (position) => this.handleLocationSuccess(position),
                (error) => this.handleLocationError(error),
                { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
            );
        } else {
            this.handleLocationError({ code: 0, message: 'Geolocation is not supported by this browser.' });
        }
    }
    
    handleLocationSuccess(position) {
        this.currentPosition = {
            latitude: position.coords.latitude,
            longitude: position.coords.longitude,
            accuracy: position.coords.accuracy
        };
        
        // Store in session
        sessionStorage.setItem('userLocation', JSON.stringify(this.currentPosition));
        
        // Show confirmation modal
        this.showConfirmModal();
    }
    
    handleLocationError(error) {
        console.error('Error getting location:', error);
        
        let errorMessage = 'Une erreur est survenue lors de la récupération de votre position.';
        
        switch(error.code) {
            case error.PERMISSION_DENIED:
                errorMessage = 'Vous avez refusé l\'accès à votre position.';
                break;
            case error.POSITION_UNAVAILABLE:
                errorMessage = 'Les informations de position ne sont pas disponibles.';
                break;
            case error.TIMEOUT:
                errorMessage = 'La demande de position a expiré.';
                break;
        }
        
        alert(errorMessage);
        this.handleLocationDenied();
    }
    
    initLocationPreviewMap() {
        if (!this.currentPosition) return;
        
        // Initialize preview map
        const mapContainer = document.getElementById('locationPreviewMap');
        
        // Clear previous map if exists
        mapContainer.innerHTML = '';
        
        // Create map
        const map = L.map(mapContainer).setView(
            [this.currentPosition.latitude, this.currentPosition.longitude], 
            15
        );
        
        // Add tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);
        
        // Add marker
        L.marker([this.currentPosition.latitude, this.currentPosition.longitude])
            .addTo(map)
            .bindPopup('Votre position')
            .openPopup();
    }
    
    confirmLocation() {
        this.hideConfirmModal();
        
        // Set permission as granted
        localStorage.setItem('locationPermissionGranted', 'true');
        
        // Trigger callback if provided
        if (typeof this.options.onLocationConfirmed === 'function') {
            this.options.onLocationConfirmed(this.currentPosition);
        }
        
        // Dispatch custom event
        document.dispatchEvent(new CustomEvent('locationConfirmed', {
            detail: this.currentPosition
        }));
    }
    
    handleLocationDenied() {
        this.hidePermissionModal();
        this.hideConfirmModal();
        
        // Set permission as denied
        localStorage.setItem('locationPermissionGranted', 'false');
        
        // Trigger callback if provided
        if (typeof this.options.onLocationDenied === 'function') {
            this.options.onLocationDenied();
        }
        
        // Dispatch custom event
        document.dispatchEvent(new CustomEvent('locationDenied'));
    }
    
    hasLocationPermission() {
        return localStorage.getItem('locationPermissionGranted') === 'true';
    }
    
    getUserLocation() {
        const storedLocation = sessionStorage.getItem('userLocation');
        return storedLocation ? JSON.parse(storedLocation) : null;
    }
}

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = LocationPermissionSystem;
}
