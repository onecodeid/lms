// 1 => LOADING
// 2 => DONE
// 3 => ERROR
import * as api from "./api_category.js"
import { one_token } from "../../assets/js/global.js"

export default {
    namespaced: true,
    state: {
        search_status: 0,
        search_error_message: '',
        search: '',
        
        categories: [],
        total_category: 0,
        
        category_name: 0,
        category_code: '',

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
                prm.category_name = context.state.category_name
                prm.category_code = context.state.category_code

                if (context.state.edit)
                    prm.category_id = context.rootState.category.selected_category.M_CategoryID

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
                        context.dispatch('category/search', {}, {root:true})
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