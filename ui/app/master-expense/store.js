// State
//  data ...
// Mutations
//
//
// Actions
import expense from "./modules/expense.js";
import expense_new from "./modules/expense_new.js";
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
        expense: expense,
        expense_new: expense_new,
        system: system
    }
});
