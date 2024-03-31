<script setup>
// import Public from '@/Layouts/Public.vue';
import { Head } from '@inertiajs/vue3'
import { ref } from 'vue'
import StandingVue from '@/Components/StandingComponent.vue'

const props = defineProps([
  'category', 'participants', 'sched'
])


const scoring_type = ref("default")
</script>
<template>
  <v-app>
    <Head title="Edit Standing"></Head>
    <v-main>
      <VContainer>
        <VRow>
          <VCol cols="4">
            <VCard title="Score Configuration" subtitle="It will configure the desired criteria for scoring">
              <v-radio-group
                v-model="scoring_type"
                inline
              >
                <v-radio
                  label="By Default"
                  value="default"
                ></v-radio>
                <v-radio
                  label="By Rubricks"
                  value="rubrick"
                ></v-radio>
              </v-radio-group>
              <VSelect v-if="scoring_type == 'rubrick'" label="Rubrick Type" hide-details></VSelect>
              <VCardActions>
                <VBtn block size="small" color="green" variant="flat">Update</VBtn>
              </VCardActions>
            </VCard>
            <VCard class="mt-5" title="Authorized Staff" subtitle="A list of staff who can alter the scoring system">
              <VList>
                <VListItem title="Pedro Penduko" subtitle="college instructor" prepend-avatar="https://ui-avatars.com/api/?name=pedro+penduko">
                  <template v-slot:append>
                    <VSwitch color="green"></VSwitch>
                  </template>
                </VListItem>
              </VList>
              <VDivider></VDivider>
              <VCardActions>
                <VBtn size="small" color="green" variant="flat">Create Link</VBtn>
              </VCardActions>
            </VCard>
          </VCol>
          <VCol cols="8">
            <VCard title="Scoring Dashboard" subtitle="This will be the section on where should the updating of score">
              <VDivider/>
              <StandingVue :participants="participants" :category="category" :sched="sched" />
            </VCard>
          </VCol>
        </VRow>
      </VContainer>
    </v-main>
  </v-app>
</template>