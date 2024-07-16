// State
//  data ...
// Mutations
//
//
// Actions

import marketplace from "./modules/marketplace.js";
import system from "../assets/js/system.js";

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
        marketplace: marketplace,
         system: system
    }
});
