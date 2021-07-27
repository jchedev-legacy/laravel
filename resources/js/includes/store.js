import Vue from "vue";
import Vuex from "vuex";
import api from "~/includes/endpoints";

Vue.use(Vuex);

export default new Vuex.Store({
    strict: false,

    state: {
        account: null
    },

    mutations: {
        /**
         *
         * @param state
         * @param account
         */
        setAccount(state, account) {
            state.account = account;
        }
    },

    getters: {
        /**
         * Return the Account info (email, firstname, etc...)
         * @param state
         * @returns {getters.account|(function(*))|null|*|default.computed.account}
         */
        account(state) {
            return state.account;
        },
    },

    actions: {
        /**
         *
         * @returns {Promise<T | never>}
         */
        fetchAccount({dispatch, commit}) {
            return api.call('getAccount')
                .then((response) => {
                    commit('setAccount', response.data);
                })
                .catch((error) => {
                    console.error(error);

                    alert('Error when loading the profile. You will be logged out');

                    window.location.href = '/logout';
                });
        }
    },
});