<template>
  <!-- HEADER AREA START -->
  <section>
    <nav class="navbar navbar-expand-lg fixed-top">
      <div class="container">
        <router-link :to="{ name: 'welcome' }" class="navbar-brand">
          <img :src="appInfo && appInfo.blackLogo" alt="logo" />
        </router-link>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
          aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"
            stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16m-7 6h7" />
          </svg>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
          <scrollactive active-class="active" :duration="1000" class="navbar-nav m-auto">
            <li class="nav-item">
              <a class="nav-link scrollactive-item" href="#home">
                {{ $t('template.home') }} <span class="sr-only">(current)</span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link scrollactive-item" href="#about-us">{{ $t('template.about') }}</a>
            </li>
            <li class="nav-item">
              <a class="nav-link scrollactive-item" href="#features">{{ $t('template.features') }}</a>
            </li>
            <li class="nav-item">
              <a class="nav-link scrollactive-item" href="#core-modules">{{ $t('template.modules') }}</a>
            </li>
            <li class="nav-item">
              <a class="nav-link scrollactive-item" href="#pricing-area">{{ $t('template.pricing') }}</a>
            </li>
            <li class="nav-item">
              <a class="nav-link scrollactive-item" href="#footer">{{ $t('template.contact') }}</a>
            </li>
          </scrollactive>
          <div class="form-inline my-2 my-lg-0 position-relative">
            <ul class="navbar-nav" v-if="!user">
              <li>
                <router-link :to="{ name: 'find-domain' }" class="btn my-2 my-sm-0">{{ $t('template.sign_in') }}
                </router-link>
              </li>
              <li>
                <router-link :to="{ name: 'register' }" class="btn btn-outline-primary my-2 my-sm-0">{{
                    $t('template.register')
                }}
                </router-link>
              </li>
            </ul>
            <div v-else>
              <a class="nav-link user-profile" data-toggle="dropdown" href="#">
                <div>
                  <img :src="user.photo_url" :alt="user.name" />
                </div>
                <div>
                  <p class="mb-0 ml-2 d-none d-md-block">{{ user.name }}</p>
                </div>
                <span class="mt-1 ml-1">
                  <i class="fas fa-angle-down"></i>
                </span>
              </a>
              <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                <router-link :to="{ name: 'home' }" class="dropdown-item dropdown-icon-center">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                      d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  {{ $t('sidebar.dashboard') }}
                </router-link>
                <div class="dropdown-divider" />
                <a href="#" class="dropdown-item dropdown-icon-center" @click.prevent="logout">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round"
                      d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                  </svg>
                  {{ $t('topbar.logout') }}
                </a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </nav>
  </section>
  <!-- HEADER AREA END -->
</template>

<script>

import { mapGetters } from 'vuex'

export default {
  data: () => ({
    appName: window.config.appName
  }),

  computed: mapGetters({
    user: 'auth/user',
    appInfo: 'operations/appInfo'
  }),

  methods: {

    handleSCroll() {
      let header = document.querySelector('.navbar')
      if (window.scrollY > 100 && !header.className.includes('menu-bg')) {
        header.classList.add('menu-bg')
      } else if (window.scrollY < 100) {
        header.classList.remove('menu-bg')
      }
    },

    async logout() {
      // Log out the user.
      await this.$store.dispatch('auth/logout')
      // Redirect to login.
      this.$router.push({ name: 'login' })
    }

  },

  created() {
    window.addEventListener('scroll', this.handleSCroll)

  },

  destroyed() {
    window.removeEventListener('scroll', this.handleSCroll)
  }
}
</script>


<style scoped>
.user-profile img {
  width: 40px;
  height: 40px;
  border-radius: 100%;
}

.user-profile {
  padding: 0;
  display: flex;
  justify-content: center;
  align-items: center;
}

.dropdown-icon-center {
  display: flex;
  align-items: center;
}

.dropdown-icon-center svg {
  margin-right: 4px;
}
</style>
