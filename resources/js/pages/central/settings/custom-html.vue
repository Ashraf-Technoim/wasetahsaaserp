<template>
  <div class="card">
    <div class="card-header setings-header">
      <h3 class="card-title">{{ $t('common.custom_html') }}</h3>
    </div>
    <div class="card-body">
      <form class="form-horizontal" @submit.prevent="update" @keydown="form.onKeydown($event)">
        <!-- form group start -->
        <div class="form-group row">
          <label for="custom_html" class="col-sm-2 col-form-label text-md-right text-left">
            {{ $t('common.custom_html') }} <span class="required">*</span>
          </label>
          <div class="col-sm-10">
            <textarea v-model="form.custom_html" class="form-control"
              :class="{ 'is-invalid': form.errors.has('custom_html') }" id="custom_html"
              :placeholder="$t('common.custom_html_placeholder')" rows="5"></textarea>
            <has-error :form="form" field="custom_html" />
          </div>
        </div>
        <!-- form group end -->

        <div class="form-group row">
          <div class="offset-sm-2 col-sm-10">
            <v-button :loading="form.busy" class="btn btn-primary">
              <i class="fas fa-edit" /> {{ $t('common.save_changes') }}
            </v-button>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import Form from 'vform'

export default {
  layout: 'central',
  middleware: ['auth', 'check-permissions'],
  metaInfo() {
    return { title: this.$t('settings.page_title') }
  },
  data: () => ({
    breadcrumbsCurrent: 'user_profile.breadcrumbs_current',
    breadcrumbs: [
      {
        name: 'user_profile.breadcrumbs_first',
        url: 'home',
      },
      {
        name: 'user_profile.breadcrumbs_active',
        url: '',
      },
    ],
    form: new Form({
      custom_html: '',
    }),
    loading: true,
  }),
  created() {
    this.getData()
  },
  methods: {
    // get the user
    async getData() {
      const { data } = await this.form.get(window.location.origin + '/api/settings/custom-html-settings')
      this.form.fill(data.data)
    },

    // update
    async update() {
      await this.form
        .patch(window.location.origin + '/api/settings/custom-html-settings')
        .then(() => {
          toast.fire({
            type: 'success',
            title: this.$t('settings.success_msg'),
          })
        })
        .catch(() => {
          toast.fire({
            type: 'error',
            title: this.$t('common.error_msg'),
          })
        })
    },
  },
}
</script>

<style lang="scss" scoped>

</style>
