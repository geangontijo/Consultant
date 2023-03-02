<script setup>
import Default from "@/Layouts/Default.vue";
import {useForm, usePage} from "@inertiajs/vue3";

defineProps({
    announce: Object
})

const form = useForm({
    consultation_appointment_times: []
});

const data = usePage()

function createCheckout(schedule) {
    data.props.loading = true
    form.consultation_appointment_times = [schedule.id];
    form.post(route('checkout.store'), {
        onFinish: () => data.loading = false,
    });
}

function maskPhone(phone) {
    return phone.replace(/(\d{2})(\d{1})(\d{4})(\d{4})/, '($1) $2 $3-$4')
}
</script>

<template>
    <Default>
        <VRow class="p-3">
            <VCol cols="8">
                <VCard>
                    <VImg max-height="500" :src="announce?.professional?.photo_url"></VImg>
                    <VCardText>
                        <VRow no-gutters align="center">
                            <VAvatar></VAvatar>
                            {{ announce.category_name }}
                            <VSpacer/>
                        </VRow>
                        <VRow no-gutters>
                            <VCol>
                                <h4>Descrição do serviço</h4>

                                <p>{{ announce?.description }}</p>
                            </VCol>
                        </VRow>
                    </VCardText>
                </VCard>

                <VDivider/>

                <div class="mt-5">

                    <VAlert type="info"
                            text="Esse serviço é online, portanto será feito com alguma plataforma online. (Google Meet, Discord, Whatsapp, etc...)"></VAlert>
                </div>

                <div class="mt-5">
                    <h3>Horários disponiveis</h3>

                    <VList>
                        <VListItem v-for="schedule in announce.appointment_times">
                            {{ new Date(schedule.start).getDay() === new Date().getDay() ? 'Hoje' : 'Amanhã' }} das {{ new Date(schedule.start).getUTCHours() }}hrs até as {{ new Date(schedule.end).getUTCHours() }}hrs
                            <template v-slot:append>
                                <VBtn size="small" color="success" plain @click="createCheckout(schedule)">Agendar Horário</VBtn>
                            </template>
                        </VListItem>
                    </VList>
<!--                    <VRow no-gutters>-->
<!--                        <VCol cols="5" v-for="schedule in announce?.appointment_times">-->
<!--                            <VCard link @click="createCheckout(schedule)">-->
<!--                                <VCardTitle>-->
<!--                                    {{ schedule.start }} / {{ schedule.end }}-->
<!--                                </VCardTitle>-->
<!--                                <VCardText>-->
<!--                                    <VBtn color="success" plain>Agendar Horário</VBtn>-->
<!--                                </VCardText>-->
<!--                            </VCard>-->
<!--                        </VCol>-->
<!--                    </VRow>-->
                </div>

                <div class="mt-5">
                    <h3>Entre em contato com esse profissional</h3>

                    <VList>
                        <VListItem>
                            <template v-slot:prepend>
                                <VIcon>mdi-whatsapp</VIcon>
                            </template>
                            <VListItemTitle>
                                <a target="_blank" :href="`https://api.whatsapp.com/send?phone=55${announce.professional.phone_number}`">
                                    {{ maskPhone(announce.professional.phone_number) }}
                                </a>
                            </VListItemTitle>
                        </VListItem>
                        <VListItem>
                            <template v-slot:prepend>
                                <VIcon>mdi-email</VIcon>
                            </template>
                            <VListItemTitle>{{ announce.professional.email }}</VListItemTitle>
                        </VListItem>
                    </VList>
                </div>
            </VCol>
            <VCol cols="4">
                <VCard link :href="route(`announce`, [announce?.id || 1])">
                    <VCardText>
                        <VAvatar></VAvatar>
                        {{ announce.category_name }}
                    </VCardText>
                </VCard>
            </VCol>
        </VRow>
    </Default>
</template>

<style scoped>

</style>
