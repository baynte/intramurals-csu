<template>
  <div>
    <VSelect v-model="selectedCollege" label="Participant" density="compact" :items="colleges" item-title="name" item-value="id" hide-details></VSelect>
    <VDataTable :headers="headers" :items="computedItems" :loading="processing"></VDataTable>
    <!-- {{computedItems}} -->
  </div>  
</template>
<script setup>
import { computed, onBeforeMount, ref, watch } from "vue";
const props = defineProps(['categories'])
const headers = computed(() => {
  return [
    { title: 'Category', key: 'category' },
    { title: 'Rank', key: 'rank', align: 'left' },
    { title: 'Points', key: 'points', align: 'center' },
    // { title: 'Draws', key: 'draws' },
  ]
})
const items = ref([])
const processing = ref(false)

const computedItems = computed(() => {
  if(!items.value.length) return []
  return items.value.map(function(obj){
    return {
      category: obj.name,
      rank: 'TBA',
      points: obj.total_contributions
    }
  })
})

const colleges = ref([])
const selectedCollege = ref(null)

const getColleges = async() => {
  try {
    const year = 2024
    const res = await axios.get(route('get-colleges', { year }))
    colleges.value = res.data
    selectedCollege.value = colleges.value[0]?.id
  } catch (error) {
    
  }
}


const getItems = () => {
  processing.value = true
  axios.get(route('get-category-standing-per-college', {id: selectedCollege.value}))
  .then((res) => {
    items.value = res.data
    console.log(res.data)
  })
  .finally(() => {
    processing.value = false
  })
}

watch(selectedCollege, () => {
  getItems()
})

onBeforeMount(() => {
  getColleges()
  // getItems()
})
</script>