<script setup lang="ts">
import * as z from 'zod'
import { useRouter } from 'vue-router'
import type { FormSubmitEvent } from '#ui/types'
import { useAuthStore } from '~/store/auth'
import {storeToRefs} from "pinia";

const toast = useToast()
const router = useRouter()

useSeoMeta({
  title: 'Login'
})

type Schema = z.output<typeof schema>

const auth = useAuthStore()
const { errorMessage } = storeToRefs(auth)

const fields = [{
  name: 'email',
  type: 'text' as const,
  label: 'Email',
  placeholder: 'Enter your email',
  required: true
}]

const schema = z.object({
  email: z.email('Invalid email'),
})

const onSubmit = async (payload: FormSubmitEvent<Schema>) => {
  await auth.signIn(payload.data.email)

  if (errorMessage.value) {
    toast.add({ icon: 'i-heroicons-x-circle', title: errorMessage.value || 'Invalid login credentials', color: 'error' })
    return
  }
  toast.add({ icon: 'i-heroicons-check-circle', title: 'Successfully signed in!', color: 'success' })
  await router.push('/project')
}
</script>

<template>
  <UAuthForm
    :fields="fields"
    :schema="schema"
    title="Welcome back"
    icon="i-lucide-lock"
    align="top"
    @submit="onSubmit"
  >
    <template #description>
      Don't have an account?

      <ULink
        to="/auth/sign-up"
        class="text-primary-500 font-medium"
        disabled
      >
        Sign up
      </ULink>
      .
    </template>

    <template #footer>
      By signing in, you agree to our
      <ULink
        disabled
        to="/public"
        class="text-primary-500 font-medium"
      >
        Terms of Service
      </ULink>
      .
    </template>
  </UAuthForm>
</template>
