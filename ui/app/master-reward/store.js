// State
//  data ...
// Mutations
//
//
// Actions
import reward from "./modules/reward.js";
import reward_new from "./modules/reward_new.js";
import image from "./modules/reward_image.js";
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
        reward: reward,
        reward_new: reward_new,
        image: image,
        system: system
    }
});
