<template>
    <AuthenticatedLayout>

        <v-snackbar
  v-model="snackbar.status"
  :timeout="snackbar.timeout"
  :color="snackbar.color"
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

            <!--Crear candidato -->
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
  <v-file-input
    v-model="newCandidate.photo"
    label="Foto del candidato principal"
    accept="image/*"
    prepend-icon="mdi-camera"
    @change="previewImage($event, 'new')"
  />
</v-col>
<v-col cols="12" class="text-center" v-if="previewPhotoNew">
  <v-img
    :src="previewPhotoNew"
    max-height="150"
    max-width="150"
    class="mx-auto rounded elevation-2"
    contain
  />
</v-col>
                                <v-col cols="12">
                                    <h3>Información del candidato suplente</h3>
                                </v-col>

                                <v-col cols="12">
                                    <v-text-field
                                        label="Nombre del suplente"
                                        v-model="newCandidate.substitute_name"
                                    ></v-text-field>
                                </v-col>
                                <v-col cols="12">
                                    <v-text-field
                                        label="Facultad del suplente"
                                        v-model="newCandidate.substitute_faculty"
                                    ></v-text-field>
                                </v-col>
                                <v-col cols="12">
                                    <v-text-field
                                        label="Programa del suplente"
                                        v-model="newCandidate.substitute_program"
                                    ></v-text-field>
                                </v-col>
                                <v-col cols="12">
  <v-file-input
    v-model="newCandidate.substitute_photo"
    label="Foto del candidato suplente"
    accept="image/*"
    prepend-icon="mdi-camera"
    @change="previewImage($event, 'new_sub')"
  />
</v-col>
<v-col cols="12" class="text-center" v-if="previewPhotoNewSub">
  <v-img
    :src="previewPhotoNewSub"
    max-height="150"
    max-width="150"
    class="mx-auto rounded elevation-2"
    contain
  />
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
            <!--Confirmar borrar candidato-->
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
  <v-file-input
    v-model="editedCandidate.photo"
    label="Foto del candidato principal"
    accept="image/*"
    prepend-icon="mdi-camera"
    @change="previewImage($event, 'edit')"
  />
</v-col>
<v-col cols="12" class="text-center" v-if="previewPhotoEdit">
  <v-img
    :src="previewPhotoEdit"
    max-height="150"
    max-width="150"
    class="mx-auto rounded elevation-2"
    contain
  />
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
  <v-file-input
    v-model="editedCandidate.substitute_photo"
    label="Foto del candidato suplente"
    accept="image/*"
    prepend-icon="mdi-camera"
    @change="previewImage($event, 'edit_sub')"
  />
</v-col>
<v-col cols="12" class="text-center" v-if="previewPhotoEditSub">
  <v-img
    :src="previewPhotoEditSub"
    max-height="150"
    max-width="150"
    class="mx-auto rounded elevation-2"
    contain
  />
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
                substitute_photo: null,
            },
            deletedCandidateId: 0,

            //Snackbars
            snackbar: {
                text: '...',
                status: false,
                timeout: 2000,
            },
            //Dialogs
            createCandidateDialog: false,
            deleteCandidateDialog: false,
            editCandidateDialog: false,
            previewPhotoNewSub: null,
previewPhotoEditSub: null,


            //Overlays
            isLoading: true,

            previewPhotoNew: null,
