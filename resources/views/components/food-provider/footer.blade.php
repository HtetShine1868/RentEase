<footer class="bg-white border-t border-gray-200">
    <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 md:px-8">
        <div class="flex flex-col md:flex-row justify-between items-center">
            <div class="text-sm text-gray-500">
                <p>&copy; {{ date('Y') }} Rent & Service Management System. All rights reserved.</p>
                <p class="mt-1 text-xs text-gray-400">
                    <i class="fas fa-code-branch mr-1"></i> Version 1.0.0 • 
                    <i class="fas fa-database ml-2 mr-1"></i> Last sync: {{ now()->format('h:i A') }}
                </p>
            </div>
            <div class="mt-2 md:mt-0">
                <div class="flex items-center space-x-4">
                    <span class="inline-flex items-center text-sm text-gray-500">
                        <i class="fas fa-shield-alt mr-2"></i>
                        Secure Provider Dashboard
                    </span>
                    <div class="h-4 w-px bg-gray-300"></div>
                    <a href="#" class="text-sm text-indigo-600 hover:text-indigo-500">
                        <i class="fas fa-question-circle mr-1"></i> Help
                    </a>
                </div>
            </div>
        </div>
    </div>
</footer>