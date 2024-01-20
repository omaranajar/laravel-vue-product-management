<template>
  <div class="center-container">
    <div class="container">
      <header>Register</header>
      <form>
        <div class="field name-field">
          <div class="input-field">
            <input
              @keyup="isValidate"
              type="text"
              v-model="model.registrationData.name"
              placeholder="Enter your name"
              class="name"
            />
          </div>
          <span v-if="validationErrors.name" class="error name-error">
            <i class="bx bx-error-circle error-icon"></i>
            <p class="error-text">Please enter a valid name</p>
          </span>
        </div>
        <div class="field email-field">
          <div class="input-field">
            <input
              @keyup="isValidate"
              type="email"
              v-model="model.registrationData.email"
              placeholder="Enter your email"
              class="email"
            />
          </div>
          <span v-if="validationErrors.email" class="error email-error">
            <i class="bx bx-error-circle error-icon"></i>
            <p class="error-text">{{ validationErrors.email }}</p>
          </span>
        </div>
        <div class="field create-password">
          <div class="input-field">
            <input
              @keyup="isValidate"
              type="password"
              v-model="model.registrationData.password"
              placeholder="Create password"
              class="password"
            />
            <i
              @click="hideAndShowPassword"
              class="fa-solid fa-eye show-hide"
            ></i>
          </div>
          <span
            v-if="validationErrors.createPassword"
            class="error password-error"
          >
            <i class="bx bx-error-circle error-icon"></i>
            <p class="error-text">
              {{ validationErrors.createPassword }}
            </p>
          </span>
        </div>
        <div class="field confirm-password">
          <div class="input-field">
            <input
              @keyup="isValidate"
              type="password"
              v-model="model.registrationData.password_confirmation"
              placeholder="Confirm password"
              class="cPassword"
            />
            <i
              @click="hideAndShowPassword"
              class="fa-solid fa-eye show-hide"
            ></i>
          </div>
          <span
            v-if="validationErrors.confirmPassword"
            class="error cPassword-error"
          >
            <i class="bx bx-error-circle error-icon"></i>
            <p class="error-text">{{ validationErrors.confirmPassword }}</p>
          </span>
        </div>
        <div class="input-field button">
          <button
            @click.prevent="register"
            :disabled="isLoading"
            type="submit"
            value="Save"
          >
            <span v-if="!isLoading">Signup Now</span>
            <div v-if="isLoading" class="lds-ellipsis">
              <div></div>
              <div></div>
              <div></div>
              <div></div>
            </div>
          </button>
        </div>
        <div class="sign_in">
          Already a member? <RouterLink to="/login">Signin now</RouterLink>
        </div>
      </form>
    </div>
  </div>
