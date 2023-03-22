<script setup>
import {computed, reactive, watch} from "vue";
import {Link, useForm, usePage} from '@inertiajs/vue3';
import {VApp, VContainer} from 'vuetify/components';

const data = reactive({
    loading: false,
    flash: []
})

const page = usePage();

watch(() => usePage().props.loading, (value) => {
    data.loading = value
});

watch(() => usePage().props.app.flash, (value) => {
    data.flash = value
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
        <link href="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.css" rel="stylesheet"/>

        <VAppBar absolute class="px-5" dense>
            <Link :href="route('home')">
                <VToolbarTitle>Consultant</VToolbarTitle>
            </Link>
            <VSpacer/>
            <VAutocomplete
                :append-inner-icon="searchForm.processing ? null : `mdi-magnify`"
                :items="searchForm.items"
                :loading="searchForm.processing"
                :menu="false"
                :model-value="searchForm.search"
                :search="searchForm.search"
                clearable
                density="compact"
                hide-details
                hide-no-data
                no-filter
                placeholder="Pesquisa"
                variant="solo"
                @update:search="search"
                @update:modelValue="confirmSearch"
            >
                <template v-slot:append-inner>
                    <VFadeTransition leave-absolute>
                        <VProgressCircular v-if="searchForm.processing" color="info" indeterminate size="24"/>
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
                <div v-for="(flash, key) in data.flash">
                    <VAlert v-if="flash" :type="key">{{ flash }}</VAlert>
                </div>
                <br>
                <slot></slot>
            </VContainer>

            <VOverlay :model-value="data.loading" persistent>
                <VProgressCircular indeterminate size="64"></VProgressCircular>
            </VOverlay>
        </VMain>
        <VFooter class="mt-10" color="grey-lighten-4">
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
                <VRow justify="center" no-gutters>
                    {{ new Date().getFullYear() }} - Consultant
                </VRow>
                <br>
            </VContainer>
        </VFooter>
    </VApp>
</template>
