<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'Hotel Management System') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        <!-- Styles -->
        <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    </head>
    <body class="bg-gray-100 text-gray-800">
        <!-- Navigation Bar -->
        <nav id="navbar" class="fixed top-0 left-0 w-full bg-transparent z-50 transition-transform transform -translate-y-full hover:translate-y-0">
            <div class="container mx-auto px-6 lg:px-20 py-4 flex justify-between items-center">
                <a href="/" class="text-xl font-bold text-white">{{ config('app.name', 'Hotel Management System') }}</a>
                @if (Route::has('login'))
                    <div class="flex space-x-4">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="text-sm text-white hover:text-yellow-500 transition-colors">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="text-sm text-white hover:text-yellow-500 transition-colors">Log in</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="text-sm text-white hover:text-yellow-500 transition-colors">Register</a>
                            @endif
                        @endauth
                    </div>
                @endif
            </div>
        </nav>

        <script>
            let lastScrollTop = 0;
            const navbar = document.getElementById('navbar');
            navbar.classList.remove('transform', '-translate-y-full'); 

            window.addEventListener('scroll', () => {
                const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                if (scrollTop > lastScrollTop) {
                    navbar.classList.add('transform', '-translate-y-full'); 
                } else {
                    navbar.classList.remove('transform', '-translate-y-full'); 
                }
                lastScrollTop = scrollTop;
            });
        </script>

        <!-- Hero Section -->
        <header class="bg-cover bg-center h-screen" style="background-image: url('https://images.unsplash.com/photo-1660557989695-14fac79c086d?q=80&w=764&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D');">
            <div class="flex items-center justify-center h-full bg-black bg-opacity-50">
                <div class="text-center text-white">
                    <h1 class="text-4xl lg:text-6xl font-bold mb-4">Welcome to {{ config('app.name', 'Hotel Management System') }}</h1>
                    <p class="text-lg lg:text-xl mb-6">Experience luxury and comfort at its finest.</p>
                    <a href="{{ route('user.bookings.index') }}" class="bg-yellow-500 text-white px-6 py-3 rounded-lg text-lg hover:bg-yellow-600">Book Now</a>
                </div>
            </div>
        </header>    

        <!-- Features Section -->
        <section class="py-16 bg-white" id="features">
            <div class="container mx-auto px-6 lg:px-20">
                <h2 class="text-3xl font-bold text-center mb-12">Why Choose Us</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="text-center">
                        <img src="https://images.unsplash.com/photo-1618773928121-c32242e63f39?q=80&w=800&h=600&auto=format&fit=crop" alt="Hotel Image">
                        <h3 class="text-xl font-semibold mb-2">Luxury Rooms</h3>
                        <p>Enjoy our elegantly designed rooms with top-notch amenities.</p>
                    </div>
                    <div class="text-center">
                        <img src="https://images.unsplash.com/photo-1694021408920-922ff450c525?q=80&w=800&h=600&auto=format&fit=crop" alt="Hotel Image">
                        <h3 class="text-xl font-semibold mb-2">Fine Dining</h3>
                        <p>Savor gourmet meals prepared by world-class chefs.</p>
                    </div>
                    <div class="text-center">
                        <img src="https://images.unsplash.com/photo-1515377905703-c4788e51af15?q=80&w=800&h=600&auto=format&fit=crop" alt="Hotel Image">
                        <h3 class="text-xl font-semibold mb-2">Relaxing Spa</h3>
                        <p>Unwind with our rejuvenating spa treatments.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Room Previews Section -->
        <section class="py-16 bg-gray-100" id="rooms">
            <div class="container mx-auto px-6 lg:px-20">
                <h2 class="text-3xl font-bold text-center mb-12">Explore Our Rooms</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1631049307264-da0ec9d70304?q=80&w=1170&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" alt="Hotel Image">
                        <div class="p-6">
                            <h3 class="text-xl font-semibold mb-2">Deluxe Room</h3>
                            <p class="text-gray-600 mb-4">Spacious and comfortable with a stunning view.</p>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1611892440504-42a792e24d32?q=80&w=1170&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" alt="Hotel Image">
                        <div class="p-6">
                            <h3 class="text-xl font-semibold mb-2">Executive Suite</h3>
                            <p class="text-gray-600 mb-4">Perfect for business travelers and families.</p>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                        <img src="https://plus.unsplash.com/premium_photo-1661963657190-ecdd1ca794f9?q=80&w=1129&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" alt="Hotel Image">
                        <div class="p-6">
                            <h3 class="text-xl font-semibold mb-2">Presidential Suite</h3>
                            <p class="text-gray-600 mb-4">Experience the pinnacle of luxury and service.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Call to Action Section -->
        <section class="py-16 bg-yellow-500 text-white">
            <div class="container mx-auto px-6 lg:px-20 text-center">
                <h2 class="text-3xl font-bold mb-4">Ready to Book Your Stay?</h2>
                <p class="text-lg mb-6">Reserve your room today and enjoy an unforgettable experience.</p>
                <a href="{{ route('user.bookings.index') }}" class="bg-white text-yellow-500 px-6 py-3 rounded-lg text-lg hover:bg-gray-100">Book Now</a>
            </div>
        </section>

        <!-- Footer -->
        <footer class="py-6 bg-gray-800 text-gray-400 text-center">
            <p>&copy; {{ date('Y') }} {{ config('app.name', 'Hotel Management System') }}. All rights reserved.</p>
        </footer>
    </body>
</html>
