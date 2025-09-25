<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    task: Object,
    categories: Array
});

// Helper function to convert UTC date to local datetime-local format
const formatDateForInput = (utcDateString) => {
    if (!utcDateString) return '';
    const date = new Date(utcDateString);
    // Convert to local timezone and format for datetime-local input
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    const hours = String(date.getHours()).padStart(2, '0');
    const minutes = String(date.getMinutes()).padStart(2, '0');
    return `${year}-${month}-${day}T${hours}:${minutes}`;
};

const form = useForm({
    title: props.task.title,
    description: props.task.description || '',
    category_id: props.task.category_id || '',
    due_date: formatDateForInput(props.task.due_date),
    priority: props.task.priority,
    is_completed: props.task.is_completed
});

const submit = () => {
    form.put(route('tasks.update', props.task.id));
};
</script>

<template>
    <Head title="Edit Task" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-fleet-text">Edit Task</h1>
                    <p class="text-fleet-text-muted mt-1">Update task details</p>
                </div>
                <Link 
                    :href="route('tasks.index')" 
                    class="bg-fleet-darker border border-fleet-accent/20 hover:border-fleet-accent/40 text-fleet-text px-4 py-2 rounded-lg font-medium transition-all duration-200 hover:bg-fleet-accent/10"
                >
                    ← Back to Tasks
                </Link>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-2xl px-4 sm:px-6 lg:px-8">
                <div class="bg-fleet-darker border border-fleet-accent/20 rounded-xl p-8">
                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Title -->
                        <div>
                            <label for="title" class="block text-sm font-medium text-fleet-text mb-2">
                                Task Title *
                            </label>
                            <input
                                id="title"
                                v-model="form.title"
                                type="text"
                                class="w-full px-4 py-3 bg-fleet-dark border border-fleet-accent/20 rounded-lg text-fleet-text placeholder-fleet-text-muted focus:border-fleet-accent focus:ring-1 focus:ring-fleet-accent focus:outline-none transition-all duration-200"
                                placeholder="Enter task title..."
                                required
                            />
                            <div v-if="form.errors.title" class="mt-1 text-sm text-fleet-danger">
                                {{ form.errors.title }}
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
                                placeholder="Enter task description..."
                            ></textarea>
                            <div v-if="form.errors.description" class="mt-1 text-sm text-fleet-danger">
                                {{ form.errors.description }}
                            </div>
                        </div>

                        <!-- Category and Priority Row -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Category -->
                            <div>
                                <label for="category_id" class="block text-sm font-medium text-fleet-text mb-2">
                                    Category
                                </label>
                                <select
                                    id="category_id"
                                    v-model="form.category_id"
                                    class="w-full px-4 py-3 bg-fleet-dark border border-fleet-accent/20 rounded-lg text-fleet-text focus:border-fleet-accent focus:ring-1 focus:ring-fleet-accent focus:outline-none transition-all duration-200"
                                >
                                    <option value="">Select a category</option>
                                    <option 
                                        v-for="category in categories" 
                                        :key="category.id" 
                                        :value="category.id"
                                    >
                                        {{ category.name }}
                                    </option>
                                </select>
                                <div v-if="form.errors.category_id" class="mt-1 text-sm text-fleet-danger">
                                    {{ form.errors.category_id }}
                                </div>
                            </div>

                            <!-- Priority -->
                            <div>
                                <label for="priority" class="block text-sm font-medium text-fleet-text mb-2">
                                    Priority *
                                </label>
                                <select
                                    id="priority"
                                    v-model="form.priority"
                                    class="w-full px-4 py-3 bg-fleet-dark border border-fleet-accent/20 rounded-lg text-fleet-text focus:border-fleet-accent focus:ring-1 focus:ring-fleet-accent focus:outline-none transition-all duration-200"
                                    required
                                >
                                    <option value="1">Low Priority</option>
                                    <option value="2">Medium Priority</option>
                                    <option value="3">High Priority</option>
                                </select>
                                <div v-if="form.errors.priority" class="mt-1 text-sm text-fleet-danger">
                                    {{ form.errors.priority }}
                                </div>
                            </div>
                        </div>

                        <!-- Due Date -->
                        <div>
                            <label for="due_date" class="block text-sm font-medium text-fleet-text mb-2">
                                Due Date
                            </label>
                            <input
                                id="due_date"
                                v-model="form.due_date"
                                type="datetime-local"
                                class="w-full px-4 py-3 bg-fleet-dark border border-fleet-accent/20 rounded-lg text-fleet-text focus:border-fleet-accent focus:ring-1 focus:ring-fleet-accent focus:outline-none transition-all duration-200"
                            />
                            <div v-if="form.errors.due_date" class="mt-1 text-sm text-fleet-danger">
                                {{ form.errors.due_date }}
                            </div>
                        </div>

                        <!-- Completion Status -->
                        <div class="flex items-center">
                            <input
                                id="is_completed"
                                v-model="form.is_completed"
                                type="checkbox"
                                class="w-4 h-4 text-fleet-accent bg-fleet-dark border-fleet-accent/20 rounded focus:ring-fleet-accent focus:ring-2"
                            />
                            <label for="is_completed" class="ml-2 text-sm font-medium text-fleet-text">
                                Mark as completed
                            </label>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex items-center justify-end space-x-4 pt-6">
                            <Link 
                                :href="route('tasks.index')" 
                                class="px-6 py-3 bg-fleet-darker border border-fleet-accent/20 text-fleet-text rounded-lg font-medium hover:opacity-90 hover:border-fleet-accent/40 transition-all duration-200"
                            >
                                Cancel
                            </Link>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="px-6 py-3 bg-fleet-gradient hover:opacity-90 text-white rounded-lg font-medium transition-all duration-200 hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed"
                            >
                                <span v-if="form.processing">Updating...</span>
                                <span v-else>Update Task</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>