<template>
  <div class="card">
    <div class="card-header setings-header">
      <h3 class="card-title">{{ $t('common.newsletter_section_title') }}</h3>
    </div>
    <div class="card-body">
      <form class="form-horizontal" @submit.prevent="update" @keydown="form.onKeydown($event)">
        <!-- form group start -->
        <div class="form-group row">
          <label for="newsletter_section_title" class="col-sm-2 col-form-label text-md-right text-left">
            {{ $t('common.newsletter_section_title') }} <span class="required">*</span>
          </label>
          <div class="col-sm-10">
            <input type="text" v-model="form.newsletter_section_title" class="form-control"
              :class="{ 'is-invalid': form.errors.has('newsletter_section_title') }" id="newsletter_section_title"
              :placeholder="$t('common.newsletter_section_title_placeholder')" />
            <has-error :form="form" field="newsletter_section_title" />
          </div>
        </div>

        <div class="form-group row">
          <label for="newsletter_section_description" class="col-sm-2 col-form-label text-md-right text-left">
            {{ $t('common.newsletter_section_description') }} <span class="required">*</span>
          </label>
          <div class="col-sm-10">
            <textarea v-model="form.newsletter_section_description" class="form-control"
              :class="{ 'is-invalid': form.errors.has('newsletter_section_description') }"
              id="newsletter_section_description"
              :placeholder="$t('common.newsletter_section_description_placeholder')"></textarea>
            <has-error :form="form" field="newsletter_section_description" />
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
      newsletter_section_title: '',
      newsletter_section_description: '',
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
      const { data } = await this.form.get(window.location.origin + '/api/settings/newsletter-settings')
      this.user = data.data
      this.form.fill(data.data)
    },

    // update
    async update() {
      await this.form
        .patch(window.location.origin + '/api/settings/newsletter-settings')
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
