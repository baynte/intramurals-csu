<script setup>
import Admin from '@/Layouts/Admin.vue';
import { Head, useForm } from '@inertiajs/vue3'
import { computed, onBeforeMount, ref, watch } from 'vue';
import moment from "moment";
import _ from 'lodash'

const year_items = [2024, 2025]
const class_selections = ['standing', 'semi-finals', 'finals']
const form = useForm({
  year: 2024,
  id: null,
  class_type: 'standing',
  time: "",
  venue: "Inside Campus",
  date_from: "",
  date_to: "",
  category_id: null,
  participants_id: [],
  description: "To be described",
})
const filter = useForm({
  categories: [],
  participants: [],
  date_to: "",
  date_from: "",
})

const showFilter = ref(false)
const showForm = ref(false)
const processingAssets = ref(false)
const categories = ref([])
const participants = ref([])

const getAssets = async() => {
  try {
    processingAssets.value = true
    const categ = await axios.get(route('admin.categ.items', { year: form.year }))
    const parti = await axios.get(route('admin.participants.items', { year: form.year }))

    categories.value = categ.data
    participants.value = parti.data
  } catch (error) {
    console.log(error)
  } finally{
    processingAssets.value = false
  }
}

const clearFilter = () => {
  filter.reset()
}

watch(() => filter.date_from, (newVal) => {
  if(!newVal){
    filter.date_to = ""
  }
})
watch(() => form.date_from, (newVal) => {
  if(!newVal){
    form.date_to = ""
  }
})
const filterDateFromWasChanged = () => {
  filter.date_to = filter.date_from
}

const formDateFromWasChanged = () => {
  form.date_to = form.date_from
}

const filterCounter = computed(() => {
  const { categories, participants, date_from } = filter
  let sum = 0
  if( categories.length ) { sum += 1 }
  if( participants.length ) { sum += 1 }
  if( date_from ) { sum += 1 }
  return sum
})

const toggleForm = () => {
  form.reset()
  showForm.value = true
}

const toggleEdit = (id) => {
  const item = items.value.find(x => x.id == id)
  form.id = item.id
  form.id = id
  form.time = item.time
  form.venue = item.venue
  form.date_from = item.date_from
  form.date_to = item.date_to
  form.category_id = item.category_id
  form.participants_id = item.participants.map(x => x.participant_id)
  form.description = item.description
  showForm.value = true
}

const items = ref([])
const itemsProcessing = ref(false)
const getItems = async() =>{
  try {
    itemsProcessing.value = true
    const res = await axios.get(route('admin.schedule.index', {year: form.year}))
    items.value = res.data
  } catch (error) {
    
  } finally {
    itemsProcessing.value = false

  }
}

const submitForm = () => {
  const {
    id,
    time,
    venue,
    year,
    date_from,
    class_type,
    date_to,
    category_id,
    participants_id,
    description,
  } = form
  form.processing = true
  if(form.id === null){
    axios.post(route('admin.schedule.store'),{
      time,
      year,
      venue,
      class_type,
      date_from,
      date_to,
      category_id,
      participants_id,
      description,
    }).then(res => {
      items.value.push(res.data.item)
    }).catch(e => {console.log(e)})
    .finally(() => {
      form.processing = false
      form.reset()
    })
  }else{
    axios.put(route('admin.schedule.update', { schedule: id }),{
      time,
      year,
      venue,
      date_from,
      class_type,
      date_to,
      category_id,
      participants_id,
      description,
    }).then(res => {
      const index = items.value.findIndex(x => x.id == id)
      items.value[index] = res.data.item
    }).catch(e => {console.log(e)})
    .finally(() => {
      form.processing = false
      // form.reset()
    })
  }
}

