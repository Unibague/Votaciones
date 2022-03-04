<template>
    <GeneralLayout>
        <div class="align-center px-6 " style="width: 100%">
            <h1 class="text-center">Emitiendo voto: {{ voter.name }}</h1>
            <v-divider class="my-6"></v-divider>

            <div class="fill-height my-4" v-if="isLoading">
                <v-row>
                    <v-col cols="4" v-for="(skeleton,key) in 4" :key="key">
                        <v-skeleton-loader type="card"></v-skeleton-loader>
                    </v-col>
                </v-row>
            </div>
            <template v-for="(votingOption,key) in votingOptions" v-if="!isLoading">
                <h2 class="mb-6 text-center">{{ votingOption.name }}</h2>
                <v-row>
                    <v-col cols="3" v-for="candidate in votingOption.candidates" :key="candidate.name">
                        <vue-glow color="#1e3a62" mode="hex" elevation="10"
                                  :intensity="votingOption.selectedCandidateId === candidate.id ? 1.5:0">
                            <v-card outlined>
                                <v-card-title>
                                <span class="text-truncate">
                                   {{ candidate.principal_name }}
                                </span>
                                </v-card-title>
                                <v-card-subtitle>
                                    Facultad {{ candidate.principal_faculty }} | Programa {{
                                        candidate.principal_program
                                    }}
                                </v-card-subtitle>
                                <v-card-title style="padding-top: 0">
                                     <span class="text-truncate">
                                    {{ candidate.substitute_name }}
                                    </span>
                                </v-card-title>
                                <v-card-subtitle>
                                    Facultad {{ candidate.substitute_faculty }} | Programa {{
                                        candidate.substitute_program
                                    }}
                                </v-card-subtitle>
                                <v-card-actions class="d-flex justify-center mb-2">
                                    <v-btn
                                        @click="selectCandidate(votingOption,candidate.id)"
                                        rounded
                                        color="primario"
                                        class="grey--text text--lighten-4">
                                            <span class="px-2">
                                                Seleccionar
                                            </span>
                                    </v-btn>
                                </v-card-actions>
                            </v-card>
                        </vue-glow>
                    </v-col>

                    <v-col cols="3">
                        <vue-glow color="#1e3a62" mode="hex" elevation="10"
                                  :intensity="votingOption.selectedCandidateId === 0? 1.5:0">
                            <v-card outlined>
                                <v-card-title>
                                <span class="text-truncate">
                                   Voto en blanco
                                </span>
                                </v-card-title>
                                <v-card-subtitle>
                                    Selecciona para votar en blanco
                                </v-card-subtitle>
                                <v-card-title style="padding-top: 0">
                                    Sin candidatos
                                </v-card-title>
                                <v-card-subtitle>
                                    No votarás por ningun candidato
                                </v-card-subtitle>
                                <v-card-actions class="d-flex justify-center mb-2">
                                    <v-btn
                                        @click="selectCandidate(votingOption,0)"
                                        rounded
                                        color="primario"
                                        class="grey--text text--lighten-4">
                                            <span class="px-2">
                                                Seleccionar
                                            </span>
                                    </v-btn>
                                </v-card-actions>
                            </v-card>
                        </vue-glow>
                    </v-col>
                </v-row>
                <v-divider class="my-8" v-if="key !== (votingOptions.length-1)"></v-divider>
            </template>

            <div class="d-flex justify-center mt-12" v-if="!isLoading">
                <v-btn
                    @click="vote"
                    color="primario"
                    large
                    class="grey--text text--lighten-4">
                    Emitir voto
                </v-btn>
            </div>
        </div>

        <!-- SNACKBAR-->
        <v-snackbar
            v-model="snackbar.status"
            :timeout="snackbar.timeout"
            color="red accent-2"
            top
            right
        >
            {{ snackbar.text }}
        </v-snackbar>
        <!-- dialogs-->

        <v-dialog v-model="showDialog" width="500" persistent>
            <v-card>
                <v-card-title>
                    ¡Gracias por votar!
                </v-card-title>
                <v-card-text>
                    Tu voto ha sido registrado exitosamente
                </v-card-text>
                <v-card-actions class="d-flex justify-end">
                    <v-btn
                        @click="closeTab()"
                        color="primario"
                        class="grey--text text--lighten-4">
                        Finalizar
                    </v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </GeneralLayout>

</template>


<script>
import GeneralLayout from "@/Layouts/GeneralLayout";
import VueGlow from 'vue-glow';

export default {
    components: {
        GeneralLayout,
        VueGlow
    },
    data: () => {
        return {
            isLoading: true,
            votingOptions: [],

            //Snackbars
            snackbar: {
                text: '...',
                status: false,
                timeout: 3000
            },

            //dialogs
            showDialog: false,
        }
    },
    props: {
        voter: Object
    },
    methods: {
        closeTab: () => {
            window.close();
        },
        selectCandidate: function (votingOption, candidateId) {
            this.votingOptions.forEach((currentVotingOption) => {
                if (currentVotingOption.id === votingOption.id) {
                    this.$set(currentVotingOption, 'selectedCandidateId', candidateId);
                }

            });
        }
        ,
        getVotingOptions: async function () {
            let request = await axios.get(route('api.votes.getVoterVotingOptions', {voter: this.voter.id}));
            this.votingOptions = request.data;
        },

        vote: async function () {
            if (!(this.AllVotingOptionsAreSelected())) {
                return;
            }
            let data = [];

            //Iterate over all voting options, get the selected value.
            this.votingOptions.forEach((currentVotingOption) => {
                data.push({
                    voter_id: this.voter.id,
                    voting_option_id: currentVotingOption.id,
                    candidate_id: currentVotingOption.selectedCandidateId
                })
            });
            //Send request
            try {
                let request = await axios.post(route('api.votes.store'), {userVotes: data});
                this.showDialog = true;
            } catch (e) {
                this.snackbar.text = e.response.data.message;
                this.snackbar.status = true;
            }

        },

        makeVoteRequest: async function (data) {
            let url = route('api.votes.store');
        },

        AllVotingOptionsAreSelected: function () {
            let response = true;
            this.votingOptions.forEach((currentVotingOption) => {
                if (currentVotingOption.selectedCandidateId === undefined) {
                    this.snackbar.text = 'Por favor, debes seleccionar un candidato por cada opción. Si no deseas votar, ' +
                        'selecciona voto en blanco';
                    this.snackbar.status = true;
                    response = false;
                }
            });
            return response;
        }

    },
    computed: {},
    async created() {
        await this.getVotingOptions();
        this.isLoading = false;
    }
}
</script>
