// State
//  data ...
// Mutations
//
//
// Actions
import leadsource from "./modules/leadsource.js";
import leadsource_new from "./modules/leadsource_new.js";
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
        leadsource: leadsource,
        leadsource_new: leadsource_new,
        system: system
    }
});
