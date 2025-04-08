<template>
    <v-app>
      <v-container class="d-flex justify-center align-center" style="min-height: 100vh">
        <v-card
          elevation="10"
          class="pa-8"
          max-width="600"
          rounded="xl"
          style="background: #f9f9f9"
        >
          <v-card-title class="text-h4 font-weight-bold text-center justify-center mb-6">
            Certificado de Votación
          </v-card-title>
  
          <v-card-text class="text-center">
            <p class="text-subtitle-1 mb-2">Este certificado valida que:</p>
  
            <h2 class="text-h5 font-weight-bold mb-1">
              {{ voter.name }}
            </h2>
  
            <p class="text-subtitle-2 mb-3">
              con número de identificación <strong>{{ voter.identification_number }}</strong>
            </p>
  
            <p class="text-subtitle-1 mb-1">
              ejerció su derecho al voto el día:
            </p>
  
            <p class="text-h6 font-weight-bold">
              {{ voted_at }}
            </p>
  
            <v-divider class="my-6"></v-divider>
  
            <p class="text-caption grey--text">
              Gracias por participar en el proceso electoral de la Universidad de Ibagué.
            </p>
          </v-card-text>
        </v-card>
      </v-container>
    </v-app>
  </template>
  
  <script>
  export default {
    props: {
      voter: Object,
      voted_at: String
    }
  };
  </script>
  