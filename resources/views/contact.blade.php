<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"="width=device-width, initial-scale=1.0">
    <title>Contact - CoDelivery</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f5f3ff',
                            100: '#ede9fe',
                            200: '#ddd6fe',
                            500: '#8b5cf6',
                            600: '#7c3aed',
                            700: '#6d28d9',
                        },
                        accent: {
                            50: '#fff1f2',
                            100: '#ffe4e6',
                            500: '#f43f5e',
                            600: '#e11d48',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-white">
        <nav class="bg-orange-950 text-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <svg class="w-12 h-12" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M32 4c-11 0-20 9-20 20 0 11 20 36 20 36s20-25 20-36c0-11-9-20-20-20z" fill="#EA580C"/>
                            <circle cx="32" cy="24" r="12" fill="#FB923C"/>
                            <rect x="24" y="18" width="16" height="12" fill="#FFFFFF"/>
                            <path d="M24 22h16M32 18v12" stroke="#EA580C" stroke-width="1.5"/>
                            <path d="M14 44l-6 6M50 44l6 6" stroke="#FB923C" stroke-width="2.5" stroke-linecap="round"/>
                        </svg>
                        <span class="text-2xl font-bold"><a href="/">CoDelivery</a></span>
                    </div>
                    <div class="hidden md:flex items-center space-x-8">
                        <a href="#services" class="text-white hover:text-orange-300 transition-colors">Services</a>
                        <a href="#how-it-works" class="text-white hover:text-orange-300 transition-colors">Comment ça marche</a>
                        <a href="contact" class="text-white hover:text-orange-300 transition-colors">Contact</a>
                        <a href="login">
                        <button class="px-6 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-full transition-all">
                            Connexion
                        </button>
                        </a>    
                    </div>
                </div>
            </div>
        </nav>

    <div class="bg-gradient-to-b from-orange-950 to-orange-900 py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="text-4xl md:text-5xl font-bold text-white mb-6">Contactez-nous</h1>
                <p class="text-xl text-gray-300 max-w-2xl mx-auto">Notre équipe est là pour répondre à toutes vos questions et vous accompagner dans vos besoins de livraison.</p>
            </div>
        </div>
    </div>

    <section class="py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-16">
                <div class="bg-white rounded-2xl shadow-xl p-8">
                    <h2 class="text-2xl font-bold mb-6">Envoyez-nous un message</h2>
                    <form>
                        <div class="space-y-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nom complet</label>
                                <input type="text" id="name" name="name" 
                                       class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-orange-500">
                            </div>
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                <input type="email" id="email" name="email" 
                                       class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-orange-500">
                            </div>
                            <div>
                                <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">Sujet</label>
                                <select id="subject" name="subject" 
                                        class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-orange-500">
                                    <option>Demande d'information</option>
                                    <option>Support technique</option>
                                    <option>Partenariat</option>
                                    <option>Autre</option>
                                </select>
                            </div>
                            <div>
                                <label for="message" class="block text-sm font-medium text-gray-700 mb-2">Message</label>
                                <textarea id="message" name="message" rows="4" 
                                          class="w-full px-4 py-3 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-orange-500"></textarea>
                            </div>
                            <button type="submit" class="w-full px-6 py-3 bg-orange-500 hover:bg-orange-600 text-white rounded-xl transition-all font-medium">
                                Envoyer le message
                            </button>
                        </div>
                    </form>
                </div>

                <div class="space-y-8">
                    <div>
                        <h2 class="text-2xl font-bold mb-6">Informations de contact</h2>
                        <div class="space-y-4">
                            <div class="flex items-start space-x-4">
                                <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                                    <i class="fa-solid fa-location-dot text-orange-500"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold">Adresse</h3>
                                    <p class="text-gray-600">123 Rue de la Livraison<br>75001 Paris, France</p>
                                </div>
                            </div>
                            <div class="flex items-start space-x-4">
                                <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                                    <i class="fa-solid fa-phone text-orange-500"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold">Téléphone</h3>
                                    <p class="text-gray-600">+33 1 23 45 67 89</p>
                                </div>
                            </div>
                            <div class="flex items-start space-x-4">
                                <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                                    <i class="fa-solid fa-envelope text-orange-500"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold">Email</h3>
                                    <p class="text-gray-600">contact@codelivery.com</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-100 rounded-2xl p-4">
                        <iframe
                            class="w-full h-[300px] rounded-xl"
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2624.9916256937604!2d2.292292615509614!3d48.85837007928757!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47e66e2964e34e2d%3A0x8ddca9ee380ef7e0!2sTour%20Eiffel!5e0!3m2!1sfr!2sfr!4v1647856687721!5m2!1sfr!2sfr"
                            style="border:0;" 
                            allowfullscreen="" 
                            loading="lazy">
                        </iframe>
                    </div>

                    <div>
                        <h3 class="text-xl font-bold mb-4">Suivez-nous</h3>
                        <div class="flex space-x-4">
                            <a href="#" class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center hover:bg-orange-200 transition-colors">
                                <i class="fab fa-facebook-f text-orange-500"></i>
                            </a>
                            <a href="#" class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center hover:bg-orange-200 transition-colors">
                                <i class="fab fa-twitter text-orange-500"></i>
                            </a>
                            <a href="#" class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center hover:bg-orange-200 transition-colors">
                                <i class="fab fa-linkedin-in text-orange-500"></i>
                            </a>
                            <a href="#" class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center hover:bg-orange-200 transition-colors">
                                <i class="fab fa-instagram text-orange-500"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="bg-orange-950 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12">
                <div class="space-y-6">
                    <div class="flex items-center space-x-2">
                       <svg class="w-12 h-12" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M32 4c-11 0-20 9-20 20 0 11 20 36 20 36s20-25 20-36c0-11-9-20-20-20z" fill="#EA580C"/>
                            <circle cx="32" cy="24" r="12" fill="#FB923C"/>
                            <rect x="24" y="18" width="16" height="12" fill="#FFFFFF"/>
                            <path d="M24 22h16M32 18v12" stroke="#EA580C" stroke-width="1.5"/>
                            <path d="M14 44l-6 6M50 44l6 6" stroke="#FB923C" stroke-width="2.5" stroke-linecap="round"/>
                        </svg>
                        <span class="text-2xl font-bold">CoDelivery</span>
                    </div>
                    <p class="text-gray-400">Votre partenaire de livraison intelligent disponible 24/7 pour tous vos besoins.</p>
                    <div class="flex space-x-4">
                        <a href="#" class="w-10 h-10 bg-orange-900 rounded-full flex items-center justify-center hover:bg-orange-800 transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                        </a>
                        <a href="#" class="w-10 h-10 bg-orange-900 rounded-full flex items-center justify-center hover:bg-orange-800 transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                            </svg>
                        </a>
                        <a href="#" class="w-10 h-10 bg-orange-900 rounded-full flex items-center justify-center hover:bg-orange-800 transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 0C8.74 0 8.333.015 7.053.072 5.775.132 4.905.333 4.14.63c-.789.306-1.459.717-2.126 1.384S.935 3.35.63 4.14C.333 4.905.131 5.775.072 7.053.012 8.333 0 8.74 0 12s.015 3.667.072 4.947c.06 1.277.261 2.148.558 2.913.306.788.717 1.459 1.384 2.126.667.666 1.336 1.079 2.126 1.384.766.296 1.636.499 2.913.558C8.333 23.988 8.74 24 12 24s3.667-.015 4.947-.072c1.277-.06 2.148-.262 2.913-.558.788-.306 1.459-.718 2.126-1.384.666-.667 1.079-1.335 1.384-2.126.296-.765.499-1.636.558-2.913.06-1.28.072-1.687.072-4.947s-.015-3.667-.072-4.947c-.06-1.277-.262-2.149-.558-2.913-.306-.789-.718-1.459-1.384-2.126C21.319 1.347 20.651.935 19.86.63c-.765-.297-1.636-.499-2.913-.558C15.667.012 15.26 0 12 0zm0 2.16c3.203 0 3.585.016 4.85.071 1.17.055 1.805.249 2.227.415.562.217.96.477 1.382.896.419.42.679.819.896 1.381.164.422.36 1.057.413 2.227.057 1.266.07 1.646.07 4.85s-.015 3.585-.074 4.85c-.061 1.17-.256 1.805-.421 2.227-.224.562-.479.96-.899 1.382-.419.419-.824.679-1.38.896-.42.164-1.065.36-2.235.413-1.274.057-1.649.07-4.859.07-3.211 0-3.586-.015-4.859-.074-1.171-.061-1.816-.256-2.236-.421-.569-.224-.96-.479-1.379-.899-.421-.419-.69-.824-.9-1.38-.165-.42-.359-1.065-.42-2.235-.045-1.26-.061-1.649-.061-4.844 0-3.196.016-3.586.061-4.861.061-1.17.255-1.814.42-2.234.21-.57.479-.96.9-1.381.419-.419.81-.689 1.379-.898.42-.166 1.051-.361 2.221-.421 1.275-.045 1.65-.06 4.859-.06l.045.03zm0 3.678c-3.405 0-6.162 2.76-6.162 6.162 0 3.405 2.76 6.162 6.162 6.162 3.405 0 6.162-2.76 6.162-6.162 0-3.405-2.76-6.162-6.162-6.162zM12 16c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4zm7.846-10.405c0 .795-.646 1.44-1.44 1.44-.795 0-1.44-.646-1.44-1.44 0-.794.646-1.439 1.44-1.439.793-.001 1.44.645 1.44 1.439z"/>
                            </svg>
                        </a>
                    </div>
                </div>

                <div>
                    <h3 class="text-lg font-semibold mb-6">Liens rapides</h3>
                    <ul class="space-y-4">
                        <li><a href="#" class="text-gray-400 hover:text-orange-500 transition-colors">Accueil</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-orange-500 transition-colors">Services</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-orange-500 transition-colors">À propos</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-orange-500 transition-colors">Contact</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-lg font-semibold mb-6">Nos services</h3>
                    <ul class="space-y-4">
                        <li><a href="#" class="text-gray-400 hover:text-orange-500 transition-colors">Livraison restaurant</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-orange-500 transition-colors">Livraison pharmacie</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-orange-500 transition-colors">Livraison courses</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-orange-500 transition-colors">Livraison colis</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-lg font-semibold mb-6">Contact</h3>
                    <ul class="space-y-4">
                        <li class="flex items-center space-x-3 text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span>123 Rue de la Livraison, Paris</span>
                        </li>
                        <li class="flex items-center space-x-3 text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <span>contact@codelivery.com</span>
                        </li>
                        <li class="flex items-center space-x-3 text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            <span>+33 1 23 45 67 89</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="border-t border-orange-900">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="md:flex md:items-center md:justify-between">
                    <div class="text-sm text-gray-400">
                        © 2025 CoDelivery. Tous droits réservés.
                    </div>
                    <div class="flex space-x-6 mt-4 md:mt-0">
                        <a href="#" class="text-sm text-gray-400 hover:text-orange-500 transition-colors">Confidentialité</a>
                        <a href="#" class="text-sm text-gray-400 hover:text-orange-500 transition-colors">Conditions d'utilisation</a>
                        <a href="#" class="text-sm text-gray-400 hover:text-orange-500 transition-colors">Mentions légales</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
