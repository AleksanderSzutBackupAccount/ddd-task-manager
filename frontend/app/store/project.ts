import { defineStore } from 'pinia'
import { useAuthStore } from '~/store/auth'

import type { User } from '~/types/user'

type Project = {
  id: string
  name: string
  slug: string
}
export const useProjectStore = defineStore('project', () => {
  const { token } = storeToRefs(useAuthStore())

  const currentProject = ref<string | null>()

  const projects = ref<Project[]>([])
  const loading = ref(false)
  const error = ref<string | null>(null)

  async function fetchProjects() {
    loading.value = true
    error.value = null

    try {
      projects.value = await $fetch('/backend/projects', { headers: {
        Authorization: `Bearer ${token.value}`
      } })
      return projects.value
    } catch (e: any) {
      error.value = e?.message ?? 'Błąd pobierania'
    } finally {
      loading.value = false
    }
  }

  const setProject = (projectId: string) => {
    if (projects.value.find((w: Project) => w.id === projectId)) {
      currentProject.value = projectId
      return true
    }

    return false
  }

  const createProject = async (name: string, slug: string) => {
    const { data, error } = await useFetch<User>('/backend/projects', {
      method: 'POST',
      body: { name, slug },
      headers: {
        Authorization: `Bearer ${token.value}`
      }
    })

    if (error) throw error

    await fetchProjects()
  }

  const resetWorkspace = () => {
    currentProject.value = null
  }

  return {
    createProject,
    projects,
    currentProject,
    fetchProjects,
    setProject,
    resetWorkspace
  }
})
