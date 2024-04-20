<template>
  <div>
    <VDataTable :headers="headers" :items="computedItems" :loading="processing"></VDataTable>
    <!-- {{computedItems}} -->
    <!-- {{ items }} -->
  </div>  
</template>
<script setup>
import { computed, onBeforeMount, ref, watch } from "vue";
const props = defineProps(['categories'])
const headers = computed(() => {
  return [
    { title: 'College', key: 'college' },
    { title: 'Rank', key: 'rank', align: 'left' },
    { title: 'Points', key: 'points', align: 'center' },
    // { title: 'Draws', key: 'draws' },
  ]
})
const items = ref([])
const processing = ref(false)

const computedItems = computed(() => {
  if(!items.value.length) return []
  return items.value.map(function(obj, index){
    let rank = 'TBA'
    switch(index + 1){
      case 1: rank = 'CHAMPION'; break;
      case 2: rank = '1st'; break;
      case 3: rank = '2nd'; break;
      case 4: rank = '3rd'; break;
      case 5: rank = '4th'; break;
    }
    return {
      college: obj.name,
      rank: obj.total_points ? rank : 'Unranked',
      points: obj.total_points
    }
  })
})


const getItems = (process) => {
  processing.value = process
  axios.get(route('get-overall'))
  .then((res) => {
    items.value = res.data
    // console.log(res.data)
  })
  .finally(() => {
    processing.value = false
  })
}

onBeforeMount(() => {
  getItems(true)
  setInterval(() => {
    getItems(false)
  }, 4000)
})
</script>