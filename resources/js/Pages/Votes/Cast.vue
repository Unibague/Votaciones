<template>
    <GeneralLayout>
        <div class="tarjeton-container px-6">
            <v-snackbar
                v-model="snackbar.status"
                :timeout="snackbar.timeout"
                :color="snackbar.color"
                top
                right
            >
                {{ snackbar.text }}
            </v-snackbar>

            <v-dialog
                v-model="resultDialog.show"
                persistent
                max-width="560"
            >
                <v-card class="result-card">
                    <div class="result-hero">
                        <div class="result-icon-wrap">
                            <v-icon large color="white">mdi-check-decagram</v-icon>
                        </div>
                        <div class="result-kicker">Sistema de votaciones</div>
                        <v-card-title class="result-title">
                            {{ resultDialog.title }}
                        </v-card-title>
                        <div class="result-subtitle">
                            Tu seleccion fue registrada correctamente.
                        </div>
                    </div>
                    <v-card-text class="result-text">
                        <div class="result-panel">
                            <div class="result-panel-label">Estado del registro</div>
                            <div class="result-panel-message">{{ resultDialog.message }}</div>
                        </div>

                        <div class="result-mail-card" :class="resultDialog.mailSent ? 'is-success' : 'is-warning'">
                            <div class="result-mail-icon">
                                <v-icon :color="resultDialog.mailSent ? '#1b5e20' : '#b26a00'">
                                    {{ resultDialog.mailSent ? 'mdi-email-check-outline' : 'mdi-email-alert-outline' }}
                                </v-icon>
                            </div>
                            <div class="result-mail-content">
                                <div class="result-mail-label">
                                    {{ resultDialog.mailSent ? 'Comprobante enviado' : 'Envio de comprobante pendiente' }}
                                </div>
                                <div class="result-mail-message">
                                    {{ resultDialog.mailMessage }}
                                </div>
                            </div>
                        </div>
                    </v-card-text>
                    <v-card-actions class="result-actions">
                        <v-btn
                            text
                            color="primario"
                            class="result-secondary-btn"
                            @click="goBackToAuthorization"
                        >
                            Volver
                        </v-btn>
                        <v-btn
                            color="primario"
                            class="grey--text text--lighten-4 result-primary-btn"
                            @click="attemptCloseWindow"
                        >
                            Cerrar ventana
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>

            <h1 class="text-center tarjeton-title">
                Emitiendo voto
            </h1>
            <p class="text-center tarjeton-subtitle">
                {{ voter.name }} · Haz clic sobre el tarjetón de tu preferencia
            </p>

            <v-divider class="my-6"></v-divider>

            <div v-if="isLoading">
                <v-row>
                    <v-col cols="3" v-for="key in 4" :key="key">
                        <v-skeleton-loader type="card"></v-skeleton-loader>
                    </v-col>
                </v-row>
            </div>

            <template v-for="(votingOption, key) in votingOptions" v-if="!isLoading">
                <h2 class="text-center tarjeton-section">
                    {{ votingOption.name }}
                </h2>

                <v-row>
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
                                <div class="tarjeton-photo">
                                    <template v-if="candidate.principal_photo && candidate.principal_photo.url">
                                        <v-img
                                            :src="candidate.principal_photo.url"
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

                                <template v-if="candidate.substitute_name || (candidate.substitute_photo && candidate.substitute_photo.url)">
                                    <v-divider class="my-3"></v-divider>

                                    <div class="tarjeton-photo small">
                                        <template v-if="candidate.substitute_photo && candidate.substitute_photo.url">
                                            <v-img
                                                :src="candidate.substitute_photo.url"
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
import VueGlow from "vue-glow";

