<template>
  <main class="p-4 bg-gray-50 min-h-screen">
    <div class="max-w-screen-xl mx-auto bg-white p-8 rounded-lg shadow-2xl my-28">
      <h2 class="text-3xl my-6">留言板</h2>
      <!--===============================================用户留言区域=====================================================-->
      <CommentBox @submit="addNewComment"/>
      <!--===============分隔线===============-->
      <DividerHorizontal/>
      <!--===============================================留言显示区域=================================================-->
      <div v-for="comment in comments" :key="comment.id">
        <!--留言内容-->
        <CommentItem :user="comment.user"
                     :avatar="comment.avatar"
                     :time="comment.time"
                     :content="comment.content"
        />

        <!--回复内容-->
        <ReplyContainer v-if="comment.replies">
          <CommentItem
            v-for="reply in comment.replies"
            :key="reply.id"
            :user="reply.user"
            :avatar="reply.avatar"
            :time="reply.time"
            :content="reply.content"
          />
        </ReplyContainer>
        <ReplyBox @submit="addReply($event,comment.id)"/>
      </div>
    </div>
  </main>
</template>

<script setup>
//组件引入
import CommentBox from "./components/CommentBox.vue";
import DividerHorizontal from "./components/DividerHorizontal.vue";
import CommentItem from "./components/CommentItem.vue";
import ReplyBox from "./components/ReplyBox.vue";
import ReplyContainer from "./components/ReplyContainer.vue";

//图片引入
import lrh from "./assets/lrh.jpg";
import lrh2 from "./assets/lrh2.jpg";
/*import mAva from "./assets/index.png";*/
import {ref} from "vue";

//示例数组
let rid = ref(3);
const comments = ref([
  {
    id: 1,
    user: "lrhAdmin",
    avatar: lrh,
    time: "2021-7-26",
    content:
      "lrh发布的第一条测试留言",
    replies: [
      {
        id: 2,
        user: "adminLrh",
        avatar: lrh2,
        time: "2021-7-26",
        content:
          "回复测试",
      }
    ]
  }
]);

const constructNewComment = (content) => {
  return {
    id: rid.value++,
    user: "当前用户",
    avatar: lrh,
    content,
    time: "10分钟前",
  };
};

const addNewComment = (content) => {
  const newComment = constructNewComment(content);
  comments.value.push(newComment);
};

const addReply = (content, id) => {
  const reply = constructNewComment(content);
  let comment = comments.value.find((comment) => comment.id === id);
  if (comment.replies) {
    comment.replies.push(reply);
  } else {
    comment.replies = [reply];
  }
};
</script>

<style>

</style>
