<template>
  <div>
    <!-- breadcrumbs Start -->
    <breadcrumbs :items="breadcrumbs" :current="breadcrumbsCurrent" />
    <!-- breadcrumbs end -->
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">
              {{ $t("setup.advanced_settings.index.page_title") }}
            </h3>
            <router-link
              :to="{ name: 'setup.index' }"
              class="btn btn-dark float-right"
            >
              <i class="fas fa-long-arrow-alt-left" /> {{ $t("common.back") }}
            </router-link>
          </div>
          <!-- /.card-header -->
          <!-- form start -->
          <form
            role="form"
            @submit.prevent="updateSettings"
            @keydown="form.onKeydown($event)"
          >
            <div class="card-body">
              <div class="row">
                <div class="form-group col-md-6">
                  <label for="stripe_public_key"
                    >{{ $t("common.stripe_public_key") }}
                    <span class="required">*</span></label
                  >
                  <input
                    id="stripe_public_key"
                    v-model="form.stripe_public_key"
                    type="password"
                    class="form-control"
                    :class="{
                      'is-invalid': form.errors.has('stripe_public_key'),
                    }"
                    name="stripe_public_key"
                    :placeholder="$t('common.stripe_public_key')"
                  />
                  <has-error :form="form" field="stripe_public_key" />
                </div>
                <div class="form-group col-md-6">
                  <label for="stripe_private_key"
                    >{{ $t("common.stripe_private_key") }}
                    <span class="required">*</span></label
                  >
                  <input
                    id="stripe_private_key"
                    v-model="form.stripe_private_key"
                    type="password"
                    class="form-control"
                    :class="{
                      'is-invalid': form.errors.has('stripe_private_key'),
                    }"
                    name="stripe_private_key"
                    :placeholder="$t('common.stripe_private_key')"
                  />
                  <has-error :form="form" field="stripe_private_key" />
                </div>
              </div>
            </div>
            <!-- /.card-body -->
            <div class="card-footer">
              <v-button :loading="form.busy" class="btn btn-primary">
                <i class="fas fa-edit" /> {{ $t("common.save_changes") }}
              </v-button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Form from "vform";
import { mapGetters } from "vuex";
import axios from "axios";

export default {
  layout: "central",
  middleware: ["auth", "check-permissions"],
  metaInfo() {
    return { title: this.$t("setup.advanced_settings.index.page_title") };
  },
  data: () => ({
    breadcrumbsCurrent: "setup.advanced_settings.index.breadcrumbs_current",
    breadcrumbs: [
      {
        name: "setup.advanced_settings.index.breadcrumbs_first",
        url: "setup.index",
      },
      {
        name: "setup.advanced_settings.index.breadcrumbs_active",
        url: "",
      },
    ],
    form: new Form({
      stripe_public_key: "",
      stripe_private_key: "",
    }),
    logo: "",
    blackLogo: "",
    smallLogo: "",
    favicon: "",
  }),
  computed: mapGetters({
    appInfo: "operations/appInfo",
    items: "operations/items",
  }),

  created() {
    this.getSettings();
  },

  methods: {
    // get settings
    async getSettings() {
      const { data } = await axios.get(
        window.location.origin + "/api/get-advanced-settings"
      );
      this.form.stripe_public_key = data.data.stripe_public_key.value;
      this.form.stripe_private_key = data.data.stripe_private_key.value;
    },

    // update settings
    async updateSettings() {
      await this.form
        .post(window.location.origin + "/api/update-advanced-settings")
        .then(() => {
          toast.fire({
            type: "success",
            title: this.$t("common.success_msg"),
          });
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

<style lang="scss" scoped></style>
