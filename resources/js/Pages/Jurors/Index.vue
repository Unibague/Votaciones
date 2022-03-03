<template>
    <AuthenticatedLayout>
        <!--Snackbars-->
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
            <div class="dd-flex flex-column align-end mb-8">
                <h2 class="align-self-start">Gestionar jurados</h2>
            </div>

            <!--Inicia tabla-->
            <v-data-table
                loading-text="Cargando, por favor espere..."
                :loading="isLoading"
                :headers="headers"
                :items="jurors"
                :items-per-page="5"
                class="elevation-1"
            >
                <template v-slot:item.actions="{ item }">
                    <v-icon
                        class="mr-2 primario--text"
                        @click="openEditTableModal(item)"
                    >
                        mdi-pencil
                    </v-icon>
                </template>
            </v-data-table>
            <!--Acaba tabla-->

            <!------------Seccion de dialogos ---------->
            <!--Editar role-->
            <v-dialog
                v-model="editJuryDialog"
                persistent
                max-width="600px"
            >
                <v-card>
                    <v-card-title>
                        <span class="text-h5 text-center">Cambiar la mesa de {{ editedJury.name }}</span>
                    </v-card-title>
                    <v-card-text>
                        <v-container>
                            <v-row>
                                <v-col cols="12">
                                    <v-select
                                        color="primario"
                                        v-model="selectedTableId"
                                        :items="tables"
                                        label="Selecciona una mesa"
                                        :item-value="(table)=>table.id"
                                        :item-text="(table)=>table.name"
                                    ></v-select>
                                </v-col>
                            </v-row>
                        </v-container>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn
                            color="primario"
                            text
                            @click="editJuryDialog = false"
                        >
                            Cancelar
                        </v-btn>
                        <v-btn
                            color="primario"
                            text
                            @click="editJuryTableRequest"
                        >
                            Guardar cambios
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>
            <!------------Seccion de dialogos ---------->

            <!------------Seccion de Overlays ---------->

            <!--            <v-overlay :value="isLoading"
                                   absolute
                                   opacity="1">
                            <v-progress-circular
                                indeterminate
                                size="64"
                            ></v-progress-circular>
                        </v-overlay>-->
            <!------------Seccion de Overlays ---------->

        </v-container>

    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import {InertiaLink} from "@inertiajs/inertia-vue";
import {prepareErrorText, clearModelProperties} from "@/HelperFunctions"
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
                {text: 'ID', value: 'id'},
                {text: 'Nombre', value: 'name'},
                {text: 'Correo electrónico', value: 'email'},
                {text: 'Mesa', value: 'table.name'},
                {text: 'Acciones', value: 'actions', sortable: false},
            ],
            jurors: [],
            tables: [],
            //Snackbars
            snackbar: {
                text: '...',
                status: false,
                timeout: 1500
            },
            //Dialogs
            editJuryDialog: false,
            //Jury models
            editedJury: {
                id: 0,
                name: '',
                table_id: 0,
            },
            selectedTableId: 0,

            //overlays
            isLoading: true
        }
    },
    async created() {
        await this.getAllJurors();
        await this.getAllTables();
        this.isLoading = false;
    },
    methods: {
        openEditTableModal: function (jury) {
            this.editedJury = {...jury};
            this.selectedTableId = jury.table_id;
            this.editJuryDialog = true;
        },
        editJuryTableRequest: async function () {
            //Verify request

            if (this.selectedTableId === 0) {
                this.snackbar.text = 'Por favor, selecciona una mesa para el usuario';
                this.snackbar.status = true;
                return;
            }
            //Recollect information
            let data = {
                tableId: this.selectedTableId,
            }

            let url = route('api.jurors.update', {'juror': this.editedJury.id});
            try {
                let request = await axios.patch(url, data);
                this.editJuryDialog = false;
                this.snackbar.text = request.data.message;
                this.snackbar.status = true;
                this.getAllJurors();

                //Clear role information
                clearModelProperties(this.editedJury);

            } catch (e) {
                this.snackbar.text = prepareErrorText(e);
                this.snackbar.status = true;
            }
        },

        getAllJurors: async function () {
            let request = await axios.get(route('api.jurors.index'));
            this.jurors = request.data;
        },

        getAllTables: async function () {
            let request = await axios.get(route('api.tables.index'));
            this.tables = request.data;
        },


    },


}
</script>
