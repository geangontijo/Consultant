<script setup>
import Default from "@/Layouts/Default.vue";
import {Head, Link, useForm} from '@inertiajs/vue3';

defineProps({
    announces: Object
})
</script>

<template>
    <Head title="Bem Vindo!"/>
    <Default>
        <h3>Lista de anúncios</h3>

        <VRow v-if="announces.data.length" no-gutters>
            <VCol cols="3" v-for="announce in announces.data">
                <Link :href="route(`announce`, [announce.id])">
                    <VCard link>
                        <VImg :src="announce.professional.photo_url"></VImg>
                        <VCardText>
                            <VAvatar></VAvatar>
                            {{ announce.category_name }}
                        </VCardText>
                    </VCard>
                </Link>
            </VCol>
        </VRow>
        <div v-else>
            Nenhum registro encontrado.
        </div>
    </Default>
</template>
