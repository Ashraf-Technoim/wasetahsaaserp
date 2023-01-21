<template>
  <div class="card">
    <div class="card-header setings-header">
      <h3 class="card-title">{{ $t('common.hero') }}</h3>
    </div>
    <div class="card-body">
      <form class="form-horizontal" @submit.prevent="update" @keydown="form.onKeydown($event)">
        <!-- form group start -->

        <div class="form-group row">
          <label for="name" class="col-sm-2 col-form-label text-md-right text-left">
            {{ $t('common.hero_tagline') }} <span class="required">*</span>
          </label>
          <div class="col-sm-10">
            <input type="text" v-model="form.hero_tagline" class="form-control"
              :class="{ 'is-invalid': form.errors.has('hero_tagline') }" id="hero_tagline"
              :placeholder="$t('common.hero_tagline_placeholder')" />
            <has-error :form="form" field="hero_tagline" />
          </div>
        </div>

        <div class="form-group row">
          <label for="hero_title" class="col-sm-2 col-form-label text-md-right text-left">
            {{ $t('common.hero_title') }} <span class="required">*</span>
          </label>
          <div class="col-sm-10">
            <input type="text" v-model="form.hero_title" class="form-control"
              :class="{ 'is-invalid': form.errors.has('hero_title') }" id="hero_title"
              :placeholder="$t('common.hero_title_placeholder')" />
            <has-error :form="form" field="hero_title" />
          </div>
        </div>

        <div class="form-group row">
          <label for="hero_description" class="col-sm-2 col-form-label text-md-right text-left">
            {{ $t('common.hero_description') }} <span class="required">*</span>
          </label>
          <div class="col-sm-10">
            <textarea v-model="form.hero_description" class="form-control"
              :class="{ 'is-invalid': form.errors.has('hero_description') }" id="hero_description"
              :placeholder="$t('common.hero_description_placeholder')"></textarea>
            <has-error :form="form" field="hero_description" />
          </div>
        </div>

        <div class="form-group row">
          <label for="hero_demo_button_text" class="col-sm-2 col-form-label text-md-right text-left">
            {{ $t('common.hero_demo_button_text') }} <span class="required">*</span>
          </label>
          <div class="col-sm-10">
            <input type="text" v-model="form.hero_demo_button_text" class="form-control"
              :class="{ 'is-invalid': form.errors.has('hero_demo_button_text') }" id="hero_demo_button_text"
              :placeholder="$t('common.hero_demo_button_text_placeholder')" />
            <has-error :form="form" field="hero_demo_button_text" />
          </div>
        </div>

        <div class="form-group row">
          <label for="hero_demo_button_link" class="col-sm-2 col-form-label text-md-right text-left">
            {{ $t('common.hero_demo_button_link') }} <span class="required">*</span>
          </label>
          <div class="col-sm-10">
            <input type="text" v-model="form.hero_demo_button_link" class="form-control"
              :class="{ 'is-invalid': form.errors.has('hero_demo_button_link') }" id="hero_demo_button_link"
              :placeholder="$t('common.hero_demo_button_link_placeholder')" />
            <has-error :form="form" field="hero_demo_button_link" />
          </div>
        </div>

        <div class="form-group row">
          <label for="hero_get_started_button_text" class="col-sm-2 col-form-label text-md-right text-left">
            {{ $t('common.hero_get_started_button_text') }} <span class="required">*</span>
          </label>
          <div class="col-sm-10">
            <input type="text" v-model="form.hero_get_started_button_text" class="form-control"
              :class="{ 'is-invalid': form.errors.has('hero_get_started_button_text') }"
              id="hero_get_started_button_text" :placeholder="$t('common.hero_get_started_button_text_placeholder')" />
            <has-error :form="form" field="hero_get_started_button_text" />
          </div>
        </div>

        <div class="form-group row">
          <label for="hero_get_started_button_link" class="col-sm-2 col-form-label text-md-right text-left">
            {{ $t('common.hero_get_started_button_link') }} <span class="required">*</span>
          </label>
          <div class="col-sm-10">
            <input type="text" v-model="form.hero_get_started_button_link" class="form-control"
              :class="{ 'is-invalid': form.errors.has('hero_get_started_button_link') }"
              id="hero_get_started_button_link" :placeholder="$t('common.hero_get_started_button_link_placeholder')" />
            <has-error :form="form" field="hero_get_started_button_link" />
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
      hero_tagline: '',
      hero_title: '',
      hero_description: '',
      hero_demo_button_text: '',
      hero_demo_button_link: '',
      hero_get_started_button_text: '',
      hero_get_started_button_link: '',
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
      const { data } = await this.form.get(window.location.origin + '/api/settings/hero-settings')
      this.user = data.data
      this.form.fill(data.data)
    },

    // update
    async update() {
      await this.form
        .patch(window.location.origin + '/api/settings/hero-settings')
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
