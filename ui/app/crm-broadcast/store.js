// State
//  data ...
// Mutations
//
//
// Actions
import broadcast from "./modules/broadcast.js?t=123";
import watemplate from "../crm-wa-template/modules/watemplate.js?t=123"
import system from "../assets/js/system.js";

export const store = new Vuex.Store({
    state: {
        dialog_delete: false,
        dialog_progress: false,
        snackbar: false,
        snackbar_text: 'Data telah tersimpan'
    },

    mutations: {
        set_dialog_delete(state, v) {
            state.dialog_delete = v
        },

        set_snackbar(state, v) {
            state.snackbar = v
        },

        set_snackbar_text(state, v) {
            state.snackbar_text = v
        },

        set_snackbars(state, v) {
            state.snackbar = v[0]
            state.snackbar_text = v[1]
        },

        set_dialog_progress(state, v) {
            state.dialog_progress = v
        }
    },

    modules: {
        broadcast: broadcast,
        watemplate: watemplate,
        system: system
    }
});