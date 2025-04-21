<template>
    <AuthenticatedLayout>
        <v-container>

            <!-- Título fuera del card -->
            <v-row>
                <v-col cols="12">
                    <h2 class="text-h5 font-weight-bold mb-4">Lista de votantes</h2>
                </v-col>
            </v-row>

            <v-card>
                <v-card-title>
                    <v-text-field
                        v-model="search"
                        append-icon="mdi-magnify"
                        label="Buscar votante..."
                        single-line
                        hide-details
                        dense
                    />
                </v-card-title>

                <v-data-table
                    :headers="headers"
                    :items="voters"
                    :search="search"
                    dense
                    class="elevation-1"
                >
                    <template v-slot:no-data>
                        <v-alert :value="true" color="warning" icon="mdi-alert">
                            No se encontraron votantes.
                        </v-alert>
                    </template>
                </v-data-table>
            </v-card>
        </v-container>
    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

export default {
    components: {
        AuthenticatedLayout
    },

    props: {
        voters: Array
    },

    data() {
        return {
            search: '',
            headers: [
                { text: 'Identificación', value: 'identification_number' },
                { text: 'Nombre', value: 'name' },
                { text: 'Correo', value: 'email' },
                { text: 'Programa', value: 'program_name' },
                { text: 'Facultad', value: 'faculty_name' }
            ]
        };
    }
};
</script>
