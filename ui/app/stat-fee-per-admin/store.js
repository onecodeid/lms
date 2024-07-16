// State
//  data ...
// Mutations
//
//
// Actions
import fee from "./modules/fee.js";
// import fee_new from "./modules/fee_new.js";
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
        fee: fee,
        // fee_new: fee_new,
        system: system
    }
});
