<template>
  <div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
    <h2 class="text-lg font-semibold text-gray-800 mb-4">Comments</h2>

    <!-- Comment list -->
    <div v-if="loading" class="text-sm text-gray-400 italic">Loading comments…</div>

    <div v-else-if="comments.length === 0" class="text-sm text-gray-400 italic mb-4">
      No comments yet. Be the first to comment!
    </div>

    <ul v-else class="space-y-4 mb-6">
      <li v-for="comment in comments" :key="comment.id" class="flex gap-3">
        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-primary-100 text-primary-700 flex items-center justify-center text-xs font-bold uppercase">
          {{ comment.user.name.charAt(0) }}
        </div>
        <div class="flex-1 min-w-0">
          <div class="flex items-baseline gap-2">
            <span class="text-sm font-medium text-gray-900">{{ comment.user.name }}</span>
            <span class="text-xs text-gray-400">{{ formatDate(comment.created_at) }}</span>
          </div>
          <p class="text-sm text-gray-700 mt-0.5 whitespace-pre-wrap">{{ comment.content }}</p>
        </div>
      </li>
    </ul>

    <!-- New comment form -->
    <form @submit.prevent="submitComment" class="border-t border-gray-100 pt-4 space-y-3">
      <div>
        <label for="comment-content" class="block text-sm font-medium text-gray-700 mb-1">Add a comment</label>
        <textarea
          id="comment-content"
          v-model="content"
          rows="3"
          maxlength="1000"
          placeholder="Write a comment…"
          class="block w-full px-3 py-2 border rounded-md shadow-sm text-gray-900 placeholder-gray-400 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
          :class="contentError ? 'border-red-400' : 'border-gray-300'"
        ></textarea>
        <p v-if="contentError" class="mt-1 text-xs text-red-600">{{ contentError }}</p>
      </div>

      <div v-if="submitError" class="rounded-md bg-red-50 p-3">
        <p class="text-sm text-red-700">{{ submitError }}</p>
      </div>

      <div class="flex justify-end">
        <button
          type="submit"
          :disabled="submitting"
          class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
        >
          <span v-if="submitting">Posting…</span>
          <span v-else>Post comment</span>
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue';
import api from '../composables/useApi.js';

const props = defineProps({
  taskId: {
    type: Number,
    required: true,
  },
});

const comments = ref([]);
const loading = ref(true);

const content = ref('');
const contentError = ref('');
const submitError = ref('');
const submitting = ref(false);

const fetchComments = async () => {
  loading.value = true;
  try {
    const response = await api.get(`/tasks/${props.taskId}/comments`);
    comments.value = response.data;
  } catch (err) {
    console.error('Failed to fetch comments:', err);
  } finally {
    loading.value = false;
  }
};

const submitComment = async () => {
  contentError.value = '';
  submitError.value = '';
  submitting.value = true;
  try {
    await api.post(`/tasks/${props.taskId}/comments`, { content: content.value });
    content.value = '';
    await fetchComments();
  } catch (err) {
    const errors = err.response?.data?.errors;
    if (errors?.content) {
      contentError.value = errors.content[0];
    } else {
      submitError.value = err.response?.data?.message || 'Failed to post comment. Please try again.';
    }
  } finally {
    submitting.value = false;
  }
};

const formatDate = (dateStr) => {
  if (!dateStr) return '';
  return new Date(dateStr).toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });
};

onMounted(fetchComments);
</script>
