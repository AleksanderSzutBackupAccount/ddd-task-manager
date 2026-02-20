<script setup lang="ts">
import type { Task } from '~/store/workspaces'

defineProps<{
  task: Task
  getUserName: (userId: string | null) => string
  statuses: string[]
}>()

defineEmits<{
  statusChange: [status: string]
}>()
</script>

<template>
  <UCard
    class="cursor-pointer hover:ring-2 hover:ring-primary-500 transition-all duration-200 shadow-sm rounded-lg"
    :ui="{ body: { padding: 'p-4' }, header: { padding: 'p-4 pb-2' }, footer: { padding: 'p-4 pt-2' } }"
  >
    <template #header>
      <div class="flex justify-between items-start gap-2">
        <h3 class="font-semibold text-sm leading-tight">
          {{ task.name }}
        </h3>
        <UBadge
          color="neutral"
          variant="subtle"
          size="xs"
          class="font-mono text-[10px]"
        >
          {{ task.slug }}
        </UBadge>
      </div>
    </template>
    <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-3 leading-relaxed">
      {{ task.description }}
    </p>
    <template #footer>
      <div class="flex justify-between items-center mt-2">
        <div class="flex items-center gap-2">
          <UAvatar
            :src="`https://ui-avatars.com/api/?name=${encodeURIComponent(getUserName(task.assigned_user_id))}&background=random`"
            size="2xs"
            class="rounded-full"
          />
          <span class="text-[10px] font-medium text-gray-500">{{ getUserName(task.assigned_user_id) }}</span>
        </div>
        <USelectMenu
          :model-value="task.status"
          :items="statuses"
          size="xs"
          @update:model-value="(val: string) => $emit('statusChange', val)"
        />
      </div>
    </template>
  </UCard>
</template>