const computedItems = computed(() => {
  if(!items.value.length) return []
  return items.value.map(function(obj){
    const categ = categories.value.find(x => x.id == obj.category_id)
    return {
      id: obj.id,
      class_type: obj.class_type,
      category_name: categ?.name || 'Uknown',
      status: obj.status || 'Pending',
      date_from: moment(obj.date_from, 'YYYY-MM-DD').format('MMM DD, YYYY'),
      date_to: moment(obj.date_to, 'YYYY-MM-DD').format('MMM DD, YYYY'),
      time_venue: `${obj.venue} @ ${obj.time}`,
      participants: _.orderBy(obj.participants.map(function(p){
        const p_find = participants.value.find(x => x.id == p.participant_id)
        return {
          name: p_find?.name || 'Unkown',
          avatar_path: p_find?.avatar_path || 'Unkown',
          bg_path: p_find?.bg_path || 'Unkown',
          score: p?.score || 0,
          rubrick_score: p?.rubrick_score || 0,
        }
      }), ['score'], ['desc'])
    }
  })
})

const headers = computed(() => {
  return [
    { title: '', key: 'more', sortable: false },
    { title: 'Category', key: 'category_name' },
    { title: 'Class', key: 'class_type' },
    { title: 'Status', key: 'status', align: 'center' },
    { title: 'Participants', key: 'participants', align: 'center' },
    { title: 'Date From', key: 'date_from', align: 'center' },
    { title: 'Date To', key: 'date_to', align: 'center' },
    { title: 'Time & Venue', key: 'time_venue', align: 'center' },
    { title: '', key: 'edit_standing', align: 'center', sortable: false },
  ]
})

const statusColor = (status) => {
  if(status == 'pending') return 'gray'
  if(status == 'on-going') return 'green'
  if(status == 'finished') return 'blue'
}
const classColor = (status) => {
  if(status == 'finals') return 'blue'
  if(status == 'standing') return 'gray'
  if(status == 'finished') return 'blue'
}

const editStanding = (id) => {
  window.open(route('admin.link.schedule.standing', { id }), '_blank')
}

onBeforeMount(()=> {
  getAssets()
  getItems()
})

