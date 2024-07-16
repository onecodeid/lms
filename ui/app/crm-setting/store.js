// State
//  data ...
// Mutations
//
//
// Actions
import setting from "./modules/setting.js";
import targetomzet from "../finance-target-omzet/modules/targetomzet.js";
import system from "../assets/js/system.js";

export const store = new Vuex.Store({
    state: {
        dialog_delete: false,
        dialog_progress: true
    },

    mutations: {
        set_dialog_delete(state, v) {
            state.dialog_delete = v
        },

        set_dialog_progress(state, v) {
            state.dialog_progress = v
        }
    },

    modules: {
        setting: setting,
        targetomzet: targetomzet,
        system: system
    }
});