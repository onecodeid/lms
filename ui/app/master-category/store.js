// State
//  data ...
// Mutations
//
//
// Actions
import category from "./modules/category.js";
import category_new from "./modules/category_new.js";
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
        category: category,
        category_new: category_new,
        system: system
    }
});
