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
                        @click="createFacultyDialog = true"
                    >
                        Crear nueva facultad
                    </v-btn>
                </div>

            </div>

            <!--Inicia tabla-->
            <v-data-table
                loading-text="Cargando, por favor espere..."
                :loading="isLoading"
                :headers="headers"
                :items="faculties"
                :items-per-page="5"
                class="elevation-1"
            >
                <template v-slot:item.actions="{ item }">
                    <v-icon
                        class="mr-2 primario--text"
                        @click="openEditRoleModal(item)"
                    >
                        mdi-pencil
                    </v-icon>
                    <v-icon
                        class="primario--text"
                        @click="confirmDeleteRole(item)"
                    >
                        mdi-delete
                    </v-icon>
                </template>
            </v-data-table>
            <!--Acaba tabla-->

            <!------------Seccion de dialogos ---------->

            <!--Crear facultad -->
            <v-dialog
                v-model="createFacultyDialog"
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
                                        v-model="newFaculty.name"
                                    ></v-text-field>
                                </v-col>
                                <v-col cols="12">
                                    <v-text-field
                                        label="Código de la facultad *"
                                        required
                                        v-model="newFaculty.code"
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
                            @click="createFacultyDialog = false"
                        >
                            Cancelar
                        </v-btn>
                        <v-btn
                            color="primario"
                            text
                            @click="createFaculty"
                        >
                            Guardar cambios
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
            <!--Confirmar borrar facultad-->
            <confirm-dialog
                :show="deleteFacultyDialog"
                @canceled-dialog="deleteFacultyDialog = false"
                @confirmed-dialog="deleteFaculty(deletedRoleId)"
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
                v-model="editFacultyDialog"
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
                                        v-model="editedFaculty.name"
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
                            @click="editFacultyDialog = false"
                        >
                            Cancelar
                        </v-btn>
                        <v-btn
                            color="primario"
                            text
                            @click="editFaculty"
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
                {text: 'Nombre de la facultad', value: 'name'},
                {text: 'Código interno de la facultad', value: 'code'},
                {text: 'Acciones', value: 'actions', sortable: false},
            ],
            faculties: [],

            //Models
            newFaculty: {
                name: '',
                code: '',
            },
            editedFaculty: {
                id: '',
                name: '',
            },
            deletedFacultyId: 0,

            //Snackbars
            snackbar: {
                text: '...',
                status: false,
                timeout: 3000
            },
            //Dialogs
            createFacultyDialog: false,
            deleteFacultyDialog: false,
            editFacultyDialog: false,

            //Overlays
            isLoading: true,
        }
    },
    async created() {
        await this.getAllFaculties();
        this.isLoading = false;
    },
    methods: {
        openEditRoleModal: function (role) {
            this.editedFaculty = {...role};
            this.editFacultyDialog = true;
        },
        editFaculty: async function () {
            //Verify request
            if (checkIfModelHasEmptyProperties(this.editedFaculty)) {
                this.snackbar.text = 'Por favor, llena todos los campos del formulario';
                this.snackbar.status = true;
                return;
            }
            //Recollect information
            let data = {
                id: this.editedFaculty.id,
                name: this.editedFaculty.name,
            }

            try {
                let request = await axios.patch(route('api.faculties.update', {'faculty': this.editedFaculty.id}), data);
                this.editFacultyDialog = false;
                this.snackbar.text = request.data.message;
                this.snackbar.status = true;
                this.getAllFaculties();

                //Clear role information
                clearModelProperties(this.editedFaculty);
                console.log(this.editedFaculty);
            } catch (e) {
                this.snackbar.text = prepareErrorText(e);
                this.snackbar.status = true;
            }
        },

        confirmDeleteRole: function (role) {
            this.deletedRoleId = role.id;
            this.deleteFacultyDialog = true;
        },

        deleteFaculty: async function (facultyId) {
            try {
                let request = await axios.delete(route('api.faculties.destroy', {faculty: facultyId}));
                this.deleteFacultyDialog = false;
                this.snackbar.text = request.data.message;
                this.snackbar.status = true;
                this.getAllFaculties();

            } catch (e) {
                this.snackbar.text = e.response.data.message;
                this.snackbar.status = true;
            }

        },
        getAllFaculties: async function () {
            let request = await axios.get(route('api.faculties.index'));
            this.faculties = request.data;
        },
        createFaculty: async function () {
            if (checkIfModelHasEmptyProperties(this.newFaculty)) {
                this.snackbar.text = 'Por favor, llena todos los campos del formulario';
                this.snackbar.status = true;
                return;
            }

            let data = {
                name: this.newFaculty.name,
                code: this.newFaculty.code
            }
            //Clear role information
            clearModelProperties(this.newFaculty);

            try {
                let request = await axios.post(route('api.faculties.store'), data);
                this.createFacultyDialog = false;
                this.snackbar.text = request.data.message;
                this.snackbar.status = true;
                this.getAllFaculties();
            } catch (e) {
                this.snackbar.text = e.response.data.message;
                this.snackbar.status = true;
            }

        },
        checkIfModelHasEmptyProperties: function (model) {
            for (const modelKey in model) {
                if (model[modelKey] === '') {
                    return true;
                }
            }
            return false;
        }
    },


}
</script>
