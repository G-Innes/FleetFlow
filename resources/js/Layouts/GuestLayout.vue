<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="flex min-h-screen flex-col items-center bg-fleet-dark bg-fleet-logo pt-6 sm:justify-center sm:pt-0">
        <div class="flex items-center space-x-2">
            <Link href="/" class="flex items-center">
                <img src="/logo.svg" alt="FleetFlow" class="w-12 h-12" />
                <span class="hidden sm:inline ml-2 text-2xl font-bold text-fleet-text">FleetFlow</span>
            </Link>
        </div>

        <div class="mt-6 w-full overflow-hidden bg-fleet-darker px-6 py-6 border border-fleet-accent/20 sm:max-w-md sm:rounded-xl">
            <slot />
        </div>
    </div>
    
</template>
