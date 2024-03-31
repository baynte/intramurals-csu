<script setup>
import Admin from '@/Layouts/Admin.vue';
import { Head } from '@inertiajs/vue3'
</script>
<template>
  <Admin>
    <Head title="Admin - Rubricks"></Head>
    <template #header-title>
      Rubricks
    </template>
    <template #header-items>
      <VBtn variant="tonal">Create</VBtn>
    </template>
    <div>
    </div>
  </Admin>
</template>