// 1 => LOADING
// 2 => DONE
// 3 => ERROR
import * as api from "./api_leadsource.js"
import { one_token } from "../../assets/js/global.js"

export default {
    namespaced: true,
    state: {
        search_status: 0,
        search_error_message: '',
        search: '',

        categories: [],
        total_leadsource: 0,

        leadsource_name: 0,
        leadsource_code: '',
        leadsource_prefix: 'SOURCE.',
        leadsource_removable: 'Y',
        leadsource_color: 'black',

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
        }
    },
    actions: {
        async save(context, fn) {
            context.commit("update_search_status", 1)
            try {
                let prm = {}
                prm.token = one_token()
                prm.leadsource_name = context.state.leadsource_name
                prm.leadsource_code = context.state.leadsource_prefix + context.state.leadsource_code
                prm.leadsource_color = context.state.leadsource_color
                prm.leadsource_removable = context.state.leadsource_removable

                if (context.state.edit)
                    prm.leadsource_id = context.rootState.leadsource.selected_leadsource.leadsource_id

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
                        context.dispatch('leadsource/search', {}, { root: true })
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