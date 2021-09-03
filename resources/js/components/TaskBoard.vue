<template>
  <div class="relative p-2 flex overflow-x-auto h-full">
    <div
      v-for="status in statuses"
      :key="status.slug"
      class="mr-6 w-4/5 max-w-xs flex-1 flex-shrink-0"
    >
      <div class="rounded-md shadow-md overflow-hidden">
        <div class="p-3 flex justify-between items-baseline bg-blue-800">
          <h4 class="font-medium text-white">{{ status.title }}</h4>
          <button class="py-1 px-2 text-sm text-orange-500 hover:underline">タスク追加</button>
        </div>
        <div class="p-2 flex-1 flex flex-col h-full overflow-x-hidden overflow-y-auto bg-blue-100">
          <!-- あとで、ここにタスク追加フォームを入れます-->

          <AddTaskForm
            v-if="newTaskForStatus === status.id"
            :status-id="status.id"
            v-on:task-added="handleTaskAdded"
            v-on:task-canceled="closeAddTaskForm"
          ></AddTaskForm>
          <!-- ./AddTaskForm -->
          <!-- Tasks -->
          <draggable
            class="flex-1 overflow-hidden"
            v-model="status.tasks"
            v-bind="taskDragOptions"
            @end="handleTaskMoved"
          >
            <transition-group
              class="flex-1 flex flex-col h-full overflow-x-hidden overflow-y-auto rounded shadow-xs"
              tag="div"
            >
              <div
                v-for="task in status.tasks"
                :key="task.id"
                class="mb-3 p-4 flex flex-col bg-white rounded-md shadow transform hover:shadow-md cursor-pointer"
              >
                <div class="flex justify-between">
                  <span class="block mb-2 text-xl text-gray-900">{{ task.title }}</span>
                  <div>
                    <button
                      aria-label="Delete task"
                      class="p-1 focus:outline-none focus:shadow-outline text-red-500 hover:text-red-600"
                      @click="onDelete(task.id, status.id)"
                    >
                      <!-- <Trash2Icon /> -->
                    </button>
                  </div>
                </div>
                <p class="text-gray-700">{{ task.description }}</p>
              </div>
              <!-- ./Tasks -->
            </transition-group>
          </draggable>

          <div
            v-show="!status.tasks.length && newTaskForStatus !== status.id"
            class="flex-1 p-4 flex flex-col items-center justify-center"
          >
            <span class="text-gray-600">タスクがありません</span>
            <button
              class="mt-1 text-sm text-orange-600 hover:underline"
              @click="openAddTaskForm(status.id)"
            >タスクを追加</button>
          </div>
          <!-- ./No Tasks -->
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import AddTaskForm from "./AddTaskForm"; //コンポーネントをインポートする
import draggable from 'vuedraggable';

export default {
  components: {
    draggable,
    AddTaskForm,


  }, // 登録

  props: {
    initialData: Array
  },
  data() {
    return {
      statuses: [],

      newTaskForStatus: 0 // 追加するステータスのID
    };
  },
   computed: {
        taskDragOptions () {
            return {
                animation: 200,
                group: "task-list",
                dragClass: "status-drag"
            };
        },
   
    },
  mounted() {
    // ステータスを「クローン」して、変更時にプロップを変更しないように
    this.statuses = JSON.parse(JSON.stringify(this.initialData));
  },
  methods: {
    // statusIdを設定し、フォームを表示
    openAddTaskForm(statusId) {
      this.newTaskForStatus = statusId;
    },
    // statusIdをリセットしてフォームを閉じる
    closeAddTaskForm() {
      this.newTaskForStatus = 0;
    },
    // ボードの正しい列にカードを追加
    handleTaskAdded(newTask) {
      // Find the index of the status where we should add the task
      const statusIndex = this.statuses.findIndex(
        status => status.id === newTask.status_id
      );

      // 新しく作成しカードをボードに追加
      this.statuses[statusIndex].tasks.push(newTask);

      // AddTaskFormを閉じる
      this.closeAddTaskForm();
    },
    handleTaskMoved (evt) {
            axios.put("/tasks/sync", {columns: this.statuses})
                .then(res => {
                    console.log(res.data);
                })
                .catch(err => {
                console.log(err.response);
            });
        },

  }
};
</script>