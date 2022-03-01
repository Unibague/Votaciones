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
                <h2 class="align-self-start">Gestionar opciones de votación</h2>
                <div>
                    <v-btn
                        color="primario"
                        class="grey--text text--lighten-4"
                        @click="createVotingOptionDialog = true"
                    >
                        Crear nueva opción de votación
                    </v-btn>
                </div>

            </div>

            <!--Inicia tabla-->
            <v-data-table
                loading-text="Cargando, por favor espere..."
                :loading="isLoading"
                :headers="headers"
                :items="votingOptions"
                :items-per-page="5"
                class="elevation-1"
            >
                <template v-slot:item.actions="{ item }">
                    <v-icon
                        class="mr-2 primario--text"
                        @click="openEditVotingOptionModal(item)"
                    >
                        mdi-pencil
                    </v-icon>
                    <v-icon
                        class="primario--text"
                        @click="confirmDeleteVotingOption(item)"
                    >
                        mdi-delete
                    </v-icon>
                </template>
            </v-data-table>
            <!--Acaba tabla-->

            <!------------Seccion de dialogos ---------->

            <!--Crear opción de votación -->
            <v-dialog
                v-model="createVotingOptionDialog"
                persistent
                max-width="600px"
            >
                <v-card>
                    <v-card-title>
                        <span class="text-h5">Crear nueva opción de votación</span>
                    </v-card-title>
                    <v-card-text>
                        <v-container>
                            <v-row>
                                <v-col cols="12">
                                    <v-text-field
                                        label="Nombre de la opción de votación *"
                                        required
                                        v-model="newVotingOption.name"
                                    ></v-text-field>
                                </v-col>
                                <v-col cols="12">
                                    <v-select
                                        @change="getVotingOptionValues"
                                        :items="VotingOptionsKeys"
                                        label="Quiénes pueden participar en esta opción de votación *"
                                        required
                                        v-model="newVotingOption.key"
                                    ></v-select>
                                </v-col>
                                <v-col cols="12">
                                    <v-select
                                        :items="programsOrFaculties"
                                        label="seleccione el valor deseado *"
                                        required
                                        v-model="newVotingOption.value"
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
                            @click="createVotingOptionDialog = false"
                        >
                            Cancelar
                        </v-btn>
                        <v-btn
                            color="primario"
                            text
                            @click="createVotingOption"
                        >
                            Guardar cambios
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
            <!--Confirmar borrar opción de votación-->
            <confirm-dialog
                :show="deleteVotingOptionDialog"
                @canceled-dialog="deleteVotingOptionDialog = false"
                @confirmed-dialog="deleteVotingOption(deletedVotingOptionId)"
            >
                <template v-slot:title>
                    Estas a punto de eliminar la opción de votación seleccionada
                </template>

                ¡Cuidado! esta acción es irreversible

                <template v-slot:confirm-button-text>
                    Borrar
                </template>
            </confirm-dialog>

            <!--Editar opción de votación-->
            <v-dialog
                v-model="editVotingOptionDialog"
                persistent
                max-width="600px"
            >
                <v-card>
                    <v-card-title>
                        <span class="text-h5">Editar opción de votación</span>
                    </v-card-title>
                    <v-card-text>
                        <v-container>
                            <v-row>
                                <v-col cols="12">
                                    <v-text-field
                                        label="Nombre de la opción de votación*"
                                        required
                                        v-model="editedVotingOption.name"
                                    ></v-text-field>
                                </v-col>
                                <v-col cols="12">
                                    <v-select
                                        @change="getVotingOptionValues"
                                        :items="VotingOptionsKeys"
                                        label="Quiénes pueden participar en esta opción de votación *"
                                        required
                                        v-model="editedVotingOption.key"
                                    ></v-select>
                                </v-col>
                                <v-col cols="12">
                                    <v-select
                                        :items="programsOrFaculties"
                                        label="seleccione el valor deseado *"
                                        required
                                        v-model="editedVotingOption.value"
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
                            @click="editVotingOptionDialog = false"
                        >
                            Cancelar
                        </v-btn>
                        <v-btn
                            color="primario"
                            text
                            @click="editVotingOption"
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
import {prepareErrorText, checkIfModelHasEmptyProperties, clearModelProperties} from "@/HelperFunctions"
import ConfirmDialog from "@/Components/ConfirmDialog";


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
                {text: 'Nombre de la opción de votación', value: 'name'},
                {text: 'Nivel de votación', value: 'key'},
                {text: 'Código de la opción de votación', value: 'value'},
                {text: 'Acciones', value: 'actions', sortable: false},
            ],
            votingOptions: [],
            newVotingOption: {
                name: '',
                key: '',
                value: '',
            },
            //all faculties and programs to show on the select

            programsOrFaculties: [],

            //Models
            editedVotingOption: {
                id: '',
                name: '',
                key: '',
                value: '',
            },
            deletedVotingOptionId: 0,

            VotingOptionsKeys: [
                {text: 'Todos', value: 'all'},
                {text: 'Facultad', value: 'faculty'},
                {text: 'Programa', value: 'program'},
            ],

            //Snackbars
            snackbar: {
                text: '...',
                status: false,
                timeout: 3000
            },
            //Dialogs
            createVotingOptionDialog: false,
            deleteVotingOptionDialog: false,
            editVotingOptionDialog: false,

            //Overlays
            isLoading: true,
        }
    },
    async created() {
        await this.getAllVotingOptions();
        this.isLoading = false;
    },
    methods: {

        getVotingOptionValues: async function () {
            let getData = this.newVotingOption.key===''?this.editedVotingOption.key:this.newVotingOption.key;
            console.log(getData);
            if (getData == 'faculty') {
                let request = await axios.get(route('api.faculties.index'));
                this.programsOrFaculties = request.data;
            } else if (getData == 'program') {
                let request = await axios.get(route('api.programs.index'));
                this.programsOrFaculties = request.data;
            } else {
                this.programsOrFaculties = [0];
            }
        },

        openEditVotingOptionModal: function (votingOption) {
            this.editedVotingOption = {...votingOption};
            this.editVotingOptionDialog = true;
        },
        editVotingOption: async function () {
            //Verify request
            if (checkIfModelHasEmptyProperties(this.editedVotingOption)) {
                this.snackbar.text = 'Por favor, llena todos los campos del formulario';
                this.snackbar.status = true;
                return;
            }

            //Recollect information
            let data = {
                id: this.editedVotingOption.id,
                name: this.editedVotingOption.name,
                key: this.editedVotingOption.key,
                value:this.editedVotingOption.value,
            }

            try {
                let request = await axios.patch(route('api.votingOptions.update', {'votingOption': this.editedVotingOption.id}), data);
                this.editVotingOptionDialog = false;
                this.snackbar.text = request.data.message;
                this.snackbar.status = true;
                this.getAllVotingOptions();

                //Clear votingOption information
                clearModelProperties(this.editedVotingOption);
                console.log(this.editedVotingOption);
            } catch (e) {
                this.snackbar.text = prepareErrorText(e);
                this.snackbar.status = true;
            }
        },

        confirmDeleteVotingOption: function (votingOption) {
            this.deletedVotingOptionId = votingOption.id;
            this.deleteVotingOptionDialog = true;
        },

        deleteVotingOption: async function (votingOptionId) {
            try {
                let request = await axios.delete(route('api.votingOptions.destroy', {votingOption: votingOptionId}));
                this.deleteVotingOptionDialog = false;
                this.snackbar.text = request.data.message;
                this.snackbar.status = true;
                this.getAllVotingOptions();

            } catch (e) {
                this.snackbar.text = e.response.data.message;
                this.snackbar.status = true;
            }

        },
        getAllVotingOptions: async function () {
            let request = await axios.get(route('api.votingOptions.index'));
            this.votingOptions = request.data;
        },
        createVotingOption: async function () {
            if (checkIfModelHasEmptyProperties(this.newVotingOption)) {
                this.snackbar.text = 'Por favor, llena todos los campos del formulario';
                this.snackbar.status = true;
                return;
            }

            let data = {
                name: this.newVotingOption.name,
                key: this.newVotingOption.key,
                value: this.newVotingOption.value,
            }
            //Clear votingOption information
            clearModelProperties(this.newVotingOption);

            try {
                let request = await axios.post(route('api.votingOptions.store'), data);
                this.createVotingOptionDialog = false;
                this.snackbar.text = request.data.message;
                this.snackbar.status = true;
                this.getAllVotingOptions();
            } catch (e) {
                this.snackbar.text = e.response.data.message;
                this.snackbar.status = true;
            }

        },
    },
}
</script>
