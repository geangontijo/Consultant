<script setup>
import {Head, useForm} from '@inertiajs/vue3';
import Default from "@/Layouts/Default.vue";
import moment from 'moment'
import {Mask} from "maska";
import Validation from "@/Tools/Validation";
import {watch} from "vue";
import AppErrors from "@/Components/AppErrors.vue";

const formAnnounce = useForm({
    openDialog: false,
    id: 0,
    category: '',
    description: '',
    defaultPrice: null,
    appointment_times: [
        defaultAppointTime()
    ]
});

const props = defineProps({
    announces: Array,
    sales: Array
});

function resetForm() {
    formAnnounce.reset('id', 'defaultPrice', 'description', 'category', 'appointment_times')
}

function maskMoney(value) {
    // make a mask to money

    return new Mask({
        mask: '9.99#,##', tokens: {
            9: {
                pattern: /[0-9]/,
                repeated: true,
            }
        },
        reversed: true
    }).masked(value)
}

function maskDate(value) {
    // make a mask to date

    return new Mask({
        mask: '##/##/####'
    }).masked(value).substring(0, 10)
}

function maskHour(value) {

    return new Mask({
        mask: '##:##'
    }).masked(value).substring(0, 5)
}

watch(() => formAnnounce.defaultPrice, function (newVal, oldVal) {
    formAnnounce.appointment_times.forEach(appointment_time => {
        appointment_time.price = appointment_time.price === oldVal ? newVal : appointment_time.price
    })
})

function defaultAppointTime() {
    let price;
    try {
        price = formAnnounce.defaultPrice
    } catch {
        price = null
    }
    return {
        day: '',
        start: '',
        end: '',
        price: price
    }
}

async function submitFormAnnounce(evt) {
    const result = await evt

    console.log(result)

    if (!result.valid) return;

    formAnnounce
        .transform(data => ({
            appointment_times: data.appointment_times.map(appointment_time => ({
                start: moment(appointment_time.day + appointment_time.start, 'DD/MM/YYYYHH:mm').toDate().toISOString(),
                end: moment(appointment_time.day + appointment_time.end, 'DD/MM/YYYYHH:mm').toDate().toISOString(),
                price: (Number(appointment_time.price.replaceAll(/[^0-9]/g, '')) / 100).toFixed(2)
            })),
            category: data.category,
            description: data.description
        }))
        .post(route('announce.store'), {
            onSuccess: () => {
                formAnnounce.openDialog = false
                resetForm()
            }
        })
}

function editAnnounce(announce) {
    formAnnounce.defaultPrice = null;
    formAnnounce.openDialog = true;
    formAnnounce.description = announce.description;
    formAnnounce.category = announce.category_name;
    formAnnounce.id = announce.id;
    formAnnounce.appointment_times = announce.appointment_times.map(appointment => ({
        day: moment(appointment.start).format('DD/MM/YYYY'),
        start: moment(appointment.start).format('HH:mm'),
        end: moment(appointment.end).format('HH:mm'),
        price: maskMoney(appointment.price)
    }));
}

function removeAnnounce() {
    formAnnounce.delete(route('announce.destroy', formAnnounce.id), {
        onSuccess: () => {
            formAnnounce.openDialog = false
            resetForm()
        }
    })
}

</script>

