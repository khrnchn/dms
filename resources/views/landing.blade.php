<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>DocuEase - Transform Your Document Management</title>
    @vite('resources/css/app.css')
</head>

<body class="min-h-screen antialiased bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm fixed w-full top-0 z-50">
        <nav class="container mx-auto px-6 py-4 flex justify-between items-center">
            <div class="text-2xl font-bold text-gray-800">DocuEase</div>
            <div class="hidden md:flex space-x-6">
                <a href="#features" class="text-gray-600 hover:text-gray-800">Features</a>
                <a href="#benefits" class="text-gray-600 hover:text-gray-800">Benefits</a>
                <a href="#modules" class="text-gray-600 hover:text-gray-800">Modules</a>
            </div>
            <a href="/login" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition duration-300">
                Get Started
            </a>
        </nav>
    </header>

    <main class="pt-16">
        <!-- Hero Section -->
        <section class="py-20 bg-gradient-to-br from-blue-600 to-indigo-700 text-white">
            <div class="container mx-auto px-6 text-center">
                <h1 class="text-5xl font-bold mb-6">Transform Your Document Management</h1>
                <p class="text-xl mb-8 max-w-2xl mx-auto">
                    Streamline your organization's document handling with automated storage,
                    enhanced security, and efficient collaboration tools.
                </p>
                <a href="/login" class="bg-white text-blue-600 px-8 py-3 rounded-lg text-lg font-semibold hover:bg-gray-100 transition duration-300 inline-flex items-center">
                    Start Digital Transformation
                </a>
            </div>
        </section>

        <!-- Features Section -->
        <section id="features" class="py-20 bg-white">
            <div class="container mx-auto px-6">
                <h2 class="text-3xl font-bold text-center mb-12">Key Features</h2>
                <div class="grid md:grid-cols-3 gap-8">
                    <div class="p-6 rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition duration-300">
                        <div class="flex items-center mb-4">
                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <h3 class="text-xl font-semibold ml-3">Smart Organization</h3>
                        </div>
                        <p class="text-gray-600">Advanced indexing and search capabilities for quick document retrieval and efficient categorization.</p>
                    </div>

                    <div class="p-6 rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition duration-300">
                        <div class="flex items-center mb-4">
                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                            <h3 class="text-xl font-semibold ml-3">Role-Based Security</h3>
                        </div>
                        <p class="text-gray-600">Granular access controls and comprehensive audit logging to protect sensitive information.</p>
                    </div>

                    <div class="p-6 rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition duration-300">
                        <div class="flex items-center mb-4">
                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <h3 class="text-xl font-semibold ml-3">Version Control</h3>
                        </div>
                        <p class="text-gray-600">Track document changes and maintain complete version history with easy restoration options.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- System Modules Section -->
        <section id="modules" class="py-20 bg-gray-50">
            <div class="container mx-auto px-6">
                <h2 class="text-3xl font-bold text-center mb-12">System Modules</h2>
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div class="bg-white p-6 rounded-xl shadow-sm hover:shadow-md transition duration-300">
                        <svg class="w-8 h-8 text-blue-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        <h3 class="text-lg font-semibold mb-2">User Management</h3>
                        <p class="text-gray-600">Control user roles and permissions with ease</p>
                    </div>

                    <div class="bg-white p-6 rounded-xl shadow-sm hover:shadow-md transition duration-300">
                        <svg class="w-8 h-8 text-blue-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <h3 class="text-lg font-semibold mb-2">Document Archives</h3>
                        <p class="text-gray-600">Secure storage and efficient retrieval system</p>
                    </div>

                    <div class="bg-white p-6 rounded-xl shadow-sm hover:shadow-md transition duration-300">
                        <svg class="w-8 h-8 text-blue-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <h3 class="text-lg font-semibold mb-2">Indexing & Search</h3>
                        <p class="text-gray-600">Quick access to documents with advanced search</p>
                    </div>

                    <div class="bg-white p-6 rounded-xl shadow-sm hover:shadow-md transition duration-300">
                        <svg class="w-8 h-8 text-blue-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h3 class="text-lg font-semibold mb-2">Audit Logs</h3>
                        <p class="text-gray-600">Track all user activities and modifications</p>
                    </div>

                    <div class="bg-white p-6 rounded-xl shadow-sm hover:shadow-md transition duration-300">
                        <svg class="w-8 h-8 text-blue-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                        </svg>
                        <h3 class="text-lg font-semibold mb-2">Document Distribution</h3>
                        <p class="text-gray-600">Streamlined sharing and delivery process</p>
                    </div>

                    <div class="bg-white p-6 rounded-xl shadow-sm hover:shadow-md transition duration-300">
                        <svg class="w-8 h-8 text-blue-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h3 class="text-lg font-semibold mb-2">Document Verification</h3>
                        <p class="text-gray-600">Ensure document authenticity and integrity</p>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8">
        <div class="container mx-auto px-6 text-center">
            <p>&copy; 2024 DocuEase. All rights reserved.</p>
        </div>
    </footer>
</body>

</html>