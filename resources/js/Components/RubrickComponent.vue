<script setup>
import { ref, computed } from 'vue';
  const props = defineProps([
    'item'
  ])

  const show = ref(false)
</script>
<template>
  <VCard :title="item.title" :subtitle="`Rubrick Title`">
    <v-card-actions>
      <v-btn
        v-if="item.insights.length"
        :append-icon="show ? 'mdi-chevron-up' : 'mdi-chevron-down'"
        @click="show = !show"
      >
      Show Insights
      </v-btn>
      <v-btn v-else disabled>
      No Insights Added
      </v-btn>
      <v-spacer></v-spacer>
      <v-btn
        color="green"
        variant="tonal"
      >
        edit
      </v-btn>
    </v-card-actions>
    <v-expand-transition>
      <div v-show="show">
        <VDivider/>
        <VListItem v-for="item, index in item.insights" :key="`categ-${index}`" class="list">
          <p>{{ `${index+1}. ${item.value}` }}</p>
        </VListItem>
      </div>
    </v-expand-transition>
  </VCard>
</template>
<style scoped>
.list:nth-child(even){
  background: rgb(248, 254, 248);
}
</style>