<template>
  <div>
    <VSelect v-model="selectedCollege" label="Participant" density="compact" :items="colleges" item-title="name" item-value="id" hide-details></VSelect>
    <VDataTable :headers="headers"></VDataTable>
  </div>  
</template>
<script setup>
import { computed, onBeforeMount, ref } from "vue";
const props = defineProps(['categories'])
const headers = computed(() => {
  return [
    { title: 'Category', key: 'college' },
    { title: 'Rank', key: 'wins' },
    { title: 'Points', key: 'loss' },
    // { title: 'Draws', key: 'draws' },
  ]
})

const computedItems = computed(() => {

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
  axios.get(route('get-category-standing-per-college', {id: selectedCollege.value}))
  .then((res) => {
    console.log(res.data)
  })
}

onBeforeMount(() => {
  getColleges()
  getItems()
})
</script>