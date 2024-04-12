<script setup>
import Public from '@/Layouts/Public.vue';
import { Head } from '@inertiajs/vue3'
import { onMounted, ref, watch } from 'vue';
import moment from 'moment';
import EventComponentVue from '@/Components/Public/EventComponent.vue';
import CollegeComponentVue from '@/Components/Public/CollegeComponent.vue';
import OverallComponentVue from '@/Components/Public/OverallComponent.vue';
const bgPic = ref('/storage/events/1.jpeg');
const props = defineProps([
  'today_scheds', 'tomorrow_scheds', 'categories'
]);

const dateConvert = ({date_from, date_to}) => {
  if(date_from == date_to){
    return moment(date_from, 'YYYY-MM-DD').format('MMMM DD, YYYY')
  }
}

const selectedDatePreview = ref(null)

selectedDatePreview.value = moment().format('YYYY-MM-DD')

const datePreviewItems = ref([])
const getSelectedDatePreview = () => {
  axios.get(route('get-selected-date', {
    date: selectedDatePreview.value
  }))
  .then(res => {
    datePreviewItems.value = res.data
  })
}

const statusColor = (status) => {
  switch(status){
    case 'on-going': return 'green';
    case 'finished': return 'primary';
    // case 'on-going': return 'green';
  }
}

watch(datePreviewItems, () => {
  getSelectedDatePreview()
})

