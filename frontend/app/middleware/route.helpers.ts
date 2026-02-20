import type { RouteLocationNormalizedGeneric } from 'vue-router'
import type {LayoutKey} from "#build/types/nitro-layouts";

export const isAuth = (to: RouteLocationNormalizedGeneric): boolean => {
  return to.path.startsWith('/auth')
}

export const isBoard = (to: RouteLocationNormalizedGeneric): boolean => {
  return /^\/project\/[^/]+\/board/.test(to.path)
}

export const isWorkspaces = (to: RouteLocationNormalizedGeneric): boolean => {
  return to.path.startsWith('/workspace')
}
export const isRouteProtected = (to: RouteLocationNormalizedGeneric): boolean => {
  return isWorkspaces(to) || isBoard(to)
}
export const getLayout = (to: RouteLocationNormalizedGeneric): LayoutKey => {
  if (isAuth(to)) {
    return 'auth'
  }

  if (isBoard(to)) {
    return 'board'
  }

  if (isWorkspaces(to)) {
    return 'workspaces'
  }

  return 'default'
}
