// 1 => LOADING
// 2 => DONE
// 3 => ERROR
import * as api from "./api_cod.js"
import { URL, UPL_URL, one_token } from "../../assets/js/global.js"

export default {
    namespaced: true,
    state: {
        URL: URL,
        UPL_URL: UPL_URL,
        edit: false,
        search_status: 0,
        search_error_message: '',
        search: '',
        query: '',
        edit: false,
        
        orders: [],
        total_order: 0,
        selected_order: {},

        items: [],
        total_item: 0,
        selected_item: {},
        selected_items: [],
        dialog_item: false,
        dialog_new: false,

        expeditions: [],
        total_expedition: 0,
        selected_expedition: null,

        current_page: 1,
        total_page: 1,
        sdate: new Date().toISOString().substr(0, 10),
        edate: new Date().toISOString().substr(0, 10),

        // statuses : [{value:'P',text:'Tagihan Terbayar'},{value:'C',text:'Pembayaran Dikonfirmasi'}],
        // selected_status: {value:'P',text:'Tagihan Terbayar'}
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
            for (let i in data) {
                if (data[i].L_SoDetailApproved == 'N')
                    data[i].L_SoDetailSubTotal = (data[i].L_SoDetailPrice - data[i].L_SoDetailDiscTotal) * data[i].L_SoDetailApprovedQty
            }
            state.items = data

            let t = 0
            for (let d of data)
                t += parseFloat(d.M_ItemWeight * d.L_SoDetailApprovedQty);
                
            let so = state.selected_order
            so.L_SoSubTotalWeight = t
            
            state.selected_order = so
            state.weight_total = parseFloat(t) + parseFloat(state.weight_add)
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

        // set_selected_status(state, val) {
        //     state.selected_status = val
        // }
    },
    actions: {
        async search(context) {
            context.commit("update_search_status", 1)
            try {
                let prm = {
                    search: context.state.query,
                    sdate: context.state.sdate,
                    edate: context.state.edate,
                    // status: context.state.selected_status?context.state.selected_status.value:'A',
                    page: context.state.current_page,
                    expedition_id: context.state.selected_expedition?context.state.selected_expedition.M_ExpeditionID:0,
                    token: one_token()
                }

                let resp = await api.search(prm)
                
                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("update_search_status", 2)
                    context.commit("update_search_error_message", "")

                    context.commit("update_orders", resp.data.records)
                    context.commit("update_total_order", resp.data.total)
                    context.commit("set_common", ['total_page', resp.data.total_page])
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
                    cod_id: context.state.selected_order.F_CodID,
                    token: one_token()
                }

                let resp = await api.search_item(prm)
                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("update_search_status", 2)
                    context.commit("update_search_error_message", "")

                    context.commit('cod_new/set_items', resp.data.records, {root:true})
                    context.commit('cod_new/set_common', ['total_item', resp.data.total], {root:true})
                }
            } catch (e) {
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
            }
        },

        async search_expedition(context) {
            context.commit("update_search_status", 1)
            try {
                let prm = { token: one_token() }
                let resp = await api.search_expedition(prm)
                
                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("update_search_status", 2)
                    context.commit("update_search_error_message", "")

                    context.commit("set_expeditions", resp.data.records)
                    context.commit("set_total_expedition", resp.data.total)
                }
            } catch (e) {
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
            }
        }
    }
}