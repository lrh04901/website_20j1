<template>
  <main class="p-4 bg-gray-50 min-h-screen">
    <div class="max-w-screen-xl mx-auto bg-white p-8 rounded-lg shadow-2xl my-28">
      <h2 class="text-3xl my-6">留言板</h2>
      <!--===============================================用户留言区域=====================================================-->
      <CommentBox/>
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
      <ReplyBox v-if="comment.replies">
        <CommentItem
          v-for="reply in comment.replies"
          :key="reply.id"
          :user="reply.user"
          :avatar="reply.avatar"
          :time="reply.time"
          :content="reply.content"
        />
      </ReplyBox>
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

//图片引入
  import lrh from "./assets/lrh.jpg";
  import lrh2 from "./assets/lrh2.jpg";

//示例数组
  const comments = [
    {
      id: 1,
      user: "lrhAdmin",
      avatar: lrh,
      time: "6天前",
      content:
        "lrh发布的第一条测试留言",
      replies: [
        {
          id: 2,
          user: "adminLrh",
          avatar: lrh2,
          time: "6天前",
          content:
            "回复测试",
        }
      ]
    }
  ]
</script>

<style>

</style>