</template>
<script>
import axios from "axios";
export default {
  name: "register",
  data() {
    return {
      validationErrors: {
        name: "",
        email: "",
        createPassword: "",
        confirmPassword: "",
      },
      isSubmitted: false,
      isLoading: false,
      model: {
        registrationData: {
          name: "",
          email: "",
          password: "",
          password_confirmation: "",
        },
      },
    };
  },
  mounted() {},
  methods: {
    register() {
      this.isSubmitted = true;
      if (this.isValidate()) {
        this.isLoading = true;
        axios
          .post("register", this.model.registrationData)
          .then((res) => {
            this.isLoading = false;
            localStorage.setItem("user", JSON.stringify(res.data.data.user));
            localStorage.setItem("token", res.data.data.token);
            axios.defaults.headers.common["Authorization"] =
              "Bearer " + res.data.data.token;
            this.$store.dispatch("setUser", res.data.data.user);
            this.$router.push("/");
          })
          .catch((error) => {
            this.isLoading = false;
            if (error.response.status == 422) {
              this.validationErrors.name = error.response.data.errors.name[0];
              this.validationErrors.email = error.response.data.errors.email[0];
              this.validationErrors.createPassword =
                error.response.data.errors.password[0];
            } else if (error.response.status == 401) {
              this.validationErrors.confirmPassword =
                error.response.data.message;
            } else {
              this.validationErrors.confirmPassword =
                "Something went wrong, try later...";
            }
          });
      }
    },
    hideAndShowPassword(event) {
      // Hide and show password
      const eyeIcon = event.target;
      const passwordInput = eyeIcon.parentElement.querySelector("input"); //getting parent element of eye icon and selecting the password input
      if (passwordInput.type === "password") {
        eyeIcon.classList.replace("fa-eye", "fa-eye-slash");
        return (passwordInput.type = "text");
      }
      eyeIcon.classList.replace("fa-eye-slash", "fa-eye");
      passwordInput.type = "password";
    },
    isValidate() {
      if (this.isSubmitted) {
        let isValidated = true;

        const namePattern = /^[a-zA-Z]{3,}$/;
        if (!this.model.registrationData.name.match(namePattern)) {
          this.validationErrors.name = "Please enter a valid name";
          isValidated = false;
        } else {
          this.validationErrors.name = "";
        }

        const emaiPattern = /^[^ ]+@[^ ]+\.[a-z]{2,3}$/;
        if (!this.model.registrationData.email.match(emaiPattern)) {
          this.validationErrors.email = "Please enter a valid email";
          isValidated = false;
        } else {
          this.validationErrors.email = "";
        }

        const passPattern =
          /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;

        if (!this.model.registrationData.password.match(passPattern)) {
          this.validationErrors.createPassword =
            "Please enter atleast 8 charatcer with number, symbol, small and capital letter.";
          isValidated = false;
        } else {
          this.validationErrors.createPassword = "";
        }

        if (
          this.model.registrationData.password !=
          this.model.registrationData.password_confirmation
        ) {
          this.validationErrors.confirmPassword = "Password don't match";
        } else {
          this.validationErrors.confirmPassword = "";
        }
        return isValidated;
      }
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
  height: 100vh;
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
.container form {
  margin-top: 30px;
}
form .field {
  margin-bottom: 20px;
}
form .input-field {
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
.input-field button {
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
  /* display: none; */
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
.button button {
  color: #fff;
  font-size: 16px;
  font-weight: 400;
  background-color: #4070f4;
  cursor: pointer;
  transition: all 0.3s ease;
}
.button button:disabled {
  background-color: #7d9bee;
}
.button input:hover {
  background-color: #0e4bf1;
}
.button button:disabled:hover {
  background-color: #7d9bee;
}
.button button:hover {
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

/* Loading Animation */
.lds-ellipsis {
  display: inline-block;
  position: relative;
  width: 80px;
  height: 54px;
}
.lds-ellipsis div {
  position: absolute;
  top: 20px;
  width: 13px;
  height: 13px;
  border-radius: 50%;
  background: #fff;
  animation-timing-function: cubic-bezier(0, 1, 1, 0);
}
.lds-ellipsis div:nth-child(1) {
  left: 8px;
  animation: lds-ellipsis1 0.6s infinite;
}
.lds-ellipsis div:nth-child(2) {
  left: 8px;
  animation: lds-ellipsis2 0.6s infinite;
}
.lds-ellipsis div:nth-child(3) {
  left: 32px;
  animation: lds-ellipsis2 0.6s infinite;
}
.lds-ellipsis div:nth-child(4) {
  left: 56px;
  animation: lds-ellipsis3 0.6s infinite;
}
@keyframes lds-ellipsis1 {
  0% {
    transform: scale(0);
  }
  100% {
    transform: scale(1);
  }
}
@keyframes lds-ellipsis3 {
  0% {
    transform: scale(1);
  }
  100% {
    transform: scale(0);
  }
}
@keyframes lds-ellipsis2 {
  0% {
    transform: translate(0, 0);
  }
  100% {
    transform: translate(24px, 0);
  }
}

/* Responsive styles for phones */
@media only screen and (max-width: 450px) {
  .container {
    box-shadow: none;
  }
}
</style>
