<script setup>
import {computed, reactive, watch} from "vue";
import {Link, router, useForm, usePage, useRemember} from '@inertiajs/vue3';
import {VApp, VTextField, VContainer} from 'vuetify/components';

const data = reactive({
    loading: false,
})

const page = usePage();

watch(() => usePage().props.loading, (value) => {
    data.loading = value
});
const user = computed(() => usePage().props.auth.user);

const logoutForm = useForm({});

function logout() {
    data.loading = true
    logoutForm.post(route('logout'), {
        onFinish: () => data.loading = false,
    });
}

const searchForm = useForm({
    search: '',
    items: []
});
var timeout = null;

function search(val) {
    searchForm.search = val;
    if (timeout) clearTimeout(timeout)

    if (searchForm.items.includes(searchForm.search)) return;

    timeout = setTimeout(() => {
        searchForm.items = [];

        if (!searchForm.search) return;

        searchForm.get(route('home.search'), {
            onSuccess: (res) => {
                searchForm.items = res.props.app.data['home.search']
            },
            preserveScroll: true,
            preserveState: true,
        });
    }, 350);
}

function confirmSearch(val) {
    searchForm
        .transform(() => ({search: val}))
        .get(route('home'));
}
</script>

<template>
    <VApp>
        <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/@mdi/font@6.x/css/materialdesignicons.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.css"/>

        <VAppBar dense absolute class="px-5">
            <Link :href="route('home')">
                <VToolbarTitle>Consultant</VToolbarTitle>
            </Link>
            <VSpacer/>
            <VAutocomplete
                :append-inner-icon="searchForm.processing ? null : `mdi-magnify`"
                hide-details
                placeholder="Pesquisa"
                variant="solo"
                density="compact"
                @update:search="search"
                :search="searchForm.search"
                :model-value="searchForm.search"
                @update:modelValue="confirmSearch"
                :items="searchForm.items"
                clearable
                no-filter
                :menu="false"
                :loading="searchForm.processing"
                hide-no-data
            >
                <template v-slot:append-inner>
                    <VFadeTransition leave-absolute>
                        <VProgressCircular v-if="searchForm.processing" indeterminate color="info" size="24"/>
                    </VFadeTransition>
                </template>
            </VAutocomplete>
            <VSpacer/>

            <div v-if="!user">
                <Link :href="route('login')">
                    <VBtn>Login</VBtn>
                </Link>
                <Link :href="route('register')">
                    <VBtn>Cadastro</VBtn>
                </Link>
            </div>
            <div v-else>
                <Link :href="route('professional')">
                    <VBtn color="success">Se tornar um profissional</VBtn>
                </Link>
                <VMenu>
                    <template v-slot:activator="{ props }">
                        <VBtn v-bind="props">{{ user.email }}</VBtn>
                    </template>
                    <VList density="compact">
                        <VListItem link @click="logout">
                            Sair
                        </VListItem>
                    </VList>
                </VMenu>
            </div>
        </VAppBar>
        <VMain>
            <VContainer>
                <div v-for="(flash, key) in page?.props?.app?.flash">
                    <VAlert v-if="flash" :type="key">{{ flash }}</VAlert>
                </div>
                <slot></slot>
            </VContainer>

            <VFooter color="grey-lighten-4" class="mt-10">
                <VContainer>

                    <h3>Entre em contato conosco</h3>

                    <VList bg-color="grey-lighten-4">
                        <VListItem>
                            <template v-slot:prepend>
                                <VIcon>mdi-email</VIcon>
                            </template>
                            <VListItemTitle>support@consultant.com.br</VListItemTitle>
                        </VListItem>
                    </VList>
                    <VRow no-gutters justify="center">
                        {{ new Date().getFullYear() }} - Consultant
                    </VRow>
                    <br>
                </VContainer>
            </VFooter>

            <VOverlay :model-value="data.loading" persistent>
                <VProgressCircular indeterminate size="64"></VProgressCircular>
            </VOverlay>
        </VMain>
    </VApp>
</template>
