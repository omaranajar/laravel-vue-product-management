<template>
  <nav v-if="user" class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
      <RouterLink class="navbar-brand" to="/">SquaHR P-M</RouterLink>
      <button
        class="navbar-toggler"
        type="button"
        data-bs-toggle="collapse"
        data-bs-target="#navbarSupportedContent"
        aria-controls="navbarSupportedContent"
        aria-expanded="false"
        aria-label="Toggle navigation"
      >
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <RouterLink class="nav-link" to="/" exact-active-class="active"
              >Home</RouterLink
            >
          </li>
          <li class="nav-item">
            <RouterLink
              class="nav-link"
              to="/products"
              exact-active-class="active"
              >Products</RouterLink
            >
          </li>
          <li v-if="!user" class="nav-item">
            <RouterLink class="nav-link" to="/login" exact-active-class="active"
              >Login</RouterLink
            >
          </li>
          <li v-if="!user" class="nav-item">
            <RouterLink
              class="nav-link"
              to="/register"
              exact-active-class="active"
              >Register</RouterLink
            >
          </li>
          <li v-if="user" class="nav-item">
            <a
              @click="logout"
              class="nav-link"
              href="javascript:void(0)"
              exact-active-class="active"
              >Logout</a
            >
          </li>
        </ul>
      </div>
    </div>
  </nav>
</template>
<script>
import axios from "axios";
export default {
  name: "Nav",
  data() {
    return {
      errorMsg: "",
    };
  },
  mounted() {},
  methods: {
    logout() {
      axios
        .post("logout")
        .then((res) => {
          localStorage.removeItem("user");
          localStorage.removeItem("token");
          this.$store.dispatch("setUser", null);
          this.$router.push("/login");
        })
        .catch((error) => {
          this.errorMsg = "Something went wrong, try later...";
        });
    },
  },
  computed: {
    user() {
      return this.$store.getters.user;
    },
  },
};
</script>
