<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    task: Object
});

const isToggling = ref(false);

const toggleCompletion = () => {
    if (isToggling.value) return;
    
    isToggling.value = true;
    // Ensure relative URL to avoid mixed-content
    const toRelative = (url) => { try { const u = new URL(url, window.location.origin); return u.pathname + u.search; } catch { return url; } };
    router.patch(toRelative(route('tasks.toggle', props.task.id)), {}, {
        preserveScroll: true,
        onFinish: () => {
            isToggling.value = false;
        }
    });
};

// Helper function to format date for display
const formatDate = (dateString) => {
    if (!dateString) return 'No due date';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};

// Helper function to get priority color classes
const getPriorityColor = (priority) => {
    const colors = {
        'Low': 'bg-fleet-success/20 text-fleet-success border-fleet-success/30',
        'Medium': 'bg-fleet-warning/20 text-fleet-warning border-fleet-warning/30',
        'High': 'bg-fleet-error/20 text-fleet-error border-fleet-error/30'
    };
    return colors[priority] || 'bg-fleet-text-muted/20 text-fleet-text-muted border-fleet-text-muted/30';
};

// Helper function to get priority label
const getPriorityLabel = (priority) => {
    const labels = {
        1: 'Low',
        2: 'Medium', 
        3: 'High'
    };
    return labels[priority] || 'Unknown';
};
</script>

<template>
    <Head :title="`Task: ${task.title}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-fleet-text">{{ task.title }}</h1>
                    <p class="text-fleet-text-muted mt-1">Task Details</p>
                </div>
                <div class="flex space-x-3">
                    <Link 
                        :href="route('tasks.edit', task.id)" 
                        class="bg-fleet-gradient hover:opacity-90 text-white px-4 py-2 rounded-lg font-medium transition-all duration-200"
                    >
                        Edit Task
                    </Link>
                    <Link 
                        :href="route('tasks.index')" 
                        class="bg-fleet-darker border border-fleet-accent/20 hover:border-fleet-accent/40 text-fleet-text px-4 py-2 rounded-lg font-medium transition-all duration-200 hover:bg-fleet-accent/10"
                    >
                        Back to Tasks
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="glow-wrap" @mousemove="(e)=>{const r=e.currentTarget.getBoundingClientRect(); e.currentTarget.style.setProperty('--gx', `${e.clientX-r.left}px`); e.currentTarget.style.setProperty('--gy', `${e.clientY-r.top}px`) }">
                    <div class="bg-fleet-darker border border-fleet-accent/20 rounded-xl p-8 glow-follow">
                        <!-- Task Header -->
                        <div class="flex items-start justify-between mb-6">
                            <div class="flex-1">
                                <h2 class="text-2xl font-bold text-fleet-text mb-2">{{ task.title }}</h2>
                                <div class="flex items-center space-x-4">
                                    <span 
                                        :class="getPriorityColor(getPriorityLabel(task.priority))"
                                        class="px-3 py-1 rounded-full text-sm font-medium border"
                                    >
                                        {{ getPriorityLabel(task.priority) }} Priority
                                    </span>
                                    <div v-if="task.is_completed" class="flex items-center space-x-2">
                                        <div class="w-5 h-5 bg-fleet-success rounded-full flex items-center justify-center">
                                            <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                        <span class="text-fleet-success font-medium">Completed</span>
                                    </div>
                                    <div v-else class="flex items-center space-x-2">
                                        <div class="w-5 h-5 bg-fleet-text-muted rounded-full"></div>
                                        <span class="text-fleet-text-muted">In Progress</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Task Details Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <!-- Due Date -->
                            <div class="bg-fleet-dark/30 rounded-lg p-4 border border-fleet-accent/10">
                                <h3 class="text-fleet-text font-semibold mb-2 flex items-center">
                                    <svg class="w-5 h-5 text-fleet-gradient mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    Due Date
                                </h3>
                                <p class="text-fleet-text-muted">{{ formatDate(task.due_date) }}</p>
                            </div>

                            <!-- Category -->
                            <div class="bg-fleet-dark/30 rounded-lg p-4 border border-fleet-accent/10">
                                <h3 class="text-fleet-text font-semibold mb-2 flex items-center">
                                    <svg class="w-5 h-5 text-fleet-gradient mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                    </svg>
                                    Category
                                </h3>
                                <p class="text-fleet-text-muted">{{ task.category?.name || 'No category' }}</p>
                            </div>
                        </div>

                        <!-- Description -->
                        <div v-if="task.description" class="bg-fleet-dark/30 rounded-lg p-4 border border-fleet-accent/10 mb-6">
                            <h3 class="text-fleet-text font-semibold mb-2 flex items-center">
                                <svg class="w-5 h-5 text-fleet-gradient mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Description
                            </h3>
                            <p class="text-fleet-text-muted whitespace-pre-wrap">{{ task.description }}</p>
                        </div>

                        <!-- Task Actions -->
                        <div class="flex items-center justify-between pt-6 border-t border-fleet-accent/10">
                            <div class="flex space-x-3">
                                <button
                                    @click="toggleCompletion"
                                    :disabled="isToggling"
                                    :class="{
                                        'bg-fleet-success hover:bg-fleet-success/80': !task.is_completed,
                                        'bg-fleet-text-muted hover:bg-fleet-text-muted/80': task.is_completed
                                    }"
                                    class="text-white px-4 py-2 rounded-lg font-medium transition-all duration-200 disabled:opacity-50"
                                >
                                    <span v-if="isToggling">Updating...</span>
                                    <span v-else-if="task.is_completed">Mark as Incomplete</span>
                                    <span v-else>Mark as Complete</span>
                                </button>
                                <Link 
                                    :href="route('tasks.edit', task.id)" 
                                    class="bg-fleet-gradient hover:opacity-90 text-white px-4 py-2 rounded-lg font-medium transition-all duration-200"
                                >
                                    Edit Task
                                </Link>
                                <Link 
                                    :href="route('tasks.index')" 
                                    class="bg-fleet-darker border border-fleet-accent/20 hover:border-fleet-accent/40 text-fleet-text px-4 py-2 rounded-lg font-medium transition-all duration-200 hover:bg-fleet-accent/10"
                                >
                                    Back to Tasks
                                </Link>
                            </div>
                            <div class="text-fleet-text-muted text-sm">
                                Created {{ new Date(task.created_at).toLocaleDateString() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