previewPhotoEdit: null,

        }
        
    },
    async created() {
        await this.getAllCandidates();
        this.getAllVotingOptions();
        this.isLoading = false;
    },
    methods: {

        previewImage(file, type) {
    if (!file) return;

    const reader = new FileReader();

    reader.onload = (e) => {
        switch (type) {
            case 'new':
                this.previewPhotoNew = e.target.result;
                break;
            case 'edit':
                this.previewPhotoEdit = e.target.result;
                break;
            case 'new_sub':
                this.previewPhotoNewSub = e.target.result;
                break;
            case 'edit_sub':
                this.previewPhotoEditSub = e.target.result;
                break;
        }
    };

    reader.readAsDataURL(file);
},

openEditCandidateModal: function (candidate) {
    this.editedCandidate = { ...candidate };
    this.editCandidateDialog = true;

    if (candidate.principal_photo && candidate.principal_photo.path) {
    this.previewPhotoEdit = `/storage/${candidate.principal_photo.path}`;
    this.editedCandidate.photo = null; 
} else {
    this.previewPhotoEdit = null;
}


    if (candidate.substitute_photo && candidate.substitute_photo.path) {
        this.previewPhotoEditSub = `/storage/${candidate.substitute_photo.path}`;
        this.editedCandidate.substitute_photo = null;
    } else {
        this.previewPhotoEditSub = null;
    }
},

editCandidate: async function () {
    const formData = new FormData();

    formData.append('principal_name', this.editedCandidate.principal_name);
    formData.append('principal_faculty', this.editedCandidate.principal_faculty);
    formData.append('principal_program', this.editedCandidate.principal_program);
    formData.append('voting_option_id', this.editedCandidate.voting_option_id);

    if (this.editedCandidate.substitute_name)
        formData.append('substitute_name', this.editedCandidate.substitute_name);
    if (this.editedCandidate.substitute_faculty)
        formData.append('substitute_faculty', this.editedCandidate.substitute_faculty);
    if (this.editedCandidate.substitute_program)
        formData.append('substitute_program', this.editedCandidate.substitute_program);

    if (this.editedCandidate.photo instanceof File)
        formData.append('photo', this.editedCandidate.photo);

    if (this.editedCandidate.substitute_photo instanceof File)
        formData.append('substitute_photo', this.editedCandidate.substitute_photo);

    formData.append('_method', 'PUT');

    try {
        const request = await axios.post(route('api.candidates.update', { candidate: this.editedCandidate.id }), formData, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        });

        this.editCandidateDialog = false;
        this.snackbar.text = request.data.message;
        this.snackbar.color = 'green';
        this.snackbar.status = true;

        this.getAllCandidates();
        clearModelProperties(this.editedCandidate);
        this.editedCandidate.photo = undefined;
        this.editedCandidate.substitute_photo = undefined; 
        this.previewPhotoEdit = null;
        this.previewPhotoEditSub = null;
    } catch (e) {
        console.log(e.response.data);
        this.snackbar.text = e.response.data.message || 'Error al actualizar candidato';
        this.snackbar.color = 'red';
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
        console.error(e.response?.data || e);
        this.snackbar.text = e.response?.data?.message || 'Error al eliminar candidato';
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
    const formData = new FormData();

    formData.append('principal_name', this.newCandidate.principal_name);
    formData.append('principal_faculty', this.newCandidate.principal_faculty);
    formData.append('principal_program', this.newCandidate.principal_program);
    formData.append('voting_option_id', this.newCandidate.voting_option_id);

    if (this.newCandidate.substitute_name)
        formData.append('substitute_name', this.newCandidate.substitute_name);
    if (this.newCandidate.substitute_faculty)
        formData.append('substitute_faculty', this.newCandidate.substitute_faculty);
    if (this.newCandidate.substitute_program)
        formData.append('substitute_program', this.newCandidate.substitute_program);

    if (this.newCandidate.photo)
        formData.append('photo', this.newCandidate.photo);

    
    if (this.newCandidate.substitute_photo)
        formData.append('substitute_photo', this.newCandidate.substitute_photo);

    try {
        const request = await axios.post(route('api.candidates.store'), formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
            }
        });

        this.createCandidateDialog = false;
        this.snackbar.text = request.data.message;
        this.snackbar.color = 'green';
        this.snackbar.status = true;

        this.getAllCandidates();
        this.newCandidate = new Candidate();
        this.previewPhotoNew = null;
        this.previewPhotoNewSub = null; 
    } catch (e) {
        console.log(e.response.data);
        this.snackbar.text = e.response.data.message || 'Error al crear candidato';
        this.snackbar.color = 'red';
        this.snackbar.status = true;
    }

},

    },


}
</script>
