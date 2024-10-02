<template>
  <div class="rounded-xl border border-gray-300 space-y-4">
    <a :href="content.link.url" class="block group">
      <div class="px-6 py-5 space-y-2">
        <div class="justify-between gap-2 flex flex-col sm:flex-row">
          <h3 v-if="content.title" class="text-gray-900 text-2xl font-semibold leading-tight transition-colors duration-100 ease-in-out group-hover:text-primary-600">{{ content.title }}</h3>
          <div v-if="content.date" class="text-gray-900 text-lg sm:text-2xl leading-tight">{{ content.date }}</div>
        </div>
        <div class="justify-between gap-4 sm:gap-2 flex flex-col sm:flex-row">
          <p v-if="content.location" class="text-gray-600 text-xl leading-7">{{ content.location }}</p>
          <button v-if="content.link.url" class="self-end flex gap-2 text-gray-600 group-hover:text-gray-800">
            <div class="justify-start items-center flex">
              <div class="text-lg leading-none">přečíst si více</div>
            </div>
            <div class="w-5 h-5 opacity-75 transition-translate duration-200 ease-in-out group-hover:translate-x-1">
              {{ icons.get( 'arrow-right' ) }}
            </div>
          </button>
        </div>
      </div>
    </a>
  </div>
</template>

<script setup>
const props = defineProps({
  content: {
    type: Object,
    required: true,
  }
})
</script>
