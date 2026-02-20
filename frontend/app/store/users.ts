import { defineStore } from 'pinia'
import { useAuthStore } from '~/store/auth'
import type { User } from '~/types/user'

export const useUsersStore = defineStore('users', () => {
  const { token } = storeToRefs(useAuthStore())

  const users = ref<User[]>([])

  const loading = ref(false)
  const error = ref<string | null>(null)

  async function fetchUsers() {
    loading.value = true
    error.value = null

    try {
      users.value = await $fetch('/backend/users', { headers: {
        Authorization: `Bearer ${token.value}`
      } })
      return users.value
    } catch (e: any) {
      error.value = e?.message ?? 'Błąd pobierania'
    } finally {
      loading.value = false
    }
  }

  return {
    users,
    fetchUsers,
    loading,
    error
  }
})
