<script setup>
import { computed, ref } from 'vue'
import moment from 'moment'

const props = defineProps([
  'category', 'participants', 'sched'
])

const sched = ref(props.sched)
const participants = ref(props.participants)

const statusSelection = [
  { value: 'pending', text: 'Pending' },
  { value: 'on-going', text: 'On Going' },
  { value: 'finished', text: 'Finished' },
]

const dateFixed = computed(() => {
  const from = moment(sched.value.date_from, 'YYYY-MM-DD').format('MMMM DD, YYYY')
  const to = moment(sched.value.date_to, 'YYYY-MM-DD').format('MMMM DD, YYYY')
  return from == to ? from : from + ' - ' + to
})
</script>
<template>
  <VCard :title="category?.name" :subtitle="`${sched.venue} @ ${sched.time} (${dateFixed})`" variant="flat">
    <template v-slot:append>
      <VSelect class="d-none d-sm-block" v-model="sched.status" variant="outlined" hide-details :items="statusSelection" item-value="value" item-title="text"></VSelect>
    </template>
    <VSelect class="d-block d-sm-none mt-3" v-model="sched.status" variant="outlined" hide-details :items="statusSelection" item-value="value" item-title="text"></VSelect>
    <div v-if="true" class="mt-1">
      <VSelect :items="participants" item-value="id" item-title="name" label="Selected Participant" hide-details></VSelect>
    </div>
    <VList v-else class="mt-5">
      <VListItem v-for="p in participants" :key="p.id"
        :title="p.name"
        :prepend-avatar="p.avatar_path"
        class="list"
      >
        <template v-slot:append>
          <p style="font-size: 1.8rem" class="font-weight-bold">{{ p.score }}</p>
          <div class="ml-5 d-flex align-center">
            <VBtn icon variant="flat" size="small" color="green">
              <VTooltip activator="parent" location="bottom">
                Add Current Score
              </VTooltip>
              <VIcon>mdi-plus</VIcon>
            </VBtn>
            <VBtn icon variant="flat" size="small" color="red" class="ml-1">
              <VTooltip activator="parent" location="bottom">
                Subtract Current Score
              </VTooltip>
              <VIcon>mdi-minus</VIcon>
            </VBtn>
          </div>
        </template>
      </VListItem>
    </VList>
  </VCard>
</template>
<style scoped>
.list:nth-child(even){
  background: rgb(248, 254, 248);
}
</style>