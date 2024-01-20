import "./assets/styles/main.css";

import { createApp } from "vue";
import App from "./App.vue";
import router from "./router";
import "./config/axios";
import store from "./config/vuex";

const app = createApp(App);

app.use(router);
app.use(store);

app.mount("#app");
