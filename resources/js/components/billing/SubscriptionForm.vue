<template>
  <div>
    <form class="form-horizontal" @submit.prevent="updateSubscriptionAllInOne">
      <!-- plans -->
      <div class="pricing-wrap" v-if="this.$store.state.operations.showPlan">
        <h6 class="my-3">{{ $t("Subscribe to a plan below") }}</h6>
        <plans
          @changePlan="changePlan"
          :planIdError="form.errors.has('plan_id')"
          :hasError="form"
        />
      </div>

      <div :class="{ 'd-none': form.plan_id == null }" id="card-wraper">
        <div v-if="tenant.plan_id != null">
          <div class="form-group">
            <!-- subscribe button -->
            <button
              class="btn btn-block btn-success"
              id="switch-plan-button"
              :disabled="loading"
              :class="{
                'btn-loading': loading,
              }"
              @click="switchSubscription"
            >
              {{ $t("Switch Plan") }}
            </button>
          </div>
        </div>
        <div :class="{ 'd-none': tenant.plan_id != null }" id="card">
          <div class="form-group row">
            <label for="name" class="col-sm-3 col-form-label"
              >{{ $t("billing.name") }} <span class="required">*</span></label
            >
            <div class="col-sm-9">
              <input
                type="text"
                v-model="name"
                class="form-control"
                id="name"
                :placeholder="$t('billing.name_placeholder')"
              />
            </div>
          </div>

          <div class="form-group row">
            <label for="card-element" class="col-sm-3 col-form-label">
              {{ $t("billing.card_details") }}
              <span class="required">*</span>
            </label>
            <div class="col-sm-9">
              <div id="card-element"></div>
            </div>
          </div>

          <div class="form-group">
            <!-- subscribe button -->
            <button
              class="btn btn-block btn-success"
              id="add-card-button"
              type="submit"
              :disabled="loading"
              :class="{
                'btn-loading': loading,
              }"
            >
              {{ $t("Subscribe") }}
            </button>
          </div>
        </div>
      </div>
    </form>
  </div>
</template>

<script>
import Plans from "~/components/billing/Plans";
import Form from "vform";

