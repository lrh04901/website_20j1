<template>
  <button class="pt-4 pb-10 text-blue-600"
          @click="showCommentBox = !showCommentBox"
          :class="[showCommentBox ? 'pb-2' : 'pb-10']">回复
  </button>
  <CommentBox
    v-if="showCommentBox"
    class="mb-4"
    @submit="
            emit('submit',$event);
            showCommentBox = false;
    "/>
</template>

<script setup>
import {ref} from "vue";
import CommentBox from "./CommentBox.vue"

const showCommentBox = ref(false);

const emit = defineEmits(['submit']);
</script>

<style>

</style>
