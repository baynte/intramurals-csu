<template>
  <div>
    <VSelect v-model="selected" label="Category" hide-details :items="categories" item-value="id" item-title="name"></VSelect>
    <VDataTable :headers="headers" :items="computedItems" :loading="processing"></VDataTable>
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
const getItems = (indicateProgress) => {
  processing.value = indicateProgress
  if(selected.value){
    axios({
      method: 'GET',
      url: route('get-dt-events', {id: selected.value})
    }).then(res => {
      items.value = res.data.items
      participants.value = res.data.participants?.map(function(obj){
        return {...obj.info, status: obj.status, score: obj.score}
      })
    }).finally(() => {
      processing.value = false
    })
  }
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
  return colleges.value.map(function(obj){
    const p_c = participants.value.filter(x => x.id == obj.id)
    return { ...obj, 
      wins: p_c.filter(x => x.status == 'winner').length, 
      loss: p_c.filter(x => x.status == 'loss').length,
      draws: p_c.filter(x => x.status == 'draw').length,
      tba: p_c.filter(x => x.status == null).length,
      // wins: 1, 
      // loss: 1,
      // draws: 1,
    }
  })
})

watch(selected, () => {
  if(selected.value){
    getItems(true)
  }
})

onMounted(() => {
  getItems(true)
  setInterval(() => {
    getItems(false)
  }, 3000)
})
</script>