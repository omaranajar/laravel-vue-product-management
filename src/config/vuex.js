import { createStore } from "vuex";

const store = createStore({
  state: {
    user: null,
    selectedProduct: null,
  },
  getters: {
    user: (state) => state.user,
    selectedProduct: (state) => state.selectedProduct,
  },
  mutations: {
    setUser: (state, user) => {
      state.user = user;
    },
    setSelectedProduct: (state, product) => {
      state.selectedProduct = product;
    },
  },
  actions: {
    setUser: ({ commit }, user) => {
      commit("setUser", user);
    },
    setSelectedProduct: ({ commit }, product) => {
      commit("setSelectedProduct", product);
    },
  },
});

export default store;
