
import './bootstrap';
import { createApp } from 'vue';
import ExampleComponent from './components/ExampleComponent.vue';
import Example from './components/ExampleComponent.vue';

const app = createApp({
    components: {
        Example,
    }
});

app.mount('#app');
 