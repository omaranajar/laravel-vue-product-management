<template>
  <div class="container">
    <ProductModal
      :product="selectedProduct"
      :showModal="isModalVisible"
      @close="closeModal"
    ></ProductModal>
    <div class="styled-table mt-5 mb-4">
      <div class="table-header">
        <div class="first-row">
          <p>Product List</p>
          <RouterLink to="/products/create">
            <button class="add-new first">+ Add New</button></RouterLink
          >
        </div>
        <div class="second-row my-2">
          <div>
            <div class="input-field">
              <select
                @change="getProducts('products/filter')"
                v-model="filterCriteria.type"
                name="type"
                class="filter-type"
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

              <div class="input-field">
                <input
                  @keyup.enter="getProducts('products/filter')"
                  v-model="filterCriteria.min_price"
                  class="input-price"
                  type="number"
                  placeholder="Min Price"
                />
                <span class="search-icon">$</span>
              </div>
              <div class="input-field">
                <input
                  @keyup.enter="getProducts('products/filter')"
                  v-model="filterCriteria.max_price"
                  class="input-price"
                  type="number"
                  placeholder="Max Price"
                />
                <span class="search-icon">$</span>
              </div>
            </div>
            <div class="input-field">
              <input
                @keyup.enter="getProducts('products/filter')"
                v-model="filterCriteria.name"
                class="input-search"
                type="text"
                placeholder="Search..."
              />
              <i
                @click="getProducts('products/filter')"
                class="search-icon bx bx-search-alt"
              ></i>
            </div>
            <RouterLink to="/products/create">
              <button class="add-new">+ Add New</button></RouterLink
            >
          </div>
        </div>
      </div>
      <div class="table-section border mt-2">
        <table>
          <thead>
            <tr>
              <th>Name</th>
              <th>Image</th>
              <th>Description</th>
              <th>Price</th>
              <th>Size</th>
              <th>Type</th>
              <th>Created at</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody v-if="products && products.length > 0">
            <tr v-for="(product, index) in products" :key="index">
              <td>{{ product.name }}</td>
              <td>
                <img v-if="product.image" :src="product.image" />
                <img v-else src="../../assets/images/no-image.png" />
              </td>
              <td class="description-column">
                {{
                  product.description
                    ? product.description.slice(0, 20) + "... "
                    : "-"
                }}
              </td>
              <td>{{ product.price }}</td>
              <td>{{ product.size ? product.size : "-" }}</td>
              <td>
                <span :class="'type ' + product.type">{{ product.type }}</span>
              </td>
              <td>{{ formatDate(product.created_at) }}</td>
              <td>
                <div class="actions">
                  <button
                    @click="openModal(product)"
                    class="btn btn-primary m-1"
                  >
                    <i class="fa-solid fa-eye"></i>
                  </button>
                  <RouterLink
                    :to="{ path: '/products/edit/' + product.id }"
                    class="btn btn-warning m-1"
                    ><i
                      style="color: #fff"
                      class="fa-solid fa-pen-to-square"
                    ></i
                  ></RouterLink>
                  <button
                    type="button"
                    @click="deleteProduct(product.id, index)"
                    class="btn btn-danger m-1"
                  >
                    <div v-if="isLoading == index" class="lds-ring">
                      <div></div>
                      <div></div>
                      <div></div>
                      <div></div>
                    </div>
                    <i v-else class="fa-solid fa-trash-can"></i>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
          <tbody v-else-if="!products">
            <tr>
              <td colspan="7" class="text-center">Loading...</td>
            </tr>
          </tbody>
          <tbody v-else>
            <tr>
              <td colspan="7" class="text-center">You have no product yet!</td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="pagination">
        <button
          :disabled="productsMeta?.current_page == 1"
          @click="getProducts(productsLinks.first)"
        >
          <i class="bx bx-chevrons-left"></i>
        </button>
        <button
          :disabled="productsLinks?.prev == null"
          @click="getProducts(productsLinks?.prev)"
        >
          <i class="bx bx-chevron-left"></i>
        </button>
        <button
          v-for="page in slidingPages"
          :key="page.url"
          :class="{ active: page.active }"
          :disabled="page.active"
          @click="getProducts(page.url)"
        >
          {{ page.label }}
        </button>
        <button
          :disabled="productsLinks?.next == null"
          @click="getProducts(productsLinks?.next)"
        >
          <i class="bx bx-chevron-right"></i>
        </button>
        <button
          :disabled="productsMeta?.current_page == productsMeta?.last_page"
          @click="getProducts(productsLinks?.last)"
        >
          <i class="bx bx-chevrons-right"></i>
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";
import { format } from "date-fns";
import enUS from "date-fns/locale/en-US";
import ProductModal from "./Show.vue";
import { backendStorageUrl } from "../../config/config";

