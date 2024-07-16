// State
//  data ...
// Mutations
//
//
// Actions
import bankaccount from "./modules/bankaccount.js";
import bankaccount_new from "./modules/bankaccount_new.js";
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
        bankaccount: bankaccount,
        bankaccount_new: bankaccount_new,
        system: system
    }
});
