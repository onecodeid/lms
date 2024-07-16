// State
//  data ...
// Mutations
//
//
// Actions
import payment from "./modules/payment.js";
import payment2 from "../finance-payment-admin-list/modules/payment.js";
import system from "../assets/js/system.js";

export const store = new Vuex.Store({
    state : {
        dialog_delete: false,
        dialog_print: false,
        report_url: ''
    },

    mutations : {
        set_dialog_delete (state, v) {
            state.dialog_delete = v
        },
        
        set_dialog_print (state, v) {
            state.dialog_print = v
        },
        
        set_report_url (state, v) {
            state.report_url = v
        }
    },

    modules : {
        payment: payment,
        payment2: payment2,
        system: system
    }
});
