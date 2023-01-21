<template>
  <div>

    <!--- SECTION TITLE FORM START --->
    <div class="card">
      <div class="card-header setings-header">
        <h3 class="card-title">{{ $t('common.pricing_plan') }}</h3>
      </div>
      <div class="card-body">
        <form class="form-horizontal" @submit.prevent="update" @keydown="form.onKeydown($event)">
          <!-- form group start -->
          <div class="form-group row">
            <label for="pricing_plan_section_tagline" class="col-sm-2 col-form-label text-md-right text-left">
              {{ $t('common.pricing_plan_section_tagline') }} <span class="required">*</span>
            </label>
            <div class="col-sm-10">
              <input type="text" v-model="form.pricing_plan_section_tagline" class="form-control"
                :class="{ 'is-invalid': form.errors.has('pricing_plan_section_tagline') }"
                id="pricing_plan_section_tagline"
                :placeholder="$t('common.pricing_plan_section_tagline_placeholder')" />
              <has-error :form="form" field="pricing_plan_section_tagline" />
            </div>
          </div>

          <div class="form-group row">
            <label for="pricing_plan_section_title" class="col-sm-2 col-form-label text-md-right text-left">
              {{ $t('common.pricing_plan_section_title') }} <span class="required">*</span>
            </label>
            <div class="col-sm-10">
              <input type="text" v-model="form.pricing_plan_section_title" class="form-control"
                :class="{ 'is-invalid': form.errors.has('pricing_plan_section_title') }" id="pricing_plan_section_title"
                :placeholder="$t('common.pricing_plan_section_title_placeholder')" />
              <has-error :form="form" field="pricing_plan_section_title" />
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
    <!--- SECTION TITLE FORM END --->



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
      pricing_plan_section_tagline: '',
      pricing_plan_section_title: '',
    }),
    user: '',
  }),



  created() {
    this.getData();
  },

  methods: {
    // get the user
    async getData() {
      const { data } = await this.form.get(window.location.origin + '/api/settings/pricing-plan-settings')
      this.user = data.data
      this.form.fill(data.data)
    },

    // update
    async update() {
      await this.form
        .patch(window.location.origin + '/api/settings/pricing-plan-settings')
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
