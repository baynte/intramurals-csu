<script setup>
import Admin from '@/Layouts/Admin.vue';
import { Head, useForm } from '@inertiajs/vue3'
import { ref } from 'vue';

const year_items = ["2024"]
const showForm = ref(false)
const items = ref([])
const processing = ref(false)

const getItems = () => {
  processing.value = true
  axios.get(route('admin.category.index'))
  .then(res => {
    items.value = res.data
    processing.value = false
  })
}

const form = useForm({
  id: null,
  name: "",
  year: "2024",
  description: "To be described"
})

const submitForm = () => {
  if(form.id != null){
    form.put(route('admin.category.update', {category: form.id||1}),{
      onSuccess: () => {
        getItems()
        form.reset()
        showForm.value = false      }
    })
  }else{
    form.post(route('admin.category.store')
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
  showForm.value = true
}

const removeItem = (category) => {
  items.value = items.value.filter(x => x.id != category)
  axios.delete(route('admin.category.destroy', {category}))
}

const toggleForm = () => {
  form.reset()
  showForm.value = true
}

getItems()
</script>
<template>
  <Admin>
    <Head title="Admin - Categories"></Head>
    <template #year-selection>
      <VSelect v-model="form.year" :items="year_items" label="Year" hide-details></VSelect>
    </template>
    <template #header-title>
      Categories
    </template>
    <template #header-items>
      <VBtn variant="tonal" @click="toggleForm">Create</VBtn>
    </template>
    <VDialog v-model="showForm" max-width="500">
      <VForm @submit.prevent="submitForm">
        <VCard title="Creation Form" subtitle="Please provide information.">
          <VDivider></VDivider>
          <VCardText>
            <VTextField v-model="form.name" label="Name" hide-details></VTextField>
            <VTextarea v-model="form.description" label="Description"/>
          </VCardText>
          <VDivider></VDivider>
          <VCardActions>
            <VBtn @click="showForm = false" variant="outlined">Close</VBtn>
            <VSpacer/>
            <VBtn color="green" type="submit" :loading="form.processing" variant="flat">Submit</VBtn>
          </VCardActions>
        </VCard>
      </VForm>
    </VDialog>
    <div class="px-3 py-4">
      <VTextField label="Search..." append-inner-icon="mdi-magnify"></VTextField>
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
        <VCol v-for="item in items" :key="item.id" cols="4">
          <VCard :title="item.name" :subtitle="item.description">
            <VDivider></VDivider>
            <VCardActions>
              <VBtn prepend-icon="mdi-pencil" @click="editForm(item)" color="warning" variant="flat">Edit</VBtn>
              <v-menu transition="scale-transition">
                <template v-slot:activator="{ props }">
                  <VBtn v-bind="props" prepend-icon="mdi-delete-outline" color="red" variant="flat">Remove</VBtn>
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