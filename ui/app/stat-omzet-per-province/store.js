// State
//  data ...
// Mutations
//
//
// Actions
import omzet_province from "./modules/omzet_province.js";
// import omzet_province_new from "./modules/omzet_province_new.js";
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
        omzet_province: omzet_province,
        // omzet_province_new: omzet_province_new,
        system: system
    }
});
