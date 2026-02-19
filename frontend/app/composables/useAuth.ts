export interface User {
  id: string
  email: string
}

export const useAuth = () => {
  const user = useState<User | null>('auth_user', () => null)
  const token = useState<string | null>('auth_token', () => null)
  if (process.client && !token.value) {
    token.value = localStorage.getItem('auth_token')
  }
  const isAuthenticated = computed(() => !!token.value)

  async function login(email: string, password: string) {
    const { data, error } = await useFetch('/api/auth/login', {
      method: 'POST',
      body: { email, password }
    })

    if (error.value) {
      throw new Error('Błąd logowania')
    }

    token.value = data.value.token
    user.value = data.value.user
  }

  function logout() {
    token.value = null
    user.value = null
    navigateTo('/')
  }

  async function fetchUser() {
    if (!token.value) return

    const { data } = await useFetch('/api/me', {
      headers: {
        Authorization: `Bearer ${token.value}`
      }
    })

    user.value = data.value
  }

  return {
    user,
    token,
    isAuthenticated,
    login,
    logout,
    fetchUser
  }
}
