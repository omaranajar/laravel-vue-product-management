<template lang="">
  <transition name="modal">
    <div v-if="showModal" class="center-container">
      <div
        class="container m-4"
        ref="container"
        v-on:click="handleClickOutside"
      >
        <button @click="closeModal" class="close-btn">
          <i class="gg-close"></i>
        </button>
        <div class="product_image_container">
          <!-- <img src="../../assets/images/img.jpg" class="product_image" /> -->
          <img
            v-if="product.image"
            :src="product.image"
            class="product_image"
          />
          <img
            v-else
            src="../../assets/images/no-image.png"
            class="product_image"
          />
          <p :class="'type ' + product.type">{{ product?.type }}</p>
        </div>

        <h3 class="product_title">{{ product?.name }}</h3>
        <div class="price_type_container">
          <p class="price">{{ product?.price + " $" }}</p>
          <p class="size">{{ product?.size }}</p>
        </div>
        <div v-if="showFullDescription" class="description">
          {{ product?.description + " " }}
        </div>
        <div v-else class="description">
          {{ truncatedDescription }}
        </div>
        <a
          v-if="product?.description.length > 100"
          class="toggleDescription"
          href="javascript:void(0)"
          @click="toggleDescription"
        >
          {{ showFullDescription ? "See Less" : "See More" }}
        </a>
      </div>
    </div>
  </transition>
</template>
<script>
import { ref } from "vue";
import { onClickOutside } from "@vueuse/core";

export default {
  name: "productModal",
  setup({}, { emit }) {
    // Create a ref for the container element
    const container = ref(null);
    // Accessing showFullDescription from the data function
    const showFullDescription = ref(false);

    // Use onClickOutside to detect clicks outside the container
    onClickOutside(container, () => {
      showFullDescription.value = false;
      emit("close");
    });

    return {
      container,
      showFullDescription,
    };
  },
  props: {
    showModal: Boolean,
    product: Object,
  },
  data() {
    return {
      maxDescriptionLength: 100, // Set your desired max length
    };
  },
  mounted() {},
  methods: {
    toggleDescription() {
      this.showFullDescription = !this.showFullDescription;
    },
    closeModal() {
      this.showFullDescription = false;
      this.$emit("close");
    },
  },
  computed: {
    truncatedDescription() {
      if (this.product?.description.length > 100) {
        return (
          this.product?.description.slice(0, this.maxDescriptionLength) + "... "
        );
      } else {
        return this.product?.description;
      }
    },
  },
};
</script>

<style scoped>
/* Google Fonts - Poppins */
@import url("https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap");

.moda-enter-active,
.modal-leave-active {
  transition: all 0.25s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
  transform: scale(1.1);
}

* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: "Poppins", sans-serif;
  text-transform: capitalize;
}

body {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #4070f4;
}

.center-container {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  min-height: 100vh;
  /* height: 100%; */
  background-color: rgba(0, 0, 0, 0.3);
  z-index: 2;
  display: flex;
  justify-content: center;
  align-items: baseline;
}
.container {
  position: relative;
  max-width: 370px;
  width: 100%;
  padding: 25px;
  border-radius: 8px;
  background-color: #fff;
  box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px 0px;
  margin-top: 80px !important;
}
.close-btn {
  background: #212529;
  color: #fff;
  border: none;
  border-radius: 100%;
  padding: 11px;
  position: absolute;
  top: -18px;
  right: -18px;
  box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px 0px;
  transition: all 0.3s ease;
}
.close-btn:hover {
  background: #bb2d3b;
}
.price_type_container {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  margin-bottom: 20px;
}

.price_type_container .price {
  border-radius: 8px;
  padding: 5px 10px;
  background-color: #4070f4;
  color: #fff;
}
.description {
  text-transform: math-auto;
  display: contents;
}
.toggleDescription {
  color: #212529;
}
.type {
  border-radius: 8px;
  padding: 5px 10px;
  color: #fff;
  position: absolute;
  right: 10px;
  bottom: 30px;
  box-shadow: rgba(100, 100, 111, 0.2) 0px 7px 29px 0px;
}

.type.electronics {
  background-color: #0e4c6d;
}
.type.clothing {
  background-color: #72bad5;
}
.type.furniture {
  background-color: #ef4043;
}
.type.appliances {
  background-color: #c86bfa;
}
.type.books {
  background-color: #fb8500;
}
.type.toys {
  background-color: #ffd500;
}
.type.footwear {
  background-color: #99ca3c;
}

.size {
  border-radius: 8px;
  padding: 5px 10px;
  background-color: #f4a261;
  color: #fff;
}

.product_title {
  margin-bottom: 20px;
}

.product_image {
  border-top-left-radius: 8px;
  border-top-right-radius: 8px;
  margin-bottom: 20px;
  width: 100%;
  object-fit: cover;
  max-height: 320px;
}
.product_image_container {
  position: relative;
}
/* Responsive styles for phones */
/* @media only screen and (max-width: 450px) {
  .container {
    box-shadow: none;
  }
} */
</style>