const selectedTab = ref(0)
onMounted(() => {
  getSelectedDatePreview()
})
</script>
<template>
  <Public>
    <Head title="Public Page"></Head>
    <div class="intramurals-banner">
      <!-- Mobile View -->
      <div class="content d-block d-sm-none">
        <div class="header-b pb-2" style="position: relative;">
          <div class="absolute bg-cover bg-center" :style="{ 'background-image': `url('${bgPic}')` }"></div>
          <div class="absolute bg-white" style="opacity: 0.8"></div>
          <div style="position: relative; z-index: 10">
            <VImg src="/storage/Caraga_State_University_1.png" height="200" alt="CARAGA State University Logo" class="university-logo" />
            <h1 class="text-center source-sans-3 ss3-900 secondary">CSU - INTRAMURALS</h1>
          </div>
        </div>
        <div class="bg-p">
          <div class="bg-s" style="height: 28px"></div>
          <v-carousel
            :continuous="false"
            :show-arrows="true"
            delimiter-icon="mdi-square"
            
            hide-delimiter-background
          >
            <v-carousel-item>
              <div class="px-3 py-2 source-sans-3 text-uppercase text-white">
                <h3>Today's Event</h3>
                <div v-if="today_scheds.length" style="max-height: calc(100vh - 400px); overflow-y: auto">
                  <VCard v-for="item in today_scheds" :key="item.id" class="mb-2">
                    <div style="position: relative" class="d-flex justify-center align-center py-2">
                      <div v-if="item.participants_info.length == 2" class="d-flex justify-center align-center">
                        <VAvatar>
                          <VImg :src="item.participants_info[0].avatar_path"/>
                          <VTooltip activator="parent" location="start">
                            {{ item.participants_info[0]?.name }}
                          </VTooltip>
                        </VAvatar>
                        <h3 class="mx-2">VS</h3>
                        <VAvatar>
                          <VImg :src="item.participants_info[1].avatar_path"/>
                          <VTooltip activator="parent" location="end">
                            {{ item.participants_info[1]?.name }}
                          </VTooltip>
                        </VAvatar>
                      </div>
                      <div v-else class="d-flex">
                        <VAvatar color="green" v-for="p in item.participants_info" :key="p.id" class="mx-1">
                          <VImg :src="p.avatar_path"/>
                          <VTooltip activator="parent" location="left">
                            {{ p?.name }}
                          </VTooltip>
                        </VAvatar>
                      </div>
                    </div>
                    <VDivider/>
                    <div class="px-3 d-flex justify-space-between align-center py-3">
                      <div>
                        <h4>
                          {{ item.category?.name }}
                        </h4>
                        <h6>
                          {{ `${dateConvert(item)} - ${item.time} @ ${item.venue}` }}
                        </h6>
                      </div>
                      <VChip :color="statusColor(item.status)" class="mx-3 mb-1 text-gray-darken-1">{{ item.status }}</VChip>
                    </div>
                  </VCard>
                </div>
                <VAlert v-else type="info" color="white" class="mt-30">No Event available to show</VAlert>
              </div>
            </v-carousel-item>
            <v-carousel-item>
              <v-sheet
                :color="`transparent`"
                height="100%"
                tile
              >
                <div class="px-3 py-2 source-sans-3 text-uppercase text-white">
                  <h3>Tomorrow's Event</h3>
                  <div v-if="tomorrow_scheds.length">
                    <VCard v-for="item in tomorrow_scheds" :key="item.id" style="max-height: calc(100vh - 400px); overflow-y: auto">
                    <div style="position: relative" class="d-flex justify-center align-center py-2">
                      <div v-if="item.participants_info.length == 2" class="d-flex justify-center align-center">
                        <VAvatar>
                          <VImg :src="item.participants_info[0].avatar_path"/>
                          <VTooltip activator="parent" location="start">
                            {{ item.participants_info[0]?.name }}
                          </VTooltip>
                        </VAvatar>
                        <h3 class="mx-2">VS</h3>
                        <VAvatar>
                          <VImg :src="item.participants_info[1].avatar_path"/>
                          <VTooltip activator="parent" location="end">
                            {{ item.participants_info[1]?.name }}
                          </VTooltip>
                        </VAvatar>
                      </div>
                      <div v-else class="d-flex">
                        <VAvatar color="green" v-for="p in item.participants_info" :key="p.id" class="mx-1">
                          <VImg :src="p.avatar_path"/>
                          <VTooltip activator="parent" location="left">
                            {{ p?.name }}
                          </VTooltip>
                        </VAvatar>
                      </div>
                    </div>
                    <VDivider/>
                    <div class="px-3 d-flex justify-space-between align-center py-3">
                      <div>
                        <h4>
                          {{ item.category?.name }}
                        </h4>
                        <h6>
                          {{ `${dateConvert(item)} - ${item.time} @ ${item.venue}` }}
                        </h6>
                      </div>
                      <VChip :color="statusColor(item.status)" class="mx-3 mb-1 text-gray-darken-1">{{ item.status }}</VChip>
                    </div>
                  </VCard>
                  </div>
                  <VAlert v-else type="info" color="white" class="mt-30">No Event available to show</VAlert>
                </div>
              </v-sheet>
            </v-carousel-item>
          </v-carousel>
        </div>
        <VTextField v-model="selectedDatePreview" type="date" label="Event Preview Date" hide-details/>
        <div v-if="datePreviewItems.length" class="px-3 py-3 bg-grey-lighten-2" style="max-height: calc(100vh - 550px); overflow-y: auto">
          <VCard v-for="item in datePreviewItems" :key="item.id" class="mb-2">
            <div style="position: relative" class="d-flex justify-center align-center py-2">
              <div v-if="item.participants_info.length == 2" class="d-flex justify-center align-center">
                <VAvatar>
                  <VImg :src="item.participants_info[0].avatar_path"/>
                  <VTooltip activator="parent" location="start">
                    {{ item.participants_info[0]?.name }}
                  </VTooltip>
                </VAvatar>
                <h3 class="mx-2">VS</h3>
                <VAvatar>
                  <VImg :src="item.participants_info[1].avatar_path"/>
                  <VTooltip activator="parent" location="end">
                    {{ item.participants_info[1]?.name }}
                  </VTooltip>
                </VAvatar>
              </div>
              <div v-else class="d-flex">
                <VAvatar color="green" v-for="p in item.participants_info" :key="p.id" class="mx-1">
                  <VImg :src="p.avatar_path"/>
                  <VTooltip activator="parent" location="left">
                    {{ p?.name }}
                  </VTooltip>
                </VAvatar>
              </div>
            </div>
            <VDivider/>
            <div class="px-3 d-flex justify-space-between align-center py-3">
              <div>
                <h4>
                  {{ item.category?.name }}
                </h4>
                <h6>
                  {{ `${dateConvert(item)} - ${item.time} @ ${item.venue}` }}
                </h6>
              </div>
              <VChip :color="statusColor(item.status)" class="mx-3 mb-1 text-gray-darken-1 text-uppercase">{{ item.status }}</VChip>
            </div>
          </VCard>
        </div>
        <VAlert v-else type="info" color="white" class="mt-30">No Event available to show</VAlert>
      </div>
      <div class="content d-none d-sm-block">
        <VImg src="/storage/Caraga_State_University_1.png" height="200" alt="CARAGA State University Logo" class="university-logo" />
        <h1 class="text-center source-sans-3 ss3-900">INTRAMURALS</h1>
        <h2 class="text-center">CARAGA STATE UNIVERSITY</h2>
      </div>
      <div class="bg-p pb-3">
        <div class="bg-s" style="height: 28px"></div>
        <!-- <VSelect density="compact" label="Year" hide-details bg-color="white"></VSelect> -->
        <VCard class="mx-2 my-2">
          <VTabs v-model="selectedTab">
            <VTab>Events</VTab>
            <VTab>Colleges</VTab>
            <VTab>Overall</VTab>
          </VTabs>
          <VDivider/>
          <VWindow v-model="selectedTab">
            <VWindowItem>
              <EventComponentVue :categories="categories"/>
            </VWindowItem>
            <VWindowItem>
              <CollegeComponentVue :categories="categories"/>
            </VWindowItem>
            <VWindowItem>
              <OverallComponentVue/>
            </VWindowItem>
          </VWindow>
        </VCard>
      </div>
    </div>
  </Public>
</template>

<style scoped>
.absolute {
    position: absolute;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
}

.bg-cover {
    background-size: cover;
}

.bg-center {
    background-position: center;
}
</style>
