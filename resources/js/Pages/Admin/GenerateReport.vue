<script setup>
import { defineProps, computed, ref, watch, onBeforeMount } from 'vue'
import moment from 'moment'
import _ from 'lodash'
import OverallComponentVue from '@/Components/Public/OverallComponent.vue';
const props = defineProps({
  total_items: Array
})

const today = computed(() => {
    return moment().format('MMMM DD YYYY')
})

const countStat = (type) => {
    return props.total_items.filter(x => x.status == type).length
}

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
  const items_l = _.orderBy(items.value, ['total_points'], ['desc'])
  return items_l
  .map(function(obj, index){
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

<template>
  <v-container>
    <VRow align="stretch">
        <VCol cols="6">
            <VCard :title="today" subtitle="Total Activities" variant="outlined">
                <template v-slot:append>
                </template>
                <VCardText class="text-center">
                    <span style="font-size: 4.5rem; font-weight: bold;">{{ total_items.length }}</span>
                </VCardText>
            </VCard>
        </VCol>
        <VCol cols="6">
            <VCard title="PENDING" variant="outlined" class="mb-3">
                <template v-slot:append>
                    <span style="font-size: 1.5rem; font-weight: bold;">{{ countStat('pending') }}</span>
                </template>
            </VCard>
            <VCard title="ON-GOING" variant="outlined" class="mb-3">
                <template v-slot:append>
                    <span style="font-size: 1.5rem; font-weight: bold;">{{ countStat('on-going') }}</span>
                </template>
            </VCard>
            <VCard title="FINISHED" variant="outlined" class="mb-3">
                <template v-slot:append>
                    <span style="font-size: 1.5rem; font-weight: bold;">{{ countStat('finished') }}</span>
                </template>
            </VCard>
        </VCol>
        <VCol cols="12">
            <VList>
                <h1>Overall Standing</h1>
                <VListItem v-for="item in computedItems" :title="item.college" :subtitle="item.rank" class="mb-2 list">
                    <template v-slot:append>
                        <span style="font-size: 1.5rem; font-weight: bold;">{{ item.points }}</span>
                    </template>
                </VListItem>
            </VList>
        </VCol>
    </VRow>
    <!-- {{ computedItems }} -->
    <!-- <OverallComponentVue/> -->
    <!-- {{ items }} -->
  </v-container>
</template>
<style scoped>
    .list:nth-child(odd){
        background-color: rgb(241, 255, 250);
    }
</style>