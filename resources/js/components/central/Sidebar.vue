<template>
  <!-- Main Sidebar Container -->
  <aside class="main-sidebar">
    <!-- Brand Logo -->
    <router-link :to="{ name: 'home' }" class="brand-link">
      <img v-if="appInfo" :src="appInfo.blackLogo" :alt="appInfo.companyName" class="lg-logo light-logo" />
      <img v-if="appInfo" :src="appInfo.logo" :alt="appInfo.companyName" class="lg-logo dark-logo" />
      <img v-if="appInfo" :src="appInfo.smallLogo" alt="appInfo.companyName" class="sm-logo" />
    </router-link>

    <!-- Sidebar -->
    <div class="sidebar custom-sidebar">
      <!-- Sidebar Menu -->
      <nav class="py-3 pb-5">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
          <li class="nav-header text-uppercase text-bold">
            {{ $t('sidebar.overview') }}
          </li>
          <li class="nav-item" v-if="
            $can('account-summery') ||
            $can('top-plans') ||
            $can('recent-activities') ||
            $can('top-clients')
          ">
            <router-link :to="{ name: 'home' }" class="nav-link">
              <i class="nav-icon fas fa-home" />
              <p>{{ $t('sidebar.dashboard') }}</p>
            </router-link>
          </li>

          <li class="nav-item">
            <a href="/" class="nav-link">
              <i class="nav-icon fas fa-arrow-left" />
              <p>{{ $t('sidebar.back_landing') }}</p>
            </a>
          </li>

          <li class="nav-header text-bold">
            {{ $t('sidebar.saas') }}
          </li>
          <li class="nav-item has-treeview" v-if="
          $can('plans-management') ||
          $can('features-management')"
            :class="menuOpen('plans') || menuOpen('features') ? 'menu-is-opening menu-open' : ''">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-money-check" />
              <p>
                {{ $t('sidebar.pricing') }}
                <i class="right fas fa-angle-left" />
              </p>
            </a>
            <ul class="nav nav-treeview" :style="
              menuOpen('plans') || menuOpen('features')
                ? 'display: block'
                : 'display: none'
            ">
              <li class="nav-item">
                <router-link :to="{ name: 'plans.index' }" class="nav-link">
                  <i class="nav-icon fas fa-list-alt" />
                  <p>{{ $t('sidebar.plans') }}</p>
                </router-link>
              </li>
              <li class="nav-item">
                <router-link :to="{ name: 'features.index' }" class="nav-link">
                  <i class="nav-icon fas fa-list-ul" />
                  <p>{{ $t('sidebar.plan_features') }}</p>
                </router-link>
              </li>
            </ul>
          </li>
          <li class="nav-item" v-if="$can('tenants-management')">
            <router-link :to="{ name: 'tenants.index' }" class="nav-link">
              <i class="nav-icon fas fa-users" />
              <p>{{ $t('sidebar.tenants') }}</p>
            </router-link>
          </li>

          <li class="nav-item" v-if="$can('promotion')">
            <router-link :to="{ name: 'newsletters-create' }" class="nav-link">
              <i class="nav-icon fas fa-gift" />
              <p>{{ $t('sidebar.promotion') }}</p>
            </router-link>
          </li>
          <li class="nav-item" v-if="$can('billing-history')">
            <router-link :to="{ name: 'billing.history.index' }" class="nav-link">
              <i class="nav-icon fas fa-money-bill" />
              <p>{{ $t('sidebar.Billing History') }}</p>
            </router-link>
          </li>
          <li class="nav-header text-bold">
            {{ $t('sidebar.cms') }}
          </li>
          <li class="nav-item" v-if="$can('pages-management')">
            <router-link :to="{ name: 'settings.index' }" class="nav-link">
              <i class="nav-icon fas fa-newspaper" />
              <p>{{ $t('sidebar.landing_page') }}</p>
            </router-link>
          </li>
          <li v-if="$can('pages-management')" class="nav-item">
            <router-link :to="{ name: 'pages.index' }" class="nav-link">
              <i class="nav-icon fas fa-images" />
              <p>{{ $t('sidebar.pages.create') }}</p>
            </router-link>
          </li>
          <li v-if="$can('newsletters-management')" class="nav-item">
            <router-link :to="{ name: 'newsletters.index' }" class="nav-link">
              <i class="nav-icon fas fa-envelope-open" />
              <p>{{ $t('sidebar.newsletters.create') }}</p>
            </router-link>
          </li>

          <li v-if="$can('domain-management')" class="nav-item has-treeview" :class="
            (menuOpen('domains') || menuOpen('domain-requests'))
              ? 'menu-is-opening menu-open'
              : ''
          ">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-database" />
              <p>
                {{ $t('sidebar.domains') }}
                <i class="right fas fa-angle-left" />
              </p>
            </a>
            <ul class="nav nav-treeview" :style="
              (menuOpen('domains') || menuOpen('domain-requests'))
                ? 'display: block'
                : 'display: none'
            ">
              <li class="nav-item">
                <router-link :to="{ name: 'domains.index' }" class="nav-link">
                  <i class="nav-icon fas fa-server" />
                  <p>{{ $t('sidebar.all_domains') }}</p>
                </router-link>
              </li>
              <li class="nav-item">
                <router-link :to="{ name: 'domain-requests.index' }" class="nav-link">
                  <i class="nav-icon fas fa-file-import" />
                  <p>{{ $t('sidebar.domain_requests') }}</p>
                </router-link>
              </li>
            </ul>
          </li>

          <li class="nav-header text-bold">
            {{ $t('sidebar.account') }}
          </li>
          <li v-if="
            $can('user-role') ||
            $can('user-management') ||
            $can('general-settings')
          " class="nav-item">
            <router-link :to="{ name: 'setup.index' }" class="nav-link">
              <i class="nav-icon fas fa-cogs" />
              <p>{{ $t('sidebar.setup') }}</p>
            </router-link>
          </li>
          <li v-if="$can('database-backup')" class="nav-item">
            <router-link :to="{ name: 'backup' }" class="nav-link">
              <i class="nav-icon fas fa-download" />
              <p>{{ $t('sidebar.database_backup') }}</p>
            </router-link>
          </li>
          <li class="nav-item has-treeview" :class="
            menuOpen('profile')
              ? 'menu-is-opening menu-open'
              : ''
          ">
            <a href="#" class="nav-link">
              <i class="nav-icon fas fa-user" />
              <p>
                {{ $t('sidebar.account_sm') }}
                <i class="right fas fa-angle-left" />
              </p>
            </a>
            <ul class="nav nav-treeview" :style="
              menuOpen('profile')
                ? 'display: block'
                : 'display: none'
            ">
              <li v-if="$can('update-profile')" class="nav-item">
                <router-link :to="{ name: 'profile' }" class="nav-link">
                  <i class="nav-icon fas fa-user-circle" />
                  <p>{{ $t('sidebar.profile') }}</p>
                </router-link>
              </li>
              <li class="nav-item">
                <a class="nav-link admin-logout" href="#" @click.prevent="logout">
                  <i class="nav-icon fas fa-power-off" />
                  {{ $t('sidebar.logout') }}
                </a>
              </li>
            </ul>
          </li>
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>
</template>

<script>
import { mapGetters } from 'vuex'

export default {
  data: () => ({
    appName: window.config.appName,
  }),
  // Map Getters
  computed: {
    ...mapGetters('operations', ['appInfo']),
  },
  mounted() {
    $('[data-widget="treeview"]').Treeview('init')
  },
  methods: {
    menuOpen(routeName) {
      if (this.$route.name) {
        let route = this.$route.name.split('.')[0]
        return route.indexOf(routeName) > -1
      }
      return false
    },
    async logout() {
      // Log out the user.
      await this.$store.dispatch('auth/logout')
      // Redirect to login.
      await this.$router.push({ name: 'login' })
    },
  },
}
</script>
