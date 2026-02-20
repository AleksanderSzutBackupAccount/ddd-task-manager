<script setup lang="ts">
import type { User } from '~/store/users'

const props = defineProps<{
  users: User[]
  loading?: boolean
}>()

const emit = defineEmits<{
  submit: [data: { name: string, description: string, assigned_user_id: string | null }]
}>()

const state = reactive({
  name: '',
  description: '',
  assigned_user_id: null as string | null
})

const onSubmit = () => {
  emit('submit', { ...state })
  state.name = ''
  state.description = ''
  state.assigned_user_id = null
}
</script>

<template>
  <div class="space-y-4 p-4">
    <UFormField
      label="Task Name"
      name="name"
      required
    >
      <UInput
        v-model="state.name"
        placeholder="Name"
        class="w-full"
      />
    </UFormField>
    <UFormField
      label="Description"
      name="description"
    >
      <UTextarea
        v-model="state.description"
        placeholder="Describe the task..."
        class="w-full"
        :rows="3"
      />
    </UFormField>
    <UFormField
      label="Assignee"
      name="assigned_user_id"
    >
      <USelectMenu
        v-model="state.assigned_user_id"
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
    <div class="flex justify-end pt-4">
      <UButton
        :loading="loading"
        @click="onSubmit"
      >
        Create Task
      </UButton>
    </div>
  </div>
</template>
