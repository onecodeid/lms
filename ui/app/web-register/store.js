// State
//  data ...
// Mutations
//
//
// Actions
import register from "./modules/register.js?t=123";
import system from "../assets/js/system.js";
import payment2 from "../finance-payment-admin-list/modules/payment.js?t=123";

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
        register: register,
        payment2: payment2,
        system: system
    }
});
