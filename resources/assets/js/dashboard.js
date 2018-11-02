import Vue from 'vue';
import Notifications from 'vue-notification';
import Datatable from 'vue2-datatable-component';

// components
import registerForm from './components/registerForm.vue';
import tablePanel from './components/tablePanel.vue';

export default function(options) {
    Vue.use(Notifications);
    Vue.use(Datatable);

    const table = new Vue({
        el: '#content',
        components: { tablePanel },
        template: '<tablePanel ref="tablePanel" :options="options" />',
        data: () => ({
            options: options,
        }),
        methods: {
            create(entry) {
                this.$refs.tablePanel.create(entry);
            },
            readerProxy() {
                this.$refs.tablePanel.readerProxy();
            },
        },
    });

    return new Vue({
        el: '#register',
        components: { registerForm },
        template: '<registerForm :table="table" :options="options" />',
        data: () => ({
            table: table,
            options: options,
        }),
    });
}