export default {
    components: {
        GeneralLayout,
        VueGlow,
    },
    props: {
        voter: Object,
    },
    data: () => ({
        isLoading: true,
        votingOptions: [],
        isVoting: false,
        closeTimeoutId: null,
        snackbar: {
            text: "...",
            status: false,
            timeout: 3000,
            color: "red accent-2",
        },
        resultDialog: {
            show: false,
            title: "",
            message: "",
            mailMessage: "",
            mailSent: false,
        },
    }),
    methods: {
        selectCandidate(votingOption, candidateId) {
            this.votingOptions.forEach((currentVotingOption) => {
                if (currentVotingOption.id === votingOption.id) {
                    this.$set(currentVotingOption, "selectedCandidateId", candidateId);
                }
            });
        },
        async getVotingOptions() {
            const request = await axios.get(route("api.votes.getVoterVotingOptions", { voter: this.voter.id }));
            this.votingOptions = request.data;
        },
        notifyParent(message, mailSent, mailMessage) {
            const payload = {
                type: "vote-registered",
                message,
                mail_sent: mailSent,
                mail_message: mailMessage,
            };

            localStorage.setItem("vote_registered_payload", JSON.stringify({
                ...payload,
                at: Date.now(),
            }));

            if (window.opener && !window.opener.closed) {
                window.opener.postMessage(payload, window.location.origin);
                window.opener.focus();
            }
        },
        showResultDialog(message, mailSent, mailMessage) {
            this.resultDialog = {
                show: true,
                title: "Voto registrado exitosamente",
                message,
                mailMessage,
                mailSent,
            };

            this.closeTimeoutId = setTimeout(() => {
                this.attemptCloseWindow();
            }, 2500);
        },
        attemptCloseWindow() {
            if (this.closeTimeoutId) {
                clearTimeout(this.closeTimeoutId);
                this.closeTimeoutId = null;
            }

            window.open("", "_self");
            window.close();

            if (!window.closed) {
                this.snackbar.text = "Puedes cerrar esta ventana manualmente o volver a la autorización.";
                this.snackbar.color = "blue darken-1";
                this.snackbar.status = true;
            }
        },
        goBackToAuthorization() {
            if (this.closeTimeoutId) {
                clearTimeout(this.closeTimeoutId);
                this.closeTimeoutId = null;
            }

            window.location.href = route("votes.authorize");
        },
        async vote() {
            if (!this.allVotingOptionsAreSelected()) {
                return;
            }

            this.isVoting = true;

            const data = this.votingOptions.map((currentVotingOption) => ({
                voter_id: this.voter.id,
                voting_option_id: currentVotingOption.id,
                candidate_id: currentVotingOption.selectedCandidateId,
            }));

            try {
                const request = await axios.post(route("api.votes.store"), { userVotes: data });
                const message = request.data.message || "Voto registrado exitosamente.";
                const mailSent = !!request.data.mail_sent;
                const mailMessage = request.data.mail_message || "";

                this.notifyParent(message, mailSent, mailMessage);
                this.showResultDialog(message, mailSent, mailMessage);
            } catch (e) {
                this.snackbar.text = e?.response?.data?.message || "No fue posible registrar el voto.";
                this.snackbar.color = "red accent-2";
                this.snackbar.status = true;
            } finally {
                this.isVoting = false;
            }
        },
        allVotingOptionsAreSelected() {
            let response = true;

            this.votingOptions.forEach((currentVotingOption) => {
                if (currentVotingOption.selectedCandidateId === undefined) {
                    this.snackbar.text = "Por favor, debes seleccionar un candidato por cada opción. Si no deseas votar, selecciona voto en blanco.";
                    this.snackbar.color = "red accent-2";
                    this.snackbar.status = true;
                    response = false;
                }
            });

            return response;
        },
    },
    async created() {
        await this.getVotingOptions();
        this.isLoading = false;
    },
    beforeDestroy() {
        if (this.closeTimeoutId) {
            clearTimeout(this.closeTimeoutId);
        }
    },
};
</script>

<style>
.tarjeton-container {
    width: 100%;
}

.result-card {
    border-radius: 24px;
    overflow: hidden;
    background:
        radial-gradient(circle at top left, rgba(215, 188, 118, 0.18), transparent 32%),
        linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
    box-shadow: 0 24px 60px rgba(15, 23, 42, 0.2);
}

.result-hero {
    padding: 28px 32px 18px;
    text-align: center;
    background: linear-gradient(135deg, #102947 0%, #1e3a62 58%, #2f5b94 100%);
    color: #fff;
}

.result-icon-wrap {
    width: 72px;
    height: 72px;
    margin: 0 auto 14px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #d7bc76 0%, #b8923f 100%);
    box-shadow: 0 12px 24px rgba(7, 18, 35, 0.32);
}

.result-kicker {
    font-size: 0.78rem;
    font-weight: 700;
    letter-spacing: 0.18em;
    text-transform: uppercase;
    color: rgba(255, 255, 255, 0.72);
}

.result-title {
    color: #ffffff;
    font-weight: 700;
    font-size: 1.8rem;
    line-height: 1.2;
    justify-content: center;
    padding: 8px 0 4px;
}

.result-subtitle {
    color: rgba(255, 255, 255, 0.82);
    font-size: 0.98rem;
}

.result-text {
    font-size: 1rem;
    padding: 28px 32px 14px !important;
}

.result-panel {
    border: 1px solid rgba(30, 58, 98, 0.1);
    border-radius: 18px;
    padding: 18px 20px;
    background: rgba(255, 255, 255, 0.92);
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.7);
}

.result-panel-label {
    font-size: 0.78rem;
    font-weight: 700;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    color: #6b7280;
    margin-bottom: 8px;
}

.result-panel-message {
    font-size: 1.05rem;
    color: #0f172a;
    font-weight: 600;
}

.result-mail-card {
    margin-top: 16px;
    border-radius: 18px;
    padding: 16px 18px;
    display: flex;
    gap: 14px;
    align-items: flex-start;
}

.result-mail-card.is-success {
    background: linear-gradient(180deg, rgba(232, 245, 233, 0.95), rgba(244, 251, 245, 0.95));
    border: 1px solid rgba(46, 125, 50, 0.16);
}

.result-mail-card.is-warning {
    background: linear-gradient(180deg, rgba(255, 244, 229, 0.98), rgba(255, 249, 240, 0.98));
    border: 1px solid rgba(178, 106, 0, 0.18);
}

.result-mail-icon {
    width: 42px;
    height: 42px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(255, 255, 255, 0.65);
    flex: 0 0 42px;
}

.result-mail-content {
    min-width: 0;
}

.result-mail-label {
    font-size: 0.95rem;
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 4px;
}

.result-mail-message {
    font-size: 0.95rem;
    line-height: 1.55;
    color: #475569;
}

.result-actions {
    padding: 0 32px 28px !important;
    gap: 10px;
    justify-content: flex-end;
}

.result-primary-btn {
    min-width: 168px;
    border-radius: 12px;
    font-weight: 700;
    letter-spacing: 0.02em;
    box-shadow: 0 10px 22px rgba(30, 58, 98, 0.24);
}

.result-secondary-btn {
    border-radius: 12px;
    font-weight: 600;
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
    background: linear-gradient(135deg, #1e3a62, #274b8a);
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
