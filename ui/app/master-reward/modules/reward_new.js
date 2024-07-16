// 1 => LOADING
// 2 => DONE
// 3 => ERROR
import * as api from "./api_reward.js"
import { one_token } from "../../assets/js/global.js"

export default {
    namespaced: true,
    state: {
        search_status: 0,
        search_error_message: '',
        search: '',
        
        rewards: [],
        total_reward: 0,
        
        reward_name: 0,
        reward_code: '',
        reward_point: 0,
        reward_quota: 10,
        reward_tmb: '',

        dialog_new: false
    },
    mutations: {
        set_common (state, v) {
            let name = v[0]
            let val = v[1]
            if (typeof(val) == "string")
                eval(`state.${name} = "${val}"`)
            else
                eval(`state.${name} = ${val}`)
        },

        update_search_error_message(state, msg) {
            state.search_error_message = msg
        },

        update_search_status(state, val) {
            state.search_status = val
        }
    },
    actions: {
        async save(context, fn) {
            context.commit("update_search_status", 1)
            try {
                let prm = {}
                prm.token = one_token()
                prm.reward_name = context.state.reward_name
                prm.reward_code = context.state.reward_code
                prm.reward_point = context.state.reward_point
                prm.reward_quota = context.state.reward_quota
                prm.reward_image = context.rootState.image.imageUrl

                if (context.state.edit)
                    prm.reward_id = context.rootState.reward.selected_reward.M_RewardID

                let resp = await api.save(prm)
                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("update_search_status", 2)
                    context.commit("update_search_error_message", "")

                    if (fn) {
                        fn()
                    } else
                        context.dispatch('reward/search', {}, {root:true})
                    context.commit('set_common', ['dialog_new', false])
                }
                return resp
            } catch (e) {
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
            }
        }
    }
}