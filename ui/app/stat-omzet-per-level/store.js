// State
//  data ...
// Mutations
//
//
// Actions
import omzet_level from "./modules/omzet_level.js";
// import omzet_level_new from "./modules/omzet_level_new.js";
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
        omzet_level: omzet_level,
        // omzet_level_new: omzet_level_new,
        system: system
    }
});
