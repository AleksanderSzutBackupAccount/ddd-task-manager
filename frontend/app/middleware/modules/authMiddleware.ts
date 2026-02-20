import type { RouteLocationNormalizedGeneric } from 'vue-router'

import { Routes } from '~/constants/Routes'

export default function (to: RouteLocationNormalizedGeneric, authToken: null | string) {
  if (authToken) {
    return navigateTo(Routes.workspaces)
  }
}
