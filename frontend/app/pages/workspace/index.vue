<script setup lang="ts">
import { useWorkspacesStore } from '~/store/workspaces'
import { useUsersStore } from '~/store/users'

const store = useWorkspacesStore()
const usersStore = useUsersStore()
const { projects } = storeToRefs(store)

await Promise.all([
  useAsyncData('projects', () => store.fetchProjects()),
  useAsyncData('users', () => usersStore.fetchUsers())
])

const getUserName = (userId: string) => {
  return usersStore.users.find(u => u.id === userId)?.name || 'User'
}
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
        <WorkspaceCard
          v-for="project in projects"
          :key="project.id"
          :project="project"
          :get-user-name="getUserName"
        />
        <UPageCard
          title="Create new Workspaces"
          description=""
          icon="i-material-symbols:add-business-outline-rounded"
        >
          <template #footer>
            <UButton to="/workspace/create">
              Create New Workspaces
            </UButton>
          </template>
        </UPageCard>
      </UPageGrid>
    </UPageSection>
  </div>
</template>

<style scoped>

</style>
