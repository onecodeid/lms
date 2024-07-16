// 1 => LOADING
// 2 => DONE
// 3 => ERROR
import * as api from "./api_saleslead.js?t=asd"
// import * as api_coupon from "./api_coupon.js"
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
        save: false,

        items: [],
        total_item: 0,
        selected_item: {},
        selected_items: [],

        packets: [],
        total_packet: 0,
        selected_packet: {},

        details: [],
        selected_detail: null,

        customers: [],
        selectedCustomer: null,
        searchCustomer: "",

        expeditions: [],
        total_expedition: 0,
        selected_expedition: null,

        leadtypes: [],
        selected_leadtype: null,

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
        dialog_step: true,

        cities: [],
        selected_city: null,
        search_city: '',
        loading_city: false,

        dialog_expedition: false,
        loading_service: false,

        cust_id: 0,
        lead_id: 0,
        lead_name: '',
        lead_address: '',
        lead_postcode: '',
        lead_phone: '',

        lead_city: '',

        leadsources: [],
        selected_leadsource: null,

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

        leadProblems: [],
        leadGreetings: [],
        leadPrecloses: [],
        selectedLeadProblem: null,
        selectedLeadGreeting: null,
        selectedLeadPreclose: null,

        leadAdsNumber: '',
        leadClose: 'N',
        leadWaText: '',

        orderHistories: [],
        followUps: [],
        defaultFollowUp: {fu_id:0, fu_date:null, fu_note:"", fu_close:"N", fu_preclose:null},
        fuTab: 0,
        mainTab: 0
    },
    mutations: {
        set_common(state, v) {
            let name = v[0]
            let val = v[1]
            if (typeof (val) == "string")
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

        set_leadtypes(state, data) {
            state.leadtypes = data
        },

        set_selected_leadtype(state, val) {
            state.selected_leadtype = val
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

        set_leadsources(state, data) {
            state.leadsources = data
        },

        set_selected_leadsource(state, val) {
            state.selected_leadsource = val
        },

        set_coupon_item_id(state, val) {
            state.coupon_item_id = val
        },

        set_origin(state, val) {
            state.origin = val
        },

        set_destination(state, val) {
            state.destination = val
        }
    },
    actions: {
        async save({state, commit, dispatch}) {
            commit('set_common', ['save', true])

            let details = []
            for (let it of state.selected_items)
                details.push({item_id:it.item_id,item_price:it.item_price,item_name:it.item_price,is_packet:it.is_packet})

            let fus = []
            if (!!state.followUps) fus = state.followUps
            for (let f in fus) if (fus[f].fu_preclose == null) fus[f].fu_preclose = 0
            
            let prm = {
                lead_id: !!state.edit ? state.lead_id : 0,
                json_data: details, //state.details,

                lead_name: state.lead_name,
                lead_address: state.lead_address,
                lead_city_id: state.selected_city ? state.selected_city.city_id : 0,
                lead_postcode: "", // state.lead_postcode,
                lead_phone: state.lead_phone,
                lead_note: state.lead_note,
                lead_source: state.selected_leadsource.leadsource_id,
                lead_type: state.selected_leadtype ? state.selected_leadtype.leadtype_id : 0,
                lead_customer: !!state.selectedCustomer ? state.selectedCustomer.M_CustomerID : 0,
                lead_adsnumber: state.leadAdsNumber,

                lead_greeting: state.selectedLeadGreeting,
                lead_problem: JSON.stringify(state.selectedLeadProblem),
                lead_preclose: (!!state.selectedLeadPreclose?state.selectedLeadPreclose:0),
                lead_close: state.leadClose,

                followups: JSON.stringify(fus),

                token: one_token()
            }

            dispatch("system/postme", {
                url: "sales/lead/save",
                prm: prm,
                callback: function (d) {
                    commit('set_common', ['dialog_quick', false])
                    dispatch('saleslead/search', null, { root: true })
                },
                failback: function (e) {
                    commit("set_text_warning", e.message, { root: true })
                    commit("set_dialog_warning", true, { root: true })
                }
            }, { root: true })

            // context.commit("update_search_status", 1)
            // context.commit("set_dialog_progress", true, { root: true })
            // try {
            //     let prm = {
            //         lead_id: 0,
            //         json_data: context.state.details,

            //         lead_name: context.state.lead_name,
            //         lead_address: context.state.lead_address,
            //         lead_city_id: context.state.selected_city ? context.state.selected_city.city_id : 0,
            //         lead_postcode: "", // context.state.lead_postcode,
            //         lead_phone: context.state.lead_phone,
            //         lead_note: context.state.lead_note,

            //         token: one_token()
            //     }

            //     let resp = await api.save_qo(prm)
            //     context.commit("set_dialog_progress", false, { root: true })
            //     if (resp.status != "OK") {
            //         context.commit("update_search_status", 3)
            //         context.commit("update_search_error_message", resp.message)
            //         context.commit("set_text_warning", resp.message, { root: true })
            //         context.commit("set_dialog_warning", true, { root: true })
            //     } else {
            //         context.commit("update_search_status", 2)
            //         context.commit("update_search_error_message", "")

            //         console.log(resp)
            //         context.commit('set_common', ['dialog_quick', false])
            //         context.dispatch('salesorder/search', null, { root: true })
            //     }
            // } catch (e) {
            //     context.commit("set_dialog_progress", false, { root: true })
            //     context.commit("update_search_status", 3)
            //     context.commit("update_search_error_message", e.message)
            // }
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

        async search_leadtype(context) {
            context.commit('set_common', ['save', true])
            let prm = {
                token: one_token(),
                limit: 999,
                search: ''
            }

            context.dispatch("system/postme", {
                url: "master/leadtype/search",
                prm: prm,
                callback: function (d) {
                    context.commit('set_leadtypes', d.records)
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
                callback: function (d) {
                    context.commit('set_leadsources', d.records)
                }
            }, { root: true })

        },

        async searchCustomer({state, commit, dispatch}) {
            let prm = {
                token: one_token(),
                search: !!state.cust_id?'':state.searchCustomer,
                id: state.cust_id?state.cust_id:0
            }

            return dispatch("system/postme", {
                url: "master/customer/search_autocomplete",
                prm: prm,
                callback: function (d) {
                    commit("set_object", ["customers", d.records])
                    return d
                }
            }, { root: true })
        },

        async searchLastOrder(context) {
            let prm = {
                token: one_token(),
                customer_id: context.state.selectedCustomer?context.state.selectedCustomer.M_CustomerID:-1
            }

            return context.dispatch("system/postme", {
                url: "sales/invoice/get_last_order",
                prm: prm,
                callback: function (d) {
                    context.commit('set_object', ['orderHistories', [d]])
                    return d
                },
                failback: function (e) {
                    context.commit('set_object', ['orderHistories', []])
                }
            }, { root: true })
        },

        async searchLeadAttr({state, commit, dispatch}, code) {
            let prm = {
                token: one_token(),
                code: code
            }

            return dispatch("system/postme", {
                url: "master/leadattr/search",
                prm: prm,
                callback: function (d) {
                    if (code == 'LEAD.PROBLEM') commit('set_object', ['leadProblems', d.records])
                    if (code == 'LEAD.GREETING') commit('set_object', ['leadGreetings', d.records])
                    if (code == 'LEAD.PRECLOSE') commit('set_object', ['leadPrecloses', d.records])

                    return d
                }
            }, { root: true })

        },

        async waSend({state, commit, dispatch}) {
            commit('set_common', ['save', true])
            let prm = {
                token: one_token(),
                ref_code: "LEAD",
                ref_id: state.lead_id,
                content: state.leadWaText
            }

            return dispatch("system/postme", {
                url: "sales/lead/wa_send",
                prm: prm,
                callback: function (d) {
                    commit('set_common', ['save', false])
                }
            }, { root: true })

        }
    }
}