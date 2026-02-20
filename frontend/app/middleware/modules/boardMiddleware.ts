import type { RouteLocationNormalizedGeneric } from 'vue-router'

import { storeToRefs } from 'pinia'
import { Routes } from '~/constants/Routes'
import { useWorkspacesStore } from '~/store/workspaces'

export default async function (to: RouteLocationNormalizedGeneric, _authToken: null | string) {
  const projectId = to.params.id as string | undefined
  const projectStore = useWorkspacesStore()

  const { currentProject } = storeToRefs(projectStore)

  if (!projectId) {
    if (currentProject.value === null) {
      return navigateTo(Routes.workspaces)
    }
    return navigateTo(`/project/${currentProject.value.slug}`)
  }

  if (!await projectStore.setProject(projectId)) {
    return navigateTo(Routes.workspaces)
  }
}
