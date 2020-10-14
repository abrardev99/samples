/*
|--------------------------------------------------------------------------
| Mutation Types
|--------------------------------------------------------------------------
*/
export const SET_EVENT = 'SET_EVENT';
export const UNSET_EVENT = 'UNSET_EVENT';

/*
|--------------------------------------------------------------------------
| Initial State
|--------------------------------------------------------------------------
*/
const initialState = {
    city: null,
    state: null,
    start_time: null,
    end_time: null,
    data: null
};

/*
|--------------------------------------------------------------------------
| Mutations
|--------------------------------------------------------------------------
*/
const mutations = {
    [SET_EVENT](state, payload) {
        state.city = payload.event.city;
        state.state = payload.event.state;
        state.start_time = payload.event.start_time;
        state.end_time = payload.event.end_time;
        state.data = payload.event.data;

    },
    [UNSET_EVENT](state, payload) {
        state.city = null;
        state.state = null;
        state.start_time = null;
        state.end_time = null;
        state.data = null;
    }
};

/*
|--------------------------------------------------------------------------
| Actions
|--------------------------------------------------------------------------
*/
const actions = {
    setEventsData: (context, event) => {
        context.commit(SET_EVENT, {
            event
        })
    },
    unsetEventsData: (SET_EVENT) => {
        context.commit(UNSET_EVENT);
    }
};

/*
|--------------------------------------------------------------------------
| Getters
|--------------------------------------------------------------------------
*/
const getters = {
    hasEventData: (state) => {
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