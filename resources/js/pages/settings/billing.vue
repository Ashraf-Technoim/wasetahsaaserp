<template>
  <div class="card">
    <div class="card-header setings-header">
      <h3 class="card-title">{{ $t("billing.form_title") }}</h3>
    </div>
    <div class="card-body">
      <div>
        <active-subscription
          :showResumePlanLink="showResumePlanLink"
          @cancelSubscription="cancelSubscription"
          @resumeSubscription="resumeSubscription"
        />
      </div>

      <subscription-form
        :showPlan="this.$store.state.operations.showPlan"
        :tenant="tenant"
        :cancelSubscriptionPlanId="cancelSubscriptionPlanId"
        :resumeSubscriptionPlanId="resumeSubscriptionPlanId"
      >
      </subscription-form>
    </div>
  </div>
</template>

<script>
import SubscriptionForm from "~/components/billing/SubscriptionForm";
import ActiveSubscription from "~/components/billing/ActiveSubscription";
import { mapGetters } from "vuex";

export default {
  middleware: ["auth", "check-permissions"],
  metaInfo() {
    return { title: this.$t("billing.page_title") };
  },
  created() {
    this.passedPlanEndsAt();
  },
  computed: {
    ...mapGetters("operations", ["tenant"]),
  },
  components: {
    ActiveSubscription,
    SubscriptionForm,
  },
  data: () => ({
    breadcrumbsCurrent: "billing.breadcrumbs_current",
    breadcrumbs: [
      {
        name: "billing.breadcrumbs_first",
        url: "home",
      },
      {
        name: "billing.breadcrumbs_active",
        url: "",
      },
    ],
    subscription: false,
    showPlan: false,
    cancelSubscriptionPlanId: null,
    resumeSubscriptionPlanId: null,
    showResumePlanLink: false,
  }),
  methods: {
    cancelSubscription(plan_id) {
      this.cancelSubscriptionPlanId = plan_id;
    },
    resumeSubscription(plan_id) {
      this.resumeSubscriptionPlanId = plan_id;
    },
    passedPlanEndsAt() {
      if (new Date(this.tenant.plan_ends_at) < new Date()) {
        this.$store.dispatch("operations/setShowPlan", true);
        this.showResumePlanLink = false;
        return true;
      }

      this.showResumePlanLink = true;
      return false;
    },
  },
};
</script>

<style scoped></style>
