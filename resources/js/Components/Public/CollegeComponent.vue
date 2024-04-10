<template>
  <div>
    <VSelect label="Participant" density="compact" hide-details></VSelect>
    <VDataTable :headers="headers"></VDataTable>
  </div>  
</template>
<script setup>
import { computed } from "vue";
const props = defineProps(['categories'])
  const headers = computed(() => {
    return [
      { title: 'Category', key: 'college' },
      { title: 'Rank', key: 'wins' },
      { title: 'Points', key: 'loss' },
      // { title: 'Draws', key: 'draws' },
    ]
  })
</script>