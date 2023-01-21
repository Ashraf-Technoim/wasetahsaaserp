<template>
  <div>
    <div v-for="(plan, i) in plans" :key="i">

      <input @change="$emit('changePlan', plan.id)" v-model="selectedPlan" :value="plan.id" :id="plan.id" type="radio"
        name="plan_id" class="radio-plan d-none" :class="{ 'is-invalid': planIdError }">
      <has-error :form="hasError" field="plan_id" />
      <label :for="plan.id" class="price-single" :class="{ 'selected': plan.id == selectedPlan && !currentPlan(plan.id), 'current-plan': currentPlan(plan.id) }">
        <img :src="plan.image" :alt="plan.name" class="w-16 h-16 mr-3" />
        <div>
          <span>{{ plan.name }}</span>
          <span class="text-xs text-gray-700 d-block">{{ plan.description }}</span>
          <ul>
            <li>{{ $t('Client Limit') }}: <span>{{ plan.limit_clients | limitFormat }}</span></li>
            <li>{{ $t('Domains Limit') }}: <span>{{ plan.limit_domains | limitFormat }}</span></li>
            <li>{{ $t('Employee Limit') }}: <span>{{ plan.limit_employees | limitFormat }}</span></li>
            <li>{{ $t('Supplier Limit') }}: <span>{{ plan.limit_suppliers | limitFormat }}</span></li>
            <li>{{ $t('Purchase Limit') }}: <span>{{ plan.limit_purchases | limitFormat }}</span></li>
            <li>{{ $t('Invoice Limit') }}: <span>{{ plan.limit_invoices | limitFormat }}</span></li>
          </ul>
          <span class="price-badge text-capitalize">
            ${{ plan.amount }} / {{ plan.interval }}
          </span>
        </div>
      </label>
    </div>
  </div>
</template>

<script>
import {mapGetters} from "vuex";

export default {
  props: {
    planIdError: {
      type: [String, Boolean],
    },
    hasError: {
      type: Object,
    },
  },
  data: () => ({
    plans: [],
    selectedPlan: null
  }),
  created() {
    this.getPlans()
  },
  computed: {
    ...mapGetters('operations', ['tenant']),
  },
  methods: {
    currentPlan(planId){
      if( this.tenant.plan_id == planId){
        return true;
      }
      return false;
    },
    getPlans() {
      this.$axios.get('/api/plans')
        .then((response) => {
          this.plans = response.data.data
        }).catch((error) => {
          console.log(error)
        })
    },
    getImg(data) {
      return data
    }
  },
}
</script>

<style lang="scss" scoped>
.price-single {
  border: 2px solid #c9cafa5c;
  width: 100%;
  padding: 15px 14px !important;
  cursor: pointer;
  position: relative;
  margin: 0 !important;
  display: flex;
  align-items: flex-start;
  margin-bottom: 10px !important;
  border-radius: 5px;
}

.price-single img {
  max-width: 80px;
}

.price-single ul {
  display: grid;
  grid-template-columns: 1fr 1fr;
  margin: 8px 0 0 0;
  padding: 0;
  list-style: none;
}

.price-single ul li {
  font-size: 12px;
  margin-right: 12px;
}

.price-single ul li {
  font-size: 12px;
  color: #8b8f95;
}

.price-single ul li span {
  color: #6366f1;
}

.price-single .price-badge {
  position: absolute;
  right: 0px;
  bottom: 0px;
  margin: 0;
  background: #6366f1;
  color: #fff;
  padding: 3px 8px;
  font-size: 13px;
  border-top-left-radius: 12px;
}

label.price-single.selected {
  border-color: #6366f1;
}

.current-plan {
  background: #ddd;
  opacity: 1;
  cursor: no-drop !important;
}
</style>
