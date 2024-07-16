// State
//  data ...
// Mutations
//
//
// Actions
import home from "./modules/home.js";
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
        home: home,
        system: system
    }
});
