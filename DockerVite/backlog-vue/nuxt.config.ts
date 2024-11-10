export default defineNuxtConfig({
  // modules: [
  //   'nuxt-fontawesome'
  // ],
  // fontawesome: {
  //   imports: [
  //     {
  //       set: '@fortawesome/free-brands-svg-icons',
  //       icons: ['fab']
  //     }
  //   ]
  // },
  build: {
    transpile: [
      '@fortawesome/vue-fontawesome',
      '@fortawesome/fontawesome-svg-core',
      '@fortawesome/free-brands-svg-icons'
    ]
  },
  devtools: { enabled: true },
  css: ['~/assets/css/main.css', '@fortawesome/fontawesome-svg-core/styles.css'],
  postcss: {
    plugins: {
      tailwindcss: {},
      autoprefixer: {},
    },
  },
  ssr: false,
  components: true,
  runtimeConfig: {
    public: {
      apiBase: 'http://laravel:8000/api',
      compatibilityDate: process.env.COMPATIBILITY_DATE
    }
  },
  plugins: ['~/plugins/fontawesome.js']
})
