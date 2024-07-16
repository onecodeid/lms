// State
//  data ...
// Mutations
//
//
// Actions

import cod from "./modules/cod.js"
import cod_new from "./modules/cod_new.js"
import system from "../assets/js/system.js"

export const store = new Vuex.Store({
    state : {
        dialog_delete: false,
        dialog_print: false,
        dialog_image: false
    },

    mutations : {
        set_dialog_delete (state, v) {
            state.dialog_delete = v
        },

        set_dialog_print (state, v) {
            state.dialog_print = v
        },

        set_dialog_image (state, v) {
            state.dialog_image = v
        }
    },

    modules : {
        cod: cod,
        cod_new: cod_new,
        system: system
    }
});
