<script lang="ts" setup>
definePageMeta({
  layout: 'public'
})


const email = ref("")
const error = ref("")
const loading = ref(false)

const password = ref("")
const { login } = useAuth()

async function loginHandler() {
  if (loading.value) return
  loading.value = true
  error.value = ""

  try {
    await login(email.value, password.value)
    navigateTo('/admin')
  } catch (e) {
    error.value = "Nieprawidłowy email lub hasło."
  } finally {
    loading.value = false
  }
}

</script>

<template>
  <UCard
    class="w-full max-w-md p-6"
  >
    <UForm @submit="loginHandler">
      <h1 class="text-2xl font-bold mb-6 text-center">
         Logowanie
      </h1>

      <UFormField label="Email" class="mb-4">
        <UInput
          v-model="email"
          type="email"
          placeholder="admin@example.com"
          size="lg"
          icon="i-heroicons-envelope"
          class="w-full"
        />
      </UFormField>

      <UButton block size="lg" :loading="loading" color="primary" class="mt-2" type="submit">
        Zaloguj się
      </UButton>

      <p
        v-if="error"
        class="text-red-500 text-center mt-4 font-medium"
      >
        {{ error }}
      </p>
    </UForm>
  </UCard>
</template>
