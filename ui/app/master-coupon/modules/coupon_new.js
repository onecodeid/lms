// 1 => LOADING
// 2 => DONE
// 3 => ERROR
import * as api from "./api_coupon.js"
import { one_token } from "../../assets/js/global.js"

export default {
    namespaced: true,
    state: {
        edit: false,
        search_status: 0,
        search_error_message: '',
        search: '',
        
        coupons: [],
        total_coupon: 0,

        types: [],
        selected_type: null,
        
        coupon_sdate: new Date().toISOString().substr(0, 10),
        coupon_edate: new Date().toISOString().substr(0, 10),
        coupon_code: '',
        coupon_amount: 0,
        coupon_amount_p: 0,
        coupon_amount_t: "R",
        coupon_min_spend: 0,
        coupon_max_spend: 0,
        coupon_qty: 0,
        coupon_used: 0,
        coupon_reset: false,

        items: [],
        selected_item: [],
        packets: [],
        selected_packet: [],
        expeditions: [],
        selected_expedition: [],

        categories : [],
        selected_category: {},

        dialog_new: false
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

        update_search_error_message(state, msg) {
            state.search_error_message = msg
        },

        update_search_status(state, val) {
            state.search_status = val
        },

        set_types (state, v) {
            state.types = v
        },

        set_selected_type (state, v) {
            state.selected_type = v
        },

        set_items (state, v) {
            state.items = v
        },

        set_packets (state, v) {
            state.packets = v
        },

        set_expeditions (state, v) {
            state.expeditions = v
        },

        set_selected_item (state, v) {
            state.selected_item = v
        },

        set_selected_packet (state, v) {
            state.selected_packet = v
        },

        set_selected_expedition (state, v) {
            state.selected_expedition = v
        },

        set_categories (state, v) {
            state.categories = v
        },

        set_selected_category (state, v) {
            state.selected_category = v
        }
    },
    actions: {
        async save(context, fn) {
            context.commit("update_search_status", 1)
            try {
                let prm = {}
                let items = []
                let packets = []
                let exps = []
                prm.token = one_token()
                prm.coupon_code = context.state.coupon_code
                prm.coupon_sdate = context.state.coupon_sdate
                prm.coupon_edate = context.state.coupon_edate
                prm.coupon_max_spend = context.state.coupon_max_spend
                prm.coupon_min_spend = context.state.coupon_min_spend
                prm.coupon_amount = context.state.coupon_amount
                prm.coupon_amount_p = context.state.coupon_amount_p
                prm.coupon_type = context.state.selected_type.M_CouponTypeID
                prm.coupon_type_code = context.state.selected_type.M_CouponTypeCode
                prm.coupon_qty = context.state.coupon_qty
                prm.coupon_category_id = context.state.selected_category ? context.state.selected_category.M_CategoryID : 0
                if (context.state.coupon_reset)
                    prm.coupon_reset = 'Y'

                for (let i of context.state.selected_item)
                    items.push(i.M_ItemID)
                for (let p of context.state.selected_packet)
                    packets.push(p.M_PacketID)
                for (let p of context.state.selected_expedition)
                    exps.push(p.M_ExpeditionID)

                prm.items = items
                prm.packets = packets
                prm.exps = exps

                if (context.state.edit)
                    prm.coupon_id = context.rootState.coupon.selected_coupon.M_CouponID

                let resp = await api.save(prm)
                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("update_search_status", 2)
                    context.commit("update_search_error_message", "")

                    context.dispatch('coupon/search', {}, {root:true})
                    context.commit('set_common', ['dialog_new', false])
                }
                return resp
            } catch (e) {
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
            }
        },

        async search_type(context) {
            context.commit("update_search_status", 1)
            try {
                let prm = {}
                prm.token = one_token()
                prm.search = ''

                let resp = await api.search_type(prm)
                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("update_search_status", 2)
                    context.commit("update_search_error_message", "")

                    context.commit('set_types', resp.data.records)                   
                }
                return resp
            } catch (e) {
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
            }
        },

        async search_item(context) {
            context.commit("update_search_status", 1)
            try {
                let prm = {token:one_token(),search:'',customer_level:1}
                let resp = await api.search_item(prm)
                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("update_search_status", 2)
                    context.commit("update_search_error_message", "")
                    context.commit("set_items", resp.data.records)
                }
            } catch (e) {
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
            }
        },

        async search_packet(context) {
            context.commit("update_search_status", 1)
            try {
                let prm = {token:one_token(),search:'',customer_level:1}
                let resp = await api.search_packet(prm)
                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("update_search_status", 2)
                    context.commit("update_search_error_message", "")
                    context.commit("set_packets", resp.data.records)
                }
            } catch (e) {
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
            }
        },

        async search_expedition(context) {
            context.commit("update_search_status", 1)
            try {
                let prm = {token:one_token(), search:''}
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
        },

        async search_category(context) {
            context.commit("update_search_status", 1)
            try {
                let prm = {token:one_token(), search:''}
                let resp = await api.search_category(prm)
                if (resp.status != "OK") {
                    context.commit("update_search_status", 3)
                    context.commit("update_search_error_message", resp.message)
                } else {
                    context.commit("update_search_status", 2)
                    context.commit("update_search_error_message", "")
                    context.commit("set_categories", resp.data.records)
                }
            } catch (e) {
                context.commit("update_search_status", 3)
                context.commit("update_search_error_message", e.message)
            }
        }
    }
}