<script setup>
import {Head, Link, useForm} from '@inertiajs/vue3';
import Default from "@/Layouts/Default.vue";
import AppErrors from "@/Components/AppErrors.vue";
import Validation from "@/Tools/Validation";

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    terms: false,
    passwordVisible: false
});

const submit = async (evt) => {
    const result = await evt
    if (!result.valid) return;

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

        <VRow justify="center">
            <VCol md="6">
                <VForm validate-on="submit" @submit.prevent="submit">
                    <VTextField
                        v-model="form.name"
                        :rules="new Validation('nome').required().get()"
                        label="Nome"
                        name="name"/>
                    <VTextField
                        v-model="form.email"
                        :rules="new Validation('email').required().email().get()"
                        label="Email"
                        name="email"
                    />
                    <VTextField
                        v-model="form.password"
                        :append-icon="form.passwordVisible ? 'mdi-eye-off' : 'mdi-eye'"
                        :rules="new Validation('senha').required().get()"
                        :type="form.passwordVisible ? 'text' : 'password'"
                        label="Senha"
                        name="password"
                        @click:append="() => (form.passwordVisible = !form.passwordVisible)"/>
                    <VTextField
                        v-model="form.password_confirmation"
                        :append-icon="form.passwordVisible ? 'mdi-eye-off' : 'mdi-eye'"
                        :rules="new Validation('confirmar senha').required().get()"
                        :type="form.passwordVisible ? 'text' : 'password'"
                        label="Confirmar Senha"
                        name="password_confirmation"
                        @click:append="() => (form.passwordVisible = !form.passwordVisible)"/>
                    <VRow justify="space-between" no-gutters style="padding-top: 2rem">
                        <div>
                            <Link :href="route('login')">Já cadastrado?</Link>
                            <br>
                            <Link :href="route('password.request')">Esqueceu a senha?</Link>
                        </div>
                        <VBtn color="primary" type="submit">Cadastrar</VBtn>
                    </VRow>
                </VForm>

            </VCol>
        </VRow>
        <AppErrors :errors="form.errors"></AppErrors>
    </Default>
</template>
