// State
//  data ...
// Mutations
//
//
// Actions
import leadtype from "./modules/leadtype.js";
import leadtype_new from "./modules/leadtype_new.js";
import system from "../assets/js/system.js";

export const store = new Vuex.Store({
    state: {
        dialog_delete: false
    },

    mutations: {
        set_dialog_delete(state, v) {
            state.dialog_delete = v
        }
    },

    modules: {
        leadtype: leadtype,
        leadtype_new: leadtype_new,
        system: system
    }
});