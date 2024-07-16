// State
//  data ...
// Mutations
//
//
// Actions
import campaign from "./modules/campaign.js";
import campaign_new from "./modules/campaign_new.js";
import campaigndetail from "./modules/campaigndetail.js";
import newitem from "../sales-order-seller-add/modules/newitem.js";
import system from "../assets/js/system.js";

export const store = new Vuex.Store({
    state : {
        dialog_delete: false,
        snackbar: false,
        snackbar_text: 'Data telah tersimpan'
    },

    mutations : {
        set_dialog_delete (state, v) {
            state.dialog_delete = v
        },

        set_snackbar (state, v) {
            state.snackbar = v
        },

        set_snackbar_text (state, v) {
            state.snackbar_text = v
        },

        set_snackbars (state, v) {
            state.snackbar = v[0]
            state.snackbar_text = v[1]
        }
    },

    modules : {
        campaign: campaign,
        campaign_new: campaign_new,
        campaigndetail: campaigndetail,
        newitem: newitem,
        system: system
    }
});
