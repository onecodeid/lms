// State
//  data ...
// Mutations
//
//
// Actions

import newitem from "./modules/newitem.js?abcde";
import salesorder from "./modules/salesorder.js?asdjkshs";
import system from "../assets/js/system.js?abcde";

export const store = new Vuex.Store({
    state: {
        dialog_delete: false,
        dialog_confirm: false,
        dialog_progress: false
    },

    mutations: {
        set_dialog_delete(state, v) {
            state.dialog_delete = v
        },

        set_dialog_confirm(state, v) {
            state.dialog_confirm = v
        },

        set_dialog_progress(state, v) {
            state.dialog_progress = v
        }
    },

    modules: {
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