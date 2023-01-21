<template>
  <div>
    <!-- breadcrumbs Start -->
    <breadcrumbs :items="breadcrumbs" :current="breadcrumbsCurrent" />
    <!-- breadcrumbs end -->

    <div class="row">
      <div class="col-lg-12">
        <div class="w-100">
          <div>
            <div class="row">
              <div class="col-xl-2 col-md-3 col-4">
                <search v-model="query" @reset-pagination="resetPagination()" @reload="reload" />
              </div>
              <div class="col-xl-10 col-md-9 col-8 text-right">
                <div class="btn-group">
                  <router-link :to="{ name: 'user.create' }" class="btn btn-primary">
                    {{ $t("common.create") }}
                    <i class="fas fa-plus-circle d-none d-sm-inline-block" />
                  </router-link>
                </div>
              </div>
            </div>
          </div>
          <!-- /.card-header -->

          <div class="card-body p-0">
            <div class="table-responsive table-custom mt-3" id="printMe">
              <table class="table">
                <thead>
                  <tr>
                    <th>{{ $t("common.s_no") }}</th>
                    <th>{{ $t("common.name") }}</th>
                    <th>{{ $t("common.email") }}</th>
                    <th>{{ $t("common.role") }}</th>
                    <th>{{ $t("common.created_at") }}</th>
                    <th class="text-right no-print">
                      {{ $t("common.action") }}
                    </th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-show="items.length" v-for="(data, i) in items" :key="i">
                    <td>
                      <span v-if="pagination.current_page > 1">
                        {{
                            pagination.per_page * (pagination.current_page - 1) +
                            (i + 1)
                        }}
                      </span>
                      <span v-else>{{ i + 1 }}</span>
                    </td>
                    <td>
                      <div class="profile_wrap">
                        <img :src="data.photo_url" :alt="data.name" class="" />
                        {{ data.name }}
                      </div>
                    </td>
                    <td>
                      <a :href="`mailto:${data.email}`">{{ data.email }}</a>
                    </td>
                    <td>
                      {{ data.roles[0] }}
                    </td>
                    <td v-if="data.created_at">
                      {{ data.created_at | moment('Do MMM, YYYY') }}
                    </td>
                    <td class="text-right no-print">
                      <div v-if="
                        developer ||
                        (data.slug != 'developer' &&
                          data.slug != 'super-admin')
                      " class="btn-group">
                        <router-link v-if="data && data.slug" v-tooltip="$t('common.edit')" :to="{
                          name: 'user.edit',
                          params: { slug: data.slug },
                        }" class="btn btn-info btn-sm">
                          <i class="fas fa-edit" />
                        </router-link>
                        <a v-tooltip="$t('common.delete')" href="#" class="btn btn-danger btn-sm"
                          @click="deleteData(data.slug)">
                          <i class="fas fa-trash" />
                        </a>
                      </div>
                    </td>
                  </tr>
                  <tr v-show="!loading && !items.length">
                    <td colspan="8">
                      <EmptyTable />
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          <!-- /.card-body -->
          <div class="dtable-footer">
            <div class="form-group row display-per-page">
              <label>{{ $t("per_page") }} </label>
              <div>
                <select @change="updatePerPager" v-model="perPage" class="form-control form-control-sm ml-1">
                  <option value="10">10</option>
                  <option value="25">25</option>
                  <option value="50">50</option>
                  <option value="100">100</option>
                </select>
              </div>
            </div>
            <!-- pagination-start -->
            <pagination v-if="pagination && pagination.last_page > 1" :pagination="pagination" :offset="5"
              class="justify-flex-end" @paginate="paginate" />
            <!-- pagination-end -->
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapGetters } from "vuex";
export default {
  layout: 'central',
  middleware: ["auth", "check-permissions"],
  metaInfo() {
    return { title: this.$t('central.setup.user_management.index.page_title') };
  },
  data: () => ({
    breadcrumbsCurrent: "central.setup.user_management.index.breadcrumbs_current",
    breadcrumbs: [
      {
        name: "central.setup.user_management.index.breadcrumbs_first",
        url: "home",
      },
      {
        name: "setup.role_and_permission.index.breadcrumbs_second",
        url: "setup.index",
      },
      {
        name: "central.setup.user_management.index.breadcrumbs_current",
        url: "",
      },
    ],
    query: "",
    perPage: 10,
    developer: false,
  }),

  // Map Getters
  computed: {
    ...mapGetters("operations", ["items", "loading", "pagination"]),
  },

  watch: {
    // watch search data
    query: function (newQ, oldQ) {
      if (newQ === "") {
        this.getData();
      } else {
        this.searchData();
      }
    },
  },

  created() {

    this.getData();
    Fire.$on("AfterDelete", () => {
      this.getData();
    });

  },

  methods: {

    // update per page count
    updatePerPager() {
      this.pagination.current_page = 1;
      this.query === "" ? this.getData() : this.searchData();
    },

    // get data
    async getData() {
      this.$store.state.operations.loading = true;
      let currentPage = this.pagination ? this.pagination.current_page : 1;
      await this.$store.dispatch("operations/fetchData", {
        path: "/api/users?page=",
        currentPage: currentPage + "&perPage=" + this.perPage,
      });
    },

    // Pagination
    async paginate() {
      this.query === "" ? this.getData() : this.searchData();
    },

    // Reset pagination
    async resetPagination() {
      this.pagination.current_page = 1;
    },

    // Reload after search
    async reload() {
      this.query = "";
    },

    // search data
    async searchData() {
      this.$store.state.operations.loading = true;
      let currentPage = this.pagination ? this.pagination.current_page : 1;
      await this.$store.dispatch("operations/searchData", {
        term: this.query,
        path: "/api/user/search/",
        currentPage: currentPage + "&perPage=" + this.perPage,
      });
    },

    // delete data
    async deleteData(slug) {
      Swal.fire({
        title: this.$t("common.delete_title"),
        text: this.$t("common.delete_warning"),
        type: "warning",
        showCancelButton: true,
        confirmButtonText: this.$t("common.delete_confirm_text"),
      }).then((result) => {
        if (result.value) {
          this.$store
            .dispatch("operations/deleteData", {
              path: "/api/user/",
              slug: slug,
            })
            .then((response) => {
              if (response === true) {
                Swal.fire(
                  this.$t("common.deleted"),
                  this.$t("common.delete_success"),
                  "success"
                );
                Fire.$emit("AfterDelete");
              } else {
                Swal.fire(
                  this.$t("common.failed"),
                  this.$t("common.delete_failed"),
                  "warning"
                );
              }
            });
        }
      });
    },
  },
};
</script>


<style scoped>
.profile_wrap {
  display: flex;
  align-items: center;
}

.profile_wrap img {
  width: 40px;
  height: 40px;
  border-radius: 100%;
  margin-right: 10px;
}

.table th,
.table td {
  vertical-align: middle;
}
</style>
