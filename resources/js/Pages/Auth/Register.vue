<script setup>
import {Head, Link, useForm} from '@inertiajs/vue3';
import Default from "@/Layouts/Default.vue";
import AppErrors from "@/Components/AppErrors.vue";

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    terms: false,
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Default>
        <br>
        <br>
        <Head title="Cadastro"/>

        <VForm @submit.prevent="submit">
            <VTextField label="Nome" v-model="form.name"></VTextField>
            <VTextField label="Email" v-model="form.email"></VTextField>
            <VTextField label="Senha" type="password" v-model="form.password"></VTextField>
            <VTextField label="Confirmar Senha" type="password" v-model="form.password_confirmation"></VTextField>
            <VRow no-gutters justify="space-between">
                <Link :href="route('login')">Já cadastrado?</Link>
                <VBtn type="submit" color="primary">Cadastrar</VBtn>
            </VRow>
        </VForm>
        <AppErrors :errors="form.errors"></AppErrors>
    </Default>
</template>
