<script setup>
import {nextTick, reactive, watch} from "vue";

const props = defineProps({
    errors: Object,
    title: {
        type: String,
        default: 'Erros'
    },
});

const data = reactive({
    filteredErrors: {}
})
watch(() => props.errors, (newV) => {
    data.filteredErrors = [];
    nextTick(() => {
        data.filteredErrors = Object.keys(JSON.parse(JSON.stringify(newV))).length > 0 ? newV : {};
    })
});
</script>

<template>
    <VDialog :model-value="Object.keys(data.filteredErrors).length > 0" max-width="500">
        <VCard>
            <VToolbar :title="props.title" color="error">
                <VSpacer/>
                <VBtn icon @click="data.filteredErrors = []">
                    <VIcon>mdi-close</VIcon>
                </VBtn>
            </VToolbar>
            <VCardText>
                <ul>
                    <li v-for="(error, key) in data.filteredErrors" :key="key">
                        <h4> {{ error.name }} </h4>
                        <p v-for="message in error.messages">{{ message }}</p>
                    </li>
                </ul>
            </VCardText>
        </VCard>
    </VDialog>
</template>

<style scoped>

</style>
