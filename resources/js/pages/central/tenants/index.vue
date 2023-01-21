<template>
  <div class="mb-50">
    <!-- breadcrumbs Start -->
    <breadcrumbs :items="breadcrumbs" :current="breadcrumbsCurrent" />
    <!-- breadcrumbs end -->
    <div class="row">
      <div class="col-lg-12">
        <div class="w-100">
          <div>
            <div class="row">
              <div class="col-12 col-md-6 col-xl-4 mb-2">
                <search
                  v-model="query"
                  @reset-pagination="resetPagination()"
                  @reload="reload"
                />
              </div>
              <div class="col-12 col-md-6 col-xl-4 mb-2">
                <date-range-picker
                  ref="picker"
                  opens="center"
                  :locale-data="locale"
                  :minDate="minDate"
                  :maxDate="maxDate"
                  :singleDatePicker="false"
                  :showWeekNumbers="false"
                  :showDropdowns="true"
                  :autoApply="true"
                  v-model="dateRange"
                  @update="updateValues"
                  :linkedCalendars="true"
                  class="c-w-100"
                >
                  <template v-slot:input="picker" style="min-width: 350px">
                    {{ picker.startDate | startDate }} -
                    {{ picker.endDate | endDate }}
                  </template>
                </date-range-picker>
              </div>
              <div class="col-12 col-md-12 col-xl-4 text-right mb-2">
                <div class="btn-group c-w-100">
                  <a
                    @click="refreshTable()"
                    href="#"
                    v-tooltip="'Refresh'"
                    class="btn btn-success"
                  >
                    <i class="fas fa-sync"></i>
                  </a>
                  <a
                    href="/tenants/pdf"
                    v-tooltip="$t('central.tenants.index.export_table')"
                    class="btn btn-secondary"
                  >
                    <i class="fas fa-file-export"></i>
                  </a>
                  <a
                    @click="print"
                    v-tooltip="$t('central.tenants.index.print_table')"
                    class="btn btn-info"
                  >
                    <i class="fas fa-print"></i>
                  </a>
                  <!--                  <router-link :to="{ name: 'tenants.create' }" class="btn btn-primary">
                    {{ $t('central.tenants.index.create') }}
                    <i class="fas fa-plus-circle d-none d-sm-inline-block" />
                  </router-link>-->
                </div>
              </div>
            </div>
          </div>
          <!-- /.card-header -->
          <div class="card-body p-0 position-relative">
            <table-loading v-show="loading" />
            <div class="table-responsive table-custom mt-3" id="printMe">
              <table class="table">
                <thead>
                  <tr>
                    <th>{{ $t("central.tenants.index.s_no") }}</th>
                    <th>{{ $t("central.tenants.index.domain") }}</th>
                    <th>{{ $t("central.tenants.index.name_and_email") }}</th>
                    <th>{{ $t("central.tenants.index.plan") }}</th>
                    <th>{{ $t("central.tenants.index.on_trial") }}</th>
                    <th>{{ $t("central.tenants.index.is_subscribed") }}</th>
                    <th>{{ $t("central.tenants.index.banned") }}</th>
                    <th class="text-right no-print">
                      {{ $t("central.tenants.index.action") }}
                    </th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-show="items.length" v-for="(data, i) in items" :key="i">
                    <td>
                      <span v-if="pagination && pagination.current_page > 1">
                        {{
                          pagination.per_page * (pagination.current_page - 1) +
                          (i + 1)
                        }}
                      </span>
                      <span v-else>{{ i + 1 }}</span>
                    </td>
                    <td>
                      <router-link
                        :to="{
                          name: 'tenants.show',
                          params: { id: data.id },
                        }"
                      >
                        {{ data.domain }}
                      </router-link>
                    </td>
                    <td class="profile-area">
                      <div class="mr-2 img">
                        <img
                          :src="data.photo_url"
                          :alt="data.name"
                          class="rounded-circle"
                        />
                      </div>
                      <div>
                        <span class="text-capitalize">{{ data.name }}</span>
                        <a :href="`mailto:${data.email}`">{{ data.email }}</a>
                      </div>
                    </td>
                    <td>{{ data.plan && data.plan.name }}</td>
                    <td>
                      <span v-if="data.on_trial" class="badge bg-success">
                        {{ $t("central.tenants.index.true") }}
                      </span>
                      <span v-else class="badge bg-danger">
                        {{ $t("central.tenants.index.false") }}
                      </span>
                    </td>
                    <td>
                      <span v-if="data.is_subscribed" class="badge bg-success">
                        {{ $t("central.tenants.index.true") }}
                      </span>
                      <span v-else class="badge bg-danger">
                        {{ $t("central.tenants.index.false") }}
                      </span>
                    </td>
                    <td>
                      <span
                        v-if="data.is_banned == false"
                        class="badge bg-success"
                        >{{ $t("central.tenants.index.false") }}</span
                      >
                      <span v-else class="badge bg-danger">{{
                        $t("central.tenants.index.true")
                      }}</span>
                    </td>
                    <td class="text-right no-print">
                      <div class="btn-group">
                        <router-link
                          v-tooltip="$t('central.tenants.index.view')"
                          :to="{
                            name: 'tenants.show',
                            params: { id: data.id },
                          }"
                          class="btn btn-primary btn-sm"
                        >
                          <i class="fas fa-eye" />
                        </router-link>
                        <button
                          @click="impersonate(data.id)"
                          v-tooltip="$t('central.tenants.index.impersonate')"
                          class="btn btn-info btn-sm"
                        >
                          <i class="fas fa-user-secret" />
                        </button>
                        <router-link
                          :to="{
                            name: 'send-notification',
                            params: { id: data.id },
                          }"
                          v-tooltip="
                            $t('central.tenants.index.send_notification')
                          "
                          class="btn btn-secondary btn-sm"
                        >
                          <i class="fas fa-envelope" />
                        </router-link>

                        <router-link
                          v-tooltip="$t('central.tenants.index.edit')"
                          :to="{
                            name: 'tenants.edit',
                            params: { id: data.id },
                          }"
                          class="btn btn-info btn-sm"
                        >
                          <i class="fas fa-edit" />
                        </router-link>
                        <a
                          href="#"
                          v-tooltip="
                            data.is_banned
                              ? $t('central.tenants.index.unban')
                              : $t('central.tenants.index.ban')
                          "
                          class="btn btn-sm"
                          :class="
                            data.is_banned ? 'btn-success' : 'btn-warning'
                          "
                          @click="ban(data.id)"
                        >
                          <i class="fas fa-ban" />
                        </a>
                        <a
                          href="#"
                          v-tooltip="$t('central.tenants.index.delete')"
                          class="btn btn-danger btn-sm"
                          @click="deleteData(data.id)"
                        >
                          <i class="fas fa-trash" />
                        </a>
                      </div>
                    </td>
                  </tr>
                  <tr v-show="!loading && !items.length">
                    <td colspan="9">
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
                <select
                  @change="updatePerPager"
                  v-model="perPage"
                  class="form-control form-control-sm ml-1"
                >
                  <option value="10">10</option>
                  <option value="25">25</option>
                  <option value="50">50</option>
                  <option value="100">100</option>
                </select>
              </div>
            </div>
            <!-- pagination-start -->
            <pagination
              v-if="pagination && pagination.last_page > 1"
              :pagination="pagination"
              :offset="5"
              class="justify-flex-end"
              @paginate="paginate"
            />
            <!-- pagination-end -->
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import i18n from "~/plugins/i18n";
import DateRangePicker from "vue2-daterange-picker";
import moment from "moment";
import { mapGetters } from "vuex";
import axios from "axios";

