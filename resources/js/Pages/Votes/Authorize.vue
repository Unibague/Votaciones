<template>
    <AuthenticatedLayout>
        <v-snackbar
            v-model="snackbar.status"
            :timeout="snackbar.timeout"
            color="red accent-2"
            top
            right
        >
            {{ snackbar.text }}

        </v-snackbar>
        <v-container class="">
            <h1 class="text-center mb-6">Autorizar votante</h1>
            <v-row class="align-content-space-between" style="height:70vh">
                <v-spacer></v-spacer>
                <v-col cols="7">
                    <v-card v-if="hasData"
                    >
                        <v-card-title class="text-h5">
                            {{ voter.name }}
                        </v-card-title>
                        <v-card-subtitle>
                            {{ voter.identification_number }}
                        </v-card-subtitle>
                        <v-card-text>
                            Facultad de {{ voter.faculty.name }} | Programa de {{ voter.program.name }}
                        </v-card-text>
                        <v-card-actions>
                            <v-btn
                                dark
                                block
                                @click="authorizeVote(voter.id)"
                            >Autorizar voto
                            </v-btn>
                        </v-card-actions>
                    </v-card>
                </v-col>
                <v-spacer></v-spacer>
                <v-col cols="12">
                    <h3>
                        Por favor, ingrese el numero de documento del votante
                    </h3>
                    <v-input>
                        <v-text-field
                            label="Documento"
                            required
                            v-model="identification_number"
                        ></v-text-field>
                    </v-input>
                    <v-btn
                        block
                        color="primario"
                        class="grey--text text--lighten-4"
                        @click="searchVoter"
                    >Buscar
                    </v-btn>
                </v-col>

            </v-row>
        </v-container>

    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import {prepareErrorText, checkIfModelHasEmptyProperties, clearModelProperties} from "@/HelperFunctions"
import ConfirmDialog from "@/Components/ConfirmDialog";

export default {
    components: {
        ConfirmDialog,
        AuthenticatedLayout,
    },
    data: () => {
        return {
            //Snackbars
            snackbar: {
                text: '...',
                status: false,
                timeout: 2000
            },
            voter: {
                name: 'Esperando datos',
                identification_number: '...',
                faculty: '...',
                program: '...'
            },
            hasData: false,
            identification_number: '',
        }
    },
    methods: {
        authorizeVote: function (voterId) {
            window.open(route('votes.vote') + '?voterId=' + voterId);
            clearModelProperties(this.voter);
            this.hasData = false;
        },
        searchVoter: async function () {
            if (this.identification_number === '') {
                this.snackbar.text = 'Por favor, ingresa un documento válido';
                this.snackbar.status = true;
                return;
            }

            let data = {
                identification_number: this.identification_number,
            }
            try {
                let request = await axios.get(route('api.voters.searchByIdentificationNumber'), {params: data});
                this.voter = request.data;
                this.hasData = true;

            } catch (e) {
                if (e.response.status === 404) {
                    this.snackbar.text = 'Usuario no encontrado en el censo electoral';
                    this.snackbar.status = true;
                } else {
                    this.snackbar.text = e.response.data.message;
                    this.snackbar.status = true;
                }

            }
        }
    },


}
</script>
