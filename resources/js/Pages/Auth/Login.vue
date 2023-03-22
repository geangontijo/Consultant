<script setup>

import {Head, Link, useForm} from '@inertiajs/vue3';
import Default from "@/Layouts/Default.vue";
import AppErrors from '@/Components/AppErrors.vue';
import {reactive} from "vue";
import AuthLayout from "@/Layouts/AuthLayout.vue";

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const data = reactive({
    loading: false,
})

const form = useForm({
    email: '',
    password: '',
    passwordVisible: false
});
const submit = async (event) => {
    let resultValidation = await event
    if (!resultValidation.valid) return;

    data.loading = true;
    form.post(route('login'), {
        onSuccess: () => form.reset('password'),
        onFinish: () => data.loading = false,
    });
};
</script>

<template>
    <Default>
        <Head title="Entrar"/>
        <AuthLayout>
            <VForm validate-on="submit" @submit.prevent="submit">
                <VTextField
                    v-model="form.email"
                    :rules="[() => !!form.email || 'O e-mail é obrigatório']"
                    autocomplete="email"
                    label="E-mail"
                    type="email"
                />

                <VTextField
                    v-model="form.password"
                    :append-icon="form.passwordVisible ? 'mdi-eye-off' : 'mdi-eye'"
                    :rules="[() => !!form.password || 'A senha é obrigatória']"
                    :type="form.passwordVisible ? 'text' : 'password'"
                    autocomplete="password"
                    label="Senha"
                    @click:append="() => (form.passwordVisible = !form.passwordVisible)"
                />

                <VRow no-gutters>
                    <Link
                        v-if="canResetPassword"
                        :href="route('password.request')"
                    >
                        Esqueceu sua senha?
                    </Link>
                    <VSpacer/>
                    <VBtn :loading="data.loading" color="primary" type="submit">
                        Login
                    </VBtn>
                </VRow>
            </VForm>
            <AppErrors :errors="form.errors"/>
        </AuthLayout>
    </Default>
</template>
