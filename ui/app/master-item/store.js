// State
//  data ...
// Mutations
//
//
// Actions
import item from "./modules/item.js";
import item_new from "./modules/item_new.js?t=asd";
// import doctor from "./modules/doctor.js";
// import payment from "./modules/payment.js";
// import order from "./modules/order.js";
// import area from "./modules/area.js";
// import other from "./modules/other.js";
// import photo from "./modules/photo.js";
import system from "../assets/js/system.js?d=787";

export const store = new Vuex.Store({
    state : {
        dialog_delete: false,
        dialog_print: false,
        report_url: ''
    },

    mutations : {
        set_dialog_delete (state, v) {
            state.dialog_delete = v
        },

        set_dialog_print (state, v) {
            state.dialog_print = v
        },

        set_report_url (state, v) {
            state.report_url = v
        }
    },

    modules : {
        item: item,
        item_new: item_new,
        //  doctor: doctor,
        //  payment: payment,
        //  order: order,
        //  area: area,
        //  other: other,
        //  photo: photo,
         system: system
    }
});
