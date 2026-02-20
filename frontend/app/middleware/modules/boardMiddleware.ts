import type { RouteLocationNormalizedGeneric } from 'vue-router'

import { storeToRefs } from 'pinia'
import { Routes } from '~/constants/Routes'
import { useProjectStore } from '~/store/project'

export default async function (to: RouteLocationNormalizedGeneric, authToken: null | string) {
  const projectId = to.params.projectId as string | undefined
  const projectStore = useProjectStore()

  const { currentProject } = storeToRefs(projectStore)

  if (!projectId) {
    if (currentProject.value === null) {
      return navigateTo(Routes.workspaces)
    }
    return navigateTo(`/project/${currentProject}`)
  }

  if (!projectStore.setProject(projectId)) {
    return navigateTo(Routes.project)
  }
}
