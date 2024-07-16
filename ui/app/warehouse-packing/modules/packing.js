// 1 => LOADING
// 2 => DONE
// 3 => ERROR
import * as api from "./api_packing.js"
import { URL, one_token } from "../../assets/js/global.js"

export default {
    namespaced: true,
    state: {
        edit: false,
        URL: URL,
        search_status: 0,
        search_error_message: '',
        search: '',
        query: '',
        
        orders: [],
        total_order: 0,
        selected_order: null,

        items: [],
        total_item: 0,
        selected_item: {},
        selected_items: [],
        dialog_item: false,
        dialog_packing: false,
        dialog_manifest: false,

        expeditions: [],
        total_expedition: 0,
        selected_expedition: {},

        sent_statuses: [{value:'Y', text:'Terkirim'}, {value:'N', text:'Belum Kirim'}],
        selected_sent_status: null,

        current_page: 1,
        sdate: new Date().toISOString().substr(0, 10),
        edate: new Date().toISOString().substr(0, 10),

        courier_name: '',
        courier_note: '',
        receipt_no: '',

        users: [],
        selected_user: null,

        manifests: []
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

        update_search(state, search) {
            state.search = search
        },

        update_orders(state, data) {
            state.orders = data
        },

        set_selected_order(state, val) {
            state.selected_order = val
        },

        update_search_status(state, val) {
            state.search_status = val
        },

        update_total_order(state, val) {
            state.total_order = val
        },

        update_items(state, data) {
            state.items = data
        },

        set_selected_item(state, val) {
            state.selected_item = val
        },

        set_users(state, data) {
            state.users = data
        },

        set_selected_user(state, val) {
            state.selected_user = val
        },

        set_total_expedition(state, v) {
            state.total_expedition = v
        },

        set_selected_items(state, val) {
            state.selected_items = val
        },

        update_total_item(state, val) {
            state.total_item = val
        },

        update_current_page(state, val) {
            state.current_page = val
        },

        set_sdate(state, val) {
            state.sdate = val
        },

        set_edate(state, val) {
            state.edate = val
        },

        set_selected_sent_status(state, val) {
            state.selected_sent_status = val
        },

        set_manifests (state, v) {
            state.manifests = v
        },

        set_expeditions (state, v) {
            state.expeditions = v
        },

        set_selected_expedition (state, v) {
            state.selected_expedition = v
        }
    },
    actions: {
        async search(context) {
            context.commit("update_search_status", 1)
            try {
                let prm = {
                    search: context.state.query,
                    sdate: context.state.sdate,
                    edate: context.state.edate,
                    expedition_id: context.state.selected_expedition ? context.state.selected_expedition.M_ExpeditionID : 0,
                    status: context.state.selected_sent_status ? context.state.selected_sent_status.value : 'A',
                    token: one_token()
                }

                let resp = await api.search(prm)
                
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

                    context.commit("update_orders", data.records)
                    context.commit("update_total_order", data.total)
                }
            } catch (e) {
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
            }
        },
        
        async search_user(context) {
            context.commit("update_search_status", 1)
            try {
                let prm = {
                    token: one_token(),
                    group_id: 4
                }

                let resp = await api.search_user(prm)
                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("update_search_status", 2)
                    context.commit("update_search_error_message", "")

                    context.commit("set_users", resp.data.records)
                }
            } catch (e) {
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
            }
        },

        async packing(context) {
            context.commit("update_search_status", 1)
            try {
                

                let prm = {
                    token: one_token(),
                    warehouse_id: context.state.selected_order.W_CourierID,
                    user_id: context.state.selected_user.user_id,
                }
                if (context.state.edit)
                    prm.packing_id = context.state.selected_order.W_PackingID

                let resp = await api.packing(prm)
                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("update_search_status", 2)
                    context.commit("update_search_error_message", "")

                    context.dispatch('search')
                    context.commit('set_common', ['dialog_packing', false])
                }
            } catch (e) {
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
            }
        },
        
        async search_expedition(context) {
            context.commit("update_search_status", 1)
            try {
                let prm = { token: one_token(), limit: 999 }
                let resp = await api.search_expedition(prm)
                
                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("update_search_status", 2)
                    context.commit("update_search_error_message", "")

                    context.commit("set_expeditions", resp.data.records)
                }
            } catch (e) {
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
            }
        }
    }
}