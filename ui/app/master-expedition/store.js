// State
//  data ...
// Mutations
//
//
// Actions
import expedition from "./modules/expedition.js";
import expedition_new from "./modules/expedition_new.js";
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
        expedition: expedition,
        expedition_new: expedition_new,
        system: system
    }
});
