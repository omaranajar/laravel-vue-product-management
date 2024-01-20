<template>
  <div class="center-container mt-4">
    <div class="container my-4">
      <header>Add Product</header>
      <form enctype="multipart/form-data">
        <div class="image-container">
          <div class="avatar-upload">
            <div class="avatar-edit">
              <input
                type="file"
                id="imageUpload"
                ref="imageUpload"
                @change="handleImageChange"
                accept=".png, .jpg, .jpeg"
              />
              <label for="imageUpload"></label>
            </div>
            <div class="avatar-preview">
              <div
                id="imagePreview"
                style="background-image: url('/src/assets/images/no-image.png')"
              ></div>
            </div>
            <div class="field mt-4">
              <span v-if="validationErrors.image" class="error image-error">
                <i class="bx bx-error-circle error-icon"></i>
                <p class="error-text">{{ validationErrors.image }}</p>
              </span>
            </div>
          </div>
        </div>
        <div class="field name-field">
          <div class="input-field">
            <input
              @keyup="isValidate"
              type="text"
              name="name"
              placeholder="Name"
              class="name"
              v-model="model.product.name"
              required
            />
          </div>
          <span v-if="validationErrors.name" class="error name-error">
            <i class="bx bx-error-circle error-icon"></i>
            <p class="error-text">{{ validationErrors.name }}</p>
          </span>
        </div>
        <div class="field description-field">
          <div class="input-field input-field-description">
            <textarea
              @keyup="isValidate"
              class="description"
              name="description"
              placeholder="Description..."
              rows="2"
              v-model="model.product.description"
            ></textarea>
          </div>
          <span
            v-if="validationErrors.description"
            class="error description-error"
          >
            <i class="bx bx-error-circle error-icon"></i>
            <p class="error-text">{{ validationErrors.description }}</p>
          </span>
        </div>
        <div class="field price-field">
          <div class="input-field">
            <input
              @keyup="isValidate"
              type="number"
              name="price"
              placeholder="price"
              class="price"
              v-model="model.product.price"
              required
            />
          </div>
          <span v-if="validationErrors.price" class="error price-error">
            <i class="bx bx-error-circle error-icon"></i>
            <p class="error-text">{{ validationErrors.price }}</p>
          </span>
        </div>
        <div class="field size-field">
          <div class="input-field">
            <select
              @change="isValidate"
              name="size"
              placeholder="size"
              class="size"
              v-model="model.product.size"
            >
              <option value="">Size...</option>
              <option value="small">Small</option>
              <option value="medium">Medium</option>
              <option value="large">Large</option>
            </select>
          </div>
          <span v-if="validationErrors.size" class="error size-error">
            <i class="bx bx-error-circle error-icon"></i>
            <p class="error-text">{{ validationErrors.size }}</p>
          </span>
        </div>
        <div class="field type-field">
          <div class="input-field">
            <select
              @change="isValidate"
              v-model="model.product.type"
              class="type"
              name="type"
              id=""
            >
              <option value="">Type...</option>
              <option value="electronics">Electronics</option>
              <option value="clothing">Clothing</option>
              <option value="furniture">Furniture</option>
              <option value="appliances">Appliances</option>
              <option value="books">Books</option>
              <option value="toys">Toys</option>
              <option value="footwear">Footwear</option>
            </select>
          </div>
          <span v-if="validationErrors.type" class="error type-error">
            <i class="bx bx-error-circle error-icon"></i>
            <p class="error-text">{{ validationErrors.type }}</p>
          </span>
        </div>
        <div class="input-field button">
          <button
            @click.prevent="storeProduct"
            :disabled="isLoading"
            type="submit"
            value="Save"
          >
            <span v-if="!isLoading">Save</span>
            <div v-if="isLoading" class="lds-ellipsis">
              <div></div>
              <div></div>
              <div></div>
              <div></div>
            </div>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>
