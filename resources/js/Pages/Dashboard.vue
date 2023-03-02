<script setup>
import {Head} from '@inertiajs/vue3';
import Default from "@/Layouts/Default.vue";
import {reactive} from "vue";

const data = reactive({
    announce: {
        openDialog: false,
        id: 0
    }
});
</script>

<template>

    <Default>
        <Head title="Dashboard"/>

        <a href="#announces">
            <VRow no-gutters>
                <h3>Lista de anúncios</h3>
                <VSpacer/>
                <VBtn variant="text" @click="data.announce.openDialog = true">
                    <VIcon>mdi-plus</VIcon>
                </VBtn>
            </VRow>
        </a>

        <VRow no-gutters justify="center">
            <h4>Você ainda não criou nenhum anúncio.</h4>
        </VRow>

        <a href="#schedule"><h3>Agenda</h3></a>

        <VDialog max-width="1200" v-model="data.announce.openDialog">
            <VCard>
                <VCardTitle>
                    <VRow no-gutters>
                        <h3>{{ data.announce.id === 0 ? 'Novo anúncio' : `Alterar anúncio #${data.announce.id}` }}</h3>
                        <VSpacer/>
                        <VBtn variant="text" @click="data.announce.openDialog = false">
                            <VIcon>mdi-close</VIcon>
                        </VBtn>
                    </VRow>
                </VCardTitle>
                <VCardText>
                    <VForm>
                        <VRow>
                            <VCol cols="12" sm="6">
                                <h4>Informações do anúncio:</h4>
                                <VTextField label="Título" placeholder="Título do anúncio"/>
                                <VTextField label="Descrição" placeholder="Descrição do anúncio"/>
                                <VTextField label="Preço" placeholder="Preço do anúncio"/>
                                <VTextField label="Imagem" placeholder="Imagem do anúncio"/>
                            </VCol>
                            <VDivider vertical/>
                            <VCol cols="12" sm="6">
                                <h4>Lista de horários disponíveis</h4>

                                <VRow no-gutters>
                                    <VCol cols="12" sm="6">
                                        <VTextField label="Dia da semana" placeholder="Dia da semana"/>
                                    </VCol>
                                    <VCol cols="12" sm="6">
                                        <VTextField label="Horário" placeholder="Horário"/>
                                    </VCol>
                                </VRow>
                                <VBtn variant="text">
                                    <VIcon>mdi-plus</VIcon>
                                </VBtn>
                            </VCol>
                        </VRow>
                    </VForm>
                </VCardText>
                <VCardActions>
                    <VRow no-gutters justify="end">
                        <VBtn color="primary">Salvar</VBtn>
                    </VRow>
                </VCardActions>
            </VCard>
        </VDialog>
    </Default>
</template>
