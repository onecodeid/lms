// 1 => LOADING
// 2 => DONE
// 3 => ERROR
import * as api from "./api_payment.js"
import { one_token } from "../../assets/js/global.js"

export default {
    namespaced: true,
    state: {
        search_status: 0,
        search_error_message: '',
        search: '',
        query: '',
        
        orders: [],
        total_order: 0,
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
        weight_total: 0
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
                    token: one_token()
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
                }
            } catch (e) {
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
            }
        },

        async search_item(context) {
            context.commit("update_search_status", 1)
            try {
                let prm = {
                    search: '',
                    order_id: context.state.selected_order.L_SoID,
                    token: one_token()
                }

                let resp = await api.search_item(prm)
                
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

                    context.commit("update_items", data.records)
                    context.commit("update_total_item", data.total)

                    let x = []
                    for (let i in data.records) {
                        let y = data.records[i]
                        if (y.L_SoDetailApproved == 'N')
                            y.L_SoDetailApprovedQty = y.L_SoDetailQty > y.I_StockQty ? y.I_StockQty : y.L_SoDetailQty
                        x.push(y.L_SoDetailID)
                        data.records[i] = y
                    }
                    context.commit('set_selected_items', x)

                }
            } catch (e) {
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
            }
        },
        
        async search_expedition(context) {
            context.commit("update_search_status", 1)
            try {
                let prm = {
                    token: one_token()
                }

                let resp = await api.search_expedition(prm)
                
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

                    context.commit("set_expeditions", data.records)
                    context.commit("set_total_expedition", data.total)
                }
            } catch (e) {
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
            }
        },
        
        async search_service(context) {
            context.commit("update_search_status", 1)
            try {
                let prm = {
                    token: one_token(),
                    to: context.state.selected_order.city_id,
                    courier: context.state.selected_expedition.M_ExpeditionROCode,
                    weight: context.state.weight_total
                }

                let resp = await api.search_service(prm)
                
                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("update_search_status", 2)
                    context.commit("update_search_error_message", "")

                    let data = JSON.parse(resp.data)

                    context.commit("set_services", data.rajaongkir.results[0].costs)
                    // context.commit("set_total_expedition", data.total)
                }
            } catch (e) {
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
            }
        },

        async confirm(context) {
            context.commit("update_search_status", 1)
            try {
                

                let prm = {
                    search: '',
                    token: one_token(),
                    json_data: JSON.stringify(x),
                    weight_add: context.state.weight_add,
                    expedition_id: context.state.selected_expedition.M_ExpeditionID,
                    service: context.state.selected_service.service,
                    cost: context.state.courier_cost
                }

                let resp = await api.approve(prm)
                
                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("update_search_status", 2)
                    context.commit("update_search_error_message", "")

                    
                    // let data = {
                    //     records: resp.data.records,
                    //     total: resp.data.total
                    // }

                    // context.commit("update_orders", data.records)
                    // context.commit("update_total_order", data.total)
                    context.commit('set_common', ['dialog_item', false])
                }
            } catch (e) {
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
            }
        }
    }
}