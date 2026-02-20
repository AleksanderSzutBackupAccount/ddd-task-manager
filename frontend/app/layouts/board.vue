<script lang="ts" setup>
import { useAuthStore } from '~/store/auth'

definePageMeta({
  middleware: ['projects', 'protected']
})

const authStore = useAuthStore()
const { user } = storeToRefs(authStore)

const showBanner = ref(true)

onMounted(() => {
  setTimeout(() => {
    showBanner.value = false
  }, 15000)
})
</script>

<template>
  <div>
    <UHeader :toggle="false">
      <template #left>
        <AppLogo class="w-auto h-6 shrink-0" />
      </template>
      <template #right>
        <UButton
          icon="i-material-symbols:logout-rounded"
          @click="authStore.signOut()"
        >
          Logout
        </UButton>
      </template>
      <template #bottom>
        <UBanner
          v-show="showBanner"
          color="secondary"
          icon="i-material-symbols:waving-hand"
          :title="`Welcome ${user?.name}`"
        />
      </template>
    </UHeader>

    <UMain>
      <UContainer>
        <slot />
      </UContainer>
    </UMain>
  </div>
</template>
