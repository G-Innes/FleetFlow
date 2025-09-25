<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    categories: Array
});

const deleteCategory = (category) => {
    if (confirm(`Are you sure you want to delete the category "${category.name}"? This action cannot be undone.`)) {
        router.delete(route('categories.destroy', category.id));
    }
};
</script>

<template>
    <Head title="Categories" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-fleet-text">Categories</h1>
                    <p class="text-fleet-text-muted mt-1">Manage your task categories</p>
                </div>
                <div class="flex space-x-2 sm:space-x-3">
                    <Link 
                        :href="route('tasks.index')" 
                    class="bg-fleet-darker border border-fleet-accent/20 hover:border-fleet-accent/40 text-fleet-text px-2.5 py-1 text-xs sm:px-4 sm:py-2 sm:text-base rounded-lg font-medium transition-all duration-200 hover:opacity-90"
                    >
                        ← Back to Tasks
                    </Link>
                    <Link 
                        :href="route('categories.create')" 
                        class="bg-fleet-accent hover:bg-fleet-accent-light text-white px-3 py-1.5 text-xs sm:px-6 sm:py-3 sm:text-base rounded-lg font-medium transition-all duration-200 hover:shadow-lg hover:shadow-fleet-accent/25"
                    >
                        + New Category
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <!-- Categories Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div
                        v-for="category in categories"
                        :key="category.id"
                        class="bg-fleet-darker border border-fleet-accent/20 rounded-xl p-6 hover:border-fleet-accent/40 transition-all duration-300 hover:shadow-lg hover:shadow-fleet-accent/10 group"
                    >
                        <!-- Category Header -->
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center space-x-3">
                                <div 
                                    class="w-4 h-4 rounded-full"
                                    :style="{ backgroundColor: category.color }"
                                ></div>
                                <h3 class="text-lg font-semibold text-fleet-text group-hover:text-fleet-accent transition-colors">
                                    {{ category.name }}
                                </h3>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="text-fleet-text-muted text-sm">
                                    {{ category.tasks_count || 0 }} tasks
                                </span>
                            </div>
                        </div>

                        <!-- Category Description -->
                        <p v-if="category.description" class="text-fleet-text-muted text-sm mb-4 line-clamp-2">
                            {{ category.description }}
                        </p>

                        <!-- Category Details -->
                        <div class="space-y-2 mb-6">
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4 text-fleet-text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                </svg>
                                <span class="text-fleet-text-muted text-sm">Color: {{ category.color }}</span>
                            </div>
                            
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4 text-fleet-text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-fleet-text-muted text-sm">
                                    Created {{ new Date(category.created_at).toLocaleDateString() }}
                                </span>
                            </div>
                        </div>

                        <!-- Category Actions -->
                        <div class="flex items-center justify-between">
                            <div class="flex space-x-2">
                                <Link
                                    :href="route('categories.edit', category.id)"
                                    class="text-fleet-accent hover:text-fleet-accent-light text-sm font-medium transition-colors"
                                >
                                    Edit
                                </Link>
                                <button
                                    @click="deleteCategory(category)"
                                    class="text-fleet-danger hover:text-red-400 text-sm font-medium transition-colors"
                                >
                                    Delete
                                </button>
                            </div>
                            
                            <div class="text-xs text-fleet-text-muted">
                                ID: {{ category.id }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-if="categories.length === 0" class="text-center py-12">
                    <div class="w-24 h-24 bg-fleet-accent/10 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-12 h-12 text-fleet-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-fleet-text mb-2">No categories found</h3>
                    <p class="text-fleet-text-muted mb-6">Get started by creating your first category.</p>
                    <Link 
                        :href="route('categories.create')" 
                        class="bg-fleet-accent hover:bg-fleet-accent-light text-white px-6 py-3 rounded-lg font-medium transition-all duration-200 hover:shadow-lg hover:shadow-fleet-accent/25"
                    >
                        Create Your First Category
                    </Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
