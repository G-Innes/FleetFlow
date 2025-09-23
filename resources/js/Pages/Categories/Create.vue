<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const form = useForm({
    name: '',
    color: '#3B82F6',
    description: ''
});

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
                    <p class="text-fleet-text-muted mt-1">Add a new category to organize your tasks</p>
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
                                class="w-full px-4 py-3 bg-fleet-dark border border-fleet-accent/20 rounded-lg text-fleet-text placeholder-fleet-text-muted focus:border-fleet-accent focus:ring-1 focus:ring-fleet-accent focus:outline-none transition-all duration-200"
                                placeholder="Enter category name..."
                                required
                            />
                            <div v-if="form.errors.name" class="mt-1 text-sm text-fleet-danger">
                                {{ form.errors.name }}
                            </div>
                        </div>

                        <!-- Color -->
                        <div>
                            <label for="color" class="block text-sm font-medium text-fleet-text mb-2">
                                Color *
                            </label>
                            <div class="flex items-center space-x-4">
                                <input
                                    id="color"
                                    v-model="form.color"
                                    type="color"
                                    class="w-16 h-12 bg-fleet-dark border border-fleet-accent/20 rounded-lg cursor-pointer"
                                    required
                                />
                                <input
                                    v-model="form.color"
                                    type="text"
                                    class="flex-1 px-4 py-3 bg-fleet-dark border border-fleet-accent/20 rounded-lg text-fleet-text placeholder-fleet-text-muted focus:border-fleet-accent focus:ring-1 focus:ring-fleet-accent focus:outline-none transition-all duration-200"
                                    placeholder="#3B82F6"
                                    pattern="^#[0-9A-Fa-f]{6}$"
                                />
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
                                rows="4"
                                class="w-full px-4 py-3 bg-fleet-dark border border-fleet-accent/20 rounded-lg text-fleet-text placeholder-fleet-text-muted focus:border-fleet-accent focus:ring-1 focus:ring-fleet-accent focus:outline-none transition-all duration-200 resize-none"
                                placeholder="Enter category description..."
                            ></textarea>
                            <div v-if="form.errors.description" class="mt-1 text-sm text-fleet-danger">
                                {{ form.errors.description }}
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex items-center justify-end space-x-4 pt-6">
                            <Link 
                                :href="route('categories.index')" 
                                class="px-6 py-3 bg-fleet-darker border border-fleet-accent/20 text-fleet-text rounded-lg font-medium hover:bg-fleet-accent/10 hover:border-fleet-accent/40 transition-all duration-200"
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