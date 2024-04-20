<script setup>
import { ref, computed } from 'vue';
  const props = defineProps([
    'item'
  ])

  const emits = defineEmits([
    'toggleEdit', 'removeItem'
  ])

  const edit = (item) => {
    emits('toggleEdit', item)
  }

  const removeItem = (item) => {
    emits('removeItem', item)
  }
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
      <v-menu transition="scale-transition">
        <template v-slot:activator="{ props }">
          <VBtn v-bind="props" color="red-darken-3" variant="text">
            <VIcon>mdi-delete</VIcon>
          </VBtn>
        </template>
        <v-card title="System Warning" subtitle="are you sure?">
          <VCardActions>
            <VBtn variant="outlined">No</VBtn>
            <VSpacer></VSpacer>
            <VBtn color="red" @click="removeItem(item)" variant="flat">Yes</VBtn>
          </VCardActions>
        </v-card>
      </v-menu>
      <v-btn
        color="green"
        variant="tonal"
        @click="edit(item)"
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