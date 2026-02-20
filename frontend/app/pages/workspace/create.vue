<script setup lang="ts">
import { useWorkspacesStore } from '~/store/workspaces'
import { useUsersStore } from '~/store/users'
import { Routes } from '~/constants/Routes'

const toast = useToast()
const store = useWorkspacesStore()
const usersStore = useUsersStore()

onMounted(async () => {
  await usersStore.fetchUsers()
})

const onSubmit = async (data: { name: string, slug: string, userIds: string[] }) => {
  try {
    await store.createProject(data.name, data.slug, data.userIds)
    navigateTo(Routes.workspaces)
  } catch (error: unknown) {
    toast.add({
      title: 'Error when creating project',
      description: error instanceof Error ? error.message : 'Something went wrong',
      icon: 'i-lucide-alert-circle',
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
          Create Workspaces
        </span>
      </template>
      <WorkspaceForm
        :users="usersStore.users"
        :loading="store.loading"
        @submit="onSubmit"
      />
    </UCard>
  </UPageSection>
</template>

<style scoped>
</style>
