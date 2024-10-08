// State
//  data ...
// Mutations
//
//
// Actions
import customer from "./modules/customer.js?t=12ss3";
import customer_new from "./modules/customer_new.js?t=123s";
import customer_transfer from "./modules/customer_transfer.js?t=12s3";
import customer_filter from "./modules/customer_filter.js?t=12s3";
import xdate from "../common/modules/date.js?t=1s2"
import system from "../assets/js/system.js?t=2s34";

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
        xdate: xdate,
        system: system
    }
});