export default {
  name: "products",
  components: {
    ProductModal,
  },
  data() {
    return {
      currentPage: 1,
      totalPages: 1,
      slidingPages: [],
      filterCriteria: {
        name: "",
        type: "",
        min_price: "",
        max_price: "",
      },
      isModalVisible: false,
      selectedProduct: null,
      products: null,
      productsLinks: null,
      productsMeta: null,
      isLoading: null,
      errorMsg: null,
    };
  },
  mounted() {
    this.getProducts();
  },
  methods: {
    deleteProduct(productId, index) {
      if (index !== -1) {
        this.isLoading = index;

        axios
          .delete("products/" + productId)
          .then((res) => {
            this.isLoading = null;
            this.products.splice(index, 1);
          })
          .catch((error) => {
            this.isLoading = null;
            if (error.response?.status == 401) {
              this.errorMsg = "You're not authorized to make this request.";
              this.$router.push("/login");
            } else if (error.response?.status == 404) {
              this.errorMsg = "No product found";
            } else if (error.response?.status == 403) {
              this.errorMsg = "You are forbidden to make this request";
            } else {
              this.errorMsg = "Something went wrong, try later...";
            }
          });
      }
    },

    async getProducts(url = "products") {
      this.products = null;
      this.productsLinks = null;
      this.productsMeta = null;
      try {
        const res = await axios.get(url, { params: this.filterCriteria });
        this.products = res.data.data;

        // Add backendStorageUrl to each product image
        this.products = this.products.map((product) => {
          return {
            ...product,
            image: product.image
              ? `${backendStorageUrl}${product.image}`
              : null,
          };
        });

        this.productsLinks = res.data.links;
        this.productsMeta = res.data.meta;
        this.isLoading = null;
        this.updateSlidingPages();
      } catch (error) {
        this.isLoading = null;
        if (error?.response?.status == 401) {
          this.errorMsg = "You're not authorized to make this request.";
          this.$router.push("/login");
        } else if (error?.response?.status == 403) {
          this.errorMsg = "You are forbidden to make this request";
        } else if (error?.response?.status == 404) {
          this.errorMsg = "Endpoint not found";
        } else {
          this.errorMsg = "Something went wrong, try later...";
        }
      }
    },

    updateSlidingPages() {
      const range = 2; // Number of page links to show on each side of the current page

      this.currentPage = this.productsMeta?.current_page;
      this.totalPages = this.productsMeta?.last_page;
      const path = this.productsMeta?.path;

      let start = Math.max(1, this.currentPage - range);
      let end = Math.min(this.totalPages, this.currentPage + range);

      this.slidingPages = Array.from(
        { length: end - start + 1 },
        (_, i) => start + i
      ).map((page) => {
        return {
          url: `${path}?page=${page}`,
          label: page,
          active: page === this.currentPage,
        };
      });
    },

    formatDate(backendDateString) {
      const dateObject = new Date(backendDateString);
      const formattedDate = format(dateObject, "dd MMM, yyyy HH:mm", {
        locale: enUS,
      });
      return formattedDate;
    },

    openModal($product) {
      this.isModalVisible = true;
      this.selectedProduct = $product;
    },

    closeModal() {
      this.isModalVisible = false;
    },
  },
};
</script>

<style scoped>
/* Google Fonts - Poppins */
@import url("https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap");