<script>
import axios from "axios";
export default {
  name: "productCreate",
  data() {
    return {
      validationErrors: {
        name: "",
        image: "",
        description: "",
        price: "",
        size: "",
        type: "",
      },
      errorMsg: "",
      isLoading: false,
      isSubmitted: false,
      model: {
        product: {
          name: "",
          image: "",
          description: "",
          price: "",
          size: "",
          type: "",
        },
      },
    };
  },
  methods: {
    storeProduct() {
      this.isSubmitted = true;
      if (this.isValidate()) {
        this.isLoading = true;

        axios
          .post("products", this.model.product, {
            headers: {
              "Content-Type": "multipart/form-data",
            },
          })
          .then((res) => {
            this.isLoading = false;
            this.model.product = {
              name: "",
              image: "",
              description: "",
              price: "",
              size: "",
              type: "",
            };
            this.validationErrors = "";
            this.$router.push("/products");
          })
          .catch((error) => {
            this.isLoading = false;
            if (error.response.status == 422) {
              const errorFields = [
                "name",
                "image",
                "description",
                "price",
                "size",
                "type",
              ];

              errorFields.forEach((field) => {
                this.validationErrors[field] =
                  error?.response?.data?.errors?.[field]?.[0] ?? "";
              });
            } else if (error.response.status == 401) {
              this.errorMsg = "You're not authorized to make this request.";
              this.$router.push("/login");
            } else if (error.response.status == 403) {
              this.errorMsg = "You are forbidden to make this request";
            } else {
              this.errorMsg = "Something went wrong, try later...";
            }
          });
      }
    },

    handleImageChange() {
      this.validationErrors.image = "";
      const input = this.$refs.imageUpload;
      // Check if files are present in the input
      if (input.files && input.files[0]) {
        this.model.product.image = input.files[0];
        // Create a new FileReader object
        const reader = new FileReader();

        // Define an onload event handler for when the FileReader has loaded the file
        reader.onload = (e) => {
          // Set the background image of an element with id 'imagePreview' to the loaded image URL
          document.getElementById("imagePreview").style.backgroundImage =
            "url(" + e.target.result + ")";
        };

        reader.readAsDataURL(input.files[0]);
      }
    },

    isValidate() {
      if (this.isSubmitted) {
        let isValidated = true;

        if (!this.model.product.name) {
          this.validationErrors.name = "The name field is required.";
          isValidated = false;
        } else {
          this.validationErrors.name = "";
        }

        if (!this.model.product.price) {
          this.validationErrors.price = "The price field is required.";
          isValidated = false;
        } else {
          this.validationErrors.price = "";
        }

        if (!this.model.product.type) {
          this.validationErrors.type = "The type field is required.";
          isValidated = false;
        } else {
          this.validationErrors.type = "";
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
  /* height: 100vh; */
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
form .input-field-description {
  height: auto;
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
.input-field textarea {
  height: 100%;
  width: 100%;
  outline: none;
  border: none;
  border-radius: 8px;
  padding: 15px;
  border: 1px solid #d1d1d1;
}
.input-field select {
  height: 100%;
  width: 100%;
  outline: none;
  border: none;
  border-radius: 8px;
  padding: 15px;
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

/* Star Loading Animation */
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
/* End Loading Animation */

body {
  background: whitesmoke;
  font-family: "Open Sans", sans-serif;
}

/* Start Image uploader */
.image-container {
  max-width: 960px;
  /* margin: 30px auto;
  padding: 20px; */
}

.avatar-upload {
  position: relative;
  max-width: 205px;
  margin: auto;
  margin-bottom: 20px;
  .avatar-edit {
    position: absolute;
    right: 12px;
    z-index: 1;
    top: 10px;
    input {
      display: none;
      + label {
        display: inline-block;
        width: 34px;
        height: 34px;
        margin-bottom: 0;
        border-radius: 100%;
        background: #ffffff;
        border: 1px solid #d1d1d1;
        box-shadow: 0px 2px 4px 0px rgba(0, 0, 0, 0.12);
        cursor: pointer;
        font-weight: normal;
        transition: all 0.2s ease-in-out;
        &:hover {
          background: #f1f1f1;
          border-color: #d6d6d6;
        }
        &:after {
          content: "\f040";
          font-family: "FontAwesome";
          color: #757575;
          position: absolute;
          top: 13%;
          left: 0;
          right: 0;
          text-align: center;
          margin: auto;
        }
      }
    }
  }
  .avatar-preview {
    width: 192px;
    height: 192px;
    position: relative;
    border-radius: 100%;
    border: 1px solid #d1d1d1;
    box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px 0px;
    > div {
      width: 100%;
      height: 100%;
      border-radius: 100%;
      background-size: cover;
      background-repeat: no-repeat;
      background-position: center;
    }
  }
}
/* End Image uploader */

/* Responsive styles for phones */
@media only screen and (max-width: 450px) {
  .container {
    box-shadow: none;
  }
}
</style>
