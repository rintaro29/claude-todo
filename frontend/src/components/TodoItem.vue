<template>
  <li class="todo-item">
    <input
      type="checkbox"
      :checked="todo.is_completed"
      @change="toggle"
    />
    <span :class="{ completed: todo.is_completed }">{{ todo.title }}</span>
    <button @click="remove">削除</button>
  </li>
</template>

<script setup>
import axios from 'axios'

const props = defineProps({
  todo: { type: Object, required: true },
})

const emit = defineEmits(['updated', 'deleted', 'error'])

async function toggle() {
  try {
    await axios.patch(`/api/todos/${props.todo.id}`)
    emit('updated')
  } catch {
    emit('error', '完了状態の更新に失敗しました')
  }
}

async function remove() {
  try {
    await axios.delete(`/api/todos/${props.todo.id}`)
    emit('deleted')
  } catch {
    emit('error', '削除に失敗しました')
  }
}
</script>

<style scoped>
.todo-item {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 8px 0;
}

.completed {
  text-decoration: line-through;
  color: #999;
}
</style>
