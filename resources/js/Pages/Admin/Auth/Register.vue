<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import { computed } from 'vue'

const form = useForm({
  email: "",
  name: "",
  password: ""
})
const submitForm = () => {
  form.post(route('login.credRegister'))
}

const canSubmit = computed(() => {
  const { name, email, password } = form
  return name && email && password ? false : true
})
</script>
<template>
  <v-app>
    <Head title="Register Page"></Head>
    <VMain>
      <VContainer>
        <VForm @submit.prevent="submitForm">
        <VCard variant="flat">
          <VImg height="200" src="/storage/Caraga_State_University_1.png"></VImg>
          <VCardText>
            <div class="mx-auto" style="width: 400px">
              <h1 class="text-center mb-5">REGISTER ACCOUNT</h1>
              <VTextField label="Name" v-model="form.name" hide-details></VTextField>
              <VTextField label="Email" type="email" v-model="form.email" hide-details></VTextField>
              <VTextField label="Password" type="password" v-model="form.password"></VTextField>
              <VBtn variant="flat" :disabled="canSubmit" block type="submit" color="green">Submit</VBtn>
            </div>
          </VCardText>
        </VCard>
        </VForm>
      </VContainer>
    </VMain>
  </v-app>
</template>