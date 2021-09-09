import {VueManager} from "../lib/vue-manager";

VueManager.QuickMount('#app', '<data-file-manager />', require('./components/DataFileManager.vue').default)
