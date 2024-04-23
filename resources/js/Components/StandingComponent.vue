<script setup>
import { computed, ref, watch } from 'vue'
import moment from 'moment'
import { useForm } from '@inertiajs/vue3'

const props = defineProps([
  'category', 'participants', 'sched', 'rubricks', 'author'
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

const form = useForm({
  participant_id: null,
  evaluations: [],
  auth_id: null,
  sched_id: null
})

const insights_form = ref([])

const setInsightsForm = () => {
  insights_form.value = props.rubricks.find(function(obj){
    return obj.id === props.sched.rubrick_id
  })?.insights
  .map(function(obj){
    return {
      id: obj.id,
      text: obj.value,
      score: 0
    }
  })
}

if(props.sched.rubrick_id){
  setInsightsForm()
}

watch(() => form.participant_id, (newValue) => {
  setInsightsForm()
})

const updateStatus = () => {
  axios({
    url: route('update.status'),
    method: 'PUT',
    data: {
      status: sched.value.status,
      sched_id: sched.value.id,
    }
  })
}

const updateScore = (item) => {
  axios({
    method: 'PUT',
    url: route('update-schedule-score'),
    data: item
  })
}

const addScore = (item) => {
  item.score += 1
  updateScore(item)
}
const subtractScore = (item) => {
  item.score -= 1
  updateScore(item)
}

const showPrompt = ref(false)
const infoParticipant = computed(() => {
  if(!form.participant_id) return null
  return participants.value.find(x => x.id == form.participant_id)
})

const submitForm = () => {
  form.evaluations = insights_form.value
  form.auth_id = props.author.id
  form.sched_id = props.sched.id
  form.put(route('update-schedule-score-auth'),{
    onSuccess: () => {
      showPrompt.value = false
      form.reset()
    }
  })
}
</script>
<template>
  <div>
    <VSnackbar v-model="form.recentlySuccessful" location="bottom" color="blue">
      {{ `Sucessfully Submitted!` }}
    </VSnackbar>
    <VDialog v-model="showPrompt" width="500">
      <VCard title="System Alert" subtitle="The system has a fewer question">
        <VDivider/>
        <VCardText>
          {{ `Are you sure to submit evaluation for (${infoParticipant?.name})?` }}
        </VCardText>
        <VDivider/>
        <VCardActions>
          <VBtn @click="showPrompt = false">No</VBtn>
          <VSpacer/>
          <VBtn color="green" variant="flat" :loading="form.processing" @click="submitForm">Yes</VBtn>
        </VCardActions>
      </VCard>
    </VDialog>
    <VCard variant="flat">
      <div v-if="author">
        <!-- <p>Current User</p> -->
        <VListItem :title="author.name" class="bg-green" :subtitle="author.position" :append-avatar="author.avatar"></VListItem>
      </div>
      <v-img
        height="200"
          src="https://cdn.vuetifyjs.com/docs/images/cards/purple-flowers.jpg"
          cover
        />
      <VCardTitle>
        {{ category?.name }}
      </VCardTitle>
      <VCardSubtitle>
        {{ `${sched.venue} @ ${sched.time} (${dateFixed})` }}
      </VCardSubtitle>
      <!-- <VDivider/> -->
      <VSelect v-if="!author || !sched.rubrick_id" class="mt-5 mx-3" v-model="sched.status" density="compact" @update:model-value="updateStatus" label="Event Status" variant="outlined" hide-details :items="statusSelection" item-value="value" item-title="text"></VSelect>
      <!-- {{ sched.status }} -->
      <div v-if="sched.rubrick_id" class="mt-1 px-3">
        <VSelect v-model="form.participant_id" :items="participants" density="compact" variant="outlined" item-value="id" item-title="name" class="mb-3 mt-3"  label="Selected Participant" hide-details></VSelect>
        <!-- {{ insights_form }} -->
        <div style="height: calc(100vh - 450px); min-height: 300px; overflow-y: auto" class="">
          <VListSubheader>Rubrick Items:</VListSubheader>
          <VListItem v-for="item, index in insights_form" :key="`categ-${index}`" class="list mb-2">
            <p>{{ `${index+1}. ${item.text}` }}</p>
            <VTextField v-model.number="item.score" :disabled="author ? false : true" type="number" min="0" hide-details variant="underlined" density="compact" class="px-5"/>
          </VListItem>
        </div>
        <VBtn @click="showPrompt = true" block color="green" class="my-3" :disabled="form.participant_id ? false : true">Submit</VBtn>
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
              <VBtn icon @click="addScore(p)" variant="flat" size="small" color="green">
                <VTooltip activator="parent" location="bottom">
                  Add Current Score
                </VTooltip>
                <VIcon>mdi-plus</VIcon>
              </VBtn>
              <VBtn icon @click="subtractScore(p)" variant="flat" size="small" color="red" class="ml-1">
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
  </div>
</template>
<style scoped>
.list:nth-child(even){
  background: rgb(248, 254, 248);
}
</style>