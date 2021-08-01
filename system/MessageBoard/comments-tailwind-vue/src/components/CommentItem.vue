<template>
    <div class="flex">
      <!--在public目录下，需要使用/绝对路径引用文件，省略public-->
      <img :src="avatar" alt="" class="w-12 h-12 mr-4 rounded-full"/>
      <div>
        <p>{{ user }}</p>
        <p class="text-gray-600 text-sm">{{ time }}</p>
      </div>

      <!--右对齐菜单-->
      <span class="ml-auto">
              <svg xmlns="http://www.w3.org/2000/svg"
                   class="h-5 w-5 text-gray-500"
                   viewBox="0 0 20 20"
                   fill="currentColor"
              >
                <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/>
              </svg>
            </span>
    </div>
    <p class="text-gray-600 py-4">
      {{ content }}
    </p>
</template>

<script setup>
  defineProps(["user","avatar","time","content"]);
</script>

<style>

</style>