* {
  font-family: "Poppins", sans-serif;
  transition: all 0.3s ease;
}
.styled-table {
  width: 100%;
}
.table-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  padding: 15px 20px;
  background-color: rgb(240, 240, 240);
  border-radius: 8px;
}
.table-header p {
  color: black;
  margin: 0;
}
button {
  outline: none;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s ease;
}
.add-new {
  padding: 10px 20px;
  color: #fff;
  background-color: #4070f4;
}
.add-new:hover {
  background-color: #0e4bf1;
}
.add-new.first {
  display: none;
}
.input-search,
.input-price,
.filter-type {
  padding: 10px 20px;
  padding-right: 30px;
  margin: 0 5px;
  outline: none;
  /* border: 1px solid #4070f4; */
  border: 1px solid #ddd;
  border-radius: 8px;
  /* color: #0298cf; */
  width: 152px;
}
.search-icon {
  position: absolute;
  right: 13px;
  top: 50%;
  transform: translateY(-50%);
  font-size: 18px;
  color: #919191;
  cursor: pointer;
  padding: 5px;
}
.input-field {
  position: relative;
  display: inline;
}
.table-section {
  /* height: 650px; */
  overflow: auto;
  border-radius: 8px;
}
table {
  width: 100%;
  /* table-layout: fixed; */
  min-width: 1000px;
  border-collapse: collapse;
}
thead th {
  position: sticky;
  top: 0;
  background-color: #f6f9fc;
  color: #8493a5;
  font-size: 15px;
}
th,
td {
  border-bottom: 1px solid #ddd;
  padding: 10px 20px;
  /* word-break: break-all; */
  text-align: center;
}
td img {
  height: 60px;
  width: 60px;
  object-fit: cover;
  border-radius: 8px;
  border: 1px solid #ddd;
}
td .type {
  padding: 4px 8px;
  border-radius: 8px;
  color: #fff;
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

tr:hover td {
  color: #4070f4;
  cursor: pointer;
  background-color: #f6f9fc;
}
.pagination {
  display: flex;
  justify-content: flex-end;
  width: 100%;
  padding: 10px 20px;
}
.pagination button {
  padding: 10px;
  /* border: 2px solid #d5d0d0; */
  height: 40px;
  width: 40px;
  border-radius: 8px;
  display: flex;
  justify-content: center;
  align-items: center;
  background-color: #4070f4;
  color: #fff;
  /* box-shadow: 0px 0px 4px 0px rgba(0, 0, 0, 0.75); */
  margin: 0 5px;
  cursor: pointer;
  transition: all 0.3s ease;
}
.pagination button:hover {
  background-color: #0e4bf1;
}
.pagination button:disabled {
  background-color: #7d9bee;
}
.pagination button:disabled:hover {
  background-color: #7d9bee;
}
.pagination button.active {
  background-color: #fb8500;
}
.pagination button.active:hover {
  background-color: #fb8500;
}

.description-column {
  min-width: 220px;
}

::-webkit-scrollbar {
  height: 5px;
  width: 5px;
}
::-webkit-scrollbar-track {
  box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.3);
  border-radius: 8px;
}
::-webkit-scrollbar-thumb {
  box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.3);
  border-radius: 8px;
}
.actions {
  min-width: 190px;
}

/* Loading animation */

.lds-ring {
  display: inline-block;
  position: relative;
  width: 19px;
  height: 19px;
}
.lds-ring div {
  box-sizing: border-box;
  display: block;
  position: absolute;
  width: 19px;
  height: 19px;
  margin-top: 4px;
  border: 3.5px solid #fff;
  border-radius: 50%;
  animation: lds-ring 1.2s cubic-bezier(0.5, 0, 0.5, 1) infinite;
  border-color: #fff transparent transparent transparent;
}
.lds-ring div:nth-child(1) {
  animation-delay: -0.45s;
}
.lds-ring div:nth-child(2) {
  animation-delay: -0.3s;
}
.lds-ring div:nth-child(3) {
  animation-delay: -0.15s;
}
@keyframes lds-ring {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
}
@media only screen and (max-width: 992px) {
  .second-row {
    margin: auto;
    margin-top: 1rem;
  }
  .add-new {
    width: 100%;
    margin-top: 1rem;
    display: none;
  }
  .add-new.first {
    width: 100%;
    display: inline;
    margin: 0;
  }
  .first-row {
    width: 100%;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
  }
}
@media only screen and (max-width: 767px) {
  .input-search,
  .input-price,
  .filter-type {
    width: 47%;
    /* margin: 0; */
    margin-top: 5px;
  }
}

/* Responsive styles for phones */
@media only screen and (max-width: 460px) {
  .table-header .first-row {
    width: 100%;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
  }
  .table-header .second-row {
    width: 100%;
  }
  .input-search,
  .input-price,
  .filter-type {
    width: 100%;
    margin: 0;
    margin-top: 1rem;
  }
  .add-new {
    width: 100%;
    margin-top: 1rem;
    display: none;
  }
  .add-new.first {
    width: 100%;
    display: inline;
    margin: 0;
  }
  .second-row {
    margin: auto;
    margin-top: 0;
  }
  .pagination {
    justify-content: center;
  }
}
</style>
