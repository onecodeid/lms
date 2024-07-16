// State
//  data ...
// Mutations
//
//
// Actions
import targetomzet from "./modules/targetomzet.js";
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
        targetomzet: targetomzet,
        system: system
    }
});
