// 1 => LOADING
// 2 => DONE
// 3 => ERROR
import * as api from "./api_salesorder.js"
import { one_token } from "../../assets/js/global.js"

export default {
    namespaced: true,
    state: {
        search_status: 0,
        search_error_message: '',
        search: '',

        items: [],
        total_item: 0,
        selected_item: {},

        expeditions: [],
        total_expedition: 0,
        selected_expedition: null,

        services: [],
        total_service: 0,
        selected_service: {},
        loading_service: false,

        origin: null,
        destination: null,

        payments: [],
        total_payment: 0,
        selected_payment: null,
        selected_payment_id: 0,


        channels: [{ value: 'indomaret', text: 'Indomaret' }, { value: 'alfamart', text: 'Alfamart' }],
        selected_channel: null,
        accounts: [],

        customers: [],
        selected_customer: null,
        search_customer: '',

        ds_customers: [],
        selected_ds_customer: null,
        search_ds_customer: '',

        total_weight: 0,
        total_price: 0,
        courier_cost: 0,
        is_dropship: 'N',
        ex_other: '',
        ex_note: '',

        current_page: 1,
        tabs: [1, 2],
        selected_tab: 1,
        order_note: '',

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

        update_items(state, data) {
            state.items = data

            let t = 0
            for (let d of data)
                t += parseFloat(d.item_weight * d.item_qty)

            state.total_weight = t
        },

        set_selected_item(state, val) {
            state.selected_item = val
        },

        update_search_status(state, val) {
            state.search_status = val
        },

        update_total_item(state, val) {
            state.total_item = val
        },

        set_expeditions(state, data) {
            state.expeditions = data
        },

        set_selected_expedition(state, val) {
            state.selected_expedition = val
        },

        set_payments(state, data) {
            state.payments = data
        },

        set_selected_payment(state, val) {
            state.selected_payment = val
        },

        set_services(state, data) {
            state.services = data
        },

        set_accounts(state, data) {
            state.accounts = data
        },

        set_selected_service(state, val) {
            state.selected_service = val
            if (!val)
                state.courier_cost = 0
            else
                state.courier_cost = parseFloat(val.cost[0].value)
        },

        update_current_page(state, val) {
            state.current_page = val
        },

        set_ds_customers(state, data) {
            state.ds_customers = data
        },

        set_selected_ds_customer(state, val) {
            state.selected_ds_customer = val
        },

        set_customers(state, data) {
            state.customers = data
        },

        set_selected_customer(state, val) {
            state.selected_customer = val
        },

        set_selected_channel(state, val) {
            state.selected_channel = val
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
                    customer_id: context.state.selected_customer.M_CustomerID,
                    expedition_id: context.state.selected_expedition.M_ExpeditionID,
                    service: context.state.selected_service.service,
                    courier_cost: context.state.courier_cost,
                    ex_other: context.state.ex_other,
                    ex_note: context.state.ex_note,
                    payment_id: context.state.selected_payment_id,
                    channel: context.state.selected_channel ? context.state.selected_channel.value : '',
                    json_data: context.state.items,
                    is_dropship: context.state.is_dropship,
                    ds_customer_id: context.state.is_dropship == "Y" ? context.state.selected_ds_customer.M_CustomerID : 0,
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

                let resp = await api.save(prm)
                context.commit("set_dialog_progress", false, { root: true })
                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("update_search_status", 2)
                    context.commit("update_search_error_message", "")

                    window.location = '../sales-order-admin-list/'
                    console.log(resp)
                }
            } catch (e) {
                context.commit("set_dialog_progress", false, { root: true })
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
                    context.commit("set_common", ["set_total_expedition", data.total])
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
                    to: context.state.selected_customer.M_DistrictID,
                    courier: context.state.selected_expedition.M_ExpeditionROCode,
                    weight: context.state.total_weight
                }
                if (context.state.is_dropship == 'Y')
                    prm.to = context.state.selected_ds_customer.M_DistrictID

                let resp = await api.search_service(prm)
                context.commit('set_common', ['loading_service', false])
                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("update_search_status", 2)
                    context.commit("update_search_error_message", "")

                    let data = JSON.parse(resp.data)

                    context.commit("set_services", data.rajaongkir.results[0].costs)
                    context.commit('set_origin', data.rajaongkir.origin_details)
                    context.commit('set_destination', data.rajaongkir.destination_details)
                    let found = false
                    if (context.state.selected_service) {
                        for (let s of data.rajaongkir.results[0].costs)
                            if (s.service == context.state.selected_service.service) {
                                context.commit("set_common", ['courier_cost', s.cost[0].value])
                                found = true
                            }
                    }
                    if (!found)
                        context.commit("set_common", ['courier_cost', 0])
                }
            } catch (e) {
                context.commit('set_common', ['loading_service', false])
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
            }
        },

        async search_payment(context) {
            context.commit("update_search_status", 1)
            try {
                let prm = {
                    token: one_token(),
                    is_cod: context.state.selected_expedition ? context.state.selected_expedition.M_ExpeditionIsCOD : 'N'
                }
                if (context.state.selected_customer) prm.customer_id = context.state.selected_customer.M_CustomerID

                let resp = await api.search_payment(prm)

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

                    context.commit("set_payments", data.records)
                    context.commit("set_common", ["set_total_payment", data.total])

                    return data
                }
            } catch (e) {
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
            }
        },

        async search_customer(context) {
            context.commit("update_search_status", 1)
            try {
                let prm = {
                    token: one_token(),
                    search: context.state.search_customer
                }

                let resp = await api.search_customer(prm)

                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("update_search_status", 2)
                    context.commit("update_search_error_message", "")

                    context.commit("set_customers", resp.data.records)
                }
            } catch (e) {
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
            }
        },

        async search_ds_customer(context) {
            context.commit("update_search_status", 1)
            try {
                let prm = {
                    token: one_token(),
                    search: context.state.search_ds_customer,
                    parent: context.state.selected_customer.M_CustomerID
                }

                let resp = await api.search_ds_customer(prm)

                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("update_search_status", 2)
                    context.commit("update_search_error_message", "")

                    context.commit("set_ds_customers", resp.data.records)
                }
            } catch (e) {
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
            }
        },

        async search_account(context) {
            context.commit("update_search_status", 1)
            try {
                let prm = {
                    token: one_token()
                }

                let resp = await api.search_account(prm)

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

                    context.commit("set_accounts", data.records)
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
                for (let i of context.state.items) {
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
                    spend: context.state.total_price
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
                        for (let i of context.state.items) {
                            if (x.coupon_item_id.indexOf(parseInt(i.item_id)) > -1 && i.is_packet == 'N') {
                                i.coupon_amount = x.coupon_amount
                                i.coupon_amount_percent = x.coupon_amount_percent
                                coupon_amounts += (i.item_qty * x.coupon_amount)

                                if (parseFloat(x.coupon_amount_percent) > 0)
                                    coupon_amounts += (x.coupon_amount_percent * i.item_subtotal / 100)
                                    // i.item_subtotal = i.item_subtotal - (i.item_qty*x.coupon_amount)

                                coupon_note += (coupon_note == '' ? i.item_name : ', ' + i.item_name)
                                    // if (i.item_subtotal < 0 ) {
                                    //     coupon_amounts += 0 - i.item_subtotal
                                    //     i.item_subtotal = 0
                                    // }
                            }
                            it.push(i)
                        }
                        context.commit('update_items', it)
                        context.commit('set_common', ['coupon_note', 'Kupon Produk : ' + coupon_note])
                    } else if (x.coupon_type == 'COUPON.PACKET') {
                        let it = []
                        for (let i of context.state.items) {
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
                            coupon_amounts = x.coupon_amount_percent * context.state.courier_cost / 100

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
                    context.commit('set_common', ['coupon_amounts', coupon_amounts])

                }
            } catch (e) {
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
            }
        },

        async search_lead(context) {
            context.commit("set_common", ["search_status", 1])
            let prm = {
                token: one_token(),
                lead: context.state.lead_id
            }

            context.dispatch("system/postme", {
                url: "sales/lead/get_for_sales",
                prm: prm,
                callback: function(d) {
                    context.commit("set_common", ["search_status", 2])
                    console.log(d)

                    context.commit("set_selected_customer", d.customer)
                    context.commit("update_items", d.items)
                },
                failback: function(e) {
                    context.commit("set_common", ["search_status", 3])
                }
            }, { root: true })
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