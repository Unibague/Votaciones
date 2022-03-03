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

        <v-container>
            <div class="d-flex flex-column align-end mb-8">
                <h2 class="align-self-start">Gestionar candidatos</h2>
                <div>
                    <v-btn
                        color="primario"
                        class="grey--text text--lighten-4"
                        @click="createCandidateDialog = true"
                    >
                        Crear nuevo candidato
                    </v-btn>
                </div>

            </div>

            <!--Inicia tabla-->
            <v-data-table
                loading-text="Cargando, por favor espere..."
                :loading="isLoading"
                :headers="headers"
                :items="candidates"
                :items-per-page="5"
                class="elevation-1"
            >
                <template v-slot:item.actions="{ item }">
                    <v-icon
                        class="mr-2 primario--text"
                        @click="openEditCandidateModal(item)"
                    >
                        mdi-pencil
                    </v-icon>
                    <v-icon
                        class="primario--text"
                        @click="confirmDeleteCandidate(item)"
                    >
                        mdi-delete
                    </v-icon>
                </template>
            </v-data-table>
            <!--Acaba tabla-->

            <!------------Seccion de dialogos ---------->

            <!--Crear facultad -->
            <v-dialog
                v-model="createCandidateDialog"
                persistent
                max-width="600px"
            >

                <v-card>
                    <v-card-title>
                        <span class="text-h5">Crear nuevo candidato</span>
                    </v-card-title>
                    <v-card-text>
                        <v-container>
                            <v-row>
                                <v-col cols="12">
                                    <h3>Información del candidato principal</h3>
                                </v-col>
                                <v-col cols="12">
                                    <v-text-field
                                        label="Nombre del principal *"
                                        required
                                        v-model="newCandidate.principal_name"
                                    ></v-text-field>
                                </v-col>
                                <v-col cols="12">
                                    <v-text-field
                                        label="Facultad del principal *"
                                        required
                                        v-model="newCandidate.principal_faculty"
                                    ></v-text-field>
                                </v-col>
                                <v-col cols="12">
                                    <v-text-field
                                        label="Programa del principal"
                                        required
                                        v-model="newCandidate.principal_program"
                                    ></v-text-field>
                                </v-col>
                                <v-col cols="12">
                                    <h3>Información del candidato suplente</h3>
                                </v-col>

                                <v-col cols="12">
                                    <v-text-field
                                        label="Nombre del suplente *"
                                        required
                                        v-model="newCandidate.substitute_name"
                                    ></v-text-field>
                                </v-col>
                                <v-col cols="12">
                                    <v-text-field
                                        label="Facultad del suplente *"
                                        required
                                        v-model="newCandidate.substitute_faculty"
                                    ></v-text-field>
                                </v-col>
                                <v-col cols="12">
                                    <v-text-field
                                        label="Programa del suplente *"
                                        required
                                        v-model="newCandidate.substitute_program"
                                    ></v-text-field>
                                </v-col>
                                <v-col cols="12">
                                    <v-select
                                        color="primario"
                                        v-model="newCandidate.voting_option_id"
                                        :items="votingOptions"
                                        label="Selecciona una opción de votación"
                                        :item-value="(votingOption)=>votingOption.id"
                                        :item-text="(votingOption)=>votingOption.name"
                                    ></v-select>
                                </v-col>

                            </v-row>
                        </v-container>
                        <small>Los campos con * son obligatorios</small>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn
                            color="primario"
                            text
                            @click="createCandidateDialog = false"
                        >
                            Cancelar
                        </v-btn>
                        <v-btn
                            color="primario"
                            text
                            @click="createCandidate"
                        >
                            Guardar cambios
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
            <!--Confirmar borrar facultad-->
            <confirm-dialog
                :show="deleteCandidateDialog"
                @canceled-dialog="deleteCandidateDialog = false"
                @confirmed-dialog="deleteCandidate(deletedCandidateId)"
            >
                <template v-slot:title>
                    Estas a punto de eliminar el candidato seleccionado
                </template>

                ¡Cuidado! esta acción es irreversible

                <template v-slot:confirm-button-text>
                    Borrar
                </template>
            </confirm-dialog>

            <!--Editar facultad-->
            <v-dialog
                v-model="editCandidateDialog"
                persistent
                max-width="600px"
            >
                <v-card>
                    <v-card-title>
                        <span class="text-h5">Editar candidato</span>
                    </v-card-title>
                    <v-card-text>
                        <v-container>
                            <v-row>
                                <v-col cols="12">
                                    <h3>Información del candidato principal</h3>
                                </v-col>
                                <v-col cols="12">
                                    <v-text-field
                                        label="Nombre del principal *"
                                        required
                                        v-model="editedCandidate.principal_name"
                                    ></v-text-field>
                                </v-col>
                                <v-col cols="12">
                                    <v-text-field
                                        label="Facultad del principal *"
                                        required
                                        v-model="editedCandidate.principal_faculty"
                                    ></v-text-field>
                                </v-col>
                                <v-col cols="12">
                                    <v-text-field
                                        label="Programa del principal"
                                        required
                                        v-model="editedCandidate.principal_program"
                                    ></v-text-field>
                                </v-col>
                                <v-col cols="12">
                                    <h3>Información del candidato suplente</h3>
                                </v-col>

                                <v-col cols="12">
                                    <v-text-field
                                        label="Nombre del suplente *"
                                        required
                                        v-model="editedCandidate.substitute_name"
                                    ></v-text-field>
                                </v-col>
                                <v-col cols="12">
                                    <v-text-field
                                        label="Facultad del suplente *"
                                        required
                                        v-model="editedCandidate.substitute_faculty"
                                    ></v-text-field>
                                </v-col>
                                <v-col cols="12">
                                    <v-text-field
                                        label="Programa del suplente *"
                                        required
                                        v-model="editedCandidate.substitute_program"
                                    ></v-text-field>
                                </v-col>
                                <v-col cols="12">
                                    <v-select
                                        color="primario"
                                        v-model="editedCandidate.voting_option_id"
                                        :items="votingOptions"
                                        label="Selecciona una opción de votación"
                                        :item-value="(votingOption)=>votingOption.id"
                                        :item-text="(votingOption)=>votingOption.name"
                                    ></v-select>
                                </v-col>

                            </v-row>
                        </v-container>
                        <small>Los campos con * son obligatorios</small>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn
                            color="primario"
                            text
                            @click="editCandidateDialog = false"
                        >
                            Cancelar
                        </v-btn>
                        <v-btn
                            color="primario"
                            text
                            @click="editCandidate"
                        >
                            Guardar cambios
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
            <!------------Seccion de dialogos ---------->
        </v-container>

    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import {InertiaLink} from "@inertiajs/inertia-vue";
