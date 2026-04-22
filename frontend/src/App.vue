<template>
  <div class="container">
    <h1>TODO</h1>

    <form @submit.prevent="addTodo">
      <input
        v-model="newTitle"
        type="text"
        placeholder="新しい TODO を入力"
        maxlength="255"
      />
      <button type="submit" :disabled="!newTitle.trim()">追加</button>
    </form>

    <p v-if="error" class="error">{{ error }}</p>

    <ul v-if="todos.length">
      <TodoItem
        v-for="todo in todos"
        :key="todo.id"
        :todo="todo"
        @updated="fetchTodos"
        @deleted="fetchTodos"
        @error="msg => error = msg"
      />
    </ul>
    <p v-else class="empty">TODO はありません</p>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'
import TodoItem from './components/TodoItem.vue'

const todos = ref([])
const newTitle = ref('')
const error = ref('')

async function fetchTodos() {
  try {
    const { data } = await axios.get('/api/todos')
    todos.value = data
    error.value = ''
  } catch {
    error.value = 'TODO の取得に失敗しました'
  }
}

async function addTodo() {
  const title = newTitle.value.trim()
  if (!title) return
  try {
    await axios.post('/api/todos', { title })
    newTitle.value = ''
    await fetchTodos()
  } catch {
    error.value = 'TODO の追加に失敗しました'
  }
}

onMounted(fetchTodos)
</script>

<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: sans-serif; background: #f5f5f5; }
</style>

<style scoped>
.container {
  max-width: 480px;
  margin: 48px auto;
  background: #fff;
  border-radius: 8px;
  padding: 24px;
  box-shadow: 0 2px 8px rgba(0,0,0,.1);
}

h1 {
  margin-bottom: 20px;
  font-size: 1.5rem;
}

form {
  display: flex;
  gap: 8px;
  margin-bottom: 16px;
}

input[type="text"] {
  flex: 1;
  padding: 8px 12px;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-size: 1rem;
}

button {
  padding: 8px 16px;
  background: #4a90e2;
  color: #fff;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 1rem;
}

button:disabled { opacity: 0.5; cursor: default; }
button:not(:disabled):hover { background: #357abd; }

ul { list-style: none; }

.error { color: #e53e3e; margin-bottom: 12px; }
.empty { color: #999; text-align: center; padding: 24px 0; }
</style>
