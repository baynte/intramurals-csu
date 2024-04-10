<script setup>
import Admin from '@/Layouts/Admin.vue';
import { Head, useForm } from '@inertiajs/vue3'
import { computed, ref } from 'vue';

const year_items = ["2024"]
const showForm = ref(false)
const items = ref([])
const processing = ref(false)

const getItems = () => {
  processing.value = true
  axios.get(route('admin.participant.index', {year: form.year}))
  .then(res => {
    items.value = res.data
    processing.value = false
  })
}

const form = useForm({
  id: null,
  name: "",
  year: "2024",
  avatar_path: "",
  bg_path: "",
  description: "To be described"
})

const submitForm = () => {
  if(form.id != null){
    form.put(route('admin.participant.update', {participant: form.id||1}),{
      onSuccess: () => {
        getItems()
        form.reset()
        showForm.value = false
      }
    })
  }else{
    form.post(route('admin.participant.store')
    , {
      onSuccess: () => {
        form.reset()
        getItems()
      }
    })
  }
}

const editForm = (item) => {
  form.id = item.id
  form.name = item.name
  form.description = item.description
  form.avatar_path = item?.avatar_path || ""
  form.bg_path = item?.bg_path || ""
  showForm.value = true
}

const removeItem = (category) => {
  items.value = items.value.filter(x => x.id != category)
  axios.delete(route('admin.participant.destroy', {category}))
}

const toggleForm = () => {
  form.reset()
  showForm.value = true
}

const avatarImgElem = ref(null)
const BgImgElem = ref(null)
const activateAvatarSelect = () => {
  avatarImgElem.value.click();
}
const activateBGSelect = () => {
  BgImgElem.value.click();
}

const handleAvatarChange = (event) => {
  const file = event.target.files[0];
  if (!file || !file.type.startsWith('image/')) {
    alert('Please select a valid image file.');
    return;
  }
  // Handle valid image file here
  console.log(file);
  const data = new FormData()
  data.append('file', file, file.name)
  data.append('id', form.id)
  form.avatar_path = URL.createObjectURL(file)

  const index = items.value.findIndex(x => x.id == form.id)
  if(index != -1){
    items.value[index].avatar_path = form.avatar_path
  }
  const settings = { headers: { 'content-type': 'multipart/form-data' } }
  axios.post(route('admin.participant.img.avatar'), data, settings)
};

const handleBgChange = (event) => {
  const file = event.target.files[0];
  if (!file || !file.type.startsWith('image/')) {
    alert('Please select a valid image file.');
    return;
  }
  // Handle valid image file here
  console.log(file);
  const data = new FormData()
  data.append('file', file, file.name)
  data.append('id', form.id)
  form.bg_path = URL.createObjectURL(file)
  
  const index = items.value.findIndex(x => x.id == form.id)
  if(index != -1){
    items.value[index].bg_path = form.bg_path
  }
  const settings = { headers: { 'content-type': 'multipart/form-data' } }
  axios.post(route('admin.participant.img.bg'), data, settings)
};

const q = ref("")
const filteredItems = computed(() => {
  if(!items.value.length) return []
  if(!q.value) return items.value
  return items.value.filter(function(obj){
    return obj.name.toLowerCase().includes(q.value.toLowerCase())
  })
})

getItems()
</script>
<template>
  <Admin>
    <Head title="Admin - Participants"></Head>
    <template #year-selection>
      <VSelect v-model="form.year" :items="year_items" label="Year" hide-details></VSelect>
    </template>
    <template #header-title>
      Participants
    </template>
    <template #header-items>
      <VBtn variant="tonal" @click="toggleForm">Create</VBtn>
    </template>
    <VDialog v-model="showForm" max-width="550" scrollable>
      <VForm @submit.prevent="submitForm">
        <input type="file" ref="avatarImgElem" accept="image/*" v-show="false" @change="handleAvatarChange">
        <input type="file" ref="BgImgElem" accept="image/*" v-show="false" @change="handleBgChange">
        <VCard title="Creation Form" subtitle="Please provide information.">
          <VDivider></VDivider>
          <VCardText>
            <VTextField v-model="form.name" label="Name" hide-details></VTextField>
            <VTextarea v-model="form.description" label="Description"/>
            <div v-if="form.id">
              <VRow justify="center" align="center">
                <VCol cols="4">
                  <VAvatar @click="activateAvatarSelect" size="80" style="margin: 0 25%">
                    <VImg :src="form.avatar_path"></VImg>
                  </VAvatar>
                </VCol>
                <VCol cols="8">
                  <VBtn @click="activateAvatarSelect" block color="blue" variant="flat" prepend-icon="mdi-camera" size="small">Change Avatar Image</VBtn>
                </VCol>
              </VRow>
              <VDivider class="my-3"></VDivider>
              <div style="position: relative;">
                <VImg height="150" cover :src="form.bg_path"></VImg>
                <VBtn @click="activateBGSelect" style="position: absolute; top: 10px; right: 10px" color="blue" variant="flat" icon size="small">
                  <VIcon>mdi-camera</VIcon>
                  <VTooltip activator="parent">
                    Change Background Image
                  </VTooltip>
                </VBtn>
              </div>
            </div>
          </VCardText>
          <VCardActions>
            <VBtn @click="showForm = false" variant="outlined">Close</VBtn>
            <VSpacer/>
            <VBtn color="green" type="submit" :loading="form.processing" variant="flat">Submit</VBtn>
          </VCardActions>
        </VCard>
      </VForm>
    </VDialog>
    <div class="px-3 py-4">
      <VTextField v-model="q" label="Search..." append-inner-icon="mdi-magnify"></VTextField>
      <VRow v-if="processing">
        <VCol cols="4">
          <VSkeletonLoader type="card"></VSkeletonLoader>
        </VCol>
        <VCol cols="4">
          <VSkeletonLoader type="card"></VSkeletonLoader>
        </VCol>
        <VCol cols="4">
          <VSkeletonLoader type="card"></VSkeletonLoader>
        </VCol>
      </VRow>
      <VRow v-else>
        <VCol v-for="item in filteredItems" :key="item.id" cols="4">
          <VCard :title="item.name" :subtitle="item.description">
            <template v-slot:append>
              <VAvatar @click="showImage()">
                <VImg :src="`${item.avatar_path}`"></VImg>
              </VAvatar>
            </template>
            <VImg height="150" cover :src="item.bg_path"></VImg>
            <VDivider></VDivider>
            <VCardActions>
              <VBtn prepend-icon="mdi-pencil" @click="editForm(item)" color="green" variant="text">Edit</VBtn>
              <v-menu transition="scale-transition">
                <template v-slot:activator="{ props }">
                  <VBtn v-bind="props" prepend-icon="mdi-delete-outline" color="red" variant="text">Remove</VBtn>
                </template>
                <v-card title="System Warning" subtitle="are you sure?">
                  <VCardActions>
                    <VBtn variant="outlined">No</VBtn>
                    <VSpacer></VSpacer>
                    <VBtn color="red" @click="removeItem(item.id)" variant="flat">Yes</VBtn>
                  </VCardActions>
                </v-card>
              </v-menu>
            </VCardActions>
          </VCard>
        </VCol>
      </VRow>
    </div>
  </Admin>
</template>