<template>

    <Default>
        <Head title="Dashboard"/>

        <AppErrors :errors="formAnnounce.errors"/>

        <a href="#announces">
            <VRow no-gutters>
                <h3>Lista de anúncios</h3>
                <VSpacer/>
                <VBtn variant="text" @click="resetForm();
                formAnnounce.openDialog = true">
                    <VIcon>mdi-plus</VIcon>
                </VBtn>
            </VRow>
        </a>
        <VRow v-if="props.announces.length === 0" no-gutters justify="center">
            <h4>Você ainda não criou nenhum anúncio.</h4>
        </VRow>
        <VRow>
            <VCol cols="12" sm="6" v-for="announce in props.announces">
                <VCard variant="elevated">
                    <VCardTitle>
                        <VRow no-gutters align="center">
                            #{{ announce.id }} - {{ announce.category_name }}
                            <VSpacer/>
                            <VBtn variant="text" @click="resetForm(); editAnnounce(announce)">
                                <VIcon>mdi-pencil</VIcon>
                            </VBtn>
                        </VRow>
                    </VCardTitle>
                    <VCardActions>
                        <VList density="compact">
                            <VListItem>Criado em: {{ moment(announce.created_at).fromNow() }}</VListItem>
                            <VListItem>Atualizado em: {{ moment(announce.updated_at).fromNow() }}</VListItem>
                        </VList>
                    </VCardActions>
                </VCard>
            </VCol>
        </VRow>

        <a href="#schedule">
            <VRow no-gutters>
                <h3>Agenda</h3>
                <VSpacer/>
                <VMenu>
                    <template v-slot:activator="{ props }">
                        <VBtn variant="text" v-bind="props">
                            Exportar agenda
                        </VBtn>
                    </template>

                    <VList density="compact">
                        <VListItem link>
                            <VListItemTitle>
                                <VIcon>mdi-image</VIcon>
                                Imagem
                            </VListItemTitle>
                        </VListItem>

                        <VListItem link>
                            <VListItemTitle>
                                <VIcon>mdi-file-pdf-box</VIcon>
                                PDF
                            </VListItemTitle>
                        </VListItem>
                    </VList>
                </VMenu>
            </VRow>
        </a>

        <a href="#future-balance">
            <h3>Saldo futuro</h3>
        </a>

        <VCard>
            <VCardTitle>
                R$ {{ maskMoney('0.00') }}
            </VCardTitle>

            <VDivider />

            <VCard>
                <VCardTitle>Vendas:</VCardTitle>

                <VCardText>
                    <VList>
                        <VListItem :key="key" v-for="(sale, key) in props.sales">
                            <VListItemTitle>
                                <VIcon>mdi-cash</VIcon>
                                {{ moment(sale.updated_at).fromNow() }}
                            </VListItemTitle>
                            <VListItemSubtitle>R$ {{ maskMoney(sale.items_amount) }}</VListItemSubtitle>
                        </VListItem>
                    </VList>
                </VCardText>
            </VCard>
        </VCard>

        <VDialog max-width="1200" v-model="formAnnounce.openDialog" :persistent="formAnnounce.processing">
            <VForm @submit.prevent="submitFormAnnounce" validate-on="submit" :disabled="formAnnounce.processing">
                <VCard>
                    <VCardTitle>
                        <VRow no-gutters>
                            <h3>{{
                                    formAnnounce.id === 0 ? 'Novo anúncio' : `Alterar anúncio #${formAnnounce.id}`
                                }}</h3>
                            <VSpacer/>
                            <VBtn type="button"
                                  variant="text"
                                  @click="resetForm();formAnnounce.openDialog = false"
                                  :disabled="formAnnounce.processing">
                                <VIcon>mdi-close</VIcon>
                            </VBtn>
                        </VRow>
                    </VCardTitle>
                    <VCardText>
                        <VRow>
                            <VCol cols="12" sm="6">
                                <h4>Informações do anúncio:</h4>
                                <VTextField v-model="formAnnounce.category"
                                            label="Categoria"
                                            placeholder="Categoria do anúncio"
                                            :rules="new Validation('categoria').required().get()"/>
                                <VTextarea v-model="formAnnounce.description"
                                           label="Descrição"
                                           placeholder="Descrição do anúncio"
                                           :rules="new Validation('descrição').required().get()"/>
                                <VTextField :model-value="formAnnounce.defaultPrice"
                                            @input="val => formAnnounce.defaultPrice = maskMoney(val.target.value)"
                                            :rules="new Validation('preço').money().greatestThan(0).get()"
                                            label="Preço"
                                            placeholder="Preço padrão de cada horário"
                                            prefix="R$"
                                />
                            </VCol>
                            <VDivider vertical/>
                            <VCol cols="12" sm="6">
                                <h4>Lista de horários disponíveis</h4>

                                <VRow no-gutters v-for="(appointment_time, key) in formAnnounce.appointment_times">
                                    <VCol cols="12" sm="5">
                                        <VTextField :model-value="appointment_time.day"
                                                    @input="val => appointment_time.day = maskDate(val.target.value)"
                                                    :rules="new Validation('dia da semana').required().date('DD/MM/YYYY')
                                                                .greatestOrEqualThan(
                                                                    new Date().setHours(0, 0, 0, 0)
                                                                ).get()"
                                                    maxlength="10"
                                                    hide-details
                                                    label="Dia da semana"
                                                    placeholder="dd/mm/yyyy"/>
                                    </VCol>
                                    <VCol cols="12" sm="2">
                                        <VTextField :model-value="appointment_time.start"
                                                    @input="val => appointment_time.start = maskHour(val.target.value)"
                                                    maxlength="5"
                                                    :rules="new Validation('início').required().time('HH:mm')
                                                        .if(appointment_time.day === moment().format('DD/MM/YYYY'),
                                                            self => self.greatestOrEqualThan(moment().seconds(0).milliseconds(0).toDate())
                                                        ).get()"
                                                    hide-details
                                                    label="Início"
                                                    placeholder="Horário"/>
                                    </VCol>
                                    <VCol cols="12" sm="2">
                                        <VTextField :model-value="appointment_time.end"
                                                    @input="val => appointment_time.end = maskHour(val.target.value)"
                                                    maxlength="5"
                                                    :rules="new Validation('final').required().time('hh:mm')
                                                        .if(appointment_time.day === moment().format('DD/MM/YYYY'),
                                                            self => self.greatestThan(moment(appointment_time.start, 'hh:mm').toDate())
                                                           ).get()"
                                                    hide-details label="Fim"
                                                    placeholder="Horário"
                                        />
                                    </VCol>
                                    <VCol cols="12" sm="2">
                                        <VTextField :model-value="appointment_time.price"
                                                    @input="val => appointment_time.price = maskMoney(val.target.value)"
                                                    hide-details
                                                    :rules="new Validation('preço').required().money().greatestOrEqualThan(0).get()"
                                                    label="Preço"
                                                    placeholder="Preço"
                                                    prefix="R$"
                                        />
                                    </VCol>
                                    <VCol cols="12" sm="1">
                                        <VBtn type="button" variant="text"
                                              @click="formAnnounce.appointment_times.splice(key, 1)"
                                              style="height: 100%">
                                            <VIcon>mdi-delete</VIcon>
                                        </VBtn>
                                    </VCol>
                                </VRow>

                                <VBtn type="button"
                                      variant="text"
                                      @click="formAnnounce.appointment_times.push(defaultAppointTime())">
                                    <VIcon>mdi-plus</VIcon>
                                </VBtn>
                            </VCol>
                        </VRow>
                    </VCardText>
                    <VCardActions>
                        <VRow type="submit" no-gutters justify="end">
                            <VDialog max-width="500" :persistent="formAnnounce.processing">
                                <template v-slot:activator="{ props }">
                                    <VBtn v-if="formAnnounce.id" color="error" v-bind="props">Remover anuncio</VBtn>
                                </template>

                                <VCard>
                                    <VCardTitle>Deseja realmente remover esse anúncio?</VCardTitle>
                                    <VDivider/>

                                    <VCardText>
                                        <b>Atenção: </b> Essa operação não pode ser desfeita.
                                    </VCardText>

                                    <VCardActions>
                                        <VRow no-gutters justify="end">
                                            <VBtn type="button" color="primary" @click="formAnnounce.openDialog = false"
                                                  :disabled="formAnnounce.processing">Cancelar
                                            </VBtn>
                                            <VBtn type="button" color="error" @click="removeAnnounce"
                                                  :loading="formAnnounce.processing">Remover
                                            </VBtn>
                                        </VRow>
                                    </VCardActions>
                                </VCard>
                            </VDialog>
                            <VBtn :loading="formAnnounce.processing" type="submit" color="primary">Salvar</VBtn>
                        </VRow>
                    </VCardActions>
                </VCard>
            </VForm>
        </VDialog>
    </Default>
</template>
