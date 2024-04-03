<script setup>
import Admin from '@/Layouts/Admin.vue';
import RubrickComponent from '@/Components/RubrickComponent.vue'
import { Head, useForm } from '@inertiajs/vue3'
import { ref, computed } from 'vue';

const year_items = ["2024"]
const showForm = ref(false)
const toggleCreateForm = () => {
  showForm.value = true
}

const form = useForm({
  id: null,
  year: 2024,
  title: "",
  categories: [],
})
const category_t = ref("")
const pushCategoryToForm = () => {
  form.categories.push(category_t.value)
  category_t.value = ""
}

const canSubmit = computed(() => {
  const { title, categories } = form
  return title.length > 3 && categories.length
})

const category_u = ref("")
const categ_edit_index = ref(null)
const updateCateg = () => {
  form.categories[categ_edit_index.value] = category_u.value
  closeUpdate()
}
const initCateg = (index, item) => {
  category_u.value = item
  categ_edit_index.value = index
}

const closeUpdate = () => {
  category_u.value = ""
  categ_edit_index.value = null
}

const removeFromCateg = (index_p) => {
  form.categories = form.categories.filter((x, index) => index != index_p)
}

const submitForm = () => {
  form.post(route('admin.rubrick.store'), {
    onSuccess: () => {
      form.reset()
      getItems()
    }
  })
}

const itemsProcessing = ref(false)
const items = ref([])
const getItems = () => {
  itemsProcessing.value = true
  axios.get(route('admin.rubrick.index', { year: form.year }))
  .then(res => {
    items.value = res?.data || []
  }).catch(e => { console.log(e) })
  .finally(() => {
    itemsProcessing.value = false
  })
}

getItems()
</script>
<template>
  <Admin>
    <Head title="Admin - Rubricks"></Head>
    <template #year-selection>
      <VSelect v-model="form.year" :items="year_items" label="Year" hide-details></VSelect>
    </template>
    <template #header-title>
      Rubricks
    </template>
    <template #header-items>
      <VBtn @click="toggleCreateForm" variant="tonal">Create</VBtn>
    </template>
    <VDialog v-model="showForm" max-width="600" scrollable persistent>
      <VCard :title="`Form`" subtitle="please provide informations.">
        <VDivider/>
        <VCardText>
          <VTextField v-model="form.title" label="Title" hide-details/>
          <VTextField v-model="category_t" label="Add Key Insight" hide-details>
            <template v-slot:append-inner>
              <VBtn @click="pushCategoryToForm" icon size="small" color="green" variant="flat"
                :disabled="category_t.length < 3"  
              >
                <VIcon>mdi-plus</VIcon>
              </VBtn>
            </template>
          </VTextField>
          <VList v-if="form.categories.length">
            <VListSubheader>
              Added Key Insights
            </VListSubheader>
            <VListItem v-for="item, index in form.categories" :key="`categ-${index}`" class="list">
              <p v-if="categ_edit_index !== index">{{ item }}</p>
              <VTextField v-else  hide-details density="compact" v-model="category_u">
                <template v-slot:append>
                    <VBtn @click="updateCateg" icon variant="tonal" color="blue" size="small" class="ml-1">
                      <VIcon>mdi-check</VIcon>
                    </VBtn>
                </template>
                <template v-slot:append-inner>
                    <VBtn @click="closeUpdate" icon variant="text" size="small" class="ml-1">
                      <VIcon>mdi-close</VIcon>
                    </VBtn>
                </template>

              </VTextField>
              <template v-slot:append>
                <VBtn v-if="index !== categ_edit_index" 
                  @click="initCateg(index, item)"
                  icon variant="outlined" size="small">
                  <VIcon>mdi-pencil</VIcon>
                </VBtn>
                <VBtn v-if="index !== categ_edit_index" @click="removeFromCateg(index)" icon variant="outlined" size="small" class="ml-1">
                  <VIcon>mdi-delete</VIcon>
                </VBtn>
              </template>
            </VListItem>
          </VList>
        </VCardText>
        <VDivider></VDivider>
        <VCardActions>
          <VBtn @click="showForm = false">close</VBtn>
          <VSpacer/>
          <h3 v-if="form.recentlySuccessful">Success!</h3>
          <VSpacer/>
          <VBtn color="green" variant="flat" :disabled="!canSubmit" :loading="form.processing" @click="submitForm">Submit</VBtn>
        </VCardActions>
      </VCard>
    </VDialog>
    <VRow v-if="itemsProcessing" class="pa-3">
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
    <div v-else class="pa-3">
      <VRow v-if="items.length">
        <VCol v-for="item in items" :key="item.id" cols="4">
          <RubrickComponent :item="item"/>
        </VCol>
      </VRow>
      <VAlert v-else type="warning" variant="outlined">
        Rubrick Item Was Empty
      </VAlert>
    </div>
  </Admin>
</template>
<style scoped>
.list:nth-child(even){
  background: rgb(248, 254, 248);
}
</style>