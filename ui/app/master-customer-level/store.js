// State
//  data ...
// Mutations
//
//
// Actions
import level from "./modules/level.js";
import level_new from "./modules/level_new.js";
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
        level: level,
        level_new: level_new,
        system: system
    }
});