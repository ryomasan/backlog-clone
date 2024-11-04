export default defineNuxtConfig({
  devtools: { enabled: true },
  css: ['~/assets/css/main.css'],
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
})
