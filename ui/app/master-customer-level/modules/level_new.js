// 1 => LOADING
// 2 => DONE
// 3 => ERROR
import * as api from "./api_level.js"
import { one_token } from "../../assets/js/global.js"

export default {
    namespaced: true,
    state: {
        search_status: 0,
        search_error_message: '',
        search: '',

        categories: [],
        total_level: 0,

        level_name: 0,
        level_code: '',
        level_monthly_min: 0,
        level_monthly_max: 0,
        level_3month_min: 0,
        level_3month_max: 0,

        level_upgrade: null,
        level_downgrade: null,

        dialog_new: false
    },
    mutations: {
        set_common(state, v) {
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
        },

        set_level_upgrade(state, v) {
            state.level_upgrade = v
        },

        set_level_downgrade(state, v) {
            state.level_downgrade = v
        }
    },
    actions: {
        async save(context, fn) {
            context.commit("update_search_status", 1)
            try {
                let prm = {}
                prm.token = one_token()
                prm.level_name = context.state.level_name
                prm.level_code = context.state.level_code
                prm.level_upgrade = !!context.state.level_upgrade ? context.state.level_upgrade.level_id : 0
                prm.level_downgrade = !!context.state.level_downgrade ? context.state.level_downgrade.level_id : 0
                prm.level_monthly_min = context.state.level_monthly_min
                prm.level_monthly_max = context.state.level_monthly_max
                prm.level_3month_min = context.state.level_3month_min
                prm.level_3month_max = context.state.level_3month_max

                if (context.state.edit)
                    prm.level_id = context.rootState.level.selected_level.M_CustomerLevelID

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
                        context.dispatch('level/search', {}, { root: true })
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