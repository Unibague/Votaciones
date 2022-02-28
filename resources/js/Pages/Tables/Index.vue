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
                <h2 class="align-self-start">Gestionar mesas de votación</h2>
                <div>
                    <v-btn
                        color="primario"
                        class="grey--text text--lighten-4"
                        @click="createTableDialog = true"
                    >
                        Crear nueva mesa
                    </v-btn>
                </div>

            </div>

            <!--Inicia tabla-->
            <v-data-table
                loading-text="Cargando, por favor espere..."
                :loading="isLoading"
                :headers="headers"
                :items="tables"
                :items-per-page="10"
                class="elevation-1"
            >
                <template v-slot:item.actions="{ item }">
                    <v-icon
                        class="mr-2 primario--text"
                        @click="openEditTableModal(item)"
                    >
                        mdi-pencil
                    </v-icon>
                    <v-icon
                        class="primario--text"
                        @click="confirmDeleteTable(item)"
                    >
                        mdi-delete
                    </v-icon>
                </template>
            </v-data-table>
            <!--Acaba tabla-->

            <!------------Seccion de dialogos ---------->

            <!--Crear facultad -->
            <v-dialog
                v-model="createTableDialog"
                persistent
                max-width="600px"
            >

                <v-card>
                    <v-card-title>
                        <span class="text-h5">Crear nueva mesa</span>
                    </v-card-title>
                    <v-card-text>
                        <v-container>
                            <v-row>
                                <v-col cols="12">
                                    <v-text-field
                                        label="Nombre de la mesa *"
                                        required
                                        v-model="newTable.name"
                                    ></v-text-field>
                                </v-col>
                                <v-col cols="12">
                                    <v-select
                                        color="primario"
                                        v-model="newTable.faculty_code"
                                        :items="faculties"
                                        label="Selecciona una facultad"
                                        :item-value="(faculty)=>faculty.code"
                                        :item-text="(faculty)=>faculty.name"
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
                            @click="createTableDialog = false"
                        >
                            Cancelar
                        </v-btn>
                        <v-btn
                            color="primario"
                            text
                            @click="createTable"
                        >
                            Guardar cambios
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
            <!--Confirmar borrar facultad-->
            <confirm-dialog
                :show="deleteTableDialog"
                @canceled-dialog="deleteTableDialog = false"
                @confirmed-dialog="deleteTable(deletedTableId)"
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
                v-model="editTableDialog"
                persistent
                max-width="600px"
            >
                <v-card>
                    <v-card-title>
                        <span class="text-h5">Editar mesa</span>
                    </v-card-title>
                    <v-card-text>
                        <v-container>
                            <v-row>
                                <v-col cols="12">
                                    <v-text-field
                                        label="Nombre de la mesa*"
                                        required
                                        v-model="editedTable.name"
                                    ></v-text-field>
                                </v-col>

                                <v-col cols="12">
                                    <v-select
                                        color="primario"
                                        v-model="editedTable.faculty_code"
                                        :items="faculties"
                                        label="Selecciona una facultad"
                                        :item-value="(faculty)=>faculty.code"
                                        :item-text="(faculty)=>faculty.name"
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
                            @click="editTableDialog = false"
                        >
                            Cancelar
                        </v-btn>
                        <v-btn
                            color="primario"
                            text
                            @click="editTable"
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
                {text: 'Id', value: 'id'},
                {text: 'Nombre', value: 'name'},
                {text: 'Código de facultad', value: 'faculty_code'},
                {text: 'Acciones', value: 'actions', sortable: false},
            ],
            tables: [],

            //Models
            newTable: {
                name: '',
                faculty_code: '',
            },
            editedTable: {
                id: '',
                name: '',
                faculty_code: '',
            },
            deletedTableId: 0,

            //Facultades
            faculties: [],
            selectedFacultyId: 0,

            //Snackbars
            snackbar: {
                text: '...',
                status: false,
                timeout: 3000
            },
            //Dialogs
            createTableDialog: false,
            deleteTableDialog: false,
            editTableDialog: false,

            //Overlays
            isLoading: true,
        }
    },
    async created() {
        await this.getAllTables();
        this.getAllFaculties();
        this.isLoading = false;
    },
    methods: {
        openEditTableModal: function (role) {
            this.editedTable = {...role};
            this.editTableDialog = true;
        },
        editTable: async function () {
            //Verify request
            if (checkIfModelHasEmptyProperties(this.editedTable)) {
                this.snackbar.text = 'Por favor, llena todos los campos del formulario';
                this.snackbar.status = true;
                return;
            }
            //Recollect information
            let data = {
                id: this.editedTable.id,
                name: this.editedTable.name,
                faculty_code: this.editedTable.faculty_code,
            }

            try {
                let request = await axios.patch(route('api.tables.update', {'table': this.editedTable.id}), data);
                this.editTableDialog = false;
                this.snackbar.text = request.data.message;
                this.snackbar.status = true;
                this.getAllTables();

                //Clear role information
                clearModelProperties(this.editedTable);
            } catch (e) {
                this.snackbar.text = prepareErrorText(e);
                this.snackbar.status = true;
            }
        },

        confirmDeleteTable: function (table) {
            this.deletedTableId = table.id;
            this.deleteTableDialog = true;
        },

        deleteTable: async function (tableId) {
            try {
                let request = await axios.delete(route('api.tables.destroy', {table: tableId}));
                this.deleteTableDialog = false;
                this.snackbar.text = request.data.message;
                this.snackbar.status = true;
                this.getAllTables();

            } catch (e) {
                this.snackbar.text = e.response.data.message;
                this.snackbar.status = true;
            }

        },
        getAllTables: async function () {
            let request = await axios.get(route('api.tables.index'));
            this.tables = request.data;
        },

        getAllFaculties: async function () {
            let request = await axios.get(route('api.faculties.index'));
            this.faculties = request.data;
            console.log(this.faculties)
        },
        createTable: async function () {
            if (checkIfModelHasEmptyProperties(this.newTable)) {
                this.snackbar.text = 'Por favor, llena todos los campos del formulario';
                this.snackbar.status = true;
                return;
            }

            let data = {
                name: this.newTable.name,
                faculty_code: this.newTable.faculty_code
            }
            //Clear role information
            clearModelProperties(this.newTable);

            try {
                let request = await axios.post(route('api.tables.store'), data);
                this.createTableDialog = false;
                this.snackbar.text = request.data.message;
                this.snackbar.status = true;
                this.getAllTables();
            } catch (e) {
                this.snackbar.text = e.response.data.message;
                this.snackbar.status = true;
            }

        },
    },


}
</script>
