<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    stats: {
        type: Object,
        default: () => ({
            totalTasks: 0,
            completedTasks: 0,
            dueSoon: 0,
            overdue: 0
        })
    },
    recentTasks: {
        type: Array,
        default: () => []
    }
});

const getPriorityColor = (priority) => {
    switch(priority) {
        case 'High': return 'text-fleet-danger border-fleet-danger';
        case 'Medium': return 'text-fleet-warning border-fleet-warning';
        case 'Low': return 'text-fleet-success border-fleet-success';
        default: return 'text-fleet-text-muted border-fleet-text-muted';
    }
};

const completionRate = computed(() => {
    if (!props.stats || props.stats.totalTasks === 0) return 0;
    return Math.round((props.stats.completedTasks / props.stats.totalTasks) * 100);
});
</script>

<template>
    <Head title="Fleet Fox Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-fleet-text">Fleet Fox Dashboard</h1>
                    <p class="text-fleet-text-muted mt-1">Manage your fleet operations efficiently</p>
                </div>
                <div class="flex space-x-3">
                    <Link 
                        :href="route('tasks.create')" 
                        class="bg-fleet-gradient hover:opacity-90 text-white px-6 py-3 rounded-lg font-medium transition-all duration-200 hover:shadow-lg hover:shadow-orange-500/25"
                    >
                        + New Task
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <!-- Stats Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Total Tasks Card -->
                    <div class="bg-fleet-darker border border-fleet-accent/20 rounded-xl p-6 hover:border-fleet-accent/40 transition-all duration-300 hover:shadow-lg hover:shadow-fleet-accent/10">
                        <div class="flex items-center justify-between">
                                  <div>
                                      <p class="text-fleet-text-muted text-sm font-medium">Total Tasks</p>
                                      <p class="text-3xl font-bold text-fleet-text">{{ props.stats?.totalTasks || 0 }}</p>
                                  </div>
                            <div class="w-12 h-12 rounded-lg flex items-center justify-center bg-fleet-dark border border-fleet-accent/20">
                                <svg class="w-6 h-6 text-fleet-gradient" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Completed Tasks Card -->
                    <div class="bg-fleet-darker border border-fleet-success/20 rounded-xl p-6 hover:border-fleet-success/40 transition-all duration-300 hover:shadow-lg hover:shadow-fleet-success/10">
                        <div class="flex items-center justify-between">
                                  <div>
                                      <p class="text-fleet-text-muted text-sm font-medium">Completed</p>
                                      <p class="text-3xl font-bold text-fleet-success">{{ props.stats?.completedTasks || 0 }}</p>
                                      <p class="text-fleet-text-muted text-sm">{{ completionRate }}% completion rate</p>
                                  </div>
                            <div class="w-12 h-12 bg-fleet-success/10 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-fleet-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Due Soon Card -->
                    <div class="bg-fleet-darker border border-fleet-warning/20 rounded-xl p-6 hover:border-fleet-warning/40 transition-all duration-300 hover:shadow-lg hover:shadow-fleet-warning/10">
                        <div class="flex items-center justify-between">
                                  <div>
                                      <p class="text-fleet-text-muted text-sm font-medium">Due Soon</p>
                                      <p class="text-3xl font-bold text-fleet-warning">{{ props.stats?.dueSoon || 0 }}</p>
                                      <p class="text-fleet-text-muted text-sm">Next 3 days</p>
                                  </div>
                            <div class="w-12 h-12 bg-fleet-warning/10 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-fleet-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Overdue Card -->
                    <div class="bg-fleet-darker border border-fleet-danger/20 rounded-xl p-6 hover:border-fleet-danger/40 transition-all duration-300 hover:shadow-lg hover:shadow-fleet-danger/10">
                        <div class="flex items-center justify-between">
                                  <div>
                                      <p class="text-fleet-text-muted text-sm font-medium">Overdue</p>
                                      <p class="text-3xl font-bold text-fleet-danger">{{ props.stats?.overdue || 0 }}</p>
                                      <p class="text-fleet-text-muted text-sm">Requires attention</p>
                                  </div>
                            <div class="w-12 h-12 bg-fleet-danger/10 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-fleet-danger" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions & Recent Tasks -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Quick Actions -->
                    <div class="lg:col-span-1">
                    <div class="glow-wrap" @mousemove="(e)=>{const r=e.currentTarget.getBoundingClientRect(); e.currentTarget.style.setProperty('--gx', `${e.clientX-r.left}px`); e.currentTarget.style.setProperty('--gy', `${e.clientY-r.top}px`) }">
                        <div class="bg-fleet-darker border border-fleet-accent/20 rounded-xl p-6 glow-follow">
                            <h3 class="text-xl font-semibold text-fleet-text mb-4">Quick Actions</h3>
                            <div class="space-y-3">
                                <Link 
                                    :href="route('tasks.index')" 
                                    class="flex items-center p-3 bg-fleet-dark/50 rounded-lg hover:opacity-90 transition-all duration-200 group"
                                >
                                    <svg class="w-5 h-5 text-fleet-gradient mr-3 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                    <span class="text-fleet-text group-hover:text-fleet-gradient transition-colors">View All Tasks</span>
                                </Link>
                                
                                <Link 
                                    :href="route('categories.index')" 
                                    class="flex items-center p-3 bg-fleet-dark/50 rounded-lg hover:opacity-90 transition-all duration-200 group"
                                >
                                    <svg class="w-5 h-5 text-fleet-gradient mr-3 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                    </svg>
                                    <span class="text-fleet-text group-hover:text-fleet-gradient transition-colors">Manage Categories</span>
                                </Link>
                                
                                <Link 
                                    :href="route('tasks.export')" 
                                    class="flex items-center p-3 bg-fleet-dark/50 rounded-lg hover:opacity-90 transition-all duration-200 group"
                                >
                                    <svg class="w-5 h-5 text-fleet-gradient mr-3 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <span class="text-fleet-text group-hover:text-fleet-gradient transition-colors">Export Tasks</span>
                                </Link>
                            </div>
                        </div>
                        </div>
                    </div>

                    <!-- Recent Tasks -->
                    <div class="lg:col-span-2">
                    <div class="glow-wrap" @mousemove="(e)=>{const r=e.currentTarget.getBoundingClientRect(); e.currentTarget.style.setProperty('--gx', `${e.clientX-r.left}px`); e.currentTarget.style.setProperty('--gy', `${e.clientY-r.top}px`) }">
                        <div class="bg-fleet-darker border border-fleet-accent/20 rounded-xl p-6 glow-follow">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-xl font-semibold text-fleet-text">Recent Tasks</h3>
                                <Link 
                                    :href="route('tasks.index')" 
                                    class="text-fleet-gradient hover:opacity-90 text-sm font-medium transition-colors"
                                >
                                    View All
                                </Link>
                            </div>
                                  <div class="space-y-3">
                                      <div 
                                          v-for="task in (props.recentTasks || [])" 
                                          :key="task.id"
                                          class="flex items-center justify-between p-4 bg-fleet-dark/30 rounded-lg border border-fleet-accent/10 hover:border-fleet-accent/30 transition-all duration-200"
                                      >
                                          <div class="flex items-center space-x-3">
                                              <div class="w-2 h-2 rounded-full bg-fleet-gradient"></div>
                                              <div>
                                                  <p class="text-fleet-text font-medium">{{ task.title }}</p>
                                                  <p class="text-fleet-text-muted text-sm">{{ task.category }}</p>
                                              </div>
                                          </div>
                                          <div class="flex items-center space-x-3">
                                              <span 
                                                  :class="getPriorityColor(task.priority)"
                                                  class="px-2 py-1 rounded-full text-xs font-medium border"
                                              >
                                                  {{ task.priority }}
                                              </span>
                                              <span class="text-fleet-text-muted text-sm">{{ task.due }}</span>
                                              <div v-if="task.completed" class="w-5 h-5 bg-fleet-success rounded-full flex items-center justify-center">
                                                  <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                      <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                  </svg>
                                              </div>
                                          </div>
                                      </div>
                                  </div>
                        </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
