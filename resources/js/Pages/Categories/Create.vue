<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const palette = ['#EF4444', '#F59E0B', '#10B981', '#3B82F6', '#8B5CF6', '#F97316'];

const form = useForm({
    name: '',
    color: palette[3],
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
                    class="bg-fleet-darker border border-fleet-accent/20 hover:border-fleet-accent/40 text-fleet-text px-4 py-2 rounded-lg font-medium transition-all duration-200 hover:opacity-90"
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
                            <label class="block text-sm font-medium text-fleet-text mb-2">
                                Color *
                            </label>
                            <div class="flex items-center flex-wrap gap-3">
                                <button
                                    v-for="c in palette"
                                    :key="c"
                                    type="button"
                                    @click="form.color = c"
                                    class="w-10 h-10 rounded-full border transition-all"
                                    :style="{ backgroundColor: c }"
                                    :class="form.color === c ? 'ring-2 ring-fleet-accent border-white' : 'border-fleet-accent/30'"
                                    aria-label="Select color"
                                />
                            </div>
                            <div v-if="form.errors.color" class="mt-2 text-sm text-fleet-danger">
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
                                class="px-6 py-3 bg-fleet-darker border border-fleet-accent/20 text-fleet-text rounded-lg font-medium hover:opacity-90 hover:border-fleet-accent/40 transition-all duration-200"
                            >
                                Cancel
                            </Link>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="px-6 py-3 bg-fleet-gradient hover:opacity-90 text-white rounded-lg font-medium transition-all duration-200 hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed"
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