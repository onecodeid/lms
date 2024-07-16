// State
//  data ...
// Mutations
//
//
// Actions

import report from "./modules/report.js?t=1234";
import report_param from "./modules/report_param.js?t=1234";
import system from "../assets/js/system.js?werwer?t=1234";

export const store = new Vuex.Store({
    state : {
        dialog_delete: false,
        dialog_print: false
    },

    mutations : {
        set_dialog_delete (state, v) {
            state.dialog_delete = v
        },

        set_dialog_print (state, v) {
            state.dialog_print = v
        }
    },

    modules : {
        report: report,
        report_param: report_param,
        system: system
    }
});
