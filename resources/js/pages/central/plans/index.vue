<template>
  <div>
    <!-- breadcrumbs Start -->
    <breadcrumbs :items="breadcrumbs" :current="breadcrumbsCurrent" />
    <!-- breadcrumbs end -->
    <div class="row">
      <div class="col-lg-12">
        <div>
          <div class="row">
            <div class="col-xl-2 col-md-3 col-4">
              <search v-model="query" @reset-pagination="resetPagination()" @reload="reload" />
            </div>
            <div class="col-xl-10 col-md-9 col-8 text-right">
              <div class="btn-group">
                <a @click="print" v-tooltip="$t('common.print_table')" class="btn btn-info">
                  <i class="fas fa-print"></i>
                </a>
                <router-link :to="{ name: 'plans.create' }" class="btn btn-primary">
                  {{ $t('common.create') }}
                  <i class="fas fa-plus-circle d-none d-sm-inline-block" />
                </router-link>
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
                  <th>{{ $t('common.s_no') }}</th>
                  <th>{{ $t('central.plans.image') }}</th>
                  <th>{{ $t('central.plans.name') }}</th>
                  <th>{{ $t('central.plans.amount') }}</th>
                  <th>{{ $t('central.plans.currency') }}</th>
                  <th>{{ $t('central.plans.interval') }}</th>
                  <th>{{ $t('central.plans.description') }}</th>
                  <th class="text-right no-print">{{ $t('central.plans.action') }}</th>
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
                    <a v-if="data.image" href="#" id="show-modal" @click="previewModal(data.image)">
                      <img :src="data.image" class="rounded preview-sm" loading="lazy" />
                    </a>
                    <div v-else class="bg-secondary rounded no-preview-sm">
                      <small>{{ $t("common.no_preview") }}</small>
                    </div>
                  </td>
                  <td>{{ data.name }}</td>
                  <td>{{ data.amount }}</td>
                  <td>{{ data.currency }}</td>
                  <td>{{ data.interval }}</td>
                  <td>{{ data.description }}</td>
                  <td class="text-right no-print">
                    <div class="btn-group">
                      <router-link v-tooltip="$t('common.edit')" :to="{
                        name: 'plans.edit',
                        params: { id: data.id },
                      }" class="btn btn-info btn-sm">
                        <i class="fas fa-edit" />
                      </router-link>
                      <a v-tooltip="$t('common.delete')" href="#" class="btn btn-danger btn-sm"
                        @click="deleteData(data.id)">
                        <i class="fas fa-trash" />
                      </a>
                    </div>
                  </td>
                </tr>
                <tr v-show="!loading && !items.length">
                  <td colspan="6">
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
            <label>{{ $t('per_page') }} </label>
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
    <Modal v-if="showModal" @close="previewModal()">
      <h5 slot="header">{{ $t('plans.index.brand_logo_view') }}</h5>
      <div class="w-100" slot="body">
        <img :src="imagePath" class="rounded img-fluid" loading="lazy" />
      </div>
    </Modal>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import Modal from '../../../components/Modal.vue'

export default {
  layout: 'central',
  middleware: ['auth', 'check-permissions'],
  metaInfo() {
    return { title: this.$t('central.plans.index.page_title') }
  },
  components: {
    Modal,
  },
  data: () => ({
    breadcrumbsCurrent: 'central.plans.index.breadcrumbs_current',
    breadcrumbs: [
      {
        name: 'central.plans.index.breadcrumbs_first',
        url: 'home',
      },
      {
        name: 'central.plans.index.breadcrumbs_active',
        url: '',
      },
    ],
    showModal: false,
    imagePath: '',
    query: '',
    perPage: 10,
  }),
  // Map Getters
  computed: {
    ...mapGetters('operations', ['items', 'loading', 'pagination']),
  },
  watch: {
    // watch search data
    query: function (newQ) {
      if (newQ === '') {
        this.getData()
      } else {
        this.searchData()
      }
    },
  },
  created() {
    this.getData()
  },
  methods: {
    // update per page count
    updatePerPager() {
      this.pagination.current_page = 1
      this.query === '' ? this.getData() : this.searchData()
    },
    // get data
    async getData() {
      this.$store.state.operations.loading = true
      let currentPage = this.pagination ? this.pagination.current_page : 1
      await this.$store.dispatch('operations/fetchData', {
        path: '/api/plans?page=',
        currentPage: currentPage + '&perPage=' + this.perPage,
      })
    },

    // Pagination
    async paginate() {
      this.query === '' ? await this.getData() : await this.searchData()
    },

    // Reset pagination
    async resetPagination() {
      this.pagination.current_page = 1
    },

    // search data
    async searchData() {
      this.$store.state.operations.loading = true
      let currentPage = this.pagination ? this.pagination.current_page : 1
      await this.$store.dispatch('operations/searchData', {
        term: this.query,
        path: '/api/plans/search/',
        currentPage: currentPage + '&perPage=' + this.perPage,
      })
    },

    // Reload after search
    async reload() {
      this.query = ''
    },

    // display modal
    previewModal(image) {
      this.imagePath = image
      if (this.showModal) {
        return (this.showModal = false)
      }
      return (this.showModal = true)
    },

    // print table
    async print() {
      await this.$htmlToPaper('printMe')
    },

    // delete data
    async deleteData(id) {
      Swal.fire({
        title: this.$t('common.delete_title'),
        text: this.$t('common.delete_warning'),
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: this.$t('central.plans.index.delete_confirm_button'),
      }).then((result) => {
        // Send request to the server
        if (result.value) {
          this.$store
            .dispatch('operations/deleteData', {
              path: '/api/plans/',
              slug: id,
            })
            .then((response) => {
              if (response === true) {
                Swal.fire(
                  this.$t('common.deleted'),
                  this.$t('common.delete_success'),
                  'success'
                )
                this.getData()
              } else {
                Swal.fire(
                  this.$t('common.failed'),
                  this.$t('common.delete_failed'),
                  'warning'
                )
              }
            })
        }
      })
    },
  },
}
</script>
