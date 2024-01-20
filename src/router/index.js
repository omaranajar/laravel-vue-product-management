import { createRouter, createWebHistory } from "vue-router";
import HomeView from "../views/HomeView.vue";
import ProductIndex from "../views/products/index.vue";
import ProductCreate from "../views/products/create.vue";
import ProductEdit from "../views/products/edit.vue";
import Login from "../views/auth/login.vue";
import Register from "../views/auth/register.vue";

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: "/",
      name: "home",
      component: HomeView,
      meta: { requiresAuth: true },
    },
    {
      path: "/products",
      name: "products",
      component: ProductIndex,
      meta: { requiresAuth: true },
    },
    {
      path: "/products/create",
      name: "productCreate",
      component: ProductCreate,
      meta: { requiresAuth: true },
    },
    {
      path: "/products/edit/:id",
      name: "productEdit",
      component: ProductEdit,
      meta: { requiresAuth: true },
    },
    {
      path: "/login",
      name: "login",
      component: Login,
      meta: { requiresAuth: false },
    },
    {
      path: "/register",
      name: "register",
      component: Register,
      meta: { requiresAuth: false },
    },
  ],
});

router.beforeEach((to, from, next) => {
  // Check if the route requires authentication
  if (to.meta.requiresAuth) {
    // Check if the user is authenticated
    const isAuthenticated = checkAuthentication();

    if (isAuthenticated) {
      // Continue to the route
      next();
    } else {
      // Redirect to the login page
      next("/login");
    }
  } else if (to.meta.requiresAuth == false) {
    // Check if the user is authenticated
    const isAuthenticated = checkAuthentication();

    if (isAuthenticated) {
      // Continue to the route
      next("/");
    } else {
      // Redirect to the login page
      next();
    }
  } else {
    // If the route doesn't require authentication, continue
    next();
  }
});

// A method to get the authentication token from local storage
const getAuthToken = () => localStorage.getItem("token");

const checkAuthentication = () => {
  // Get the authentication token
  const authToken = getAuthToken();

  // Check if the token exists and is valid (you would have your own validation logic)
  return authToken !== null && authToken !== "undefined";
};

export default router;
