<script setup>

// const user = useSupabaseUser()

onMounted(() => {
  // if (user.value) {
  //   navigateTo('/project')
  // }
})
</script>

<template>
  <UApp>
    <div class="min-h-screen flex items-center justify-center bg-gray-100 dark:bg-gray-900 p-6">
      <slot />
    </div>
  </UApp>
</template>
