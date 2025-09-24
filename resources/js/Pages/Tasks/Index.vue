<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
    tasks: Object,
    categories: Array,
    filters: Object
});

const categoryFilter = ref(props.filters.category || '');
const statusFilter = ref(props.filters.status || '');
const priorityFilter = ref(props.filters.priority || '');
const dueSoonFilter = ref(Boolean(props.filters.due_soon) || false);
const showImport = ref(false);
const importForm = useForm({ csv_file: null });

const onFileChange = (e) => {
    const files = e.target.files;
    if (files && files[0]) {
        importForm.csv_file = files[0];
    }
};

const submitImport = () => {
    if (!importForm.csv_file) return;
    importForm.post(route('tasks.import'), {
        preserveScroll: true,
        onSuccess: () => {
            showImport.value = false;
            importForm.reset('csv_file');
        },
    });
};

const filteredTasks = computed(() => {
    return props.tasks.data;
});

const getPriorityColor = (priority) => {
    switch(priority) {
        case 3: return 'text-fleet-danger border-fleet-danger bg-fleet-danger/10';
        case 2: return 'text-fleet-warning border-fleet-warning bg-fleet-warning/10';
        case 1: return 'text-fleet-success border-fleet-success bg-fleet-success/10';
        default: return 'text-fleet-text-muted border-fleet-text-muted bg-fleet-text-muted/10';
    }
};

const getPriorityLabel = (priority) => {
    switch(priority) {
        case 3: return 'High';
        case 2: return 'Medium';
        case 1: return 'Low';
        default: return 'Unknown';
    }
};

const formatDate = (date) => {
    if (!date) return 'No due date';
    return new Date(date).toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric'
    });
};

const isDueSoon = (dueDate) => {
    if (!dueDate) return false;
    const due = new Date(dueDate);
    const now = new Date();
    const diffTime = due - now;
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    return diffDays <= 3 && diffDays >= 0;
};

const isOverdue = (dueDate) => {
    if (!dueDate) return false;
    const due = new Date(dueDate);
    const now = new Date();
    return due < now;
};

const toggleTask = (task) => {
    router.patch(route('tasks.toggle', task.id), {}, {
        preserveState: true,
        preserveScroll: true,
    });
};

const applyFilters = () => {
    router.get(route('tasks.index'), {
        category: categoryFilter.value,
        status: statusFilter.value,
        priority: priorityFilter.value,
        due_soon: dueSoonFilter.value
    }, {
        preserveState: true,
        replace: true
    });
};

const clearFilters = () => {
    categoryFilter.value = '';
    statusFilter.value = '';
    priorityFilter.value = '';
    dueSoonFilter.value = false;
    applyFilters();
};
</script>