export default {
  name: "SubscriptionForm",

  components: {
    Plans,
  },

  props: {
    tenant: {
      type: Object,
      default: () => ({}),
    },
    cancelSubscriptionPlanId: {
      type: Number,
      default: null,
    },
    resumeSubscriptionPlanId: {
      type: Number,
      default: null,
    },
  },

  created() {
    this.name = this.tenant.name;
    this.loadIntent();
    this.includeStripe("js.stripe.com/v3/", () => {
      this.configureStripe();
    });
  },

  data: () => ({
    loading: false,
    user: "",
    plans: [],
    stripe: "",
    elements: "",
    card: "",
    intentToken: "",
    name: "",
    addPaymentStatus: 0,
    addPaymentStatusError: "",
    paymentMethods: [],
    paymentMethodsLoadStatus: 0,
    currentSubscriptions: [],
    form: new Form({
      plan_id: null,
      payment_method_id: {},
    }),
  }),

  watch: {
    cancelSubscriptionPlanId: function (cancelSubscriptionPlanId) {
      this.cancelSubscription(cancelSubscriptionPlanId);
    },
    resumeSubscriptionPlanId: function (resumeSubscriptionPlanId) {
      this.resumeSubscription(resumeSubscriptionPlanId);
    },
  },

  methods: {
    // get settings
    async refreshTenant() {
      await this.$store.dispatch("operations/fetchTenant");
    },

    changePlan(plan_id) {
      this.form.plan_id = plan_id;
    },

    // stripe functionality start
    /*
        Includes Stripe.js dynamically
    */
    includeStripe(URL, callback) {
      let documentTag = document,
        tag = "script",
        object = documentTag.createElement(tag),
        scriptTag = documentTag.getElementsByTagName(tag)[0];
      object.src = "https://" + URL;
      if (callback) {
        object.addEventListener(
          "load",
          function (e) {
            callback(null, e);
          },
          false
        );
      }
      scriptTag.parentNode.insertBefore(object, scriptTag);
    },
    /*
      Configures Stripe by setting up the elements and
      creating the card element.
    */
    configureStripe() {
      this.stripe = Stripe(this.$stripe_key);

      this.elements = this.stripe.elements();
      this.card = this.elements.create("card");

      this.card.mount("#card-element");
    },
    /*
      Loads the payment intent key for the user to pay.
    */
    loadIntent() {
      this.$axios.get("/api/subscriptions/setup-intent").then((response) => {
        this.intentToken = response.data;
      });
    },

    /*
      submit payment method
    */
    submitPaymentMethod() {
      this.addPaymentStatus = 1;

      this.stripe
        .confirmCardSetup(this.intentToken.client_secret, {
          payment_method: {
            card: this.card,
            billing_details: {
              name: this.name,
            },
          },
        })
        .then((result) => {
          if (result.error) {
            this.addPaymentStatus = 3;
            this.addPaymentStatusError = result.error.message;
          } else {
            this.savePaymentMethod(result.setupIntent.payment_method);
            this.addPaymentStatus = 2;
            this.card.clear();
            this.name = "";
          }
        });
    },

    /*
      Saves the payment method for the user and
      re-loads the payment methods.
    */
    savePaymentMethod(method) {
      this.$axios
        .post("/api/subscriptions/payment-methods", {
          payment_method: method,
        })
        .then((response) => {
          console.log(response);
        })
        .catch((error) => {
          console.log(error);
        });
    },

    // update subscription all in one
    updateSubscriptionAllInOne() {
      this.loading = true;
      this.addPaymentStatus = 1;

      this.stripe
        .confirmCardSetup(this.intentToken.client_secret, {
          payment_method: {
            card: this.card,
            billing_details: {
              name: this.name,
            },
          },
        })
        .then((result) => {
          if (result.error) {
            this.addPaymentStatus = 3;
            this.addPaymentStatusError = result.error.message;
            this.loading = false;
          } else {
            this.$axios
              .post("/api/subscriptions/payment-methods", {
                payment_method: result.setupIntent.payment_method,
              })
              .then(() => {
                this.form.payment_method_id = result.setupIntent.payment_method;
                this.form
                  .post("/api/subscriptions")
                  .then(() => {
                    toast.fire({
                      type: "success",
                      title: this.$t("You Are Subscribed!"),
                    });
                    this.refreshTenant();
                    this.card.clear();
                    this.form.reset();
                    this.name = "";
                    this.loading = false;
                    this.$store.dispatch("operations/setShowPlan", false);
                  })
                  .catch(() => {
                    toast.fire({
                      type: "error",
                      title: this.$t("common.error_msg"),
                    });
                    this.loading = false;
                  });
              })
              .catch((error) => {
                console.log(error);
                this.loading = false;
              });
          }
        });
    },

    switchSubscription() {
      this.form.post("/api/subscriptions").then(() => {
        this.refreshTenant();
        this.form.reset();
        this.loading = false;
        this.$store.dispatch("operations/setShowPlan", false);
        toast.fire({
          type: "success",
          title: this.$t("Plan switched!"),
        });
      });
    },

    cancelSubscription(plan_id) {
      this.$axios
        .post("/api/subscriptions/cancel", {
          plan_id: plan_id,
        })
        .then(() => {
          toast.fire({
            type: "success",
            title: this.$t("You Have Cancelled the Subscription!"),
          });
          this.refreshTenant();
        })
        .catch((error) => {
          console.log(error);
        });
    },

    resumeSubscription(plan_id) {
      this.$axios
        .post("/api/subscriptions/resume", {
          plan_id: plan_id,
        })
        .then(() => {
          toast.fire({
            type: "success",
            title: this.$t("You Have Resumed the Subscription!"),
          });
          this.refreshTenant();
        })
        .catch((error) => {
          console.log(error);
        });
    },
    // stripe functionality end
  },
};
</script>

<style scoped>
.pricing-wrap h2 {
  font-size: 15px;
  color: #4c4e51;
}

div#card-element {
  box-sizing: border-box;
  border: 1px solid #ced4da;
  padding: 10px;
  border-radius: 4px;
}

div#card-wraper {
  margin-top: 0px;
  background: #f9f9f9;
  padding: 20px;
  box-shadow: 0px 1px 2px #00000030;
  border-radius: 3px;
}

#name {
  background: transparent;
}
</style>