import ConfirmDialog from "@/Components/ConfirmDialog";
import {prepareErrorText, checkIfModelHasEmptyProperties, clearModelProperties} from "@/HelperFunctions";
import Candidate from "./Candidate"

export default {
    components: {
        ConfirmDialog,
        AuthenticatedLayout,
        InertiaLink,
    },
    data: () => {
        return {
            //Table info
            headers: [
                {text: 'Nombre del principal', value: 'principal_name'},
                {text: 'Facultad del principal', value: 'principal_faculty'},
                {text: 'Programa del principal', value: 'principal_program'},
                {text: 'Nombre del suplente', value: 'substitute_name'},
                {text: 'Facultad del suplente', value: 'substitute_faculty'},
                {text: 'Programa del suplente', value: 'substitute_program'},
                {text: 'Opción de votación', value: 'voting_option_id'},
                {text: 'Acciones', value: 'actions', sortable: false},
            ],
            candidates: [],
            votingOptions: [],

            //Models
            newCandidate: new Candidate(),
            editedCandidate: {
                id: '',
                name: '',
            },
            deletedCandidateId: 0,

            //Snackbars
            snackbar: {
                text: '...',
                status: false,
                timeout: 3000
            },
            //Dialogs
            createCandidateDialog: false,
            deleteCandidateDialog: false,
            editCandidateDialog: false,

            //Overlays
            isLoading: true,
        }
    },
    async created() {
        await this.getAllCandidates();
        this.getAllVotingOptions();
        this.isLoading = false;
    },
    methods: {
        openEditCandidateModal: function (candidate) {
            this.editedCandidate = {...candidate};
            this.editCandidateDialog = true;
        },
        editCandidate: async function () {
            //Verify request
            if (checkIfModelHasEmptyProperties(this.editedCandidate)) {
                this.snackbar.text = 'Por favor, llena todos los campos del formulario';
                this.snackbar.status = true;
                return;
            }
            //Recollect information
            let data = this.editedCandidate;

            try {
                let request = await axios.patch(route('api.candidates.update', {'candidate': this.editedCandidate.id}), data);
                this.editCandidateDialog = false;
                this.snackbar.text = request.data.message;
                this.snackbar.status = true;
                this.getAllCandidates();

                //Clear candidate information
                clearModelProperties(this.editedCandidate);
                console.log(this.editedCandidate);
            } catch (e) {
                this.snackbar.text = prepareErrorText(e);
                this.snackbar.status = true;
            }
        },

        confirmDeleteCandidate: function (candidate) {
            this.deletedCandidateId = candidate.id;
            this.deleteCandidateDialog = true;
        },

        deleteCandidate: async function (candidateId) {
            try {
                let request = await axios.delete(route('api.candidates.destroy', {candidate: candidateId}));
                this.deleteCandidateDialog = false;
                this.snackbar.text = request.data.message;
                this.snackbar.status = true;
                this.getAllCandidates();

            } catch (e) {
                this.snackbar.text = e.response.data.message;
                this.snackbar.status = true;
            }

        },
        getAllCandidates: async function () {
            let request = await axios.get(route('api.candidates.index'));
            this.candidates = request.data;
        },

        getAllVotingOptions: async function () {
            let request = await axios.get(route('api.votingOptions.index'));
            this.votingOptions = request.data;
        },


        createCandidate: async function () {
            if (checkIfModelHasEmptyProperties(this.newCandidate)) {
                this.snackbar.text = 'Por favor, llena todos los campos del formulario';
                this.snackbar.status = true;
                return;
            }

            let data = this.newCandidate.toObject()

            try {
                let request = await axios.post(route('api.candidates.store'), data);
                this.createCandidateDialog = false;
                this.snackbar.text = request.data.message;
                this.snackbar.status = true;
                this.getAllCandidates();

                //Clear candidate information
                this.newCandidate = new Candidate();
            } catch (e) {
                this.snackbar.text = e.response.data.message;
                this.snackbar.status = true;
            }

        },
    },


}
</script>
