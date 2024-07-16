// State
//  data ...
// Mutations
//
//
// Actions
import adjusttransfer from "./modules/adjusttransfer.js?t=234";
import system from "../assets/js/system.js";

export const store = new Vuex.Store({
    state : {
        dialog_delete: false
    },

    mutations : {
        set_dialog_delete (state, v) {
            state.dialog_delete = v
        }
    },

    modules : {
        adjusttransfer: adjusttransfer,
         system: system
    }
});