<template>
    <Head title="Tasks" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-fleet-text">Task Management</h1>
                    <p class="text-fleet-text-muted mt-1">Manage your fleet operations tasks</p>
                </div>
                <div class="flex space-x-3">
                    <button 
                        type="button"
                        @click="showImport = true"
                        class="bg-fleet-darker border border-fleet-accent/20 hover:border-fleet-accent/40 text-fleet-text px-4 py-2 rounded-lg font-medium transition-all duration-200 hover:bg-fleet-accent/10"
                    >
                        Import CSV
                    </button>
                    <Link 
                        :href="route('tasks.export', { 
                            category: categoryFilter || undefined,
                            status: statusFilter || undefined,
                            priority: priorityFilter || undefined,
                            due_soon: dueSoonFilter || undefined
                        })" 
                        class="bg-fleet-darker border border-fleet-accent/20 hover:border-fleet-accent/40 text-fleet-text px-4 py-2 rounded-lg font-medium transition-all duration-200 hover:bg-fleet-accent/10"
                    >
                        Export CSV
                    </Link>
                    <Link 
                        :href="route('tasks.create')" 
                        class="bg-fleet-accent hover:bg-fleet-accent-light text-white px-6 py-3 rounded-lg font-medium transition-all duration-200 hover:shadow-lg hover:shadow-fleet-accent/25"
                    >
                        + New Task
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <!-- Filters -->
                <div class="bg-fleet-darker border border-fleet-accent/20 rounded-xl p-6 mb-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Category Filter -->
                        <div>
                            <label class="block text-sm font-medium text-fleet-text mb-2">Category</label>
                            <select
                                v-model="categoryFilter"
                                class="w-full px-4 py-2 bg-fleet-dark border border-fleet-accent/20 rounded-lg text-fleet-text focus:border-fleet-accent focus:ring-2 focus:ring-fleet-accent/20 focus:outline-none transition-all duration-200"
                                @change="applyFilters"
                            >
                                <option value="">All Categories</option>
                                <option v-for="category in categories" :key="category.id" :value="category.id">
                                    {{ category.name }}
                                </option>
                            </select>
                        </div>

                        <!-- Status Filter -->
                        <div>
                            <label class="block text-sm font-medium text-fleet-text mb-2">Status</label>
                            <select
                                v-model="statusFilter"
                                class="w-full px-4 py-2 bg-fleet-dark border border-fleet-accent/20 rounded-lg text-fleet-text focus:border-fleet-accent focus:ring-2 focus:ring-fleet-accent/20 focus:outline-none transition-all duration-200"
                                @change="applyFilters"
                            >
                                <option value="">All Tasks</option>
                                <option value="pending">Pending</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>

                        <!-- Priority Filter -->
                        <div>
                            <label class="block text-sm font-medium text-fleet-text mb-2">Priority</label>
                            <select
                                v-model="priorityFilter"
                                class="w-full px-4 py-2 bg-fleet-dark border border-fleet-accent/20 rounded-lg text-fleet-text focus:border-fleet-accent focus:ring-2 focus:ring-fleet-accent/20 focus:outline-none transition-all duration-200"
                                @change="applyFilters"
                            >
                                <option value="">All</option>
                                <option value="3">High</option>
                                <option value="2">Medium</option>
                                <option value="1">Low</option>
                            </select>
                        </div>

                        <!-- Due Soon Filter -->
                        <div class="flex items-end">
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input
                                    v-model="dueSoonFilter"
                                    type="checkbox"
                                    class="w-4 h-4 text-fleet-accent bg-fleet-dark border-fleet-accent/20 rounded focus:ring-fleet-accent/20 focus:ring-2"
                                    @change="applyFilters"
                                />
                                <span class="text-sm font-medium text-fleet-text">Due Soon</span>
                            </label>
                        </div>
                    </div>

                    <!-- Clear Filters -->
                    <div class="mt-4 flex justify-end">
                        <button
                            @click="clearFilters"
                            class="text-fleet-accent hover:text-fleet-accent-light text-sm font-medium transition-colors"
                        >
                            Clear Filters
                        </button>
                    </div>
                </div>

                <!-- Tasks Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div class="glow-wrap" @mousemove="(e)=>{const r=e.currentTarget.getBoundingClientRect(); e.currentTarget.style.setProperty('--gx', `${e.clientX-r.left}px`); e.currentTarget.style.setProperty('--gy', `${e.clientY-r.top}px`) }" v-for="task in filteredTasks" :key="task.id">
                        <div
                            class="bg-fleet-darker border border-fleet-accent/20 rounded-xl p-6 hover:border-fleet-accent/40 transition-all duration-300 glow-follow group"
                        >
                        <!-- Task Header -->
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-fleet-text group-hover:text-fleet-accent transition-colors">
                                    {{ task.title }}
                                </h3>
                                <p v-if="task.description" class="text-fleet-text-muted text-sm mt-1 line-clamp-2">
                                    {{ task.description }}
                                </p>
                            </div>
                            <div class="flex items-center space-x-2 ml-4">
                                <!-- Priority Badge -->
                                <span 
                                    :class="getPriorityColor(task.priority)"
                                    class="px-2 py-1 rounded-full text-xs font-medium border"
                                >
                                    {{ getPriorityLabel(task.priority) }}
                                </span>
                                
                                <!-- Completion Toggle -->
                                <button
                                    @click="toggleTask(task)"
                                    :class="task.is_completed ? 'bg-fleet-success' : 'bg-fleet-dark border border-fleet-accent/20'"
                                    class="w-6 h-6 rounded-full flex items-center justify-center transition-all duration-200 hover:scale-110"
                                >
                                    <svg v-if="task.is_completed" class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Task Details -->
                        <div class="space-y-3">
                            <!-- Category -->
                            <div v-if="task.category" class="flex items-center space-x-2">
                                <div 
                                    class="w-3 h-3 rounded-full"
                                    :style="{ backgroundColor: task.category.color }"
                                ></div>
                                <span class="text-fleet-text-muted text-sm">{{ task.category.name }}</span>
                            </div>

                            <!-- Due Date -->
                            <div v-if="task.due_date" class="flex items-center space-x-2">
                                <svg class="w-4 h-4 text-fleet-text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <span 
                                    :class="{
                                        'text-fleet-danger': isOverdue(task.due_date),
                                        'text-fleet-warning': isDueSoon(task.due_date) && !isOverdue(task.due_date),
                                        'text-fleet-text-muted': !isDueSoon(task.due_date) && !isOverdue(task.due_date)
                                    }"
                                    class="text-sm font-medium"
                                >
                                    {{ formatDate(task.due_date) }}
                                    <span v-if="isOverdue(task.due_date)" class="ml-1">(Overdue)</span>
                                    <span v-else-if="isDueSoon(task.due_date)" class="ml-1">(Due Soon)</span>
                                </span>
                            </div>

                            <!-- Created Date -->
                            <div class="flex items-center space-x-2">
                                <svg class="w-4 h-4 text-fleet-text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-fleet-text-muted text-sm">
                                    Created {{ new Date(task.created_at).toLocaleDateString() }}
                                </span>
                            </div>
                        </div>

                        <!-- Task Actions -->
                        <div class="mt-6 flex items-center justify-between">
                            <div class="flex space-x-2">
                                <Link
                                    :href="route('tasks.edit', task.id)"
                                    class="text-fleet-accent hover:text-fleet-accent-light text-sm font-medium transition-colors"
                                >
                                    Edit
                                </Link>
                                <button
                                    @click="router.delete(route('tasks.destroy', task.id), { onBefore: () => confirm('Are you sure you want to delete this task?') })"
                                    class="text-fleet-danger hover:text-red-400 text-sm font-medium transition-colors"
                                >
                                    Delete
                                </button>
                            </div>
                            
                            <div class="text-xs text-fleet-text-muted">
                                ID: {{ task.id }}
                            </div>
                        </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-if="filteredTasks.length === 0" class="text-center py-12">
                    <div class="w-24 h-24 bg-fleet-accent/10 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-12 h-12 text-fleet-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-fleet-text mb-2">No tasks found</h3>
                    <p class="text-fleet-text-muted mb-6">Get started by creating your first task.</p>
                    <Link 
                        :href="route('tasks.create')" 
                        class="bg-fleet-accent hover:bg-fleet-accent-light text-white px-6 py-3 rounded-lg font-medium transition-all duration-200 hover:shadow-lg hover:shadow-fleet-accent/25"
                    >
                        Create Your First Task
                    </Link>
                </div>

                <!-- Pagination -->
                <div v-if="tasks.links && tasks.links.length > 3" class="mt-8 flex justify-center">
                    <nav class="flex space-x-2">
                        <Link
                            v-for="link in tasks.links"
                            :key="link.label"
                            :href="link.url"
                            v-html="link.label"
                            :class="{
                                'bg-fleet-accent text-white': link.active,
                                'bg-fleet-darker text-fleet-text hover:bg-fleet-accent/10': !link.active,
                                'pointer-events-none opacity-50': !link.url
                            }"
                            class="px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200"
                        />
                    </nav>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>

    <!-- Import Modal -->
    <Modal :show="showImport" @close="showImport = false">
        <div class="bg-fleet-darker border border-fleet-accent/20 px-6 py-6">
            <h3 class="text-xl font-semibold text-fleet-text mb-4">Import Tasks (CSV)</h3>
            <p class="text-fleet-text-muted text-sm mb-4">Upload a CSV with headers: Title, Description, Category, Due Date, Status, Priority.</p>
            <input type="file" accept=".csv,text/csv" @change="onFileChange" class="w-full text-fleet-text" />
            <div class="mt-6 flex justify-end space-x-3">
                <button @click="showImport = false" class="px-4 py-2 bg-fleet-darker border border-fleet-accent/20 text-fleet-text rounded-lg hover:opacity-90">Cancel</button>
                <button @click="submitImport" :disabled="importForm.processing || !importForm.csv_file" class="px-4 py-2 bg-fleet-gradient text-white rounded-lg hover:opacity-90 disabled:opacity-50">Import</button>
            </div>
        </div>
    </Modal>
</template>
