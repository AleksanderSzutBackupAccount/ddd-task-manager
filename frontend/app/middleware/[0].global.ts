import type { RouteLocationNormalizedGeneric } from 'vue-router'
import { useAuthStore } from '~/store/auth'
import { getLayout, isAuth, isBoard, isWorkspaces, isRouteProtected } from '~/middleware/route.helpers'
import authMiddleware from '~/middleware/modules/authMiddleware'
import workspaceMiddleware from '~/middleware/modules/workspacesMiddleware'
import { Routes } from '~/constants/Routes'
import boardMiddleware from '~/middleware/modules/boardMiddleware'

export default defineNuxtRouteMiddleware(async (to: RouteLocationNormalizedGeneric) => {
  to.meta.layout = getLayout(to)

  const authStore = useAuthStore()

  const { user, token } = authStore

  if (isAuth(to)) {
    return authMiddleware(to, token)
  }

  if (isRouteProtected(to) && !token) {
    return navigateTo(Routes.signIn)
  }
  if (!user) {
    await authStore.fetchUserData()
  }

  if (!isWorkspaces(to)) {
    return
  }
  if (isBoard(to)) {
    return boardMiddleware(to, token)
  }
  return workspaceMiddleware(to, token)
})
