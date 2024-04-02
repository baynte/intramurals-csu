<script setup>
import Admin from '@/Layouts/Admin.vue';
import { Head, useForm } from '@inertiajs/vue3'
import { ref } from 'vue';

const showForm = ref(false)
const toggleCreateForm = () => {
  showForm.value = true
}

const form = useForm({
  id: null,
  title: "",
  categories: [],
})
const category_t = ref("")
const pushCategoryToForm = () => {
  form.categories.push(category_t.value)
  category_t.value = ""
}

</script>
<template>
  <Admin>
    <Head title="Admin - Rubricks"></Head>
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
          <VTextField v-model="category_t" label="Add Category" hide-details>
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
              Added Categories
            </VListSubheader>
            <VListItem v-for="item, index in form.categories" :key="`categ-${index}`" class="list">
              <p>{{ item }}</p>
              <template v-slot:append>
                <VBtn icon variant="outlined" size="small">
                  <VIcon>mdi-pencil</VIcon>
                </VBtn>
                <VBtn icon variant="outlined" size="small" class="ml-1">
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
          <VBtn color="green" variant="flat">Submit</VBtn>
        </VCardActions>
      </VCard>
    </VDialog>
    <div>
    </div>
  </Admin>
</template>
<style scoped>
.list:nth-child(even){
  background: rgb(248, 254, 248);
}
</style>