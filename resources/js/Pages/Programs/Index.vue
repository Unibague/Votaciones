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
                <h2 class="align-self-start">Gestionar Programas</h2>
                <div>
                    <v-btn
                        color="primario"
                        class="grey--text text--lighten-4"
                        @click="createProgramDialog = true"
                    >
                        Crear nuevo programa
                    </v-btn>
                </div>

            </div>

            <!--Inicia tabla-->
            <v-data-table
                loading-text="Cargando, por favor espere..."
                :loading="isLoading"
                :headers="headers"
                :items="programs"
                :items-per-page="10"
                class="elevation-1"
            >
                <template v-slot:item.actions="{ item }">
                    <v-icon
                        class="mr-2 primario--text"
                        @click="openEditProgramModal(item)"
                    >
                        mdi-pencil
                    </v-icon>
                    <v-icon
                        class="primario--text"
                        @click="confirmDeleteProgram(item)"
                    >
                        mdi-delete
                    </v-icon>
                </template>
            </v-data-table>
            <!--Acaba tabla-->

            <!------------Seccion de dialogos ---------->

            <!--Crear facultad -->
            <v-dialog
                v-model="createProgramDialog"
                persistent
                max-width="600px"
            >

                <v-card>
                    <v-card-title>
                        <span class="text-h5">Crear nueva facultad</span>
                    </v-card-title>
                    <v-card-text>
                        <v-container>
                            <v-row>
                                <v-col cols="12">
                                    <v-text-field
                                        label="Nombre de la facultad *"
                                        required
                                        v-model="newProgram.name"
                                    ></v-text-field>
                                </v-col>
                                <v-col cols="12">
                                    <v-text-field
                                        label="Código de la facultad *"
                                        required
                                        v-model="newProgram.code"
                                    ></v-text-field>
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
                            @click="createProgramDialog = false"
                        >
                            Cancelar
                        </v-btn>
                        <v-btn
                            color="primario"
                            text
                            @click="createProgram"
                        >
                            Guardar cambios
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
            <!--Confirmar borrar facultad-->
            <confirm-dialog
                :show="deleteProgramDialog"
                @canceled-dialog="deleteProgramDialog = false"
                @confirmed-dialog="deleteProgram(deletedProgramId)"
            >
                <template v-slot:title>
                    Estas a punto de eliminar la facultad seleccionada
                </template>

                ¡Cuidado! esta acción es irreversible

                <template v-slot:confirm-button-text>
                    Borrar
                </template>
            </confirm-dialog>

            <!--Editar facultad-->
            <v-dialog
                v-model="editProgramDialog"
                persistent
                max-width="600px"
            >
                <v-card>
                    <v-card-title>
                        <span class="text-h5">Editar facultad</span>
                    </v-card-title>
                    <v-card-text>
                        <v-container>
                            <v-row>
                                <v-col cols="12">
                                    <v-text-field
                                        label="Nombre de la facultad*"
                                        required
                                        v-model="editedProgram.name"
                                    ></v-text-field>
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
                            @click="editProgramDialog = false"
                        >
                            Cancelar
                        </v-btn>
                        <v-btn
                            color="primario"
                            text
                            @click="editProgram"
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
                {text: 'Nombre del programa', value: 'name'},
                {text: 'Código interno del programa', value: 'code'},
                {text: 'Acciones', value: 'actions', sortable: false},
            ],
            programs: [],

            //Models
            newProgram: {
                name: '',
                code: '',
            },
            editedProgram: {
                id: '',
                name: '',
            },
            deletedProgramId: 0,

            //Snackbars
            snackbar: {
                text: '...',
                status: false,
                timeout: 3000
            },
            //Dialogs
            createProgramDialog: false,
            deleteProgramDialog: false,
            editProgramDialog: false,

            //Overlays
            isLoading: true,
        }
    },
    async created() {
        await this.getAllPrograms();
        this.isLoading = false;
    },
    methods: {
        openEditProgramModal: function (role) {
            this.editedProgram = {...role};
            this.editProgramDialog = true;
        },
        editProgram: async function () {
            //Verify request
            if (checkIfModelHasEmptyProperties(this.editedProgram)) {
                this.snackbar.text = 'Por favor, llena todos los campos del formulario';
                this.snackbar.status = true;
                return;
            }
            //Recollect information
            let data = {
                id: this.editedProgram.id,
                name: this.editedProgram.name,
            }

            try {
                let request = await axios.patch(route('api.programs.update', {'program': this.editedProgram.id}), data);
                this.editProgramDialog = false;
                this.snackbar.text = request.data.message;
                this.snackbar.status = true;
                this.getAllPrograms();

                //Clear role information
                clearModelProperties(this.editedProgram);
                console.log(this.editedProgram);
            } catch (e) {
                this.snackbar.text = prepareErrorText(e);
                this.snackbar.status = true;
            }
        },

        confirmDeleteProgram: function (program) {
            this.deletedProgramId = program.id;
            this.deleteProgramDialog = true;
        },

        deleteProgram: async function (programId) {
            try {
                let request = await axios.delete(route('api.programs.destroy', {program: programId}));
                this.deleteProgramDialog = false;
                this.snackbar.text = request.data.message;
                this.snackbar.status = true;
                this.getAllPrograms();

            } catch (e) {
                this.snackbar.text = e.response.data.message;
                this.snackbar.status = true;
            }

        },
        getAllPrograms: async function () {
            let request = await axios.get(route('api.programs.index'));
            this.programs = request.data;
        },
        createProgram: async function () {
            if (checkIfModelHasEmptyProperties(this.newProgram)) {
                this.snackbar.text = 'Por favor, llena todos los campos del formulario';
                this.snackbar.status = true;
                return;
            }

            let data = {
                name: this.newProgram.name,
                code: this.newProgram.code
            }
            //Clear role information
            clearModelProperties(this.newProgram);

            try {
                let request = await axios.post(route('api.programs.store'), data);
                this.createProgramDialog = false;
                this.snackbar.text = request.data.message;
                this.snackbar.status = true;
                this.getAllPrograms();
            } catch (e) {
                this.snackbar.text = e.response.data.message;
                this.snackbar.status = true;
            }

        },
    },


}
</script>
