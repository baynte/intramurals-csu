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
      <VSelect v-model="sched.status" variant="outlined" hide-details :items="statusSelection" item-value="value" item-title="text"></VSelect>
    </template>
    <VList>
      <VListItem v-for="p in participants" :key="p.id"
        :title="p.name"
        :prepend-avatar="p.avatar_path"
      >
        <template v-slot:append>
          <div class="d-flex align-center">
            <VBtn icon variant="flat" size="small" color="green">
              <VTooltip activator="parent" location="start">
                Add Current Score
              </VTooltip>
              <VIcon>mdi-plus</VIcon>
            </VBtn>
            <VBtn icon variant="flat" size="small" color="red">
              <VTooltip activator="parent" location="start">
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