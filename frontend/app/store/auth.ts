import {defineStore} from "pinia";

import type {User} from "~/types/user";

export const useAuthStore = defineStore('auth', () => {
  const router = useRouter()

  const isLoading = ref(false)
  const errorMessage = ref<string | null>(null)
  const user = ref<User | null>(null)
  const token = useCookie<string | null>('auth_token')
  const isAuthenticated = computed(() => !!token.value)

  const signIn = async (email: string) => {
    isLoading.value = true
    errorMessage.value = null

    const { data, error } = await useFetch<{token: string}>('/backend/auth/login', {
      method: 'POST',
      body: { email }
    })
    if (error.value || !data.value?.token) {
      console.error(error.value)
      errorMessage.value = 'Błąd logowania'
      isLoading.value = false
      return
    }
    token.value = data.value.token
    await fetchUserData()

    isLoading.value = false
  }

  const fetchUserData = async () => {
    if (!token.value) return

    const { data, error } = await useFetch<User>('/backend/auth/me', {
      headers: {
        Authorization: `Bearer ${token.value}`
      }
    })

    if (error.value || !data.value) {
      throw new Error('Błąd logowania')
    }

    user.value = data.value
  }

  const signOut = async () => {
    token.value = null
    user.value = null

    navigateTo('/')
    await router.push('/auth/sign-in')
  }
  return {
    user,
    isLoading,
    signIn,
    signOut,
    fetchUserData,
    errorMessage,
    token,
    isAuthenticated
  }
})
