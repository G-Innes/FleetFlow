<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const form = useForm({
    name: '',
    color: '#f97316',
    description: ''
});

const colorOptions = [
    { value: '#f97316', label: 'Orange', class: 'bg-orange-500' },
    { value: '#ef4444', label: 'Red', class: 'bg-red-500' },
    { value: '#f59e0b', label: 'Yellow', class: 'bg-yellow-500' },
    { value: '#10b981', label: 'Green', class: 'bg-green-500' },
    { value: '#3b82f6', label: 'Blue', class: 'bg-blue-500' },
    { value: '#8b5cf6', label: 'Purple', class: 'bg-purple-500' },
    { value: '#ec4899', label: 'Pink', class: 'bg-pink-500' },
    { value: '#6b7280', label: 'Gray', class: 'bg-gray-500' }
];

const submit = () => {
    form.post(route('categories.store'));
};
</script>

<template>
    <Head title="Create Category" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-fleet-text">Create New Category</h1>
                    <p class="text-fleet-text-muted mt-1">Add a new category for organizing tasks</p>
                </div>
                <Link 
                    :href="route('categories.index')" 
                    class="bg-fleet-darker border border-fleet-accent/20 hover:border-fleet-accent/40 text-fleet-text px-4 py-2 rounded-lg font-medium transition-all duration-200 hover:bg-fleet-accent/10"
                >
                    ← Back to Categories
                </Link>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-2xl px-4 sm:px-6 lg:px-8">
                <div class="bg-fleet-darker border border-fleet-accent/20 rounded-xl p-8">
                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-fleet-text mb-2">
                                Category Name *
                            </label>
                            <input
                                id="name"
                                v-model="form.name"
                                type="text"
                                required
                                class="w-full px-4 py-3 bg-fleet-dark border border-fleet-accent/20 rounded-lg text-fleet-text placeholder-fleet-text-muted focus:border-fleet-accent focus:ring-2 focus:ring-fleet-accent/20 focus:outline-none transition-all duration-200"
                                placeholder="Enter category name..."
                            />
                            <div v-if="form.errors.name" class="mt-1 text-sm text-fleet-danger">
                                {{ form.errors.name }}
                            </div>
                        </div>

                        <!-- Color Selection -->
                        <div>
                            <label class="block text-sm font-medium text-fleet-text mb-2">
                                Category Color *
                            </label>
                            <div class="grid grid-cols-4 gap-3">
                                <label
                                    v-for="color in colorOptions"
                                    :key="color.value"
                                    class="relative cursor-pointer"
                                >
                                    <input
                                        v-model="form.color"
                                        :value="color.value"
                                        type="radio"
                                        class="sr-only"
                                    />
                                    <div
                                        :class="[
                                            'w-full h-12 rounded-lg border-2 transition-all duration-200 hover:scale-105',
                                            form.color === color.value 
                                                ? 'border-fleet-accent ring-2 ring-fleet-accent/20' 
                                                : 'border-fleet-accent/20 hover:border-fleet-accent/40'
                                        ]"
                                    >
                                        <div 
                                            :class="color.class"
                                            class="w-full h-full rounded-lg flex items-center justify-center"
                                        >
                                            <div v-if="form.color === color.value" class="w-6 h-6 bg-white rounded-full flex items-center justify-center">
                                                <svg class="w-4 h-4 text-gray-800" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="text-xs text-fleet-text-muted text-center mt-1">{{ color.label }}</p>
                                </label>
                            </div>
                            <div v-if="form.errors.color" class="mt-1 text-sm text-fleet-danger">
                                {{ form.errors.color }}
                            </div>
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-fleet-text mb-2">
                                Description
                            </label>
                            <textarea
                                id="description"
                                v-model="form.description"
                                rows="3"
                                class="w-full px-4 py-3 bg-fleet-dark border border-fleet-accent/20 rounded-lg text-fleet-text placeholder-fleet-text-muted focus:border-fleet-accent focus:ring-2 focus:ring-fleet-accent/20 focus:outline-none transition-all duration-200 resize-none"
                                placeholder="Enter category description (optional)..."
                            ></textarea>
                            <div v-if="form.errors.description" class="mt-1 text-sm text-fleet-danger">
                                {{ form.errors.description }}
                            </div>
                        </div>

                        <!-- Preview -->
                        <div>
                            <label class="block text-sm font-medium text-fleet-text mb-2">
                                Preview
                            </label>
                            <div class="bg-fleet-dark border border-fleet-accent/20 rounded-lg p-4">
                                <div class="flex items-center space-x-3">
                                    <div 
                                        class="w-4 h-4 rounded-full"
                                        :style="{ backgroundColor: form.color }"
                                    ></div>
                                    <span class="text-fleet-text font-medium">{{ form.name || 'Category Name' }}</span>
                                </div>
                                <p v-if="form.description" class="text-fleet-text-muted text-sm mt-2">
                                    {{ form.description }}
                                </p>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex items-center justify-end space-x-4 pt-6 border-t border-fleet-accent/20">
                            <Link 
                                :href="route('categories.index')" 
                                class="px-6 py-3 bg-fleet-dark border border-fleet-accent/20 text-fleet-text rounded-lg font-medium hover:bg-fleet-accent/10 hover:border-fleet-accent/40 transition-all duration-200"
                            >
                                Cancel
                            </Link>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="px-6 py-3 bg-fleet-accent hover:bg-fleet-accent-light text-white rounded-lg font-medium transition-all duration-200 hover:shadow-lg hover:shadow-fleet-accent/25 disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                <span v-if="form.processing">Creating...</span>
                                <span v-else>Create Category</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
