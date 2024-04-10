<template>
  <div>
    <VSelect v-model="selected" label="Category" hide-details :items="categories" item-value="id" item-title="name"></VSelect>
    <VDataTable :headers="headers" :items="computedItems"></VDataTable>
    {{ colleges }}
  </div>  
</template>
<script setup>
import { computed, ref, watch } from "vue";
import _ from 'lodash'
const props = defineProps(['categories'])
const selected = ref(null)
const processing = ref(false)
const items = ref([])

selected.value = props.categories[0]?.id || null
const getItems = () => {
  processing.value = true
  axios({
    method: 'GET',
    url: route('get-dt-events', {id: selected.value})
  }).then(res => {
    items.value = res.data
  }).finally(() => {
    processing.value = false
  })
}

const headers = computed(() => {
  return [
    { title: 'College', key: 'name' },
    { title: 'Wins', key: 'wins' },
    { title: 'Loss', key: 'loss' },
    { title: 'Draws', key: 'draws' },
  ]
})


const colleges = computed(() => {
  if(!items.value.length) return []
  return _.orderBy(_.uniqBy(items.value.map(x => x.info), 'id'), ['name'], ['asc'])
})

const computedItems = computed(() => {
  return colleges.value
})

watch(selected, () => {
  if(selected.value){
    getItems()
  }
})
getItems()
</script>