<template>
  <div>
    <!-- breadcrumbs Start -->
    <breadcrumbs :items="breadcrumbs" :current="breadcrumbsCurrent" />
    <!-- breadcrumbs end -->
    <div class="row">
      <div class="col-lg-12">
        <div class="w-100">
          <div class="card">
            <div class="card-header setings-header">
              <h3 class="card-title">{{ $t('Billing History') }}</h3>
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
                            <th>{{ $t('central.tenants.index.s_no') }}</th>
                            <th>{{ $t('Net Total') }}</th>
                            <th>{{ $t('Transaction Type') }}</th>
                            <th>{{ $t('Transaction Description') }}</th>
                            <th>{{ $t('Amount') }}</th>
                            <th>{{ $t('Currency') }}</th>
                            <th>{{ $t('Created At') }}</th>
                            <th>{{ $t('Payment Status') }}</th>
                            <th>{{ $t('Stripe Fee') }}</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr v-show="items.length" v-for="(data, i) in items" :key="i">
                            <td>{{ ++i }}</td>
                            <td>{{ data.net / 100 }}</td>
                            <td>{{ data.type }}</td>
                            <td>{{ data.description }}</td>
                            <td>{{ data.amount / 100 }}</td>
                            <td>{{ data.currency }}</td>
                            <td>{{ data.created }}</td>
                            <td>{{ data.status }}</td>
                            <td>{{ data.stripeFee / 100 }}</td>

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
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapGetters } from 'vuex';

export default {
  layout: 'central',
  middleware: ['auth', 'check-permissions'],
  metaInfo() {
    return { title: this.$t('Billing History') }
  },
  data: () => ({
    breadcrumbsCurrent: 'Billing History',
    breadcrumbs: [
      {
        name: 'central.tenants.index.breadcrumbs_first',
        url: 'home',
      },
      {
        name: 'Billing History',
        url: '',
      },
    ],
    query: '',
    perPage: 10,
    billingHistory: [],
  }),
  // Map Getters
  computed: {
    ...mapGetters('operations', ['items', 'loading', 'pagination', 'appInfo', 'tenant']),
  },
  created() {
    this.getData()
  },
  methods: {
    // get data
    async getData() {
      this.$store.state.operations.loading = true
      let currentPage = this.pagination ? this.pagination.current_page : 1
      await this.$store.dispatch('operations/fetchData', {
        path: '/api/billing/history?page=',
        currentPage: currentPage + '&perPage=' + this.perPage,
      })
    },
  },
}
</script>
