// State
//  data ...
// Mutations
//
//
// Actions

import saleslead from "./modules/saleslead.js?t=234"
import saleslead_new from "./modules/saleslead_new.js?3454"
import saleslead_convert from "./modules/saleslead_convert.js?34435"
import saleslead_address from "./modules/saleslead_address.js?3345"
import xdate from "../common/modules/date.js?t=12"
// import payment2 from "./modules/payment.js";
// import customer from "../master-customer/modules/customer.js"
// import payment from "../finance-payment-admin-list/modules/payment.js";
// import coupon from "./modules/coupon.js";
import system from "../assets/js/system.js?t=1";

export const store = new Vuex.Store({
    state: {
        dialog_delete: false,
        dialog_print: false,
        dialog_progress: false,
        dialog_warning: false,
        dialog_confirm: false,
        dialog_confirm_2: false,
        text_warning: 'Maaf Stok barang tersebut tidak mencukupi',

        snackbar: false,
        snackbar_text: ''
    },

    mutations: {
        set_dialog_delete(state, v) {
            state.dialog_delete = v
        },

        set_dialog_print(state, v) {
            state.dialog_print = v
        },

        set_dialog_progress(state, v) {
            state.dialog_progress = v
        },

        set_dialog_warning(state, v) {
            state.dialog_warning = v
        },

        set_text_warning(state, v) {
            state.text_warning = v
        },

        set_snackbar(state, v) {
            state.snackbar = v
        },

        set_snackbar_text(state, v) {
            state.snackbar_text = v
        },

        set_dialog_confirm(state, v) {
            state.dialog_confirm = v
        },

        set_dialog_confirm_2(state, v) {
            state.dialog_confirm = v
        }
    },

    modules: {
        saleslead: saleslead,
        saleslead_new: saleslead_new,
        saleslead_convert: saleslead_convert,
        saleslead_address: saleslead_address,
        xdate: xdate,
        // customer: customer,
        // payment: payment,
        // payment2: payment2,
        // coupon: coupon,
        system: system
    }
});