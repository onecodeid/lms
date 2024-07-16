// State
//  data ...
// Mutations
//
//
// Actions
import leadsource from "../master-leadsource/modules/leadsource.js?t=12";
import leadsource_new from "../master-leadsource/modules/leadsource_new.js?t=1223";
import leadtype from "../master-leadtype/modules/leadtype.js?t=123";
import leadtype_new from "../master-leadtype/modules/leadtype_new.js?t=123";
import leadAttr from "./modules/leadattr.js?t=1";
import system from "../assets/js/system.js";

export const store = new Vuex.Store({
    state: {
        dialog_delete: false,
        tabs: [{ id: 'TAB.SOURCE', text: 'SUMBER' }, { id: 'TAB.TYPE', text: 'TIPE' }, { id: 'TAB.ATTR', text: 'ATRIBUT'}],
        selected_tab: null
    },

    mutations: {
        set_dialog_delete(state, v) {
            state.dialog_delete = v
        },

        set_selected_tab(state, v) {
            state.selected_tab = v
        }
    },

    modules: {
        leadsource: leadsource,
        leadsource_new: leadsource_new,
        leadtype: leadtype,
        leadtype_new: leadtype_new,
        leadAttr: leadAttr,
        system: system
    }
});