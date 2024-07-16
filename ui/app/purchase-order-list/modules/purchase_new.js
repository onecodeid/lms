// 1 => LOADING
// 2 => DONE
// 3 => ERROR
import * as api from "./api_purchase.js"
import { URL, one_token } from "../../assets/js/global.js"

export default {
    namespaced: true,
    state: {
        URL: URL,
        edit: false,
        dialog_new: false,
        search_status: 0,
        search_error_message: '',
        search: '',
        query: '',
        
        items: [],
        total_item: 0,
        selected_item: null,

        vendors: [],
        selected_vendor: null,

        order_id: 0,
        order_note: '',
        order_date: new Date().toISOString().substr(0, 10),

        current_page: 1,
        total_page: 1
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

        set_items(state, data) {
            state.items = data
        },

        set_selected_item(state, val) {
            state.selected_item = val
        },

        update_search_status(state, val) {
            state.search_status = val
        },

        update_current_page(state, val) {
            state.current_page = val
        },

        set_order_date(state, val) {
            state.order_date = val
        },

        set_vendors (state, val) {
            state.vendors = val
        },

        set_selected_vendor (state, val) {
            state.selected_vendor = val
        }
    },
    actions: {
        async save(context) {
            context.commit("update_search_status", 1)
            context.commit("set_dialog_progress", true, {root:true})
            try {
                let prm = {
                    hdata : JSON.stringify({
                        order_id: context.state.order_id,
                        vendor_id: context.state.selected_vendor.M_VendorID,
                        order_date: context.state.order_date,
                        order_note: context.state.order_note
                    }),
                    jdata : JSON.stringify(context.state.items),
                    token: one_token()
                }

                let resp = await api.save(prm)
                context.commit("set_dialog_progress", false, {root:true})
                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("update_search_status", 2)
                    context.commit("update_search_error_message", "")

                    context.commit('set_common', ['dialog_new', false])
                    context.dispatch('search')
                }
            } catch (e) {
                context.commit("set_dialog_progress", false, {root:true})
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
            }
        },

        async search_item(context) {
            context.commit("update_search_status", 1)
            try {
                let prm = {
                    search: context.state.query,
                    purchase_id: context.rootState.purchase.selected_order.P_PurchaseID,
                    page: context.state.current_page,
                    token: one_token()
                }

                let resp = await api.search_item(prm)
                
                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                    
                } else {
                    context.commit("update_search_status", 2)
                    context.commit("update_search_error_message", "")

                    context.commit("set_items", resp.data.records)
                    context.commit("set_common", ['total_item', resp.data.total])
                    context.commit("set_common", ['total_page', resp.data.total_page])
                }
            } catch (e) {
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
            }
        },
        
        async search_vendor(context) {
            context.commit("update_search_status", 1)
            try {
                let prm = {
                    search: context.state.query,
                    page: 1,
                    token: one_token()
                }

                let resp = await api.search_vendor(prm)
                
                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                    
                } else {
                    context.commit("update_search_status", 2)
                    context.commit("update_search_error_message", "")

                    context.commit("set_vendors", resp.data.records)
                    context.commit("set_common", ['total_page', resp.data.total_page])
                }
            } catch (e) {
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
            }
        }
    }
}