<script setup>

import {Head, Link, useForm} from '@inertiajs/vue3';
import Default from "@/Layouts/Default.vue";
import AppErrors from '@/Components/AppErrors.vue';
import {reactive} from "vue";

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

        <AppErrors :errors="form.errors"/>
        <br>
        <br>
        <br>
        <VRow no-gutters justify="center" align="center">
            <VCol cols="6">
                <VForm @submit.prevent="submit" validate-on="submit">
                    <VTextField
                        label="E-mail"
                        type="email"
                        v-model="form.email"
                        autocomplete="email"
                        :rules="[() => !!form.email || 'O e-mail é obrigatório']"
                    />

                    <VTextField
                        label="Senha"
                        type="password"
                        v-model="form.password"
                        autocomplete="password"
                        :rules="[() => !!form.password || 'A senha é obrigatória']"
                    />

                    <Link
                        v-if="canResetPassword"
                        :href="route('password.request')"
                        class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                        Esqueceu sua senha?
                    </Link>

                    <VBtn type="submit" class="ml-4" block color="primary" :loading="data.loading">
                        Login
                    </VBtn>
                </VForm>
            </VCol>
        </VRow>
    </Default>
</template>
