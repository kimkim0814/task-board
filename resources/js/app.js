require('./bootstrap');

window.Vue = require('vue').default;

Vue.component("task-board", require("./components/TaskBoard.vue").default);

const app = new Vue({
  el: "#app"
});