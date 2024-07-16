// State
//  data ...
// Mutations
//
//
// Actions
import general from "./modules/general.js";
import general_new from "./modules/general_new.js";
import system from "../assets/js/system.js";

export const store = new Vuex.Store({
    state : {
        dialog_delete: false,
        dialog_progress: false
    },

    mutations : {
        set_dialog_delete (state, v) {
            state.dialog_delete = v
        },

        set_dialog_progress (state, v) {
            state.dialog_progress = v
        }
    },

    modules : {
        general: general,
        general_new: general_new,
         system: system
    }
});
