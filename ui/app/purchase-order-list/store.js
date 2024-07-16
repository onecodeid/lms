// State
//  data ...
// Mutations
//
//
// Actions

import purchase from "./modules/purchase.js";
import purchase_new from "./modules/purchase_new.js";
import newitem from "./modules/newitem.js";
import system from "../assets/js/system.js";

export const store = new Vuex.Store({
    state : {
        dialog_delete: false,
        dialog_print: false,
        dialog_progress: false,
        dialog_warning: false,
        dialog_confirm: false,

        snackbar: false,
        snackbar_text: ''
    },

    mutations : {
        set_dialog_delete (state, v) {
            state.dialog_delete = v
        },

        set_dialog_print (state, v) {
            state.dialog_print = v
        },

        set_dialog_progress (state, v) {
            state.dialog_progress = v
        },

        set_dialog_warning (state, v) {
            state.dialog_warning = v
        },

        set_text_warning (state, v) {
            state.text_warning = v
        },

        set_snackbar (state, v) {
            state.snackbar = v
        },

        set_snackbar_text (state, v) {
            state.snackbar_text = v
        },

        set_dialog_confirm (state, v) {
            state.dialog_confirm = v
        }
    },

    modules : {
        purchase: purchase,
        purchase_new: purchase_new,
        newitem: newitem,
        system: system
    }
});
