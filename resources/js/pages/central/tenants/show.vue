<template>
  <div>
    <!-- breadcrumbs Start -->
    <breadcrumbs :items="breadcrumbs" :current="breadcrumbsCurrent" />
    <!-- breadcrumbs end -->

    <div class="row" v-if="data">
      <div class="col-md-12 col-lg-4 tenant-details">
        <div class="card card-primary card-outline">
          <div class="card-body">
            <div class="text-center">
              <img
                class="profile-user-img img-fluid img-circle"
                :src="data.photo_url"
                :alt="$t('common.image_alt')"
              />
            </div>
            <h3 class="profile-username text-center text-capitalize">
              {{ data.name }}
            </h3>
            <a href="#" class="profile-username text-center d-block">{{
              data.email
            }}</a>
            <table>
              <tr>
                <td width="100">{{ $t("central.tenant_id") }}</td>
                <td>{{ data.id }}</td>
              </tr>
              <tr>
                <td width="100">{{ $t("name") }}</td>
                <td>{{ data.name }}</td>
              </tr>
              <tr>
                <td width="100">{{ $t("email") }}</td>
                <td>{{ data.email }}</td>
              </tr>
              <tr>
                <td width="100">{{ $t("common.company") }}</td>
                <td>{{ data.company }}</td>
              </tr>
              <tr>
                <td width="100">{{ $t("domain") }}</td>
                <td>{{ data.domain }}</td>
              </tr>
              <tr>
                <td width="100">{{ $t("central.current_plan") }}</td>
                <td>{{ data.plan && data.plan.name }}</td>
              </tr>
              <tr>
                <td width="100">{{ $t("central.trial_start") }}</td>
                <td>{{ data.created_at | moment("Do MMM, YYYY") }}</td>
              </tr>
              <tr v-if="data.trial_ends_at">
                <td width="100">{{ $t("central.trial_end") }}</td>
                <td>{{ data.trial_ends_at | moment("Do MMM, YYYY") }}</td>
              </tr>
              <tr v-if="data.created_at">
                <td width="100">{{ $t("common.created_at") }}</td>
                <td>{{ data.created_at | moment("Do MMM, YYYY") }}</td>
              </tr>
              <tr>
                <td width="100">{{ $t("common.banned") }}</td>
                <td>{{ data.is_banned ? "True" : "False" }}</td>
              </tr>
            </table>
          </div>
          <!-- /.card-body -->
        </div>
        <!-- CARD DETAILS TABLE -->

        <div class="domain-twrapper card mt-0">
          <div class="card-header">
            <h3>{{ $t("domain") }}</h3>
          </div>
          <div class="card-body">
            <table class="mt-0">
              <tr>
                <td width="100">{{ $t("primary_domain") }}</td>
                <td>
                  <a :href="data.domain_url" target="_blank">{{
                    data.domain_url
                  }}</a>
                </td>
              </tr>
              <tr v-for="(domain, i) in data.domains" :key="domain.id">
                <td width="100">{{ $t("domain") }} {{ i + 1 }}</td>
                <td>
                  <a :href="domain" target="_blank">{{ data.domain }}</a>
                </td>
              </tr>
            </table>
          </div>
        </div>
        <!-- DOMAIN TABLE-->
      </div>
      <!-- /.col -->

      <div class="col-md-12 col-lg-8">
        <div class="row">
          <div
            v-for="(limitation, i) in data.limitations && data.limitations"
            :key="i"
            class="col-lg-4 col-md-2 col-sm-6 col-12"
          >
            <div class="small-box" :class="limitation.bgColor">
              <div class="inner">
                <h3 class="text-wrap">
                  {{ $t(limitation.name) }}: {{ limitation.limit }}
                </h3>
                <p class="mb-0">
                  {{ $t("central.used") }}: {{ limitation.current }}
                </p>
                <p>{{ $t("central.remaining") }}: {{ limitation.remaining }}</p>
              </div>
              <div class="icon">
                <i :class="limitation.icon"></i>
              </div>
            </div>
          </div>
        </div>

        <div class="card">
          <div class="card-header setings-header">
            <h3 class="card-title">{{ $t("billing.invoice") }}</h3>
          </div>
          <!-- /.card-header -->
          <div class="card-body">
            <div class="row">
              <div class="col-lg-12">
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
                        <tr
                          v-show="data && data.tenant_invoices"
                          v-for="(tenant_invoice, i) in data &&
                          data.tenant_invoices"
                          :key="i"
                        >
                          <td>
                            <span>{{ i + 1 }}</span>
                          </td>
                          <td>{{ tenant_invoice.id }}</td>
                          <td>
                            {{
                              tenant_invoice.created | moment("Do MMM, YYYY")
                            }}
                          </td>
                          <td>
                            {{
                              tenant_invoice.total / 100 +
                              " " +
                              tenant_invoice.currency.toUpperCase()
                            }}
                          </td>
                          <td class="text-right no-print">
                            <div v-if="tenant_invoice.id" class="btn-group">
                              <a
                                v-tooltip="$t('common.download')"
                                href="#"
                                class="btn btn-danger btn-sm"
                                @click="
                                  download(
                                    tenant_invoice.id,
                                    tenant_invoice.account_name
                                  )
                                "
                              >
                                <i class="fas fa-file-download" />
                              </a>
                            </div>
                          </td>
                        </tr>
                        <tr
                          v-show="
                            data.tenant_invoices &&
                            data.tenant_invoices.length < 1
                          "
                        >
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
      </div>
    </div>
  </div>
</template>
<script>
import axios from "axios";
import { mapGetters } from "vuex";

export default {
  layout: "central",
  middleware: ["auth", "check-permissions"],
  metaInfo() {
    return { title: this.$t("central.tenants.view.page_title") };
  },
  data: () => ({
    breadcrumbsCurrent: "central.tenants.view.breadcrumbs_current",
    breadcrumbs: [
      {
        name: "central.tenants.view.breadcrumbs_first",
        url: "home",
      },
      {
        name: "central.tenants.view.breadcrumbs_second",
        url: "tenants.index",
      },
      {
        name: "central.tenants.view.breadcrumbs_current",
        url: "",
      },
    ],
    url: null,
    showModal: false,
    data: {},
    loading: true,
  }),

  computed: {
    ...mapGetters("operations", ["appInfo"]),
  },

  created() {
    this.getTenant();
  },
  methods: {
    // get the tenant
    async getTenant() {
      const { data } = await axios.get(
        window.location.origin + "/api/tenants/" + this.$route.params.id
      );
      this.data = data;
      this.loading = false;
    },

    // download invoice
    async download(id, account_name) {
      await axios
        .post(
          window.location.origin + "/api/subscription-invoices",
          {
            tenant_id: this.data.id,
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

    // print
    printWindow() {
      window.print();
    },
  },
};
</script>

<style lang="scss" scoped>
.tenant-details h3 {
  margin: 15px 0 0 0;
}

.card-body.tenant-details a {
  text-align: center;
  display: block;
  font-size: 16px;
  margin-top: 0;
}

.tenant-details table {
  width: 100%;
  margin-top: 20px;
}

.tenant-details table tr td {
  border: 1px solid #ddd;
  padding: 8px 15px;
}

.tenant-details table tr td:first-child {
  width: 30%;
}

.domain-twrapper .card-header h3 {
  padding: 0;
  margin: 0;
  font-size: 16px;
}

.box-profile .list-group-item {
  padding-left: 20px;
  padding-right: 20px;
}

.box-profile .list-group {
  border-left: 1px solid #ddd;
  border-right: 1px solid #ddd;
  border-radius: 0;
}
</style>