export default {
  layout: "central",
  middleware: ["auth", "check-permissions"],
  metaInfo() {
    return { title: this.$t("central.tenants.index.page_title") };
  },
  components: {
    DateRangePicker,
  },
  data: () => ({
    breadcrumbsCurrent: "central.tenants.index.breadcrumbs_current",
    breadcrumbs: [
      {
        name: "central.tenants.index.breadcrumbs_first",
        url: "home",
      },
      {
        name: "central.tenants.index.breadcrumbs_active",
        url: "",
      },
    ],
    query: "",
    perPage: 10,
    clientPrefix: "",
    minDate: moment(new Date("01-01-2021")).format("YYYY-MM-DD"),
    maxDate: moment().add(1, "days").format("YYYY-MM-DD"),
    dateRange: {
      startDate: "",
      endDate: "",
    },
    locale: {
      direction: "ltr",
      format: "YYYY-MM-DD",
      separator: " - ",
      applyLabel: "Apply",
      cancelLabel: "Cancel",
      weekLabel: "W",
      customRangeLabel: "Custom Range",
      daysOfWeek: moment.weekdaysMin(),
      monthNames: moment.monthsShort(),
      firstDay: 1,
    },
  }),
  filters: {
    startDate(val) {
      return val ? moment(val).format("YYYY-MM-DD") : i18n.t("common.from");
    },
    endDate(val) {
      return val ? moment(val).format("YYYY-MM-DD") : i18n.t("common.to");
    },
  },
  // Map Getters
  computed: {
    ...mapGetters("operations", ["items", "loading", "pagination", "appInfo"]),
  },
  watch: {
    // watch search data
    query: function (newQ) {
      if (newQ === "") {
        if (this.dateRange.startDate && this.dateRange.endDate) {
          this.searchData();
        } else {
          this.getData();
        }
      } else {
        this.searchData();
      }
    },
  },
  created() {
    this.getData();
    this.clientPrefix = this.appInfo.clientPrefix;
  },
  methods: {
    // filter data for selected date range
    async updateValues() {
      this.dateRange.startDate = moment(this.dateRange.startDate).format(
        "YYYY-MM-DD"
      );
      this.dateRange.endDate = moment(this.dateRange.endDate).format(
        "YYYY-MM-DD"
      );
      this.searchData();
    },
    // refresh table
    refreshTable() {
      this.query = "";
      this.dateRange.startDate = null;
      this.dateRange.endDate = null;

      this.query === "" ? this.getData() : this.searchData();

      setTimeout(
        function () {
          this.dateRange.startDate = "";
          this.dateRange.endDate = "";
        }.bind(this),
        500
      );
    },
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
        path: "/api/tenants?page=",
        currentPage: currentPage + "&perPage=" + this.perPage,
      });
    },

    async impersonate(id) {
      axios
        .get(`/api/impersonate/${id}`)
        .then(({ data }) => {
          window.location.replace(data.data.redirect_url);
        })
        .catch((error) => {
          toast.fire({
            icon: "error",
            title: error.response.data.message,
          });
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

    // search data
    async searchData() {
      this.$store.state.operations.loading = true;
      let currentPage = this.pagination ? this.pagination.current_page : 1;
      await this.$store.dispatch("operations/searchData", {
        path: "/api/tenants/search",
        term: this.query,
        currentPage: currentPage + "&perPage=" + this.perPage,
        startDate: this.dateRange.startDate,
        endDate: this.dateRange.endDate,
      });
    },

    // Reload after search
    async reload() {
      this.query = "";
      await this.searchData();
    },

    // print table
    async print() {
      await this.$htmlToPaper("printMe");
    },

    // delete data
    async deleteData(slug) {
      Swal.fire({
        title: this.$t("central.tenants.index.delete_title"),
        text: this.$t("central.tenants.index.delete_warning"),
        type: "warning",
        showCancelButton: true,
        confirmButtonText: this.$t("central.tenants.index.delete_confirm_text"),
      }).then((result) => {
        // Send request to the server
        if (result.value) {
          this.$store
            .dispatch("operations/deleteData", {
              path: "/api/tenants/",
              slug: slug,
            })
            .then((response) => {
              if (response === true) {
                Swal.fire(
                  this.$t("central.tenants.index.deleted"),
                  this.$t("central.tenants.index.delete_success"),
                  "success"
                );
                this.getData();
              } else {
                Swal.fire(
                  this.$t("central.tenants.index.failed"),
                  this.$t("tenants.index.delete_failed"),
                  "warning"
                );
              }
            });
        }
      });
    },

    // delete data
    async ban(id) {
      axios
        .post(window.location.origin + "/api/tenants/" + id + "/ban")
        .then((response) => {
          if (response.data.success === true) {
            Swal.fire(
              this.$t(response.data.message),
              this.$t(response.data.message + "_success"),
              "success"
            );
            this.getData();
          } else {
            Swal.fire(
              this.$t("central.tenants.index.failed"),
              this.$t("tenants.index.ban_failed"),
              "warning"
            );
          }
        });
    },
  },
};
</script>

<style scoped>
.table-custom .table td {
  vertical-align: middle;
}

.profile-area {
  display: flex;
  align-items: center;
}

.profile-area img {
  width: 50px;
  height: 50px;
  border-radius: 100%;
}

.profile-area a {
  display: block;
}
</style>
