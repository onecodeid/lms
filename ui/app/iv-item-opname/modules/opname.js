// 1 => LOADING
// 2 => DONE
// 3 => ERROR
import * as api from "./api_opname.js"
import { one_token, URL } from "../../assets/js/global.js"

export default {
    namespaced: true,
    state: {
        URL: URL,
        search_status: 0,
        search_error_message: '',
        search: '',
        query: '',
        
        opnames: [],
        total_opname: 0,
        selected_opname: {},

        current_page: 1,
        sdate: new Date().toISOString().substr(0, 10),
        edate: new Date().toISOString().substr(0, 10)
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

        update_search(state, search) {
            state.search = search
        },

        update_opnames(state, data) {
            state.opnames = data
        },

        set_selected_opname(state, val) {
            state.selected_opname = val
        },

        update_search_status(state, val) {
            state.search_status = val
        },

        update_total_opname(state, val) {
            state.total_opname = val
        },

        update_current_page(state, val) {
            state.current_page = val
        },

        set_sdate(state, val) {
            state.sdate = val
        },

        set_edate(state, val) {
            state.edate = val
        }
    },
    actions: {
        async search(context, prm) {
            context.commit("update_search_status", 1)
            try {
                prm.token = one_token()
                prm.sdate = context.state.sdate
                prm.edate = context.state.edate
                prm.search = context.state.query

                let resp = await api.search(prm)
                console.log(resp)
                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("update_search_status", 2)
                    context.commit("update_search_error_message", "")
                    let data = {
                        records: resp.data.records,
                        total: resp.data.total
                    }

                    context.commit("update_opnames", data.records)
                    context.commit("update_total_opname", data.total)
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
                console.log(prm)
                prm.token = one_token()
                let resp = await api.del(prm)
                console.log(resp)
                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("update_search_status", 2)
                    context.commit("update_search_error_message", "")
                    
                    context.dispatch('search', [])
                }
            } catch (e) {
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
                console.log(e)
            }
        }
    }
}