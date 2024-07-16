// State
//  data ...
// Mutations
//
//
// Actions

import user from "./modules/user.js?t=w4";
import user_new from "./modules/user_new.js?t=1s23";
import system from "../assets/js/system.js";

export const store = new Vuex.Store({
    state: {
        dialog_delete: false,
        dialog_print: false,
        dialog_confirm: false,
    },

    mutations: {
        set_dialog_delete(state, v) {
            state.dialog_delete = v
        },

        set_dialog_print(state, v) {
            state.dialog_print = v
        },

        set_dialog_confirm(state, v) {
            state.dialog_confirm = v
        }
    },

    modules: {
        user: user,
        user_new: user_new,
        system: system
    }
});