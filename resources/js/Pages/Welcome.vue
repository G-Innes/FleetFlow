<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
});

function handleImageError() {
    document.getElementById('screenshot-container')?.classList.add('!hidden');
    document.getElementById('docs-card')?.classList.add('!row-span-1');
    document.getElementById('docs-card-content')?.classList.add('!flex-row');
    document.getElementById('background')?.classList.add('!hidden');
}
</script>

<template>
    <Head title="Fleet Fox Task Management" />
    <div class="bg-fleet-dark text-fleet-text min-h-screen">
        <div class="relative flex min-h-screen flex-col items-center justify-center">
            <div class="relative w-full max-w-4xl px-6 lg:max-w-7xl">
                <header class="grid grid-cols-2 items-center gap-2 py-10 lg:grid-cols-3">
                    <div class="flex lg:col-start-2 lg:justify-center">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 bg-fleet-accent rounded-lg flex items-center justify-center">
                                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"></path>
                                </svg>
                            </div>
                            <span class="text-2xl font-bold text-fleet-text">Fleet Fox</span>
                        </div>
                    </div>
                    <nav v-if="canLogin" class="-mx-3 flex flex-1 justify-end">
                        <Link
                            v-if="$page.props.auth.user"
                            :href="route('dashboard')"
                            class="rounded-lg px-4 py-2 bg-fleet-accent text-white font-medium transition-all duration-200 hover:bg-fleet-accent-light hover:shadow-lg hover:shadow-fleet-accent/25"
                        >
                            Dashboard
                        </Link>

                        <template v-else>
                            <Link
                                :href="route('login')"
                                class="rounded-lg px-4 py-2 text-fleet-text ring-1 ring-fleet-accent/20 transition hover:bg-fleet-accent/10 hover:ring-fleet-accent/40 focus:outline-none focus-visible:ring-fleet-accent"
                            >
                                Log in
                            </Link>

                            <Link
                                v-if="canRegister"
                                :href="route('register')"
                                class="ml-3 rounded-lg px-4 py-2 bg-fleet-accent text-white font-medium transition-all duration-200 hover:bg-fleet-accent-light hover:shadow-lg hover:shadow-fleet-accent/25"
                            >
                                Register
                            </Link>
                        </template>
                    </nav>
                </header>

                <main class="mt-6">
                    <!-- Hero Section -->
                    <div class="text-center mb-12">
                        <h1 class="text-5xl font-bold text-fleet-text mb-4">
                            Fleet Fox Task Management
                        </h1>
                        <p class="text-xl text-fleet-text-muted mb-8">
                            Streamline your fleet operations with intelligent task management
                        </p>
                        <div class="flex justify-center space-x-4">
                            <Link
                                v-if="canRegister"
                                :href="route('register')"
                                class="bg-fleet-accent hover:bg-fleet-accent-light text-white px-8 py-4 rounded-lg font-medium transition-all duration-200 hover:shadow-lg hover:shadow-fleet-accent/25"
                            >
                                Get Started
                            </Link>
                            <Link
                                v-if="canLogin"
                                :href="route('login')"
                                class="bg-fleet-darker border border-fleet-accent/20 hover:border-fleet-accent/40 text-fleet-text px-8 py-4 rounded-lg font-medium transition-all duration-200 hover:bg-fleet-accent/10"
                            >
                                Sign In
                            </Link>
                        </div>
                    </div>

                    <!-- Features Grid -->
                    <div class="grid gap-6 lg:grid-cols-3 lg:gap-8">
                        <div class="bg-fleet-darker border border-fleet-accent/20 rounded-xl p-6 hover:border-fleet-accent/40 transition-all duration-300 hover:shadow-lg hover:shadow-fleet-accent/10">
                            <div class="w-12 h-12 bg-fleet-accent/10 rounded-lg flex items-center justify-center mb-4">
                                <svg class="w-6 h-6 text-fleet-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-fleet-text mb-2">Task Management</h3>
                            <p class="text-fleet-text-muted">
                                Create, organize, and track fleet maintenance tasks with due dates and priority levels.
                            </p>
                        </div>

                        <div class="bg-fleet-darker border border-fleet-accent/20 rounded-xl p-6 hover:border-fleet-accent/40 transition-all duration-300 hover:shadow-lg hover:shadow-fleet-accent/10">
                            <div class="w-12 h-12 bg-fleet-accent/10 rounded-lg flex items-center justify-center mb-4">
                                <svg class="w-6 h-6 text-fleet-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-fleet-text mb-2">Categories</h3>
                            <p class="text-fleet-text-muted">
                                Organize tasks by fleet operations: maintenance, inspections, driver tasks, and more.
                            </p>
                        </div>

                        <div class="bg-fleet-darker border border-fleet-accent/20 rounded-xl p-6 hover:border-fleet-accent/40 transition-all duration-300 hover:shadow-lg hover:shadow-fleet-accent/10">
                            <div class="w-12 h-12 bg-fleet-accent/10 rounded-lg flex items-center justify-center mb-4">
                                <svg class="w-6 h-6 text-fleet-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-fleet-text mb-2">Due Date Tracking</h3>
                            <p class="text-fleet-text-muted">
                                Never miss critical deadlines with intelligent due date highlighting and SLA tracking.
                            </p>
                        </div>
                    </div>

                    <!-- Tech Stack -->
                    <div class="mt-16 text-center">
                        <h2 class="text-2xl font-bold text-fleet-text mb-8">Built with Modern Technology</h2>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                            <div class="bg-fleet-darker border border-fleet-accent/20 rounded-lg p-4">
                                <div class="text-fleet-accent font-semibold">Laravel 12</div>
                                <div class="text-fleet-text-muted text-sm">PHP Framework</div>
                            </div>
                            <div class="bg-fleet-darker border border-fleet-accent/20 rounded-lg p-4">
                                <div class="text-fleet-accent font-semibold">Vue 3</div>
                                <div class="text-fleet-text-muted text-sm">Frontend Framework</div>
                            </div>
                            <div class="bg-fleet-darker border border-fleet-accent/20 rounded-lg p-4">
                                <div class="text-fleet-accent font-semibold">Inertia.js</div>
                                <div class="text-fleet-text-muted text-sm">SPA Experience</div>
                            </div>
                            <div class="bg-fleet-darker border border-fleet-accent/20 rounded-lg p-4">
                                <div class="text-fleet-accent font-semibold">Tailwind CSS</div>
                                <div class="text-fleet-text-muted text-sm">Styling</div>
                            </div>
                        </div>
                    </div>
                </main>

                <footer class="py-16 text-center text-fleet-text-muted">
                    <p>Fleet Fox Task Management - Built with Laravel {{ laravelVersion }} (PHP {{ phpVersion }})</p>
                </footer>
            </div>
        </div>
    </div>
</template>
