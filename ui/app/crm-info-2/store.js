// State
//  data ...
// Mutations
//
//
// Actions
import info from "./modules/info.js?t=1233";
import info_new from "./modules/info_new.js?t=1233";
import infoPreview from "./modules/info_preview.js?t=1233";
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
        info: info,
        info_new: info_new,
        infoPreview: infoPreview,
        system: system
    }
});