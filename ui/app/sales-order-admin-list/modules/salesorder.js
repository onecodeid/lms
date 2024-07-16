// 1 => LOADING
// 2 => DONE
// 3 => ERROR
import * as api from "./api_salesorder.js"
import { URL, one_token } from "../../assets/js/global.js"

export default {
    namespaced: true,
    state: {
        URL: URL,
        edit: false,
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
        total_page: 1,
        sdate: window.local_date,//new Date().toISOString().substr(0, 10),
        edate: window.local_date,

        courier_cost: 0,
        weight_add: 0,
        weight_total: 0,

        dialog_payment: false,
        dialog_filter: false
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
                    page: context.state.current_page,
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

                    let x = []
                    for (let i in data.records) {
                        let y = data.records[i]
                        if (y.L_SoDetailApproved == 'N')
                            y.L_SoDetailApprovedQty = parseFloat(y.L_SoDetailQty) > parseFloat(y.I_StockQty) ? y.I_StockQty : y.L_SoDetailQty
                        x.push(y.L_SoDetailID)
                        data.records[i] = y
                    }

                    context.commit("update_items", data.records)
                    context.commit("update_total_item", data.total)

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
                    token: one_token(),
                    limit: 999
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
                let ctx = context.state
                
                let prm = {
                    token: one_token(),
                    to: (ctx.selected_order.L_SoIsDS == 'Y'?ctx.selected_order.ds_district_id:ctx.selected_order.district_id),
                    courier: ctx.selected_expedition.M_ExpeditionROCode,
                    weight: ctx.weight_total
                }

                let resp = await api.search_service(prm)
                
                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("update_search_status", 2)
                    context.commit("update_search_error_message", "")

                    let data = JSON.parse(resp.data)

                    context.commit('set_common', ['courier_cost', context.state.selected_order.L_SoExpCost])
                    context.commit("set_services", data.rajaongkir.results[0].costs)
                    if (context.state.edit) {
                        for (let svc of context.state.services) {
                            if (svc.service == context.state.selected_order.L_SoExpService) {
                                context.commit('set_selected_service', svc)
                            }
                        }
                    }
                    context.commit('set_common', ['courier_cost', context.state.selected_service.cost[0].value])
                }
            } catch (e) {
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
            }
        },

        async approve(context) {
            context.commit("update_search_status", 1)
            context.commit("set_dialog_progress", true, {root:true})
            try {
                let x = []
                let y = context.state
                for (let i in y.items) {
                    if (y.selected_items.indexOf(y.items[i].L_SoDetailID) > -1)
                        x.push({id:y.items[i].L_SoDetailID, qty:y.items[i].L_SoDetailApprovedQty})
                }

                let prm = {
                    search: '',
                    token: one_token(),
                    json_data: JSON.stringify(x),
                    weight_add: context.state.weight_add,
                    expedition_id: context.state.selected_expedition.M_ExpeditionID,
                    service: context.state.selected_service.service,
                    cost: context.state.courier_cost,
                    coupon_id: context.rootState.coupon.coupon_id,
                    coupon_amount: context.rootState.coupon.coupon_amount,
                    coupon_note: context.rootState.coupon.coupon_note
                }

                let resp = await api.approve(prm)
                
                context.commit("set_dialog_progress", false, {root:true})
                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("u pdate_search_error_message", resp.message)
                    context.commit('set_text_warning', resp.message, {root:true})
                    context.commit('set_dialog_warning', true, {root:true})
                } else {
                    context.commit("update_search_status", 2)
                    context.commit("update_search_error_message", "")

                    context.commit('set_common', ['dialog_item', false])
                    context.dispatch('search', {})

                    // this.report_url = this.$store.state.salesorder.URL+"report/one_iv_001?soid="+so.L_SoID
                    context.commit('set_dialog_print', true, {root:true})
                }
            } catch (e) {
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
                context.commit('set_text_warning', e.message, {root:true})
                context.commit('set_dialog_warning', true, {root:true})
                context.commit("set_dialog_progress", false, {root:true})
            }
        },

        async cancel_item(context) {
            context.commit("update_search_status", 1)
            context.commit('set_dialog_progress', true, {root:true})
            try {
                let prm = {id:context.state.selected_item.L_SoDetailID}
                let resp = await api.cancel_item(prm)
                
                context.commit('set_dialog_progress', false, {root:true})
                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                    context.commit('set_text_warning', resp.message, {root:true})
                    context.commit('set_dialog_warning', true, {root:true})
                } else {
                    context.commit("update_search_status", 2)
                    context.commit("update_search_error_message", "")
                    

                    // this.report_url = this.$store.state.salesorder.URL+"report/one_iv_001?soid="+so.L_SoID
                    context.dispatch('search_item')
                }
            } catch (e) {
                context.commit('set_dialog_progress', false, {root:true})
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
                context.commit('set_text_warning', e.message, {root:true})
                context.commit('set_dialog_warning', true, {root:true})
            }
        },

        async send_email(context) {
            context.commit("update_search_status", 1)
            context.commit('set_dialog_progress', true, {root:true})
            try {
                let prm = {invoice_id:context.state.selected_order.L_InvoiceID}
                let resp = await api.send_email(prm)
                
                context.commit('set_dialog_progress', false, {root:true})
                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                    context.commit('set_text_warning', resp.message, {root:true})
                    context.commit('set_dialog_warning', true, {root:true})
                } else {
                    context.commit("update_search_status", 2)
                    context.commit("update_search_error_message", "")
                    context.commit('set_snackbar_text', resp.data, {root:true})
                    context.commit('set_snackbar', true, {root:true})
                }
            } catch (e) {
                context.commit('set_dialog_progress', false, {root:true})
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
                context.commit('set_text_warning', e.message, {root:true})
                context.commit('set_dialog_warning', true, {root:true})
            }
        },

        async cancel(context) {
            context.commit("update_search_status", 1)
            try {

                let prm = {
                    token: one_token(),
                    so_id: context.state.selected_order.L_SoID
                }

                let resp = await api.cancel(prm)
                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                    context.commit('set_text_warning', resp.message, {root:true})
                    context.commit('set_dialog_warning', true, {root:true})
                } else {
                    context.commit("update_search_status", 2)
                    context.commit("update_search_error_message", "")
                    context.dispatch('search', {})
                }
            } catch (e) {
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
                context.commit('set_text_warning', e.message, {root:true})
                context.commit('set_dialog_warning', true, {root:true})
            }
        },
        
        async check_coupon(context) {
            context.commit("update_search_status", 1)
            try {
                let x = []
                for (let i of context.state.items)
                    if (i.is_packet == "N")
                        x.push(i.M_ItemID)
                let prm = {
                    token: one_token(),
                    coupon_code: context.state.coupon_code,
                    courier_id: context.state.selected_expedition.M_ExpeditionID,
                    items: x,
                    packets: [],
                    spend: context.state.total
                }

                let resp = await api.check_coupon(prm)
                
                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                    context.commit('set_common', ['coupon_error', true])
                    context.commit('set_common', ['coupon_error_msg', resp.message])
                } else {
                    context.commit("update_search_status", 2)
                    context.commit("update_search_error_message", "")

                    let x = resp.data
                    console.log(x)
                    return
                    context.commit('set_common', ['coupon_code', x.coupon_code])
                    context.commit('set_common', ['coupon_amount', x.coupon_amount])
                    context.commit('set_common', ['coupon_type', x.coupon_type])
                    context.commit('set_common', ['coupon_id', x.coupon_id])
                    context.commit('set_common', ['coupon_courier_id', x.coupon_courier_id])
                    context.commit('set_coupon_item_id', x.coupon_item_id)
                    context.commit('set_common', ['coupon_set', true])
                    context.commit('set_common', ['coupon_error', false])
                    context.commit('set_common', ['coupon_error_msg', ''])
                    
                    let coupon_amounts = 0
                    let coupon_note = ''
                    if (x.coupon_type == 'COUPON.PRODUCT' || x.coupon_type == 'COUPON.CATEGORY') {
                        let it = []
                        for (let i of context.state.items) {
                            if (x.coupon_item_id.indexOf(parseInt(i.item_id)) > -1 && i.is_packet == 'N') {
                                i.coupon_amount = x.coupon_amount
                                // i.item_subtotal = i.item_subtotal - (i.item_qty*x.coupon_amount)
                                coupon_amounts += (i.item_qty*x.coupon_amount)
                                coupon_note += (coupon_note == '' ? i.item_name : ', '+i.item_name)
                                if (i.item_subtotal < 0 ) {
                                    coupon_amounts += 0 - i.item_subtotal
                                    i.item_subtotal = 0
                                }
                            }
                            it.push(i)
                        }
                        context.commit('update_items', it)
                        context.commit('set_common', ['coupon_note', 'Kupon Produk : '+coupon_note])
                    } else if (x.coupon_type == 'COUPON.PACKET') {
                        let it = []
                        for (let i of context.state.items) {
                            if (x.coupon_item_id.indexOf(parseInt(i.item_id)) > -1 && i.is_packet == 'Y') {
                                i.coupon_amount = x.coupon_amount
                                // i.item_subtotal = i.item_subtotal - (i.item_qty*x.coupon_amount)
                                coupon_amounts += (i.item_qty*x.coupon_amount)
                                coupon_note += (coupon_note == '' ? i.item_name : ', '+i.item_name)
                                if (i.item_subtotal < 0 ) {
                                    coupon_amounts += 0 - i.item_subtotal
                                    i.item_subtotal = 0
                                }
                            }
                            it.push(i)
                        }
                        context.commit('set_details', it)
                        context.commit('set_common', ['coupon_note', 'Kupon Produk : '+coupon_note])
                    } else if (x.coupon_type == 'COUPON.COURIER') {
                        coupon_amounts = x.coupon_amount > this.courier_cost ? this.courier_cost : x.coupon_amount
                        // context.commit('set_common', ['coupon_amounts', coupon_amounts])
                        context.commit('set_common', ['coupon_note', 'Kupon Produk : '+coupon_note])
                    } else {
                        coupon_amounts = x.coupon_amount
                    }
                    context.commit('set_common', ['coupon_amounts', coupon_amounts])

                }
            } catch (e) {
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
            }
        }
    }
}