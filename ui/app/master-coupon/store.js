// State
//  data ...
// Mutations
//
//
// Actions
import coupon from "./modules/coupon.js?t=123";
import coupon_new from "./modules/coupon_new.js?t=123";
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
        coupon: coupon,
        coupon_new: coupon_new,
        system: system
    }
});
