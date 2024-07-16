// State
//  data ...
// Mutations
//
//
// Actions

import newitem from "./modules/newitem.js";
import salesorder from "./modules/salesorder.js?t=67A";
// import payment from "./modules/payment.js";
// import order from "./modules/order.js";
// import area from "./modules/area.js";
// import other from "./modules/other.js";
// import photo from "./modules/photo.js";
import system from "../assets/js/system.js";

export const store = new Vuex.Store({
    state : {
        dialog_delete: false,
        dialog_confirm: false,
        dialog_progress: false,
        dialog_warning: false,

        text_warning: ''
    },

    mutations : {
        set_dialog_delete (state, v) {
            state.dialog_delete = v
        },

        set_dialog_confirm (state, v) {
            state.dialog_confirm = v
        },

        set_dialog_progress (state, v) {
            state.dialog_progress = v
        },

        set_dialog_warning (state, v) {
            state.dialog_warning = v
        },

        set_text_warning (state, v) {
            state.text_warning = v
        }
    },

    modules : {
        newitem: newitem,
        salesorder: salesorder,
        //  payment: payment,
        //  order: order,
        //  area: area,
        //  other: other,
        //  photo: photo,
         system: system
    }
});
