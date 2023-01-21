<template>
  <div class="card">
    <div class="card-header setings-header">
      <h3 class="card-title">{{ $t('common.about_us') }}</h3>
    </div>
    <div class="card-body">
      <form class="form-horizontal" @submit.prevent="update" @keydown="form.onKeydown($event)">
        <!-- form group start -->

        <div class="form-group row">
          <label for="about_us_tagline" class="col-sm-2 col-form-label text-md-right text-left">
            {{ $t('common.about_us_tagline') }} <span class="required">*</span>
          </label>
          <div class="col-sm-10">
            <input type="text" v-model="form.about_us_tagline" class="form-control"
              :class="{ 'is-invalid': form.errors.has('about_us_tagline') }" id="about_us_tagline"
              :placeholder="$t('common.about_us_tagline_placeholder')" />
            <has-error :form="form" field="about_us_tagline" />
          </div>
        </div>

        <div class="form-group row">
          <label for="about_us_title" class="col-sm-2 col-form-label text-md-right text-left">
            {{ $t('common.about_us_title') }} <span class="required">*</span>
          </label>
          <div class="col-sm-10">
            <input type="text" v-model="form.about_us_title" class="form-control"
              :class="{ 'is-invalid': form.errors.has('about_us_title') }" id="about_us_title"
              :placeholder="$t('common.about_us_title_placeholder')" />
            <has-error :form="form" field="about_us_title" />
          </div>
        </div>

        <div class="form-group row">
          <label for="about_us_description" class="col-sm-2 col-form-label text-md-right text-left">
            {{ $t('common.about_us_description') }} <span class="required">*</span>
          </label>
          <div class="col-sm-10">
            <textarea v-model="form.about_us_description" class="form-control"
              :class="{ 'is-invalid': form.errors.has('about_us_description') }" id="about_us_description"
              :placeholder="$t('common.about_us_description_placeholder')"></textarea>
            <has-error :form="form" field="about_us_description" />
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
      about_us_tagline: '',
      about_us_title: '',
      about_us_description: '',
    }),
    loading: true,
    user: '',
  }),
  created() {
    this.getData()
  },
  methods: {
    // get the user
    async getData() {
      const { data } = await this.form.get(window.location.origin + '/api/settings/about-us-settings')
      this.user = data.data
      this.form.fill(data.data)
    },

    // update
    async update() {
      await this.form
        .patch(window.location.origin + '/api/settings/about-us-settings')
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
