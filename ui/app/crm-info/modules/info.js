// 1 => LOADING
// 2 => DONE
// 3 => ERROR
import * as api from "./api_info.js"
import { one_token } from "../../assets/js/global.js"

export default {
    namespaced: true,
    state: {
        search_status: 0,
        search_error_message: '',
        search: '',

        infos: [],
        total_info: 0,
        total_info_page: 0,
        selected_info: {},
        shortcodes: ['customer_name',
            'customer_phone',
            'admin_name',
            'admin_phone',
            'order_number',
            'order_total',
            'order_total_text',
            'order_items',
            'company_name'
        ],

        current_page: 1
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

        set_object(state, v) {
            state[v[0]] = v[1]
        },

        update_search_error_message(state, msg) {
            state.search_error_message = msg
        },

        update_search(state, search) {
            state.search = search
        },

        update_infos(state, data) {
            state.infos = data
        },

        set_selected_info(state, val) {
            state.selected_info = val
        },

        update_search_status(state, val) {
            state.search_status = val
        },

        update_total_info(state, val) {
            state.total_info = val
        },

        update_total_info_page(state, val) {
            state.total_info_page = val
        },

        update_current_page(state, val) {
            state.current_page = val
        }
    },
    actions: {
        async search(context, prm) {
            context.commit("update_search_status", 1)
            try {
                prm.token = one_token()
                prm.search = context.state.search
                prm.page = context.state.current_page
                let resp = await api.search(prm)

                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("update_search_status", 2)
                    context.commit("update_search_error_message", "")
                    let data = {
                        records: resp.data.records,
                        total: resp.data.total,
                        total_page: resp.data.total_page
                    }

                    context.commit("update_infos", data.records)
                    context.commit("update_total_info", data.total)
                    context.commit("update_total_info_page", data.total_page)
                }
            } catch (e) {
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
                console.log(e)
            }
        },

        async del(context, prm) {
            context.commit("update_search_status", 1)
            try {
                prm.token = one_token()
                prm.id = context.state.selected_info.info_id

                let resp = await api.del(prm)
                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("update_search_status", 2)
                    context.commit("update_search_error_message", "")

                    context.dispatch('search', {})
                }
            } catch (e) {
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
                console.log(e)
            }
        },

        async pin(context) {
            let prm = {
                token: one_token(),
                id: context.state.selected_info.info_id
            }

            context.dispatch("system/postme", {
                url: "crm/info/pin",
                prm: prm,
                callback: function(d) {
                    context.dispatch("search", {})
                }
            }, { root: true })

        }
    }
}