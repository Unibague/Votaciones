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
            <h1 class="text-center mb-6">Autorizar votante</h1>
            <v-row class="align-content-space-between" style="height:70vh">
                <v-spacer></v-spacer>
                <v-col cols="7">
                    <v-card v-if="hasData">
                        <v-card-title class="text-h5">
                            {{ voter.name }}
                        </v-card-title>
                        <v-card-subtitle>
                            {{ voter.identification_number }}
                        </v-card-subtitle>
                        <v-card-text>
                            Facultad de {{ facultyName }} | Programa de {{ programName }}
                        </v-card-text>
                        <v-card-actions>
                            <v-btn
                                dark
                                block
                                @click="authorizeVote(voter.id)"
                            >
                                Autorizar voto
                            </v-btn>
                        </v-card-actions>
                    </v-card>
                </v-col>
                <v-spacer></v-spacer>
                <v-col cols="12">
                    <h3>
                        Por favor, ingrese el numero de documento del votante
                    </h3>
                    <v-input>
                        <v-text-field
                            label="Documento"
                            required
                            v-model="identification_number"
                        ></v-text-field>
                    </v-input>
                    <v-btn
                        block
                        color="primario"
                        class="grey--text text--lighten-4"
                        @click="searchVoter"
                    >
                        Buscar
                    </v-btn>
                </v-col>
            </v-row>
        </v-container>
    </AuthenticatedLayout>
</template>

<script>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { clearModelProperties } from "@/HelperFunctions";
import ConfirmDialog from "@/Components/ConfirmDialog";

export default {
    components: {
        ConfirmDialog,
        AuthenticatedLayout,
    },
    data: () => ({
        snackbar: {
            text: "...",
            status: false,
            timeout: 2500,
            color: "red accent-2",
        },
        voter: {
            name: "Esperando datos",
            identification_number: "...",
            faculty: null,
            program: null,
        },
        hasData: false,
        identification_number: "",
    }),
    computed: {
        facultyName() {
            return this.voter && this.voter.faculty && this.voter.faculty.name
                ? this.voter.faculty.name
                : "Sin facultad";
        },
        programName() {
            return this.voter && this.voter.program && this.voter.program.name
                ? this.voter.program.name
                : "Sin programa";
        },
    },
    methods: {
        authorizeVote(voterId) {
            window.open(route("votes.vote") + "?voterId=" + voterId, "_blank", "toolbar=0,location=0,menubar=0");
            clearModelProperties(this.voter);
            this.hasData = false;
            this.identification_number = "";
        },
        handleVoteRegistered(event) {
            if (event.origin !== window.location.origin) {
                return;
            }

            if (!event.data || event.data.type !== "vote-registered") {
                return;
            }

            this.showVoteRegisteredFeedback(event.data);
        },
        showVoteRegisteredFeedback(payload) {
            const baseMessage = payload.message || "Voto registrado exitosamente.";
            const mailMessage = payload.mail_message || "";

            this.snackbar.text = mailMessage ? `${baseMessage} ${mailMessage}` : baseMessage;
            this.snackbar.color = payload.mail_sent ? "green darken-1" : "orange darken-2";
            this.snackbar.status = true;
        },
        async searchVoter() {
            if (this.identification_number === "") {
                this.snackbar.text = "Por favor, ingresa un documento válido";
                this.snackbar.color = "red accent-2";
                this.snackbar.status = true;
                return;
            }

            const data = {
                identification_number: this.identification_number,
            };

            try {
                const request = await axios.get(route("api.voters.searchByIdentificationNumber"), { params: data });
                this.voter = request.data;
                this.hasData = true;
            } catch (e) {
                if (e.response.status === 404) {
                    this.snackbar.text = "Usuario no encontrado en el censo electoral";
                } else {
                    this.snackbar.text = e.response.data.message;
                }

                this.snackbar.color = "red accent-2";
                this.snackbar.status = true;
            }
        },
    },
    mounted() {
        window.addEventListener("message", this.handleVoteRegistered);

        const storedPayload = localStorage.getItem("vote_registered_payload");
        if (storedPayload) {
            try {
                const parsedPayload = JSON.parse(storedPayload);
                this.showVoteRegisteredFeedback(parsedPayload);
            } finally {
                localStorage.removeItem("vote_registered_payload");
            }
        }
    },
    beforeDestroy() {
        window.removeEventListener("message", this.handleVoteRegistered);
    },
};
</script>
