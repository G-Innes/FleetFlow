<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Log in" />

        <div v-if="status" class="mb-4 text-sm font-medium text-fleet-success">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-5">
            <div>
                <InputLabel for="email" value="Email" class="text-fleet-text-muted" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full bg-fleet-dark border border-fleet-accent/20 text-fleet-text focus:border-fleet-accent focus:ring-2 focus:ring-fleet-accent/20"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                />

                <InputError class="mt-2 text-fleet-danger" :message="form.errors.email" />
            </div>

            <div>
                <InputLabel for="password" value="Password" class="text-fleet-text-muted" />

                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full bg-fleet-dark border border-fleet-accent/20 text-fleet-text focus:border-fleet-accent focus:ring-2 focus:ring-fleet-accent/20"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                />

                <InputError class="mt-2 text-fleet-danger" :message="form.errors.password" />
            </div>

            <div class="flex items-center justify-between">
                <label class="flex items-center">
                    <Checkbox name="remember" v-model:checked="form.remember" />
                    <span class="ms-2 text-sm text-fleet-text">Remember me</span>
                </label>

                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="text-sm text-fleet-text-muted hover:text-fleet-text underline"
                >
                    Forgot your password?
                </Link>
            </div>

            <PrimaryButton
                class="w-full bg-fleet-gradient hover:opacity-90"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
            >
                Log in
            </PrimaryButton>
        </form>
    </GuestLayout>
</template>
