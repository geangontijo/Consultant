<script setup>

import Default from "@/Layouts/Default.vue";
import {useForm, usePage} from "@inertiajs/vue3";
import JsBarcode from "jsbarcode";
import {nextTick, onMounted, reactive, watch} from "vue";
import {Mask} from 'maska'

const page = usePage();

const data = reactive({
    paymentMethodsPanels: []
})

const form = useForm({
    cardNumber: '',
    validDate: '',
    paymentMethodId: 0
});

function generateBarcode(evt, paymentMethod) {
    if (!evt.value) return;

    nextTick(() => {
        JsBarcode("#barcode", paymentMethod.metadata.barcode);
    });
}

onMounted(() => {
    data.paymentMethodsPanels = Array.from(Array(page.props.order.payments.length).keys())
})

function moneyFilter(value) {
    if (!value) value = 0;

    value = parseFloat(value)

    return value.toLocaleString('pt-BR', {style: 'currency', currency: 'BRL'});
}

watch(() => form.cardNumber, function (newVal) {
    form.cardNumber = String(new Mask({ mask: '####-####-####-####' }).masked(newVal)).substring(0, 19)
})

watch(() => form.validDate, function (newVal) {
    form.validDate = String(new Mask({ mask: '##/##' }).masked(newVal)).substring(0, 5)
})

async function confirmPaymentCreditCard(evt, paymentMethodId) {
    const validateData = await evt;
    if (validateData.valid === false) return

    form.paymentMethodId = paymentMethodId
    page.props.loading = true
    form.post(route('checkout.pay', page.props.order.id), {
        onFinish: () => page.props.loading = false
    })
}

</script>

<template>
    <Default>
        <h3>Valor dos itens: {{ moneyFilter(page.props.order.items_amount) }} </h3>
        <h3>Meios de pagamento</h3>

        <v-expansion-panels multiple v-model="data.paymentMethodsPanels">
            <v-expansion-panel @group:selected="evt => generateBarcode(evt, paymentMethod)" :key="key"
                               :title="paymentMethod.method === 'bank_slip' ? 'Boleto bancário' : 'Cartão de crédito'"
                               v-for="(paymentMethod, key) in page.props.order.payments">
                <VExpansionPanelText v-if="paymentMethod.method === 'bank_slip'">
                    <h3>Valor total: {{ moneyFilter(paymentMethod.amount) }}</h3>
                    <VRow>
                        <VCol cols="6">
                            <svg id="barcode"></svg>
                        </VCol>
                        <VCol cols="6">
                            <VBtn block :href="paymentMethod.metadata.pdf" target="_blank" size="x-large">
                                <VIcon size="x-large">mdi-file-pdf-box</VIcon>
                                PDF
                            </VBtn>
                        </VCol>
                    </VRow>
                    Faça o pagamento do boleto para confirmar o pedido.
                </VExpansionPanelText>
                <VExpansionPanelText v-else-if="paymentMethod.method === 'credit_card'">
                    <h3>Valor total: {{ moneyFilter(paymentMethod.amount) }}</h3>
                    <VForm @submit.prevent="(evt) => confirmPaymentCreditCard(evt, paymentMethod.id)" validate-on="submit">
                        <VTextField label="Nome do titular"/>
                        <VTextField maxlength="19" v-model="form.cardNumber" label="Número do cartão"/>
                        <VTextField maxlength="5" label="Validade" v-model="form.validDate"/>
                        <VTextField maxlength="4" label="Código de segurança"/>
                        <VBtn type="submit" color="primary">Confirmar pagamento</VBtn>
                    </VForm>
                </VExpansionPanelText>
            </v-expansion-panel>
        </v-expansion-panels>
    </Default>
</template>

<style scoped>

</style>
