// State
//  data ...
// Mutations
//
//
// Actions
import z from "./modules/z.js";
import system from "../assets/js/system.js";

export const store = new Vuex.Store({
    state : {
        dialog_delete: false
    },

    mutations : {
        set_dialog_delete (state, v) {
            state.dialog_delete = v
        }
    },

    modules : {
        z: z,
        system: system
    }
});
