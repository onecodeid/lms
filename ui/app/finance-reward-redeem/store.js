// State
//  data ...
// Mutations
//
//
// Actions
import reward from "./modules/reward.js";
import reward_new from "./modules/reward_new.js";
import redeem from "./modules/redeem.js";
import system from "../assets/js/system.js";

export const store = new Vuex.Store({
    state : {
        dialog_delete: false,
        dialog_confirm: false,
        tabs: [{id:1,label:'PENUKARAN REWARD'},{id:2,label:'HISTORI PENUKARAN'}],
        selected_tab: {id:1}
    },

    mutations : {
        set_dialog_delete (state, v) {
            state.dialog_delete = v
        },

        set_dialog_confirm (state, v) {
            state.dialog_confirm = v
        },

        set_selected_tab (state, v) {
            state.selected_tab = v
        }
    },

    modules : {
        reward: reward,
        redeem: redeem,
        reward_new: reward_new,
        system: system
    }
});
