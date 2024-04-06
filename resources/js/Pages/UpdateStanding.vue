<script setup>
// import Public from '@/Layouts/Public.vue';
import { Head } from '@inertiajs/vue3'
import { ref } from 'vue'
import StandingVue from '@/Components/StandingComponent.vue'

const props = defineProps([
  'category', 'participants', 'sched', 'rubricks', 'author'
])


const scoring_type = ref("default")
</script>
<template>
  <v-app>
    <Head title="Edit Standing"></Head>
    <v-main>
      <StandingVue :participants="participants" :category="category" :sched="sched" :rubricks="rubricks" :author="author" />
    </v-main>
  </v-app>
</template>