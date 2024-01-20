<template>
  <main>
    <div class="center-container">
      <div class="container">
        <header class="text-center">
          🚀 Welcome to SquaHR Product Management! 🚀
        </header>
        <p>
          Elevate your product management experience with our user-friendly
          tools. Explore, filter, and manage your products effortlessly
        </p>

        <div class="input-field button">
          <input
            @click.prevent="toProducts()"
            to="/products"
            type="submit"
            value="Manage Products"
          />
        </div>
      </div>
    </div>
  </main>
</template>

<script>
export default {
  name: "home",
  methods: {
    toProducts() {
      this.$router.push("/products");
    },
  },
  computed: {
    user() {
      return this.$store.getters.user;
    },
  },
};
</script>

<style scoped>
/* Google Fonts - Poppins */
@import url("https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap");

* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: "Poppins", sans-serif;
}
body {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #4070f4;
}

.center-container {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 90vh;
}
.container {
  position: relative;
  max-width: 370px;
  width: 100%;
  padding: 25px;
  border-radius: 8px;
  background-color: #fff;
  box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px 0px;
}
.container header {
  font-size: 22px;
  font-weight: 600;
  color: #333;
}
.container p {
  margin-top: 30px;
}
.container form {
  margin-top: 30px;
}
form .field {
  margin-bottom: 20px;
}
.input-field {
  position: relative;
  height: 55px;
  width: 100%;
}
.input-field input {
  height: 100%;
  width: 100%;
  outline: none;
  border: none;
  border-radius: 8px;
  padding: 0 15px;
  border: 1px solid #d1d1d1;
}
.invalid input {
  border-color: #d93025;
}
.input-field .show-hide {
  position: absolute;
  right: 13px;
  top: 50%;
  transform: translateY(-50%);
  font-size: 18px;
  color: #919191;
  cursor: pointer;
  padding: 3px;
}
.field .error {
  display: flex;
  align-items: center;
  margin-top: 6px;
  color: #d93025;
  font-size: 13px;
  display: none;
}
.invalid .error {
  display: flex;
}
.error .error-icon {
  margin-right: 6px;
  font-size: 15px;
}
.create-password .error {
  align-items: flex-start;
}
.create-password .error-icon {
  margin-top: 4px;
}
.button {
  margin: 25px 0 6px;
}
.button input {
  color: #fff;
  font-size: 16px;
  font-weight: 400;
  background-color: #4070f4;
  cursor: pointer;
  transition: all 0.3s ease;
}
.button input:hover {
  background-color: #0e4bf1;
}
form .sign_in {
  text-align: center;
  margin-top: 25px;
}
.sign_up a {
  color: #4070f4;
}
form a {
  text-decoration: none;
}
form a:hover {
  text-decoration: underline;
}

/* Responsive styles for phones */
@media only screen and (max-width: 450px) {
  .container {
    box-shadow: none;
  }
}
</style>
