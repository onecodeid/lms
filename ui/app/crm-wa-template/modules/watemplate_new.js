// 1 => LOADING
// 2 => DONE
// 3 => ERROR
import * as api from "./api_watemplate.js"
import { one_token, insertAtCursor } from "../../assets/js/global.js"

export default {
    namespaced: true,
    state: {
        search_status: 0,
        search_error_message: '',
        search: '',

        watemplates: [],
        total_watemplate: 0,

        watemplate_name: 0,
        watemplate_content: '',
        watemplate_img: '',

        colors: [],
        selected_color: null,

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

        set_object(state, val) {
            state[val[0]] = val[1]
        },

        update_search_error_message(state, msg) {
            state.search_error_message = msg
        },

        update_search_status(state, val) {
            state.search_status = val
        },

        set_content(state, val) {
            state.watemplate_content = val
        }
    },
    actions: {
        async save(context, fn) {
            context.commit("update_search_status", 1)
            try {
                let prm = {}
                prm.token = one_token()
                prm.watemplate_name = context.state.watemplate_name
                prm.watemplate_content = context.state.watemplate_content
                prm.watemplate_color = context.state.selected_color ? context.state.selected_color.color_id : 0
                prm.watemplate_img = context.state.watemplate_img

                if (context.state.edit)
                    prm.watemplate_id = context.rootState.watemplate.selected_watemplate.watemplate_id

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
                        context.dispatch('watemplate/search', {}, { root: true })
                    context.commit('set_common', ['dialog_new', false])
                }
                return resp
            } catch (e) {
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
            }
        },

        async search_color(context) {
            let prm = {
                token: one_token(),
                limit: 999,
                search: ''
            }

            context.dispatch("system/postme", {
                url: "master/color/search",
                prm: prm,
                callback: function(d) {
                    context.commit('set_object', ['colors', d.records])
                }
            }, { root: true })

        }
    }
}