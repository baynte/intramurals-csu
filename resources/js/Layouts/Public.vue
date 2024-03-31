<script setup>
  import { ref } from "vue"

  const items = ["2024"]
  
</script>
<template>
   <v-app>
    <v-main>
      <slot></slot>
    </v-main>
  </v-app>
</template>