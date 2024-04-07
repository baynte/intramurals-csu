<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import { computed } from 'vue'

const form = useForm({
  email: "",
  password: ""
})
const submitForm = () => {
  form.post(route('login.authenticate'))
}

const canSubmit = computed(() => {
  const { email, password } = form
  return email && password ? false : true
})
</script>
<template>
  <v-app>
    <Head title="Login Page"></Head>
    <VMain>
      <VContainer>
        <VForm @submit.prevent="submitForm">
        <VCard variant="flat">
          <VImg height="200" src="/storage/Caraga_State_University_1.png"></VImg>
          <VCardText>
            <div class="mx-auto" style="width: 400px">
              <h1 class="text-center mb-5">CSU - INTRAMURALS</h1>
              <p v-if="form.errors?.email" class="text-center mb-3 text-red font-weight-bold">{{ form.errors.email }}</p>
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