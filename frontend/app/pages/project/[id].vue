<script setup lang="ts">
import { useWorkspacesStore, type Task } from '~/store/workspaces'
import { useUsersStore } from '~/store/users'
const route = useRoute()
const slug = route.params.id as string

const store = useWorkspacesStore()
const usersStore = useUsersStore()

const tasks = ref<Task[]>([])
const members = ref<{ id: string, name: string }[]>([])

const loading = ref(true)
const fetchAllData = async () => {
  loading.value = true
  const [tasksData, membersData] = await Promise.all([
    store.fetchTasks(),
    store.getProjectMembers(slug),
    usersStore.fetchUsers()
  ])
  tasks.value = tasksData || []
  members.value = (membersData as { id: string, name: string }[]) || []
  loading.value = false
}

onMounted(fetchAllData)

const statuses = ['To Do', 'In Progress', 'Done']

const tasksByStatus = computed(() => {
  const grouped: Record<string, Task[]> = {
    'To Do': [],
    'In Progress': [],
    'Done': []
  }
  tasks.value.forEach((task) => {
    if (grouped[task.status]) {
      grouped[task.status].push(task)
    }
  })
  return grouped
})

const isAddTaskModalOpen = ref(false)

const onAddTask = async (data: { name: string, description: string, assigned_user_id: string | null }) => {
  try {
    await store.createTask(data.name, data.description, data.assigned_user_id)
    isAddTaskModalOpen.value = false
    await fetchAllData()
  } catch (e: unknown) {
    console.error(e)
  }
}

const onChangeStatus = async (taskId: string, newStatus: string) => {
  try {
    await store.updateTaskStatus(taskId, newStatus)
    await fetchAllData()
  } catch (e: unknown) {
    console.error(e)
  }
}

const getUserName = (userId: string | null) => {
  if (!userId) return 'Unassigned'
  return usersStore.users.find(u => u.id === userId)?.name || 'Unknown'
}
</script>

<template>
  <UPageSection :title="`Project: ${slug.toUpperCase()}`">
    <template #header>
      <div class="flex justify-between items-center w-full">
        <h1 class="text-2xl font-bold">
          {{ slug.toUpperCase() }} Board
        </h1>

        <UModal
          v-model:open="isAddTaskModalOpen"
          title="Add New Task"
        >
          <UButton
            icon="i-lucide-plus"
            @click="isAddTaskModalOpen = true"
          >
            Add Task
          </UButton>
          <template #body>
            <TaskForm
              :users="usersStore.users"
              :loading="store.loading"
              @submit="onAddTask"
            />
          </template>
        </UModal>
      </div>
    </template>

    <div
      v-if="loading"
      class="flex justify-center p-10"
    >
      <UIcon
        name="i-lucide-loader-2"
        class="animate-spin size-8"
      />
    </div>

    <div
      v-else
      class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6"
    >
      <div
        v-for="status in statuses"
        :key="status"
        class="bg-gray-50/50 dark:bg-gray-800/50 p-4 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 min-h-[500px]"
      >
        <h2 class="font-bold text-sm tracking-widest uppercase text-gray-400 pb-2 mb-4 flex justify-between items-center">
          {{ status }}
          <UBadge
            color="neutral"
            variant="subtle"
            size="sm"
            class="rounded-full"
          >
            {{ tasksByStatus[status].length }}
          </UBadge>
        </h2>

        <div class="space-y-4">
          <TaskCard
            v-for="task in tasksByStatus[status]"
            :key="task.id"
            :task="task"
            :get-user-name="getUserName"
            :statuses="statuses"
            @status-change="(val: string) => onChangeStatus(task.id, val)"
          />
        </div>
      </div>
    </div>
  </UPageSection>
</template>
