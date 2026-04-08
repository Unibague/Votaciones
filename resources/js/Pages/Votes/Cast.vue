<template>
    <GeneralLayout>
        <div class="tarjeton-container px-6">

            <h1 class="text-center tarjeton-title">
                Emitiendo voto
            </h1>
            <p class="text-center tarjeton-subtitle">
                {{ voter.name }} · Haz clic sobre el tarjetón de tu preferencia
            </p>

            <v-divider class="my-6"></v-divider>

            <!-- LOADING -->
            <div v-if="isLoading">
                <v-row>
                    <v-col cols="3" v-for="key in 4" :key="key">
                        <v-skeleton-loader type="card"></v-skeleton-loader>
                    </v-col>
                </v-row>
            </div>

            <!-- OPCIONES -->
            <template v-for="(votingOption, key) in votingOptions" v-if="!isLoading">
                <h2 class="text-center tarjeton-section">
                    {{ votingOption.name }}
                </h2>

                <v-row>
                    <!-- CANDIDATOS -->
                    <v-col cols="3" v-for="candidate in votingOption.candidates" :key="candidate.id">
                        <vue-glow
                            color="#1e3a62"
                            :intensity="votingOption.selectedCandidateId === candidate.id ? 2.5 : 0"
                        >
                            <v-card
                                outlined
                                class="tarjeton-card"
                                :class="{ selected: votingOption.selectedCandidateId === candidate.id }"
                                @click="selectCandidate(votingOption, candidate.id)"
                            >

                                <!-- PRINCIPAL -->
                                <div class="tarjeton-photo">
                                    <template v-if="candidate.principal_photo && candidate.principal_photo.path">

                                        <v-img
                                            :src="`/storage/${candidate.principal_photo.path}`"
                                            max-width="200"
                                            max-height="200"
                                            contain
                                            class="rounded"
                                        />
                                    </template>
                                    <template v-else>
                                        <v-avatar size="200">
                                            <v-icon size="180">mdi-account-circle</v-icon>
                                        </v-avatar>
                                    </template>
                                </div>

                                <div class="tarjeton-name">
                                    {{ candidate.principal_name }}
                                </div>

                                <div class="tarjeton-meta">
                                    {{ candidate.principal_faculty }} · {{ candidate.principal_program }}
                                </div>

                                <!-- SUPLENTE -->
                                <template v-if="candidate.substitute_name || (candidate.substitute_photo && candidate.substitute_photo.path)">

                                    <v-divider class="my-3"></v-divider>

                                    <div class="tarjeton-photo small">
                                        <template v-if="candidate.substitute_photo && candidate.substitute_photo.path">

                                            <v-img
                                                :src="`/storage/${candidate.substitute_photo.path}`"
                                                max-width="150"
                                                max-height="150"
                                                contain
                                                class="rounded"
                                            />
                                        </template>
                                        <template v-else>
                                            <v-avatar size="150">
                                                <v-icon size="130">mdi-account-circle</v-icon>
                                            </v-avatar>
                                        </template>
                                    </div>

                                    <div class="tarjeton-name small">
                                        {{ candidate.substitute_name }}
                                    </div>

                                    <div class="tarjeton-meta">
                                        {{ candidate.substitute_faculty }} · {{ candidate.substitute_program }}
                                    </div>
                                </template>

                                <!-- CHECK -->
                                <div class="tarjeton-check">
                                    <v-icon v-if="votingOption.selectedCandidateId === candidate.id">
                                        mdi-check-circle
                                    </v-icon>
                                    <span>
                                        {{ votingOption.selectedCandidateId === candidate.id ? 'Seleccionado' : '' }}
                                    </span>
                                </div>

                            </v-card>
                        </vue-glow>
                    </v-col>

                    <!-- VOTO EN BLANCO -->
                    <v-col cols="3">
                        <vue-glow
                            color="#1e3a62"
                            :intensity="votingOption.selectedCandidateId === 0 ? 2.5 : 0"
                        >
                            <v-card
                                outlined
                                class="tarjeton-card blank"
                                :class="{ selected: votingOption.selectedCandidateId === 0 }"
                                @click="selectCandidate(votingOption, 0)"
                            >
                                <div class="tarjeton-name">
                                    Voto en blanco
                                </div>

                                <div class="tarjeton-meta">
                                    No seleccionar ningún candidato
                                </div>

                                <div class="tarjeton-check">
                                    <v-icon v-if="votingOption.selectedCandidateId === 0">
                                        mdi-check-circle
                                    </v-icon>
                                    <span>
                                        {{ votingOption.selectedCandidateId === 0 ? 'Seleccionado' : '' }}
                                    </span>
                                </div>
                            </v-card>
                        </vue-glow>
                    </v-col>
                </v-row>

                <v-divider class="my-10" v-if="key !== votingOptions.length - 1"></v-divider>
            </template>

            <!-- BOTÓN FINAL -->
            <div class="d-flex justify-center mt-12" v-if="!isLoading">
                <v-btn
                    @click="vote"
                    :disabled="isVoting"
                    color="primario"
                    large
                    class="emitir-voto-btn"
                >
                    Emitir voto
                </v-btn>
            </div>
        </div>
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
            isVoting: false,

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

            console.log('Opciones de votación cargadas:', this.votingOptions); // 👈 Añade esto
        },


        vote: async function () {
            if (!(this.AllVotingOptionsAreSelected())) {
                return;
            }


            this.isVoting = true;

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

            this.isVoting = false;

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
 
<style>
.tarjeton-container {
    width: 100%;
}

.tarjeton-title {
    font-weight: 700;
    color: #1e3a62;
}

.tarjeton-subtitle {
    color: #6b7280;
    font-size: 1rem;
}

.tarjeton-section {
    font-weight: 600;
    margin-bottom: 2rem;
    color: #1e3a62;
}

.tarjeton-card {
    cursor: pointer;
    border-radius: 16px;
    padding: 18px;
    text-align: center;
    transition: all 0.25s ease;
    min-height: 520px;
}

.tarjeton-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 14px 30px rgba(30, 58, 98, 0.25);
}

.tarjeton-card.selected {
    border: 2px solid #1e3a62;
    background-color: #eef3fb;
}

.tarjeton-photo {
    display: flex;
    justify-content: center;
    margin-bottom: 12px;
}

.tarjeton-photo.small {
    margin-top: 10px;
}

.tarjeton-name {
    font-weight: 600;
    font-size: 1.05rem;
    margin-top: 6px;
}

.tarjeton-name.small {
    font-size: 0.95rem;
}

.tarjeton-meta {
    font-size: 0.85rem;
    color: #6b7280;
}

.tarjeton-check {
    margin-top: 14px;
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 6px;
    font-size: 0.9rem;
    color: #1e3a62;
}

.blank {
    display: flex;
    flex-direction: column;
    justify-content: center;
    min-height: 520px;
}

.emitir-voto-btn {
    font-weight: 600;
    padding: 14px 48px;
    border-radius: 10px;
    font-size: 1rem;

    background: linear-gradient(
        135deg,
        #1e3a62,
        #274b8a
    );

    color: #ffffff !important;
    box-shadow: 0 10px 20px rgba(30, 58, 98, 0.35);
    transition: all 0.25s ease;
}

.emitir-voto-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 14px 28px rgba(30, 58, 98, 0.45);
}

.emitir-voto-btn:disabled {
    opacity: 0.6;
    box-shadow: none;
}


</style>