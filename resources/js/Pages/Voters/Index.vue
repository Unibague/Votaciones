<template>
    <AuthenticatedLayout>
        <Snackbar :timeout="snackbar.timeout" :text="snackbar.text" :type="snackbar.type"
                  :show="snackbar.status" @closeSnackbar="snackbar.status = false"></Snackbar>

        <v-container>

            <!-- Título fuera del card -->
            <v-row>
                <v-col cols="12">
                    <h2 class="text-h5 font-weight-bold mb-4">Cargue de votantes</h2>
                </v-col>
            </v-row>

            <v-card>
                <v-card-title>
                        <span>
                        </span>
                </v-card-title>
                <v-card-text>
                    <v-container>
                        <v-alert
                            type="info"
                            outlined
                            border="left"
                            prominent
                            color="primary"
                            class="mb-4"
                        >
                            <v-icon left>mdi-information</v-icon>
                            <strong>Importante:</strong> El archivo Excel debe contener las siguientes columnas y en el orden indicado:
                            <ul class="mt-2">
                                <li><strong>Número de identificación</strong></li>
                                <li><strong>Nombre completo del estudiante</strong></li>
                                <li><strong>Código del programa académico</strong></li>
                                <li><strong>Correo institucional</strong></li>
                            </ul>
                            Asegúrate de que todos los datos estén completos y correctos para evitar errores en la carga.
                        </v-alert>
                        <v-row>
                            <v-col cols="12">
                                <template>
                                    <v-file-input
                                        label="Click aquí para agregar el archivo (formato excel)"
                                        ref="inputFile"
                                        outlined
                                        dense
                                        @change="onFileChanged"
                                    ></v-file-input>

                                    <div class="text-center my-4" v-if="isLoading">
                                        <v-progress-circular
                                            indeterminate
                                            color="primary"
                                            size="32"
                                            class="mb-2"
                                        ></v-progress-circular>
                                        <div class="text-subtitle-2">{{ loadingMessage }}</div>
                                    </div>

                                </template>
                            </v-col>
                        </v-row>
                    </v-container>

                    <v-dialog v-model="errorDialog" max-width="800px">
                        <v-card>
                            <v-card-title class="text-h6">Errores al importar votantes</v-card-title>
                            <v-card-text>
                                <p>Las siguientes filas no fueron importadas debido a errores:</p>
                                <v-simple-table dense>
                                    <thead>
                                    <tr>
                                        <th>Fila</th>
                                        <th>Identificación</th>
                                        <th>Código del programa</th>
                                        <th>Motivo</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr v-for="(error, index) in voterImportErrors" :key="index">
                                        <td>{{ error.row }}</td>
                                        <td>{{ error.identification_number }}</td>
                                        <td>{{ error.program_code }}</td>
                                        <td>{{ error.reason }}</td>
                                    </tr>
                                    </tbody>
                                </v-simple-table>
                            </v-card-text>
                            <v-card-actions>
                                <v-spacer></v-spacer>
                                <v-btn color="primario" text @click="errorDialog = false">Cerrar</v-btn>
                            </v-card-actions>
                        </v-card>
                    </v-dialog>

                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn
                        color="primario"
                        text
                        @click="cancelUpload"
                    >
                        Cancelar
                    </v-btn>
                    <v-btn
                        color="primario"
                        text
                        @click="updateVoters"
                    >
                        Confirmar
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-container>
    </AuthenticatedLayout>
</template>

<script>
import {Link} from '@inertiajs/inertia-vue';
import {prepareErrorText, showSnackbar} from "@/HelperFunctions"
import Snackbar from "@/Components/Snackbar.vue"
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";



export default {
    components: {
        AuthenticatedLayout,
        Link,
        Snackbar
    },

    data: () => ({
        snackbar: {
            text: '...',
            status: false,
            timeout: 3000
        },

        loadingMessage: '',
        selectedFile: null,

        isLogged: false,
        isLoading: false,
        voterImportErrors: [],
        errorDialog: false
    }),

    created() {
        this.isLogged = this.$page.props.user.email !== undefined;
    },

    methods:{

        cancelUpload(){
            this.$refs.inputFile.reset();
        },

        async onFileChanged(e) {
            console.log(e, "info obtenida");
            if (e === null){
                return;
            }
            this.selectedFile = e;
        },

        async updateVoters(){

            if (this.selectedFile !== null){
                const file = new FormData();
                file.append("voters", this.selectedFile)
                this.loadingMessage = "Por favor espera mientras se actualiza la lista de votantes, no recargues ni cierres la página";
                this.isLoading = true;
                try {
                    let request = await axios.post(route('api.voters.store'), file,
                        {headers:{'content-type': 'multipart/form-data'}});
                    showSnackbar(this.snackbar, request.data.message, 'success', 7000);

                    // Mostrar errores si existen
                    if (request.data.skipped && request.data.skipped.length > 0) {
                        this.voterImportErrors = request.data.skipped;
                        this.errorDialog = true;
                    }

                } catch (e) {
                    showSnackbar(this.snackbar, e.response.data.message, 'alert', 12000);
                }
                this.$refs.inputFile.reset();
                this.loadingMessage = "";
                this.isLoading = false;
            }
        }
    }
}
</script>

