// State
//  data ...
// Mutations
//
//
// Actions

import packing from "./modules/packing.js?t=ss";
import system from "../assets/js/system.js";

export const store = new Vuex.Store({
    state : {
        dialog_delete: false,
        dialog_print: false,
        dialog_confirm: false,
        dialog_manifest: false
    },

    mutations : {
        set_dialog_delete (state, v) {
            state.dialog_delete = v
        },

        set_dialog_print (state, v) {
            state.dialog_print = v
        },

        set_dialog_confirm (state, v) {
            state.dialog_confirm = v
        },

        set_dialog_manifest (state, v) {
            state.dialog_manifest = v
        }
    },

    modules : {
        packing: packing,
        system: system
    }
});
