<script setup lang="ts">
import { useProjectStore } from '~/store/project'

const store = useProjectStore()
const { projects } = storeToRefs(store)
await useAsyncData('projects', () => store.fetchProjects())
</script>

<template>
  <div>
    <UPageSection
      title="Select Workspace"
    >
      <template #header>
        <h2 class="text-lg font-bold" />
      </template>
      <UPageGrid>
        <UPageCard
          v-for="project in projects"
          :key="project.id"
          :class="[
            'cursor-pointer transition'
          ]"
          :title="project.name"
          :to="`/workspace/${project.id}`"
          icon="i-material-symbols:add-business-outline-rounded"
        />
        <UPageCard
          title="Create new Project"
          description=""
          icon="i-material-symbols:add-business-outline-rounded"
        >
          <template #footer>
            <UButton to="/workspace/create">
              Create New Project
            </UButton>
          </template>
        </UPageCard>
      </UPageGrid>
    </UPageSection>
  </div>
</template>

<style scoped>

</style>
