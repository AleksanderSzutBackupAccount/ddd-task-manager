<script setup lang="ts">
import * as z from 'zod'
import type { FormSubmitEvent } from '#ui/types'
import type { User } from '~/store/users'

defineProps<{
  users: User[]
  loading?: boolean
}>()

const emit = defineEmits<{
  submit: [data: { name: string, slug: string, userIds: string[] }]
}>()

const schema = z.object({
  name: z.string().min(4, 'Name must be at least 4 characters'),
  slug: z.string().regex(/^[a-z]+$/, 'Slug must be only lowercase letters').max(4, 'Slug must be at most 4 characters'),
  userIds: z.array(z.string()).optional()
})

type Schema = z.output<typeof schema>

const state = reactive({
  name: undefined,
  slug: undefined,
  userIds: []
})

const onSubmit = async (event: FormSubmitEvent<Schema>) => {
  emit('submit', {
    name: event.data.name,
    slug: event.data.slug,
    userIds: event.data.userIds || []
  })
}
</script>

<template>
  <UForm
    class="space-y-4"
    :schema="schema"
    :state="state"
    @submit="onSubmit"
  >
    <UFormField
      label="Workspaces Name"
      name="name"
      required
    >
      <UInput
        v-model="state.name"
        class="w-full"
        placeholder="Workspaces Name"
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
        placeholder="Slug (4 lowercase letters)"
        required
        :disabled="loading"
      />
    </UFormField>

    <UFormField
      label="Users"
      name="userIds"
    >
      <USelectMenu
        v-model="state.userIds"
        multiple
        :items="users"
        class="w-full"
        value-key="id"
        label-key="name"
        placeholder="Select users"
      >
        <template #item-leading="{ item }">
          <UAvatar
            :src="`https://i.pravatar.cc/120?img=${encodeURIComponent(item.id)}`"
            size="sm"
            class="ring-2 ring-white dark:ring-gray-900"
          />
        </template>
      </USelectMenu>
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
        :loading="loading"
      >
        Create
      </UButton>
    </div>
  </UForm>
</template>
