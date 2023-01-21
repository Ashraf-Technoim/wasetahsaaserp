<template>
  <div>

    <!--- SECTION TITLE FORM START --->
    <div class="card">
      <div class="card-header setings-header">
        <h3 class="card-title">{{ $t('common.all_features_and_get_started') }}</h3>
      </div>
      <div class="card-body">
        <form class="form-horizontal" @submit.prevent="update" @keydown="form.onKeydown($event)">
          <!-- form group start -->
          <div class="form-group row">
            <label for="get_started_box_title" class="col-sm-2 col-form-label text-md-right text-left">
              {{ $t('common.get_started_box_title') }} <span class="required">*</span>
            </label>
            <div class="col-sm-10">
              <input type="text" v-model="form.get_started_box_title" class="form-control"
                :class="{ 'is-invalid': form.errors.has('get_started_box_title') }" id="get_started_box_title"
                :placeholder="$t('common.get_started_box_title_placeholder')" />
              <has-error :form="form" field="get_started_box_title" />
            </div>
          </div>

          <div class="form-group row">
            <label for="get_started_box_description" class="col-sm-2 col-form-label text-md-right text-left">
              {{ $t('common.get_started_box_description') }} <span class="required">*</span>
            </label>
            <div class="col-sm-10">
              <textarea v-model="form.get_started_box_description" class="form-control"
                :class="{ 'is-invalid': form.errors.has('get_started_box_description') }"
                id="get_started_box_description"
                :placeholder="$t('common.get_started_box_description_placeholder')"></textarea>
              <has-error :form="form" field="get_started_box_description" />
            </div>
          </div>

          <div class="form-group row">
            <label for="get_started_box_button_text" class="col-sm-2 col-form-label text-md-right text-left">
              {{ $t('common.get_started_box_button_text') }} <span class="required">*</span>
            </label>
            <div class="col-sm-10">
              <input type="text" v-model="form.get_started_box_button_text" class="form-control"
                :class="{ 'is-invalid': form.errors.has('get_started_box_button_text') }"
                id="get_started_box_button_text" :placeholder="$t('common.get_started_box_button_text_placeholder')" />
              <has-error :form="form" field="get_started_box_button_text" />
            </div>
          </div>

          <div class="form-group row">
            <label for="get_started_box_button_link" class="col-sm-2 col-form-label text-md-right text-left">
              {{ $t('common.get_started_box_button_link') }} <span class="required">*</span>
            </label>
            <div class="col-sm-10">
              <input type="text" v-model="form.get_started_box_button_link" class="form-control"
                :class="{ 'is-invalid': form.errors.has('get_started_box_button_link') }"
                id="get_started_box_button_link" :placeholder="$t('common.get_started_box_button_link_placeholder')" />
              <has-error :form="form" field="get_started_box_button_link" />
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
import { mapGetters } from 'vuex'
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
      get_started_box_title: '',
      get_started_box_description: '',
      get_started_box_button_text: '',
      get_started_box_button_link: '',
    }),
    dataForm: new Form({
      title: '',
      description: '',
      status: '',
      image: ''
    }),
    user: '',
  }),

  // Map Getters
  computed: {
    ...mapGetters('operations', ['items', 'loading', 'pagination']),
  },

  created() {
    this.getData();
  },

  methods: {

    // get the user
    async getData() {
      const { data } = await this.form.get(window.location.origin + '/api/settings/get-started-settings')
      this.user = data.data
      this.form.fill(data.data)
    },


    // update
    async update() {
      await this.form
        .patch(window.location.origin + '/api/settings/get-started-settings')
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
