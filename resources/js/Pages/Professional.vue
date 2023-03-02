<script setup>
import Default from "@/Layouts/Default.vue";
import {useForm, Head} from "@inertiajs/vue3";
import {Mask} from "maska";
import AppErrors from "@/Components/AppErrors.vue";
import {reactive} from "vue";
import Validation from "@/Tools/Validation";

const form = useForm({
    taxpayer_id: '',
    crm: '',
    phone_number: '',
    address: '',
    avatar: null,
    certificates: [null],
    verification_code: ''
});

function maskTaxpayerId(inputEvent) {
    const newVal = inputEvent.target.value.replaceAll(/[^\d]/g, '')
    form.taxpayer_id = String(new Mask({mask: '###.###.###-##'}).masked(newVal)).substring(0, 14)
}

function maskPhone(inputEvent) {
    const newVal = inputEvent.target.value.replaceAll(/[^\d]/g, '')
    form.phone_number = String(new Mask({mask: '(##) # ####-####'}).masked(newVal)).substring(0, 16)
}

async function submit(evt) {
    if (data.step === 'form') {
        const result = await evt
        if (!result.valid) return;

        form
            .transform((data) => ({
                telefone: data.phone_number
            }))
            .post(route('user.verify'), {
                onSuccess: () => data.step = 'confirmation'
            })

        return
    }
    form
        .transform((data) => ({
            telefone: data.phone_number,
            cpf: data.taxpayer_id,
            foto_perfil: data.avatar,
            codigo_verificacao: data.verification_code
        }))
        .post(route('professional.store'))
}

const data = reactive({
    step: 'form'
});

</script>

<template>
    <Default>
        <Head title="Se tornar um profissional"></Head>
        <h3>Antes de anunciar um serviço você precisa preencher os seguintes dados</h3>
        <p>Preencha os dados abaixo.</p>
        <br>
        <VForm @submit.prevent="submit" :disabled="form.processing" validate-on="submit">
            <VWindow v-model="data.step">
                <VWindowItem value="form">
                    <VRow no-gutters>
                        <VCol cols="4">
                            Foto de perfil <small class="text-error">* Obrigatório</small>
                        </VCol>
                        <VCol cols="8">
                            <VFileInput
                                accept="image/*"
                                @input="inputEvent => form.avatar = inputEvent.target.files[0]"
                                :rules="new Validation('Foto de perfil').required().get()"
                                key="fileInputKey.1"/>
                        </VCol>
                    </VRow>
                    <VRow no-gutters>
                        <VCol cols="4">
                            CPF <small class="text-error">* Obrigatório</small>
                        </VCol>
                        <VCol cols="8">
                            <VTextField
                                :model-value="form.taxpayer_id"
                                @input="maskTaxpayerId"
                                maxlength="14"
                                :rules="new Validation('CPF').required().get()"/>
                        </VCol>
                    </VRow>
                    <VRow no-gutters>
                        <VCol cols="4">
                            Telefone <small class="text-error">* Obrigatório</small>
                        </VCol>
                        <VCol cols="8">
                            <VTextField
                                maxlength="16"
                                :model-value="form.phone_number"
                                @input="maskPhone"
                                :rules="new Validation('Telefone').required().get()"/>
                        </VCol>
                    </VRow>
                    <VRow no-gutters>
                        <VCol cols="4">
                            CRM <small class="text-info">Preencher somente se você fornecer serviços de saúde</small>
                        </VCol>
                        <VCol cols="8">
                            <VTextField maxlength="3">

                            </VTextField>
                        </VCol>
                    </VRow>
                    <VRow no-gutters>
                        <VCol cols="4">
                            Certificados <small class="text-info">Preencher somente se você fornecer serviços de
                            saúde</small>
                        </VCol>
                        <VCol cols="8">
                            <VFileInput v-for="(certificate, key) in form.certificates">
                                <template v-slot:append>
                                    <VBtn v-if="key > 0"
                                          @click="form.certificates.splice(key, 1)"
                                          color="error"
                                          variant="plain">
                                        <VIcon>mdi-delete</VIcon>
                                    </VBtn>
                                    <VBtn v-else variant="plain" @click="form.certificates.push(null)">
                                        <VIcon>mdi-plus</VIcon>
                                    </VBtn>
                                </template>
                            </VFileInput>
                        </VCol>
                    </VRow>
                    <VRow no-gutters>
                        <VCol cols="4">
                            Localização <small class="text-info">Preencher somente se você fornecer serviços
                            presenciais</small>
                        </VCol>
                        <VCol cols="8">
                            <VTextField label="Endereço" v-model="form.address"/>
                            <!--                    TODO: Implementar o componente de localização-->
                        </VCol>
                    </VRow>
                    <VRow no-gutters justify="end">
                        <VBtn type="submit"
                              color="primary"
                              append-icon="mdi-arrow-right"
                              v-text="'Prosseguir'"
                              :loading="form.processing"/>
                        <!--                <VBtn color="primary" type="submit" :loading="form.processing">Salvar</VBtn>-->
                    </VRow>
                </VWindowItem>
                <VWindowItem value="confirmation">
                    Enviamos um código de verificação para o seu <strong>e-mail</strong>. Por favor, insira o código
                    abaixo.

                    <VTextField
                        label="Código de verificação"
                        hint="Digite o código de 6 números"
                        counter
                        maxlength="6"
                        v-model="form.verification_code"/>
                    <br>
                    <VRow no-gutters justify="end">
                        <VBtn type="button"
                              color="error"
                              :disabled="form.processing"
                              @click="data.step = 'form'"
                              prepend-icon="mdi-arrow-left"
                              v-text="'Voltar'"/>
                        <VSpacer/>
                        <VBtn color="primary" type="submit" :loading="form.processing" v-text="'Salvar'"/>
                    </VRow>
                </VWindowItem>
            </VWindow>
        </VForm>
        <AppErrors :errors="form.errors"></AppErrors>
    </Default>
</template>

<style scoped>

</style>