watch(() => form.year, () => {
  getItems()
})
</script>
<template>
  <Admin>
    <Head title="Admin Page"></Head>
    <template #year-selection>
      <VSelect v-model="form.year" :items="year_items" label="Year" hide-details></VSelect>
    </template>
    <template #header-title>
      Schedules
    </template>
    <template #header-items>
      <VBtn variant="tonal" @click="toggleForm">Create Schedule</VBtn>
      <!-- <VBtn variant="tonal" @click="showFilter = true" prepend-icon="mdi-filter" class="ml-2">{{filterCounter ? `${filterCounter} - Filtered` : "Filter"}}</VBtn> -->
    </template>
    <VDialog v-model="showFilter" max-width="500">
      <VCard title="Filter Items" subtitle="Here we can customize items to be seen in the screen">
        <VDivider/>
        <div class="d-flex mt-2">
          <VTextField v-model="filter.date_from" @change="filterDateFromWasChanged" type="date" label="Date From" hide-details clearable></VTextField>
          <VTextField v-model="filter.date_to" :min="filter.date_from" :disabled="filter.date_from ? false : true" type="date" label="Date To" hide-details clearable></VTextField>
        </div>
        <VSelect v-model="filter.categories" label="Categories" :items="categories" item-title="name" chips closable-chips item-color="green" item-value="id" multiple hide-details clearable></VSelect>
        <VSelect v-model="filter.participants" label="Participants" :items="participants" item-title="name" chips closable-chips item-value="id" multiple hide-details clearable></VSelect>
        <VDivider/>
        <VCardActions>
          <VBtn variant="outlined" @click="showFilter = false">close</VBtn>
          <VSpacer/>
          <v-menu transition="scale-transition" location="end">
            <template v-slot:activator="{ props }">
              <VBtn v-bind="props" :disabled="!filterCounter" prepend-icon="mdi-close" color="warning" variant="flat">Clear Filters</VBtn>
            </template>
            <v-card title="System Warning" subtitle="are you sure?">
              <VCardActions>
                <VBtn variant="outlined">No</VBtn>
                <VSpacer></VSpacer>
                <VBtn color="warning" @click="clearFilter" variant="flat">Yes</VBtn>
              </VCardActions>
            </v-card>
          </v-menu>
        </VCardActions>
      </VCard>
    </VDialog>
    <VDialog v-model="showForm" max-width="500" scrollable>
      <VCard :title="`${form.id ? '(Edit) ' : ''}Schedule Form`" subtitle="Please fill the entries">
        <VDivider/>
        <VCardText>
          <VSelect v-model="form.class_type" :items="class_selections" label="Class Type" hide-details></VSelect>
          <div class="d-flex">
            <VTextField v-model="form.date_from" @change="formDateFromWasChanged" type="date" label="Date From" hide-details clearable></VTextField>
            <VTextField v-model="form.date_to" :min="form.date_from" :disabled="form.date_from ? false : true" 
              type="date" label="Date To" hide-details></VTextField>
          </div>
          <div class="d-flex mb-5">
            <VTextField v-model="form.venue" label="Venue" hide-details clearable></VTextField>
            <VTextField v-model="form.time" label="Time" placeholder="HH:MM AM/PM" hide-details clearable></VTextField>
          </div>
          <VAutocomplete v-model="form.category_id" :items="categories" item-title="name" item-value="id" label="Category Type" hide-details></VAutocomplete>
          <VSelect v-model="form.participants_id" :items="participants" item-title="name" item-value="id" label="Participants" hide-details multiple></VSelect>
          <VTextarea v-model="form.description" label="Description"></VTextarea>
        </VCardText>
        <VDivider></VDivider>
        <VCardActions>
          <VBtn @click="showForm = false">Close</VBtn>
          <VSpacer/>
          <VBtn @click="submitForm" variant="flat" color="blue" :loading="form.processing">Submit</VBtn>
        </VCardActions>
      </VCard>
    </VDialog>
    <div>
      <VTextField prepend-inner-icon="mdi-magnify" label="Search. . ." clearable></VTextField>
        <VSkeletonLoader v-if="itemsProcessing" type="table"></VSkeletonLoader>
        <div v-else>
          <VDataTable :items="computedItems" :headers="headers">
            <template v-slot:[`item.more`]="{ item }">
              <div class="d-flex">
                <VBtn @click="toggleEdit(item.id)" icon color="yellow-darken-3" variant="text" size="sm">
                  <VIcon>mdi-pencil</VIcon>
                </VBtn>
                <VBtn icon color="red-darken-3" variant="text" size="sm">
                  <VIcon>mdi-delete</VIcon>
                </VBtn>
              </div>
            </template>
            <template v-slot:[`item.edit_standing`]="{ item }">
              <VBtn @click="editStanding(item.id)" variant="flat" color="blue" size="small">Update Score</VBtn>
            </template>
            <template v-slot:[`item.status`]="{ item }">
              <VChip :color="statusColor(item.status)" size="small" class="text-uppercase">{{ item.status }}</VChip>
            </template>
            <template v-slot:[`item.class_type`]="{ item }">
              <VChip :color="classColor(item.class_type)" variant="flat" size="small" class="text-uppercase">{{ item.class_type }}</VChip>
            </template>
            <template v-slot:[`item.participants`]="{ item }">
              <VMenu location="end">
                <template v-slot:activator="{ props }">
                  <div v-bind="props" class="cursor-pointer">
                    <div v-if="item.participants.length == 2" class="d-flex justify-center align-center">
                      <VAvatar>
                        <VImg :src="item.participants[0].avatar_path"/>
                        <VTooltip activator="parent" location="start">
                          {{ item.participants[0]?.name }}
                        </VTooltip>
                      </VAvatar>
                      <h3 class="mx-2">VS</h3>
                      <VAvatar>
                        <VImg :src="item.participants[1].avatar_path"/>
                        <VTooltip activator="parent" location="end">
                          {{ item.participants[1]?.name }}
                        </VTooltip>
                      </VAvatar>
                    </div>
                    <div v-else class="d-flex">
                      <VAvatar v-for="p in item.participants" :key="p.id">
                        <VImg :src="p.avatar_path"/>
                      </VAvatar>
                    </div>
                  </div>
                </template>
                <VCard title="Standing" subtitle="Current score standing">
                  <VList>
                    <VListItem
                      v-for="p in item.participants" :key="`${p.id}-standing`"
                      :title="p.name"
                      :prepend-avatar="p.avatar_path"
                    >
                      <VListItemAction>
                        <h3>{{ p.rubrick_score }}</h3>
                      </VListItemAction>
                    </VListItem>
                  </VList>
                </VCard>
              </VMenu>
            </template>
          </VDataTable>
        </div>
    </div>
  </Admin>
</template>