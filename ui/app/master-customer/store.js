// State
//  data ...
// Mutations
//
//
// Actions
import customer from "./modules/customer.js?t=123";
import customer_new from "./modules/customer_new.js?t=123";
import customer_transfer from "./modules/customer_transfer.js?t=123";
import customer_filter from "./modules/customer_filter.js?t=123";
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
        customer: customer,
        customer_new: customer_new,
        customer_transfer: customer_transfer,
        customer_filter: customer_filter,
        system: system
    }
});