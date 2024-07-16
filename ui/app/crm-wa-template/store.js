// State
//  data ...
// Mutations
//
//
// Actions
import watemplate from "./modules/watemplate.js";
import watemplate_new from "./modules/watemplate_new.js?t=123";
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
        watemplate: watemplate,
        watemplate_new: watemplate_new,
        system: system
    }
});