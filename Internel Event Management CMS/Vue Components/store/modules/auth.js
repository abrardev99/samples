/*
|--------------------------------------------------------------------------
| Mutation Types
|--------------------------------------------------------------------------
*/
export const SET_USER = 'SET_USER';
export const UNSET_USER = 'UNSET_USER';

/*
|--------------------------------------------------------------------------
| Initial State
|--------------------------------------------------------------------------
*/
const initialState = {
	id: null,
	name: null,
	email: null,
	phone: null,
	status: null,
	role_id: null,
};

/*
|--------------------------------------------------------------------------
| Mutations
|--------------------------------------------------------------------------
*/
const mutations = {
	[SET_USER](state, payload) {
		state.name = payload.user.first_name + ' ' + payload.user.last_name;
		state.id = payload.user.id;
		state.email = payload.user.email;
		state.phone = payload.user.phone;
		state.status = payload.user.status;
		state.role_id = payload.user.role_id;
	},
	[UNSET_USER](state, payload) {
		state.name = null;
		state.id = null;
		state.email = null;
		state.phone = null;
		state.status = null;
		state.role_id = null;
	}
};

/*
|--------------------------------------------------------------------------
| Actions
|--------------------------------------------------------------------------
*/
const actions = {
	setAuthUser: (context, user) => {
		context.commit(SET_USER, {
			user
		})
	},
	unsetAuthUser: (context) => {
		context.commit(UNSET_USER);
	}
};

/*
|--------------------------------------------------------------------------
| Getters
|--------------------------------------------------------------------------
*/
const getters = {
	isLoggedIn: (state) => {
		return !!(state.name && state.email);
	},
	userData: (state) => {
		return state;
	}
};

/*
|--------------------------------------------------------------------------
| Export the module
|--------------------------------------------------------------------------
*/
export default {
	state: initialState,
	mutations,
	actions,
	getters
}