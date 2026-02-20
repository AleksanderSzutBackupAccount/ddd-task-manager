<script setup lang="ts">
import * as z from 'zod'
import type { FormSubmitEvent } from '#ui/types'
import { useProjectStore } from '~/store/project'
import { Routes } from '~/constants/Routes'

const schema = z.object({
  name: z.string().min(4),
  slug: z.string().regex(/^[a-z]+$/).max(4)
})

const toast = useToast()
type Schema = z.output<typeof schema>

const state = reactive<Partial<Schema>>({
  name: undefined,
  slug: undefined
})
const { createProject } = useProjectStore()

const onSubmit = async (event: FormSubmitEvent<Schema>) => {
  try {
    const data = await createProject(event.data.name, event.data.slug)
    navigateTo(Routes.workspaces)
  } catch (error) {
    toast.add({
      title: 'Error when create project',
      description: error.message,
      icon: 'i-lucide-check-circle',
      color: 'error'
    })
  }
}
</script>

<template>
  <UPageSection>
    <UCard
      variant="subtle"
      color="primary"
      class="mt-auto"
      :ui="{ header: 'flex items-center gap-1.5' }"
    >
      <template #header>
        <UIcon
          name="i-material-symbols:add-business"
          class="size-5"
        />

        <span class="text-sm truncate">
          Create Project
        </span>
      </template>
      <UForm
        class="space-y-4"
        :schema="schema"
        :state="state"
        @submit="onSubmit"
      >
        <UFormField
          label="Project Name"
          name="name"
          required
        >
          <UInput
            v-model="state.name"
            class="w-full"
            placeholder="Project Name"
          />
        </UFormField>
        <UFormField
          label="Slug"
          name="slug"
          required
        >
          <UInput
            v-model="state.slug"
            class="w-full"
            placeholder="Description of project for AI"
            autoresize
            required
            :rows="4"
            :disabled="false"
          />
        </UFormField>

        <div class="flex items-center justify-between gap-2">
          <UButton
            variant="solid"
            color="neutral"
            to="/workspace"
          >
            Back to select
          </UButton>
          <UButton
            type="submit"
            variant="solid"
          >
            Create
          </UButton>
        </div>
      </UForm>
    </UCard>
  </UPageSection>
</template>

<style scoped>

</style>
