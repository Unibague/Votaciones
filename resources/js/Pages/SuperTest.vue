<template>
    <AuthenticatedLayout>
        <v-btn @click="showDialog = !showDialog">
            hola
        </v-btn>
        <ConfirmDialog
            v-on:canceled="showDialog = false"
            :show="showDialog">
            <template v-slot:title>
                Titulo
            </template>
            <template v-slot:body>
                Cuerpo
            </template>
            <template v-slot:confirm-button-text>
                Acepppto
            </template>
        </ConfirmDialog>
    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import {InertiaLink} from "@inertiajs/inertia-vue";
import ConfirmDialog from "@/Components/ConfirmDialog";

export default {
    components: {
        AuthenticatedLayout,
        InertiaLink,
        ConfirmDialog
    },
    data: () => ({
        showDialog: false
    }),
    methods: {}
}
</script>
