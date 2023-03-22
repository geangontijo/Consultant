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
    sales: Array,
    categories: Array,
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
        <VRow v-if="props.announces.length === 0" justify="center" no-gutters>
            <h4>Você ainda não criou nenhum anúncio.</h4>
        </VRow>
        <VRow>
            <VCol v-for="announce in props.announces" cols="12" sm="6">
                <VCard variant="elevated">
                    <VCardTitle>
                        <VRow align="center" no-gutters>
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
                        <VBtn v-bind="props" variant="text">
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

            <VDivider/>

            <VCard>
                <VCardTitle>Vendas:</VCardTitle>

                <VCardText>
                    <VList>
                        <VListItem v-for="(sale, key) in props.sales" :key="key">
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

        <VDialog v-model="formAnnounce.openDialog" :persistent="formAnnounce.processing" max-width="1200">
            <VForm :disabled="formAnnounce.processing" validate-on="submit" @submit.prevent="submitFormAnnounce">
                <VCard>
                    <VCardTitle>
                        <VRow no-gutters>
                            <h3>{{
                                    formAnnounce.id === 0 ? 'Novo anúncio' : `Alterar anúncio #${formAnnounce.id}`
                                }}</h3>
                            <VSpacer/>
                            <VBtn :disabled="formAnnounce.processing"
                                  type="button"
                                  variant="text"
                                  @click="resetForm();formAnnounce.openDialog = false">
                                <VIcon>mdi-close</VIcon>
                            </VBtn>
                        </VRow>
                    </VCardTitle>
                    <VDivider/>
                    <VCardText>
                        <VRow>
                            <VCol cols="12" sm="6">
                                <h4>Informações do anúncio:</h4>
                                <VAutocomplete v-model="formAnnounce.category"
                                               :items="props.categories"
                                               :rules="new Validation('categoria').required().get()"
                                               label="Categoria"
                                               placeholder="Categoria do anúncio"/>
                                <VTextarea v-model="formAnnounce.description"
                                           :rules="new Validation('descrição').required().get()"
                                           label="Descrição"
                                           placeholder="Descrição do anúncio"/>
                                <VTextField :model-value="formAnnounce.defaultPrice"
                                            :rules="new Validation('preço').money().greatestThan(0).get()"
                                            label="Preço"
                                            placeholder="Preço padrão de cada horário"
                                            prefix="R$"
                                            @input="val => formAnnounce.defaultPrice = maskMoney(val.target.value)"
                                />
                            </VCol>
                            <VDivider vertical/>
                            <VCol cols="12" sm="6">
                                <h4>Lista de horários disponíveis</h4>

                                <VRow v-for="(appointment_time, key) in formAnnounce.appointment_times"
                                      :key="key"
                                      no-gutters>
                                    <VCol cols="12" sm="5">
                                        <VTextField :model-value="appointment_time.day"
                                                    :rules="new Validation('dia da semana').required().date('DD/MM/YYYY')
                                                                .greatestOrEqualThan(
                                                                    new Date().setHours(0, 0, 0, 0)
                                                                ).get()"
                                                    hide-details
                                                    label="Dia da semana"
                                                    maxlength="10"
                                                    placeholder="dd/mm/yyyy"
                                                    @input="val => appointment_time.day = maskDate(val.target.value)"/>
                                    </VCol>
                                    <VCol cols="12" sm="2">
                                        <VTextField :model-value="appointment_time.start"
                                                    :rules="new Validation('início').required().time('HH:mm')
                                                        .if(appointment_time.day === moment().format('DD/MM/YYYY'),
                                                            self => self.greatestOrEqualThan(moment().seconds(0).milliseconds(0).toDate())
                                                        ).get()"
                                                    hide-details
                                                    label="Início"
                                                    maxlength="5"
                                                    placeholder="Horário"
                                                    @input="val => appointment_time.start = maskHour(val.target.value)"/>
                                    </VCol>
                                    <VCol cols="12" sm="2">
                                        <VTextField :model-value="appointment_time.end"
                                                    :rules="new Validation('final').required().time('hh:mm')
                                                        .if(appointment_time.day === moment().format('DD/MM/YYYY'),
                                                            self => self.greatestThan(moment(appointment_time.start, 'hh:mm').toDate())
                                                           ).get()"
                                                    hide-details
                                                    label="Fim"
                                                    maxlength="5" placeholder="Horário"
                                                    @input="val => appointment_time.end = maskHour(val.target.value)"
                                        />
                                    </VCol>
                                    <VCol cols="12" sm="2">
                                        <VTextField :model-value="appointment_time.price"
                                                    :rules="new Validation('preço').required().money().greatestOrEqualThan(0).get()"
                                                    hide-details
                                                    label="Preço"
                                                    placeholder="Preço"
                                                    prefix="R$"
                                                    @input="val => appointment_time.price = maskMoney(val.target.value)"
                                        />
                                    </VCol>
                                    <VCol cols="12" sm="1">
                                        <VBtn style="height: 100%" type="button"
                                              variant="text"
                                              @click="formAnnounce.appointment_times.splice(key, 1)">
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
                        <VRow justify="end" no-gutters type="submit">
                            <VDialog :persistent="formAnnounce.processing" max-width="500">
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
                                        <VRow justify="end" no-gutters>
                                            <VBtn :disabled="formAnnounce.processing" color="primary" type="button"
                                                  @click="formAnnounce.openDialog = false">Cancelar
                                            </VBtn>
                                            <VBtn :loading="formAnnounce.processing" color="error" type="button"
                                                  @click="removeAnnounce">Remover
                                            </VBtn>
                                        </VRow>
                                    </VCardActions>
                                </VCard>
                            </VDialog>
                            <VBtn :loading="formAnnounce.processing" color="primary" type="submit">Salvar</VBtn>
                        </VRow>
                    </VCardActions>
                </VCard>
            </VForm>
        </VDialog>
    </Default>
</template>
