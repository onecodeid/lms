// 1 => LOADING
// 2 => DONE
// 3 => ERROR
import * as api from "./api_salesorder.js"
import * as api_coupon from "./api_coupon.js"
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

        items: [],
        total_item: 0,
        selected_item: {},
        selected_items: [],

        packets: [],
        total_packet: 0,
        selected_packet: {},

        details: [],
        selected_detail: null,

        expeditions: [],
        total_expedition: 0,
        selected_expedition: null,

        services: [],
        total_service: 0,
        selected_service: null,

        origin: null,
        destination: null,

        courier_cost: 0,
        weight_add: 0,
        weight_total: 0,
        total: 0,
        ex_other: '',
        ex_note: '',
        order_note: '',

        dialog_quick: false,
        dialog_note: false,
        cities: [],
        selected_city: null,
        search_city: '',
        loading_city: false,

        dialog_expedition: false,
        loading_service: false,

        cust_id: 0,
        cust_name: '',
        cust_address: '',
        cust_postcode: '',
        cust_phone: '',

        cust_city: '',

        payments: [],
        selected_payment: null,

        dialog_customer: false,
        dialog_confirm_remove_coupon: false,

        coupon_code: '',
        coupon_type: '',
        coupon_amount: 0,
        coupon_amount_percent: 0,
        coupon_amounts: 0,
        coupon_id: 0,
        coupon_item_id: 0,
        coupon_courier_id: 0,
        coupon_set: false,
        coupon_note: '',
        coupon_error: false,
        coupon_error_msg: '',

        lead_id: 0,
        leadsources: [],
        selected_leadsource: null,

        sales_ads: '',
        sales_mp: ''
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
            let name = v[0]
            let val = v[1]
            state[name] = val
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
            // for (let i in data) {
            //     if (data[i].L_SoDetailApproved == 'N')
            //         data[i].L_SoDetailSubTotal = (data[i].L_SoDetailPrice - data[i].L_SoDetailDiscTotal) * data[i].L_SoDetailApprovedQty
            // }
            state.items = data

            // let t = 0
            // for (let d of data)
            //     t += parseFloat(d.M_ItemWeight * d.L_SoDetailApprovedQty);

            // let so = state.selected_order
            // so.L_SoSubTotalWeight = t

            // state.selected_order = so
            // state.weight_total = parseFloat(t) + parseFloat(state.weight_add)
        },

        set_selected_item(state, val) {
            state.selected_item = val
        },

        update_packets(state, data) {
            state.packets = data
        },

        set_selected_packet(state, val) {
            state.selected_packet = val
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

        set_details(state, v) {
            let total = 0
            let weight = 0
            for (let d of v) {
                total += parseFloat(d.item_subtotal)
                weight += parseFloat(d.item_weight * d.item_qty)
            }

            state.details = v
            state.total = total
            state.weight_total = weight
        },

        set_selected_detail(state, v) {
            state.selected_detail = v
        },

        set_cities(state, v) {
            state.cities = v
        },

        set_selected_city(state, v) {
            state.selected_city = v
        },

        set_payments(state, data) {
            state.payments = data
        },

        set_selected_payment(state, val) {
            state.selected_payment = val
        },

        set_coupon_item_id(state, val) {
            state.coupon_item_id = val
        },

        set_origin(state, val) {
            state.origin = val
        },

        set_destination(state, val) {
            state.destination = val
        },

        set_leadsources(state, data) {
            state.leadsources = data
        },

        set_selected_leadsource(state, val) {
            state.selected_leadsource = val
        }
    },
    actions: {
        async save(context) {
            context.commit("update_search_status", 1)
            context.commit("set_dialog_progress", true, { root: true })
            try {
                let prm = {
                    order_id: 0,
                    expedition_id: context.state.selected_expedition.M_ExpeditionID,
                    service: context.state.selected_service ? context.state.selected_service.service : '',
                    courier_cost: context.state.courier_cost,
                    ex_other: context.state.ex_other,
                    ex_note: context.state.ex_note,
                    payment_id: context.state.selected_payment.M_PaymentTypeID,
                    channel: context.state.selected_payment.channel_id ? context.state.selected_payment.channel_id : '',
                    json_data: context.state.details,
                    is_dropship: "N", //context.state.is_dropship,
                    ds_customer_id: 0, //context.state.is_dropship == "Y" ? context.state.selected_ds_customer.M_CustomerID : 0,
                    cust_id: context.state.cust_id,
                    cust_name: context.state.cust_name,
                    cust_address: context.state.cust_address,
                    cust_kelurahan_id: context.state.selected_city.kelurahan_id,
                    cust_postcode: context.state.cust_postcode,
                    cust_phone: context.state.cust_phone,
                    order_note: context.state.order_note,
                    leadsource: context.state.selected_leadsource.leadsource_id,

                    coupon_code: context.state.coupon_code,
                    coupon_type: context.state.coupon_type,
                    coupon_amount: context.state.coupon_amounts,
                    coupon_id: context.state.coupon_id,
                    coupon_note: context.state.coupon_note,

                    lead_id: context.state.lead_id,
                    sales_ads: context.state.sales_ads,
                    sales_mp: context.state.sales_mp,

                    token: one_token()
                }

                let resp = await api.save_qo(prm)
                context.commit("set_dialog_progress", false, { root: true })
                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                    context.commit("set_text_warning", resp.message, { root: true })
                    context.commit("set_dialog_warning", true, { root: true })
                } else {
                    context.commit("update_search_status", 2)
                    context.commit("update_search_error_message", "")

                    console.log(resp)
                    context.commit('set_common', ['dialog_quick', false])
                    context.dispatch('salesorder/search', null, { root: true })
                }
            } catch (e) {
                context.commit("set_dialog_progress", false, { root: true })
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
            }
        },

        async search_item(context) {
            context.commit("update_search_status", 1)
            try {
                let prm = {}
                prm.token = one_token()
                prm.customer_level = 5
                prm.search = context.state.query
                let resp = await api.search_items(prm)

                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("update_search_status", 2)
                    context.commit("update_search_error_message", "")

                    context.commit("update_items", resp.data.records)
                    context.commit("update_total_item", resp.data.total)
                }
            } catch (e) {
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
            }
        },

        async search_packet(context) {
            context.commit("update_search_status", 1)
            try {
                let prm = {}
                prm.token = one_token()
                prm.customer_level = 5
                prm.search = context.state.query
                let resp = await api.search_packets(prm)

                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("update_search_status", 2)
                    context.commit("update_search_error_message", "")

                    context.commit("update_packets", resp.data.records)
                        // context.commit("update_total_packet", resp.data.total)
                }
            } catch (e) {
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
            }
        },

        async search_city(context) {
            context.commit("update_search_status", 1)
            context.commit('set_common', ['loading_city', true])
            try {
                let prm = {
                    token: one_token(),
                    search: context.state.search_city
                }

                let resp = await api.search_city(prm)

                context.commit('set_common', ['loading_city', false])
                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("update_search_status", 2)
                    context.commit("update_search_error_message", "")

                    context.commit("set_cities", resp.data.records)
                }
            } catch (e) {
                context.commit('set_common', ['loading_city', false])
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
            context.commit('set_common', ['loading_service', true])
            try {
                let prm = {
                    token: one_token(),
                    to: context.state.selected_city.kecamatan_id,
                    courier: context.state.selected_expedition.M_ExpeditionROCode,
                    weight: context.state.weight_total
                }

                let resp = await api.search_service(prm)
                context.commit('set_common', ['loading_service', false])

                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("update_search_status", 2)
                    context.commit("update_search_error_message", "")

                    let data = JSON.parse(resp.data)

                    // context.commit('set_common', ['courier_cost', context.state.selected_order.L_SoExpCost])
                    context.commit("set_services", data.rajaongkir.results[0].costs)
                    context.commit('set_origin', data.rajaongkir.origin_details)
                    context.commit('set_destination', data.rajaongkir.destination_details)
                        // if (context.state.edit) {
                        //     for (let svc of context.state.services) {
                        //         if (svc.service == context.state.selected_order.L_SoExpService) {
                        //             context.commit('set_selected_service', svc)
                        //         }
                        //     }
                        // }
                        // context.commit('set_common', ['courier_cost', context.state.selected_service.cost[0].value])
                }
            } catch (e) {
                context.commit('set_common', ['loading_service', false])
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
            }
        },

        async search_payment_expanded(context) {
            context.commit("update_search_status", 1)
            try {
                let prm = {
                    token: one_token(),
                    is_cod: context.state.selected_expedition ? context.state.selected_expedition.M_ExpeditionIsCOD : 'N'
                }

                let resp = await api.search_payment_expanded(prm)

                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("update_search_status", 2)
                    context.commit("update_search_error_message", "")

                    context.commit("set_payments", resp.data.records)
                }
            } catch (e) {
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
            }
        },

        async check_coupon(context) {
            context.commit("update_search_status", 1)
            try {
                let x = []
                let y = []
                for (let i of context.state.details) {
                    if (i.is_packet == "N")
                        x.push(i.item_id)
                    else
                        y.push(i.item_id)
                }

                let prm = {
                    token: one_token(),
                    coupon_code: context.state.coupon_code,
                    courier_id: context.state.selected_expedition.M_ExpeditionID,
                    items: x,
                    packets: y,
                    spend: context.state.total
                }

                let resp = await api_coupon.check_coupon(prm)

                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                    context.commit('set_common', ['coupon_error', true])
                    context.commit('set_common', ['coupon_error_msg', resp.message])
                } else {
                    context.commit("update_search_status", 2)
                    context.commit("update_search_error_message", "")

                    let x = resp.data
                    context.commit('set_common', ['coupon_code', x.coupon_code])
                    context.commit('set_common', ['coupon_amount', x.coupon_amount])
                    context.commit('set_common', ['coupon_amount_percent', x.coupon_amount_percent])
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
                        for (let i of context.state.details) {
                            if (x.coupon_item_id.indexOf(parseInt(i.item_id)) > -1 && i.is_packet == 'N') {
                                i.coupon_amount = x.coupon_amount
                                i.coupon_amount_percent = x.coupon_amount_percent
                                    // i.item_subtotal = i.item_subtotal - (i.item_qty*x.coupon_amount)
                                coupon_amounts += (i.item_qty * x.coupon_amount)
                                if (parseFloat(x.coupon_amount_percent) > 0)
                                    coupon_amounts += (x.coupon_amount_percent * i.item_subtotal / 100)

                                coupon_note += (coupon_note == '' ? i.item_name : ', ' + i.item_name)
                                    // if (i.item_subtotal < 0 ) {
                                    //     coupon_amounts += 0 - i.item_subtotal
                                    //     i.item_subtotal = 0
                                    // }
                            }
                            it.push(i)
                        }
                        context.commit('set_details', it)
                        context.commit('set_common', ['coupon_note', 'Kupon Produk : ' + coupon_note])
                    } else if (x.coupon_type == 'COUPON.PACKET') {
                        let it = []
                        for (let i of context.state.details) {
                            console.log(i)
                            if (x.coupon_item_id.indexOf(parseInt(i.item_id)) > -1 && i.is_packet == 'Y') {
                                i.coupon_amount = x.coupon_amount
                                i.coupon_amount_percent = x.coupon_amount_percent
                                    // i.item_subtotal = i.item_subtotal - (i.item_qty*x.coupon_amount)
                                coupon_amounts += (i.item_qty * x.coupon_amount)
                                if (parseFloat(x.coupon_amount_percent) > 0)
                                    coupon_amounts += (x.coupon_amount_percent * i.item_subtotal / 100)

                                coupon_note += (coupon_note == '' ? i.item_name : ', ' + i.item_name)
                                    // if (i.item_subtotal < 0 ) {
                                    //     coupon_amounts += 0 - i.item_subtotal
                                    //     i.item_subtotal = 0
                                    // }
                            }
                            it.push(i)
                        }
                        context.commit('set_details', it)
                        context.commit('set_common', ['coupon_note', 'Kupon Produk : ' + coupon_note])
                    } else if (x.coupon_type == 'COUPON.COURIER') {
                        coupon_amounts = x.coupon_amount > context.state.courier_cost ? context.state.courier_cost : x.coupon_amount
                        if (parseFloat(x.coupon_amount_percent) > 0)
                            coupon_amounts += (x.coupon_amount_percent * context.state.courier_cost / 100)
                            // context.commit('set_common', ['coupon_amounts', coupon_amounts])
                        context.commit('set_common', ['coupon_note', 'Kupon Produk : ' + coupon_note])
                    } else {
                        coupon_amounts = x.coupon_amount
                        if (parseFloat(x.coupon_amount_percent) > 0) {
                            let ttl = context.state.courier_cost
                            for (let i of context.state.items)
                                ttl += parseFloat(i.item_subtotal)
                            coupon_amounts += (x.coupon_amount_percent * ttl / 100)
                        }
                    }
                    console.log(x.coupon_type)
                    console.log(x.coupon_item_id)
                    context.commit('set_common', ['coupon_amounts', coupon_amounts])

                }
            } catch (e) {
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
            }
        },

        async search_leadsource(context) {
            context.commit('set_common', ['save', true])
            let prm = {
                token: one_token(),
                limit: 999,
                search: ''
            }

            context.dispatch("system/postme", {
                url: "master/leadsource/search",
                prm: prm,
                callback: function(d) {
                    context.commit('set_leadsources', d.records)
                    context.commit('set_selected_leadsource', d.records[0])
                }
            }, { root: true })

        }
    }
}