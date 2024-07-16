// 1 => LOADING
// 2 => DONE
// 3 => ERROR
import * as api from "./api_omzet.js"
import { one_token, one_years_month } from "../../assets/js/global.js"

export default {
    namespaced: true,
    state: {
        search_status: 0,
        search_error_message: '',
        search: '',
        query: '',
        
        orders: [],
        total_order: 0,
        total_order_page: 1,
        selected_order: {},

        items: [],
        total_item: 0,
        selected_item: {},
        selected_items: [],
        dialog_item: false,

        expeditions: [],
        total_expedition: 0,
        selected_expedition: {},

        services: [],
        total_service: 0,
        selected_service: {},

        current_page: 1,
        sdate: new Date().toISOString().substr(0, 10),
        edate: new Date().toISOString().substr(0, 10),

        courier_cost: 0,
        weight_add: 0,
        weight_total: 0,

        months: one_years_month(),

        dialog_degrade: false,

        levels: [],
        selected_level: null
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

        set_expeditions(state, data) {
            state.expeditions = data
        },

        set_selected_expedition(state, val) {
            state.selected_expedition = val
        },

        set_total_expedition(state, v) {
            state.total_expedition = v
        },

        set_services(state, data) {
            state.services = data
        },

        set_selected_service(state, val) {
            state.selected_service = val
        },

        set_total_service(state, v) {
            state.total_service = v
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

        set_levels(state, data) {
            state.levels = data
        },

        set_selected_level(state, val) {
            state.selected_level = val
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
                    level_id: context.state.selected_level.M_CustomerLevelID,
                    token: one_token(),
                    page: context.state.current_page
                }

                let resp = await api.search(prm)
                
                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("update_search_status", 2)
                    context.commit("update_search_error_message", "")

                    console.log(resp)
                    let data = {
                        records: resp.data.records,
                        total: resp.data.total
                    }

                    context.commit("update_orders", data.records)
                    context.commit("update_total_order", data.total)
                    context.commit('set_common', ['total_order_page', resp.data.total_page])
                }
            } catch (e) {
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
            }
        },

        async degrade(context) {
            context.commit("update_search_status", 1)
            try {
                
                let prm = {
                    token: one_token(),
                    customer_id: context.state.selected_order.customer_id
                }

                let resp = await api.degrade(prm)
                
                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("update_search_status", 2)
                    context.commit("update_search_error_message", "")

                    context.dispatch('search')
                }
            } catch (e) {
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
            }
        },

        async search_level(context) {
            context.commit("update_search_status", 1)
            try {
                let prm = { token : one_token() }
                let resp = await api.search_level(prm)
                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("update_search_status", 2)
                    context.commit("update_search_error_message", "")
                    
                    let x = ['CUST.DISTRIBUTOR','CUST.AGENCY','CUST.RESELLER']
                    let y = []
                    for (let r of resp.data.records)
                        if (x.indexOf(r.M_CustomerLevelCode)>-1)
                            y.push(r)
                    context.commit("set_levels", y)
                    context.commit("set_selected_level", y[0])
                    context.dispatch('search')
                }
            } catch (e) {
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
            }
        }
    }
}