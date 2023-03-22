<script setup>
import {Head, useForm} from '@inertiajs/vue3';
import Default from "@/Layouts/Default.vue";
import AuthLayout from "@/Layouts/AuthLayout.vue";
import Validation from "@/Tools/Validation";
import Filters from "@/Tools/Filters";
import AppErrors from "@/Components/AppErrors.vue";

const form = useForm({
    email: '',
    phone_number: '',
});

const submit = async (evt) => {
    const result = await evt;
    if (!result.valid) return;

    form.post(route('password.forgot'));
};
</script>

<template>
    <Default>
        <AuthLayout>
            <Head title="Recuperar senha"/>

            <VForm :disabled="form.processing" validate-on="submit" @submit.prevent="submit">
                <VTextField
                    v-model="form.email"
                    :rules="new Validation('email').if(!form.phone_number, self => self.required().email()).get()"
                    autofocus
                    hide-details
                    label="Email"
                    name="email"/>
                <VRow align="center" class="my-1">
                    <VCol cols="5">
                        <VDivider/>
                    </VCol>
                    <VCol class="text-center" cols="2">
                        <span class="mx-3">Ou</span>
                    </VCol>
                    <VCol cols="5">
                        <VDivider/>
                    </VCol>
                </VRow>
                <VTextField
                    :model-value="form.phone_number"
                    :rules="new Validation('telefone').if(!form.email, self => self.required()).get()"
                    label="Telefone"
                    maxlength="16"
                    name="phone_number"
                    @input="evt => form.phone_number = Filters.maskPhone(evt.target.value)"/>

                <VRow no-gutters>
                    <a :href="route('login')">Fazer login</a>
                    <VSpacer/>
                    <VBtn
                        :loading="form.processing"
                        color="primary"
                        type="submit"
                    >Recuperar senha
                    </VBtn>
                </VRow>
            </VForm>

            <AppErrors :errors="form.errors"/>
        </AuthLayout>
    </Default>
</template>
