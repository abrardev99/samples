import Vue from 'vue';
import Vuex from 'vuex';
import createPersistedState from 'vuex-persistedstate'
import auth from "./modules/auth";
import events from "./modules/events";


Vue.use(Vuex);

export default new Vuex.Store({
	modules: {
		auth,
		events
	},
	plugins: [
		createPersistedState({
			key: 'MYAPP'
		})
	],
	strict: true
});