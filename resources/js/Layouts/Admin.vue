<script setup>
  import { router } from "@inertiajs/vue3";
  import { ref } from "vue"

  const links = [
    { title: "Participants", active: route().current('admin.participants'), url: route('admin.participants'), icon: "mdi-account-group-outline" },
    { title: "Categories", active: route().current('admin.categories'), url: route('admin.categories'), icon: "mdi-shape-outline" },
    { title: "Rubricks", active: route().current('admin.rubricks'), url: route('admin.rubricks'), icon: "mdi-puzzle-outline" },
  ]

  const showNavBar = ref(true)

  const toggleNavBar = () => {
    showNavBar.value = !showNavBar.value
  }

  const visit = (link) => {
    router.visit(link, {
      method: 'get'
    })
  }

  const toggleLogoutPrompt = ref(false)

  const logout = () => {
    router.visit(route("logout"), {
      method: "post",
    });
  };
</script>
<template>
   <v-app>
    <v-dialog v-model="toggleLogoutPrompt" width="auto">
      <v-card>
        <v-toolbar color="warning">
          <v-toolbar-title>System Alert</v-toolbar-title>
        </v-toolbar>
        <v-card-text> You are about to logout the session, Proceed? </v-card-text>
        <v-card-actions>
          <v-btn @click="toggleLogoutPrompt = false">Cancel</v-btn>
          <v-spacer></v-spacer>
          <v-btn @click="logout" color="warning" variant="flat">Proceed</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
    <v-navigation-drawer v-model="showNavBar">
      <v-list-item class="py-3" title="CSU - Intramurals" subtitle="Admin Page" prepend-avatar="/storage/Caraga_State_University_1.png"></v-list-item>
      <slot name="year-selection"></slot>

      <VListSubheader class="px-2">Navigation Links</VListSubheader>
      <VListItem link title="Schedules" @click="visit(route('admin.home'))" color="green" :active="route().current('admin.home')" :prepend-icon="'mdi-invoice-text-clock-outline'"></VListItem>
      <v-divider></v-divider>
      <VListSubheader class="px-2">Configurations Links</VListSubheader>
      <VListItem v-for="link in links" :key="link.tite" link
        @click="visit(link.url)"
        :title="link.title" color="green" :active="link.active" :prepend-icon="link.icon"></VListItem>
      <template v-slot:append>
        <div class="pa-2">
          <v-btn
            id="user-logout"
            @click="toggleLogoutPrompt = true"
            block
            variant="flat"
            size="small"
            color="grey"
            >Logout</v-btn
          >
        </div>
      </template>
    </v-navigation-drawer>
    <VAppBar flat color="green">
      <template v-slot:prepend>
          <v-app-bar-nav-icon @click="toggleNavBar"></v-app-bar-nav-icon>
        </template>
      <VAppBarTitle>
        <slot name="header-title"></slot>
      </VAppBarTitle>
      <VSpacer></VSpacer>
      <slot name="header-items"></slot>
    </VAppBar>
    <v-main>
      <slot></slot>
    </v-main>
  </v-app>
</template>