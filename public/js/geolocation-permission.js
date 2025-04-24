/**
 * Geolocation Permission Handler
 * Handles requesting, verifying, and storing user geolocation
 */

class GeolocationPermission {
    constructor() {
        this.locationData = null;
        this.map = null;
        this.marker = null;
    }

    /**
     * Request geolocation permission and show confirmation modal
     */
    requestPermission() {
        // Show the permission modal
        document.getElementById('locationPermissionModal').classList.remove('hidden');
        
        // Handle allow button click
        document.getElementById('allowLocationBtn').addEventListener('click', () => {
            this.getLocation();
        });
        
        // Handle deny button click
        document.getElementById('denyLocationBtn').addEventListener('click', () => {
            this.handleDenied();
        });
    }
    
    /**
     * Get the user's current location
     */
    getLocation() {
        if (navigator.geolocation) {
            // Hide permission modal
            document.getElementById('locationPermissionModal').classList.add('hidden');
            
            // Show loading
            document.getElementById('locationLoadingModal').classList.remove('hidden');
            
            navigator.geolocation.getCurrentPosition(
                (position) => this.handleLocationSuccess(position),
                (error) => this.handleLocationError(error),
                { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
            );
        } else {
            this.showError("Geolocation is not supported by this browser.");
        }
    }
    
    /**
     * Handle successful location retrieval
     */
    handleLocationSuccess(position) {
        // Store location data
        this.locationData = {
            latitude: position.coords.latitude,
            longitude: position.coords.longitude,
            accuracy: position.coords.accuracy
        };
        
        // Hide loading
        document.getElementById('locationLoadingModal').classList.add('hidden');
        
        // Show confirmation modal
        document.getElementById('locationConfirmModal').classList.remove('hidden');
        
        // Initialize map preview
        this.initMapPreview();
        
        // Handle confirm button click
        document.getElementById('confirmLocationBtn').addEventListener('click', () => {
            this.confirmLocation();
        });
        
        // Handle retry button click
        document.getElementById('retryLocationBtn').addEventListener('click', () => {
            this.getLocation();
        });
    }
    
    /**
     * Initialize map preview in confirmation modal
     */
    initMapPreview() {
        if (!this.map) {
            this.map = L.map('locationPreviewMap').setView(
                [this.locationData.latitude, this.locationData.longitude], 
                15
            );
            
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(this.map);
        } else {
            this.map.setView([this.locationData.latitude, this.locationData.longitude], 15);
        }
        
        // Add or update marker
        if (this.marker) {
            this.marker.setLatLng([this.locationData.latitude, this.locationData.longitude]);
        } else {
            this.marker = L.marker([this.locationData.latitude, this.locationData.longitude]).addTo(this.map);
        }
        
        // Force map to recalculate size
        setTimeout(() => {
            this.map.invalidateSize();
        }, 100);
    }
    
    /**
     * Handle location error
     */
    handleLocationError(error) {
        document.getElementById('locationLoadingModal').classList.add('hidden');
        
        let errorMessage = "Unknown error occurred.";
        switch(error.code) {
            case error.PERMISSION_DENIED:
                errorMessage = "User denied the request for geolocation.";
                break;
            case error.POSITION_UNAVAILABLE:
                errorMessage = "Location information is unavailable.";
                break;
            case error.TIMEOUT:
                errorMessage = "The request to get user location timed out.";
                break;
        }
        
        this.showError(errorMessage);
    }
    
    /**
     * Handle denied permission
     */
    handleDenied() {
        document.getElementById('locationPermissionModal').classList.add('hidden');
        this.showError("Location permission is required for tracking.");
    }
    
    /**
     * Confirm location and proceed
     */
    confirmLocation() {
        // Store location in session storage
        sessionStorage.setItem('userLocation', JSON.stringify(this.locationData));
        
        // Hide confirmation modal
        document.getElementById('locationConfirmModal').classList.add('hidden');
        
        // Redirect to tracking page or continue with form submission
        const form = document.getElementById('loginForm');
        if (form) {
            // Add location data to form
            const locationInput = document.createElement('input');
            locationInput.type = 'hidden';
            locationInput.name = 'location';
            locationInput.value = JSON.stringify(this.locationData);
            form.appendChild(locationInput);
            
            // Submit the form
            form.submit();
        }
    }
    
    /**
     * Show error message
     */
    showError(message) {
        const errorModal = document.getElementById('locationErrorModal');
        document.getElementById('locationErrorMessage').textContent = message;
        errorModal.classList.remove('hidden');
        
        // Handle close button
        document.getElementById('closeErrorBtn').addEventListener('click', () => {
            errorModal.classList.add('hidden');
        });
    }
}

// Create global instance
window.geolocationPermission = new GeolocationPermission();
