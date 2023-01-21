<template>
  <div class="card">
    <div class="card-header setings-header">
      <h3 class="card-title">{{ $t("billing.invoice") }}</h3>
    </div>
    <div class="card-body">
      <div class="row">
        <div class="col-lg-12">
          <!-- /.card-header -->
          <div class="card-body p-0 position-relative">
            <table-loading v-show="loading" />
            <div class="table-responsive table-custom mt-3" id="printMe">
              <table class="table">
                <thead>
                  <tr>
                    <th>{{ $t("common.s_no") }}</th>
                    <th>{{ $t("common.id") }}</th>
                    <th>{{ $t("common.date") }}</th>
                    <th>{{ $t("common.total") }}</th>
                    <th class="text-right no-print">
                      {{ $t("common.action") }}
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
                    <td>{{ data.id }}</td>
                    <td>{{ data.created | moment("Do MMM, YYYY") }}</td>
                    <td>
                      <span v-if="data && data.currency">{{
                        data.total / 100 + " " + data.currency.toUpperCase()
                      }}</span>
                    </td>
                    <td class="text-right no-print">
                      <div v-if="data.id" class="btn-group">
                        <a
                          v-tooltip="$t('common.download')"
                          href="#"
                          class="btn btn-danger btn-sm"
                          @click="download(data.id, data.account_name)"
                        >
                          <i class="fas fa-file-download" />
                        </a>
                      </div>
                    </td>
                  </tr>
                  <tr v-show="!loading && !items.length">
                    <td colspan="12">
                      <EmptyTable />
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
          <!-- /.card-body -->
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapGetters } from "vuex";

export default {
  middleware: ["auth", "check-permissions"],
  metaInfo() {
    return { title: this.$t("subscription_invoices.list.index.page_title") };
  },
  data: () => ({
    breadcrumbsCurrent: "subscription_invoices.list.index.breadcrumbs_current",
    breadcrumbs: [
      {
        name: "subscription_invoices.list.index.breadcrumbs_first",
        url: "home",
      },
      {
        name: "subscription_invoices.list.index.breadcrumbs_active",
        url: "",
      },
    ],
    query: "",
    perPage: 10,
  }),
  // Map Getters
  computed: {
    ...mapGetters("operations", [
      "items",
      "loading",
      "pagination",
      "appInfo",
      "tenant",
    ]),
  },
  created() {
    this.getData();
  },
  methods: {
    // get data
    async getData() {
      this.$store.state.operations.loading = true;
      let currentPage = this.pagination ? this.pagination.current_page : 1;
      await this.$store.dispatch("operations/fetchData", {
        path: "/api/subscription-invoices?page=",
        currentPage: currentPage + "&perPage=" + this.perPage,
      });
    },
    // download invoice
    async download(id, account_name) {
      await this.$axios
        .post(
          window.location.origin + "/api/subscription-invoices",
          {
            invoice_id: id,
            product_name: this.appInfo.companyName + " - Subscription",
            vendor_name: account_name,
          },
          {
            responseType: "blob",
          }
        )
        .then((response) => {
          toast.fire({
            type: "success",
            title: this.$t("subscription_invoices.create.success_msg"),
          });
          window.open(URL.createObjectURL(response.data));
        })
        .catch(() => {
          toast.fire({
            type: "error",
            title: this.$t("common.error_msg"),
          });
        });
    },
  },
};
</script>
