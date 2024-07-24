// 1 => LOADING
// 2 => DONE
// 3 => ERROR
import { one_token, one_user, URL } from "../../assets/js/global.js"

export default {
    namespaced: true,
    state: {
        sexes: [{id:"M",text:"Laki-laki"}, {id:"F",text:"Perempuan"}],
        selectedSex: null,

        cust_id: 0,
        cust_name: '',
        cust_address: '',
        cust_email: '',
        cust_phone: '',
        cust_sex: 'M',
        step: 1,

        order_note: '',

        cities: [],
        selected_city: null,

        coupon_code: '',
        coupon_amount: 0,
        coupon_id: 0,

        items: [],
        total_item: 0,
        selected_item: {},
        selected_items: [],

        levels: [],
        selectedLevel: null,

        payments: [],
        selected_payment: null,

        coupon_code: '',
        order_note: '',
        invoiceNumber: '',

        schedules: [],
        selected_schedule: null,
        days: [],
        selected_day: null,
        schedule_time: '00:00',

        selected_order: {}
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

        set_object(state, v) {
            let name = v[0]
            let val = v[1]
            state[name] = val
        }
    },
    actions: {
        async search_item({state, commit, dispatch}) {
            let prm = {
                token : one_token(),
                customer_level : 5,
                search : ""
            }

            return dispatch("system/postme", {
                url: "master/item/search_w_price",
                prm: prm,
                callback: function(d) {
                    commit("set_object", ['items', d.records])
                    return d
                },
                failback: function(e) {
                    // context.commit('set_common', ['loading_city', false])
                }
            }, { root: true })
        },

        async search_level({state, commit, dispatch}) {
            let prm = {
                token : one_token(),
                search : ""
            }

            dispatch("system/postme", {
                url: "master/customerlevel/search",
                prm: prm,
                callback: function(d) {
                    commit("set_object", ['levels', d.records])
                },
                failback: function(e) {
                    // context.commit('set_common', ['loading_city', false])
                }
            }, { root: true })
        },

        async search_payment({state, commit, dispatch}) {
            let prm = {
                token: one_token(),
                is_cod: 'N'
            }

            dispatch("system/postme", {
                url: 'master/paymenttype/search',
                prm: prm,
                callback: function(d) {
                    commit("set_object", ['payments', d.records])
                    commit("set_object", ['selected_payment', d.records[0].M_PaymentTypeID])
                },
                failback: function(e) {
                    // context.commit('set_common', ['loading_city', false])
                }
            }, { root: true })
        },

        async save({state, commit, dispatch}) {
            let prm = {
                order_id: 0,
                expedition_id: 0,
                service: '',
                courier_cost: 0,
                ex_other: '',
                ex_note: '',
                channel: '',
                is_dropship: 'N',
                ds_customer_id: 0,
                leadsource: 0,
                cust_postcode: '',
                lead_id: 0,
                sales_ads: '',
                sales_mp: '',

                // diisi
                payment_id: state.selected_payment ?? 0,
                json_data: [{item_id: state.selected_item.M_ItemID,
                    item_qty: 1,
                    item_price: state.selected_item.M_PriceAmount,
                    item_disc: 0,
                    item_discrp: 0,
                    is_packet: 'N'}],
                cust_id: 0,
                cust_name: state.cust_name,
                cust_address: state.cust_address,
                cust_city_id: 0, //state.selected_city.M_CityID,

                coupon_code: state.coupon_code,
                coupon_type: 'X',
                coupon_amount: state.coupon_amount,
                coupon_id: state.coupon_id,
                
                cust_phone: state.cust_phone,
                order_note: state.order_note,

                token: one_token()
            }

            return dispatch("system/postme", {
                url: 'sales/so/save_qo',
                prm: prm,
                callback: function(d) {
                    return d
                },
                failback: function(e) {
                    // context.commit('set_common', ['loading_city', false])
                }
            }, { root: true })
        },

        async searchInvoice({state, commit, dispatch}) {
            return dispatch("system/postme", {
                url: 'sales/invoice/search',
                prm: {
                    search: state.invoiceNumber,
                    sdate: '2024-01-01', edate: '2055-01-01', page: 1, token: one_token()
                },

                callback: function(d) {
                    return d
                },

                failback: function(e) {
                    // context.commit('set_common', ['loading_city', false])
                }
            }, { root: true })
        },

        async search_schedule({state, commit, dispatch}) {
            let prm = {
                token: one_token(),
                item_id: state.selected_item.M_ItemID
            }

            return dispatch("system/postme", {
                url: 'master/schedule/search',
                prm: prm,
                callback: function(d) {
                    commit("set_object", ['schedules', d.records])
                    // commit("set_object", ['selected_payment', d.records[0].M_PaymentTypeID])

                    return d
                },
                failback: function(e) {
                    // context.commit('set_common', ['loading_city', false])
                }
            }, { root: true })
        },

        async search_day({state, commit, dispatch}) {
            let prm = {
                token : one_token(),
                search : ""
            }

            dispatch("system/postme", {
                url: "master/day/search",
                prm: prm,
                callback: function(d) {
                    commit("set_object", ['days', d.records])
                },
                failback: function(e) {
                    // context.commit('set_common', ['loading_city', false])
                }
            }, { root: true })
        },
    }
}