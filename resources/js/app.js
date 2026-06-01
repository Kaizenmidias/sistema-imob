console.log('App.js loading!');

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';

createInertiaApp({
  resolve: (name) => {
    console.log('Resolving page:', name);
    return resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue'));
  },
  setup({ el, App, props, plugin }) {
    console.log('Setting up app!');
    return createApp({ render: () => h(App, props) })
      .use(plugin)
      .mount(el);
  },
});
