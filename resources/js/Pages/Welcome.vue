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
    <Head title="FleetFlow Task Management" />
    <div class="bg-fleet-dark text-fleet-text min-h-screen bg-fleet-logo">
        <div class="relative flex min-h-screen flex-col items-center justify-center">
            <div class="relative w-full max-w-4xl px-6 lg:max-w-7xl">
                <header class="flex items-center justify-between py-10">
                    <div class="flex items-center space-x-0">
                        <img src="/logo.svg" alt="FleetFlow" class="w-24 h-24" />
                        <span class="text-2xl font-bold text-fleet-text">FleetFlow</span>
                    </div>
                    <nav v-if="canLogin" class="flex items-center gap-3">
                        <Link
                            v-if="$page.props.auth.user"
                            :href="route('dashboard')"
                            class="rounded-lg px-4 py-2 bg-fleet-gradient text-white font-medium transition-all duration-200 hover:opacity-90 hover:shadow-lg"
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
                                class="rounded-lg px-4 py-2 bg-fleet-gradient text-white font-medium transition-all duration-200 hover:opacity-90 hover:shadow-lg"
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
                            FleetFlow Task Management
                        </h1>
                        <p class="text-xl text-fleet-text-muted mb-8">
                            Streamline your fleet operations with intelligent task management
                        </p>
                        <div class="flex justify-center space-x-4">
                            <Link
                                v-if="canRegister"
                                :href="route('register')"
                                class="bg-fleet-gradient hover:opacity-90 text-white px-8 py-4 rounded-lg font-medium transition-all duration-200 hover:shadow-lg"
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
                        <div class="glow-wrap" @mousemove="(e)=>{const r=e.currentTarget.getBoundingClientRect(); e.currentTarget.style.setProperty('--gx', `${e.clientX-r.left}px`); e.currentTarget.style.setProperty('--gy', `${e.clientY-r.top}px`) }">
                            <div class="bg-fleet-darker border border-fleet-accent/20 rounded-xl p-6 hover:border-fleet-accent/40 transition-all duration-300 glow-follow">
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
                        </div>

                        <div class="glow-wrap" @mousemove="(e)=>{const r=e.currentTarget.getBoundingClientRect(); e.currentTarget.style.setProperty('--gx', `${e.clientX-r.left}px`); e.currentTarget.style.setProperty('--gy', `${e.clientY-r.top}px`) }">
                            <div class="bg-fleet-darker border border-fleet-accent/20 rounded-xl p-6 hover:border-fleet-accent/40 transition-all duration-300 glow-follow">
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
                            </div>

                        <div class="glow-wrap" @mousemove="(e)=>{const r=e.currentTarget.getBoundingClientRect(); e.currentTarget.style.setProperty('--gx', `${e.clientX-r.left}px`); e.currentTarget.style.setProperty('--gy', `${e.clientY-r.top}px`) }">
                            <div class="bg-fleet-darker border border-fleet-accent/20 rounded-xl p-6 hover:border-fleet-accent/40 transition-all duration-300 glow-follow">
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
                            </div>

                    <!-- Tech Stack -->
                    <div class="mt-16 text-center">
                        <h2 class="text-2xl font-bold text-fleet-text mb-8">Built with</h2>
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                            <div class="bg-fleet-darker border border-fleet-accent/20 rounded-lg p-4 transition-all duration-500 hover:animate-gradientGlow-slow">
                                <div class="text-fleet-accent font-semibold">Laravel 12</div>
                                <div class="text-fleet-text-muted text-sm">PHP Framework</div>
                            </div>
                            <div class="bg-fleet-darker border border-fleet-accent/20 rounded-lg p-4 transition-all duration-500 hover:animate-gradientGlow-slow">
                                <div class="text-fleet-accent font-semibold">Vue 3</div>
                                <div class="text-fleet-text-muted text-sm">Frontend Framework</div>
                            </div>
                            <div class="bg-fleet-darker border border-fleet-accent/20 rounded-lg p-4 transition-all duration-500 hover:animate-gradientGlow-slow">
                                <div class="text-fleet-accent font-semibold">Inertia.js</div>
                                <div class="text-fleet-text-muted text-sm">SPA Experience</div>
                            </div>
                            <div class="bg-fleet-darker border border-fleet-accent/20 rounded-lg p-4 transition-all duration-500 hover:animate-gradientGlow-slow">
                                <div class="text-fleet-accent font-semibold">Tailwind CSS</div>
                                <div class="text-fleet-text-muted text-sm">Styling</div>
                            </div>
                            <div class="bg-fleet-darker border border-fleet-accent/20 rounded-lg p-4 transition-all duration-500 hover:animate-gradientGlow-slow">
                                <div class="text-fleet-accent font-semibold">Neon PostgreSQL</div>
                                <div class="text-fleet-text-muted text-sm">Database</div>
                            </div>
                            <div class="bg-fleet-darker border border-fleet-accent/20 rounded-lg p-4 transition-all duration-500 hover:animate-gradientGlow-slow">
                                <div class="text-fleet-accent font-semibold">Vercel</div>
                                <div class="text-fleet-text-muted text-sm">Hosting & Deployment</div>
                            </div>
                            <div class="bg-fleet-darker border border-fleet-accent/20 rounded-lg p-4 transition-all duration-500 hover:animate-gradientGlow-slow">
                                <div class="text-fleet-accent font-semibold">Vite</div>
                                <div class="text-fleet-text-muted text-sm">Build Tool</div>
                            </div>
                            <div class="bg-fleet-darker border border-fleet-accent/20 rounded-lg p-4 transition-all duration-500 hover:animate-gradientGlow-slow">
                                <div class="text-fleet-accent font-semibold">PEST</div>
                                <div class="text-fleet-text-muted text-sm">Testing Framework</div>
                            </div>
                            <div class="bg-fleet-darker border border-fleet-accent/20 rounded-lg p-4 transition-all duration-500 hover:animate-gradientGlow-slow">
                                <div class="text-fleet-accent font-semibold">Laravel Breeze</div>
                                <div class="text-fleet-text-muted text-sm">Authentication</div>
                            </div>
                        </div>
                    </div>
                </main>

                <footer class="py-16 text-center text-fleet-text-muted">
                    <p>FleetFlow Task Management - Built with Love by Grant Innes</p>
                </footer>
            </div>
        </div>
    </div>
</template>
