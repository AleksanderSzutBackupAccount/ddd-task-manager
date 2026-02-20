<script setup lang="ts">
import type { Project } from '~/store/workspaces'

defineProps<{
  project: Project
  getUserName: (userId: string) => string
}>()
</script>

<template>
  <UPageCard
    :class="[
      'cursor-pointer transition'
    ]"
    :title="project.name"
    :to="`/project/${project.slug}`"
    icon="i-material-symbols:add-business-outline-rounded"
  >
    <template #description>
      <div class="flex -space-x-2 overflow-hidden mt-2">
        <UAvatar
          v-for="userId in project.users"
          :key="userId"
          :src="`https://i.pravatar.cc/120?img=${encodeURIComponent(getUserName(userId))}`"
          :alt="getUserName(userId)"
          size="sm"
          class="ring-2 ring-white dark:ring-gray-900"
        />
      </div>
    </template>
  </UPageCard>
</template>
