<script setup>
// import Public from '@/Layouts/Public.vue';
import { Head, router, useForm, usePage } from '@inertiajs/vue3'
import { computed, ref, watch } from 'vue'
import StandingVue from '@/Components/StandingComponent.vue'

const props = defineProps([
  'category', 'participants', 'sched', 'rubricks', 'authors'
])

const rubrickId = ref(null)
const scoring_type = ref("default")
const authors = ref([...props.authors])

if(props.sched.rubrick_id){
  scoring_type.value = "rubrick"
  rubrickId.value = props.sched.rubrick_id
}

watch(scoring_type, (newValue) => {
  if(newValue === 'default'){
    rubrickId.value = null
  }
})

const canProceedUpdateRubrick = computed(() => {
  if(scoring_type.value !== "default") return rubrickId.value ? true : false
  return true
})

const updateRubrickType = () => {
  router.put(route('admin.update-rubrick-type', {id: props.sched?.id}),{
    rubrick_id: rubrickId.value
  }, {
    preserveState: false
  })
}

const showAuthorInfo = ref(false)
const authorInfo = ref(null)
const showAuthorForm = ref(false)
const author = useForm({
  name: '',
  position: '',
  sched_id: props.sched.id
})

const toggleAuthorForm = () => {
  showAuthorForm.value = true
}

const submitAuthor = () => {
  author.post(route('admin.schedule-auth.store'), {
    onSuccess: () => {
      author.reset()
    },
    preserveState: false
  })
}

const toggleShowAuthor = (item) => {
  authorInfo.value = item
  showAuthorInfo.value = true
}

const page = usePage()

const ip_address = computed(() => page.props.ip_address)

const convertLink = (sched_id, auth_id) => {
  const protocol = window.location.protocol
  const pathname = (window.location.pathname).replace('/admin', '')
  return `${protocol}//${ip_address.value}:8000${pathname}?id=${sched_id}&author_id=${auth_id}`
}
</script>
<template>
  <v-app>
    <Head title="Edit Standing"></Head>
    <v-main>
      <VDialog v-model="showAuthorForm" width="500">
        <VCard :title="`Authorized Staff Form`" :subtitle="`Please provide informations`">
          <VDivider/>
          <VCardText>
            <VTextField v-model="author.name" label="Name" hide-details></VTextField>
            <VTextField v-model="author.position" label="Position" hide-details></VTextField>
          </VCardText>
          <VDivider/>
          <VCardActions>
            <VBtn @click="showAuthorForm = false">Close</VBtn>
            <VSpacer/>
            <VBtn @click="submitAuthor" variant="flat" color="blue" :loading="author.processing">Submit</VBtn>
          </VCardActions>
        </VCard>
      </VDialog>
      <VDialog v-model="showAuthorInfo" width="500">
        <VCard title="Updating Form" subtitle="Showing the authorized staff's information">
          <VCardText v-if="authorInfo">
            <!-- {{ authorInfo }} -->
            <VTextField v-model="authorInfo.name" label="Name" hide-details></VTextField>
            <VTextField v-model="authorInfo.position" label="Position"></VTextField>
            <h5>Shareable Link:</h5>
            
            <p>{{ convertLink(sched.id, authorInfo.id)  }}</p>
          </VCardText>
          <VDivider/>
          <VCardActions>
            <VBtn @click="showAuthorInfo = false">close</VBtn>
          </VCardActions>
        </VCard>
      </VDialog>
      <VContainer>
        <VRow>
          <VCol cols="4">
            <VCard title="Score Configuration" subtitle="It will configure the desired criteria for scoring">
              <v-radio-group
                v-model="scoring_type"
                inline
              >
                <v-radio
                  label="By Default"
                  value="default"
                ></v-radio>
                <v-radio
                  label="By Rubricks"
                  value="rubrick"
                ></v-radio>
              </v-radio-group>
              <VSelect v-model="rubrickId" v-if="scoring_type == 'rubrick'" :items="rubricks" item-title="title" item-value="id" label="Rubrick Type" hide-details></VSelect>
              <VCardActions>
                <VBtn block :disabled="!canProceedUpdateRubrick" size="small" color="blue" variant="flat" @click="updateRubrickType">Update</VBtn>
              </VCardActions>
            </VCard>
            <VCard class="mt-5" title="Authorized Staff" subtitle="A list of staff who can alter the scoring system">
              <VAlert v-if="!authors.length" type="warning" class="mx-2 my-2">No authorized staff included</VAlert>
              <VList v-else>
                <VListItem v-for="item in authors" @click="toggleShowAuthor(item)"
                  :key="item.id" :title="item.name" :subtitle="item.position" :prepend-avatar="item.avatar">
                  <template v-slot:append>
                    <VIcon v-if="item.enabled" color="blue-darken-2">mdi-eye-check</VIcon>
                    <VIcon v-else color="">mdi-minus-circle-outline</VIcon>
                  </template>
                </VListItem>
              </VList>
              <!-- {{ authors }} -->
              <VDivider></VDivider>
              <VCardActions>
                <VBtn size="small" color="blue" variant="flat" @click="toggleAuthorForm">Add staff</VBtn>
              </VCardActions>
            </VCard>
          </VCol>
          <VCol cols="8">
            <VCard title="Scoring Dashboard" subtitle="This will be the section on where should the updating of score">
              <VDivider/>
              <StandingVue :participants="participants" :category="category" :sched="sched" :rubricks="rubricks" />
            </VCard>
          </VCol>
        </VRow>
      </VContainer>
    </v-main>
  </v-app>
</template>