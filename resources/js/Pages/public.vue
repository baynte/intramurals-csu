<script setup>
import Public from '@/Layouts/Public.vue';
import { Head } from '@inertiajs/vue3'
import { onMounted, ref, watch, computed } from 'vue';
import moment from 'moment';
import EventComponentVue from '@/Components/Public/EventComponent.vue';
import CollegeComponentVue from '@/Components/Public/CollegeComponent.vue';
import OverallComponentVue from '@/Components/Public/OverallComponent.vue';
const bgPic = ref('/storage/events/1.jpeg');

const changePhoto = () => {
  const num = Math.floor(Math.random() * 8)
  bgPic.value = `/storage/events/${num}.jpeg`
}

setInterval(changePhoto, 3000);
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

const currentDate = ref(new Date());
const months = [
  'January', 'February', 'March', 'April', 'May', 'June',
  'July', 'August', 'September', 'October', 'November', 'December'
];

const currentMonth = computed(() => {
  const year = currentDate.value.getFullYear();
  const month = months[currentDate.value.getMonth()];
  return `${month} ${year}`;
});

const daysInMonth = computed(() => {
  const year = currentDate.value.getFullYear();
  const month = currentDate.value.getMonth();
  const numDays = new Date(year, month + 1, 0).getDate();
  return Array.from({ length: numDays }, (_, i) => i + 1);
});

const previousMonth = () => {
  const newDate = new Date(currentDate.value);
  newDate.setMonth(newDate.getMonth() - 1);
  currentDate.value = newDate;
};

const nextMonth = () => {
  const newDate = new Date(currentDate.value);
  newDate.setMonth(newDate.getMonth() + 1);
  currentDate.value = newDate;
};
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
        <div class="bg-p pb-3">
          <div class="bg-s" style="height: 28px"></div>
          <!-- <VSelect density="compact" label="Year" hide-details bg-color="white"></VSelect> -->
          <VCard class="mx-auto my-2" max-width="600">
            <VTabs v-model="selectedTab" grow>
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
      <div class="content d-none d-sm-block">
        <div class="header-b pb-2" style="position: relative;">
          <div class="absolute bg-cover bg-center" :style="{ 'background-image': `url('${bgPic}')` }"></div>
          <div class="absolute bg-white" style="opacity: 0.8"></div>
          <div style="position: relative; z-index: 10">
            <div style="width: 850px; margin: 0 auto;" class="d-flex justify-space-between align-center">
              <div>
                <p class="source-sans-3 ss3-900" style="font-size: 2.3rem; margin-bottom: 0">Caraga State University</p>
                <p class="source-sans-3 ss3-900 secondary" style="font-size: 3.8rem; margin: 0">INTRAMURALS</p>
              </div>
              <div>
                <VImg src="/storage/Caraga_State_University_1.png" height="230" width="300" alt="CARAGA State University Logo" class="university-logo" />
              </div>
            </div>
          </div>
        </div>
        <div class="bg-p pb-3">
          <div class="bg-s" style="height: 28px"></div>
          <div class="py-10" style="width: 850px; margin: 0 auto">
            <VRow>
              <VCol cols="6">
                <VCard class="flex-grow-1 d-flex mx-1">
                  <section class="d-flex flex-column text-center bg-p text-white">
                    <div class="flex-grow-1 py-4 px-3" style="min-width: 100px">
                      <p class="text-uppercase font-weight-bold">{{ moment().format('MMMM') }}</p>
                      <p style="font-size: 2.5rem">{{ moment().format('DD') }}</p>
                    </div>
                    <div class="bg-s py-2">TODAY</div>
                  </section>
                  <div style="overflow-x: auto" class="d-flex">
                    <section v-for="item in today_scheds" :key="item.id" variant="flat" style="" class="d-flex flex-column">
                      <div class="flex-grow-1">
                        <div class="px-3 py-3 text-center">
                          <h4>
                            {{ item.category?.name }}
                          </h4>
                          <!-- <h6>
                            {{ `${dateConvert(item)} - ${item.time} @ ${item.venue}` }}
                          </h6> -->
                          <h6 class="text-uppercase">
                            {{ item.class_type}}
                          </h6>
                        </div>
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
                      <div v-if="item.status != 'on-going'" class="text-center primary py-2 font-weight-bold">
                        {{ `${item.time}` }}
                      </div>
                      <div v-else class="bg-s py-2 text-white text-center">ON-GOING</div>
                    </section>
                  </div>
                </VCard>
              </VCol>
              <VCol cols="6">
                <VCard class="flex-grow-1 d-flex mx-1">
                  <section class="d-flex flex-column text-center bg-p text-white">
                    <div class="flex-grow-1 py-4 px-3" style="min-width: 100px">
                      <p class="text-uppercase font-weight-bold">{{ moment().add(1, 'day').format('MMMM') }}</p>
                      <p style="font-size: 2.5rem">{{ moment().add(1, 'day').format('DD') }}</p>
                    </div>
                    <div class="bg-s py-2 px-2">TOMORROW</div>
                  </section>
                  <div style="overflow-x: auto" class="d-flex">
                    <section v-for="item in tomorrow_scheds" :key="item.id" variant="flat" style="" class="d-flex flex-column">
                      <div class="flex-grow-1">
                        <div class="px-3 py-3 text-center">
                          <h4>
                            {{ item.category?.name }}
                          </h4>
                          <!-- <h6>
                            {{ `${dateConvert(item)} - ${item.time} @ ${item.venue}` }}
                          </h6> -->
                          <h6 class="text-uppercase">
                            {{ item.class_type}}
                          </h6>
                        </div>
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
                      <div v-if="item.status != 'on-going'" class="text-center primary py-2 font-weight-bold">
                        {{ `${item.time}` }}
                      </div>
                      <div v-else class="bg-s py-2 text-white text-center">ON-GOING</div>
                    </section>
                  </div>
                </VCard>
              </VCol>
            </VRow>
          </div>
        </div>
        <div class="mx-auto" style="width: 700px">
          <div class="calendar">
            <div class="header">
              <button @click="previousMonth" icon>
                <VIcon>mdi-chevron-left</VIcon>
              </button>
              <h2>{{ currentMonth }}</h2>
              <button @click="nextMonth">
                <VIcon>mdi-chevron-right</VIcon>
              </button>
            </div>
            <div class="days">
              <div v-for="(day, index) in daysInMonth" :key="index" class="day">{{ day }}</div>
            </div>
          </div>
        </div>
        <VCard class="mx-auto my-2" max-width="600">
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

.calendar {
  font-family: Arial, sans-serif;
}

.header {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.days {
  display: grid;
  grid-template-columns: repeat(7, 1fr);
  gap: 5px;
}

.day {
  border: 1px solid #ccc;
  padding: 5px;
  text-align: center;
}
</style>
