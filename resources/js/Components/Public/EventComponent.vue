<template>
  <div>
    <VSelect v-model="selected" label="Category" hide-details :items="categories" item-value="id" item-title="name"></VSelect>
    <VDataTable :headers="headers" :items="computedItems" :loading="processing"></VDataTable>
    <!-- {{ colleges }} -->
    {{ items }}

    <!-- {{ participants }} -->
  </div>  
</template>
<script setup>
import { computed, onMounted, ref, watch } from "vue";
import _ from 'lodash'
const props = defineProps(['categories'])
const selected = ref(null)
const processing = ref(false)

const participants = ref([])
const items = ref([])

selected.value = props.categories[0]?.id || null
const getItems = () => {
  processing.value = true
  axios({
    method: 'GET',
    url: route('get-dt-events', {id: selected.value})
  }).then(res => {
    items.value = res.data.items
    participants.value = res.data.participants
  }).finally(() => {
    processing.value = false
  })
}

const headers = computed(() => {
  return [
    { title: 'College', key: 'name' },
    { title: 'Wins', key: 'wins', align: 'center' },
    { title: 'Loss', key: 'loss', align: 'center' },
    { title: 'Draws', key: 'draws', align: 'center' },
    { title: 'TBA', key: 'tba', align: 'center' },
  ]
})


const colleges = computed(() => {
  if(!participants.value.length) return []
  return _.orderBy(_.uniqBy(participants.value, 'id'), ['name'], ['asc'])
})

const computedItems = computed(() => {
  const c = colleges.value.map(function(obj){
    return { id: obj.id, wins: [], loss: [], draw: [], tba: [] }
  })
  
  items.value.forEach(function(obj){
    if(obj.status != 'finished'){
        
    }
  })

})

watch(selected, () => {
  if(selected.value){
    getItems()
  }
})

onMounted(() => {
  getItems()
})
</script>