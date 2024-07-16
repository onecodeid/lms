// State
//  data ...
// Mutations
//
//
// Actions
import omzet_product from "./modules/omzet_product.js";
// import omzet_product_new from "./modules/omzet_product_new.js";
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
        omzet_product: omzet_product,
        // omzet_product_new: omzet_product_new,
        system: system
    }
});
