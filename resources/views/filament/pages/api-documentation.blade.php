<x-filament-panels::page>
    <style>
        .method-badge {
            font-weight: 700;
            font-size: 0.75rem;
            padding: 0.25rem 0.625rem;
            border-radius: 0.25rem;
            text-align: center;
            display: inline-block;
        }
        .method-get { background: #49cc90; color: white; }
        .method-post { background: #61affe; color: white; }
        .method-put { background: #fca130; color: white; }
        .method-delete { background: #f93e3e; color: white; }
        .endpoint-header {
            cursor: pointer;
            transition: background-color 0.15s ease;
            background-color: rgb(243, 244, 246);
        }
        .dark .endpoint-header {
            background-color: rgb(31, 41, 55);
        }
        .endpoint-header:hover {
            background-color: rgb(229, 231, 235);
        }
        .dark .endpoint-header:hover {
            background-color: rgb(55, 65, 81);
        }
        .json-code {
            direction: ltr;
            text-align: left;
            font-family: 'Monaco', 'Courier New', monospace;
            font-size: 0.875rem;
            line-height: 1.5;
        }
        .rotate-180 {
            transform: rotate(180deg);
        }
        .transition-transform {
            transition: transform 0.2s ease;
        }
        .chevron-icon {
            width: 1.25rem !important;
            height: 1.25rem !important;
            flex-shrink: 0;
        }
    </style>

    <!-- Header -->
    <div class="mb-8 text-center">
        <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-2">API Documentation</h1>
        <p class="text-gray-600 dark:text-gray-400 mb-4">RESTful API endpoints</p>
        <div class="inline-flex items-center gap-2 bg-gray-100 dark:bg-gray-800 px-4 py-2 rounded-lg">
            <span class="text-sm text-gray-600 dark:text-gray-400">Base URL:</span>
            <code class="text-sm font-mono text-primary-600 dark:text-primary-400">{{ url('/api/v1') }}</code>
        </div>
    </div>

    <div class="space-y-8" dir="ltr">
        @foreach($this->getApiRoutes() as $group)
            <!-- Group Title -->
            <div class="text-left">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">{{ $group['group'] }}</h2>

                <!-- Endpoints -->
                <div>
                    @foreach($group['routes'] as $index => $route)
                        <div x-data="{ open: false }" class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden bg-white dark:bg-gray-900 mb-6" style="margin-bottom: 1.5rem;">
                            <!-- Collapsible Header -->
                            <div @click="open = !open" class="endpoint-header flex flex-row items-center gap-4" style="display: flex; flex-direction: row; align-items: center; padding: 2rem;">
                                <span class="method-badge method-{{ strtolower($route['method']) }}" style="flex-shrink: 0;">
                                    {{ $route['method'] }}
                                </span>
                                <span class="text-base font-semibold text-gray-900 dark:text-white" style="flex: 1; padding-left: 1rem;">{{ $route['description'] }}</span>
                                <svg class="chevron-icon text-gray-500 transition-transform" :class="open && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="flex-shrink: 0; width: 1.25rem; height: 1.25rem;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>

                            <!-- Collapsible Content -->
                            <div x-show="open" x-collapse>
                                <!-- Labels Section with Light Background -->
                                <div class="border-t border-gray-200 dark:border-gray-700 p-6 bg-gray-50 dark:bg-gray-800 space-y-1" >
                                    <h3 class="text-sm font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wide">Endpoint</h3>
                                </div>
                                
                                <!-- Code Section with Dark Background -->
                                <div class="p-6 bg-gray-900 dark:bg-black" >
                                    <code class="json-code text-green-400 font-mono text-base">{{ $route['path'] }}</code>
                                </div>

                                <!-- Parameters -->
                                @if(!empty($route['parameters']))
                                    <!-- Labels Section -->
                                    <div class="p-6 bg-gray-50 dark:bg-gray-800" >
                                        <h3 class="text-sm font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wide">Parameters</h3>
                                    </div>
                                    
                                    <!-- Code Section -->
                                    <div class="p-6 bg-blue-50 dark:bg-blue-950" >
                                        <pre class="json-code text-gray-800 dark:text-blue-200 overflow-x-auto">{{ json_encode($route['parameters'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                    </div>
                                @endif

                                <!-- Example Response Labels Section -->
                                <div class="p-6 bg-gray-50 dark:bg-gray-800">
                                    <div class="flex items-center gap-2">
                                        <h3 class="text-sm font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wide" >Example Response</h3>
                                        <span class="text-xs bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 px-2 py-1 rounded font-semibold" >200 OK</span>
                                    </div>
                                </div>
                                
                                <!-- Example Response Code Section -->
                                <div class="p-6 bg-gray-900 dark:bg-black border-t-0" style="background-color: rgb(31, 41, 55);">
                                    <pre class="json-code text-green-400 overflow-x-auto">{{ json_encode($route['sample_response'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</x-filament-panels::page>
