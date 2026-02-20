import { defineStore } from 'pinia'
import { useAuthStore } from '~/store/auth'

export type Project = {
  id: string
  name: string
  slug: string
  users?: string[]
}

export type Task = {
  id: string
  slug: string
  name: string
  description: string
  status: string
  assigned_user_id: string | null
}

export const useWorkspacesStore = defineStore('workspaces', () => {
  const { token } = storeToRefs(useAuthStore())

  const currentProjectSlug = ref<string | null>(null)

  const projects = ref<Project[]>([])
  const loading = ref(false)
  const error = ref<string | null>(null)

  async function fetchProjects() {
    loading.value = true
    error.value = null

    try {
      const data = await $fetch<Project[]>('/backend/projects', {
        headers: {
          Authorization: `Bearer ${token.value}`
        }
      })
      projects.value = data
      return data
    } catch (e: unknown) {
      error.value = e instanceof Error ? e.message : 'Błąd pobierania'
    } finally {
      loading.value = false
    }
  }

  async function getProjectMembers(projectSlug: string) {
    loading.value = true
    error.value = null

    try {
      const members = await $fetch(`/backend/projects/${projectSlug}/members`, {
        headers: {
          Authorization: `Bearer ${token.value}`
        }
      })
      return members
    } catch (e: unknown) {
      error.value = e instanceof Error ? e.message : 'Błąd pobierania'
    } finally {
      loading.value = false
    }
  }

  const setProject = async (projectSlug: string) => {
    if (projects.value.length === 0) {
      await fetchProjects()
    }

    if (projects.value.find((w: Project) => w.slug === projectSlug)) {
      currentProjectSlug.value = projectSlug
      return true
    }

    return false
  }

  const currentProject = computed(() =>
    projects.value.find((w: Project) => w.slug === currentProjectSlug.value) || null
  )

  const createProject = async (name: string, slug: string, userIds: string[] = []) => {
    const { error: fetchError } = await useFetch('/backend/projects', {
      method: 'POST',
      body: { name, slug, userIds },
      headers: {
        Authorization: `Bearer ${token.value}`
      }
    })

    if (fetchError.value) throw fetchError.value

    await fetchProjects()
  }

  const fetchTasks = async () => {
    if (!currentProject.value) return []

    loading.value = true
    error.value = null

    try {
      const tasks = await $fetch<Task[]>(`/backend/projects/${currentProject.value.slug}/tasks`, {
        headers: {
          Authorization: `Bearer ${token.value}`
        }
      })
      return tasks
    } catch (e: unknown) {
      error.value = e instanceof Error ? e.message : 'Błąd pobierania zadań'
      return []
    } finally {
      loading.value = false
    }
  }

  const createTask = async (name: string, description: string, assignedUserId: string | null) => {
    if (!currentProject.value) return

    const { error: fetchError } = await useFetch(`/backend/projects/${currentProject.value.slug}/tasks`, {
      method: 'POST',
      body: { name, description, assigned_user_id: assignedUserId },
      headers: {
        Authorization: `Bearer ${token.value}`
      }
    })

    if (fetchError.value) throw fetchError.value
  }

  const updateTaskStatus = async (taskId: string, status: string) => {
    const { error: fetchError } = await useFetch(`/backend/tasks/${taskId}/status`, {
      method: 'PATCH',
      body: { status },
      headers: {
        Authorization: `Bearer ${token.value}`
      }
    })

    if (fetchError.value) throw fetchError.value
  }

  const resetWorkspace = () => {
    currentProjectSlug.value = null
  }

  return {
    createProject,
    projects,
    currentProject,
    fetchProjects,
    currentProjectSlug,
    getProjectMembers,
    setProject,
    resetWorkspace,
    fetchTasks,
    createTask,
    updateTaskStatus,
    loading,
    error
  }
})
