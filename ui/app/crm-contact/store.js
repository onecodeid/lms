// State
//  data ...
// Mutations
//
//
// Actions
import contact from "./modules/contact.js";
import system from "../assets/js/system.js?t=234";

export const store = new Vuex.Store({
    state: {
        dialog_delete: false,
        dialog_progress: false,
        dialog_print: false
    },

    mutations: {
        set_dialog_delete(state, v) {
            state.dialog_delete = v
        },

        set_dialog_progress(state, v) {
            state.dialog_progress = v
        },

        set_dialog_print(state, v) {
            state.dialog_print = v
        }
    },

    modules: {
        contact: contact,
        system: system
    }
});